<?php

declare(strict_types=1);

namespace App\Domain\Doctors;

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

// router 
add_action('rest_api_init', function () {
    register_rest_route('api/v1', '/doctors', [
       'methods' => 'GET',
       'callback' => [app()] 
    ]);
});