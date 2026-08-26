<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Dto;

use App\Domain\Assignment\Enums\RuleKind;

/** A typed reader over the jsonb blob, so rule code never touches raw arrays. */
final readonly class RuleConfig
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $appliesTo
     */
    public function __construct(
        public int $ruleId,
        public string $ruleType,
        public RuleKind $kind,
        public int $weight,          // 0..100, ignored for hard rules
        public array $data = [],
        public array $appliesTo = [],
    ) {}

    public function int(string $key, int $default = 0): int
    {
        return isset($this->data[$key]) ? (int) $this->data[$key] : $default;
    }

    public function bool(string $key, bool $default = false): bool
    {
        return isset($this->data[$key]) ? (bool) $this->data[$key] : $default;
    }

    public function string(string $key, string $default = ''): string
    {
        return isset($this->data[$key]) && is_scalar($this->data[$key])
            ? (string) $this->data[$key]
            : $default;
    }

    public function appliesToStudent(StudentSnapshot $student): bool
    {
        $years = $this->appliesTo['year_levels'] ?? null;

        return ! is_array($years) || $years === []
            || in_array($student->yearLevel, array_map(intval(...), $years), strict: true);
    }
}
