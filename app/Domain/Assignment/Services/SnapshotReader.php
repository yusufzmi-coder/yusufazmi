<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Services;

use App\Domain\Assignment\Dto\ClassSnapshot;
use App\Domain\Assignment\Dto\ExistingPlacement;
use App\Domain\Assignment\Dto\RuleConfig;
use App\Domain\Assignment\Dto\Snapshot;
use App\Domain\Assignment\Dto\StudentSnapshot;
use App\Domain\Assignment\RuleTypeRegistry;
use App\Enums\EnrolmentStatus;
use App\Enums\SiblingPolicy;
use App\Models\Enrolment;
use App\Models\Rule;
use App\Models\SchoolClass;
use App\Models\Student;

/** Reads the whole solver input in one pass. The only place the engine touches the database. */
final readonly class SnapshotReader
{
    public function __construct(private RuleTypeRegistry $registry) {}

    public function read(int $sessionId): Snapshot
    {
        return new Snapshot(
            students: $this->students(),
            classes: $this->classes($sessionId),
            existingPlacements: $this->placements($sessionId),
            rules: $this->rules($sessionId),
            sessionId: $sessionId,
        );
    }

    /** @return list<StudentSnapshot> */
    private function students(): array
    {
        return Student::query()
            ->assignable()
            ->with('family:id,sibling_policy')
            ->orderBy('id')
            ->get(['id', 'name', 'year_level', 'gender', 'behaviour_level', 'family_id'])
            ->map(fn (Student $s): StudentSnapshot => new StudentSnapshot(
                id: $s->id,
                name: $s->name,
                yearLevel: $s->year_level,
                gender: $s->gender,
                behaviourLevel: $s->behaviour_level,
                familyId: $s->family_id,
                familyPolicy: $s->family->sibling_policy ?? SiblingPolicy::Inherit,
            ))
            ->values()
            ->all();
    }

    /** @return list<ClassSnapshot> */
    private function classes(int $sessionId): array
    {
        return SchoolClass::query()
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->with(['meetings.room:id,capacity', 'teacher:id,name,firmness'])
            ->orderBy('id')
            ->get()
            ->map(fn (SchoolClass $c): ClassSnapshot => new ClassSnapshot(
                id: $c->id,
                name: $c->name,
                yearLevel: $c->year_level,
                capacity: $c->effectiveCapacity(),
                teacherId: $c->teacher_id,
                teacherName: $c->teacher?->name,
                teacherFirmness: $c->teacher?->firmness,
            ))
            ->values()
            ->all();
    }

    /** @return list<ExistingPlacement> */
    private function placements(int $sessionId): array
    {
        return Enrolment::query()
            ->where('session_id', $sessionId)
            ->where('status', EnrolmentStatus::Active->value)
            ->orderBy('student_id')
            ->get(['student_id', 'class_id', 'is_pinned'])
            ->map(fn (Enrolment $e): ExistingPlacement => new ExistingPlacement(
                studentId: $e->student_id,
                classId: $e->class_id,
                isPinned: $e->is_pinned,
            ))
            ->values()
            ->all();
    }

    /** @return list<RuleConfig> */
    private function rules(int $sessionId): array
    {
        return Rule::query()
            ->active()
            ->where(fn ($q) => $q->where('session_id', $sessionId)->orWhereNull('session_id'))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->filter(fn (Rule $r): bool => $this->registry->has($r->rule_type))
            ->map(fn (Rule $r): RuleConfig => new RuleConfig(
                ruleId: $r->id,
                ruleType: $r->rule_type,
                // Trust the registry over the stored copy: `kind` is denormalised for
                // cheap querying, not a source of truth.
                kind: $this->registry->get($r->rule_type)->kind(),
                weight: $r->weight,
                data: $r->config ?? [],
                appliesTo: $r->applies_to ?? [],
            ))
            ->values()
            ->all();
    }
}
