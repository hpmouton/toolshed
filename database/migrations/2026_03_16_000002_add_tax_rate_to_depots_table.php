<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FR-5.7 — Add a per-depot tax_rate column.
     * Stored as a decimal fraction (e.g. 0.15 for 15%). Default 0.15 (Namibia VAT).
     */
    public function up(): void
    {
        Schema::table('depots', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 4)->default(0.15)->after('currency_code');
        });
    }

    public function down(): void
    {
        Schema::table('depots', function (Blueprint $table) {
            $table->dropColumn('tax_rate');
        });
    }
};
