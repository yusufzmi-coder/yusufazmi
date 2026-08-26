<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TimeSlot> */
class TimeSlotFactory extends Factory
{
    public function definition(): array
    {
        $hour = fake()->unique()->numberBetween(8, 16);

        return [
            'label' => sprintf('%d:00-%d:00', $hour, $hour + 1),
            'starts_at' => sprintf('%02d:00:00', $hour),
            'ends_at' => sprintf('%02d:00:00', $hour + 1),
            'is_break' => false,
            'sort_order' => $hour,
        ];
    }
}
