<?php

declare(strict_types=1);

namespace App\Enums;

enum SiblingPolicy: string
{
    /** Follow whatever the global rule says. */
    case Inherit = 'inherit';
    /** This family asked for their children to be in the same class. */
    case Together = 'together';
    /** This family asked for their children to be separated. */
    case Apart = 'apart';

    public function label(): string
    {
        return match ($this) {
            self::Inherit => 'Ikut peraturan umum',
            self::Together => 'Mahu sekelas',
            self::Apart => 'Mahu diasingkan',
        };
    }
}
