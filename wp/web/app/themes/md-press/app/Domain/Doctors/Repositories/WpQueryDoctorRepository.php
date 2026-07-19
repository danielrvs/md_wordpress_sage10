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

        if (!empty($filters['specialty'])) {
            $args['meta_query'][] = [
                'key' => 'medical_specialty',
                'value' => sanitize_text_field($filters['specialty']),
                'compare' => 'LIKE',
            ];
        }

        if (!empty($filters['search'])) {
            $keyword = sanitize_text_field($filters['search']);

            $queryS = new WP_Query([
                'post_type' => 'doctor',
                'post_status' => 'publish',
                's' => $keyword,
                'fields' => 'ids',
                'posts_per_page' => -1,
                'no_found_rows' => true,
            ]);
            $idsFromS = $queryS->posts;

            $queryMeta = new WP_Query([
                'post_type' => 'doctor',
                'post_status' => 'publish',
                'fields' => 'ids',
                'posts_per_page' => -1,
                'no_found_rows' => true,
                'meta_query' => [
                    'relation' => 'OR',
                    [
                        'key' => 'medical_specialty',
                        'value' => $keyword,
                        'compare' => 'LIKE',
                    ],
                    [
                        'key' => 'medical_location',
                        'value' => $keyword,
                        'compare' => 'LIKE',
                    ]
                ]
            ]);
            $idsFromMeta = $queryMeta->posts;

            $matchedIds = array_unique(array_merge($idsFromS, $idsFromMeta));

            if (!empty($matchedIds)) {
                $args['post__in'] = $matchedIds;
            } else {
                $args['post__in'] = [0];
            }
        }

        return $args;
    }
}