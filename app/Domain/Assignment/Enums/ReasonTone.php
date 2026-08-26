<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Enums;

enum ReasonTone: string
{
    /** Rule satisfied; it drove the choice. */
    case Positive = 'positive';
    /** A soft rule knowingly broken -- a trade-off that must be shown, never hidden. */
    case Negative = 'negative';
    /** A hard constraint refused this class. */
    case Blocking = 'blocking';
    case Info = 'info';
}
