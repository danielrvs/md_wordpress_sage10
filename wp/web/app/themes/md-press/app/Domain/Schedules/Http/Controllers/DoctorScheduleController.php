<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Http\Controllers;

use App\Domain\Schedules\Contracts\GenerateDoctorScheduleServiceInterface;
use App\Domain\Schedules\Services\AddAbsenceService;
use App\Domain\Schedules\Services\DeleteAbsenceService;
use App\Domain\Schedules\Services\GetAbsencesByDoctorIdService;
use App\Domain\Schedules\Services\UpdateDoctorScheduleService;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class DoctorScheduleController
{
    public function __construct(
        private readonly GenerateDoctorScheduleServiceInterface $scheduleService,
        private readonly UpdateDoctorScheduleService $updateService,
        private readonly GetAbsencesByDoctorIdService $getAbsencesByDoctorService,
        private readonly AddAbsenceService $addAbsenceService,
        private readonly DeleteAbsenceService $deleteAbsenceService
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
            $res = $this->scheduleService->execute($doctorId, $date);

            return new WP_REST_Response($res->toArray(), 200);
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

    public function getAbsences(WP_REST_Request $request): WP_REST_Response
    {
        $doctorId = (int) $request->get_param('id');
        $res = $this->getAbsencesByDoctorService->execute($doctorId);

        return new WP_REST_Response($res, 200);
    }

    public function addAbsence(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $doctorId = (int) $request->get_param('id');
        $params = $request->get_json_params();

        try {
            $this->addAbsenceService->execute(
                $doctorId,
                $params['start_date'] ?? '',
                $params['end_date'] ?? '',
                $params['reason'] ?? null
            );

            return new WP_REST_Response(['message' => 'Ausencia añadida con éxito.'], 201);
        } catch (\InvalidArgumentException $e) {
            return new WP_Error('invalid_dates', $e->getMessage(), ['status' => 400]);
        }
    }

    public function deleteAbsence(WP_REST_Request $request): WP_REST_Response
    {
        $absenceId = (int) $request->get_param('absence_id');
        $this->deleteAbsenceService->execute($absenceId);

        return new WP_REST_Response(['message' => 'Ausencia eliminada con éxito.'], 200);
    }
}