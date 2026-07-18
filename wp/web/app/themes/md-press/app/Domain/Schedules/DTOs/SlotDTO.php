<?php

declare(strict_types=1);

namespace App\Domain\Schedules\DTOs;

class SlotDTO
{
    public function __construct(
        public string $time,
        public bool $isAvailable,
        public string $type // 'presencial' o 'telemedicina'
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            time: $data['time'],
            isAvailable: (bool) $data['is_available'],
            type: $data['type'] ?? 'presencial'
        );
    }

    public function toArray(): array
    {
        return [
            'time' => $this->time,
            'isAvailable' => $this->isAvailable,
            'type' => $this->type,
        ];
    }
}