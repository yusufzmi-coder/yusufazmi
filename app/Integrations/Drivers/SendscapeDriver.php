<?php

declare(strict_types=1);

namespace App\Integrations\Drivers;

use App\Integrations\IntegrationDriver;
use App\Integrations\TestResult;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Sendscape has no official Laravel SDK, so this talks to its HTTP API directly:
 * POST https://api.sendscape.ai/v1/emails with a Bearer key beginning "ss_".
 */
final class SendscapeDriver implements IntegrationDriver
{
    private const ENDPOINT = 'https://api.sendscape.ai/v1/emails';

    public static function key(): string
    {
        return 'sendscape';
    }

    public function label(): string
    {
        return 'Sendscape (Emel)';
    }

    public function description(): string
    {
        return 'Hantar emel jadual dan makluman kepada ibu bapa melalui Sendscape.';
    }

    public function fields(): array
    {
        return [
            [
                'name' => 'api_key',
                'label' => 'API Key',
                'type' => 'password',
                'hint' => 'Bermula dengan ss_ — dijana di halaman API Keys Sendscape.',
                'required' => true,
            ],
            [
                'name' => 'from_address',
                'label' => 'Alamat Penghantar',
                'type' => 'text',
                'hint' => 'Mesti daripada domain yang sudah disahkan.',
                'required' => true,
            ],
        ];
    }

    /** @return array<string, string> */
    public function envFallback(): array
    {
        return [
            'api_key' => 'services.sendscape.key',
            'from_address' => 'services.sendscape.from',
        ];
    }

    /** @param array<string, string|null> $config */
    public function test(array $config): TestResult
    {
        $key = $config['api_key'] ?? null;
        $from = $config['from_address'] ?? null;

        if (blank($key) || blank($from)) {
            return TestResult::failed('API key dan alamat penghantar diperlukan.');
        }

        try {
            $response = Http::withToken($key)
                ->timeout(15)
                ->acceptJson()
                ->post(self::ENDPOINT, [
                    'from' => $from,
                    'to' => $from,           // send to itself; no third party is bothered
                    'subject' => 'Ujian sambungan — Jadual Kelas Student Auto',
                    'text' => 'Jika anda menerima emel ini, sambungan Sendscape berjaya.',
                ]);
        } catch (Throwable $e) {
            return TestResult::failed('Tidak dapat menghubungi Sendscape: '.$e->getMessage());
        }

        // The API acknowledges a queued send with 202.
        if ($response->status() === 202 || $response->successful()) {
            return TestResult::ok("Emel ujian dihantar ke {$from}.");
        }

        if ($response->status() === 401 || $response->status() === 403) {
            return TestResult::failed('API key ditolak oleh Sendscape.');
        }

        return TestResult::failed("Sendscape membalas {$response->status()}: ".$response->body());
    }
}
