<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BehaviourNote;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BehaviourNote> */
class BehaviourNoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'level_at_time' => 2,
            'body' => fake()->sentence(),
            'occurred_on' => now()->subDays(fake()->numberBetween(0, 60)),
        ];
    }
}
