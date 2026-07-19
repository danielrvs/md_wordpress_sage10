<?php

declare(strict_types=1);

if (defined('WP_CLI') && WP_CLI) {
    \WP_CLI::add_command('schedule:migrate', \App\Domain\Schedules\Cli\ScheduleMigrator::class);
    \WP_CLI::add_command('schedule:seed', \App\Domain\Schedules\Cli\ScheduleSeeder::class);
}

/**
 * Registrar el grupo de campos de ACF estructurado por pestañas para cada día
 */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $days = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo',
    ];

    $fields = [];

    foreach ($days as $num => $name) {
        // Creación de la pestaña del día
        $fields[] = [
            'key' => "field_tab_{$num}",
            'label' => $name,
            'type' => 'tab',
            'placement' => 'top',
            'endpoint' => 0,
        ];

        // Interruptor para activar/desactivar el día
        $fields[] = [
            'key' => "field_works_{$num}",
            'label' => "¿Pasa consulta el {$name}?",
            'name' => "works_{$num}",
            'type' => 'true_false',
            'ui' => 1,
            'ui_on_text' => 'Sí',
            'ui_off_text' => 'No',
            'default_value' => 0,
        ];

        // Hora de Entrada (Solo visible si el interruptor está en 'Sí')
        $fields[] = [
            'key' => "field_start_{$num}",
            'label' => 'Hora de Entrada',
            'name' => "start_{$num}",
            'type' => 'time_picker',
            'display_format' => 'H:i',
            'return_format' => 'H:i:s',
            'wrapper' => ['width' => '33'],
            'conditional_logic' => [
                [
                    [
                        'field' => "field_works_{$num}",
                        'operator' => '==',
                        'value' => '1',
                    ],
                ],
            ],
        ];

        // Hora de Salida (Solo visible si el interruptor está en 'Sí')
        $fields[] = [
            'key' => "field_end_{$num}",
            'label' => 'Hora de Salida',
            'name' => "end_{$num}",
            'type' => 'time_picker',
            'display_format' => 'H:i',
            'return_format' => 'H:i:s',
            'wrapper' => ['width' => '33'],
            'conditional_logic' => [
                [
                    [
                        'field' => "field_works_{$num}",
                        'operator' => '==',
                        'value' => '1',
                    ],
                ],
            ],
        ];

        // Duración de la cita (Solo visible si el interruptor está en 'Sí')
        $fields[] = [
            'key' => "field_duration_{$num}",
            'label' => 'Duración Cita (Min)',
            'name' => "duration_{$num}",
            'type' => 'number',
            'default_value' => 30,
            'wrapper' => ['width' => '34'],
            'conditional_logic' => [
                [
                    [
                        'field' => "field_works_{$num}",
                        'operator' => '==',
                        'value' => '1',
                    ],
                ],
            ],
        ];
    }

    acf_add_local_field_group([
        'key' => 'group_doctor_schedule',
        'title' => '⚙️ Configuración de Horarios por Día (Asíncrono)',
        'fields' => $fields,
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'doctor',
                ],
            ],
        ],
        'menu_order' => 10,
    ]);
});

/**
 * Sincronizar los cambios del panel de administración con la tabla personalizada y la caché de Redis
 */
add_action('acf/save_post', function (int $postId) {
    if (get_post_type($postId) !== 'doctor') {
        return;
    }

    $rules = [];

    for ($day = 1; $day <= 7; $day++) {
        $worksThisDay = get_field("works_{$day}", $postId);

        if ($worksThisDay) {
            $startTime = get_field("start_{$day}", $postId);
            $endTime = get_field("end_{$day}", $postId);
            $duration = get_field("duration_{$day}", $postId);

            if ($startTime && $endTime) {
                $rules[] = [
                    'day_of_week' => $day,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'slot_duration' => (int) ($duration ?? 30),
                ];
            }
        }
    }
    app(\App\Domain\Schedules\Services\UpdateDoctorScheduleService::class)->execute($postId, $rules);
}, 20);

// TODO; añadir invalidación de caché cuando se creen citas para un médico
// Cache::forget(sprintf('doctor_schedules:id_%d:date_%s', $doctorId, $date));

//router
add_action('rest_api_init', function () {
    register_rest_route('api/v1', '/doctors/(?P<id>\d+)/schedule', [
        'methods' => 'GET',
        'callback' => [app(\App\Domain\Schedules\Http\Controllers\DoctorScheduleController::class), 'getSchedule'],
        'permission_callback' => '__return_true',
        'args' => [
            'date' => [
                'required' => true,
                'validate_callback' => function (mixed $param): bool {
                    if (!is_string($param)) {
                        return false;
                    }
                    $d = \DateTime::createFromFormat('Y-m-d', $param);
                    return $d && $d->format('Y-m-d') === $param;
                },
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ],
    ]);

    register_rest_route('api/v1', '/doctors/(?P<id>\d+)/schedule', [
        'methods' => 'POST',
        'callback' => [app(\App\Domain\Schedules\Http\Controllers\DoctorScheduleController::class), 'updateSchedule'],
        'permission_callback' => '__return_true',
        'args' => [
            '_json' => [
                'description' => 'Array de reglas semanales de horario',
                'type' => 'array',
                'required' => true,
                'items' => [
                    'type' => 'object',
                    'required' => ['day_of_week', 'start_time', 'end_time', 'slot_duration'],
                    'properties' => [
                        'day_of_week' => [
                            'type' => 'integer',
                            'minimum' => 1,
                            'maximum' => 7,
                            'description' => '1 para Lunes, 7 para Domingo',
                        ],
                        'start_time' => [
                            'type' => 'string',
                            'pattern' => '^([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$',
                            'description' => 'Formato HH:MM o HH:MM:SS',
                        ],
                        'end_time' => [
                            'type' => 'string',
                            'pattern' => '^([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$',
                            'description' => 'Formato HH:MM o HH:MM:SS',
                        ],
                        'slot_duration' => [
                            'type' => 'integer',
                            'minimum' => 5,
                            'maximum' => 240,
                            'description' => 'Duración del slot en minutos',
                        ],
                    ],
                ],
            ],
        ],
    ]);
});