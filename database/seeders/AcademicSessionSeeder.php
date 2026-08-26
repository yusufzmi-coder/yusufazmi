<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AcademicSession;
use Illuminate\Database\Seeder;

/**
 * Part of the BASE seed, not the demo data: every install needs an active session
 * or the dashboard has nothing to scope to.
 */
class AcademicSessionSeeder extends Seeder
{
    public function run(): void
    {
        if (AcademicSession::where('is_active', true)->exists()) {
            return;
        }

        AcademicSession::updateOrCreate(
            ['name' => (string) now()->year],
            [
                'starts_on' => now()->startOfYear(),
                'ends_on' => now()->endOfYear(),
                'is_active' => true,
            ],
        );
    }
}
