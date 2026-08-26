<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Dto;

final readonly class ConstraintVerdict
{
    private function __construct(
        public bool $allowed,
        public ?Reason $reason,
    ) {}

    public static function allow(): self
    {
        return new self(true, null);
    }

    public static function block(Reason $reason): self
    {
        return new self(false, $reason);
    }
}
