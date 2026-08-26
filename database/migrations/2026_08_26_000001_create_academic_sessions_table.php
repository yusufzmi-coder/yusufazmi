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
        Schema::create('academic_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->date('starts_on');
            $table->date('ends_on');
            $table->boolean('is_active')->default(false);
            $table->timestampsTz();
        });

        // Only one session may ever be active. Enforced by the database, not by hope.
        DB::statement('CREATE UNIQUE INDEX academic_sessions_single_active_idx ON academic_sessions (is_active) WHERE is_active');
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_sessions');
    }
};
