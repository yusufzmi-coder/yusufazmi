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
        // The grid rows only. The day is an enum column on `classes` -- a five-row
        // lookup table would buy nothing.
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('label', 30);
            $table->time('starts_at');
            $table->time('ends_at');
            $table->boolean('is_break')->default(false);   // the REHAT row
            $table->smallInteger('sort_order');

            $table->unique('sort_order');
            $table->unique(['starts_at', 'ends_at']);
        });

        DB::statement('ALTER TABLE time_slots ADD CONSTRAINT time_slots_range_check CHECK (ends_at > starts_at)');
    }

    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
