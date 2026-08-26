<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Teacher> */
class TeacherFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Cikgu '.fake()->firstName(),
            'phone' => '+601'.fake()->numerify('#########'),
            'email' => fake()->safeEmail(),
            'firmness' => fake()->numberBetween(2, 5),
            'max_classes' => 6,
            'is_active' => true,
        ];
    }

    public function firmness(int $level): static
    {
        return $this->state(fn (): array => ['firmness' => $level]);
    }
}
