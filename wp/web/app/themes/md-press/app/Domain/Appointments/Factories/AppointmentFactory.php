<?php

declare(strict_types=1);

namespace App\Domain\Appointments\Factories;

use Faker\Factory as Faker;

class AppointmentFactory
{
    protected \Faker\Generator $faker;

    public function __construct()
    {
        $this->faker = Faker::create('es_ES');
    }

    public static function new(): self
    {
        return new self();
    }

    public function definition(array $overrides = []): array
    {
        $statusOptions = ['pending', 'confirmed', 'completed', 'cancelled'];
        
        $date = $this->faker->dateTimeBetween('now', '+3 months');
        $minutes = $this->faker->randomElement([0, 15, 30, 45]);
        $date->setTime($this->faker->numberBetween(8, 19), $minutes, 0);

        return array_merge([
            'doctor_id' => $this->faker->numberBetween(1, 100),
            'patient_id' => $this->faker->numberBetween(1, 500),
            'clinic_id' => $this->faker->numberBetween(1, 5),
            'appointment_date' => $date->format('Y-m-d H:i:s'),
            'status' => $this->faker->randomElement($statusOptions),
            'notes' => $this->faker->boolean(70) ? $this->faker->sentence(6) : null,
        ], $overrides);
    }

    public function count(int $amount, array $overrides = []): array
    {
        $records = [];
        for ($i = 0; $i < $amount; $i++) {
            $records[] = $this->definition($overrides);
        }
        return $records;
    }
}
