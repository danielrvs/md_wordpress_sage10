<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Services;

use App\Domain\Schedules\Contracts\ScheduleRepositoryInterface;
use App\Infrastructure\Cache\VersionedCache;

final class DeleteAbsenceService
{
    public function __construct(
        private readonly ScheduleRepositoryInterface $scheduleRepository
    ) {
    }

    public function execute(int $absenceId): void
    {
        $absence = $this->scheduleRepository->getAbsenceById($absenceId);

        if ($absence) {
            $this->scheduleRepository->deleteAbsence($absenceId);
            VersionedCache::invalidate('doctor_schedules', (string) $absence['doctor_id']);
        }
    }
}