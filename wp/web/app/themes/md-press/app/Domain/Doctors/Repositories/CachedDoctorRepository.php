<?php

declare(strict_types=1);

namespace App\Domain\Doctors\Repositories;

use App\Domain\Doctors\DTOs\DoctorDTO;
use Illuminate\Support\Facades\Cache;

class CachedDoctorRepository implements DoctorRepositoryInterface
{
    private const TTL = 3600;

    public function __construct(
        protected DoctorRepositoryInterface $next
    ) {
    }

    public function all(int $page, int $perPage = 10): array
    {
        return $this->search([], $page, $perPage);
    }

    public function findById(int $id): ?DoctorDTO
    {
        $key = "doctors:id:{$id}";

        $data = Cache::remember($key, self::TTL, function () use ($id) {
            $dto = $this->next->findById($id);
            return $dto ? $dto->toArray() : null;
        });

        return $data ? DoctorDTO::fromArray($data) : null;
    }

    public function search(array $filters, int $page, int $perPage = 10): array
    {
        $filtersHash = md5(serialize($filters));
        $key = "doctors:search:{$filtersHash}:p_{$page}:per_{$perPage}";

        // Almacenamos una colección de arrays
        $cachedArray = Cache::remember($key, self::TTL, function () use ($filters, $page, $perPage) {
            $doctors = $this->next->search($filters, $page, $perPage);
            return array_map(fn(DoctorDTO $doctor) => $doctor->toArray(), $doctors);
        });

        // Rehidratamos los DTOs para el resto de la aplicación
        return array_map(fn(array $data) => DoctorDTO::fromArray($data), $cachedArray);
    }

    public function count(array $filters): int
    {
        $filtersHash = md5(serialize($filters));
        $key = "doctors:count:{$filtersHash}";

        // Los enteros se guardan de forma nativa sin problemas
        return (int) Cache::remember($key, self::TTL, function () use ($filters) {
            return $this->next->count($filters);
        });
    }
}