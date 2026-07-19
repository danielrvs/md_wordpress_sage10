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

        $admins = get_users(['role' => 'administrator', 'number' => 1]);
        $defaultAdminId = !empty($admins) ? $admins[0]->ID : null;

        $mockDoctors = DoctorFactory::new()->count($count);
        $insertedCount = 0;

        foreach ($mockDoctors as $data) {
            //validar que no haya dos médicos con el mismo nombre
            $existing = new \WP_Query([
                'post_type'              => 'doctor',
                'title'                  => $data['name'],
                'post_status'            => 'any',
                'posts_per_page'         => 1,
                'no_found_rows'          => true,
                'ignore_sticky_posts'    => true,
                'update_post_term_cache' => false,
                'update_post_meta_cache' => false,
            ]);

            if ($existing->have_posts()) {
                continue;
            }

            $postId = wp_insert_post([
                'post_title' => $data['name'],
                'post_content' => $data['bio'],
                'post_type' => 'doctor',
                'post_status' => 'publish',
            ]);

            if (is_wp_error($postId)) {
                continue;
            }

            update_field('medical_specialty', $data['specialty'], $postId);
            update_field('medical_location', $data['location'], $postId);
            update_field('medical_availability', $data['availability'], $postId);
            update_field('medical_rating', $data['rating'], $postId);

            if ($defaultAdminId) {
                update_field('_assigned_user_id', $defaultAdminId, $postId);
            }

            WP_CLI::log("- Creado: {$data['name']} [" . implode(', ', $data['specialty']) . "]");
            $insertedCount++;
        }

        if ($insertedCount > 0) {
            if (function_exists('App\Domain\Doctors\invalidate_doctor_cache')) {
                \App\Domain\Doctors\invalidate_doctor_cache(0);
            }
            WP_CLI::success("Seeder completado con éxito! Se han añadido {$insertedCount} médicos.");
        }
    }
}