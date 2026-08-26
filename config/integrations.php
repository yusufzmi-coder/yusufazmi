<?php

declare(strict_types=1);
use App\Integrations\Drivers\CloudflareR2Driver;
use App\Integrations\Drivers\GoogleOAuthDriver;
use App\Integrations\Drivers\SendscapeDriver;

return [
    /*
     | Every integration the admin can configure from Tetapan -> Integrasi.
     | Credentials saved through the widget are encrypted at rest and override the
     | matching .env value at runtime, so keys rotate without a deploy.
     */
    'drivers' => [
        GoogleOAuthDriver::class,
        SendscapeDriver::class,
        CloudflareR2Driver::class,
    ],
];
