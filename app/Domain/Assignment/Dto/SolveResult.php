<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Dto;

use App\Domain\Assignment\Enums\ItemAction;

final readonly class SolveResult
{
    /** @param list<ResultItem> $items */
    public function __construct(
        public array $items,
        public float $objective,
        public int $durationMs,
        public int $iterations = 0,
    ) {}

    /** @return array<string, int> */
    public function stats(): array
    {
        $counts = array_fill_keys(array_column(ItemAction::cases(), 'value'), 0);

        foreach ($this->items as $item) {
            $counts[$item->action->value]++;
        }

        $counts['violations'] = array_sum(array_map(
            static fn (ResultItem $i): int => count($i->violations),
            $this->items,
        ));
        $counts['iterations'] = $this->iterations;

        return $counts;
    }

    /** @return array<int, int|null> student id => class id */
    public function placementMap(): array
    {
        $map = [];

        foreach ($this->items as $item) {
            $map[$item->studentId] = $item->toClassId;
        }

        ksort($map);

        return $map;
    }
}
