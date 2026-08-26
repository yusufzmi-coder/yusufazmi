<?php

declare(strict_types=1);

namespace App\Domain\Assignment;

use App\Domain\Assignment\Contracts\HardConstraint;
use App\Domain\Assignment\Contracts\SoftPreference;
use App\Domain\Assignment\Dto\ClassSnapshot;
use App\Domain\Assignment\Dto\Reason;
use App\Domain\Assignment\Dto\RuleConfig;
use App\Domain\Assignment\Dto\ScoredPlacement;
use App\Domain\Assignment\Dto\Snapshot;
use App\Domain\Assignment\Dto\SolveResult;
use App\Domain\Assignment\Dto\StudentSnapshot;
use App\Domain\Assignment\Enums\ReasonTone;
use App\Domain\Assignment\Enums\RuleKind;
use App\Domain\Assignment\Enums\SolveMode;

/**
 * Greedy most-constrained-first, then a bounded local-search repair pass.
 *
 * Pure greedy alone is not enough: gender balance and sibling spreading are global
 * properties, so whoever is placed last gets whatever is left and the final classes
 * end up lopsided. A CSP/ILP solver would fix that but returns an assignment vector
 * with no narrative -- and the hard requirement here is that an admin reads a Malay
 * sentence and nods. Nobody can tell a 0.94-optimal timetable from a 1.00-optimal
 * one; everybody notices one they cannot explain.
 *
 * There is no database access anywhere below. The solver is a pure function over
 * the snapshot, which is what keeps it fast, testable and reproducible.
 */
final class AssignmentSolver
{
    public function __construct(
        private readonly RuleTypeRegistry $registry,
        private readonly float $incumbentBonus = 0.25,
        private readonly int $maxPasses = 6,
        private readonly int $timeBudgetMs = 1500,
        private readonly float $epsilon = 0.001,
    ) {}

    public function solve(Snapshot $snapshot, SolveMode $mode = SolveMode::FillOnly): SolveResult
    {
        $startedAt = hrtime(true);
        $ctx = PlacementContext::fromSnapshot($snapshot, $mode);

        [$hard, $soft] = $this->partitionRules($snapshot->rules);

        // --- Phase 1: seed. Pinned students are always fixed; in fill_only, so is
        // everyone who already has a class.
        foreach ($snapshot->existingPlacements as $placement) {
            $ctx->markOriginal($placement->studentId, $placement->classId, $placement->isPinned);
            $ctx->seat(
                $placement->studentId,
                $placement->classId,
                fixed: $placement->isPinned || $mode === SolveMode::FillOnly,
            );
        }

        // --- Phase 2: greedy, most-constrained-first.
        foreach ($this->orderStudents($ctx->unseatedStudents(), $ctx, $hard, $snapshot) as $rank => $student) {
            $feasible = $this->feasibleClasses($student, $ctx, $hard);

            if ($feasible === []) {
                $ctx->markUnplaceable($student, $this->collectBlockers($student, $ctx, $hard), $rank);

                continue;
            }

            $best = $this->scoreAll($student, $feasible, $ctx, $soft)[0];

            $ctx->seat($student->id, $best->classId);
            $ctx->record($student, $best, $rank);
        }

        // --- Phase 3: bounded repair for the greedy tail.
        $this->repair($ctx, $hard, $soft, $startedAt);

        return $ctx->toResult(
            durationMs: (int) ((hrtime(true) - $startedAt) / 1_000_000),
        );
    }

    /**
     * @param  list<RuleConfig>  $rules
     * @return array{0: list<array{HardConstraint, RuleConfig}>, 1: list<array{SoftPreference, RuleConfig}>}
     */
    private function partitionRules(array $rules): array
    {
        $hard = [];
        $soft = [];

        // Physical facts first: these apply regardless of what the rules table says.
        foreach ($this->registry->alwaysOn() as $key => $type) {
            if ($type instanceof HardConstraint) {
                $hard[] = [$type, new RuleConfig(0, $key, RuleKind::Hard, 100)];
            }
        }

        foreach ($rules as $config) {
            if (! $this->registry->has($config->ruleType)) {
                continue;
            }

            $type = $this->registry->get($config->ruleType);

            if ($type instanceof HardConstraint) {
                $hard[] = [$type, $config];
            } elseif ($type instanceof SoftPreference) {
                $soft[] = [$type, $config];
            }
        }

        return [$hard, $soft];
    }

    /**
     * Most-constrained-first. Students whose year level has only one open class must
     * go before the ones with choices, or they become unplaceable for no reason.
     *
     * @param  list<StudentSnapshot>  $students
     * @param  list<array{HardConstraint, RuleConfig}>  $hard
     * @return list<StudentSnapshot>
     */
    private function orderStudents(array $students, PlacementContext $ctx, array $hard, Snapshot $snapshot): array
    {
        $familySize = [];

        foreach ($snapshot->students as $student) {
            if ($student->familyId !== null) {
                $familySize[$student->familyId] = ($familySize[$student->familyId] ?? 0) + 1;
            }
        }

        $keyed = array_map(
            fn (StudentSnapshot $s): array => [
                'student' => $s,
                'options' => count($this->feasibleClasses($s, $ctx, $hard)),
                'severity' => $s->behaviourLevel->value,
                'family' => $s->familyId === null ? 0 : ($familySize[$s->familyId] ?? 0),
            ],
            $students,
        );

        usort($keyed, static fn (array $a, array $b): int =>
            // Fewest options first, then hardest to place, then biggest family,
            // then id -- the anchor that makes runs reproducible. Never rand().
            [$a['options'], -$a['severity'], -$a['family'], $a['student']->id]
            <=> [$b['options'], -$b['severity'], -$b['family'], $b['student']->id]
        );

        return array_column($keyed, 'student');
    }

    /**
     * @param  list<ClassSnapshot>  $feasible
     * @param  list<array{SoftPreference, RuleConfig}>  $soft
     * @return list<ScoredPlacement> sorted best first
     */
    private function scoreAll(StudentSnapshot $student, array $feasible, PlacementContext $ctx, array $soft): array
    {
        $scored = [];

        foreach ($feasible as $class) {
            $total = 0.0;
            $reasons = [];

            foreach ($soft as [$rule, $config]) {
                if (! $config->appliesToStudent($student)) {
                    continue;
                }

                $contribution = $rule->score($student, $class, $ctx, $config);
                $weighted = $contribution->score * ($config->weight / 100);
                $total += $weighted;

                foreach ($contribution->reasons as $reason) {
                    $reasons[] = $reason->withDelta($weighted);
                }
            }

            $objective = $total;

            // Stickiness: when two classes score the same, the student stays put.
            // This is what makes "re-running does not shuffle everybody" true rather
            // than merely hoped for -- and it is small enough that a genuinely
            // better class still wins.
            if ($ctx->currentClassOf($student->id) === $class->id) {
                $total += $this->incumbentBonus;
                $reasons[] = new Reason(
                    'sistem', 'kekal_di_kelas_asal',
                    ['kelas' => $class->name], ReasonTone::Info,
                );
            }

            $scored[] = new ScoredPlacement($class->id, $class->name, $total, $objective, $reasons);
            $ctx->countIteration();
        }

        // Highest score, then lowest class id. Deterministic in every tie.
        usort($scored, static fn (ScoredPlacement $a, ScoredPlacement $b): int => [$b->score, $a->classId] <=> [$a->score, $b->classId]
        );

        return $scored;
    }

    /**
     * @param  list<array{HardConstraint, RuleConfig}>  $hard
     * @return list<ClassSnapshot>
     */
    private function feasibleClasses(StudentSnapshot $student, PlacementContext $ctx, array $hard): array
    {
        $out = [];

        foreach ($ctx->classesForYear($student->yearLevel) as $class) {
            foreach ($hard as [$rule, $config]) {
                if (! $config->appliesToStudent($student)) {
                    continue;
                }

                if (! $rule->allows($student, $class, $ctx, $config)->allowed) {
                    continue 2;
                }
            }

            $out[] = $class;
        }

        return $out;
    }

    /**
     * Why every class was refused, deduplicated by reason code -- so the admin reads
     * "Semua 3 kelas Tahun 3 penuh (had 30)" rather than getting a shrug.
     *
     * @param  list<array{HardConstraint, RuleConfig}>  $hard
     * @return list<Reason>
     */
    private function collectBlockers(StudentSnapshot $student, PlacementContext $ctx, array $hard): array
    {
        $blockers = [];

        foreach ($ctx->classesForYear($student->yearLevel) as $class) {
            foreach ($hard as [$rule, $config]) {
                if (! $config->appliesToStudent($student)) {
                    continue;
                }

                $verdict = $rule->allows($student, $class, $ctx, $config);

                if (! $verdict->allowed && $verdict->reason !== null) {
                    $blockers[$verdict->reason->ruleKey.'.'.$verdict->reason->code] = $verdict->reason;

                    break;
                }
            }
        }

        if ($blockers === [] && $ctx->classesForYear($student->yearLevel) === []) {
            $blockers[] = new Reason(
                'sistem', 'tiada_kelas_tahun',
                ['tahun' => $student->yearLevel],
                ReasonTone::Blocking,
            );
        }

        return array_values($blockers);
    }

    /**
     * Hill-climb over the greedy tail: try relocating each movable student and accept
     * only strict improvements. Fixed pass order plus a strict epsilon means ties
     * never flip, so the result stays reproducible.
     *
     * @param  list<array{HardConstraint, RuleConfig}>  $hard
     * @param  list<array{SoftPreference, RuleConfig}>  $soft
     */
    private function repair(PlacementContext $ctx, array $hard, array $soft, int|float $startedAt): void
    {
        for ($pass = 0; $pass < $this->maxPasses; $pass++) {
            $improved = false;

            foreach ($ctx->movableStudents() as $student) {
                if ((hrtime(true) - $startedAt) / 1_000_000 > $this->timeBudgetMs) {
                    return;
                }

                $from = $ctx->currentClassOf($student->id);

                if ($from === null) {
                    continue;
                }

                // Free the seat first, so capacity checks see the true picture.
                $ctx->unseat($student->id);
                $feasible = $this->feasibleClasses($student, $ctx, $hard);

                if ($feasible === []) {
                    $ctx->seat($student->id, $from);

                    continue;
                }

                $scored = $this->scoreAll($student, $feasible, $ctx, $soft);
                $best = $scored[0];
                $stay = $this->findPlacement($scored, $from);

                $worthMoving = $best->classId !== $from
                    && ($stay === null || $best->objective > $stay->objective + $this->epsilon);

                $chosen = $worthMoving ? $best : ($stay ?? $best);

                $ctx->seat($student->id, $chosen->classId);
                $ctx->record($student, $chosen);

                if ($chosen->classId !== $from) {
                    $improved = true;
                }
            }

            if (! $improved) {
                return; // converged
            }
        }
    }

    /** @param list<ScoredPlacement> $scored */
    private function findPlacement(array $scored, int $classId): ?ScoredPlacement
    {
        foreach ($scored as $placement) {
            if ($placement->classId === $classId) {
                return $placement;
            }
        }

        return null;
    }
}
