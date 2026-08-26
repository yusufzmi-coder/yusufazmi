<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Assignment\Enums\RunStatus;
use App\Models\AssignmentRun;
use Illuminate\Console\Command;

class ExpireAssignmentDrafts extends Command
{
    protected $signature = 'assign:expire-drafts';

    protected $description = 'Tandakan draf auto assign yang sudah lapuk sebagai luput.';

    public function handle(): int
    {
        $ttl = (int) config('assignment.solver.draft_ttl_minutes', 30);

        $expired = AssignmentRun::query()
            ->whereIn('status', [RunStatus::Draft->value, RunStatus::Running->value])
            ->where('created_at', '<', now()->subMinutes($ttl))
            ->update(['status' => RunStatus::Expired->value, 'updated_at' => now()]);

        $this->info("{$expired} draf ditandakan luput.");

        return self::SUCCESS;
    }
}
