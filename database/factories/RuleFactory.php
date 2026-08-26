<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Assignment\Enums\RuleKind;
use App\Domain\Assignment\Rules\MaxClassSizeRule;
use App\Models\Rule;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Rule> */
class RuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'session_id' => null,
            'rule_type' => MaxClassSizeRule::key(),
            'name' => 'Had Maksimum Kelas',
            'kind' => RuleKind::Hard,
            'is_active' => true,
            'weight' => 100,
            'config' => ['max_students' => 30],
            'applies_to' => [],
            'sort_order' => 0,
        ];
    }
}
