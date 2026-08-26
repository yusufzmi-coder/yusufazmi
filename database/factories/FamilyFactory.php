<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SiblingPolicy;
use App\Models\Family;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Family> */
class FamilyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Keluarga '.fake()->lastName(),
            'sibling_policy' => SiblingPolicy::Inherit,
            'address' => fake()->address(),
        ];
    }

    public function wantsTogether(): static
    {
        return $this->state(fn (): array => ['sibling_policy' => SiblingPolicy::Together]);
    }

    public function wantsApart(): static
    {
        return $this->state(fn (): array => ['sibling_policy' => SiblingPolicy::Apart]);
    }
}
