<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Services;

use App\Domain\Schedules\Contracts\ScheduleRepositoryInterface;

class GetAbsencesByDoctorIdService
{
    public function __construct(
        private readonly ScheduleRepositoryInterface $scheduleRepository
    ) {
    }

    public function execute(int $doctorId): array
    {
        return $this->scheduleRepository->getAbsences($doctorId);
    }
}