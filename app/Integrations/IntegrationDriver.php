<?php

declare(strict_types=1);

namespace App\Integrations;

interface IntegrationDriver
{
    /** Stable key stored in integrations.key. Never rename. */
    public static function key(): string;

    public function label(): string;

    public function description(): string;

    /**
     * The credential fields rendered in the admin widget.
     *
     * @return list<array{name: string, label: string, type: 'text'|'password'|'url', hint?: string, required?: bool}>
     */
    public function fields(): array;

    /**
     * Env keys used as the fallback when nothing is saved in the database.
     *
     * @return array<string, string>
     */
    public function envFallback(): array;

    /**
     * Actually reach the service. Returns a human-readable Malay result.
     *
     * @param  array<string, string|null>  $config
     */
    public function test(array $config): TestResult;
}
