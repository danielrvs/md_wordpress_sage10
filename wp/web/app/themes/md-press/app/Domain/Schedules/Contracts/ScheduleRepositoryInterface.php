<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Contracts;

interface ScheduleRepositoryInterface
{
    public function getWeeklyRules(int $doctorId, int $dayOfWeek): array;
    public function hasAbsence(int $doctorId, string $date): bool;
    public function syncWeeklyRules(int $doctorId, array $rules): void;
    public function getAbsences(int $doctorId): array;
    public function getAbsenceById(int $absenceId): ?array;
    public function addAbsence(int $doctorId, string $startDate, string $endDate, ?string $reason): int;
    public function deleteAbsence(int $absenceId): bool;
}