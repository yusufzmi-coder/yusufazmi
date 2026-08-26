<?php

declare(strict_types=1);

namespace App\Providers;

use App\Integrations\IntegrationManager;
use App\Mail\Transport\SendscapeTransport;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class IntegrationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            IntegrationManager::class,
            fn (Application $app): IntegrationManager => new IntegrationManager(
                config('integrations.drivers', []),
                $app,
            ),
        );
    }

    public function boot(): void
    {
        // MAIL_MAILER=sendscape now routes every mail through the HTTP API.
        Mail::extend('sendscape', fn (): SendscapeTransport => new SendscapeTransport(
            $this->app->make(IntegrationManager::class),
        ));
    }
}
