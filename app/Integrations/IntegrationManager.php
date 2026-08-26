<?php

declare(strict_types=1);

namespace App\Integrations;

use App\Models\Integration;
use Illuminate\Contracts\Container\Container;
use RuntimeException;

/**
 * Resolves integration credentials, preferring what the admin typed into the widget
 * over the .env file -- so keys can be rotated from the browser without a deploy.
 */
final class IntegrationManager
{
    /** @var array<string, IntegrationDriver> */
    private array $drivers = [];

    /** @param list<class-string<IntegrationDriver>> $classes */
    public function __construct(array $classes, private readonly Container $container)
    {
        foreach ($classes as $class) {
            $this->drivers[$class::key()] = $container->make($class);
        }
    }

    public function driver(string $key): IntegrationDriver
    {
        return $this->drivers[$key] ?? throw new RuntimeException("Integrasi tidak dikenali: [{$key}].");
    }

    /** @return array<string, IntegrationDriver> */
    public function all(): array
    {
        return $this->drivers;
    }

    public function has(string $key): bool
    {
        return isset($this->drivers[$key]);
    }

    /**
     * Saved credentials merged over the env fallback. Blank saved values fall back
     * rather than blanking the working configuration.
     *
     * @return array<string, string|null>
     */
    public function config(string $key): array
    {
        $driver = $this->driver($key);
        $stored = Integration::where('key', $key)->first()?->config ?? [];

        $config = [];

        foreach ($driver->envFallback() as $field => $envKey) {
            $value = $stored[$field] ?? null;
            $config[$field] = ($value === null || $value === '') ? config($envKey) : $value;
        }

        return $config;
    }

    public function enabled(string $key): bool
    {
        $record = Integration::where('key', $key)->first();

        if ($record !== null) {
            return $record->enabled;
        }

        // No row yet: treat it as configured if the env fallback is complete.
        return collect($this->config($key))->every(fn ($v): bool => $v !== null && $v !== '');
    }
}
