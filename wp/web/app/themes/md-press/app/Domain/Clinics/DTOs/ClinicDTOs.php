<?php

declare(strict_types=1);

namespace App\Domain\Clinics\DTOs;

class ClinicDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $address,
        public string $city,
        public ?string $phone
    ) {
    }

    public static function fromPost(\WP_Post $post): self
    {
        return new self(
            id: $post->ID,
            name: $post->post_title,
            address: get_post_meta($post->ID, 'clinic_address', true) ?: '',
            city: get_post_meta($post->ID, 'clinic_city', true) ?: '',
            phone: get_post_meta($post->ID, 'clinic_phone', true) ?: null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'phone' => $this->phone,
        ];
    }
}