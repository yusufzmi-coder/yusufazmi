<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AcademicSessionSeeder::class,
            TimeSlotSeeder::class,
            RuleSeeder::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@jadual.test'],
            ['name' => 'Admin User', 'password' => bcrypt('password')],
        );

        $admin->assignRole('Super Admin');

        if (app()->environment('local', 'testing') && ! app()->runningUnitTests()) {
            $this->call(DemoSeeder::class);
        }
    }
}
