<?php

declare(strict_types=1);

namespace App\Domain\Appointments\DTOs;

class CreateAppointmentDTO
{
    public function __construct(
        public int $doctorId,
        public int $patientId,
        public int $clinicId,
        public string $appointmentDate,
        public string $startTime,
        public string $status = 'confirmed',
        public ?string $notes = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        $doctorId = (int) ($data['doctor_id'] ?? $data['doctorId'] ?? 0);
        $patientId = (int) ($data['patient_id'] ?? $data['patientId'] ?? 0);
        $clinicId = (int) ($data['clinic_id'] ?? $data['clinicId'] ?? 1);
        $date = (string) ($data['appointment_date'] ?? $data['date'] ?? '');
        $startTime = (string) ($data['start_time'] ?? $data['startTime'] ?? '');
        $status = (string) ($data['status'] ?? 'confirmed');
        $notes = isset($data['notes']) && !empty($data['notes']) ? (string) $data['notes'] : null;

        return new self(
            doctorId: $doctorId,
            patientId: $patientId,
            clinicId: $clinicId,
            appointmentDate: $date,
            startTime: $startTime,
            status: $status,
            notes: $notes
        );
    }
}
