<?php

declare(strict_types=1);

namespace App\Integrations\Drivers;

use App\Integrations\IntegrationDriver;
use App\Integrations\TestResult;
use Illuminate\Support\Facades\Storage;
use Throwable;

/** R2 is S3-compatible, so it rides Laravel's s3 driver with a custom endpoint. */
final class CloudflareR2Driver implements IntegrationDriver
{
    public static function key(): string
    {
        return 'cloudflare_r2';
    }

    public function label(): string
    {
        return 'Cloudflare R2 (Storan)';
    }

    public function description(): string
    {
        return 'Simpan fail import, laporan PDF dan lampiran dalam bucket R2 anda.';
    }

    public function fields(): array
    {
        return [
            ['name' => 'access_key_id', 'label' => 'Access Key ID', 'type' => 'password', 'required' => true],
            ['name' => 'secret_access_key', 'label' => 'Secret Access Key', 'type' => 'password', 'required' => true],
            ['name' => 'bucket', 'label' => 'Nama Bucket', 'type' => 'text', 'required' => true],
            [
                'name' => 'endpoint',
                'label' => 'Endpoint S3',
                'type' => 'url',
                'hint' => 'Contoh: https://<account-id>.r2.cloudflarestorage.com',
                'required' => true,
            ],
        ];
    }

    /** @return array<string, string> */
    public function envFallback(): array
    {
        return [
            'access_key_id' => 'filesystems.disks.s3.key',
            'secret_access_key' => 'filesystems.disks.s3.secret',
            'bucket' => 'filesystems.disks.s3.bucket',
            'endpoint' => 'filesystems.disks.s3.endpoint',
        ];
    }

    /** @param array<string, string|null> $config */
    public function test(array $config): TestResult
    {
        foreach (['access_key_id', 'secret_access_key', 'bucket', 'endpoint'] as $field) {
            if (blank($config[$field] ?? null)) {
                return TestResult::failed('Semua medan R2 perlu diisi.');
            }
        }

        $path = 'ujian-sambungan/'.now()->format('YmdHis').'.txt';
        $payload = 'Ujian sambungan R2 pada '.now()->toDateTimeString();

        try {
            // Build the disk on the fly so an unsaved credential can still be tested.
            $disk = Storage::build([
                'driver' => 's3',
                'key' => $config['access_key_id'],
                'secret' => $config['secret_access_key'],
                'region' => 'auto',
                'bucket' => $config['bucket'],
                'endpoint' => $config['endpoint'],
                'use_path_style_endpoint' => true,
                'throw' => true,
            ]);

            $disk->put($path, $payload);
            $readBack = $disk->get($path);
            $disk->delete($path);

            if ($readBack !== $payload) {
                return TestResult::failed('Fail ditulis tetapi kandungan dibaca tidak sepadan.');
            }
        } catch (Throwable $e) {
            return TestResult::failed('Gagal: '.$e->getMessage());
        }

        return TestResult::ok("Tulis, baca dan padam berjaya dalam bucket {$config['bucket']}.");
    }
}
