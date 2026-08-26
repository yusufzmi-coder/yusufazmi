<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SchoolClass> */
class SchoolClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'session_id' => AcademicSession::factory(),
            'year_level' => fake()->numberBetween(1, 6),
            'stream' => fake()->randomElement(['ALPHA', 'BETA', 'GAMMA']),
            'teacher_id' => Teacher::factory(),
            'capacity_override' => 30,
            'is_active' => true,
        ];
    }
}
