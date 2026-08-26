<?php

declare(strict_types=1);

namespace App\Domain\Assignment;

use App\Domain\Assignment\Dto\ClassSnapshot;
use App\Domain\Assignment\Dto\Reason;
use App\Domain\Assignment\Dto\ResultItem;
use App\Domain\Assignment\Dto\ScoredPlacement;
use App\Domain\Assignment\Dto\Snapshot;
use App\Domain\Assignment\Dto\SolveResult;
use App\Domain\Assignment\Dto\StudentSnapshot;
use App\Domain\Assignment\Enums\ItemAction;
use App\Domain\Assignment\Enums\SolveMode;
use App\Enums\Gender;

/**
 * The mutable working set for one solve.
 *
 * Every counter is maintained incrementally by seat()/unseat(), so a rule's
 * score() is O(1) rather than a scan over the class roster. That is the whole
 * reason a full solve of ~250 students finishes inside a web request.
 */
final class PlacementContext
{
    /** @var array<int, StudentSnapshot> */
    private array $students = [];

    /** @var array<int, ClassSnapshot> */
    private array $classes = [];

    /** @var array<int, list<ClassSnapshot>> */
    private array $classesByYear = [];

    /** @var array<int, int> student id => class id */
    private array $seat = [];

    /** @var array<int, bool> student id => may not be moved */
    private array $fixed = [];

    /** @var array<int, int> student id => class id at the start of the solve */
    private array $originalSeat = [];

    /** @var array<int, bool> student id => pinned by an admin */
    private array $pinned = [];

    /** @var array<int, int> class id => headcount */
    private array $headcount = [];

    /** @var array<int, array<string, int>> class id => gender => count */
    private array $genderCount = [];

    /** @var array<int, array<int, int>> class id => behaviour level => count */
    private array $levelCount = [];

    /** @var array<int, array<int, int>> family id => student id => class id */
    private array $familySeats = [];

    /** @var array<int, array<string, int>> year level => gender => count */
    private array $cohortGender = [];

    /** @var array<int, list<Reason>> student id => reasons for the current placement */
    private array $reasons = [];

    /** @var array<int, list<Reason>> student id => blocking reasons */
    private array $violations = [];

    /** @var array<int, float> student id => objective contribution */
    private array $objective = [];

    /** @var array<int, int> student id => placement order */
    private array $rank = [];

    private int $iterations = 0;

    private function __construct(public readonly SolveMode $mode) {}

    public static function fromSnapshot(Snapshot $snapshot, SolveMode $mode): self
    {
        $ctx = new self($mode);

        foreach ($snapshot->classes as $class) {
            $ctx->classes[$class->id] = $class;
            $ctx->classesByYear[$class->yearLevel][] = $class;
            $ctx->headcount[$class->id] = 0;
            $ctx->genderCount[$class->id] = [Gender::Lelaki->value => 0, Gender::Perempuan->value => 0];
            $ctx->levelCount[$class->id] = [0 => 0, 1 => 0, 2 => 0, 3 => 0];
        }

        // Deterministic candidate order everywhere: lowest class id first.
        foreach ($ctx->classesByYear as $year => $classes) {
            usort($classes, static fn (ClassSnapshot $a, ClassSnapshot $b): int => $a->id <=> $b->id);
            $ctx->classesByYear[$year] = $classes;
        }

        foreach ($snapshot->students as $student) {
            $ctx->students[$student->id] = $student;
            $ctx->cohortGender[$student->yearLevel] ??= [Gender::Lelaki->value => 0, Gender::Perempuan->value => 0];
            $ctx->cohortGender[$student->yearLevel][$student->gender->value]++;
        }

        ksort($ctx->students);

        return $ctx;
    }

    // ---------------------------------------------------------------- seating

    public function seat(int $studentId, int $classId, bool $fixed = false): void
    {
        if (! isset($this->students[$studentId]) || ! isset($this->classes[$classId])) {
            return;
        }

        if (isset($this->seat[$studentId])) {
            $this->unseat($studentId);
        }

        $student = $this->students[$studentId];

        $this->seat[$studentId] = $classId;
        $this->fixed[$studentId] = $fixed;
        $this->headcount[$classId]++;
        $this->genderCount[$classId][$student->gender->value]++;
        $this->levelCount[$classId][$student->behaviourLevel->value]++;

        if ($student->familyId !== null) {
            $this->familySeats[$student->familyId][$studentId] = $classId;
        }
    }

    public function unseat(int $studentId): void
    {
        $classId = $this->seat[$studentId] ?? null;

        if ($classId === null) {
            return;
        }

        $student = $this->students[$studentId];

        $this->headcount[$classId]--;
        $this->genderCount[$classId][$student->gender->value]--;
        $this->levelCount[$classId][$student->behaviourLevel->value]--;

        unset($this->seat[$studentId], $this->fixed[$studentId]);

        if ($student->familyId !== null) {
            unset($this->familySeats[$student->familyId][$studentId]);
        }
    }

    /** Records where everyone sat before the solve began, so actions can be derived. */
    public function markOriginal(int $studentId, int $classId, bool $isPinned): void
    {
        $this->originalSeat[$studentId] = $classId;

        if ($isPinned) {
            $this->pinned[$studentId] = true;
        }
    }

    // ---------------------------------------------------------------- queries

    public function currentClassOf(int $studentId): ?int
    {
        return $this->seat[$studentId] ?? null;
    }

    public function headcount(int $classId): int
    {
        return $this->headcount[$classId] ?? 0;
    }

    public function genderCount(int $classId, Gender $gender): int
    {
        return $this->genderCount[$classId][$gender->value] ?? 0;
    }

    /** How many students at or above the given behaviour level sit in this class. */
    public function behaviourCount(int $classId, int $minLevel): int
    {
        $total = 0;

        for ($level = $minLevel; $level <= 3; $level++) {
            $total += $this->levelCount[$classId][$level] ?? 0;
        }

        return $total;
    }

    /** @return list<ClassSnapshot> */
    public function classesForYear(int $yearLevel): array
    {
        return $this->classesByYear[$yearLevel] ?? [];
    }

    public function classById(int $classId): ?ClassSnapshot
    {
        return $this->classes[$classId] ?? null;
    }

    /**
     * The share of boys in this year group. Gender balance targets the cohort's own
     * ratio, not a hard 50/50 -- a Tahun 6 intake that is 70% girls should not have
     * the engine fighting arithmetic.
     */
    public function cohortMaleRatio(int $yearLevel): float
    {
        $counts = $this->cohortGender[$yearLevel] ?? null;

        if ($counts === null) {
            return 0.5;
        }

        $total = $counts[Gender::Lelaki->value] + $counts[Gender::Perempuan->value];

        return $total === 0 ? 0.5 : $counts[Gender::Lelaki->value] / $total;
    }

    /** @return list<StudentSnapshot> */
    public function unseatedStudents(): array
    {
        return array_values(array_filter(
            $this->students,
            fn (StudentSnapshot $s): bool => ! isset($this->seat[$s->id]),
        ));
    }

    /** @return list<StudentSnapshot> */
    public function movableStudents(): array
    {
        return array_values(array_filter(
            $this->students,
            fn (StudentSnapshot $s): bool => isset($this->seat[$s->id]) && ! ($this->fixed[$s->id] ?? false),
        ));
    }

    /** @return list<StudentSnapshot> */
    public function allStudents(): array
    {
        return array_values($this->students);
    }

    public function isPinned(int $studentId): bool
    {
        return $this->pinned[$studentId] ?? false;
    }

    // ---------------------------------------------------------------- siblings

    /** @return list<StudentSnapshot> */
    public function siblingsPlacedIn(StudentSnapshot $student, int $classId): array
    {
        return $this->siblings($student, $classId, inside: true);
    }

    /** @return list<StudentSnapshot> */
    public function siblingsPlacedElsewhere(StudentSnapshot $student, int $classId): array
    {
        return $this->siblings($student, $classId, inside: false);
    }

    /** @return list<StudentSnapshot> */
    private function siblings(StudentSnapshot $student, int $classId, bool $inside): array
    {
        if ($student->familyId === null) {
            return [];
        }

        $out = [];

        foreach ($this->familySeats[$student->familyId] ?? [] as $siblingId => $seatedIn) {
            if ($siblingId === $student->id) {
                continue;
            }

            if (($seatedIn === $classId) === $inside) {
                $out[] = $this->students[$siblingId];
            }
        }

        usort($out, static fn (StudentSnapshot $a, StudentSnapshot $b): int => $a->id <=> $b->id);

        return $out;
    }

    // ---------------------------------------------------------------- recording

    public function record(StudentSnapshot $student, ScoredPlacement $placement, int $rank = 0): void
    {
        $this->reasons[$student->id] = $placement->reasons;
        $this->objective[$student->id] = $placement->objective;
        $this->rank[$student->id] = $rank;
    }

    /** @param list<Reason> $blockers */
    public function markUnplaceable(StudentSnapshot $student, array $blockers, int $rank = 0): void
    {
        $this->violations[$student->id] = $blockers;
        $this->rank[$student->id] = $rank;
    }

    public function objectiveOf(int $studentId): float
    {
        return $this->objective[$studentId] ?? 0.0;
    }

    public function objectiveScore(): float
    {
        return round(array_sum($this->objective), 3);
    }

    public function countIteration(): void
    {
        $this->iterations++;
    }

    // ---------------------------------------------------------------- result

    public function toResult(int $durationMs): SolveResult
    {
        $items = [];

        foreach ($this->students as $student) {
            $to = $this->seat[$student->id] ?? null;
            $from = $this->originalSeat[$student->id] ?? null;
            $reasons = $this->reasons[$student->id] ?? [];

            $action = match (true) {
                $this->isPinned($student->id) => ItemAction::Pinned,
                $to === null => ItemAction::Unplaceable,
                $from === null => ItemAction::Place,
                $from === $to => ItemAction::Keep,
                default => ItemAction::Move,
            };

            // Trade-offs are surfaced separately so the preview can never bury them.
            $violations = $action === ItemAction::Unplaceable
                ? ($this->violations[$student->id] ?? [])
                : array_values(array_filter(
                    $reasons,
                    static fn (Reason $r): bool => $r->tone->value === 'negative',
                ));

            $items[] = new ResultItem(
                studentId: $student->id,
                fromClassId: $from,
                toClassId: $to,
                action: $action,
                score: $this->objective[$student->id] ?? null,
                reasons: $reasons,
                violations: $violations,
                rank: $this->rank[$student->id] ?? 0,
            );
        }

        return new SolveResult(
            items: $items,
            objective: $this->objectiveScore(),
            durationMs: $durationMs,
            iterations: $this->iterations,
        );
    }
}
