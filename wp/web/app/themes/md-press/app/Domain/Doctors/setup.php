<?php

declare(strict_types=1);

namespace App\Domain\Doctors;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

add_action('init', function () {
    $labels = [
        'name' => 'Médicos',
        'singular_name' => 'Médico',
        'menu_name' => 'Directorio Médico',
        'all_items' => 'Todos los Médicos',
        'add_new' => 'Añadir Nuevo',
        'add_new_item' => 'Añadir Nuevo Médico',
        'edit_item' => 'Editar Médico',
        'menu_icon' => 'dashicons-medical',
    ];

    $args = [
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'medicos', 'with_front' => false],
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
    ];

    register_post_type('doctor', $args);
});

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

// router 
add_action('rest_api_init', function () {
    register_rest_route('api/v1', '/doctors', [
        'methods' => 'GET',
        'callback' => [app()]
    ]);
});