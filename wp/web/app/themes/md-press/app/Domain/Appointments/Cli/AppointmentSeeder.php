<?php

declare(strict_types=1);

namespace App\Domain\Appointments\Cli;

use App\Domain\Appointments\Factories\AppointmentFactory;
use WP_CLI;

class AppointmentSeeder
{
    /**
     * Seed mock appointments linked to existing doctor CPT posts.
     *
     * ## OPTIONS
     *
     * [--count=<count>]
     * : Number of appointments to seed.
     * [default: 50]
     *
     * [--clear]
     * : Truncates the appointments table before seeding.
     *
     * ## EXAMPLES
     *
     *     wp appointment:seed --count=100 --clear
     *
     * @when after_wp_load
     */
    public function __invoke(array $args, array $assocArgs): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'doctor_appointments';

        if (isset($assocArgs['clear'])) {
            WP_CLI::log('Vaciando tabla de citas previas...');
            $wpdb->query("TRUNCATE TABLE {$table}");
            WP_CLI::success('Tabla de citas truncada correctamente.');
        }

        $count = isset($assocArgs['count']) ? (int) $assocArgs['count'] : 50;

        $doctorIds = get_posts([
            'post_type'      => 'doctor',
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'posts_per_page' => -1,
        ]);

        if (empty($doctorIds)) {
            WP_CLI::error('No se encontraron médicos. Por favor, ejecuta primero: wp doctor:seed');
            return;
        }

        WP_CLI::log(sprintf('Generando %d citas ficticias para %d médicos...', $count, count($doctorIds)));

        $factory = AppointmentFactory::new();
        $inserted = 0;

        for ($i = 0; $i < $count; $i++) {
            $doctorId = $doctorIds[array_rand($doctorIds)];
            $data = $factory->definition([
                'doctor_id' => $doctorId,
            ]);

            $wpdb->insert($table, [
                'doctor_id'        => $data['doctor_id'],
                'patient_id'       => $data['patient_id'],
                'clinic_id'        => $data['clinic_id'],
                'appointment_date' => $data['appointment_date'],
                'status'           => $data['status'],
                'notes'            => $data['notes'],
            ]);

            $inserted++;
        }

        WP_CLI::success(sprintf('Se han insertado %d citas de prueba.', $inserted));
    }
}
