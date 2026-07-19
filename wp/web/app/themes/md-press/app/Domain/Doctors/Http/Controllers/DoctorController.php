<?php

namespace App\Domain\Doctors\Http\Controllers;

use App\Domain\Doctors\Services\IndexDoctorService;

class DoctorController
{
    public function __construct(
        private readonly IndexDoctorService $indexService
    ) {
    }

    public function index(\WP_REST_Request $request): \WP_REST_Response
    {
        $page = (int) ($request->get_param('page') ?: 1);
        $perPage = (int) ($request->get_param('per_page') ?: 10);

        $payload = $this->indexService->execute([
            'filters' => [
                'search' => $request->get_param('search') ?: '',
                'specialty' => $request->get_param('specialty') ?: '',
            ],
            'page' => $page,
            'per_page' => $perPage,
        ]);

        // Transformamos los DTOs a arrays nativos para la respuesta JSON
        $payload['doctors'] = array_map(
            fn($doctor) => $doctor->toArray(),
            $payload['doctors']
        );

        return new \WP_REST_Response($payload, 200);
    }
}