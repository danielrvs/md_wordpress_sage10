<?php

declare(strict_types=1);

namespace App\Domain\Doctors\DTOs;

class DoctorDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $specialty,
        public string $location,
        public string $availability,
        public float $rating,
    ) {
    }

    public static function fromPost(\WP_Post $post): self
    {
        return new self(
            id: $post->ID,
            name: $post->post_title,
            specialty: get_post_meta($post->ID, 'medical_specialty', true) ?: 'General',
            location: get_post_meta($post->ID, 'medical_location', true) ?: 'No especificada',
            availability: get_post_meta($post->ID, 'medical_availability', true) ?: 'Bajo consulta',
            rating: (float) (get_post_meta($post->ID, 'medical_rating', true) ?: 0.0)
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'specialty' => $this->specialty,
            'location' => $this->location,
            'availability' => $this->availability,
            'rating' => $this->rating,
        ];
    }
}

