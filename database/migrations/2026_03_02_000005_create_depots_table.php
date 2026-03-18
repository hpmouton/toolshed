<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Depots are physical locations (warehouses, stores, hubs) where tools
     * are stored.  Each depot has:
     *   - full address fields for display
     *   - latitude/longitude for proximity searches (haversine)
     *   - ISO 4217 currency_code so prices can be shown in the local currency
     *   - a contact email/phone for the location manager
     */
    public function up(): void
    {
        Schema::create('depots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('state_province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country_code', 2);    // ISO 3166-1 alpha-2
            $table->string('country_name');
            $table->string('currency_code', 3);   // ISO 4217, e.g. 'USD', 'EUR'

            // Geolocation — stored as DECIMAL for cross-DB compatibility
            $table->decimal('latitude',  10, 7);
            $table->decimal('longitude', 10, 7);

            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index('country_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depots');
    }
};
