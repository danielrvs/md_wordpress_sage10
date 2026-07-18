<?php

declare(strict_types=1);

namespace App\Domain\Patients\DTOs;

class PatientDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $phone,
        public ?string $medicalInsurance
    ) {}

    public static function fromUser(\WP_User $user): self
    {
        return new self(
            id: $user->ID,
            name: $user->display_name,
            email: $user->user_email,
            phone: get_user_meta($user->ID, 'patient_phone', true) ?: null,
            medicalInsurance: get_user_meta($user->ID, 'patient_insurance', true) ?: null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'medicalInsurance' => $this->medicalInsurance,
        ];
    }
}