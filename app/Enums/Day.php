<?php

declare(strict_types=1);

namespace App\Enums;

enum Day: string
{
    case Isnin = 'isnin';
    case Selasa = 'selasa';
    case Rabu = 'rabu';
    case Khamis = 'khamis';
    case Jumaat = 'jumaat';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    /** @return list<self> */
    public static function schoolWeek(): array
    {
        return self::cases();
    }
}
