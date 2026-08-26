<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Assignment\Dto\RuleConfig;
use App\Domain\Assignment\RuleTypeRegistry;
use App\Models\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RuleController
{
    public function index(RuleTypeRegistry $registry): Response
    {
        return Inertia::render('rules/index', [
            'rules' => Rule::query()
                ->orderBy('sort_order')
                ->get()
                ->filter(fn (Rule $r): bool => $registry->has($r->rule_type))
                ->map(fn (Rule $r): array => [
                    'id' => $r->id,
                    'rule_type' => $r->rule_type,
                    'name' => $r->name,
                    'kind' => $r->kind->value,
                    'is_active' => $r->is_active,
                    'weight' => $r->weight,
                    'config' => $r->config ?? [],
                    'description' => $registry->get($r->rule_type)->describe($this->toConfig($r)),
                ])
                ->values()
                ->all(),

            // Physical constraints the admin cannot switch off. Shown for honesty, so
            // nobody wonders why capacity is still enforced with every rule disabled.
            'always_on' => collect($registry->alwaysOn())
                ->map(fn ($type, string $key): array => [
                    'key' => $key,
                    'name' => $type->label(),
                    'description' => $type->describe(new RuleConfig(0, $key, $type->kind(), 100)),
                ])
                ->values()
                ->all(),
        ]);
    }

    public function update(Request $request, Rule $rule, RuleTypeRegistry $registry): RedirectResponse
    {
        $type = $registry->get($rule->rule_type);

        $rules = [
            'is_active' => ['boolean'],
            'weight' => ['integer', 'between:0,100'],
            'name' => ['string', 'max:120'],
            'config' => ['array'],
        ];

        foreach ($type->configRules() as $key => $constraint) {
            $rules["config.{$key}"] = $constraint;
        }

        $validated = $request->validate($rules);

        // `kind` is never accepted from input; it comes from the registry.
        $rule->update($validated + ['kind' => $type->kind()]);

        return back()->with('success', "Peraturan \"{$rule->name}\" dikemas kini.");
    }

    private function toConfig(Rule $rule): RuleConfig
    {
        return new RuleConfig(
            $rule->id, $rule->rule_type, $rule->kind, $rule->weight,
            $rule->config ?? [], $rule->applies_to ?? [],
        );
    }
}
