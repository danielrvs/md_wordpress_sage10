<?php

declare(strict_types=1);

namespace App\Domain\Schedules\DTOs;

class ScheduleDTO
{
    /**
     * @param SlotDTO[] $slots
     */
    public function __construct(
        public int $doctorId,
        public string $date, // Formato Y-m-d
        public bool $isWorkday,
        public array $slots
    ) {}

    public static function fromArray(array $data): self
    {
        $slots = array_map(
            fn (array $slot) => SlotDTO::fromArray($slot),
            $data['slots'] ?? []
        );

        return new self(
            doctorId: (int) $data['doctor_id'],
            date: $data['date'],
            isWorkday: (bool) ($data['is_workday'] ?? true),
            slots: $slots
        );
    }

    public function toArray(): array
    {
        return [
            'doctorId'  => $this->doctorId,
            'date'      => $this->date,
            'isWorkday' => $this->isWorkday,
            'slots'     => array_map(fn (SlotDTO $slot) => $slot->toArray(), $this->slots),
        ];
    }
}