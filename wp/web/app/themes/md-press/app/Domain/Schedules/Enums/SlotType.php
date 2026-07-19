<?php

declare(strict_types=1);

namespace App\Domain\Schedules\Enums;

enum SlotType: string
{
    case PRESENCIAL = 'presencial';
    case TELEMEDICINA = 'telemedicina';
}