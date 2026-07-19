<?php

declare(strict_types=1);

namespace App\Domain\Schedules\DTOs;

use App\Domain\Schedules\Enums\SlotType;

class SlotDTO
{
    public function __construct(
        public string $startTime,
        public string $endTime,
        public bool $isAvailable,
        public SlotType $type
    ) {
    }

    public static function fromArray(array $data): self
    {
        $rawType = $data['type'] ?? 'presencial';
        $type = $rawType instanceof SlotType
            ? $rawType
            : (SlotType::tryFrom((string) $rawType) ?? SlotType::PRESENCIAL);

        return new self(
            startTime: (string) $data['start_time'],
            endTime: (string) $data['end_time'],
            isAvailable: (bool) $data['is_available'] ?? false,
            type: $type
        );
    }

    public function toArray(): array
    {
        return [
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'is_available' => $this->isAvailable,
            'type' => $this->type->value,
        ];
    }
}