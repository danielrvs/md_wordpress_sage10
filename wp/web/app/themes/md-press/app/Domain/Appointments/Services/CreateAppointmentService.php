<?php

declare(strict_types=1);

namespace App\Domain\Appointments\Services;

use App\Domain\Appointments\Contracts\AppointmentRepositoryInterface;
use App\Domain\Appointments\Contracts\CreateAppointmentServiceInterface;
use App\Domain\Appointments\DTOs\AppointmentDTO;
use App\Domain\Appointments\DTOs\CreateAppointmentDTO;
use App\Domain\Schedules\Contracts\ScheduleRepositoryInterface;
use App\Infrastructure\Cache\AtomicLock;
use App\Infrastructure\Cache\VersionedCache;
use DateTime;
use DomainException;
use InvalidArgumentException;

class CreateAppointmentService implements CreateAppointmentServiceInterface
{
    private const MIN_ADVANCE_MINUTES = 30;

    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepository,
        private readonly ScheduleRepositoryInterface $scheduleRepository
    ) {
    }

    public function execute(CreateAppointmentDTO $dto): AppointmentDTO
    {
        $formattedDate = $this->parseDate($dto->appointmentDate);
        $formattedTime = $this->parseTime($dto->startTime);

        $lock = new AtomicLock(
            context: 'appointment_slot',
            resource: sprintf('%d_%s_%s', $dto->doctorId, $formattedDate, $formattedTime),
        );

        return $lock->run(
            callback: fn () => $this->processBooking($dto, $formattedDate, $formattedTime),
            failMessage: 'El tramo horario seleccionado está siendo procesado por otra reserva. Por favor, reintenta en unos segundos.',
        );
    }

    private function processBooking(CreateAppointmentDTO $dto, string $date, string $time): AppointmentDTO
    {
        $this->assertDoctorExists($dto->doctorId);
        $this->assertNotInPastOrTooSoon($date, $time);
        $this->assertDoctorAvailable($dto->doctorId, $date);
        $this->assertSlotFree($dto->doctorId, $date, $time);

        $appointmentId = $this->appointmentRepository->create([
            'doctor_id'        => $dto->doctorId,
            'patient_id'       => $dto->patientId,
            'clinic_id'        => $dto->clinicId,
            'appointment_date' => $date,
            'start_time'       => $time,
            'status'           => $dto->status,
            'notes'            => $dto->notes,
        ]);

        VersionedCache::invalidate('doctor_schedules', (string) $dto->doctorId);

        return new AppointmentDTO(
            id: $appointmentId,
            doctorId: $dto->doctorId,
            patientId: $dto->patientId,
            clinicId: $dto->clinicId,
            dateTime: sprintf('%s %s:00', $date, $time),
            status: $dto->status,
            notes: $dto->notes
        );
    }



    private function assertDoctorExists(int $doctorId): void
    {
        if ($doctorId <= 0 || get_post_type($doctorId) !== 'doctor' || get_post_status($doctorId) !== 'publish') {
            throw new InvalidArgumentException('El médico especificado no existe o no está publicado.');
        }
    }

    private function assertNotInPastOrTooSoon(string $date, string $time): void
    {
        $tz  = wp_timezone();
        $now = new DateTime('now', $tz);
        $appointment = new DateTime("$date $time", $tz);

        if ($appointment <= $now) {
            throw new InvalidArgumentException('No es posible reservar citas en fechas u horas pasadas.');
        }

        $cutoff = (clone $now)->modify(sprintf('+%d minutes', self::MIN_ADVANCE_MINUTES));
        if ($appointment < $cutoff) {
            throw new InvalidArgumentException(
                sprintf('Las citas deben reservarse con al menos %d minutos de antelación.', self::MIN_ADVANCE_MINUTES)
            );
        }
    }

    private function assertDoctorAvailable(int $doctorId, string $date): void
    {
        if ($this->scheduleRepository->hasAbsence($doctorId, $date)) {
            throw new DomainException('El médico no se encuentra disponible en la fecha seleccionada por motivo de ausencia.');
        }
    }

    private function assertSlotFree(int $doctorId, string $date, string $time): void
    {
        $bookedTimes = $this->appointmentRepository->getBookedStartTimes($doctorId, $date);

        if (in_array($time, $bookedTimes, true)) {
            throw new DomainException('El tramo horario seleccionado ya se encuentra reservado.');
        }
    }


    private function parseDate(string $raw): string
    {
        $d = DateTime::createFromFormat('Y-m-d', $raw);

        if (!$d || $d->format('Y-m-d') !== $raw) {
            throw new InvalidArgumentException('El formato de la fecha debe ser YYYY-MM-DD.');
        }

        return $d->format('Y-m-d');
    }

    private function parseTime(string $raw): string
    {
        $t = DateTime::createFromFormat('H:i', $raw)
            ?: DateTime::createFromFormat('H:i:s', $raw);

        if (!$t) {
            throw new InvalidArgumentException('El formato de la hora debe ser HH:MM.');
        }

        return $t->format('H:i');
    }
}
