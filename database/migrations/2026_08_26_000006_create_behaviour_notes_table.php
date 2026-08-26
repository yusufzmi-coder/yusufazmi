<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Free-text behaviour notes are the most sensitive PII in the system and need
        // authorship + history, so they live apart from the students row.
        Schema::create('behaviour_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->smallInteger('level_at_time');
            $table->text('body');                 // PII, highest sensitivity
            $table->date('occurred_on');
            $table->timestampsTz();

            $table->index(['student_id', 'occurred_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('behaviour_notes');
    }
};
