<?php

declare(strict_types=1);

namespace App\Domain\Appointments\Admin;

use App\Domain\Appointments\Services\GetDoctorAppointmentsService;
use DateTime;

/**
 * Registra y renderiza la página de administración de citas del menú lateral de WordPress.
 * La presentación se delega a plantillas Blade en resources/views/admin/appointments/.
 */
class AppointmentsAdminPage
{
    private const MENU_SLUG = 'doctor-appointments';

    private const STATUS_COLORS = [
        'pending'   => ['bg' => '#fef0f0', 'text' => '#f56c6c', 'border' => '#fde2e2'],
        'confirmed' => ['bg' => '#f0f9eb', 'text' => '#67c23a', 'border' => '#e1f3d8'],
        'cancelled' => ['bg' => '#f4f4f5', 'text' => '#909399', 'border' => '#e9e9eb'],
        'completed'  => ['bg' => '#ecf5ff', 'text' => '#409eff', 'border' => '#d9ecff'],
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

        if ($doctorId > 0 && get_post_type($doctorId) === 'doctor') {
            $this->renderDoctorAgendaView($doctorId);
        } else {
            $this->renderDoctorsCatalogView();
        }
    }


    private function renderDoctorsCatalogView(): void
    {
        $doctors = get_posts([
            'post_type'      => 'doctor',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        echo view('admin.appointments.catalog', [
            'doctors'  => $doctors,
            'service'  => app(GetDoctorAppointmentsService::class),
            'menuSlug' => self::MENU_SLUG,
        ])->render();
    }


    private function renderDoctorAgendaView(int $doctorId): void
    {
        $filterDate = isset($_GET['filter_date']) ? sanitize_text_field($_GET['filter_date']) : '';
        $orderDir   = (isset($_GET['order_dir']) && $_GET['order_dir'] === 'desc') ? 'DESC' : 'ASC';

        echo view('admin.appointments.agenda', [
            'doctorId'     => $doctorId,
            'doctorName'   => get_the_title($doctorId),
            'appointments' => app(GetDoctorAppointmentsService::class)->execute($doctorId, $filterDate ?: null, $orderDir),
            'filterDate'   => $filterDate,
            'orderDir'     => $orderDir,
            'statusColors' => self::STATUS_COLORS,
            'menuSlug'     => self::MENU_SLUG,
            'backUrl'      => admin_url('admin.php?page=' . self::MENU_SLUG),
            'agendaBase'   => admin_url('admin.php?page=' . self::MENU_SLUG . '&doctor_id=' . $doctorId),
        ])->render();
    }
}
