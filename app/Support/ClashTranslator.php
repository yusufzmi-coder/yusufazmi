<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\ClassMeeting;
use App\Models\Room;
use App\Models\Teacher;
use App\Models\TimeSlot;
use Illuminate\Database\QueryException;

/**
 * Room and teacher clashes are enforced by two partial unique indexes, not by
 * application code. That is the right place for the guarantee, but a raw 23505 is
 * useless to an admin -- this turns it into a sentence naming the actual conflict.
 */
final class ClashTranslator
{
    public static function isUniqueViolation(QueryException $e): bool
    {
        return $e->getCode() === '23505';
    }

    /**
     * @param  list<array{day: string, time_slot_id: int, room_id: int|null}>  $meetings
     */
    public static function explain(QueryException $e, int $sessionId, ?int $teacherId, array $meetings, ?int $ignoreClassId = null): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'class_meetings_room_clash_idx')) {
            return self::describe($sessionId, $meetings, $ignoreClassId, byRoom: true)
                ?? 'Bilik itu sudah digunakan pada waktu tersebut.';
        }

        if (str_contains($message, 'class_meetings_teacher_clash_idx')) {
            return self::describe($sessionId, $meetings, $ignoreClassId, byRoom: false, teacherId: $teacherId)
                ?? 'Guru itu sudah mengajar pada waktu tersebut.';
        }

        if (str_contains($message, 'classes_session_year_stream_unique')) {
            return 'Kelas untuk tahun dan aliran ini sudah wujud.';
        }

        return 'Data ini bertindih dengan rekod sedia ada.';
    }

    /** @param list<array{day: string, time_slot_id: int, room_id: int|null}> $meetings */
    private static function describe(int $sessionId, array $meetings, ?int $ignoreClassId, bool $byRoom, ?int $teacherId = null): ?string
    {
        foreach ($meetings as $meeting) {
            $query = ClassMeeting::query()
                ->where('session_id', $sessionId)
                ->where('day', $meeting['day'])
                ->where('time_slot_id', $meeting['time_slot_id'])
                ->when($ignoreClassId, fn ($q) => $q->where('class_id', '!=', $ignoreClassId));

            $query = $byRoom
                ? $query->where('room_id', $meeting['room_id'])
                : $query->where('teacher_id', $teacherId);

            $existing = $query->with('schoolClass:id,year_level,stream,name')->first();

            if ($existing === null) {
                continue;
            }

            $slot = TimeSlot::find($meeting['time_slot_id']);
            $when = ucfirst($meeting['day']).' '.($slot?->label ?? '');

            if ($byRoom) {
                $room = Room::find($meeting['room_id']);

                return sprintf(
                    '%s sudah digunakan oleh kelas %s pada %s.',
                    $room?->name ?? 'Bilik itu',
                    $existing->schoolClass->name,
                    trim($when),
                );
            }

            $teacher = Teacher::find($teacherId);

            return sprintf(
                '%s sudah mengajar kelas %s pada %s.',
                $teacher?->name ?? 'Guru itu',
                $existing->schoolClass->name,
                trim($when),
            );
        }

        return null;
    }
}
