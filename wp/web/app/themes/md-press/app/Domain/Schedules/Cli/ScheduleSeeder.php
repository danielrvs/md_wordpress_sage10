<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Cli;

use Faker\Factory as Faker;
use Faker\Generator;
use WP_CLI;

class ScheduleSeeder
{
    protected string $tableWeekly;
    protected string $tableAbsences;
    protected Generator $faker;

    /**
     * Seed custom schedules and absences linking them to existing doctors CPT posts.
     *
     * ## OPTIONS
     *
     * [--clear]
     * : Truncates schedule tables before inserting the mock data.
     *
     * ## EXAMPLES
     *
     *     wp schedule:seed --clear
     *
     * @when after_wp_load
     */
    public function __invoke(array $args, array $assocArgs): void
    {
        global $wpdb;

        $this->faker = Faker::create('es_ES');
        $this->tableWeekly = $wpdb->prefix . 'doctor_weekly_schedules';
        $this->tableAbsences = $wpdb->prefix . 'doctor_absences';

        if (isset($assocArgs['clear'])) {
            $this->clearTables();
        }

        $doctorIds = $this->getDoctorIds();
        if (empty($doctorIds)) {
            WP_CLI::error('No se encontraron médicos en el CPT "doctor". Ejecuta primero: wp doctor:seed');
            return;
        }

        WP_CLI::log(sprintf('Generando horarios para %d médicos...', count($doctorIds)));

        $this->seedAllSchedules($doctorIds);
    }

    private function clearTables(): void
    {
        global $wpdb;
        WP_CLI::log('Vaciando tablas de horarios previas...');
        $wpdb->query("TRUNCATE TABLE {$this->tableWeekly}");
        $wpdb->query("TRUNCATE TABLE {$this->tableAbsences}");
        WP_CLI::success('Tablas truncadas correctamente.');
    }

    private function getDoctorIds(): array
    {
        return get_posts([
            'post_type' => 'doctor',
            'post_status' => 'publish',
            'fields' => 'ids',
            'posts_per_page' => -1,
        ]);
    }

    private function seedAllSchedules(array $doctorIds): void
    {
        $shifts = [
            'morning' => ['start' => '08:00:00', 'end' => '14:00:00'],
            'afternoon' => ['start' => '15:00:00', 'end' => '20:00:00'],
        ];

        $durations = [15, 20, 30, 60];
        $insertedSchedules = 0;
        $insertedAbsences = 0;

        foreach ($doctorIds as $doctorId) {
            $insertedSchedules += $this->seedWeeklyScheduleForDoctor($doctorId, $shifts, $durations);
            $insertedAbsences += $this->seedAbsenceForDoctor($doctorId);
        }

        WP_CLI::success(sprintf(
            '¡Sembrado completado! Se han insertado %d jornadas semanales y %d bloqueos de ausencia.',
            $insertedSchedules,
            $insertedAbsences
        ));
    }

    private function seedWeeklyScheduleForDoctor(int $doctorId, array $shifts, array $durations): int
    {
        global $wpdb;

        $workdays = (array) $this->faker->randomElements([1, 2, 3, 4, 5], $this->faker->numberBetween(3, 5));
        $count = 0;

        for ($day = 1; $day <= 7; $day++) {
            update_field("works_{$day}", 0, $doctorId);
        }

        foreach ($workdays as $day) {
            $chosenShift = $this->faker->randomElement($shifts);
            $chosenDuration = $this->faker->randomElement($durations);

            $wpdb->insert($this->tableWeekly, [
                'doctor_id' => $doctorId,
                'day_of_week' => $day,
                'start_time' => $chosenShift['start'],
                'end_time' => $chosenShift['end'],
                'slot_duration' => $chosenDuration,
            ]);

            update_field("works_{$day}", 1, $doctorId);
            update_field("start_{$day}", $chosenShift['start'], $doctorId);
            update_field("end_{$day}", $chosenShift['end'], $doctorId);
            update_field("duration_{$day}", $chosenDuration, $doctorId);

            $count++;
        }

        return $count;
    }

    private function seedAbsenceForDoctor(int $doctorId): int
    {
        global $wpdb;

        if (!$this->faker->boolean(30)) {
            return 0;
        }

        $startDate = $this->faker->dateTimeBetween('now', '+3 months');
        $endDate = clone $startDate;
        $endDate->modify('+' . $this->faker->numberBetween(2, 10) . ' days');

        $reasons = ['Vacaciones de verano', 'Congreso Médico', 'Asuntos propios', 'Formación Externa'];

        $wpdb->insert($this->tableAbsences, [
            'doctor_id' => $doctorId,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'reason' => $this->faker->randomElement($reasons),
        ]);

        return 1;
    }
}