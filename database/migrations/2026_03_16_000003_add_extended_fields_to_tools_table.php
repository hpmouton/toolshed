<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FR-8.3 — Tool records shall support the following additional fields:
     * serial number, condition (new, good, fair, poor), last serviced date,
     * weight, and dimensions.
     */
    public function up(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->string('serial_number')->nullable()->after('sku');
            $table->string('condition')->default('new')->after('status');  // new, good, fair, poor
            $table->date('last_serviced_date')->nullable()->after('condition');
            $table->decimal('weight_kg', 8, 2)->nullable()->after('last_serviced_date');
            $table->string('dimensions')->nullable()->after('weight_kg'); // e.g. "30x20x15 cm"
        });
    }

    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn([
                'serial_number',
                'condition',
                'last_serviced_date',
                'weight_kg',
                'dimensions',
            ]);
        });
    }
};
