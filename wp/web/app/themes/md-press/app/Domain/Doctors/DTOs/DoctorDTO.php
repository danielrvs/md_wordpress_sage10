<?php

declare(strict_types=1);

namespace App\Domain\Doctors\DTOs;

use App\Domain\Doctors\Enums\Specialty;
use WP_Post;

class DoctorDTO
{
    /**
     * @param Specialty[] $specialty
     */
    public function __construct(
        public int $id,
        public string $name,
        public array $specialty, // Tipado como array, pero semánticamente contiene solo Specialties
        public string $location,
        public string $availability,
        public float $rating,
        public ?string $thumbnail,
        public ?string $permalink,
        public ?int $assignedUserId
    ) {
    }

    public static function fromPost(WP_Post $post): self
    {
        $metaSpecialty = get_post_meta($post->ID, 'medical_specialty', true);
        $rawSpecialties = is_array($metaSpecialty) ? $metaSpecialty : (empty($metaSpecialty) ? [] : [$metaSpecialty]);

        $specialties = array_map(
            fn($s) => Specialty::tryFrom((string) $s) ?? Specialty::UNKNOWN,
            $rawSpecialties
        );

        if (empty($specialties)) {
            $specialties = [Specialty::UNKNOWN];
        }

        $assignedUser = get_post_meta($post->ID, '_assigned_user_id', true);

        return new self(
            id: $post->ID,
            name: $post->post_title,
            specialty: $specialties,
            location: get_post_meta($post->ID, 'medical_location', true) ?: 'No especificada',
            availability: get_post_meta($post->ID, 'medical_availability', true) ?: 'Bajo consulta',
            rating: (float) (get_post_meta($post->ID, 'medical_rating', true) ?: 0.0),
            thumbnail: get_the_post_thumbnail_url($post->ID, 'thumbnail') ?: null,
            permalink: get_permalink($post->ID) ?: null,
            assignedUserId: $assignedUser ? (int) $assignedUser : null
        );
    }

    public static function fromArray(array $data): self
    {
        $specialties = array_map(
            fn($s) => Specialty::tryFrom((string) $s) ?? Specialty::UNKNOWN,
            $data['specialty']
        );

        return new self(
            id: (int) $data['id'],
            name: (string) $data['name'],
            specialty: $specialties,
            location: (string) $data['location'],
            availability: (string) $data['availability'],
            rating: (float) ($data['rating'] ?? 0.0),
            thumbnail: $data['thumbnail'] ?? null,
            permalink: $data['permalink'] ?? null,
            assignedUserId: isset($data['assigned_user_id']) ? (int) $data['assigned_user_id'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // Extraemos el string nativo (->value) para que viaje limpio a Redis o a React
            'specialty' => array_map(fn(Specialty $s) => $s->value, $this->specialty),
            'location' => $this->location,
            'availability' => $this->availability,
            'rating' => $this->rating,
            'thumbnail' => $this->thumbnail,
            'permalink' => $this->permalink,
            'assigned_user_id' => $this->assignedUserId,
        ];
    }
}