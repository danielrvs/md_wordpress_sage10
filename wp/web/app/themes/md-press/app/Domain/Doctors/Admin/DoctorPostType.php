<?php

declare(strict_types=1);

namespace App\Domain\Doctors\Admin;

/**
 * Registra el Custom Post Type 'doctor' en WordPress.
 */
class DoctorPostType
{
    public function register(): void
    {
        add_action('init', [$this, 'registerPostType']);
    }

    public function registerPostType(): void
    {
        register_post_type('doctor', [
            'labels' => [
                'name' => 'Médico',
                'singular_name' => 'Médico',
                'menu_name' => 'Directorio Médico',
                'all_items' => 'Todos los Médicos',
                'add_new' => 'Añadir Nuevo',
                'add_new_item' => 'Añadir Nuevo Médico',
                'edit_item' => 'Editar Médico',
                'menu_icon' => 'dashicons-database-add',
            ],
            'public' => true,
            'has_archive' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'doctors', 'with_front' => false],
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        ]);
    }
}
