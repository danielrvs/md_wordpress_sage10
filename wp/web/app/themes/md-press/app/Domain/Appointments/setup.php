<?php

declare(strict_types=1);

if (defined('WP_CLI')) {
    \WP_CLI::add_command('appointment:migrate', App\Domain\Appointments\Cli\AppointmentMigrator::class);
}