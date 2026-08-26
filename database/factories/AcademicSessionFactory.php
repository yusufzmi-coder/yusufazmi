<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AcademicSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AcademicSession> */
class AcademicSessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => (string) now()->year,
            'starts_on' => now()->startOfYear(),
            'ends_on' => now()->endOfYear(),
            'is_active' => false,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => ['is_active' => true]);
    }
}
