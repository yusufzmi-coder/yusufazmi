<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->nullable()->constrained('families')->nullOnDelete();
            $table->string('name', 150);                    // PII
            $table->string('phone', 20);                    // PII, normalised to +60...
            $table->string('email', 150)->nullable();       // PII
            $table->string('national_id', 255)->nullable(); // PII, encrypted cast (not searchable)
            $table->string('occupation', 100)->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();

            $table->index('family_id');
            $table->index('phone');                          // dedupe lookup on create
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
