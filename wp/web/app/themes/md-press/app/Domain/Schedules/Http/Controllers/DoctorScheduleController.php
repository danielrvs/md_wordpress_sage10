<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Http\Controllers;

use App\Domain\Schedules\Contracts\GenerateDoctorScheduleServiceInterface;
use App\Domain\Schedules\Services\UpdateDoctorScheduleService;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class DoctorScheduleController
{
    public function __construct(
        private readonly GenerateDoctorScheduleServiceInterface $scheduleService,
        private readonly UpdateDoctorScheduleService $updateService
    ) {
    }

    public function getSchedule(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $doctorId = (int) $request->get_param('id');
        $date = $request->get_param('date');

        if (get_post_type($doctorId) !== 'doctor' || get_post_status($doctorId) !== 'publish') {
            return new WP_Error(
                'doctor_not_found',
                'El médico especificado no existe.',
                ['status' => 404]
            );
        }

        try {
            $scheduleDTO = $this->scheduleService->execute($doctorId, $date);

            return new WP_REST_Response($scheduleDTO->toArray(), 200);
        } catch (\Exception $e) {
            return new WP_Error(
                'schedule_error',
                'Error al procesar la agenda del médico.',
                ['status' => 500]
            );
        }
    }

    public function updateSchedule(WP_REST_Request $request): WP_REST_Response|\WP_Error
    {
        $doctorId = (int) $request->get_param('id');
        $rules = $request->get_json_params();

        if (get_post_type($doctorId) !== 'doctor') {
            return new WP_Error('not_found', 'Médico no encontrado', ['status' => 404]);
        }

        try {
            $this->updateService->execute($doctorId, $rules ?? []);

            return new WP_REST_Response(['message' => 'Horario actualizado correctamente y caché invalidada.'], 200);
        } catch (\Exception $e) {
            return new WP_Error('server_error', 'No se pudo actualizar el horario.', ['status' => 500]);
        }
    }
}