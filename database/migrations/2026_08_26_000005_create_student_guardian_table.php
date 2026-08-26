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
        Schema::create('student_guardian', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('guardian_id')->constrained('guardians')->cascadeOnDelete();
            $table->string('relationship', 30);   // bapa|ibu|penjaga|datuk|nenek|lain
            $table->boolean('is_primary')->default(false);
            $table->boolean('can_pickup')->default(true);

            $table->primary(['student_id', 'guardian_id']);
            $table->index('guardian_id');
        });

        DB::statement('CREATE UNIQUE INDEX student_guardian_single_primary_idx ON student_guardian (student_id) WHERE is_primary');
    }

    public function down(): void
    {
        Schema::dropIfExists('student_guardian');
    }
};
