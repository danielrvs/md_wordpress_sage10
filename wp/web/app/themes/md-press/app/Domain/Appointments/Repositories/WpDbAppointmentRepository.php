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
            "SELECT TIME_FORMAT(appointment_date, '%%H:%%i') FROM {$this->table} 
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

    public function getAppointmentsByDoctor(int $doctorId, ?string $date = null, string $order = 'ASC'): array
    {
        global $wpdb;

        $direction = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        if ($date) {
            $start = $date . ' 00:00:00';
            $end = $date . ' 23:59:59';
            $query = $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE doctor_id = %d AND appointment_date >= %s AND appointment_date <= %s ORDER BY appointment_date {$direction}",
                $doctorId,
                $start,
                $end
            );
        } else {
            $query = $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE doctor_id = %d AND appointment_date >= NOW() AND status != 'cancelled' ORDER BY appointment_date {$direction}",
                $doctorId
            );
        }

        return $wpdb->get_results($query, OBJECT) ?: [];
    }

    public function getAppointmentsByPatient(int $patientId): array
    {
        global $wpdb;

        $postsTable = $wpdb->posts;
        $postmetaTable = $wpdb->postmeta;

        $query = $wpdb->prepare(
            "SELECT 
                a.id,
                a.doctor_id,
                a.patient_id,
                a.clinic_id,
                a.appointment_date,
                a.status,
                a.notes,
                a.created_at,
                p.post_title as doctor_name,
                pm_spec.meta_value as doctor_specialty,
                pm_location.meta_value as doctor_location
             FROM {$this->table} a
             LEFT JOIN {$postsTable} p ON p.ID = a.doctor_id
             LEFT JOIN {$postmetaTable} pm_spec ON (pm_spec.post_id = a.doctor_id AND pm_spec.meta_key = 'specialty')
             LEFT JOIN {$postmetaTable} pm_location ON (pm_location.post_id = a.doctor_id AND pm_location.meta_key = 'consultory_address')
             WHERE a.patient_id = %d
             ORDER BY a.appointment_date DESC",
            $patientId
        );

        $results = $wpdb->get_results($query, ARRAY_A) ?: [];

        return array_map(function ($row) {
            $doctorId = (int) $row['doctor_id'];
            $thumbnailId = get_post_thumbnail_id($doctorId);
            $thumbnailUrl = $thumbnailId ? wp_get_attachment_image_url($thumbnailId, 'thumbnail') : null;

            return [
                'id' => (int) $row['id'],
                'doctor_id' => $doctorId,
                'doctor_name' => $row['doctor_name'] ?? ('Médico #' . $doctorId),
                'doctor_specialty' => $row['doctor_specialty'] ?? 'Medicina General',
                'doctor_location' => $row['doctor_location'] ?? 'Consultorio Principal',
                'doctor_avatar' => $thumbnailUrl ?: null,
                'appointment_date' => $row['appointment_date'],
                'status' => $row['status'],
                'notes' => $row['notes'],
                'created_at' => $row['created_at'],
            ];
        }, $results);
    }

    public function cancelAppointment(int $appointmentId, int $patientId): bool
    {
        global $wpdb;

        $updated = $wpdb->update(
            $this->table,
            ['status' => 'cancelled'],
            [
                'id' => $appointmentId,
                'patient_id' => $patientId,
            ],
            ['%s'],
            ['%d', '%d']
        );

        return $updated !== false && $updated > 0;
    }
}