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
        // A move is never an UPDATE of class_id: the old row is closed and a new one
        // inserted, so "why is Ali in 5 BETA" stays answerable forever.
        Schema::create('enrolments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('academic_sessions')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->restrictOnDelete();
            $table->string('status', 12);        // active | moved | withdrawn
            $table->boolean('is_pinned')->default(false);   // engine must never move this student
            $table->string('source', 12);        // manual | auto | import
            $table->foreignId('assignment_run_id')->nullable()->constrained('assignment_runs')->nullOnDelete();
            $table->jsonb('placement_reason')->nullable();
            $table->text('placement_reason_text')->nullable();
            $table->decimal('placement_score', 8, 3)->nullable();
            $table->timestampTz('joined_at');
            $table->timestampTz('left_at')->nullable();
            $table->timestampsTz();

            $table->index('assignment_run_id');
        });

        DB::statement("ALTER TABLE enrolments ADD CONSTRAINT enrolments_status_check CHECK (status IN ('active','moved','withdrawn'))");
        DB::statement("ALTER TABLE enrolments ADD CONSTRAINT enrolments_source_check CHECK (source IN ('manual','auto','import'))");
        DB::statement("ALTER TABLE enrolments ADD CONSTRAINT enrolments_left_at_check CHECK ((status = 'active') = (left_at IS NULL))");

        // The single most important constraint in the schema. Locks give good error
        // messages; THIS is the correctness boundary -- the database physically
        // cannot double-enrol a student, even if every lock fails.
        DB::statement("CREATE UNIQUE INDEX enrolments_one_active_per_student_idx ON enrolments (session_id, student_id) WHERE status = 'active'");
        DB::statement("CREATE INDEX enrolments_class_active_idx ON enrolments (class_id) WHERE status = 'active'");
    }

    public function down(): void
    {
        Schema::dropIfExists('enrolments');
    }
};
