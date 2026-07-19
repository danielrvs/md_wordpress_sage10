<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Services;

use App\Domain\Schedules\Contracts\ScheduleRepositoryInterface;
use Illuminate\Support\Facades\Cache;

final class UpdateDoctorScheduleService
{
    public function __construct(
        private readonly ScheduleRepositoryInterface $scheduleRepository
    ) {
    }

    public function execute(int $doctorId, array $rules): void
    {
        $this->scheduleRepository->syncWeeklyRules($doctorId, $rules);
        Cache::tags(["doctor_schedules:{$doctorId}"])->flush();
    }
}