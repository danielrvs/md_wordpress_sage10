<?php

declare(strict_types=1);

namespace App\Domain\Doctors\Enums;

enum Specialty: string
{
    case CARDIOLOGY = 'Cardiología';
    case PEDIATRICS = 'Pediatría';
    case DERMATOLOGY = 'Dermatología';
    case GYN_OB = 'Ginecología';
    case TRAUMATOLOGY = 'Traumatología';
    case NEUROLOGY = 'Neurología';
    case OPHTHALMOLOGY = 'Oftalmología';
    case UNKNOWN = 'General';
}