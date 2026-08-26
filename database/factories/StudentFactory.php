<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BehaviourLevel;
use App\Enums\Gender;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Student> */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        $gender = fake()->randomElement([Gender::Lelaki, Gender::Perempuan]);

        return [
            'family_id' => null,
            'student_code' => 'P-'.fake()->unique()->numerify('#####'),
            'name' => $gender === Gender::Lelaki ? fake()->firstNameMale() : fake()->firstNameFemale(),
            'gender' => $gender,
            'date_of_birth' => fake()->dateTimeBetween('-13 years', '-7 years'),
            'year_level' => fake()->numberBetween(1, 6),
            'behaviour_level' => BehaviourLevel::Normal,
            'is_active' => true,
            'enrolled_on' => now()->subMonths(fake()->numberBetween(0, 24)),
        ];
    }

    public function yearLevel(int $year): static
    {
        return $this->state(fn (): array => ['year_level' => $year]);
    }

    public function behaviour(BehaviourLevel $level): static
    {
        return $this->state(fn (): array => ['behaviour_level' => $level]);
    }
}
