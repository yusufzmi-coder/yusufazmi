<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Dto;

use App\Enums\BehaviourLevel;
use App\Enums\Gender;
use App\Enums\SiblingPolicy;

final readonly class StudentSnapshot
{
    public function __construct(
        public int $id,
        public string $name,
        public int $yearLevel,
        public Gender $gender,
        public BehaviourLevel $behaviourLevel,
        public ?int $familyId = null,
        public SiblingPolicy $familyPolicy = SiblingPolicy::Inherit,
    ) {}
}
