<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\Day;
use App\Enums\EnrolmentStatus;
use App\Http\Requests\SchoolClassRequest;
use App\Models\AcademicSession;
use App\Models\Room;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\TimeSlot;
use App\Services\ClassMeetingWriter;
use App\Support\ClashTranslator;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SchoolClassController
{
    public function __construct(private readonly ClassMeetingWriter $meetings) {}

    public function index(): Response
    {
        $session = AcademicSession::active();

        return Inertia::render('kelas/index', [
            'kelas' => SchoolClass::query()
                ->where('session_id', $session->id)
                ->with(['teacher:id,name,firmness', 'meetings.room:id,name,capacity', 'meetings.timeSlot:id,label'])
                ->withCount(['enrolments as pelajar' => fn ($q) => $q->where('status', EnrolmentStatus::Active->value)])
                ->orderBy('year_level')
                ->orderBy('stream')
                ->get()
                ->map(fn (SchoolClass $c): array => [
                    'id' => $c->id,
                    'nama' => $c->name,
                    'tahun' => $c->year_level,
                    'stream' => $c->stream,
                    'guru' => $c->teacher?->name,
                    'ketegasan' => $c->teacher?->firmness,
                    'kapasiti' => $c->effectiveCapacity(),
                    'pelajar' => $c->pelajar,
                    'pertemuan' => $c->meetings->map(fn ($m): string => sprintf(
                        '%s %s · %s',
                        $m->day->label(),
                        $m->timeSlot?->label ?? '-',
                        $m->room?->name ?? 'Tiada bilik',
                    ))->all(),
                    'aktif' => $c->is_active,
                ])
                ->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('kelas/form', $this->formData(null));
    }

    public function edit(SchoolClass $class): Response
    {
        $class->load('meetings');

        return Inertia::render('kelas/form', $this->formData($class));
    }

    public function store(SchoolClassRequest $request): RedirectResponse
    {
        $session = AcademicSession::active();
        $data = $request->validated();
        $meetings = $this->normaliseMeetings($data['meetings'] ?? []);

        try {
            $class = DB::transaction(function () use ($data, $session, $meetings): SchoolClass {
                $class = SchoolClass::create([...$this->attributes($data), 'session_id' => $session->id]);
                $this->meetings->sync($class, $meetings);

                return $class;
            });
        } catch (QueryException $e) {
            return $this->clashResponse($e, $session->id, $data['teacher_id'] ?? null, $meetings);
        }

        return to_route('kelas.index')->with('success', "Kelas {$class->name} ditambah.");
    }

    public function update(SchoolClassRequest $request, SchoolClass $class): RedirectResponse
    {
        $session = AcademicSession::active();
        $data = $request->validated();
        $meetings = $this->normaliseMeetings($data['meetings'] ?? []);

        try {
            DB::transaction(function () use ($class, $data, $meetings): void {
                $class->update($this->attributes($data));
                // sync() rewrites the denormalised teacher/session copies that the
                // clash indexes depend on, so a teacher change stays enforceable.
                $this->meetings->sync($class->refresh(), $meetings);
            });
        } catch (QueryException $e) {
            return $this->clashResponse($e, $session->id, $data['teacher_id'] ?? null, $meetings, $class->id);
        }

        return to_route('kelas.index')->with('success', "Kelas {$class->fresh()->name} dikemas kini.");
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $active = $class->activeEnrolments()->count();

        if ($active > 0) {
            return back()->with('error', "Kelas {$class->name} masih ada {$active} pelajar. Pindahkan mereka dahulu.");
        }

        $name = $class->name;
        DB::transaction(function () use ($class): void {
            $class->meetings()->delete();
            $class->delete();
        });

        return to_route('kelas.index')->with('success', "Kelas {$name} dibuang.");
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function attributes(array $data): array
    {
        return [
            'year_level' => $data['year_level'],
            'stream' => $data['stream'],
            'teacher_id' => $data['teacher_id'] ?? null,
            'capacity_override' => $data['capacity_override'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'notes' => $data['notes'] ?? null,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $meetings
     * @return list<array{day: string, time_slot_id: int, room_id: int|null}>
     */
    private function normaliseMeetings(array $meetings): array
    {
        return array_values(array_map(static fn (array $m): array => [
            'day' => (string) $m['day'],
            'time_slot_id' => (int) $m['time_slot_id'],
            'room_id' => isset($m['room_id']) ? (int) $m['room_id'] : null,
        ], $meetings));
    }

    /** @param list<array{day: string, time_slot_id: int, room_id: int|null}> $meetings */
    private function clashResponse(QueryException $e, int $sessionId, ?int $teacherId, array $meetings, ?int $ignoreClassId = null): RedirectResponse
    {
        if (! ClashTranslator::isUniqueViolation($e)) {
            throw $e;
        }

        return back()
            ->withInput()
            ->with('error', ClashTranslator::explain($e, $sessionId, $teacherId, $meetings, $ignoreClassId));
    }

    /** @return array<string, mixed> */
    private function formData(?SchoolClass $class): array
    {
        return [
            'kelas' => $class === null ? null : [
                'id' => $class->id,
                'year_level' => $class->year_level,
                'stream' => $class->stream,
                'teacher_id' => $class->teacher_id,
                'capacity_override' => $class->capacity_override,
                'is_active' => $class->is_active,
                'notes' => $class->notes,
                'meetings' => $class->meetings->map(fn ($m): array => [
                    'day' => $m->day->value,
                    'time_slot_id' => $m->time_slot_id,
                    'room_id' => $m->room_id,
                ])->values()->all(),
            ],
            'pilihan' => [
                'guru' => Teacher::where('is_active', true)->orderBy('name')
                    ->get(['id', 'name', 'firmness'])
                    ->map(fn (Teacher $t): array => [
                        'id' => $t->id,
                        'nama' => $t->name,
                        'ketegasan' => $t->firmnessLabel(),
                    ])->all(),
                'bilik' => Room::where('is_active', true)->orderBy('name')
                    ->get(['id', 'name', 'capacity'])
                    ->map(fn (Room $r): array => [
                        'id' => $r->id,
                        'nama' => $r->name,
                        'kapasiti' => $r->capacity,
                    ])->all(),
                // REHAT is excluded: a class can never be scheduled into the break row.
                'slot' => TimeSlot::where('is_break', false)->orderBy('sort_order')
                    ->get(['id', 'label'])
                    ->map(fn (TimeSlot $t): array => ['id' => $t->id, 'label' => $t->label])->all(),
                'hari' => array_map(
                    fn (Day $d): array => ['value' => $d->value, 'label' => $d->label()],
                    Day::schoolWeek(),
                ),
            ],
        ];
    }
}
