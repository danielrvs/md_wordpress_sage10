<?php

declare(strict_types=1);

namespace App\Domain\Appointments\Admin;

use App\Domain\Appointments\Services\GetDoctorAppointmentsService;
use DateTime;

/**
 * Registra y renderiza la página de administración de citas del menú lateral de WordPress.
 * Incluye la vista de catálogo de médicos y la agenda detallada por doctor con filtros.
 */
class AppointmentsAdminPage
{
    private const MENU_SLUG = 'doctor-appointments';

    private const STATUS_COLORS = [
        'pending' => ['bg' => '#fef0f0', 'text' => '#f56c6c', 'border' => '#fde2e2'],
        'confirmed' => ['bg' => '#f0f9eb', 'text' => '#67c23a', 'border' => '#e1f3d8'],
        'cancelled' => ['bg' => '#f4f4f5', 'text' => '#909399', 'border' => '#e9e9eb'],
        'completed' => ['bg' => '#ecf5ff', 'text' => '#409eff', 'border' => '#d9ecff'],
    ];

    public function register(): void
    {
        add_action('admin_menu', [$this, 'registerMenuPage']);
    }

    public function registerMenuPage(): void
    {
        add_menu_page(
            'Gestión de Citas',
            'Citas',
            'edit_posts',
            self::MENU_SLUG,
            [$this, 'render'],
            'dashicons-calendar-alt',
            25
        );
    }

    public function render(): void
    {
        $doctorId = isset($_GET['doctor_id']) ? (int) $_GET['doctor_id'] : 0;

        echo '<div class="wrap">';

        if ($doctorId > 0 && get_post_type($doctorId) === 'doctor') {
            $this->renderDoctorAgendaView($doctorId);
        } else {
            $this->renderDoctorsCatalogView();
        }

        echo '</div>';
    }

    /**
     * Vista 1: Catálogo de médicos con contador de citas activas.
     */
    private function renderDoctorsCatalogView(): void
    {
        $doctors = get_posts([
            'post_type' => 'doctor',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

        $service = app(GetDoctorAppointmentsService::class);
        ?>
        <h1 class="wp-heading-inline">Control Central de Citas</h1>
        <p class="description">Selecciona un médico de la lista para gestionar sus próximas consultas programadas.</p>
        <hr class="wp-header-end">

        <table class="wp-list-table widefat fixed striped pages" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th style="padding: 12px; font-weight: bold;">Médico</th>
                    <th style="padding: 12px; font-weight: bold; width: 180px;">Próximas Citas</th>
                    <th style="padding: 12px; font-weight: bold; width: 120px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($doctors)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 20px; color: #666;">
                            No se encontraron médicos publicados.
                        </td>
                    </tr>
                <?php else:
                    foreach ($doctors as $doctor):
                        $count = count($service->execute($doctor->ID));
                        $agendaUrl = admin_url('admin.php?page=' . self::MENU_SLUG . '&doctor_id=' . $doctor->ID);
                        ?>
                        <tr>
                            <td style="padding: 12px; font-size: 14px;">
                                <strong>
                                    <a href="<?php echo $agendaUrl; ?>" class="row-title">
                                        👤 <?php echo esc_html($doctor->post_title); ?>
                                    </a>
                                </strong>
                            </td>
                            <td style="padding: 12px;">
                                <span class="badge"
                                    style="background: #e1ecf4; color: #0c4b78; padding: 4px 8px; border-radius: 12px; font-weight: 600; font-size: 12px;">
                                    <?php echo $count; ?> citas pendientes
                                </span>
                            </td>
                            <td style="padding: 12px; text-align: right;">
                                <a href="<?php echo $agendaUrl; ?>" class="button button-primary button-small">
                                    Ver Agenda
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
            </tbody>
        </table>
        <?php
    }

    /**
     * Vista 2: Agenda detallada de un médico con filtros de fecha y ordenación.
     */
    private function renderDoctorAgendaView(int $doctorId): void
    {
        $doctorName = get_the_title($doctorId);
        $filterDate = isset($_GET['filter_date']) ? sanitize_text_field($_GET['filter_date']) : '';
        $orderDir = (isset($_GET['order_dir']) && $_GET['order_dir'] === 'desc') ? 'DESC' : 'ASC';

        $appointments = app(GetDoctorAppointmentsService::class)
            ->execute($doctorId, $filterDate ?: null, $orderDir);

        $backUrl = admin_url('admin.php?page=' . self::MENU_SLUG);
        $agendaBase = admin_url('admin.php?page=' . self::MENU_SLUG . '&doctor_id=' . $doctorId);
        ?>
        <a href="<?php echo $backUrl; ?>" class="page-title-action" style="margin-right: 8px;">← Volver al
            listado</a>
        <h1 class="wp-heading-inline">Agenda de: <?php echo esc_html($doctorName); ?></h1>
        <hr class="wp-header-end">

        <!-- BARRA DE FILTROS -->
        <div class="tablenav top"
            style="margin: 20px 0 10px 0; height: auto; padding: 10px; background: #fff; border: 1px solid #ccd0d4;">
            <form method="get" action="<?php echo admin_url('admin.php'); ?>"
                style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <input type="hidden" name="page" value="<?php echo self::MENU_SLUG; ?>">
                <input type="hidden" name="doctor_id" value="<?php echo $doctorId; ?>">

                <div class="alignleft actions">
                    <label for="filter_date" style="font-weight: 600; margin-right: 5px;">Filtrar por Día:</label>
                    <input type="date" id="filter_date" name="filter_date" value="<?php echo esc_attr($filterDate); ?>"
                        style="height: 30px;">
                </div>

                <div class="alignleft actions">
                    <label for="order_dir" style="font-weight: 600; margin-right: 5px;">Cronología:</label>
                    <select id="order_dir" name="order_dir" style="height: 30px; min-width: 160px;">
                        <option value="asc" <?php selected($orderDir, 'ASC'); ?>>Más próximas primero</option>
                        <option value="desc" <?php selected($orderDir, 'DESC'); ?>>Más lejanas primero</option>
                    </select>
                </div>

                <div class="alignleft actions">
                    <input type="submit" class="button action button-secondary" value="Aplicar Filtros">
                    <?php if ($filterDate): ?>
                        <a href="<?php echo $agendaBase; ?>" class="button button-link"
                            style="color: #b32d2e; text-decoration: none; margin-left: 10px;">
                            Limpiar Filtro
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- TABLA DE RESULTADOS -->
        <table class="wp-list-table widefat fixed striped pages" style="border: 1px solid #ccd0d4;">
            <thead>
                <tr>
                    <th style="padding: 12px; width: 80px;"><b>Cita ID</b></th>
                    <th style="padding: 12px; width: 160px;"><b>Fecha y Hora</b></th>
                    <th style="padding: 12px;"><b>Paciente</b></th>
                    <th style="padding: 12px; width: 100px;"><b>Clínica ID</b></th>
                    <th style="padding: 12px; width: 120px;"><b>Estado</b></th>
                    <th style="padding: 12px;"><b>Notas de Consulta</b></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($appointments)): ?>
                    <tr>
                        <td colspan="6" style="color:#666; font-style:italic; padding: 25px; text-align: center; font-size: 14px;">
                            <?php echo $filterDate
                                ? 'No se encontraron citas para el día seleccionado.'
                                : 'Este médico no tiene citas pendientes.'; ?>
                        </td>
                    </tr>
                <?php else:
                    foreach ($appointments as $appointment):
                        $formattedDate = (new DateTime($appointment->dateTime))->format('d/m/Y H:i');
                        $style = self::STATUS_COLORS[$appointment->status] ?? ['bg' => '#fff', 'text' => '#000', 'border' => '#ccc'];
                        ?>
                        <tr>
                            <td style="padding: 12px;"><code>#<?php echo $appointment->id; ?></code></td>
                            <td style="padding: 12px; font-size: 13px;"><b>🗓️ <?php echo esc_html($formattedDate); ?> hs</b></td>
                            <td style="padding: 12px;">👤 Paciente ID: <code><?php echo $appointment->patientId; ?></code></td>
                            <td style="padding: 12px;">🏥 ID: <?php echo $appointment->clinicId; ?></td>
                            <td style="padding: 12px;">
                                <span
                                    style="background: <?php echo $style['bg']; ?>; color: <?php echo $style['text']; ?>; border: 1px solid <?php echo $style['border']; ?>; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; display: inline-block;">
                                    <?php echo esc_html($appointment->status); ?>
                                </span>
                            </td>
                            <td style="padding: 12px; color: #555; font-size: 12px; line-height: 1.4;">
                                <?php echo esc_html($appointment->notes ?: 'Sin observaciones'); ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
            </tbody>
        </table>
        <?php
    }
}
