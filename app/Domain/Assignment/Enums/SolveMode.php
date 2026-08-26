<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Enums;

enum SolveMode: string
{
    /** Only place students who have no active enrolment. Existing placements are fixed. */
    case FillOnly = 'fill_only';
    /** May relocate unplaced students, but must beat the incumbent bonus to do so. */
    case Rebalance = 'rebalance';
}
