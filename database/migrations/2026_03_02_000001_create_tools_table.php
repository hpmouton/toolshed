<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tools', function (Blueprint $table) {
            $table->id();

            // FR-2.1 — every tool has a unique, human-readable SKU.
            // The database-level unique constraint ensures no two rows can ever
            // share the same SKU, regardless of tool name or category.
            $table->string('sku')->unique();

            $table->string('name');
            $table->text('description')->nullable();

            // FR-2.2 — current lifecycle status of the physical tool.
            $table->string('status')->default(\App\Enums\ToolStatus::Available->value);

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
