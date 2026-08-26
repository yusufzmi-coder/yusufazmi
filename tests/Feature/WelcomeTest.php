<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

it('shows a green status page when everything is reachable', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('welcome')
            ->where('status.app.ok', true)
            ->where('status.database.ok', true)
            ->where('status.cache.ok', true)
            ->has('environment')
        );
});

it('reports the failure instead of blowing up when the database is unreachable', function () {
    // The page exists to answer "did the deploy work" -- a 500 here would tell the
    // operator nothing, so a broken dependency has to render as a red row.
    DB::shouldReceive('selectOne')->andThrow(new RuntimeException('could not connect to server'));

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('status.database.ok', false)
            ->where('status.database.detail', 'could not connect to server')
            ->etc()
        );
});

it('is reachable without logging in', function () {
    $this->get('/')->assertOk()->assertSee('data-page', false);
});
