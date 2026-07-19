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
        public ?string $thumbnail,
        public ?string $permalink,
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
            rating: (float) (get_post_meta($post->ID, 'medical_rating', true) ?: 0.0),
            thumbnail: get_the_post_thumbnail_url($post->ID, 'thumbnail') ?: null,
            permalink: get_permalink($post->ID),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            specialty: $data['specialty'],
            location: $data['location'],
            availability: $data['availability'],
            rating: (float) ($data['rating'] ?? 0.0),
            thumbnail: $data['thumbnail'] ?? null,
            permalink: $data['permalink'] ?? null,
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
            'thumbnail' => $this->thumbnail,
            'permalink' => $this->permalink,
        ];
    }
}

