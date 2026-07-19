<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;
use App\Domain\Doctors\Repositories\DoctorRepositoryInterface;

class SingleDoctor extends Composer
{
    protected static $views = [
        'single-doctor',
    ];

    public function __construct(
        protected DoctorRepositoryInterface $repository
    ) {
    }

    /**
     * Datos que se pasan a la vista Blade
     */
    public function with(): array
    {
        $doctorId = get_the_ID();

        // Aquí sí forzamos el paso por nuestro decorador de Redis y DTO
        $doctorDto = $this->repository->findById($doctorId);

        return [
            'doctor' => $doctorDto ? $doctorDto->toArray() : null,
        ];
    }
}