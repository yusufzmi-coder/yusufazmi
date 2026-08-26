<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /** Grouped so the intent stays readable as the list grows. */
    private const PERMISSIONS = [
        'pelajar.lihat', 'pelajar.urus',
        'guru.lihat', 'guru.urus',
        'bilik.lihat', 'bilik.urus',
        'kelas.lihat', 'kelas.urus',
        'jadual.lihat',
        'peraturan.lihat', 'peraturan.urus',
        'auto-assign.jalankan', 'auto-assign.sahkan',
        'laporan.lihat',
        'integrasi.urus',
        'pengguna.urus',
        'log.lihat',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $name) {
            Permission::findOrCreate($name, 'web');
        }

        // Full access, including inviting other admins and editing credentials.
        Role::findOrCreate('Super Admin', 'web')->syncPermissions(self::PERMISSIONS);

        // Day-to-day operations, but cannot manage users or integration secrets.
        Role::findOrCreate('Admin', 'web')->syncPermissions(array_diff(
            self::PERMISSIONS,
            ['integrasi.urus', 'pengguna.urus'],
        ));

        // Read-only.
        Role::findOrCreate('Guru', 'web')->syncPermissions([
            'pelajar.lihat', 'kelas.lihat', 'jadual.lihat', 'laporan.lihat',
        ]);
    }
}
