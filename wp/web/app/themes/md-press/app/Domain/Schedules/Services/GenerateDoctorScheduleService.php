<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Services;

use App\Domain\Schedules\Contracts\GenerateDoctorScheduleServiceInterface;
use App\Domain\Schedules\Contracts\ScheduleRepositoryInterface;
use App\Domain\Schedules\DTOs\ScheduleDTO;
use App\Domain\Schedules\DTOs\SlotDTO;
use App\Domain\Schedules\Enums\SlotType;
use DateTime;

class GenerateDoctorScheduleService implements GenerateDoctorScheduleServiceInterface
{
    private ScheduleRepositoryInterface $repository;

    public function __construct(ScheduleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $doctorId, string $date): ScheduleDTO
    {
        if ($this->repository->hasAbsence($doctorId, $date)) {
            return new ScheduleDTO($doctorId, $date, false, []);
        }
        $timestamp = strtotime($date);
        $dayOfWeek = (int) date('N', $timestamp); // 1 (Lunes) a 7 (Domingo)

        $rules = $this->repository->getWeeklyRules($doctorId, $dayOfWeek);
        if (empty($rules)) {
            return new ScheduleDTO($doctorId, $date, false, []);
        }

        $slots = [];
        foreach ($rules as $rule) {
            $slots = array_merge($slots, $this->generateSlotsFromRule($date, $rule));
        }

        //TODO: VALIDACIÓN DE CITAS

        return new ScheduleDTO($doctorId, $date, true, $slots);
    }

    private function generateSlotsFromRule(string $date, array $rule): array
    {
        $slots = [];
        $current = new DateTime($date . ' ' . $rule['start_time']);
        $end = new DateTime($date . ' ' . $rule['end_time']);
        $duration = (int) $rule['slot_duration'];

        while ($current < $end) {
            $startTimeStr = $current->format('H:i');

            $current->modify("+{$duration} minutes");

            // Fin de la jornada
            if ($current > $end) {
                break;
            }

            $slots[] = new SlotDTO(
                startTime: $startTimeStr,
                endTime: $current->format('H:i'),
                isAvailable: true,
                type: SlotType::PRESENCIAL
            );
        }

        return $slots;
    }
}