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
 * The room is a physical ceiling that exists even when the admin switches the
 * "Had Maksimum Kelas" rule off. Turning that rule off must not put 40 children
 * into a 25-seat room, so this constraint is separate and always on.
 */
final class RoomCapacityRule implements HardConstraint
{
    public static function key(): string
    {
        return 'kapasiti_bilik';
    }

    public function label(): string
    {
        return 'Kapasiti Bilik';
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
        return 'Bilangan pelajar tidak boleh melebihi kapasiti fizikal bilik.';
    }

    public function allows(
        StudentSnapshot $student,
        ClassSnapshot $class,
        PlacementContext $ctx,
        RuleConfig $config,
    ): ConstraintVerdict {
        if ($ctx->headcount($class->id) < $class->capacity) {
            return ConstraintVerdict::allow();
        }

        return ConstraintVerdict::block(new Reason(
            ruleKey: self::key(),
            code: 'bilik_penuh',
            params: ['kelas' => $class->name, 'kapasiti' => $class->capacity],
            tone: ReasonTone::Blocking,
        ));
    }
}
