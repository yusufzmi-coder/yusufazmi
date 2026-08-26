<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);                                  // PII
            // Sibling truth lives here, not in the guardian pivot: the "penjaga" on a
            // form is often a van driver or agent listed against unrelated children.
            $table->string('sibling_policy', 16)->default('inherit');     // inherit|together|apart
            $table->text('address')->nullable();                          // PII
            $table->text('notes')->nullable();                            // PII
            $table->softDeletesTz();
            $table->timestampsTz();

            $table->index('sibling_policy');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('families');
    }
};
