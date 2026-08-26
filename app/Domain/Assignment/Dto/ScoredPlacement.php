<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Dto;

final readonly class ScoredPlacement
{
    /** @param list<Reason> $reasons */
    public function __construct(
        public int $classId,
        public string $className,
        public float $score,       // includes the incumbent bonus, used for choosing
        public float $objective,   // excludes it, used for the solution objective
        public array $reasons = [],
    ) {}
}
