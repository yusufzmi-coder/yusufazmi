<?php

declare(strict_types=1);

use App\Models\Integration;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

function fakeGoogleUser(string $email): void
{
    $socialiteUser = (new SocialiteUser)->map([
        'id' => '1234567890',
        'name' => 'Google Person',
        'email' => $email,
    ]);

    $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
}

function configureGoogle(): void
{
    Integration::create([
        'key' => 'google_oauth',
        'enabled' => true,
        'config' => [
            'client_id' => 'test.apps.googleusercontent.com',
            'client_secret' => 'secret',
            'redirect' => 'http://localhost/auth/google/callback',
        ],
    ]);
}

it('signs in an invited user via Google', function () {
    configureGoogle();
    $user = User::factory()->create(['email' => 'admin@jadual.test']);
    fakeGoogleUser('admin@jadual.test');

    $this->get('/auth/google/callback')->assertRedirect(route('dashboard'));

    expect(Auth::id())->toBe($user->id);
});

it('refuses a Google account that was never invited', function () {
    // A valid Google login proves identity, not authorisation. This system holds
    // children's records, so an unknown account must never be auto-registered.
    configureGoogle();
    fakeGoogleUser('penceroboh@gmail.com');

    $this->get('/auth/google/callback')
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    expect(Auth::check())->toBeFalse()
        ->and(User::where('email', 'penceroboh@gmail.com')->exists())->toBeFalse();
});

it('matches the invited email case-insensitively', function () {
    configureGoogle();
    $user = User::factory()->create(['email' => 'admin@jadual.test']);
    fakeGoogleUser('Admin@Jadual.Test');

    $this->get('/auth/google/callback')->assertRedirect(route('dashboard'));

    expect(Auth::id())->toBe($user->id);
});

it('refuses to start the flow when Google is not configured', function () {
    $this->get('/auth/google/redirect')
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');
});

it('verifies the email address on first Google sign-in', function () {
    configureGoogle();
    $user = User::factory()->unverified()->create(['email' => 'admin@jadual.test']);
    fakeGoogleUser('admin@jadual.test');

    $this->get('/auth/google/callback')->assertRedirect(route('dashboard'));

    expect($user->fresh()->email_verified_at)->not->toBeNull();
});
