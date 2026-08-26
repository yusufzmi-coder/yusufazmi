<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Enrolment\EnrolmentManager;
use App\Enums\EnrolmentSource;
use App\Enums\EnrolmentStatus;
use App\Models\AcademicSession;
use App\Models\Enrolment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** The manual counterpart to Auto Assign: one student at a time, from the class roster. */
class EnrolmentController
{
    public function __construct(private readonly EnrolmentManager $enrolments) {}

    /** Seats a student who currently has no class. */
    public function store(Request $request, SchoolClass $class): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', Rule::exists('students', 'id')->whereNull('deleted_at')],
        ], [], ['student_id' => 'pelajar']);

        $student = Student::whereKey($validated['student_id'])->firstOrFail();

        // The manual path honours the same non-negotiable limits as the engine, or it
        // could create a roster the solver considers impossible.
        if ($reason = $this->enrolments->rejectionReason($student, $class)) {
            return back()->with('error', $reason);
        }

        $this->enrolments->place($student, $class, EnrolmentSource::Manual, reasonText: 'Ditambah secara manual oleh admin.');

        return back()->with('success', "{$student->name} dimasukkan ke dalam {$class->name}.");
    }

    /** Moves a student to a different class. */
    public function update(Request $request, Enrolment $enrolment): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => ['required', 'integer', Rule::exists('classes', 'id')->whereNull('deleted_at')],
        ], [], ['class_id' => 'kelas']);

        $target = SchoolClass::whereKey($validated['class_id'])->firstOrFail();
        $student = $enrolment->student;

        if ($target->id === $enrolment->class_id) {
            return back()->with('error', "{$student->name} sudah berada dalam {$target->name}.");
        }

        if ($reason = $this->enrolments->rejectionReason($student, $target)) {
            return back()->with('error', $reason);
        }

        $from = $enrolment->schoolClass->name;

        $this->enrolments->place(
            $student,
            $target,
            EnrolmentSource::Manual,
            reasonText: "Dipindah dari {$from} secara manual oleh admin.",
            pinned: $enrolment->is_pinned,
        );

        return back()->with('success', "{$student->name} dipindah dari {$from} ke {$target->name}.");
    }

    public function destroy(Enrolment $enrolment): RedirectResponse
    {
        $student = $enrolment->student;
        $class = $enrolment->schoolClass->name;

        $this->enrolments->withdraw($student, $enrolment->session_id);

        return back()->with('success', "{$student->name} dikeluarkan daripada {$class}.");
    }

    /** Pinning is how an admin overrides the engine for one student. */
    public function pin(Request $request, Enrolment $enrolment): RedirectResponse
    {
        $validated = $request->validate(['pinned' => ['required', 'boolean']]);

        $this->enrolments->setPinned($enrolment, $validated['pinned']);

        $name = $enrolment->student->name;

        return back()->with(
            'success',
            $validated['pinned']
                ? "{$name} disemat — Auto Assign tidak akan mengalihkannya."
                : "Semat dibuang daripada {$name}.",
        );
    }

    /**
     * Students eligible for a given class: right year, currently unplaced.
     * Used by the "Tambah pelajar" picker on the roster panel.
     *
     * @return list<array{id: int, nama: string, kod: string}>
     */
    public static function eligibleFor(SchoolClass $class): array
    {
        $sessionId = AcademicSession::active()->id;

        return Student::query()
            ->where('is_active', true)
            ->where('year_level', $class->year_level)
            ->whereDoesntHave('enrolments', fn ($q) => $q
                ->where('session_id', $sessionId)
                ->where('status', EnrolmentStatus::Active->value))
            ->orderBy('name')
            ->get(['id', 'name', 'student_code'])
            ->map(fn (Student $s): array => [
                'id' => $s->id,
                'nama' => $s->name,
                'kod' => $s->student_code,
            ])
            ->values()
            ->all();
    }
}
