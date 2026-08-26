<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\AcademicSessionSeeder;

it('redirects guests to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('lets an authenticated user reach the dashboard', function () {
    // The dashboard scopes everything to the active academic session, so a usable
    // install always has one -- it ships in the base seed, not the demo data.
    $this->seed(AcademicSessionSeeder::class);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk();
});
