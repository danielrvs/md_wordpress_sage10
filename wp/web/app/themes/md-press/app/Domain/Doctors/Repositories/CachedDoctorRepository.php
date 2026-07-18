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
        $key = "doctors:all:p_{$page}:per_{$perPage}";

        return Cache::remember($key, self::TTL, function () use ($page, $perPage) {
            return $this->next->all($page, $perPage);
        });
    }

    public function findById(int $id): ?DoctorDTO
    {
        $key = "doctors:id:{$id}";

        return Cache::remember($key, self::TTL, function () use ($id) {
            return $this->next->findById($id);
        });
    }

    public function search(array $filters, int $page, int $perPage = 10): array
    {
        $filtersHash = md5(serialize($filters));
        $key = "doctors:search:{$filtersHash}:p_{$page}:per_{$perPage}";

        return Cache::remember($key, self::TTL, function () use ($filters, $page, $perPage) {
            return $this->next->search($filters, $page, $perPage);
        });
    }

    public function count(array $filters): int
    {
        $filtersHash = md5(serialize($filters));
        $key = "doctors:count:{$filtersHash}";

        return Cache::remember($key, self::TTL, function () use ($filters) {
            return $this->next->count($filters);
        });
    }
}