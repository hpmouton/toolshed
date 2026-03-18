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
        Schema::table('tools', function (Blueprint $table) {
            // Stored as integer cents to avoid floating-point precision issues.
            // e.g. $25.00/day → 2500,  $10.50 maintenance → 1050
            $table->unsignedInteger('daily_rate_cents')->default(0)->after('description');
            $table->unsignedInteger('maintenance_fee_cents')->default(0)->after('daily_rate_cents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn(['daily_rate_cents', 'maintenance_fee_cents']);
        });
    }
};
