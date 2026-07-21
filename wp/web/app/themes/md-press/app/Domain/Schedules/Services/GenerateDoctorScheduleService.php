<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Services;

use App\Domain\Appointments\Contracts\AppointmentRepositoryInterface;
use App\Domain\Schedules\Contracts\GenerateDoctorScheduleServiceInterface;
use App\Domain\Schedules\Contracts\ScheduleRepositoryInterface;
use App\Domain\Schedules\DTOs\ScheduleDTO;
use App\Domain\Schedules\DTOs\SlotDTO;
use App\Domain\Schedules\Enums\SlotType;
use DateTime;

class GenerateDoctorScheduleService implements GenerateDoctorScheduleServiceInterface
{
    public function __construct(
        private readonly ScheduleRepositoryInterface $scheduleRepository,
        private readonly AppointmentRepositoryInterface $appointmentRepository
    ) {
    }

    public function execute(int $doctorId, string $date): ScheduleDTO
    {
        $today = (new DateTime('now', wp_timezone()))->format('Y-m-d');
        if ($date < $today) {
            return ScheduleDTO::unavailable($doctorId, $date);
        }

        if ($this->isDoctorAbsent($doctorId, $date)) {
            return ScheduleDTO::unavailable($doctorId, $date);
        }

        $rules = $this->getWorkdayRules($doctorId, $date);
        if (empty($rules)) {
            return ScheduleDTO::unavailable($doctorId, $date);
        }

        $slots = $this->buildSlotsFromRules($date, $rules);
        $slots = $this->applyBookingAvailability($doctorId, $date, $slots);
        $slots = array_values(array_filter($slots, fn (SlotDTO $slot) => $slot->isAvailable));
        $slots = $this->filterPastAndUpcomingSlots($date, $slots);

        return new ScheduleDTO($doctorId, $date, true, $slots);
    }

    private function filterPastAndUpcomingSlots(string $date, array $slots): array
    {
        $timezone = wp_timezone();
        $cutoff = new DateTime('now', $timezone);
        $cutoff->modify('+30 minutes');

        $filtered = array_filter($slots, function (SlotDTO $slot) use ($date, $timezone, $cutoff) {
            $slotDateTime = new DateTime($date . ' ' . $slot->startTime, $timezone);
            return $slotDateTime >= $cutoff;
        });

        return array_values($filtered);
    }

    private function isDoctorAbsent(int $doctorId, string $date): bool
    {
        return $this->scheduleRepository->hasAbsence($doctorId, $date);
    }

    private function getWorkdayRules(int $doctorId, string $date): array
    {
        $dayOfWeek = (int) date('N', strtotime($date)); // 1 (Lunes) a 7 (Domingo)

        return $this->scheduleRepository->getWeeklyRules($doctorId, $dayOfWeek);
    }


    private function buildSlotsFromRules(string $date, array $rules): array
    {
        $slots = [];
        foreach ($rules as $rule) {
            $slots = array_merge($slots, $this->generateSlotsFromRule($date, $rule));
        }

        return $slots;
    }

    private function applyBookingAvailability(int $doctorId, string $date, array $slots): array
    {
        $bookedTimes = $this->appointmentRepository->getBookedStartTimes($doctorId, $date);

        if (empty($bookedTimes)) {
            return $slots;
        }

        foreach ($slots as $slot) {
            if (in_array($slot->startTime, $bookedTimes, true)) {
                $slot->isAvailable = false;
            }
        }

        return $slots;
    }

    /** @return SlotDTO[] */
    private function generateSlotsFromRule(string $date, array $rule): array
    {
        $slots = [];
        $current = new DateTime($date . ' ' . $rule['start_time']);
        $end = new DateTime($date . ' ' . $rule['end_time']);
        $duration = (int) $rule['slot_duration'];

        while ($current < $end) {
            $startTime = $current->format('H:i');
            $current->modify("+{$duration} minutes");

            if ($current > $end) {
                break;
            }

            $slots[] = new SlotDTO(
                startTime: $startTime,
                endTime: $current->format('H:i'),
                isAvailable: true,
                type: SlotType::PRESENCIAL
            );
        }

        return $slots;
    }
}