<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create
        {email? : Alamat emel admin}
        {--name= : Nama penuh}
        {--password= : Kata laluan (dijana jika tidak diberi)}
        {--role=Super Admin : Peranan}';

    protected $description = 'Cipta akaun admin. Guna sekali selepas deploy pertama.';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Emel admin');
        $name = $this->option('name') ?: $this->ask('Nama penuh', 'Admin');
        $role = (string) $this->option('role');

        // A generated password is safer than a default one: it cannot be guessed from
        // documentation, and it is shown exactly once.
        $password = $this->option('password') ?: Str::password(16);
        $generated = ! $this->option('password');

        $validator = Validator::make(
            ['email' => mb_strtolower((string) $email), 'name' => $name, 'role' => $role],
            [
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'name' => ['required', 'string', 'max:255'],
                'role' => ['required', Rule::exists('roles', 'name')],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => mb_strtolower((string) $email),
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($role);

        $this->newLine();
        $this->info("Akaun {$role} dicipta.");
        $this->table(['Emel', 'Kata Laluan'], [[$user->email, $generated ? $password : '(seperti diberi)']]);

        if ($generated) {
            $this->warn('Simpan kata laluan ini sekarang — ia tidak akan dipaparkan lagi.');
        }

        return self::SUCCESS;
    }
}
