<?php

declare(strict_types=1);

namespace App\Integrations\Drivers;

use App\Integrations\IntegrationDriver;
use App\Integrations\TestResult;
use App\Models\User;

final class GoogleOAuthDriver implements IntegrationDriver
{
    public static function key(): string
    {
        return 'google_oauth';
    }

    public function label(): string
    {
        return 'Google Sign-In';
    }

    public function description(): string
    {
        return 'Benarkan admin dan staf log masuk dengan akaun Google mereka.';
    }

    public function fields(): array
    {
        return [
            ['name' => 'client_id', 'label' => 'Client ID', 'type' => 'text', 'required' => true],
            ['name' => 'client_secret', 'label' => 'Client Secret', 'type' => 'password', 'required' => true],
            [
                'name' => 'redirect',
                'label' => 'Redirect URI',
                'type' => 'url',
                'hint' => 'Salin nilai ini ke Google Cloud Console.',
                'required' => true,
            ],
        ];
    }

    /** @return array<string, string> */
    public function envFallback(): array
    {
        return [
            'client_id' => 'services.google.client_id',
            'client_secret' => 'services.google.client_secret',
            'redirect' => 'services.google.redirect',
        ];
    }

    /**
     * There is nothing to call without a user in the browser, so this checks the
     * things that actually go wrong: missing credentials, or an allowlist nobody
     * can pass because no user has a matching email.
     */
    /** @param array<string, string|null> $config */
    public function test(array $config): TestResult
    {
        foreach (['client_id', 'client_secret', 'redirect'] as $field) {
            if (blank($config[$field] ?? null)) {
                return TestResult::failed('Client ID, secret dan redirect URI perlu diisi.');
            }
        }

        if (! str_contains((string) $config['client_id'], '.apps.googleusercontent.com')) {
            return TestResult::failed('Client ID tidak kelihatan seperti Client ID Google yang sah.');
        }

        $invited = User::query()->whereNotNull('email')->count();

        if ($invited === 0) {
            return TestResult::failed('Tiada pengguna dijemput lagi — tiada sesiapa boleh log masuk.');
        }

        return TestResult::ok("Konfigurasi lengkap. {$invited} pengguna dibenarkan log masuk.");
    }
}
