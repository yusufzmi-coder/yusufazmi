<?php

use App\Providers\AppServiceProvider;
use App\Providers\AssignmentServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\IntegrationServiceProvider;

return [
    AssignmentServiceProvider::class,
    IntegrationServiceProvider::class,
    AppServiceProvider::class,
    FortifyServiceProvider::class,
];
