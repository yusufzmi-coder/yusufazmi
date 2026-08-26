<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\EnrolmentStatus;
use App\Enums\Gender;
use App\Models\AcademicSession;
use App\Models\Room;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\TimeSlot;
use Inertia\Inertia;
use Inertia\Response;

class StatisticsController
{
    public function index(): Response
    {
        $session = AcademicSession::active();

        return Inertia::render('statistik/index', [
            'kelas' => $this->classLoad($session->id),
            'guru' => $this->teacherLoad($session->id),
            'bilik' => $this->roomUsage(),
        ]);
    }

    /**
     * Headcount, capacity and gender split per class -- the numbers admins argue about.
     *
     * @return list<array<string, mixed>>
     */
    private function classLoad(int $sessionId): array
    {
        return SchoolClass::query()
            ->where('session_id', $sessionId)
            ->with(['teacher:id,name', 'meetings.room:id,capacity'])
            ->withCount([
                'enrolments as pelajar' => fn ($q) => $q->where('status', EnrolmentStatus::Active->value),
                'enrolments as lelaki' => fn ($q) => $q
                    ->where('status', EnrolmentStatus::Active->value)
                    ->whereHas('student', fn ($q) => $q->where('gender', Gender::Lelaki->value)),
            ])
            ->orderBy('year_level')
            ->orderBy('stream')
            ->get()
            ->map(function (SchoolClass $c): array {
                $capacity = $c->effectiveCapacity();

                return [
                    'nama' => $c->name,
                    'guru' => $c->teacher?->name,
                    'pelajar' => $c->pelajar,
                    'kapasiti' => $capacity,
                    'peratus' => $capacity > 0 ? (int) round($c->pelajar / $capacity * 100) : 0,
                    'lelaki' => $c->lelaki,
                    'perempuan' => $c->pelajar - $c->lelaki,
                ];
            })
            ->all();
    }

    /** @return list<array<string, mixed>> */
    private function teacherLoad(int $sessionId): array
    {
        return Teacher::query()
            ->where('is_active', true)
            ->withCount(['classes' => fn ($q) => $q->where('session_id', $sessionId)])
            ->orderByDesc('classes_count')
            ->orderBy('name')
            ->get()
            ->map(fn (Teacher $t): array => [
                'nama' => $t->name,
                'kelas' => $t->classes_count,
                'had' => $t->max_classes,
                'ketegasan' => $t->firmness,
                'ketegasan_label' => $t->firmnessLabel(),
            ])
            ->all();
    }

    /** @return list<array<string, mixed>> */
    private function roomUsage(): array
    {
        $slotsPerWeek = TimeSlot::where('is_break', false)->count() * 5;

        return Room::query()
            ->where('is_active', true)
            ->withCount('classMeetings')
            ->orderBy('name')
            ->get()
            ->map(fn (Room $r): array => [
                'nama' => $r->name,
                'kapasiti' => $r->capacity,
                'pertemuan' => $r->class_meetings_count,
                'peratus' => $slotsPerWeek > 0
                    ? (int) round($r->class_meetings_count / $slotsPerWeek * 100)
                    : 0,
            ])
            ->all();
    }
}
