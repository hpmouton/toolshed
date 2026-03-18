<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,    // 20 users (3 fixed demo + 17 random)
            ToolSeeder::class,    // 60 tools across 9 categories
            DepotSeeder::class,   // 21 international depots; assigns tools to depots
            BookingSeeder::class, // rich mix of past / active / upcoming bookings
        ]);
    }
}
