<?php

declare(strict_types=1);

namespace App\Domain\Doctors\Cli;

use App\Domain\Doctors\Factories\DoctorFactory;
use WP_CLI;

class DoctorSeeder
{
    public function __invoke(array $args, array $assocArgs): void
    {
        $count = (int) $assocArgs['count'];
        WP_CLI::log("Iniciando el sembrado de {$count} médicos aleatorios a través del Factory...");

        $defaultAdminId = $this->getDefaultAdminId();
        $mockDoctors = DoctorFactory::new()->count($count);
        
        $insertedCount = $this->seedDoctors($mockDoctors, $defaultAdminId);

        if ($insertedCount > 0) {
            $this->invalidateCache();
            WP_CLI::success("Seeder completado con éxito! Se han añadido {$insertedCount} médicos.");
        }
    }

    private function getDefaultAdminId(): ?int
    {
        $admins = get_users(['role' => 'administrator', 'number' => 1]);
        return !empty($admins) ? (int) $admins[0]->ID : null;
    }

    private function seedDoctors(array $mockDoctors, ?int $defaultAdminId): int
    {
        $insertedCount = 0;

        foreach ($mockDoctors as $data) {
            if ($this->doctorExists($data['name'])) {
                continue;
            }

            $postId = $this->insertDoctorPost($data['name'], $data['bio']);
            if ($postId === null) {
                continue;
            }

            $this->updateDoctorMetadata($postId, $data, $defaultAdminId);
            
            WP_CLI::log("- Creado: {$data['name']} [" . implode(', ', $data['specialty']) . "]");
            $insertedCount++;
        }

        return $insertedCount;
    }

    private function doctorExists(string $name): bool
    {
        $existing = new \WP_Query([
            'post_type'              => 'doctor',
            'title'                  => $name,
            'post_status'            => 'any',
            'posts_per_page'         => 1,
            'no_found_rows'          => true,
            'ignore_sticky_posts'    => true,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
        ]);

        return $existing->have_posts();
    }

    private function insertDoctorPost(string $name, string $bio): ?int
    {
        $postId = wp_insert_post([
            'post_title'   => $name,
            'post_content' => $bio,
            'post_type'    => 'doctor',
            'post_status'  => 'publish',
        ]);

        return is_wp_error($postId) ? null : (int) $postId;
    }

    private function updateDoctorMetadata(int $postId, array $data, ?int $defaultAdminId): void
    {
        update_field('medical_specialty', $data['specialty'], $postId);
        update_field('medical_location', $data['location'], $postId);
        update_field('medical_availability', $data['availability'], $postId);
        update_field('medical_rating', $data['rating'], $postId);
        update_post_meta($postId, '_mock_avatar_url', $data['avatar_url']);

        if ($defaultAdminId) {
            update_field('_assigned_user_id', $defaultAdminId, $postId);
        }
    }

    private function invalidateCache(): void
    {
        if (function_exists('App\Domain\Doctors\invalidate_doctor_cache')) {
            \App\Domain\Doctors\invalidate_doctor_cache(0);
        }
    }
}