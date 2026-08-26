<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Dto;

use App\Domain\Assignment\Enums\ItemAction;

final readonly class ResultItem
{
    /**
     * @param  list<Reason>  $reasons
     * @param  list<Reason>  $violations
     */
    public function __construct(
        public int $studentId,
        public ?int $fromClassId,
        public ?int $toClassId,
        public ItemAction $action,
        public ?float $score,
        public array $reasons = [],
        public array $violations = [],
        public int $rank = 0,
    ) {}
}
