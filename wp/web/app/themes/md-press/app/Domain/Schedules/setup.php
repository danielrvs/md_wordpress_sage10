<?php

declare(strict_types=1);

// CLI Commands
if (defined('WP_CLI') && WP_CLI) {
    \WP_CLI::add_command('schedule:migrate', \App\Domain\Schedules\Cli\ScheduleMigrator::class);
    \WP_CLI::add_command('schedule:seed', \App\Domain\Schedules\Cli\ScheduleSeeder::class);
}

// Admin: ACF field group for weekly schedule + sync to custom table
(new \App\Domain\Schedules\Admin\AcfScheduleFieldGroup())->register();

// Admin: Meta box for managing doctor absences/vacations
(new \App\Domain\Schedules\Admin\AbsencesMetaBox())->register();

// REST API routes
(new \App\Domain\Schedules\Http\Routes\ScheduleRoutes())->register();

// TODO: añadir invalidación de caché cuando se creen citas para un médico
// VersionedCache::forget('doctor_schedules', $doctorId, "date_{$date}");