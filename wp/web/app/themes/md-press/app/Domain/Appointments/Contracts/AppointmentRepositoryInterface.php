<?php

declare(strict_types=1);

namespace App\Domain\Appointments\Contracts;

interface AppointmentRepositoryInterface
{
    /**
     * Devuelve las horas de inicio ocupadas para un doctor en una fecha concreta.
     * @return string[] Ej: ['08:00', '08:30', '10:00']
     */
    public function getBookedStartTimes(int $doctorId, string $date): array;

    /**
     * Persiste una nueva cita en la base de datos.
     */
    public function create(array $data): int;
}