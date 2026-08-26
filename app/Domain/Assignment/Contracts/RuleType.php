<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Contracts;

use App\Domain\Assignment\Dto\RuleConfig;
use App\Domain\Assignment\Enums\RuleKind;

interface RuleType
{
    /** Stable identifier stored in rules.rule_type. Never rename it. */
    public static function key(): string;

    /** Default Malay label used when a rule row is created. */
    public function label(): string;

    public function kind(): RuleKind;

    /** True when only one active instance may exist per session. */
    public function isSingleton(): bool;

    /**
     * Whether the admin may create/toggle this rule. Physical facts (year match,
     * room capacity) are always enforced and never appear in the Peraturan panel.
     */
    public function isConfigurable(): bool;

    /**
     * Laravel validation rules for the jsonb `config`, keyed relative to config.*
     *
     * @return array<string, mixed>
     */
    public function configRules(): array;

    /** @return array<string, mixed> */
    public function defaultConfig(): array;

    /** One-line Malay description rendered in the Peraturan panel. */
    public function describe(RuleConfig $config): string;
}
