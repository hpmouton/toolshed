<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * User internationalisation preferences:
     *   - preferred_currency : ISO 4217 code shown in all pricing displays
     *   - city / country_code: used to pre-filter the depot finder
     *   - latitude / longitude: optional precise location for proximity sort
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('preferred_currency', 3)->default('USD')->after('birth_year');
            $table->string('city')->nullable()->after('preferred_currency');
            $table->string('country_code', 2)->nullable()->after('city');
            $table->decimal('latitude',  10, 7)->nullable()->after('country_code');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['preferred_currency', 'city', 'country_code', 'latitude', 'longitude']);
        });
    }
};
