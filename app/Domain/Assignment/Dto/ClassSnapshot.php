<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Dto;

final readonly class ClassSnapshot
{
    public function __construct(
        public int $id,
        public string $name,
        public int $yearLevel,
        public int $capacity,           // capacity_override ?? room.capacity
        public ?int $teacherId = null,
        public ?string $teacherName = null,
        public ?int $teacherFirmness = null,
    ) {}
}
