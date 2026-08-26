<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Immutable audit: one row per dry-run. No soft deletes.
        Schema::create('assignment_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('academic_sessions')->restrictOnDelete();
            $table->string('mode', 12);              // fill_only | rebalance
            $table->string('status', 12);            // running|draft|committed|discarded|expired|failed|reverted
            $table->char('input_hash', 64);          // sha256 of the canonical snapshot
            $table->jsonb('rules_snapshot');
            $table->decimal('objective_score', 10, 3)->nullable();
            $table->jsonb('stats')->default('{}');
            $table->integer('seed')->default(0);
            $table->integer('duration_ms')->nullable();
            $table->text('error')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestampTz('committed_at')->nullable();
            $table->foreignId('committed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();

            $table->index(['session_id', 'created_at']);
        });

        DB::statement("ALTER TABLE assignment_runs ADD CONSTRAINT assignment_runs_mode_check CHECK (mode IN ('fill_only','rebalance'))");
        DB::statement("ALTER TABLE assignment_runs ADD CONSTRAINT assignment_runs_status_check CHECK (status IN ('running','draft','committed','discarded','expired','failed','reverted'))");

        // At most one run in flight per session -- a cheap second line of defence
        // behind the advisory lock.
        DB::statement("CREATE UNIQUE INDEX assignment_runs_single_running_idx ON assignment_runs (session_id) WHERE status = 'running'");
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_runs');
    }
};
