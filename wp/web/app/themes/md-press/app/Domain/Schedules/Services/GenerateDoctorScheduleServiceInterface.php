<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Contracts;

use App\Domain\Schedules\DTOs\ScheduleDTO;

interface GenerateDoctorScheduleServiceInterface
{
    public function execute(int $doctorId, string $date): ScheduleDTO;
}