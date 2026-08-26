<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TeacherRequest;
use App\Models\AcademicSession;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TeacherController
{
    public function index(): Response
    {
        $sessionId = AcademicSession::active()->id;

        return Inertia::render('guru/index', [
            'guru' => Teacher::query()
                ->withCount(['classes' => fn ($q) => $q->where('session_id', $sessionId)])
                ->orderBy('name')
                ->get()
                ->map(fn (Teacher $t): array => [
                    'id' => $t->id,
                    'nama' => $t->name,
                    'emel' => $t->email,
                    'telefon' => $t->phone,
                    'ketegasan' => $t->firmness,
                    'ketegasan_label' => $t->firmnessLabel(),
                    'kelas' => $t->classes_count,
                    'had_kelas' => $t->max_classes,
                    'aktif' => $t->is_active,
                ])
                ->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('guru/form', ['guru' => null]);
    }

    public function edit(Teacher $teacher): Response
    {
        return Inertia::render('guru/form', [
            'guru' => [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
                'phone' => $teacher->phone,
                'firmness' => $teacher->firmness,
                'max_classes' => $teacher->max_classes,
                'is_active' => $teacher->is_active,
                'notes' => $teacher->notes,
            ],
        ]);
    }

    public function store(TeacherRequest $request): RedirectResponse
    {
        $teacher = Teacher::create($request->validated());

        return to_route('guru.index')->with('success', "{$teacher->name} ditambah.");
    }

    public function update(TeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        // Editing a teacher's own attributes never changes which class they teach,
        // so the denormalised copy in class_meetings is untouched here. That copy is
        // only refreshed when a CLASS changes teacher (see SchoolClassController).
        $teacher->update($request->validated());

        return to_route('guru.index')->with('success', "{$teacher->name} dikemas kini.");
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        // Soft delete, so the foreign key stays quiet. An unguarded delete would leave
        // classes pointing at a teacher who no longer appears anywhere in the UI.
        $classes = $teacher->classes()->count();

        if ($classes > 0) {
            return back()->with(
                'error',
                "{$teacher->name} masih mengajar {$classes} kelas. Tukar guru kelas itu dahulu.",
            );
        }

        $teacher->delete();

        return to_route('guru.index')->with('success', "{$teacher->name} dibuang.");
    }
}
