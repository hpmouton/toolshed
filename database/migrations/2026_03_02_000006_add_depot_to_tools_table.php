<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Link each tool to the depot that holds it.
     * Also stores the currency the tool's rates were entered in so that
     * the pricing calculator can convert to the user's preferred currency.
     */
    public function up(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->foreignId('depot_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained()
                  ->nullOnDelete();

            // The ISO 4217 currency code the daily_rate_cents/maintenance_fee_cents
            // are denominated in (matches the depot's currency by default).
            $table->string('currency_code', 3)->default('USD')->after('depot_id');
        });
    }

    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropForeign(['depot_id']);
            $table->dropColumn(['depot_id', 'currency_code']);
        });
    }
};
