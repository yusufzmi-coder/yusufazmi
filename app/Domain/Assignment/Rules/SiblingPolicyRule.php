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
use App\Enums\SiblingPolicy;

/**
 * Bidirectional by design. The centre's default is usually "keep siblings apart",
 * but some parents specifically ask for their children to be together -- that is a
 * per-family wish, so it lives on the family row and overrides the global default.
 */
final class SiblingPolicyRule implements SoftPreference
{
    public static function key(): string
    {
        return 'kelas_adik_beradik';
    }

    public function label(): string
    {
        return 'Kelas Adik Beradik';
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
            'default_policy' => ['required', 'string', 'in:apart,together'],
            'allow_family_override' => ['boolean'],
        ];
    }

    public function defaultConfig(): array
    {
        return ['default_policy' => 'apart', 'allow_family_override' => true];
    }

    public function describe(RuleConfig $config): string
    {
        $base = $config->string('default_policy', 'apart') === 'together'
            ? 'Cuba letakkan adik-beradik dalam kelas yang sama.'
            : 'Elakkan adik-beradik dalam kelas yang sama.';

        return $config->bool('allow_family_override', true)
            ? $base.' Pilihan keluarga tertentu akan diutamakan.'
            : $base;
    }

    public function score(
        StudentSnapshot $student,
        ClassSnapshot $class,
        PlacementContext $ctx,
        RuleConfig $config,
    ): Contribution {
        if ($student->familyId === null) {
            return Contribution::neutral();
        }

        // Only siblings already seated can influence the score.
        $here = $ctx->siblingsPlacedIn($student, $class->id);
        $elsewhere = $ctx->siblingsPlacedElsewhere($student, $class->id);

        if ($here === [] && $elsewhere === []) {
            return Contribution::neutral();
        }

        $policy = $this->resolvePolicy($student, $config);
        $names = static fn (array $s): string => implode(', ', array_map(
            static fn (StudentSnapshot $x): string => $x->name,
            $s,
        ));

        if ($policy === SiblingPolicy::Together) {
            return $here !== []
                ? new Contribution(1.0, [new Reason(
                    self::key(), 'bersama_dipenuhi',
                    ['adik_beradik' => $names($here), 'kelas' => $class->name],
                    ReasonTone::Positive,
                )])
                : new Contribution(-1.0, [new Reason(
                    self::key(), 'bersama_gagal',
                    ['adik_beradik' => $names($elsewhere)],
                    ReasonTone::Negative,
                )]);
        }

        // Apart: the penalty grows with how many siblings are already crowded in.
        if ($here !== []) {
            return new Contribution(-min(1.0, count($here) / 2), [new Reason(
                self::key(), 'asing_gagal',
                ['adik_beradik' => $names($here), 'kelas' => $class->name],
                ReasonTone::Negative,
            )]);
        }

        return new Contribution(1.0, [new Reason(
            self::key(), 'asing_dipenuhi',
            ['adik_beradik' => $names($elsewhere), 'kelas' => $class->name],
            ReasonTone::Positive,
        )]);
    }

    private function resolvePolicy(StudentSnapshot $student, RuleConfig $config): SiblingPolicy
    {
        $default = SiblingPolicy::tryFrom($config->string('default_policy', 'apart')) ?? SiblingPolicy::Apart;

        if (! $config->bool('allow_family_override', true)) {
            return $default;
        }

        return $student->familyPolicy === SiblingPolicy::Inherit
            ? $default
            : $student->familyPolicy;
    }
}
