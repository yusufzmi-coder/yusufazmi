<?php

declare(strict_types=1);

namespace App\Domain\Assignment;

use RuntimeException;

final class UnknownRuleTypeException extends RuntimeException
{
    public function __construct(string $key)
    {
        parent::__construct("Jenis peraturan tidak dikenali: [{$key}].");
    }
}
