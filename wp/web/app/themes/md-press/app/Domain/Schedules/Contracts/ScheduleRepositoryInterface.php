<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Contracts;

interface ScheduleRepositoryInterface
{
    public function getWeeklyRules(int $doctorId, int $dayOfWeek): array;
    public function hasAbsence(int $doctorId, string $date): bool;
    public function syncWeeklyRules(int $doctorId, array $rules): void;
}