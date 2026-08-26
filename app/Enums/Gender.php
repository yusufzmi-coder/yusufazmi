<?php

declare(strict_types=1);

namespace App\Enums;

enum Gender: string
{
    case Lelaki = 'L';
    case Perempuan = 'P';

    public function label(): string
    {
        return match ($this) {
            self::Lelaki => 'Lelaki',
            self::Perempuan => 'Perempuan',
        };
    }
}
