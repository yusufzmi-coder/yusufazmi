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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->smallInteger('capacity');
            $table->string('location', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletesTz();
            $table->timestampsTz();
        });

        DB::statement('ALTER TABLE rooms ADD CONSTRAINT rooms_capacity_check CHECK (capacity > 0)');
        DB::statement('CREATE UNIQUE INDEX rooms_name_unique ON rooms (name) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
