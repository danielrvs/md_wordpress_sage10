<?php

declare(strict_types=1);

namespace App\Domain\Appointments\Repositories;

use App\Domain\Appointments\Contracts\AppointmentRepositoryInterface;

final class WpDbAppointmentRepository implements AppointmentRepositoryInterface
{
    private string $table;

    public function __construct()
    {
        global $wpdb;
        $this->table = $wpdb->prefix . 'doctor_appointments';
    }

    public function getBookedStartTimes(int $doctorId, string $date): array
    {
        global $wpdb;

        // Definimos los límites del día para optimizar la búsqueda por índice compuesto
        $startOfDay = $date . ' 00:00:00';
        $endOfDay = $date . ' 23:59:59';

        // TIME_FORMAT extrae únicamente el tramo HH:MM para acoplarse con tu SlotDTO
        $query = $wpdb->prepare(
            "SELECT TIME_FORMAT(appointment_date, '%H:%i') FROM {$this->table} 
             WHERE doctor_id = %d 
               AND appointment_date >= %s 
               AND appointment_date <= %s 
               AND status = 'confirmed'",
            $doctorId,
            $startOfDay,
            $endOfDay
        );

        return $wpdb->get_col($query) ?: [];
    }

    public function create(array $data): int
    {
        global $wpdb;

        // Combinamos la fecha y hora recibida (ej: '2026-07-20' y '08:30') en formato ISO DATETIME
        $fullDateTime = sprintf('%s %s:00', $data['appointment_date'], $data['start_time']);

        $wpdb->insert($this->table, [
            'doctor_id' => (int) $data['doctor_id'],
            'patient_id' => (int) $data['patient_id'],
            'clinic_id' => (int) $data['clinic_id'],
            'appointment_date' => $fullDateTime,
            'status' => sanitize_text_field($data['status'] ?? 'confirmed'),
            'notes' => !empty($data['notes']) ? sanitize_textarea_field($data['notes']) : null,
        ]);

        return (int) $wpdb->insert_id;
    }
}