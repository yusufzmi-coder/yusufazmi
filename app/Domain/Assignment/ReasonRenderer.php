<?php

declare(strict_types=1);

namespace App\Domain\Assignment;

use App\Domain\Assignment\Dto\Reason;
use App\Domain\Assignment\Dto\ResultItem;
use App\Domain\Assignment\Enums\ItemAction;
use App\Domain\Assignment\Enums\ReasonTone;
use Illuminate\Support\Facades\Lang;

/**
 * Turns structured reasons into the Malay sentence the admin actually reads.
 *
 * The top positives explain the choice; every negative is appended even when it
 * makes the sentence longer, because a trade-off the system hid is a trade-off the
 * admin discovers the hard way.
 */
final class ReasonRenderer
{
    private const MAX_POSITIVES = 3;

    public function render(ResultItem $item, ?string $toClass, ?string $fromClass = null): string
    {
        $reasons = $item->action === ItemAction::Unplaceable ? $item->violations : $item->reasons;
        $parts = $this->phrases($reasons);

        $replace = [
            'kelas' => $toClass ?? '-',
            'dari' => $fromClass ?? '-',
        ];

        if ($parts === []) {
            return trim(Lang::get("assignment.fallback.{$item->action->value}", $replace, 'ms'));
        }

        return trim(Lang::get(
            "assignment.sentence.{$item->action->value}",
            $replace + ['sebab' => implode('; ', $parts)],
            'ms',
        ));
    }

    /**
     * @param  list<Reason>  $reasons
     * @return list<string>
     */
    public function phrases(array $reasons): array
    {
        $positive = [];
        $negative = [];

        foreach ($reasons as $reason) {
            $text = $this->phrase($reason);

            if ($text === null) {
                continue;
            }

            match ($reason->tone) {
                ReasonTone::Negative => $negative[] = ['text' => $text, 'delta' => abs($reason->weightedDelta)],
                default => $positive[] = ['text' => $text, 'delta' => abs($reason->weightedDelta)],
            };
        }

        // Strongest influence first, so the headline reason is the real one.
        usort($positive, static fn (array $a, array $b): int => $b['delta'] <=> $a['delta']);
        usort($negative, static fn (array $a, array $b): int => $b['delta'] <=> $a['delta']);

        return array_merge(
            array_column(array_slice($positive, 0, self::MAX_POSITIVES), 'text'),
            array_column($negative, 'text'),   // never truncated
        );
    }

    public function phrase(Reason $reason): ?string
    {
        $key = $reason->translationKey();

        if (! Lang::has($key, 'ms')) {
            return null;
        }

        return Lang::get($key, $reason->params, 'ms');
    }
}
