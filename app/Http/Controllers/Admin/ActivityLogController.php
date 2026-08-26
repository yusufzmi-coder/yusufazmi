<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController
{
    /**
     * Before/after pairs for display. These come from `attribute_changes`, which is
     * constrained by each model's logOnly() allow-list -- PII never reaches it.
     *
     * @return list<array{medan: string, dari: string, ke: string}>
     */
    private function changes(Activity $activity): array
    {
        $changes = $activity->attribute_changes ?? collect();
        $new = $changes['attributes'] ?? [];
        $old = $changes['old'] ?? [];

        $rows = [];

        foreach ($new as $field => $value) {
            $rows[] = [
                'medan' => $field,
                'dari' => $this->stringify($old[$field] ?? null),
                'ke' => $this->stringify($value),
            ];
        }

        return $rows;
    }

    private function stringify(mixed $value): string
    {
        return match (true) {
            $value === null => '—',
            is_bool($value) => $value ? 'ya' : 'tidak',
            is_array($value) => json_encode($value) ?: '—',
            default => (string) $value,
        };
    }

    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'log' => ['nullable', 'string', 'max:50'],
        ]);

        return Inertia::render('log-aktiviti/index', [
            'aktiviti' => Activity::query()
                ->when($filters['log'] ?? null, fn ($q, string $log) => $q->where('log_name', $log))
                ->with('causer')
                ->latest('id')
                ->paginate(30)
                ->withQueryString()
                ->through(fn (Activity $a): array => [
                    'id' => $a->id,
                    'log' => $a->log_name,
                    'peristiwa' => $a->event,
                    'huraian' => $a->description,
                    'subjek' => class_basename((string) $a->subject_type),
                    'oleh' => $a->causer instanceof User ? $a->causer->name : 'Sistem',
                    'perubahan' => $this->changes($a),
                    'bila' => $a->created_at?->diffForHumans(),
                    'masa' => $a->created_at?->format('d/m/Y H:i'),
                ]),
            'logs' => Activity::query()->distinct()->orderBy('log_name')->pluck('log_name')->all(),
            'filters' => $filters,
        ]);
    }
}
