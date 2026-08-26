<?php

declare(strict_types=1);

namespace App\Domain\Enrolment;

use App\Enums\EnrolmentSource;
use App\Enums\EnrolmentStatus;
use App\Models\Enrolment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

/**
 * The single place a student is seated or unseated.
 *
 * A move is never an UPDATE of class_id: the old row is closed and a new one opened,
 * so the history of every placement survives and "why is Ali in 5 BETA" stays
 * answerable. Both the auto-assign commit and the manual buttons go through here, so
 * neither can drift from that rule.
 */
final class EnrolmentManager
{
    /**
     * @param  array<int, mixed>|null  $reason  structured Reason[] from the engine
     */
    public function place(
        Student $student,
        SchoolClass $class,
        EnrolmentSource $source,
        ?array $reason = null,
        ?string $reasonText = null,
        ?float $score = null,
        ?int $runId = null,
        bool $pinned = false,
    ): Enrolment {
        return DB::transaction(function () use ($student, $class, $source, $reason, $reasonText, $score, $runId, $pinned): Enrolment {
            $this->closeActive($student, $class->session_id, EnrolmentStatus::Moved);

            return Enrolment::create([
                'session_id' => $class->session_id,
                'student_id' => $student->id,
                'class_id' => $class->id,
                'status' => EnrolmentStatus::Active->value,
                'is_pinned' => $pinned,
                'source' => $source->value,
                'assignment_run_id' => $runId,
                'placement_reason' => $reason,
                'placement_reason_text' => $reasonText,
                'placement_score' => $score,
                'joined_at' => now(),
            ]);
        });
    }

    /** Takes the student out of their class without deleting the history. */
    public function withdraw(Student $student, int $sessionId): bool
    {
        return $this->closeActive($student, $sessionId, EnrolmentStatus::Withdrawn) > 0;
    }

    /** Pinned students consume capacity and count toward balance, but never move. */
    public function setPinned(Enrolment $enrolment, bool $pinned): void
    {
        $enrolment->update(['is_pinned' => $pinned]);
    }

    /**
     * Why this student cannot go into this class, or null if they can.
     * Mirrors the engine's non-negotiable constraints so the manual path cannot
     * create a state the solver would consider impossible.
     */
    public function rejectionReason(Student $student, SchoolClass $class): ?string
    {
        if ($student->year_level !== $class->year_level) {
            return sprintf(
                '%s berada dalam Tahun %d, tetapi %s ialah kelas Tahun %d.',
                $student->name,
                $student->year_level,
                $class->name,
                $class->year_level,
            );
        }

        if (! $class->is_active) {
            return "Kelas {$class->name} tidak aktif.";
        }

        $capacity = $class->effectiveCapacity();
        $current = $class->activeEnrolments()->count();

        if ($capacity > 0 && $current >= $capacity) {
            return "Kelas {$class->name} sudah penuh ({$current}/{$capacity}).";
        }

        if ($capacity === 0) {
            return "Kelas {$class->name} belum ada bilik, jadi kapasitinya tidak diketahui.";
        }

        return null;
    }

    private function closeActive(Student $student, int $sessionId, EnrolmentStatus $status): int
    {
        return Enrolment::query()
            ->where('session_id', $sessionId)
            ->where('student_id', $student->id)
            ->where('status', EnrolmentStatus::Active->value)
            ->update([
                'status' => $status->value,
                'left_at' => now(),
                'updated_at' => now(),
            ]);
    }
}
