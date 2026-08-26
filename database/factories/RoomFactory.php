<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Room> */
class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Bilik '.fake()->unique()->numberBetween(1, 999),
            'capacity' => 30,
            'location' => 'Aras '.fake()->numberBetween(1, 3),
            'is_active' => true,
        ];
    }
}
