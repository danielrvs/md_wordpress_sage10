<?php

declare(strict_types=1);

namespace App\Domain\Doctors\Services;

use App\Domain\Doctors\Repositories\DoctorRepositoryInterface;

class IndexDoctorService
{
    public function __construct(
        protected DoctorRepositoryInterface $repository
    ) {
    }

    public function execute(array $params): array
    {
        $doctors = $this->repository->search($params['filters'], $params['page'], $params['per_page']);
        $count = $this->repository->count($params['filters']);
        $totalPages = (int) ceil($count / $params['per_page']);

        return [
            'doctors' => $doctors,
            'count' => $count,
            'total_pages' => $totalPages,
            'current_page' => $params['page'],
            'per_page' => $params['per_page'],
        ];
    }
}