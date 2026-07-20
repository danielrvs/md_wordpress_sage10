<?php

declare(strict_types=1);

namespace App\Domain\Appointments\Cli;

use WP_CLI;

final class AppointmentMigrator
{
    /**
     * Crea la tabla personalizada de citas mapeada con el AppointmentDTO.
     *
     * ## EXAMPLES
     *
     *     wp appointment:migrate
     *
     * @when after_wp_load
     */
    public function __invoke(): void
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'doctor_appointments';
        $charsetCollate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $tableName (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            doctor_id BIGINT(20) UNSIGNED NOT NULL,
            patient_id BIGINT(20) UNSIGNED NOT NULL,
            clinic_id BIGINT(20) UNSIGNED NOT NULL,
            appointment_date DATETIME NOT NULL,
            status VARCHAR(50) DEFAULT 'pending' NOT NULL,
            notes TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY doctor_date_idx (doctor_id, appointment_date),
            KEY patient_idx (patient_id),
            KEY clinic_idx (clinic_id)
        ) $charsetCollate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        WP_CLI::success("Tabla '$tableName' sincronizada correctamente con el esquema del DTO.");
    }
}