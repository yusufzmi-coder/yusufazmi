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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 150);                 // PII
            $table->string('phone', 20)->nullable();     // PII
            $table->string('email', 150)->nullable();    // PII
            // 1 Sangat Lembut .. 4 Tegas .. 5 Sangat Tegas. A boolean would make three
            // "tegas" teachers indistinguishable, so problem students would spread at random.
            $table->smallInteger('firmness')->default(3);
            $table->smallInteger('max_classes')->default(6);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();
        });

        DB::statement('ALTER TABLE teachers ADD CONSTRAINT teachers_firmness_check CHECK (firmness BETWEEN 1 AND 5)');
        DB::statement('CREATE INDEX teachers_firmness_idx ON teachers (firmness) WHERE is_active AND deleted_at IS NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
