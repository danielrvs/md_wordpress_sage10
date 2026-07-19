<?php

declare(strict_types=1);

namespace App\Domain\Doctors;

use App\Domain\Doctors\Enums\Specialty;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

/**
 * Registrar Custom Post Type Doctor
 */

add_action('init', function () {
    $labels = [
        'name' => 'Médico',
        'singular_name' => 'Médico',
        'menu_name' => 'Directorio Médico',
        'all_items' => 'Todos los Médicos',
        'add_new' => 'Añadir Nuevo',
        'add_new_item' => 'Añadir Nuevo Médico',
        'edit_item' => 'Editar Médico',
        'menu_icon' => 'dashicons-database-add',
    ];

    $args = [
        'labels' => $labels,
        'public' => true,
        'has_archive' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'doctors', 'with_front' => false],
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
    ];

    register_post_type('doctor', $args);
});

/**
 * Invalidación de caché a partir de hooks
 */

add_action('save_post_doctor', 'App\Domain\Doctors\invalidate_doctor_cache', 10, 1);
add_action('before_delete_post', 'App\Domain\Doctors\invalidate_doctor_cache', 10, 1);


function invalidate_doctor_cache(int $postId): void
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    Cache::forget("doctors:id:{$postId}");

    try {
        $redis = Redis::connection();
        $prefix = config('cache.prefix', 'laravel_cache') . ':';
        $keys = $redis->keys($prefix . 'doctors:*');

        if (!empty($keys)) {
            foreach ($keys as $key) {
                $cleanKey = str_replace($prefix, '', $key);
                Cache::forget($cleanKey);
            }
        }
    } catch (\Throwable $e) {
        error_log('Error al invalidar la caché de médicos en Redis: ' . $e->getMessage());
    }
}

/**
 * Registrar los campos personalizados (Metadatos) para el CPT Doctor vía ACF
 */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key' => 'group_doctor_metadata',
        'title' => 'Información Profesional del Médico',
        'fields' => [
            [
                'key' => 'field_medical_specialty',
                'label' => 'Especialidad Médica',
                'name' => 'medical_specialty',
                'type' => 'select',
                'choices' => array_reduce(Specialty::cases(), function ($carry, Specialty $case) {
                    $carry[$case->value] = $case->value;
                    return $carry;
                }, []),
                'multiple' => 1,
                'ui' => 1,
                'required' => 1,
            ],
            [
                'key' => 'field_medical_location',
                'label' => 'Ubicación / Consultorio',
                'name' => 'medical_location',
                'type' => 'text',
                'required' => 1,
                'placeholder' => 'Ej: Planta 2, Consultorio 204 o Madrid, Centro',
            ],
            [
                'key' => 'field_medical_availability',
                'label' => 'Disponibilidad',
                'name' => 'medical_availability',
                'type' => 'select',
                'choices' => [
                    'Inmediata' => 'Inmediata',
                    'Esta semana' => 'Esta semana',
                    'Bajo consulta' => 'Bajo consulta',
                ],
                'default_value' => 'Bajo consulta',
                'required' => 1,
            ],
            [
                'key' => 'field_medical_rating',
                'label' => 'Puntuación Inicial (Rating)',
                'name' => 'medical_rating',
                'type' => 'number',
                'default_value' => '5.0',
                'min' => '1',
                'max' => '5',
                'step' => '0.1',
                'required' => 1,
            ],
            [
                'key' => 'field_assigned_user_id',
                'label' => 'Usuario de WordPress Vinculado',
                'name' => '_assigned_user_id',
                'type' => 'user',
                'instructions' => 'Asigna el usuario de acceso para que este médico pueda gestionar su perfil.',
                'role' => ['medical_professional', 'administrator'],
                'allow_null' => 1,
                'multiple' => 0,
                'return_format' => 'id',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'doctor',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ]);
});

/**
 * Registrar Comandos en WP-CLI
 */

if (defined('WP_CLI') && WP_CLI) {
    \WP_CLI::add_command('doctor:seed', \App\Domain\Doctors\Cli\DoctorSeeder::class);
}

// router 
add_action('rest_api_init', function () {
    register_rest_route('api/v1', '/doctors', [
        'methods' => 'GET',
        'callback' => [app(\App\Domain\Doctors\Http\Controllers\DoctorController::class), 'index'],
        'permission_callback' => '__return_true',
    ]);
});

add_action('rest_api_init', function () {
    register_rest_route('api/v1', '/specialties', [
        'methods' => 'GET',
        'callback' => [app(\App\Domain\Doctors\Http\Controllers\SpecialtyController::class), 'index'],
        'permission_callback' => '__return_true',
    ]);
});