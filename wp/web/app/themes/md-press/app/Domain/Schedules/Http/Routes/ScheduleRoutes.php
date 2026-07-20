<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Http\Routes;

use App\Domain\Schedules\Http\Controllers\DoctorScheduleController;

/**
 * Registra todas las rutas REST de la API para el dominio de horarios.
 * Debe llamarse dentro de la acción `rest_api_init`.
 */
class ScheduleRoutes
{
    public function register(): void
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes(): void
    {
        $controller = app(DoctorScheduleController::class);

        $this->registerScheduleRoutes($controller);
        $this->registerAbsenceRoutes($controller);
    }

    private function registerScheduleRoutes(DoctorScheduleController $controller): void
    {
        register_rest_route('api/v1', '/doctors/(?P<id>\d+)/schedule', [
            'methods' => 'GET',
            'callback' => [$controller, 'getSchedule'],
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
            'callback' => [$controller, 'updateSchedule'],
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
    }

    private function registerAbsenceRoutes(DoctorScheduleController $controller): void
    {
        register_rest_route('api/v1', '/doctors/(?P<id>\d+)/absences', [
            [
                'methods' => 'GET',
                'callback' => [$controller, 'getAbsences'],
                'permission_callback' => '__return_true',
            ],
            [
                'methods' => 'POST',
                'callback' => [$controller, 'addAbsence'],
                'permission_callback' => '__return_true',
                'args' => [
                    'start_date' => ['required' => true, 'sanitize_callback' => 'sanitize_text_field'],
                    'end_date' => ['required' => true, 'sanitize_callback' => 'sanitize_text_field'],
                ],
            ],
        ]);

        register_rest_route('api/v1', '/absences/(?P<absence_id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteAbsence'],
            'permission_callback' => '__return_true',
        ]);
    }
}
