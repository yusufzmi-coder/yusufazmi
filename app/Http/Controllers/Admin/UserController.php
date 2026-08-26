<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

/**
 * The invite list. Google Sign-In refuses anyone who is not here, so this screen is
 * the only door into the system.
 */
class UserController
{
    public function index(): Response
    {
        return Inertia::render('pengguna/index', [
            'pengguna' => User::query()
                ->with('roles:id,name')
                ->orderBy('name')
                ->get()
                ->map(fn (User $u): array => [
                    'id' => $u->id,
                    'nama' => $u->name,
                    'emel' => $u->email,
                    'peranan' => $u->roles->pluck('name')->all(),
                    'disahkan' => $u->email_verified_at !== null,
                    'dijemput' => $u->created_at?->diffForHumans(),
                ])
                ->all(),
            'peranan' => Role::orderBy('name')->pluck('name')->all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => mb_strtolower($validated['email']),
            // No password is set: the invitee signs in with Google, or resets a
            // password through the normal flow. A random secret is never a login.
            'password' => Hash::make(Str::random(48)),
        ]);

        $user->assignRole($validated['role']);

        return back()->with('success', "{$user->email} dijemput sebagai {$validated['role']}.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak boleh membuang akaun anda sendiri.');
        }

        if ($user->hasRole('Super Admin') && User::role('Super Admin')->count() <= 1) {
            return back()->with('error', 'Mesti ada sekurang-kurangnya seorang Super Admin.');
        }

        $email = $user->email;
        $user->delete();

        return back()->with('success', "Akses {$email} dibuang.");
    }
}
