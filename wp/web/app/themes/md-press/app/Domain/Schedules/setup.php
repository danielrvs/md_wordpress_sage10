<?php

declare(strict_types=1);

if (defined('WP_CLI') && WP_CLI) {
    \WP_CLI::add_command('schedule:migrate', \App\Domain\Schedules\Cli\ScheduleMigrator::class);
    \WP_CLI::add_command('schedule:seed', \App\Domain\Schedules\Cli\ScheduleSeeder::class);
}

// TODO; añadir invalidación de caché cuando se creen citas para un médico
// Cache::forget(sprintf('doctor_schedules:id_%d:date_%s', $doctorId, $date));

//router
add_action('rest_api_init', function () {
    register_rest_route('api/v1', '/doctors/(?P<id>\d+)/schedule', [
        'methods' => 'GET',
        'callback' => [app(\App\Domain\Schedules\Http\Controllers\DoctorScheduleController::class), 'getSchedule'],
        'permission_callback' => '__return_true',
        'args' => [
            'date' => [
                'required' => true,
                'validate_callback' => function (mixed $param): bool {
                    if (!is_string($param)) {
                        return false;
                    }
                    $d = \DateTime::createFromFormat('Y-m-d', $param);
                    return $d && $d->format('Y-m-d') === $param;
                },
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ],
    ]);
});