<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RoomRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RoomController
{
    public function index(): Response
    {
        return Inertia::render('bilik/index', [
            'bilik' => Room::query()
                ->withCount('classMeetings')
                ->orderBy('name')
                ->get()
                ->map(fn (Room $r): array => [
                    'id' => $r->id,
                    'nama' => $r->name,
                    'kapasiti' => $r->capacity,
                    'lokasi' => $r->location,
                    'pertemuan' => $r->class_meetings_count,
                    'aktif' => $r->is_active,
                ])
                ->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('bilik/form', ['bilik' => null]);
    }

    public function edit(Room $room): Response
    {
        return Inertia::render('bilik/form', [
            'bilik' => [
                'id' => $room->id,
                'name' => $room->name,
                'capacity' => $room->capacity,
                'location' => $room->location,
                'is_active' => $room->is_active,
            ],
        ]);
    }

    public function store(RoomRequest $request): RedirectResponse
    {
        $room = Room::create($request->validated());

        return to_route('bilik.index')->with('success', "{$room->name} ditambah.");
    }

    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        return to_route('bilik.index')->with('success', "{$room->name} dikemas kini.");
    }

    public function destroy(Room $room): RedirectResponse
    {
        // This is a soft delete, so the foreign key never objects. Without an explicit
        // check the room would vanish from the list while the timetable still points
        // at it, and its capacity would silently stop constraining the engine.
        $inUse = $room->classMeetings()->count();

        if ($inUse > 0) {
            return back()->with(
                'error',
                "{$room->name} masih digunakan dalam {$inUse} waktu kelas. Keluarkan ia dari jadual dahulu.",
            );
        }

        $room->delete();

        return to_route('bilik.index')->with('success', "{$room->name} dibuang.");
    }
}
