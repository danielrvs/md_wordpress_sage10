<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Services;

use App\Domain\Schedules\Contracts\ScheduleRepositoryInterface;
use App\Infrastructure\Cache\VersionedCache;

final class AddAbsenceService
{
    public function __construct(
        private readonly ScheduleRepositoryInterface $scheduleRepository
    ) {
    }

    public function execute(int $doctorId, string $startDate, string $endDate, ?string $reason): void
    {
        if (strtotime($startDate) > strtotime($endDate)) {
            throw new \InvalidArgumentException('La fecha de inicio no puede ser posterior a la de fin.');
        }

        $this->scheduleRepository->addAbsence($doctorId, $startDate, $endDate, $reason);

        VersionedCache::invalidate('doctor_schedules', (string) $doctorId);
    }
}