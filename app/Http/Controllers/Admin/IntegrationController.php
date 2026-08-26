<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Integrations\IntegrationManager;
use App\Models\Integration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IntegrationController
{
    public function index(IntegrationManager $manager): Response
    {
        $records = Integration::all()->keyBy('key');

        return Inertia::render('integrasi/index', [
            'integrations' => collect($manager->all())
                ->map(function ($driver, string $key) use ($manager, $records): array {
                    $record = $records->get($key);
                    $config = $manager->config($key);

                    return [
                        'key' => $key,
                        'label' => $driver->label(),
                        'description' => $driver->description(),
                        'enabled' => $manager->enabled($key),
                        'fields' => $driver->fields(),
                        // Secrets are never sent to the browser in full.
                        'values' => collect($driver->fields())
                            ->mapWithKeys(fn (array $field): array => [
                                $field['name'] => $this->mask($field, $config[$field['name']] ?? null),
                            ])
                            ->all(),
                        'configured' => collect($config)->every(fn ($v): bool => filled($v)),
                        'last_test_status' => $record?->last_test_status,
                        'last_test_message' => $record?->last_test_message,
                        'last_tested_at' => $record?->last_tested_at?->diffForHumans(),
                    ];
                })
                ->values()
                ->all(),
        ]);
    }

    public function update(Request $request, string $key, IntegrationManager $manager): RedirectResponse
    {
        $driver = $manager->driver($key);

        $validated = $request->validate([
            'enabled' => ['boolean'],
            'config' => ['array'],
            ...collect($driver->fields())
                ->mapWithKeys(fn (array $f): array => ["config.{$f['name']}" => ['nullable', 'string', 'max:500']])
                ->all(),
        ]);

        $record = Integration::firstOrNew(['key' => $key]);
        $existing = $record->config ?? [];

        // A blank field means "leave it alone", so a masked secret round-tripping
        // from the browser can never wipe a working credential.
        $incoming = collect($validated['config'] ?? [])
            ->filter(fn ($value): bool => filled($value) && ! str_contains((string) $value, '••'))
            ->all();

        $record->fill([
            'config' => [...$existing, ...$incoming],
            'enabled' => $validated['enabled'] ?? $record->enabled ?? false,
        ])->save();

        return back()->with('success', "Kredensial {$driver->label()} disimpan.");
    }

    public function test(string $key, IntegrationManager $manager): RedirectResponse
    {
        $driver = $manager->driver($key);
        $result = $driver->test($manager->config($key));

        Integration::updateOrCreate(
            ['key' => $key],
            [
                'last_test_status' => $result->ok ? 'ok' : 'failed',
                'last_test_message' => $result->message,
                'last_tested_at' => now(),
            ],
        );

        return back()->with($result->ok ? 'success' : 'error', $result->message);
    }

    /** @param array{name: string, type: string} $field */
    private function mask(array $field, ?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if ($field['type'] !== 'password') {
            return $value;
        }

        $head = mb_substr($value, 0, 3);
        $tail = mb_substr($value, -4);

        return "{$head}••••••{$tail}";
    }
}
