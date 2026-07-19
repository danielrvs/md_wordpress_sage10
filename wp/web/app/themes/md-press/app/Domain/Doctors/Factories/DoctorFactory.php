<?php

declare(strict_types=1);

namespace App\Domain\Doctors\Factories;

use App\Domain\Doctors\Enums\Specialty;
use Faker\Factory as Faker;

class DoctorFactory
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

    public function definition(): array
    {
        $cases = array_filter(
            Specialty::cases(),
            fn(Specialty $s) => $s !== Specialty::UNKNOWN
        );

        $specialtyValues = array_map(fn(Specialty $s) => $s->value, $cases);
        $availabilities = ['Inmediata', 'Esta semana', 'Bajo consulta'];

        $gender = $this->faker->randomElement(['male', 'female']);
        $firstName = $this->faker->firstName($gender);
        $lastName = $this->faker->lastName() . ' ' . $this->faker->lastName();
        $prefix = $gender === 'male' ? 'Dr. ' : 'Dra. ';

        return [
            'name' => $prefix . $firstName . ' ' . $lastName,
            'bio' => $this->faker->paragraph(3),
            'specialty' => $this->faker->randomElements($specialtyValues, $this->faker->numberBetween(1, 2)),
            'location' => $this->faker->city() . ', Consultorio ' . $this->faker->numberBetween(100, 500),
            'availability' => $this->faker->randomElement($availabilities),
            'rating' => $this->faker->randomFloat(1, 2.5, 5.0),
        ];
    }

    public function count(int $amount): array
    {
        $records = [];
        for ($i = 0; $i < $amount; $i++) {
            $records[] = $this->definition();
        }
        return $records;
    }
}