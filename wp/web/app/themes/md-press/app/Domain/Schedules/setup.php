<?php

declare(strict_types=1);

if (defined('WP_CLI') && WP_CLI) {
    \WP_CLI::add_command('schedule:migrate', \App\Domain\Schedules\Cli\ScheduleMigrator::class);
    \WP_CLI::add_command('schedule:seed', \App\Domain\Schedules\Cli\ScheduleSeeder::class);
}