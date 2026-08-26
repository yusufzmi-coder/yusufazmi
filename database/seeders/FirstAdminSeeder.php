<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Bootstraps the very first login on a fresh deployment.
 *
 * Runs only when ADMIN_EMAIL is set AND no users exist yet, so redeploying never
 * resurrects a deleted account or resets a password someone has since changed.
 */
class FirstAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('app.first_admin_email');
        $password = config('app.first_admin_password');

        if (blank($email)) {
            return;
        }

        if (User::query()->exists()) {
            $this->command->info('Pengguna sudah wujud — admin pertama dilangkau.');

            return;
        }

        if (blank($password)) {
            $this->command->warn('ADMIN_EMAIL ditetapkan tetapi ADMIN_PASSWORD tiada — admin pertama dilangkau.');

            return;
        }

        $user = User::create([
            'name' => config('app.first_admin_name') ?: 'Admin',
            'email' => mb_strtolower((string) $email),
            'password' => Hash::make((string) $password),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('Super Admin');

        $this->command->info("Admin pertama dicipta: {$user->email}");
    }
}
