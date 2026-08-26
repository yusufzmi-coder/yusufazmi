<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Guardian;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Guardian> */
class GuardianFactory extends Factory
{
    public function definition(): array
    {
        return [
            'family_id' => null,
            'name' => fake()->name(),
            'phone' => '+601'.fake()->numerify('#########'),
            'email' => fake()->safeEmail(),
            'occupation' => fake()->jobTitle(),
        ];
    }
}
