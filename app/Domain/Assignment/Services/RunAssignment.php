<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Services;

use App\Domain\Assignment\AssignmentSolver;
use App\Domain\Assignment\Dto\ResultItem;
use App\Domain\Assignment\Enums\RunStatus;
use App\Domain\Assignment\Enums\SolveMode;
use App\Domain\Assignment\ReasonRenderer;
use App\Models\AssignmentRun;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Produces a DRAFT. Nothing is written to `enrolments` here -- the admin reads the
 * preview and confirms first.
 *
 * Runs synchronously: ~250 students x ~3 candidate classes x ~5 rules is a few
 * thousand scoring calls, comfortably inside a request. Queueing it would buy a job
 * table, a spinner and a new failure surface in exchange for nothing. The seam is
 * still here though -- this is a plain service, so wrapping it in a queued job is a
 * one-line change once `sync_max_students` is exceeded.
 */
final readonly class RunAssignment
{
    public function __construct(
        private SnapshotReader $reader,
        private SnapshotHasher $hasher,
        private AssignmentSolver $solver,
        private ReasonRenderer $renderer,
    ) {}

    public function __invoke(int $sessionId, SolveMode $mode, int $userId): AssignmentRun
    {
        $lockKey = "assignment:{$sessionId}";

        // Non-blocking: a second admin pressing the button gets an immediate, friendly
        // refusal rather than a competing draft.
        $acquired = (bool) DB::selectOne(
            'SELECT pg_try_advisory_lock(hashtextextended(?, 0)) AS locked',
            [$lockKey],
        )->locked;

        if (! $acquired) {
            throw new RuntimeException('Satu proses agihan sedang berjalan. Sila cuba sebentar lagi.');
        }

        try {
            $snapshot = $this->reader->read($sessionId);
            $hash = $this->hasher->hash($snapshot, $mode);

            $result = $this->solver->solve($snapshot, $mode);

            $classNames = [];
            foreach ($snapshot->classes as $class) {
                $classNames[$class->id] = $class->name;
            }

            return DB::transaction(function () use ($sessionId, $mode, $userId, $snapshot, $hash, $result, $classNames): AssignmentRun {
                $run = AssignmentRun::create([
                    'session_id' => $sessionId,
                    'mode' => $mode->value,
                    'status' => RunStatus::Draft->value,
                    'input_hash' => $hash,
                    'rules_snapshot' => array_map(static fn ($r): array => [
                        'id' => $r->ruleId,
                        'rule_type' => $r->ruleType,
                        'weight' => $r->weight,
                        'config' => $r->data,
                    ], $snapshot->rules),
                    'objective_score' => $result->objective,
                    'stats' => $result->stats(),
                    'duration_ms' => $result->durationMs,
                    'created_by' => $userId,
                ]);

                $rows = array_map(
                    fn (ResultItem $item): array => [
                        'assignment_run_id' => $run->id,
                        'student_id' => $item->studentId,
                        'from_class_id' => $item->fromClassId,
                        'to_class_id' => $item->toClassId,
                        'action' => $item->action->value,
                        'score' => $item->score,
                        'reasons' => json_encode(array_map(static fn ($r): array => $r->toArray(), $item->reasons)),
                        'reason_text' => $this->renderer->render(
                            $item,
                            $classNames[$item->toClassId] ?? null,
                            $classNames[$item->fromClassId] ?? null,
                        ),
                        'violations' => json_encode(array_map(static fn ($r): array => $r->toArray(), $item->violations)),
                        'rank' => $item->rank,
                        'created_at' => now(),
                    ],
                    $result->items,
                );

                foreach (array_chunk($rows, 500) as $chunk) {
                    DB::table('assignment_run_items')->insert($chunk);
                }

                return $run;
            });
        } finally {
            DB::select('SELECT pg_advisory_unlock(hashtextextended(?, 0))', [$lockKey]);
        }
    }
}
