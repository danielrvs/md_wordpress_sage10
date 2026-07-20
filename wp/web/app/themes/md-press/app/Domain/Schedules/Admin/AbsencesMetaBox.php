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

        wp_nonce_field('doctor_abs_nonce_action', 'doctor_abs_nonce');
        ?>
        <div style="padding: 10px 0;">
            <table class="wp-list-table widefat fixed striped" style="margin-bottom: 20px; border: 1px solid #ccd0d4;">
                <thead>
                    <tr>
                        <th style="padding: 10px;"><b>Fecha Inicio</b></th>
                        <th style="padding: 10px;"><b>Fecha Fin</b></th>
                        <th style="padding: 10px;"><b>Motivo / Descripción</b></th>
                        <th style="padding: 10px; width: 100px;"><b>Acciones</b></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($absences)): ?>
                        <tr>
                            <td colspan="4" style="color:#666; font-style:italic; padding: 10px;">
                                Este médico no tiene periodos de vacaciones programados.
                            </td>
                        </tr>
                    <?php else:
                        foreach ($absences as $absence): ?>
                            <tr>
                                <td style="padding: 10px;"><code><?php echo esc_html($absence['start_date']); ?></code></td>
                                <td style="padding: 10px;"><code><?php echo esc_html($absence['end_date']); ?></code></td>
                                <td style="padding: 10px;"><?php echo esc_html($absence['reason'] ?: 'No especificado'); ?></td>
                                <td style="padding: 10px;">
                                    <a href="<?php echo esc_url(wp_nonce_url(
                                        admin_url("post.php?post={$post->ID}&action=edit&remove_absence={$absence['id']}"),
                                        'remove_abs_' . $absence['id']
                                    )); ?>"
                                        class="button button-link-delete"
                                        style="color: #b32d2e; text-decoration: none;">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach;
                    endif; ?>
                </tbody>
            </table>

            <h4 style="border-top: 1px solid #ddd; padding-top: 15px; margin-bottom: 15px;">
                ✨ Añadir Nuevo Periodo de Ausencia
            </h4>
            <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight: 600;">Inicio:</label>
                    <input type="date" name="new_abs_start" style="height: 30px; padding: 4px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight: 600;">Fin:</label>
                    <input type="date" name="new_abs_end" style="height: 30px; padding: 4px;">
                </div>
                <div style="flex-grow: 1; min-width: 200px;">
                    <label style="display:block; margin-bottom:5px; font-weight: 600;">Motivo:</label>
                    <input type="text" name="new_abs_reason" placeholder="Ej: Vacaciones de Verano"
                        style="width: 100%; height: 30px;">
                </div>
                <div>
                    <input type="submit" name="save_absence_btn" class="button button-primary" value="Bloquear Calendario">
                </div>
            </div>
        </div>
        <?php
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
