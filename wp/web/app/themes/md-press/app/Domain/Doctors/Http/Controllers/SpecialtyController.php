<?php

namespace App\Domain\Doctors\Http\Controllers;

use App\Domain\Doctors\Enums\Specialty;

class SpecialtyController
{
    public function __construct(
    ) {
    }

    public function index(): \WP_REST_Response
    {
        $specialties = array_reduce(Specialty::cases(), function ($carry, Specialty $case) {
            $carry[$case->value] = $case->value;
            return $carry;
        }, []);

        return new \WP_REST_Response([
            'specialties' => $specialties,
        ], 200);
    }
}