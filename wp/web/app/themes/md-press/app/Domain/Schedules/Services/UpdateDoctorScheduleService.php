<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Services;

use App\Domain\Schedules\Contracts\ScheduleRepositoryInterface;
use App\Infrastructure\Cache\VersionedCache;
final class UpdateDoctorScheduleService
{
    public function __construct(
        private readonly ScheduleRepositoryInterface $scheduleRepository
    ) {
    }

    public function execute(int $doctorId, array $rules): void
    {
        $this->scheduleRepository->syncWeeklyRules($doctorId, $rules);
        VersionedCache::invalidate("doctor_schedules", (string) $doctorId);
    }
}