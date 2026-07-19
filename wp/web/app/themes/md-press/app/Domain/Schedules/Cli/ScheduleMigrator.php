<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Cli;

use WP_CLI;

class ScheduleMigrator
{

    public function __invoke(array $args, array $assocArgs): void
    {
        global $wpdb;

        WP_CLI::log('Iniciando migración de tablas de horarios...');

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charsetCollate = $wpdb->get_charset_collate();

        $tableWeekly = $wpdb->prefix . 'doctor_weekly_schedules';
        $sqlWeekly = "CREATE TABLE $tableWeekly (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            doctor_id bigint(20) unsigned NOT NULL,
            day_of_week tinyint(4) NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            slot_duration int(11) NOT NULL,
            PRIMARY KEY  (id),
            KEY doctor_day (doctor_id,day_of_week)
        ) $charsetCollate;";

        $tableAbsences = $wpdb->prefix . 'doctor_absences';
        $sqlAbsences = "CREATE TABLE $tableAbsences (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            doctor_id bigint(20) unsigned NOT NULL,
            start_date date NOT NULL,
            end_date date NOT NULL,
            reason varchar(255) DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY doctor_dates (doctor_id,start_date,end_date)
        ) $charsetCollate;";

        $resultWeekly = dbDelta($sqlWeekly);
        $resultAbsences = dbDelta($sqlAbsences);

        $this->logResult($tableWeekly, $resultWeekly);
        $this->logResult($tableAbsences, $resultAbsences);

        WP_CLI::success('¡Proceso de migración finalizado con éxito!');
    }

    private function logResult(string $tableName, array $result): void
    {
        if (empty($result)) {
            WP_CLI::log("- Tabla '$tableName': Sin cambios (Ya actualizada).");
        } else {
            foreach ($result as $message) {
                WP_CLI::log("- Tarjeta '$tableName': " . trim($message));
            }
        }
    }
}