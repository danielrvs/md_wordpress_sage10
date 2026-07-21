<?php

declare(strict_types=1);

namespace App\Domain\Appointments\Http\Controllers;

use App\Domain\Appointments\Contracts\AppointmentRepositoryInterface;
use App\Domain\Appointments\Contracts\CreateAppointmentServiceInterface;
use App\Domain\Appointments\DTOs\CreateAppointmentDTO;
use DomainException;
use InvalidArgumentException;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class AppointmentController
{
    public function __construct(
        private readonly CreateAppointmentServiceInterface $createAppointmentService,
        private readonly AppointmentRepositoryInterface $appointmentRepository
    ) {
    }

    /**
     * Crear una nueva cita médica.
     * POST /wp-json/api/v1/appointments
     */
    public function create(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $params = $request->get_json_params() ?: $request->get_params();

        // Validar presencia de parámetros requeridos
        $doctorId = (int) ($params['doctor_id'] ?? $params['doctorId'] ?? 0);
        $date = (string) ($params['appointment_date'] ?? $params['date'] ?? '');
        $startTime = (string) ($params['start_time'] ?? $params['startTime'] ?? '');

        if ($doctorId <= 0) {
            return new WP_Error('missing_doctor_id', 'El campo doctor_id es obligatorio.', ['status' => 400]);
        }

        if (empty($date)) {
            return new WP_Error('missing_appointment_date', 'El campo appointment_date (YYYY-MM-DD) es obligatorio.', ['status' => 400]);
        }

        if (empty($startTime)) {
            return new WP_Error('missing_start_time', 'El campo start_time (HH:MM) es obligatorio.', ['status' => 400]);
        }

        try {
            $dto = CreateAppointmentDTO::fromArray($params);
            $appointment = $this->createAppointmentService->execute($dto);

            return new WP_REST_Response([
                'success' => true,
                'message' => 'Cita reservada correctamente.',
                'appointment' => $appointment->toArray(),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return new WP_Error('invalid_argument', $e->getMessage(), ['status' => 400]);
        } catch (DomainException $e) {
            return new WP_Error('slot_unavailable', $e->getMessage(), ['status' => 409]);
        } catch (\Throwable $e) {
            return new WP_Error('server_error', 'Error interno al procesar la cita: ' . $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Obtener las citas registradas de un médico.
     * GET /wp-json/api/v1/doctors/{id}/appointments
     */
    public function getDoctorAppointments(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $doctorId = (int) $request->get_param('id');
        $date = $request->get_param('date');

        if ($doctorId <= 0 || get_post_type($doctorId) !== 'doctor') {
            return new WP_Error('doctor_not_found', 'El médico especificado no existe.', ['status' => 404]);
        }

        try {
            $appointments = $this->appointmentRepository->getAppointmentsByDoctor($doctorId, $date);

            return new WP_REST_Response([
                'success' => true,
                'appointments' => $appointments,
            ], 200);
        } catch (\Throwable $e) {
            return new WP_Error('server_error', 'Error al obtener las citas del médico.', ['status' => 500]);
        }
    }
}
