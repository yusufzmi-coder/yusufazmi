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
        // One row per cell in the weekly grid.
        Schema::create('class_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            // session_id and teacher_id are denormalised from the parent class purely
            // so the clash indexes below can exist at all -- a partial unique index
            // cannot reach across a foreign key. Kept in sync by ClassMeetingWriter.
            $table->foreignId('session_id')->constrained('academic_sessions')->restrictOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->restrictOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->restrictOnDelete();
            $table->string('day', 8);
            $table->foreignId('time_slot_id')->constrained('time_slots')->restrictOnDelete();
            $table->timestampsTz();
        });

        DB::statement("ALTER TABLE class_meetings ADD CONSTRAINT class_meetings_day_check CHECK (day IN ('isnin','selasa','rabu','khamis','jumaat'))");

        // A class cannot meet twice in the same slot.
        DB::statement('CREATE UNIQUE INDEX class_meetings_unique ON class_meetings (class_id, day, time_slot_id)');

        // These two ARE the whole clash-detection requirement. A 23505 from either is
        // caught in the FormRequest and rendered as a Malay sentence.
        DB::statement('CREATE UNIQUE INDEX class_meetings_room_clash_idx ON class_meetings (session_id, day, time_slot_id, room_id) WHERE room_id IS NOT NULL');
        DB::statement('CREATE UNIQUE INDEX class_meetings_teacher_clash_idx ON class_meetings (session_id, day, time_slot_id, teacher_id) WHERE teacher_id IS NOT NULL');

        DB::statement('CREATE INDEX class_meetings_grid_idx ON class_meetings (session_id, day, time_slot_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('class_meetings');
    }
};
