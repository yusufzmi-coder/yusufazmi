<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Assignment\Enums\ItemAction;
use App\Models\AssignmentRunItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AssignmentRunItem> */
class AssignmentRunItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'action' => ItemAction::Place,
            'reasons' => [],
            'violations' => [],
            'rank' => 0,
            'created_at' => now(),
        ];
    }
}
