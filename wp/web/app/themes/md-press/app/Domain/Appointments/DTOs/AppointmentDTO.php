<?php

declare(strict_types=1);

namespace App\Domain\Appointments\DTOs;

class AppointmentDTO
{
    public function __construct(
        public int $id,
        public int $doctorId,
        public int $patientId,
        public int $clinicId,
        public string $dateTime,
        public string $status, // 'pending', 'confirmed', 'cancelled', 'completed'
        public ?string $notes
    ) {
    }

    public static function fromEloquent(object $model): self
    {
        return new self(
            id: (int) $model->id,
            doctorId: (int) $model->doctor_id,
            patientId: (int) $model->patient_id,
            clinicId: (int) $model->clinic_id,
            dateTime: $model->appointment_date,
            status: $model->status,
            notes: $model->notes
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'doctorId' => $this->doctorId,
            'patientId' => $this->patientId,
            'clinicId' => $this->clinicId,
            'dateTime' => $this->dateTime,
            'status' => $this->status,
            'notes' => $this->notes,
        ];
    }
}