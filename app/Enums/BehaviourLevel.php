<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Severity, not a boolean. When firm teachers are scarce the engine has to know
 * who gets priority; a flag makes that arbitrary, a scale makes it explainable.
 */
enum BehaviourLevel: int
{
    case Normal = 0;
    case PerluPerhatian = 1;
    case Bermasalah = 2;
    case Kritikal = 3;

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Normal',
            self::PerluPerhatian => 'Perlu Perhatian',
            self::Bermasalah => 'Bermasalah',
            self::Kritikal => 'Kritikal',
        };
    }
}
