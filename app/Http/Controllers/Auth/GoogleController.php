<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Integrations\IntegrationManager;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Throwable;

/**
 * Google Sign-In is invitation-only: an account must already exist before anyone
 * can sign in with it. A valid Google login proves identity, not authorisation,
 * and this system holds children's records.
 */
class GoogleController
{
    public function redirect(IntegrationManager $manager): SymfonyRedirect|RedirectResponse
    {
        if (! $this->configure($manager)) {
            return to_route('login')->withErrors([
                'email' => 'Google Sign-In belum dikonfigurasikan. Sila hubungi Super Admin.',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(IntegrationManager $manager): RedirectResponse
    {
        if (! $this->configure($manager)) {
            return to_route('login')->withErrors(['email' => 'Google Sign-In belum dikonfigurasikan.']);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return to_route('login')->withErrors(['email' => 'Log masuk Google gagal. Sila cuba lagi.']);
        }

        $email = $googleUser->getEmail();

        if (blank($email)) {
            return to_route('login')->withErrors(['email' => 'Akaun Google itu tiada alamat emel.']);
        }

        $user = User::whereRaw('lower(email) = ?', [mb_strtolower($email)])->first();

        // No silent registration: an unknown Google account is simply refused.
        if ($user === null) {
            return to_route('login')->withErrors([
                'email' => "Akaun {$email} tiada akses. Minta Super Admin menjemput anda dahulu.",
            ]);
        }

        if ($user->email_verified_at === null) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        return to_route('dashboard');
    }

    /** Credentials come from the admin widget first, then .env. */
    private function configure(IntegrationManager $manager): bool
    {
        $config = $manager->config('google_oauth');

        if (blank($config['client_id'] ?? null) || blank($config['client_secret'] ?? null)) {
            return false;
        }

        Config::set('services.google', [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'redirect' => $config['redirect'] ?: route('auth.google.callback'),
        ]);

        return true;
    }
}
