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

final class FirmTeacherForBehaviourRule implements SoftPreference
{
    public static function key(): string
    {
        return 'pelajar_bermasalah';
    }

    public function label(): string
    {
        return 'Pelajar Bermasalah';
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
        return [
            'min_behaviour_level' => ['required', 'integer', 'between:1,3'],
            'min_firmness' => ['required', 'integer', 'between:1,5'],
            'spread_across_classes' => ['boolean'],
            'max_per_class' => ['nullable', 'integer', 'between:1,20'],
        ];
    }

    public function defaultConfig(): array
    {
        return [
            'min_behaviour_level' => 2,   // Bermasalah and above
            'min_firmness' => 4,          // Tegas and above
            'spread_across_classes' => true,
            'max_per_class' => 4,
        ];
    }

    public function describe(RuleConfig $config): string
    {
        return sprintf(
            'Pelajar bermasalah (tahap %d ke atas) diletakkan bersama guru tegas (ketegasan %d ke atas).',
            $config->int('min_behaviour_level', 2),
            $config->int('min_firmness', 4),
        );
    }

    public function score(
        StudentSnapshot $student,
        ClassSnapshot $class,
        PlacementContext $ctx,
        RuleConfig $config,
    ): Contribution {
        $minLevel = $config->int('min_behaviour_level', 2);

        // A well-behaved child must score exactly zero here, not incidentally negative.
        if ($student->behaviourLevel->value < $minLevel) {
            return Contribution::neutral();
        }

        if ($class->teacherFirmness === null) {
            return new Contribution(-0.5, [new Reason(
                self::key(), 'tiada_guru',
                ['kelas' => $class->name],
                ReasonTone::Negative,
            )]);
        }

        $min = $config->int('min_firmness', 4);
        $firmEnough = $class->teacherFirmness >= $min;

        // Map firmness 1..5 onto [-1, 1], centred on the configured threshold.
        $fit = $firmEnough
            ? 0.6 + 0.4 * (($class->teacherFirmness - $min) / max(1, 5 - $min))
            : -1.0 + 0.8 * (($class->teacherFirmness - 1) / max(1, $min - 1));

        // Severity amplifies: Kritikal cares far more than Perlu Perhatian.
        $fit *= $student->behaviourLevel->value / 3;

        $reasons = [new Reason(
            self::key(),
            $firmEnough ? 'guru_tegas' : 'guru_kurang_tegas',
            [
                'guru' => $class->teacherName ?? '-',
                'ketegasan' => $class->teacherFirmness,
                'kelas' => $class->name,
            ],
            $firmEnough ? ReasonTone::Positive : ReasonTone::Negative,
        )];

        // Don't pile every difficult child onto the same teacher.
        if ($config->bool('spread_across_classes', true)) {
            $cap = $config->int('max_per_class', 4);
            $current = $ctx->behaviourCount($class->id, $minLevel);

            if ($cap > 0 && $current >= $cap) {
                $fit -= 0.8;
                $reasons[] = new Reason(
                    self::key(), 'terlalu_ramai_bermasalah',
                    ['kelas' => $class->name, 'bilangan' => $current, 'had' => $cap],
                    ReasonTone::Negative,
                );
            }
        }

        return new Contribution(max(-1.0, min(1.0, $fit)), $reasons);
    }
}
