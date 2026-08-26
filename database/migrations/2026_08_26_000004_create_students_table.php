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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->nullable()->constrained('families')->nullOnDelete();
            $table->string('student_code', 20);
            $table->string('name', 150);                     // PII
            $table->string('national_id', 255)->nullable();  // PII, encrypted cast
            $table->char('gender', 1);                       // L | P
            $table->date('date_of_birth')->nullable();       // PII
            $table->smallInteger('year_level');
            // Severity, not a boolean: when firm teachers are scarce the engine must
            // know who gets priority. 0 Normal, 1 Perlu Perhatian, 2 Bermasalah, 3 Kritikal.
            $table->smallInteger('behaviour_level')->default(0);
            $table->text('special_needs')->nullable();       // PII
            $table->string('phone', 20)->nullable();         // PII
            $table->boolean('is_active')->default(true);
            $table->date('enrolled_on');
            $table->softDeletesTz();
            $table->timestampsTz();

            $table->index('family_id');
        });

        DB::statement("ALTER TABLE students ADD CONSTRAINT students_gender_check CHECK (gender IN ('L','P'))");
        DB::statement('ALTER TABLE students ADD CONSTRAINT students_year_level_check CHECK (year_level BETWEEN 1 AND 6)');
        DB::statement('ALTER TABLE students ADD CONSTRAINT students_behaviour_level_check CHECK (behaviour_level BETWEEN 0 AND 3)');
        DB::statement('CREATE UNIQUE INDEX students_code_unique ON students (student_code) WHERE deleted_at IS NULL');
        // Exactly the predicate the solver snapshot reads with.
        DB::statement('CREATE INDEX students_assignable_idx ON students (year_level, id) WHERE is_active AND deleted_at IS NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
