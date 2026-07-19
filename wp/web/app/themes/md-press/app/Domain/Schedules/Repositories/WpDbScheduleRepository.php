<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Repositories;

use App\Domain\Schedules\Contracts\ScheduleRepositoryInterface;

class WpDbScheduleRepository implements ScheduleRepositoryInterface
{
    protected string $tableWeekly;
    protected string $tableAbsences;

    public function __construct()
    {
        global $wpdb;
        $this->tableWeekly = $wpdb->prefix . 'doctor_weekly_schedules';
        $this->tableAbsences = $wpdb->prefix . 'doctor_absences';
    }

    public function getWeeklyRules(int $doctorId, int $dayOfWeek): array
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT start_time, end_time, slot_duration 
             FROM {$this->tableWeekly} 
             WHERE doctor_id = %d AND day_of_week = %d",
            $doctorId,
            $dayOfWeek
        );

        $results = $wpdb->get_results($query, ARRAY_A);

        return $results ?: [];
    }

    public function hasAbsence(int $doctorId, string $date): bool
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT COUNT(*) 
             FROM {$this->tableAbsences} 
             WHERE doctor_id = %d 
               AND %s BETWEEN start_date AND end_date",
            $doctorId,
            $date
        );

        return (int) $wpdb->get_var($query) > 0;
    }

    public function syncWeeklyRules(int $doctorId, array $rules): void
    {
        global $wpdb;

        $wpdb->query('START TRANSACTION');

        $wpdb->delete($this->tableWeekly, ['doctor_id' => $doctorId], ['%d']);

        foreach ($rules as $rule) {
            $wpdb->insert($this->tableWeekly, [
                'doctor_id' => $doctorId,
                'day_of_week' => (int) $rule['day_of_week'],
                'start_time' => sanitize_text_field($rule['start_time']),
                'end_time' => sanitize_text_field($rule['end_time']),
                'slot_duration' => (int) $rule['slot_duration'],
            ]);
        }

        $wpdb->query('COMMIT');
    }
}