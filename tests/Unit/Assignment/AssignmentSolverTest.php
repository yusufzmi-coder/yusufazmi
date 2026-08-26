<?php

declare(strict_types=1);

use App\Domain\Assignment\AssignmentSolver;
use App\Domain\Assignment\Enums\ItemAction;
use App\Domain\Assignment\Enums\SolveMode;
use App\Domain\Assignment\Rules\FirmTeacherForBehaviourRule;
use App\Domain\Assignment\Rules\GenderBalanceRule;
use App\Domain\Assignment\Rules\MaxClassSizeRule;
use App\Domain\Assignment\Rules\SiblingPolicyRule;
use App\Enums\BehaviourLevel;
use App\Enums\Gender;
use App\Enums\SiblingPolicy;
use Tests\Support\SnapshotBuilder;

function solve(SnapshotBuilder $b, SolveMode $mode = SolveMode::FillOnly)
{
    return app(AssignmentSolver::class)->solve($b->build(), $mode);
}

/** @return array<string, string|null> student name => class name */
function placements(SnapshotBuilder $b, $result): array
{
    $out = [];

    foreach ($result->placementMap() as $studentId => $classId) {
        $out[$b->studentName($studentId)] = $classId === null ? null : $b->className($classId);
    }

    return $out;
}

// ---------------------------------------------------------------- hard constraints

it('never exceeds the configured max class size', function () {
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 30], '4 BETA' => ['capacity' => 30]])
        ->students(10, yearLevel: 4)
        ->rule(MaxClassSizeRule::key(), config: ['max_students' => 3]);

    $result = solve($b);
    $placed = array_filter($result->placementMap());

    expect($placed)->toHaveCount(6)
        ->and($result->stats()[ItemAction::Unplaceable->value])->toBe(4);
});

it('respects room capacity even when the max class size rule is disabled', function () {
    // The trap: switching off "Had Maksimum Kelas" must not put 40 children into a
    // 2-seat room. Room capacity is a separate, always-on constraint.
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 2]])
        ->students(6, yearLevel: 4);

    $result = solve($b);

    expect(array_filter($result->placementMap()))->toHaveCount(2);
});

it('never places a student in a different year level', function () {
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 10]])
        ->student('Ali', yearLevel: 3);

    $result = solve($b);

    expect($result->placementMap()[$b->studentId('Ali')])->toBeNull()
        ->and($result->items[0]->action)->toBe(ItemAction::Unplaceable);
});

// ---------------------------------------------------------------- siblings

it('separates siblings when the policy is apart', function () {
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 5], '4 BETA' => ['capacity' => 5]])
        ->family('Abdullah')
        ->student('Ahmad', family: 'Abdullah')
        ->student('Aminah', gender: Gender::Perempuan, family: 'Abdullah')
        ->rule(SiblingPolicyRule::key(), weight: 100, config: ['default_policy' => 'apart']);

    $p = placements($b, solve($b));

    expect($p['Ahmad'])->not->toBe($p['Aminah']);
});

it('keeps siblings together when the family overrides to together', function () {
    // The inverse requirement, asserted directly: some parents specifically ask for
    // their children to share a class, and that wish beats the global default.
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 5], '4 BETA' => ['capacity' => 5]])
        ->family('Abdullah', SiblingPolicy::Together)
        ->student('Ahmad', family: 'Abdullah')
        ->student('Aminah', gender: Gender::Perempuan, family: 'Abdullah')
        ->rule(SiblingPolicyRule::key(), weight: 100, config: [
            'default_policy' => 'apart',
            'allow_family_override' => true,
        ]);

    $p = placements($b, solve($b));

    expect($p['Ahmad'])->toBe($p['Aminah']);
});

it('ignores the family override when allow_family_override is false', function () {
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 5], '4 BETA' => ['capacity' => 5]])
        ->family('Abdullah', SiblingPolicy::Together)
        ->student('Ahmad', family: 'Abdullah')
        ->student('Aminah', gender: Gender::Perempuan, family: 'Abdullah')
        ->rule(SiblingPolicyRule::key(), weight: 100, config: [
            'default_policy' => 'apart',
            'allow_family_override' => false,
        ]);

    $p = placements($b, solve($b));

    expect($p['Ahmad'])->not->toBe($p['Aminah']);
});

it('places siblings together when separating them is impossible and reports the violation', function () {
    // The trade-off must be surfaced, not swallowed.
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 3]])
        ->family('Abdullah')
        ->student('Ahmad', family: 'Abdullah')
        ->student('Aminah', gender: Gender::Perempuan, family: 'Abdullah')
        ->student('Amir', family: 'Abdullah')
        ->rule(SiblingPolicyRule::key(), weight: 100, config: ['default_policy' => 'apart']);

    $result = solve($b);

    expect(array_filter($result->placementMap()))->toHaveCount(3);

    $violated = collect($result->items)
        ->flatMap(fn ($i) => array_map(fn ($v) => $v->code, $i->violations))
        ->filter(fn (string $code) => $code === 'asing_gagal');

    expect($violated)->not->toBeEmpty();
});

it('treats a student with no family as having no siblings', function () {
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 5], '4 BETA' => ['capacity' => 5]])
        ->student('Ali')
        ->student('Abu')
        ->rule(SiblingPolicyRule::key(), weight: 100);

    $result = solve($b);

    expect(array_filter($result->placementMap()))->toHaveCount(2);
});

// ---------------------------------------------------------------- behaviour

it('places a problem student with the firmest available teacher', function () {
    $b = SnapshotBuilder::make()
        ->classes([
            '4 ALPHA' => ['capacity' => 5, 'firmness' => 5],
            '4 BETA' => ['capacity' => 5, 'firmness' => 1],
        ])
        ->student('Bad Boy', behaviour: BehaviourLevel::Bermasalah)
        ->rule(FirmTeacherForBehaviourRule::key(), weight: 100);

    $p = placements($b, solve($b));

    expect($p['Bad Boy'])->toBe('4 ALPHA');
});

it('prioritises the more severe student when firm teachers are scarce', function () {
    // Impossible to express with a boolean flag: with only one firm seat going, the
    // engine must know that Kritikal outranks Bermasalah. This is the 0-3 severity
    // scale justified as an executable assertion.
    $b = SnapshotBuilder::make()
        ->classes([
            '4 ALPHA' => ['capacity' => 1, 'firmness' => 5],
            '4 BETA' => ['capacity' => 5, 'firmness' => 1],
        ])
        ->student('Sederhana', behaviour: BehaviourLevel::Bermasalah)
        ->student('Teruk', behaviour: BehaviourLevel::Kritikal)
        ->rule(FirmTeacherForBehaviourRule::key(), weight: 100, config: [
            'min_behaviour_level' => 2,
            'min_firmness' => 4,
            'spread_across_classes' => false,
        ]);

    $p = placements($b, solve($b));

    expect($p['Teruk'])->toBe('4 ALPHA')
        ->and($p['Sederhana'])->toBe('4 BETA');
});

it('does not penalise a well behaved student in a gentle teachers class', function () {
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 5, 'firmness' => 1]])
        ->student('Baik')
        ->rule(FirmTeacherForBehaviourRule::key(), weight: 100);

    $result = solve($b);

    expect($result->items[0]->score)->toBe(0.0);
});

it('handles a class with no teacher assigned', function () {
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 5, 'firmness' => null]])
        ->student('Bad Boy', behaviour: BehaviourLevel::Kritikal)
        ->rule(FirmTeacherForBehaviourRule::key(), weight: 100);

    $p = placements($b, solve($b));

    expect($p['Bad Boy'])->toBe('4 ALPHA');
});

// ---------------------------------------------------------------- gender balance

it('spreads genders evenly when all else is equal', function () {
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 4], '4 BETA' => ['capacity' => 4]])
        ->students(4, yearLevel: 4, gender: Gender::Lelaki, prefix: 'L')
        ->students(4, yearLevel: 4, gender: Gender::Perempuan, prefix: 'P')
        ->rule(GenderBalanceRule::key(), weight: 100);

    $result = solve($b);
    $byClass = [];

    foreach ($result->items as $item) {
        $byClass[$b->className($item->toClassId)][] = $b->studentName($item->studentId);
    }

    foreach ($byClass as $students) {
        $males = count(array_filter($students, fn ($n) => str_starts_with($n, 'L')));
        expect($males)->toBe(2);
    }
});

// ---------------------------------------------------------------- determinism

it('produces an identical result across ten runs', function () {
    $build = fn () => SnapshotBuilder::make()
        ->classes([
            '4 ALPHA' => ['capacity' => 6, 'firmness' => 5],
            '4 BETA' => ['capacity' => 6, 'firmness' => 2],
            '4 GAMMA' => ['capacity' => 6, 'firmness' => 3],
        ])
        ->family('Abdullah')
        ->student('Ahmad', family: 'Abdullah')
        ->student('Aminah', gender: Gender::Perempuan, family: 'Abdullah')
        ->students(10, yearLevel: 4)
        ->student('Teruk', behaviour: BehaviourLevel::Kritikal)
        ->rule(SiblingPolicyRule::key(), weight: 80)
        ->rule(FirmTeacherForBehaviourRule::key(), weight: 90)
        ->rule(GenderBalanceRule::key(), weight: 40);

    $first = solve($build())->placementMap();

    for ($i = 0; $i < 9; $i++) {
        expect(solve($build())->placementMap())->toBe($first);
    }
});

it('breaks a perfect tie by lowest class id', function () {
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 5], '4 BETA' => ['capacity' => 5]])
        ->student('Ali');

    $p = placements($b, solve($b));

    expect($p['Ali'])->toBe('4 ALPHA');
});

// ---------------------------------------------------------------- idempotency

it('produces no moves when re-run on an already solved state', function () {
    // The headline guarantee: pressing the button twice must not reshuffle the school.
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 5], '4 BETA' => ['capacity' => 5]])
        ->student('Ali')->student('Abu')->student('Ana', gender: Gender::Perempuan)
        ->placed('Ali', '4 ALPHA')->placed('Abu', '4 BETA')->placed('Ana', '4 ALPHA')
        ->rule(GenderBalanceRule::key(), weight: 100)
        ->rule(SiblingPolicyRule::key(), weight: 100);

    $result = solve($b);
    $stats = $result->stats();

    expect($stats[ItemAction::Keep->value])->toBe(3)
        ->and($stats[ItemAction::Move->value])->toBe(0)
        ->and($stats[ItemAction::Place->value])->toBe(0);
});

it('does not shuffle existing students when one new student is added', function () {
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 5], '4 BETA' => ['capacity' => 5]])
        ->student('Ali')->student('Abu')
        ->placed('Ali', '4 ALPHA')->placed('Abu', '4 BETA')
        ->student('Baru')
        ->rule(GenderBalanceRule::key(), weight: 60);

    $stats = solve($b, SolveMode::Rebalance)->stats();

    expect($stats[ItemAction::Place->value])->toBe(1)
        ->and($stats[ItemAction::Move->value])->toBe(0);
});

it('never moves a pinned student even in rebalance mode', function () {
    $b = SnapshotBuilder::make()
        ->classes([
            '4 ALPHA' => ['capacity' => 5, 'firmness' => 1],
            '4 BETA' => ['capacity' => 5, 'firmness' => 5],
        ])
        ->student('Degil', behaviour: BehaviourLevel::Kritikal)
        ->placed('Degil', '4 ALPHA', pinned: true)
        ->rule(FirmTeacherForBehaviourRule::key(), weight: 100);

    $result = solve($b, SolveMode::Rebalance);
    $p = placements($b, $result);

    expect($p['Degil'])->toBe('4 ALPHA')
        ->and($result->items[0]->action)->toBe(ItemAction::Pinned);
});

// ---------------------------------------------------------------- degenerate input

it('reports unplaceable students instead of crashing when every class is full', function () {
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 1]])
        ->student('Ali')->student('Abu');

    $result = solve($b);
    $unplaceable = collect($result->items)->firstWhere('action', ItemAction::Unplaceable);

    expect($unplaceable)->not->toBeNull()
        ->and($unplaceable->toClassId)->toBeNull()
        ->and($unplaceable->violations)->not->toBeEmpty();
});

it('places nobody and reports everyone when there are no classes at all', function () {
    $b = SnapshotBuilder::make()->students(3, yearLevel: 4);

    $result = solve($b);

    expect($result->stats()[ItemAction::Unplaceable->value])->toBe(3)
        ->and($result->items[0]->violations)->not->toBeEmpty();
});

it('still places everyone deterministically when there are zero rules', function () {
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 3], '4 BETA' => ['capacity' => 3]])
        ->students(4, yearLevel: 4);

    $result = solve($b);

    expect(array_filter($result->placementMap()))->toHaveCount(4);
});
