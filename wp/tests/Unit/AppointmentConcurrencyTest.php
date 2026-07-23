<?php

declare(strict_types=1);

use App\Domain\Appointments\Contracts\AppointmentRepositoryInterface;
use App\Domain\Appointments\DTOs\CreateAppointmentDTO;
use App\Domain\Appointments\Services\CreateAppointmentService;
use App\Domain\Schedules\Contracts\ScheduleRepositoryInterface;
use App\Infrastructure\Cache\AtomicLock;

test('prevents race conditions when a slot lock is already held in Redis', function () {
    $doctorId = 9999;
    $date = date('Y-m-d', strtotime('+2 days'));
    $time = '11:00';

    $appointmentRepo = new class implements AppointmentRepositoryInterface {
        public function getBookedStartTimes(int $doctorId, string $date): array { return []; }
        public function create(array $data): int { return 1; }
        public function getAppointmentsByDoctor(int $doctorId, ?string $date = null, string $order = 'ASC'): array { return []; }
        public function getAppointmentsByPatient(int $patientId): array { return []; }
        public function cancelAppointment(int $appointmentId, int $patientId): bool { return true; }
    };

    $scheduleRepo = new class implements ScheduleRepositoryInterface {
        public function getWeeklyRules(int $doctorId, int $dayOfWeek): array { return []; }
        public function hasAbsence(int $doctorId, string $date): bool { return false; }
        public function syncWeeklyRules(int $doctorId, array $rules): void {}
        public function getAbsences(int $doctorId): array { return []; }
        public function getAbsenceById(int $absenceId): ?array { return null; }
        public function addAbsence(int $doctorId, string $startDate, string $endDate, ?string $reason): int { return 1; }
        public function deleteAbsence(int $absenceId): bool { return true; }
    };

    // Simular que otra petición en paralelo ya adquirió el bloqueo atómico en Redis
    $lock = new AtomicLock(
        context: 'appointment_slot',
        resource: sprintf('%d_%s_%s', $doctorId, $date, $time),
        ttl: 10,
    );
    $lock->acquire();

    try {
        $service = new CreateAppointmentService($appointmentRepo, $scheduleRepo);

        $dto = new CreateAppointmentDTO(
            doctorId: $doctorId,
            patientId: 1,
            clinicId: 1,
            appointmentDate: $date,
            startTime: $time,
            notes: 'Test concurrencia'
        );

        // Intentar crear la cita mientras el candado atómico en Redis está activo debe fallar inmediatamente
        expect(fn () => $service->execute($dto))
            ->toThrow(DomainException::class, 'El tramo horario seleccionado está siendo procesado por otra reserva');
    } finally {
        $lock->release();
    }
});
