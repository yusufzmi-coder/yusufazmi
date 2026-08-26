<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Services;

use App\Domain\Assignment\Enums\RunStatus;
use App\Enums\EnrolmentStatus;
use App\Models\AssignmentRun;
use App\Models\Enrolment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Undoes a committed run by deleting the enrolments it created and reopening the
 * ones it closed.
 *
 * Only the most recent committed run can be undone. Rolling back an older one would
 * mean reasoning about every change layered on top of it, and the honest answer to
 * "which placement should come back?" stops existing once someone has moved a
 * student by hand since.
 */
final readonly class RevertAssignment
{
    /** @return array{dibuang: int, dipulihkan: int} */
    public function __invoke(AssignmentRun $run): array
    {
        return DB::transaction(function () use ($run): array {
            DB::select('SELECT pg_advisory_xact_lock(hashtextextended(?, 0))', ["assignment:{$run->session_id}"]);

            $run->refresh();

            if ($run->status !== RunStatus::Committed) {
                throw new RuntimeException('Hanya larian yang telah disahkan boleh dibuat asal.');
            }

            $newer = AssignmentRun::query()
                ->where('session_id', $run->session_id)
                ->where('status', RunStatus::Committed->value)
                ->where('id', '>', $run->id)
                ->exists();

            if ($newer) {
                throw new RuntimeException('Ada larian lebih baharu yang telah disahkan. Buat asal larian itu dahulu.');
            }

            $created = Enrolment::query()
                ->where('assignment_run_id', $run->id)
                ->get();

            $restored = 0;

            foreach ($created as $enrolment) {
                // Anything touched by hand after the commit is left alone: the admin's
                // deliberate change outranks an automated rollback.
                if ($enrolment->status !== EnrolmentStatus::Active) {
                    continue;
                }

                $previous = Enrolment::query()
                    ->where('session_id', $enrolment->session_id)
                    ->where('student_id', $enrolment->student_id)
                    ->where('status', EnrolmentStatus::Moved->value)
                    ->where('id', '<', $enrolment->id)
                    ->orderByDesc('id')
                    ->first();

                $enrolment->delete();

                if ($previous !== null) {
                    $previous->update([
                        'status' => EnrolmentStatus::Active->value,
                        'left_at' => null,
                    ]);
                    $restored++;
                }
            }

            $run->update(['status' => RunStatus::Reverted->value]);

            return ['dibuang' => $created->count(), 'dipulihkan' => $restored];
        });
    }

    /** Whether the UI should offer an undo button for this run. */
    public function canRevert(AssignmentRun $run): bool
    {
        if ($run->status !== RunStatus::Committed) {
            return false;
        }

        return ! AssignmentRun::query()
            ->where('session_id', $run->session_id)
            ->where('status', RunStatus::Committed->value)
            ->where('id', '>', $run->id)
            ->exists();
    }
}
