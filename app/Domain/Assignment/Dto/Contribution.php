<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Dto;

final readonly class Contribution
{
    /** @param list<Reason> $reasons */
    public function __construct(
        public float $score,          // normalised to [-1.0, 1.0]
        public array $reasons = [],
    ) {}

    public static function neutral(): self
    {
        return new self(0.0);
    }
}
