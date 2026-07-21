<?php

declare(strict_types=1);

namespace App\Domain\Pages\Cli;

use WP_CLI;

class PageSeeder
{
    /**
     * Seed essential static pages in WordPress (e.g. Pricing, Doctors directory).
     *
     * ## EXAMPLES
     *
     *     wp page:seed
     *
     * @when after_wp_load
     */
    public function __invoke(array $args, array $assocArgs): void
    {
        $pages = [
            [
                'title'   => 'Planes y Tarifas',
                'slug'    => 'pricing',
                'content' => '',
            ],
            [
                'title'   => 'Directorio Médico',
                'slug'    => 'doctors',
                'content' => '',
            ],
        ];

        $created = 0;

        foreach ($pages as $page) {
            $existing = get_page_by_path($page['slug']);
            if ($existing instanceof \WP_Post) {
                WP_CLI::log(sprintf('La página "%s" (/%s) ya existe (ID: %d).', $page['title'], $page['slug'], $existing->ID));
                continue;
            }

            $pageId = wp_insert_post([
                'post_title'   => $page['title'],
                'post_name'    => $page['slug'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => $page['content'],
            ]);

            if (is_wp_error($pageId)) {
                WP_CLI::warning(sprintf('Error al crear la página "%s": %s', $page['title'], $pageId->get_error_message()));
            } else {
                WP_CLI::success(sprintf('Página "%s" creada correctamente (ID: %d, /%s).', $page['title'], $pageId, $page['slug']));
                $created++;
            }
        }

        WP_CLI::success(sprintf('Sembrado de páginas completado. %d páginas creadas.', $created));
    }
}
