<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Day;
use App\Models\ClassMeeting;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ClassMeeting> */
class ClassMeetingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'day' => fake()->randomElement(Day::cases()),
        ];
    }
}
