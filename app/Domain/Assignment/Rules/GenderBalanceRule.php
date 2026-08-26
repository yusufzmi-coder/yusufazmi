<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Rules;

use App\Domain\Assignment\Contracts\SoftPreference;
use App\Domain\Assignment\Dto\ClassSnapshot;
use App\Domain\Assignment\Dto\Contribution;
use App\Domain\Assignment\Dto\Reason;
use App\Domain\Assignment\Dto\RuleConfig;
use App\Domain\Assignment\Dto\StudentSnapshot;
use App\Domain\Assignment\Enums\ReasonTone;
use App\Domain\Assignment\Enums\RuleKind;
use App\Domain\Assignment\PlacementContext;
use App\Enums\Gender;

final class GenderBalanceRule implements SoftPreference
{
    public static function key(): string
    {
        return 'seimbangkan_jantina';
    }

    public function label(): string
    {
        return 'Seimbangkan Jantina';
    }

    public function kind(): RuleKind
    {
        return RuleKind::Soft;
    }

    public function isSingleton(): bool
    {
        return true;
    }

    public function isConfigurable(): bool
    {
        return true;
    }

    public function configRules(): array
    {
        return ['max_deviation' => ['nullable', 'numeric', 'between:0.05,1']];
    }

    public function defaultConfig(): array
    {
        return ['max_deviation' => 0.25];
    }

    public function describe(RuleConfig $config): string
    {
        return 'Seimbangkan jumlah pelajar lelaki dan perempuan mengikut nisbah kohort tahun tersebut.';
    }

    public function score(
        StudentSnapshot $student,
        ClassSnapshot $class,
        PlacementContext $ctx,
        RuleConfig $config,
    ): Contribution {
        $males = $ctx->genderCount($class->id, Gender::Lelaki);
        $females = $ctx->genderCount($class->id, Gender::Perempuan);

        if ($student->gender === Gender::Lelaki) {
            $males++;
        } else {
            $females++;
        }

        $total = $males + $females;

        // Compare head COUNTS against the expected count, not ratios. A ratio is
        // scale-invariant, so a class holding 1 boy and one holding 2 boys both read
        // as "100% male" and the rule cannot tell them apart -- which quietly funnels
        // every student into whichever class sorts first.
        $target = $ctx->cohortMaleRatio($student->yearLevel);
        $expectedMales = $target * $total;
        $excess = abs($males - $expectedMales);

        $maxDeviation = (float) ($config->data['max_deviation'] ?? 0.25);
        $maxDeviation = $maxDeviation <= 0.0 ? 0.25 : $maxDeviation;

        // Scaling by class size keeps the score inside [-1, 1] without saturating so
        // early that genuinely different options end up tied.
        $scale = max(1.0, $class->capacity * $maxDeviation * 2);

        $score = 1.0 - 2.0 * ($excess / $scale);
        $score = max(-1.0, min(1.0, $score));

        return new Contribution($score, [new Reason(
            self::key(),
            $score >= 0 ? 'imbangan_baik' : 'imbangan_terjejas',
            ['kelas' => $class->name, 'lelaki' => $males, 'perempuan' => $females],
            $score >= 0 ? ReasonTone::Positive : ReasonTone::Negative,
        )]);
    }
}
