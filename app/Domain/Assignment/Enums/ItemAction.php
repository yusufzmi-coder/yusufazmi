<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Enums;

enum ItemAction: string
{
    case Place = 'place';
    case Move = 'move';
    case Keep = 'keep';
    case Pinned = 'pinned';
    case Unplaceable = 'unplaceable';
}
