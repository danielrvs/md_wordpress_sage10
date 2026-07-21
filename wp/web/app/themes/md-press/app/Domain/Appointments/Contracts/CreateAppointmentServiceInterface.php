<?php

declare(strict_types=1);

namespace App\Domain\Appointments\Contracts;

use App\Domain\Appointments\DTOs\AppointmentDTO;
use App\Domain\Appointments\DTOs\CreateAppointmentDTO;

interface CreateAppointmentServiceInterface
{
    public function execute(CreateAppointmentDTO $dto): AppointmentDTO;
}
