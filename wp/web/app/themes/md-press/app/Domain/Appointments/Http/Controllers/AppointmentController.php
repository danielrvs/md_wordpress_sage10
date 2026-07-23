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

    public function create(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $params = $request->get_json_params() ?: $request->get_params() ?: [];

        // Asignar patient_id dinámicamente desde el usuario autenticado en WordPress
        $currentUserId = get_current_user_id();
        if (empty($params['patient_id']) || !current_user_can('manage_options')) {
            $params['patient_id'] = $currentUserId;
        }

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

    /**
     * Obtener las citas del paciente autenticado.
     * GET /wp-json/api/v1/patient/appointments
     */
    public function getPatientAppointments(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $patientId = get_current_user_id();

        if ($patientId <= 0) {
            return new WP_Error('rest_unauthorized', 'Debes estar autenticado para ver tus citas.', ['status' => 401]);
        }

        try {
            $appointments = $this->appointmentRepository->getAppointmentsByPatient($patientId);

            return new WP_REST_Response([
                'success' => true,
                'appointments' => $appointments,
            ], 200);
        } catch (\Throwable $e) {
            return new WP_Error('server_error', 'Error al consultar tus citas médicas: ' . $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Cancelar una cita médica del paciente autenticado.
     * POST /wp-json/api/v1/patient/appointments/{id}/cancel
     */
    public function cancelPatientAppointment(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $patientId = get_current_user_id();
        $appointmentId = (int) $request->get_param('id');

        if ($patientId <= 0) {
            return new WP_Error('rest_unauthorized', 'Debes estar autenticado para cancelar tus citas.', ['status' => 401]);
        }

        if ($appointmentId <= 0) {
            return new WP_Error('invalid_appointment_id', 'ID de cita no válido.', ['status' => 400]);
        }

        try {
            $success = $this->appointmentRepository->cancelAppointment($appointmentId, $patientId);

            if (!$success) {
                return new WP_Error('appointment_not_found', 'No se encontró la cita especificada o no perteneces a ella.', ['status' => 404]);
            }

            return new WP_REST_Response([
                'success' => true,
                'message' => 'Cita cancelada correctamente.',
            ], 200);
        } catch (\Throwable $e) {
            return new WP_Error('server_error', 'Error al cancelar la cita médica: ' . $e->getMessage(), ['status' => 500]);
        }
    }
}
