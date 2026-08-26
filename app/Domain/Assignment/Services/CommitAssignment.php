<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Services;

use App\Domain\Assignment\Enums\ItemAction;
use App\Domain\Assignment\Enums\RunStatus;
use App\Domain\Enrolment\EnrolmentManager;
use App\Enums\EnrolmentSource;
use App\Models\AssignmentRun;
use App\Models\AssignmentRunItem;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final readonly class CommitAssignment
{
    public function __construct(
        private SnapshotReader $reader,
        private SnapshotHasher $hasher,
        private EnrolmentManager $enrolments,
    ) {}

    /**
     * @param  list<int>  $excludedItemIds  rows the admin unticked in the preview
     * @return array{applied: int, skipped: int}
     */
    public function __invoke(AssignmentRun $run, int $userId, array $excludedItemIds = []): array
    {
        // The expiry check runs BEFORE the transaction on purpose: marking the draft
        // expired is a write we want to keep, and throwing from inside the
        // transaction would roll that write straight back out again.
        $this->guardNotExpired($run);

        return DB::transaction(function () use ($run, $userId, $excludedItemIds): array {
            // Blocking, auto-released with the transaction.
            DB::select('SELECT pg_advisory_xact_lock(hashtextextended(?, 0))', ["assignment:{$run->session_id}"]);

            $run->refresh();

            if ($run->status !== RunStatus::Draft) {
                throw new RuntimeException('Cadangan ini sudah tidak sah lagi.');
            }

            // Optimistic concurrency: the world must not have moved under us.
            $current = $this->hasher->hash($this->reader->read($run->session_id), $run->mode);

            if ($current !== $run->input_hash) {
                throw new RuntimeException('Data telah berubah sejak cadangan ini dijana. Sila jalankan semula.');
            }

            $applied = 0;
            $skipped = 0;

            $items = $run->items()
                ->whereIn('action', [ItemAction::Place->value, ItemAction::Move->value])
                ->with('student')
                ->orderBy('student_id')
                ->get();

            foreach ($items as $item) {
                if (in_array($item->id, $excludedItemIds, strict: true)) {
                    $skipped++;

                    continue;
                }

                $this->apply($run, $item);
                $applied++;
            }

            $run->update([
                'status' => RunStatus::Committed->value,
                'committed_at' => now(),
                'committed_by' => $userId,
            ]);

            return ['applied' => $applied, 'skipped' => $skipped];
        });
    }

    private function guardNotExpired(AssignmentRun $run): void
    {
        $ttl = (int) config('assignment.solver.draft_ttl_minutes', 30);

        if ($run->status !== RunStatus::Draft || $run->created_at->gte(now()->subMinutes($ttl))) {
            return;
        }

        $run->update(['status' => RunStatus::Expired->value]);

        throw new RuntimeException("Cadangan ini melebihi {$ttl} minit. Sila jalankan semula.");
    }

    private function apply(AssignmentRun $run, AssignmentRunItem $item): void
    {
        $this->enrolments->place(
            student: $item->student,
            class: SchoolClass::whereKey($item->to_class_id)->firstOrFail(),
            source: EnrolmentSource::Auto,
            reason: $item->reasons,
            reasonText: $item->reason_text,
            score: $item->score,
            runId: $run->id,
        );
    }
}
