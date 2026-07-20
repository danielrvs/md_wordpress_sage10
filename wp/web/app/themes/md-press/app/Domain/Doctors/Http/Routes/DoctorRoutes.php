<?php

declare(strict_types=1);

namespace App\Domain\Doctors\Http\Routes;

use App\Domain\Doctors\Http\Controllers\DoctorController;
use App\Domain\Doctors\Http\Controllers\SpecialtyController;

/**
 * Registra todas las rutas REST de la API para el dominio de médicos.
 * Debe llamarse dentro de la acción `rest_api_init`.
 */
class DoctorRoutes
{
    public function register(): void
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes(): void
    {
        $doctorController = app(DoctorController::class);
        $specialtyController = app(SpecialtyController::class);

        register_rest_route('api/v1', '/doctors', [
            'methods' => 'GET',
            'callback' => [$doctorController, 'index'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('api/v1', '/specialties', [
            'methods' => 'GET',
            'callback' => [$specialtyController, 'index'],
            'permission_callback' => '__return_true',
        ]);
    }
}
