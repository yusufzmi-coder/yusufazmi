<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * The public landing page. It doubles as a deployment smoke test: if this renders
 * with green checks, the container, the database and the cache are all reachable —
 * which is the first thing you want to know after a deploy.
 */
class WelcomeController
{
    public function __invoke(): Response
    {
        return Inertia::render('welcome', [
            'status' => [
                'app' => [
                    'ok' => true,
                    'label' => 'Aplikasi',
                    'detail' => 'Laravel '.app()->version().' · PHP '.PHP_VERSION,
                ],
                'database' => $this->check('Pangkalan Data', function (): string {
                    $version = DB::selectOne('SHOW server_version')->server_version ?? '?';
                    $students = DB::table('students')->count();

                    return "PostgreSQL {$version} · {$students} pelajar";
                }),
                'cache' => $this->check('Cache & Baris Gilir', function (): string {
                    Cache::put('healthcheck', 'ok', 10);

                    return Cache::get('healthcheck') === 'ok'
                        ? 'Redis bertindak balas'
                        : 'Redis tidak membalas nilai yang betul';
                }),
            ],
            'environment' => app()->environment(),
        ]);
    }

    /**
     * @param  callable(): string  $probe
     * @return array{ok: bool, label: string, detail: string}
     */
    private function check(string $label, callable $probe): array
    {
        try {
            return ['ok' => true, 'label' => $label, 'detail' => $probe()];
        } catch (Throwable $e) {
            // The message is the whole point of the page when something is broken.
            return ['ok' => false, 'label' => $label, 'detail' => $e->getMessage()];
        }
    }
}
