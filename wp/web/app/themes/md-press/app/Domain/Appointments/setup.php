<?php

declare(strict_types=1);

// CLI Commands
if (defined('WP_CLI')) {
    \WP_CLI::add_command('appointment:migrate', \App\Domain\Appointments\Cli\AppointmentMigrator::class);
    \WP_CLI::add_command('appointment:seed', \App\Domain\Appointments\Cli\AppointmentSeeder::class);
}

// Admin: página de gestión de citas en el menú lateral
(new \App\Domain\Appointments\Admin\AppointmentsAdminPage())->register();