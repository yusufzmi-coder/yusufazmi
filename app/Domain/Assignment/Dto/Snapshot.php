<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Dto;

/**
 * The complete, immutable input to a solve. The solver is a pure function over
 * this object -- no database access happens inside the loop, which is what makes
 * it fast, testable and deterministic.
 */
final readonly class Snapshot
{
    /**
     * @param  list<StudentSnapshot>  $students
     * @param  list<ClassSnapshot>  $classes
     * @param  list<ExistingPlacement>  $existingPlacements
     * @param  list<RuleConfig>  $rules
     */
    public function __construct(
        public array $students,
        public array $classes,
        public array $existingPlacements = [],
        public array $rules = [],
        public int $sessionId = 0,
    ) {}
}
