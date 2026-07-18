<?php

declare(strict_types=1);

namespace App\Domain\Doctors\Repositories;

use App\Domain\Doctors\DTOs\DoctorDTO;

interface DoctorRepositoryInterface
{
    public function all(int $page, int $perPage = 10): array;
    public function findById(int $id): ?DoctorDTO;
    public function search(array $filters, int $page, int $perPage = 10): array;
    public function count(array $filters): int;
}