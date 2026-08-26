<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Imports\StudentImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Three steps: upload, review the mapping and the damage, then commit. Nothing is
 * written until the admin has seen exactly which rows will fail and why.
 */
class StudentImportController
{
    private const PREVIEW_ROWS = 8;

    public function create(): Response
    {
        return Inertia::render('pelajar/import', [
            'medan' => $this->fieldLabels(),
        ]);
    }

    public function preview(Request $request, StudentImporter $importer): Response|RedirectResponse
    {
        $request->validate([
            'fail' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:10240'],
        ], [], ['fail' => 'fail']);

        $file = $request->file('fail');
        // Kept on the local disk between the two steps; removed once committed.
        $path = $file->store('imports');

        if ($path === false) {
            return back()->with('error', 'Fail gagal disimpan. Sila cuba lagi.');
        }

        $parsed = $importer->read(Storage::path($path), (string) $file->getClientOriginalExtension());

        if ($parsed['rows'] === []) {
            Storage::delete($path);

            return back()->with('error', 'Fail itu tiada baris data.');
        }

        $mapping = $importer->guessMapping($parsed['headers']);
        $result = $importer->validate($parsed['rows'], $mapping);

        return Inertia::render('pelajar/import', [
            'medan' => $this->fieldLabels(),
            'preview' => [
                'path' => $path,
                'nama_fail' => $file->getClientOriginalName(),
                'headers' => $parsed['headers'],
                'mapping' => $mapping,
                'jumlah_baris' => count($parsed['rows']),
                'sah' => count($result['valid']),
                'ralat' => $result['errors'],
                'contoh' => array_slice($result['valid'], 0, self::PREVIEW_ROWS),
            ],
        ]);
    }

    /** Re-validates under a mapping the admin corrected by hand. */
    public function remap(Request $request, StudentImporter $importer): Response|RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string'],
            'nama_fail' => ['required', 'string'],
            'mapping' => ['required', 'array'],
        ]);

        if (! Storage::exists($validated['path'])) {
            return to_route('pelajar.import.create')->with('error', 'Fail import sudah luput. Sila muat naik semula.');
        }

        $parsed = $importer->read(
            Storage::path($validated['path']),
            pathinfo($validated['nama_fail'], PATHINFO_EXTENSION),
        );

        $mapping = array_map(
            fn ($v) => filled($v) ? (string) $v : null,
            $validated['mapping'],
        );

        $result = $importer->validate($parsed['rows'], $mapping);

        return Inertia::render('pelajar/import', [
            'medan' => $this->fieldLabels(),
            'preview' => [
                'path' => $validated['path'],
                'nama_fail' => $validated['nama_fail'],
                'headers' => $parsed['headers'],
                'mapping' => $mapping,
                'jumlah_baris' => count($parsed['rows']),
                'sah' => count($result['valid']),
                'ralat' => $result['errors'],
                'contoh' => array_slice($result['valid'], 0, self::PREVIEW_ROWS),
            ],
        ]);
    }

    public function store(Request $request, StudentImporter $importer): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string'],
            'nama_fail' => ['required', 'string'],
            'mapping' => ['required', 'array'],
        ]);

        if (! Storage::exists($validated['path'])) {
            return to_route('pelajar.import.create')->with('error', 'Fail import sudah luput. Sila muat naik semula.');
        }

        $parsed = $importer->read(
            Storage::path($validated['path']),
            pathinfo($validated['nama_fail'], PATHINFO_EXTENSION),
        );

        $mapping = array_map(fn ($v) => filled($v) ? (string) $v : null, $validated['mapping']);
        $result = $importer->validate($parsed['rows'], $mapping);

        // Invalid rows are skipped, never guessed at.
        $counts = $importer->commit($result['valid']);

        Storage::delete($validated['path']);

        $message = "{$counts['dicipta']} pelajar baharu, {$counts['dikemaskini']} dikemas kini.";

        if ($result['errors'] !== []) {
            $message .= ' '.count($result['errors']).' baris dilangkau kerana ralat.';
        }

        return to_route('pelajar.index')->with('success', $message);
    }

    /** @return array<string, array{label: string, wajib: bool}> */
    private function fieldLabels(): array
    {
        return [
            'student_code' => ['label' => 'Kod Pelajar', 'wajib' => true],
            'name' => ['label' => 'Nama', 'wajib' => true],
            'gender' => ['label' => 'Jantina', 'wajib' => true],
            'year_level' => ['label' => 'Tahun', 'wajib' => true],
            'behaviour_level' => ['label' => 'Tahap Tingkah Laku', 'wajib' => false],
            'date_of_birth' => ['label' => 'Tarikh Lahir', 'wajib' => false],
            'national_id' => ['label' => 'No. Kad Pengenalan', 'wajib' => false],
            'phone' => ['label' => 'Telefon', 'wajib' => false],
            'family_name' => ['label' => 'Keluarga (untuk adik-beradik)', 'wajib' => false],
        ];
    }
}
