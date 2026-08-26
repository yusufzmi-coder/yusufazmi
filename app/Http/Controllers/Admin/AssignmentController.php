<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Assignment\Enums\ItemAction;
use App\Domain\Assignment\Enums\RunStatus;
use App\Domain\Assignment\Enums\SolveMode;
use App\Domain\Assignment\Services\CommitAssignment;
use App\Domain\Assignment\Services\RevertAssignment;
use App\Domain\Assignment\Services\RunAssignment;
use App\Models\AcademicSession;
use App\Models\AssignmentRun;
use App\Models\AssignmentRunItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class AssignmentController
{
    public function index(RevertAssignment $revert): Response
    {
        $session = AcademicSession::active();

        return Inertia::render('auto-assign/index', [
            'runs' => AssignmentRun::query()
                ->where('session_id', $session->id)
                ->with('creator:id,name')
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn (AssignmentRun $r): array => [
                    'id' => $r->id,
                    'mode' => $r->mode->value,
                    'status' => $r->status->value,
                    'stats' => $r->stats,
                    'duration_ms' => $r->duration_ms,
                    'created_by' => $r->creator?->name,
                    'created_at' => $r->created_at->diffForHumans(),
                    'boleh_buat_asal' => $revert->canRevert($r),
                ])
                ->all(),
        ]);
    }

    /** Solves in memory and stores a DRAFT. Nothing touches `enrolments` here. */
    public function store(Request $request, RunAssignment $run): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'string', 'in:fill_only,rebalance'],
        ]);

        $session = AcademicSession::active();

        try {
            $draft = $run($session->id, SolveMode::from($validated['mode']), (int) $request->user()->id);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return to_route('auto-assign.show', $draft);
    }

    public function show(AssignmentRun $run): Response
    {
        abort_unless($run->session_id === AcademicSession::active()->id, 404);

        $items = $run->items()
            ->with(['student:id,name,year_level,gender,behaviour_level', 'toClass:id,year_level,stream,name', 'fromClass:id,year_level,stream,name'])
            ->orderByRaw("array_position(array['unplaceable','move','place','pinned','keep']::text[], action)")
            ->orderBy('student_id')
            ->get()
            ->map(fn (AssignmentRunItem $i): array => [
                'id' => $i->id,
                'action' => $i->action->value,
                'pelajar' => $i->student->name,
                'tahun' => $i->student->year_level,
                'dari' => $i->fromClass?->name,
                'ke' => $i->toClass?->name,
                'sebab' => $i->reason_text,
                'ada_pertukaran' => $i->violations !== [],
                'skor' => $i->score,
            ]);

        return Inertia::render('auto-assign/preview', [
            'run' => [
                'id' => $run->id,
                'mode' => $run->mode->value,
                'status' => $run->status->value,
                'stats' => $run->stats,
                'duration_ms' => $run->duration_ms,
                'objective' => $run->objective_score,
                'created_at' => $run->created_at->diffForHumans(),
                'boleh_sahkan' => $run->status === RunStatus::Draft,
            ],
            'items' => $items->all(),
            'ringkasan' => $this->summary($run),
        ]);
    }

    public function commit(Request $request, AssignmentRun $run, CommitAssignment $commit): RedirectResponse
    {
        $validated = $request->validate([
            'excluded' => ['array'],
            'excluded.*' => ['integer'],
        ]);

        try {
            $result = $commit($run, (int) $request->user()->id, $validated['excluded'] ?? []);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return to_route('jadual.index')->with(
            'success',
            "{$result['applied']} penempatan disahkan".($result['skipped'] > 0 ? ", {$result['skipped']} dilangkau." : '.'),
        );
    }

    /** Rolls a committed run back: created enrolments go, closed ones come back. */
    public function revert(AssignmentRun $run, RevertAssignment $revert): RedirectResponse
    {
        abort_unless($run->session_id === AcademicSession::active()->id, 404);

        try {
            $result = $revert($run);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return to_route('auto-assign.index')->with(
            'success',
            "Larian #{$run->id} dibuat asal: {$result['dibuang']} penempatan dibuang, {$result['dipulihkan']} dipulihkan.",
        );
    }

    public function destroy(AssignmentRun $run): RedirectResponse
    {
        abort_unless($run->status === RunStatus::Draft, 409);

        $run->update(['status' => RunStatus::Discarded->value]);

        return to_route('auto-assign.index')->with('success', 'Cadangan dibuang.');
    }

    /**
     * Per-class before/after headcount, so the admin sees the shape of the change
     * rather than only a list of individual rows.
     *
     * @return list<array<string, mixed>>
     */
    private function summary(AssignmentRun $run): array
    {
        $rows = $run->items()
            ->whereNotNull('to_class_id')
            ->with('toClass:id,year_level,stream,name')
            ->get()
            ->groupBy('to_class_id')
            ->map(fn ($items) => [
                'kelas' => $items->first()->toClass->name,
                'baharu' => $items->where('action', ItemAction::Place)->count(),
                'masuk' => $items->where('action', ItemAction::Move)->count(),
                'kekal' => $items->whereIn('action', [ItemAction::Keep, ItemAction::Pinned])->count(),
                'jumlah' => $items->count(),
            ])
            ->sortBy('kelas')
            ->values()
            ->all();

        return $rows;
    }
}
