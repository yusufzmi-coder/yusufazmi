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
        Schema::create('assignment_run_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_run_id')->constrained('assignment_runs')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('from_class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('to_class_id')->nullable()->constrained('classes')->nullOnDelete(); // null => unplaceable
            $table->string('action', 14);       // place|move|keep|pinned|unplaceable
            $table->decimal('score', 8, 3)->nullable();
            // Stored twice on purpose: `reasons` stays queryable and re-renderable,
            // `reason_text` is the frozen Malay sentence that survives rule edits.
            $table->jsonb('reasons')->default('[]');
            $table->text('reason_text')->nullable();
            $table->jsonb('violations')->default('[]');
            $table->integer('rank')->default(0);
            $table->timestampTz('created_at')->useCurrent();

            $table->unique(['assignment_run_id', 'student_id']);
            $table->index(['assignment_run_id', 'action']);
        });

        DB::statement("ALTER TABLE assignment_run_items ADD CONSTRAINT assignment_run_items_action_check CHECK (action IN ('place','move','keep','pinned','unplaceable'))");
        // Answers "show every placement caused by the firm-teacher rule" without a scan.
        DB::statement('CREATE INDEX run_items_reasons_gin ON assignment_run_items USING gin (reasons jsonb_path_ops)');
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_run_items');
    }
};
