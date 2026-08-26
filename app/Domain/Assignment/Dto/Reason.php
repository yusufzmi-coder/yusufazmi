<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Dto;

use App\Domain\Assignment\Enums\ReasonTone;

/**
 * A reason is structured data, never a pre-baked sentence. The Malay wording is
 * applied at render time, so fixing a translation never requires re-running a solve.
 */
final readonly class Reason
{
    /** @param array<string, scalar> $params */
    public function __construct(
        public string $ruleKey,
        public string $code,
        public array $params = [],
        public ReasonTone $tone = ReasonTone::Info,
        public float $weightedDelta = 0.0,
    ) {}

    public function translationKey(): string
    {
        return "assignment.reasons.{$this->ruleKey}.{$this->code}";
    }

    public function withDelta(float $delta): self
    {
        return new self($this->ruleKey, $this->code, $this->params, $this->tone, $delta);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'rule' => $this->ruleKey,
            'code' => $this->code,
            'params' => $this->params,
            'tone' => $this->tone->value,
            'delta' => round($this->weightedDelta, 3),
        ];
    }
}
