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
        // A class is a cohort of students with one teacher. WHEN it meets lives in
        // `class_meetings` -- "4 ALPHA" runs on both Isnin and Khamis, so a single
        // day/slot column here would not survive contact with the real timetable.
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('academic_sessions')->restrictOnDelete();
            $table->smallInteger('year_level');
            $table->string('stream', 20);                       // ALPHA | BETA | GAMMA
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->restrictOnDelete();
            $table->smallInteger('capacity_override')->nullable(); // null => smallest room it meets in
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();
        });

        // Derived, so "4 ALPHA" can never drift out of sync with its parts.
        DB::statement("ALTER TABLE classes ADD COLUMN name varchar(30) GENERATED ALWAYS AS (year_level::text || ' ' || stream) STORED");

        DB::statement('ALTER TABLE classes ADD CONSTRAINT classes_year_level_check CHECK (year_level BETWEEN 1 AND 6)');
        DB::statement('CREATE UNIQUE INDEX classes_session_year_stream_unique ON classes (session_id, year_level, stream) WHERE deleted_at IS NULL');
        DB::statement('CREATE INDEX classes_open_idx ON classes (session_id, year_level) WHERE is_active AND deleted_at IS NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
