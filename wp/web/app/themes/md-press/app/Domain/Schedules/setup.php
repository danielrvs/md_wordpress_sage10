<?php

declare(strict_types=1);

if (defined('WP_CLI') && WP_CLI) {
    \WP_CLI::add_command('schedule:migrate', \App\Domain\Schedules\Cli\ScheduleMigrator::class);
    \WP_CLI::add_command('schedule:seed', \App\Domain\Schedules\Cli\ScheduleSeeder::class);
}

// TODO; añadir invalidación de caché cuando se creen citas para un médico
// Cache::forget(sprintf('doctor_schedules:id_%d:date_%s', $doctorId, $date));