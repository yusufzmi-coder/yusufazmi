<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Contracts;

use App\Domain\Assignment\Dto\ClassSnapshot;
use App\Domain\Assignment\Dto\ConstraintVerdict;
use App\Domain\Assignment\Dto\RuleConfig;
use App\Domain\Assignment\Dto\StudentSnapshot;
use App\Domain\Assignment\PlacementContext;

/** Filters the candidate set. A blocked class is never scored at all. */
interface HardConstraint extends RuleType
{
    public function allows(
        StudentSnapshot $student,
        ClassSnapshot $class,
        PlacementContext $ctx,
        RuleConfig $config,
    ): ConstraintVerdict;
}
