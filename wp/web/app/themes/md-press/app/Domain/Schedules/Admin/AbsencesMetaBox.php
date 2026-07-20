<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Admin;

/**
 * Registra y gestiona el Meta Box de Vacaciones y Ausencias en el CPT Doctor.
 * Incluye el renderizado del formulario, el guardado de nuevas ausencias
 * y la eliminación de ausencias existentes vía GET seguro con nonce.
 */
class AbsencesMetaBox
{
    public function register(): void
    {
        add_action('add_meta_boxes', [$this, 'addMetaBox']);
        add_action('save_post', [$this, 'handleSavePost']);
        add_action('admin_init', [$this, 'handleDeleteAbsence']);
    }

    public function addMetaBox(): void
    {
        add_meta_box(
            'doctor_absences_meta_box',
            '🌴 Gestión de Vacaciones y Ausencias (Custom Table)',
            [$this, 'renderMetaBox'],
            'doctor',
            'normal',
            'default'
        );
    }

    public function renderMetaBox(\WP_Post $post): void
    {
        $repo = app(\App\Domain\Schedules\Contracts\ScheduleRepositoryInterface::class);
        $absences = $repo->getAbsences($post->ID);

        // wp_nonce_field() imprime directamente, lo capturamos para pasarlo a la vista
        ob_start();
        wp_nonce_field('doctor_abs_nonce_action', 'doctor_abs_nonce');
        $nonceField = ob_get_clean();

        echo view('admin.schedules.absences-meta-box', [
            'postId'     => $post->ID,
            'absences'   => $absences,
            'nonceField' => $nonceField,
        ])->render();
    }

    public function handleSavePost(int $postId): void
    {
        if (get_post_type($postId) !== 'doctor' || !isset($_POST['doctor_abs_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['doctor_abs_nonce'], 'doctor_abs_nonce_action')) {
            return;
        }

        if (isset($_POST['save_absence_btn']) && !empty($_POST['new_abs_start']) && !empty($_POST['new_abs_end'])) {
            try {
                app(\App\Domain\Schedules\Services\AddAbsenceService::class)->execute(
                    $postId,
                    sanitize_text_field($_POST['new_abs_start']),
                    sanitize_text_field($_POST['new_abs_end']),
                    sanitize_text_field($_POST['new_abs_reason'] ?? '')
                );
            } catch (\Exception $e) {
                // Manejo silencioso en el guardado de WP
            }
        }
    }

    public function handleDeleteAbsence(): void
    {
        if (!isset($_GET['remove_absence'], $_GET['post'])) {
            return;
        }

        $absenceId = (int) $_GET['remove_absence'];
        $postId = (int) $_GET['post'];

        if (wp_verify_nonce($_GET['_wpnonce'] ?? '', 'remove_abs_' . $absenceId)) {
            app(\App\Domain\Schedules\Services\DeleteAbsenceService::class)->execute($absenceId);
            wp_redirect(admin_url("post.php?post={$postId}&action=edit"));
            exit;
        }
    }
}
