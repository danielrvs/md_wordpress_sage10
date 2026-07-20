<?php

declare(strict_types=1);

namespace App\Domain\Appointments\Services;

use App\Domain\Appointments\Contracts\AppointmentRepositoryInterface;
use App\Domain\Appointments\DTOs\AppointmentDTO;

class GetDoctorAppointmentsService
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepository
    ) {
    }

    /**
     * @return AppointmentDTO[]
     */
    public function execute(int $doctorId, ?string $date = null, string $order = 'ASC'): array
    {
        $models = $this->appointmentRepository->getAppointmentsByDoctor($doctorId, $date, $order);

        return array_map(
            fn(object $model) => AppointmentDTO::fromEloquent($model),
            $models
        );
    }
}