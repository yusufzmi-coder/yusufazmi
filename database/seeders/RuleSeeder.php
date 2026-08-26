<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Assignment\Rules\FirmTeacherForBehaviourRule;
use App\Domain\Assignment\Rules\GenderBalanceRule;
use App\Domain\Assignment\Rules\MaxClassSizeRule;
use App\Domain\Assignment\Rules\SiblingPolicyRule;
use App\Domain\Assignment\RuleTypeRegistry;
use App\Models\Rule;
use Illuminate\Database\Seeder;

class RuleSeeder extends Seeder
{
    public function run(): void
    {
        $registry = app(RuleTypeRegistry::class);

        // The four rules shown in the Peraturan panel, in display order.
        $rules = [
            [SiblingPolicyRule::key(), 'Elak Kelas Adik Beradik', 80, null],
            [FirmTeacherForBehaviourRule::key(), 'Pelajar Bermasalah', 90, null],
            [GenderBalanceRule::key(), 'Seimbangkan Jantina', 40, null],
            [MaxClassSizeRule::key(), 'Had Maksimum Kelas', 100, ['max_students' => 30]],
        ];

        foreach ($rules as $order => [$key, $name, $weight, $config]) {
            $type = $registry->get($key);

            Rule::updateOrCreate(
                ['rule_type' => $key, 'session_id' => null],
                [
                    'name' => $name,
                    // Written from the registry, never trusted from input.
                    'kind' => $type->kind(),
                    'is_active' => true,
                    'weight' => $weight,
                    'config' => $config ?? $type->defaultConfig(),
                    'applies_to' => [],
                    'sort_order' => $order,
                ],
            );
        }
    }
}
