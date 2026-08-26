<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ClassMeeting;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;

/**
 * The one place that writes class_meetings.
 *
 * `session_id` and `teacher_id` are copied down from the parent class so the two
 * partial unique indexes that enforce room and teacher clashes can exist -- a
 * partial index cannot reach across a foreign key. Everything funnels through here
 * so those copies cannot drift.
 */
final class ClassMeetingWriter
{
    /**
     * Replaces the class's whole weekly schedule.
     *
     * @param  list<array{day: string, time_slot_id: int, room_id: int|null}>  $meetings
     */
    public function sync(SchoolClass $class, array $meetings): void
    {
        DB::transaction(function () use ($class, $meetings): void {
            $class->meetings()->delete();

            foreach ($meetings as $meeting) {
                ClassMeeting::create([
                    'class_id' => $class->id,
                    'session_id' => $class->session_id,
                    'teacher_id' => $class->teacher_id,
                    'room_id' => $meeting['room_id'] ?? null,
                    'day' => $meeting['day'],
                    'time_slot_id' => $meeting['time_slot_id'],
                ]);
            }
        });
    }

    /** Called after the class's teacher changes, so the clash index stays truthful. */
    public function refreshDenormalisedColumns(SchoolClass $class): void
    {
        $class->meetings()->update([
            'teacher_id' => $class->teacher_id,
            'session_id' => $class->session_id,
            'updated_at' => now(),
        ]);
    }
}
