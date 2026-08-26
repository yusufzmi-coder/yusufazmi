<?php

declare(strict_types=1);

namespace App\Enums;

enum EnrolmentStatus: string
{
    case Active = 'active';
    case Moved = 'moved';
    case Withdrawn = 'withdrawn';
}
