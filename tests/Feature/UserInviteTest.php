<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\AcademicSessionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(AcademicSessionSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');
});

it('invites a user with a role', function () {
    $this->actingAs($this->admin)
        ->post('/pengguna', ['name' => 'Cikgu Farah', 'email' => 'Farah@Sekolah.My', 'role' => 'Admin'])
        ->assertRedirect();

    $user = User::where('email', 'farah@sekolah.my')->first();

    // Stored lower-cased so the Google callback's case-insensitive match always lands.
    expect($user)->not->toBeNull()
        ->and($user->hasRole('Admin'))->toBeTrue();
});

it('refuses a duplicate email', function () {
    $this->actingAs($this->admin)
        ->post('/pengguna', ['name' => 'Dup', 'email' => $this->admin->email, 'role' => 'Admin'])
        ->assertSessionHasErrors('email');
});

it('refuses an unknown role', function () {
    $this->actingAs($this->admin)
        ->post('/pengguna', ['name' => 'X', 'email' => 'x@y.my', 'role' => 'Tuhan'])
        ->assertSessionHasErrors('role');
});

it('stops you removing your own access', function () {
    $this->actingAs($this->admin)
        ->delete("/pengguna/{$this->admin->id}")
        ->assertSessionHas('error');

    expect(User::find($this->admin->id))->not->toBeNull();
});

it('refuses to remove the last Super Admin', function () {
    $other = User::factory()->create();
    $other->assignRole('Super Admin');
    $this->admin->syncRoles(['Admin']);

    $this->actingAs($this->admin)
        ->delete("/pengguna/{$other->id}")
        ->assertSessionHas('error');

    expect(User::find($other->id))->not->toBeNull();
});
