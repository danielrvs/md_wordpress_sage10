<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Admin;

/**
 * Registra el grupo de campos ACF estructurado por pestañas para cada día
 * y sincroniza los cambios con la tabla personalizada + caché Redis.
 */
class AcfScheduleFieldGroup
{
    public function register(): void
    {
        add_action('acf/init', [$this, 'registerFieldGroup']);
        add_action('acf/save_post', [$this, 'syncToCustomTable'], 20);
    }

    public function registerFieldGroup(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_doctor_schedule',
            'title' => '⚙️ Configuración de Horarios por Día (Asíncrono)',
            'fields' => $this->buildDayFields(),
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
    }

    public function syncToCustomTable(int $postId): void
    {
        if (get_post_type($postId) !== 'doctor') {
            return;
        }

        $rules = [];

        for ($day = 1; $day <= 7; $day++) {
            if (!get_field("works_{$day}", $postId)) {
                continue;
            }

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

        app(\App\Domain\Schedules\Services\UpdateDoctorScheduleService::class)->execute($postId, $rules);
    }

    private function buildDayFields(): array
    {
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
            $fields[] = $this->buildTabField($num, $name);
            $fields[] = $this->buildToggleField($num, $name);
            $fields[] = $this->buildTimeField("field_start_{$num}", "start_{$num}", 'Hora de Entrada', $num, '33');
            $fields[] = $this->buildTimeField("field_end_{$num}", "end_{$num}", 'Hora de Salida', $num, '33');
            $fields[] = $this->buildDurationField($num, '34');
        }

        return $fields;
    }

    private function buildTabField(int $num, string $name): array
    {
        return [
            'key' => "field_tab_{$num}",
            'label' => $name,
            'type' => 'tab',
            'placement' => 'top',
            'endpoint' => 0,
        ];
    }

    private function buildToggleField(int $num, string $name): array
    {
        return [
            'key' => "field_works_{$num}",
            'label' => "¿Pasa consulta el {$name}?",
            'name' => "works_{$num}",
            'type' => 'true_false',
            'ui' => 1,
            'ui_on_text' => 'Sí',
            'ui_off_text' => 'No',
            'default_value' => 0,
        ];
    }

    private function buildTimeField(string $key, string $name, string $label, int $dayNum, string $width): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'name' => $name,
            'type' => 'time_picker',
            'display_format' => 'H:i',
            'return_format' => 'H:i:s',
            'wrapper' => ['width' => $width],
            'conditional_logic' => [
                [
                    [
                        'field' => "field_works_{$dayNum}",
                        'operator' => '==',
                        'value' => '1',
                    ],
                ],
            ],
        ];
    }

    private function buildDurationField(int $num, string $width): array
    {
        return [
            'key' => "field_duration_{$num}",
            'label' => 'Duración Cita (Min)',
            'name' => "duration_{$num}",
            'type' => 'number',
            'default_value' => 30,
            'wrapper' => ['width' => $width],
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
}
