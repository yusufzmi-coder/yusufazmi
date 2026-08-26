<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\Day;
use App\Enums\EnrolmentStatus;
use App\Models\ClassMeeting;
use App\Models\Enrolment;
use App\Models\TimeSlot;

/**
 * Builds the weekly grid the timetable screen renders: rows are time slots, columns
 * are the five school days, and each cell holds at most one class.
 */
final class TimetableGrid
{
    /** @return array{days: list<array{value: string, label: string}>, rows: list<array<string, mixed>>} */
    public function build(int $sessionId): array
    {
        $slots = TimeSlot::orderBy('sort_order')->get();

        $meetings = ClassMeeting::query()
            ->where('session_id', $sessionId)
            ->with([
                'schoolClass:id,year_level,stream,teacher_id,name',
                'schoolClass.teacher:id,name,firmness',
                'room:id,name,capacity',
            ])
            ->withCount([])
            ->get();

        // Headcount per class in one query rather than N.
        $counts = Enrolment::query()
            ->where('status', EnrolmentStatus::Active->value)
            ->selectRaw('class_id, count(*) as jumlah')
            ->groupBy('class_id')
            ->pluck('jumlah', 'class_id');

        $indexed = [];
        foreach ($meetings as $meeting) {
            $indexed[$meeting->time_slot_id][$meeting->day->value] = $meeting;
        }

        $rows = [];

        foreach ($slots as $slot) {
            $cells = [];

            foreach (Day::schoolWeek() as $day) {
                $meeting = $indexed[$slot->id][$day->value] ?? null;

                $cells[] = $meeting === null
                    ? ['day' => $day->value, 'kelas' => null]
                    : [
                        'day' => $day->value,
                        'kelas' => [
                            'id' => $meeting->schoolClass->id,
                            'nama' => $meeting->schoolClass->name,
                            'stream' => $meeting->schoolClass->stream,
                            'guru' => $meeting->schoolClass->teacher?->name,
                            'bilik' => $meeting->room?->name,
                            'pelajar' => (int) ($counts[$meeting->schoolClass->id] ?? 0),
                            'kapasiti' => $meeting->room?->capacity ?? 0,
                        ],
                    ];
            }

            $rows[] = [
                'slot_id' => $slot->id,
                'label' => $slot->label,
                'is_break' => $slot->is_break,
                'cells' => $cells,
            ];
        }

        return [
            'days' => array_map(
                static fn (Day $d): array => ['value' => $d->value, 'label' => $d->label()],
                Day::schoolWeek(),
            ),
            'rows' => $rows,
        ];
    }
}
