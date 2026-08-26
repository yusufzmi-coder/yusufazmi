<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Dto;

final readonly class ExistingPlacement
{
    public function __construct(
        public int $studentId,
        public int $classId,
        public bool $isPinned = false,
    ) {}
}
