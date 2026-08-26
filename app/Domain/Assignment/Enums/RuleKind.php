<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Enums;

enum RuleKind: string
{
    /** Must never be violated. Filters the candidate set; weight is meaningless. */
    case Hard = 'hard';
    /** A weighted preference. Contributes to the score. */
    case Soft = 'soft';
}
