<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Services;

use App\Domain\Assignment\Dto\Snapshot;
use App\Domain\Assignment\Enums\SolveMode;

/**
 * A fingerprint of everything the solve depended on.
 *
 * The commit step re-hashes and compares: if another admin added a student, deleted
 * a class or moved a weight slider between preview and confirm, the draft is refused
 * with a clear message instead of being applied against stale assumptions.
 */
final class SnapshotHasher
{
    public function hash(Snapshot $snapshot, SolveMode $mode): string
    {
        $canonical = [
            'mode' => $mode->value,
            'students' => array_map(static fn ($s): array => [
                $s->id, $s->yearLevel, $s->gender->value, $s->behaviourLevel->value,
                $s->familyId, $s->familyPolicy->value,
            ], $snapshot->students),
            'classes' => array_map(static fn ($c): array => [
                $c->id, $c->yearLevel, $c->capacity, $c->teacherFirmness,
            ], $snapshot->classes),
            'placements' => array_map(static fn ($p): array => [
                $p->studentId, $p->classId, $p->isPinned,
            ], $snapshot->existingPlacements),
            'rules' => array_map(static fn ($r): array => [
                $r->ruleId, $r->ruleType, $r->weight, $r->data, $r->appliesTo,
            ], $snapshot->rules),
        ];

        return hash('sha256', json_encode($canonical, JSON_THROW_ON_ERROR));
    }
}
