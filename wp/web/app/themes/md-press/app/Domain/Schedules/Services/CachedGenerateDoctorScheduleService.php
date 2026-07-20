<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Services;

use App\Domain\Schedules\Contracts\GenerateDoctorScheduleServiceInterface;
use App\Domain\Schedules\DTOs\ScheduleDTO;
use App\Infrastructure\Cache\VersionedCache;
use Illuminate\Support\Facades\Cache;

class CachedGenerateDoctorScheduleService implements GenerateDoctorScheduleServiceInterface
{
    private GenerateDoctorScheduleServiceInterface $innerService;
    private const TTL = 3600; // 1 hora en segundos

    public function __construct(GenerateDoctorScheduleServiceInterface $innerService)
    {
        $this->innerService = $innerService;
    }

    public function execute(int $doctorId, string $date): ScheduleDTO
    {
        $cachedData = VersionedCache::get("doctor_schedules", (string) $doctorId, "date_{$date}");

        if (is_array($cachedData)) {
            return ScheduleDTO::fromArray($cachedData);
        }

        $schedule = $this->innerService->execute($doctorId, $date);

        VersionedCache::put("doctor_schedules", (string) $doctorId, "date_{$date}", $schedule->toArray(), self::TTL);

        return $schedule;
    }
}