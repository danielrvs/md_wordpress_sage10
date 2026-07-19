<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Services;

use App\Domain\Schedules\Contracts\GenerateDoctorScheduleServiceInterface;
use App\Domain\Schedules\DTOs\ScheduleDTO;
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


        $cacheKey = sprintf('doctor_schedules:id_%d:date_%s', $doctorId, $date);


        $cachedData = Cache::get($cacheKey);


        if (is_array($cachedData)) {
            return ScheduleDTO::fromArray($cachedData);
        }

        $schedule = $this->innerService->execute($doctorId, $date);

        Cache::put($cacheKey, $schedule->toArray(), self::TTL);

        return $schedule;
    }
}