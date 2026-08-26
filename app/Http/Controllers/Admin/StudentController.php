<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\BehaviourLevel;
use App\Enums\EnrolmentStatus;
use App\Enums\Gender;
use App\Enums\SiblingPolicy;
use App\Http\Requests\StudentRequest;
use App\Models\Family;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class StudentController
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'cari' => ['nullable', 'string', 'max:100'],
            'tahun' => ['nullable', 'integer', 'between:1,6'],
            'status' => ['nullable', 'string', 'in:aktif,tidak_aktif,belum_ada_kelas'],
        ]);

        $students = Student::query()
            ->with(['activeEnrolment.schoolClass:id,year_level,stream,name', 'family:id,name'])
            ->when($filters['cari'] ?? null, fn ($q, string $cari) => $q->where(
                fn ($q) => $q->where('name', 'ilike', "%{$cari}%")->orWhere('student_code', 'ilike', "%{$cari}%")
            ))
            ->when($filters['tahun'] ?? null, fn ($q, int $tahun) => $q->where('year_level', $tahun))
            ->when(($filters['status'] ?? null) === 'aktif', fn ($q) => $q->where('is_active', true))
            ->when(($filters['status'] ?? null) === 'tidak_aktif', fn ($q) => $q->where('is_active', false))
            ->when(($filters['status'] ?? null) === 'belum_ada_kelas', fn ($q) => $q
                ->where('is_active', true)
                ->whereDoesntHave('enrolments', fn ($q) => $q->where('status', EnrolmentStatus::Active->value)))
            ->orderBy('year_level')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString()
            ->through(fn (Student $s): array => [
                'id' => $s->id,
                'kod' => $s->student_code,
                'nama' => $s->name,
                'tahun' => $s->year_level,
                'jantina' => $s->gender->value,
                'tingkah_laku' => $s->behaviour_level->value,
                'tingkah_laku_label' => $s->behaviour_level->label(),
                'keluarga' => $s->family?->name,
                'kelas' => $s->activeEnrolment?->schoolClass?->name,
                'aktif' => $s->is_active,
            ]);

        return Inertia::render('pelajar/index', [
            'pelajar' => $students,
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('pelajar/form', $this->formData(null));
    }

    public function edit(Student $student): Response
    {
        return Inertia::render('pelajar/form', $this->formData($student));
    }

    public function store(StudentRequest $request): RedirectResponse
    {
        $student = DB::transaction(fn (): Student => Student::create($this->payload($request)));

        return to_route('pelajar.index')->with('success', "Pelajar {$student->name} ditambah.");
    }

    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        DB::transaction(fn () => $student->update($this->payload($request)));

        return to_route('pelajar.index')->with('success', "Pelajar {$student->name} dikemas kini.");
    }

    public function destroy(Student $student): RedirectResponse
    {
        // Soft delete: an active enrolment keeps its history, and the partial unique
        // index on student_code frees the code for reuse.
        $student->enrolments()
            ->where('status', EnrolmentStatus::Active->value)
            ->update([
                'status' => EnrolmentStatus::Withdrawn->value,
                'left_at' => now(),
                'updated_at' => now(),
            ]);

        $student->delete();

        return to_route('pelajar.index')->with('success', "Pelajar {$student->name} dibuang.");
    }

    /** @return array<string, mixed> */
    private function payload(StudentRequest $request): array
    {
        $data = $request->validated();

        // "Tambah adik-beradik" creates the family inline so the admin never has to
        // visit a separate screen first.
        if (blank($data['family_id'] ?? null) && filled($data['family_name'] ?? null)) {
            $data['family_id'] = Family::firstOrCreate(
                ['name' => $data['family_name']],
                ['sibling_policy' => SiblingPolicy::Inherit],
            )->id;
        }

        unset($data['family_name']);

        return $data;
    }

    /** @return array<string, mixed> */
    private function formData(?Student $student): array
    {
        return [
            'pelajar' => $student === null ? null : [
                'id' => $student->id,
                'student_code' => $student->student_code,
                'name' => $student->name,
                'gender' => $student->gender->value,
                'year_level' => $student->year_level,
                'behaviour_level' => $student->behaviour_level->value,
                'date_of_birth' => $student->date_of_birth?->toDateString(),
                'national_id' => $student->national_id,
                'phone' => $student->phone,
                'special_needs' => $student->special_needs,
                'is_active' => $student->is_active,
                'enrolled_on' => $student->enrolled_on->toDateString(),
                'family_id' => $student->family_id,
            ],
            'keluarga' => Family::orderBy('name')
                ->get(['id', 'name', 'sibling_policy'])
                ->map(fn (Family $f): array => [
                    'id' => $f->id,
                    'nama' => $f->name,
                    'polisi' => $f->sibling_policy->label(),
                ])
                ->all(),
            'pilihan' => [
                'jantina' => array_map(
                    fn (Gender $g): array => ['value' => $g->value, 'label' => $g->label()],
                    Gender::cases(),
                ),
                'tingkah_laku' => array_map(
                    fn (BehaviourLevel $b): array => ['value' => $b->value, 'label' => $b->label()],
                    BehaviourLevel::cases(),
                ),
            ],
        ];
    }
}
