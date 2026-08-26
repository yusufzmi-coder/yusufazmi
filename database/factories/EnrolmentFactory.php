<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EnrolmentSource;
use App\Enums\EnrolmentStatus;
use App\Models\Enrolment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Enrolment> */
class EnrolmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => EnrolmentStatus::Active,
            'is_pinned' => false,
            'source' => EnrolmentSource::Manual,
            'joined_at' => now(),
            'left_at' => null,
        ];
    }
}
