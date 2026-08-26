<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Assignment\Enums\RunStatus;
use App\Domain\Assignment\Enums\SolveMode;
use App\Models\AssignmentRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AssignmentRun> */
class AssignmentRunFactory extends Factory
{
    public function definition(): array
    {
        return [
            'mode' => SolveMode::FillOnly,
            'status' => RunStatus::Draft,
            'input_hash' => str_repeat('0', 64),
            'rules_snapshot' => [],
            'stats' => [],
            'seed' => 0,
        ];
    }
}
