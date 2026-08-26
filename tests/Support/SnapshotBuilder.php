<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Domain\Assignment\Dto\ClassSnapshot;
use App\Domain\Assignment\Dto\ExistingPlacement;
use App\Domain\Assignment\Dto\RuleConfig;
use App\Domain\Assignment\Dto\Snapshot;
use App\Domain\Assignment\Dto\StudentSnapshot;
use App\Domain\Assignment\Enums\RuleKind;
use App\Domain\Assignment\RuleTypeRegistry;
use App\Enums\BehaviourLevel;
use App\Enums\Gender;
use App\Enums\SiblingPolicy;

/**
 * Builds Snapshot objects by hand so engine tests never touch the database.
 * Highest-leverage piece of test infrastructure here: it turns every awkward
 * scenario into three readable lines.
 */
final class SnapshotBuilder
{
    /** @var list<StudentSnapshot> */
    private array $students = [];

    /** @var list<ClassSnapshot> */
    private array $classes = [];

    /** @var list<ExistingPlacement> */
    private array $placements = [];

    /** @var list<RuleConfig> */
    private array $rules = [];

    /** @var array<string, int> */
    private array $classIds = [];

    /** @var array<string, int> */
    private array $studentIds = [];

    /** @var array<string, array{id: int, policy: SiblingPolicy}> */
    private array $families = [];

    private int $nextStudentId = 1;

    private int $nextClassId = 1;

    private int $nextFamilyId = 1;

    private int $nextRuleId = 1;

    public static function make(): self
    {
        return new self;
    }

    /**
     * @param  array<string, array{capacity?: int, firmness?: int|null, teacher?: string|null, year?: int}>  $classes
     */
    public function classes(array $classes): self
    {
        foreach ($classes as $name => $spec) {
            $this->addClass($name, $spec);
        }

        return $this;
    }

    /** @param array{capacity?: int, firmness?: int|null, teacher?: string|null, year?: int} $spec */
    public function addClass(string $name, array $spec = []): self
    {
        $year = $spec['year'] ?? (int) (preg_match('/^(\d+)/', $name, $m) ? $m[1] : 1);
        $firmness = array_key_exists('firmness', $spec) ? $spec['firmness'] : 3;

        $id = $this->nextClassId++;
        $this->classIds[$name] = $id;

        $this->classes[] = new ClassSnapshot(
            id: $id,
            name: $name,
            yearLevel: $year,
            capacity: $spec['capacity'] ?? 30,
            teacherId: $firmness === null ? null : $id,
            teacherName: $spec['teacher'] ?? ($firmness === null ? null : "Cikgu {$name}"),
            teacherFirmness: $firmness,
        );

        return $this;
    }

    public function family(string $name, SiblingPolicy $policy = SiblingPolicy::Inherit): self
    {
        $this->families[$name] = ['id' => $this->nextFamilyId++, 'policy' => $policy];

        return $this;
    }

    public function student(
        string $name,
        int $yearLevel = 4,
        Gender $gender = Gender::Lelaki,
        BehaviourLevel $behaviour = BehaviourLevel::Normal,
        ?string $family = null,
    ): self {
        $id = $this->nextStudentId++;
        $this->studentIds[$name] = $id;

        $this->students[] = new StudentSnapshot(
            id: $id,
            name: $name,
            yearLevel: $yearLevel,
            gender: $gender,
            behaviourLevel: $behaviour,
            familyId: $family === null ? null : $this->familyId($family),
            familyPolicy: $family === null ? SiblingPolicy::Inherit : $this->families[$family]['policy'],
        );

        return $this;
    }

    /** Adds `count` students with generated names, alternating gender by default. */
    public function students(
        int $count,
        int $yearLevel = 4,
        ?string $family = null,
        ?Gender $gender = null,
        BehaviourLevel $behaviour = BehaviourLevel::Normal,
        string $prefix = 'Pelajar',
    ): self {
        for ($i = 1; $i <= $count; $i++) {
            $this->student(
                name: sprintf('%s %d', $prefix, $this->nextStudentId),
                yearLevel: $yearLevel,
                gender: $gender ?? ($i % 2 === 1 ? Gender::Lelaki : Gender::Perempuan),
                behaviour: $behaviour,
                family: $family,
            );
        }

        return $this;
    }

    /** @param array<string, mixed> $config */
    public function rule(string $ruleType, int $weight = 50, array $config = [], array $appliesTo = []): self
    {
        /** @var RuleTypeRegistry $registry */
        $registry = app(RuleTypeRegistry::class);
        $type = $registry->get($ruleType);

        $this->rules[] = new RuleConfig(
            ruleId: $this->nextRuleId++,
            ruleType: $ruleType,
            kind: $type->kind(),
            weight: $weight,
            data: $config === [] ? $type->defaultConfig() : $config,
            appliesTo: $appliesTo,
        );

        return $this;
    }

    public function rawRule(string $ruleType, RuleKind $kind, int $weight, array $config = []): self
    {
        $this->rules[] = new RuleConfig($this->nextRuleId++, $ruleType, $kind, $weight, $config);

        return $this;
    }

    public function placed(string $student, string $class, bool $pinned = false): self
    {
        $this->placements[] = new ExistingPlacement(
            studentId: $this->studentId($student),
            classId: $this->classId($class),
            isPinned: $pinned,
        );

        return $this;
    }

    public function build(): Snapshot
    {
        return new Snapshot(
            students: $this->students,
            classes: $this->classes,
            existingPlacements: $this->placements,
            rules: $this->rules,
            sessionId: 1,
        );
    }

    public function classId(string $name): int
    {
        return $this->classIds[$name] ?? throw new \InvalidArgumentException("Kelas tidak wujud: {$name}");
    }

    public function studentId(string $name): int
    {
        return $this->studentIds[$name] ?? throw new \InvalidArgumentException("Pelajar tidak wujud: {$name}");
    }

    public function className(int $id): string
    {
        return array_search($id, $this->classIds, strict: true) ?: '-';
    }

    public function studentName(int $id): string
    {
        return array_search($id, $this->studentIds, strict: true) ?: '-';
    }

    private function familyId(string $name): int
    {
        if (! isset($this->families[$name])) {
            $this->family($name);
        }

        return $this->families[$name]['id'];
    }
}
