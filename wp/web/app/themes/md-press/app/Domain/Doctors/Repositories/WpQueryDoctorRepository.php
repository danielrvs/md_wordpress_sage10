<?php

declare(strict_types=1);

namespace App\Domain\Doctors\Repositories;

use App\Domain\Doctors\DTOs\DoctorDTO;
use WP_Query;

class WpQueryDoctorRepository implements DoctorRepositoryInterface
{
    public function all(int $page, int $perPage = 10): array
    {
        return $this->search([], $page, $perPage);
    }

    public function findById(int $id): ?DoctorDTO
    {
        $post = get_post($id);

        if (!$post || $post->post_type !== 'doctor' || $post->post_status !== 'publish') {
            return null;
        }

        return DoctorDTO::fromPost($post);
    }

    public function search(array $filters, int $page, int $perPage = 10): array
    {
        $args = $this->buildQueryArgs($filters);
        $args['paged'] = $page;
        $args['posts_per_page'] = $perPage;

        $query = new WP_Query($args);
        $doctors = [];

        foreach ($query->posts as $post) {
            $doctors[] = DoctorDTO::fromPost($post);
        }

        return $doctors;
    }

    public function count(array $filters): int
    {
        $args = $this->buildQueryArgs($filters);

        $args['posts_per_page'] = 1;
        $args['fields'] = 'ids';
        $args['no_found_rows'] = false;

        $query = new WP_Query($args);

        return $query->found_posts;
    }

    private function buildQueryArgs(array $filters): array
    {
        $args = [
            'post_type' => 'doctor',
            'post_status' => 'publish',
            'meta_query' => [],
        ];

        if (!empty($filters['search'])) {
            $args['s'] = sanitize_text_field($filters['search']);
        }

        if (!empty($filters['specialty'])) {
            $args['meta_query'][] = [
                'key' => 'medical_specialty',
                'value' => sanitize_text_field($filters['specialty']),
                'compare' => '=',
            ];
        }

        return $args;
    }
}