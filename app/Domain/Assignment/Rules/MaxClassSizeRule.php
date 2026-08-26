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

final class MaxClassSizeRule implements HardConstraint
{
    public static function key(): string
    {
        return 'had_maksimum_kelas';
    }

    public function label(): string
    {
        return 'Had Maksimum Kelas';
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
        return true;
    }

    public function configRules(): array
    {
        return ['max_students' => ['required', 'integer', 'min:1', 'max:60']];
    }

    public function defaultConfig(): array
    {
        return ['max_students' => 30];
    }

    public function describe(RuleConfig $config): string
    {
        return sprintf('Maksimum %d pelajar setiap kelas.', $config->int('max_students', 30));
    }

    public function allows(
        StudentSnapshot $student,
        ClassSnapshot $class,
        PlacementContext $ctx,
        RuleConfig $config,
    ): ConstraintVerdict {
        // The effective cap is the tighter of the admin's rule and the room itself.
        $cap = min($config->int('max_students', 30), $class->capacity);

        if ($ctx->headcount($class->id) < $cap) {
            return ConstraintVerdict::allow();
        }

        return ConstraintVerdict::block(new Reason(
            ruleKey: self::key(),
            code: 'kelas_penuh',
            params: ['kelas' => $class->name, 'had' => $cap],
            tone: ReasonTone::Blocking,
        ));
    }
}
