<?php

declare(strict_types=1);

namespace App\Domain\Appointments\Http\Routes;

use App\Domain\Appointments\Http\Controllers\AppointmentController;

/**
 * Registra las rutas REST para la gestión de citas en la API.
 */
class AppointmentRoutes
{
    public function register(): void
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes(): void
    {
        $controller = app(AppointmentController::class);

        // Crear una nueva cita médica
        register_rest_route('api/v1', '/appointments', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => '__return_true',
            'args' => [
                'doctor_id' => [
                    'required' => true,
                    'type' => 'integer',
                    'sanitize_callback' => 'absint',
                ],
                'appointment_date' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'start_time' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);

        // Obtener citas de un médico
        register_rest_route('api/v1', '/doctors/(?P<id>\d+)/appointments', [
            'methods' => 'GET',
            'callback' => [$controller, 'getDoctorAppointments'],
            'permission_callback' => '__return_true',
        ]);
    }
}
