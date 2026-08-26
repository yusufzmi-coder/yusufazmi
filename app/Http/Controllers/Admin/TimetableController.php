<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Support\TimetableGrid;
use Inertia\Inertia;
use Inertia\Response;

class TimetableController
{
    public function index(TimetableGrid $grid): Response
    {
        $session = AcademicSession::active();

        return Inertia::render('jadual/index', [
            'grid' => $grid->build($session->id),
            'sesi' => $session->name,
        ]);
    }

    /** The roster panel that opens when a grid cell is clicked. */
    public function show(SchoolClass $class): Response
    {
        $class->load(['teacher:id,name,firmness', 'meetings.room:id,name,capacity', 'meetings.timeSlot']);

        return Inertia::render('jadual/kelas', [
            'kelas' => [
                'id' => $class->id,
                'nama' => $class->name,
                'tahun' => $class->year_level,
                'stream' => $class->stream,
                'guru' => $class->teacher?->name,
                'ketegasan' => $class->teacher?->firmness,
                'kapasiti' => $class->effectiveCapacity(),
                'pertemuan' => $class->meetings->map(fn ($m): array => [
                    'hari' => $m->day->label(),
                    'slot' => $m->timeSlot?->label,
                    'bilik' => $m->room?->name,
                ])->all(),
            ],
            'pelajar' => $class->activeEnrolments()
                ->with('student:id,name,student_code,gender,behaviour_level,year_level')
                ->get()
                ->map(fn ($e): array => [
                    'enrolment_id' => $e->id,
                    'id' => $e->student->id,
                    'nama' => $e->student->name,
                    'kod' => $e->student->student_code,
                    'jantina' => $e->student->gender->value,
                    'tingkah_laku' => $e->student->behaviour_level->label(),
                    'disemat' => $e->is_pinned,
                    'sebab' => $e->placement_reason_text,
                ])
                ->all(),

            // Everything the roster panel needs to move someone without a page change.
            'boleh_tambah' => EnrolmentController::eligibleFor($class),
            'kelas_lain' => SchoolClass::query()
                ->where('session_id', $class->session_id)
                ->where('year_level', $class->year_level)
                ->where('is_active', true)
                ->whereKeyNot($class->id)
                ->with('meetings.room:id,capacity')
                ->orderBy('stream')
                ->get()
                ->map(fn (SchoolClass $c): array => [
                    'id' => $c->id,
                    'nama' => $c->name,
                    'kapasiti' => $c->effectiveCapacity(),
                    'pelajar' => $c->activeEnrolments()->count(),
                ])
                ->all(),
        ]);
    }
}
