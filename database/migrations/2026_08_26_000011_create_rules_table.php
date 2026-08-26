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
        // Rows are instances (on/off, weight, config). The evaluation logic is code,
        // registered in config/assignment.php.
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->nullable()->constrained('academic_sessions')->cascadeOnDelete();
            $table->string('rule_type', 60);         // RuleType::key()
            $table->string('name', 120);             // admin-editable Malay label
            $table->string('kind', 4);               // hard | soft -- written from the registry, never from the request
            $table->boolean('is_active')->default(true);
            $table->smallInteger('weight')->default(50);
            $table->jsonb('config')->default('{}');
            $table->jsonb('applies_to')->default('{}');
            $table->smallInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletesTz();
            $table->timestampsTz();
        });

        DB::statement('ALTER TABLE rules ADD CONSTRAINT rules_weight_check CHECK (weight BETWEEN 0 AND 100)');
        DB::statement("ALTER TABLE rules ADD CONSTRAINT rules_kind_check CHECK (kind IN ('hard','soft'))");
        DB::statement('CREATE INDEX rules_active_idx ON rules (session_id, sort_order) WHERE is_active AND deleted_at IS NULL');

        // Some rule types make no sense twice. The registry's isSingleton() is the
        // primary guard; this is the database-level backstop for the known ones.
        DB::statement("CREATE UNIQUE INDEX rules_singleton_idx ON rules (session_id, rule_type)
            WHERE is_active AND deleted_at IS NULL
              AND rule_type IN ('had_maksimum_kelas','seimbangkan_jantina','kelas_adik_beradik','pelajar_bermasalah')");
    }

    public function down(): void
    {
        Schema::dropIfExists('rules');
    }
};
