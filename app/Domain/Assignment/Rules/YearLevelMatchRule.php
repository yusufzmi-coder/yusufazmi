<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Rules;

use App\Domain\Assignment\Contracts\HardConstraint;
use App\Domain\Assignment\Dto\ClassSnapshot;
use App\Domain\Assignment\Dto\ConstraintVerdict;
use App\Domain\Assignment\Dto\Reason;
use App\Domain\Assignment\Dto\RuleConfig;
use App\Domain\Assignment\Dto\StudentSnapshot;
use App\Domain\Assignment\Enums\ReasonTone;
use App\Domain\Assignment\Enums\RuleKind;
use App\Domain\Assignment\PlacementContext;

/**
 * A physical fact, not a preference: a Tahun 3 pupil does not belong in a Tahun 4
 * class at any weight. Never shown in the Peraturan panel, never toggleable.
 */
final class YearLevelMatchRule implements HardConstraint
{
    public static function key(): string
    {
        return 'padanan_tahun';
    }

    public function label(): string
    {
        return 'Padanan Tahun';
    }

    public function kind(): RuleKind
    {
        return RuleKind::Hard;
    }

    public function isSingleton(): bool
    {
        return true;
    }

    public function isConfigurable(): bool
    {
        return false;
    }

    public function configRules(): array
    {
        return [];
    }

    public function defaultConfig(): array
    {
        return [];
    }

    public function describe(RuleConfig $config): string
    {
        return 'Pelajar hanya boleh diletakkan dalam kelas tahun yang sama.';
    }

    public function allows(
        StudentSnapshot $student,
        ClassSnapshot $class,
        PlacementContext $ctx,
        RuleConfig $config,
    ): ConstraintVerdict {
        if ($student->yearLevel === $class->yearLevel) {
            return ConstraintVerdict::allow();
        }

        return ConstraintVerdict::block(new Reason(
            ruleKey: self::key(),
            code: 'tahun_tidak_sepadan',
            params: ['kelas' => $class->name, 'tahun' => $student->yearLevel],
            tone: ReasonTone::Blocking,
        ));
    }
}
