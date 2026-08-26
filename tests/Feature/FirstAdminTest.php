<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\FirstAdminSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

it('creates the first Super Admin from the environment', function () {
    Config::set('app.first_admin_email', 'Boss@Sekolah.My');
    Config::set('app.first_admin_password', 'rahsia-yang-panjang');
    Config::set('app.first_admin_name', 'Ketua Admin');

    $this->seed(FirstAdminSeeder::class);

    $user = User::first();

    expect($user->email)->toBe('boss@sekolah.my')
        ->and($user->hasRole('Super Admin'))->toBeTrue()
        ->and(Hash::check('rahsia-yang-panjang', $user->password))->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull();
});

it('never resurrects an account once any user exists', function () {
    // A redeploy runs the seeder again. If it recreated the admin, removing someone's
    // access would silently undo itself on the next deploy.
    User::factory()->create(['email' => 'sedia.ada@sekolah.my']);

    Config::set('app.first_admin_email', 'boss@sekolah.my');
    Config::set('app.first_admin_password', 'rahsia');

    $this->seed(FirstAdminSeeder::class);

    expect(User::count())->toBe(1)
        ->and(User::first()->email)->toBe('sedia.ada@sekolah.my');
});

it('does nothing when the environment is not configured', function () {
    Config::set('app.first_admin_email', null);

    $this->seed(FirstAdminSeeder::class);

    expect(User::count())->toBe(0);
});

it('refuses to create an admin without a password', function () {
    Config::set('app.first_admin_email', 'boss@sekolah.my');
    Config::set('app.first_admin_password', null);

    $this->seed(FirstAdminSeeder::class);

    expect(User::count())->toBe(0);
});

it('creates an admin from the command line with a generated password', function () {
    $this->artisan('admin:create', ['email' => 'cikgu@sekolah.my', '--name' => 'Cikgu Admin'])
        ->assertSuccessful();

    expect(User::where('email', 'cikgu@sekolah.my')->first()->hasRole('Super Admin'))->toBeTrue();
});

it('refuses a duplicate email from the command line', function () {
    User::factory()->create(['email' => 'ada@sekolah.my']);

    $this->artisan('admin:create', ['email' => 'ada@sekolah.my', '--name' => 'X'])
        ->assertFailed();

    expect(User::count())->toBe(1);
});
