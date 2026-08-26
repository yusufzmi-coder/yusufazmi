<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Assignment\Dto\RuleConfig;
use App\Domain\Assignment\RuleTypeRegistry;
use App\Enums\EnrolmentStatus;
use App\Models\AcademicSession;
use App\Models\Room;
use App\Models\Rule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Support\TimetableGrid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class DashboardController
{
    public function __invoke(Request $request, TimetableGrid $grid, RuleTypeRegistry $registry): Response
    {
        $session = AcademicSession::active();

        return Inertia::render('dashboard', [
            'stats' => $this->stats($session->id),
            'grid' => $grid->build($session->id),
            'rules' => $this->rules($registry),
            'distribution' => $this->distribution(),
            'activities' => $this->activities(),
        ]);
    }

    /** @return array<string, array<string, int>> */
    private function stats(int $sessionId): array
    {
        $students = Student::query()
            ->selectRaw('count(*) filter (where is_active) as aktif, count(*) filter (where not is_active) as tidak_aktif')
            ->first();

        // A class counts as "terisi" once it has at least one active enrolment.
        $classes = SchoolClass::query()
            ->where('session_id', $sessionId)
            ->selectRaw('count(*) as jumlah')
            ->selectRaw('count(*) filter (where exists (
                select 1 from enrolments e where e.class_id = classes.id and e.status = ?
            )) as terisi', [EnrolmentStatus::Active->value])
            ->first();

        $teachers = Teacher::query()
            ->selectRaw('count(*) filter (where is_active) as aktif, count(*) filter (where not is_active) as tidak_aktif')
            ->first();

        // Counted without a join: joining class_meetings multiplies the rows, so
        // count(*) would report the number of meetings rather than the number of rooms.
        $roomTotal = Room::query()->where('is_active', true)->count();
        $roomsInUse = Room::query()
            ->where('is_active', true)
            ->whereHas('classMeetings')
            ->count();

        return [
            'pelajar' => [
                'jumlah' => (int) $students->aktif + (int) $students->tidak_aktif,
                'aktif' => (int) $students->aktif,
                'tidak_aktif' => (int) $students->tidak_aktif,
            ],
            'kelas' => [
                'jumlah' => (int) $classes->jumlah,
                'terisi' => (int) $classes->terisi,
                'kosong' => (int) $classes->jumlah - (int) $classes->terisi,
            ],
            'guru' => [
                'jumlah' => (int) $teachers->aktif + (int) $teachers->tidak_aktif,
                'aktif' => (int) $teachers->aktif,
                'tidak_aktif' => (int) $teachers->tidak_aktif,
            ],
            'bilik' => [
                'jumlah' => $roomTotal,
                'dalam_guna' => $roomsInUse,
                'tersedia' => $roomTotal - $roomsInUse,
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    private function rules(RuleTypeRegistry $registry): array
    {
        return Rule::query()
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (Rule $r): bool => $registry->has($r->rule_type))
            ->map(fn (Rule $r): array => [
                'id' => $r->id,
                'rule_type' => $r->rule_type,
                'name' => $r->name,
                'is_active' => $r->is_active,
                'weight' => $r->weight,
                'description' => $registry->get($r->rule_type)->describe(
                    new RuleConfig(
                        $r->id, $r->rule_type, $r->kind, $r->weight, $r->config ?? [], $r->applies_to ?? []
                    )
                ),
            ])
            ->values()
            ->all();
    }

    /** @return list<array{tahun: int, jumlah: int}> */
    private function distribution(): array
    {
        return Student::query()
            ->where('is_active', true)
            ->select('year_level as tahun', DB::raw('count(*) as jumlah'))
            ->groupBy('year_level')
            ->orderBy('year_level')
            ->get()
            ->map(fn ($r): array => ['tahun' => (int) $r->tahun, 'jumlah' => (int) $r->jumlah])
            ->all();
    }

    /** @return list<array<string, mixed>> */
    private function activities(): array
    {
        return Activity::query()
            ->latest('id')
            ->limit(6)
            ->get()
            ->map(fn (Activity $a): array => [
                'id' => $a->id,
                'description' => $a->description,
                'log_name' => $a->log_name,
                'when' => $a->created_at?->diffForHumans(),
            ])
            ->all();
    }
}
