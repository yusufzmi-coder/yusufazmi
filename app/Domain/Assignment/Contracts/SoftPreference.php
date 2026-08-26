<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Contracts;

use App\Domain\Assignment\Dto\ClassSnapshot;
use App\Domain\Assignment\Dto\Contribution;
use App\Domain\Assignment\Dto\RuleConfig;
use App\Domain\Assignment\Dto\StudentSnapshot;
use App\Domain\Assignment\PlacementContext;

interface SoftPreference extends RuleType
{
    /**
     * Must return a score in [-1.0, 1.0]; the engine multiplies by weight/100.
     * The normalisation is what makes the admin's weight slider meaningful --
     * a rule returning raw headcounts would silently dominate every other rule.
     */
    public function score(
        StudentSnapshot $student,
        ClassSnapshot $class,
        PlacementContext $ctx,
        RuleConfig $config,
    ): Contribution;
}
