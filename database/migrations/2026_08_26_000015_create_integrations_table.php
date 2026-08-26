<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Credentials the admin fills in from the UI. `config` is encrypted at rest by
        // the model cast, and overrides .env at runtime so keys can change without a deploy.
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->string('key', 40)->unique();      // sendscape | cloudflare_r2 | google_oauth
            $table->boolean('enabled')->default(false);
            $table->text('config')->nullable();       // encrypted:array
            $table->string('last_test_status', 12)->nullable();  // ok | failed
            $table->text('last_test_message')->nullable();
            $table->timestampTz('last_tested_at')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
