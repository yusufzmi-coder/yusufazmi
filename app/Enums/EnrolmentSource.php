<?php

declare(strict_types=1);

namespace App\Enums;

enum EnrolmentSource: string
{
    case Manual = 'manual';
    case Auto = 'auto';
    case Import = 'import';
}
