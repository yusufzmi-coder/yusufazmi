<?php

declare(strict_types=1);

use App\Integrations\IntegrationManager;
use App\Models\Integration;
use App\Models\User;
use Database\Seeders\AcademicSessionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(AcademicSessionSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
});

it('lists every configurable integration', function () {
    $this->actingAs($this->user)
        ->get('/integrasi')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('integrasi/index')
            ->has('integrations', 3)
        );
});

it('never sends a stored secret to the browser in full', function () {
    Integration::create([
        'key' => 'sendscape',
        'enabled' => true,
        'config' => ['api_key' => 'ss_supersecretvalue1234', 'from_address' => 'admin@jadual.test'],
    ]);

    $response = $this->actingAs($this->user)->get('/integrasi');

    $response->assertOk();
    $response->assertDontSee('ss_supersecretvalue1234');
});

it('keeps the existing secret when the field is left blank', function () {
    // The widget shows a masked placeholder; submitting the form unchanged must not
    // wipe a working credential.
    Integration::create([
        'key' => 'sendscape',
        'enabled' => true,
        'config' => ['api_key' => 'ss_original', 'from_address' => 'admin@jadual.test'],
    ]);

    $this->actingAs($this->user)
        ->patch('/integrasi/sendscape', ['config' => ['api_key' => '', 'from_address' => 'baru@jadual.test']])
        ->assertRedirect();

    $config = Integration::where('key', 'sendscape')->first()->config;

    expect($config['api_key'])->toBe('ss_original')
        ->and($config['from_address'])->toBe('baru@jadual.test');
});

it('stores credentials encrypted rather than as readable text', function () {
    $this->actingAs($this->user)->patch('/integrasi/sendscape', [
        'enabled' => true,
        'config' => ['api_key' => 'ss_plaintextcheck', 'from_address' => 'admin@jadual.test'],
    ]);

    $raw = DB::table('integrations')->where('key', 'sendscape')->value('config');

    expect($raw)->not->toContain('ss_plaintextcheck');
});

it('prefers a saved credential over the env fallback', function () {
    Config::set('services.sendscape.key', 'ss_from_env');

    expect(app(IntegrationManager::class)->config('sendscape')['api_key'])->toBe('ss_from_env');

    Integration::create(['key' => 'sendscape', 'enabled' => true, 'config' => ['api_key' => 'ss_from_widget']]);

    expect(app(IntegrationManager::class)->config('sendscape')['api_key'])->toBe('ss_from_widget');
});

it('records the outcome of a connection test', function () {
    $this->actingAs($this->user)
        ->post('/integrasi/google_oauth/uji')
        ->assertRedirect();

    $record = Integration::where('key', 'google_oauth')->first();

    // Nothing is configured yet, so the test must fail loudly rather than silently pass.
    expect($record->last_test_status)->toBe('failed')
        ->and($record->last_test_message)->not->toBeEmpty()
        ->and($record->last_tested_at)->not->toBeNull();
});

it('rejects an unknown integration key', function () {
    $this->actingAs($this->user)
        ->patch('/integrasi/tidak-wujud', ['config' => []])
        ->assertServerError();
});
