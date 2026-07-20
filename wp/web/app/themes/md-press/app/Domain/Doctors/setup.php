<?php

declare(strict_types=1);

// CLI Commands
if (defined('WP_CLI') && WP_CLI) {
    \WP_CLI::add_command('doctor:seed', \App\Domain\Doctors\Cli\DoctorSeeder::class);
}

// CPT registration
(new \App\Domain\Doctors\Admin\DoctorPostType())->register();

// Cache invalidation on save/delete
(new \App\Domain\Doctors\Admin\DoctorCacheInvalidator())->register();

// Admin: ACF field group with doctor professional info
(new \App\Domain\Doctors\Admin\AcfDoctorFieldGroup())->register();

// REST API routes
(new \App\Domain\Doctors\Http\Routes\DoctorRoutes())->register();