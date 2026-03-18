<?php

namespace Database\Seeders;

use App\Enums\ToolStatus;
use App\Models\Depot;
use App\Models\Tool;
use Illuminate\Database\Seeder;

class DepotSeeder extends Seeder
{
    public function run(): void
    {
        $depots = [
            // ── North America ─────────────────────────────────────────────────
            [
                'name'          => 'ToolShed New York',
                'address_line1' => '245 W 17th St',
                'city'          => 'New York',
                'state_province'=> 'NY',
                'postal_code'   => '10011',
                'country_code'  => 'US',
                'country_name'  => 'United States',
                'currency_code' => 'USD',
                'latitude'      => 40.7415,
                'longitude'     => -74.0007,
                'phone'         => '+1-212-555-0101',
                'email'         => 'nyc@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Los Angeles',
                'address_line1' => '1801 Century Park E',
                'city'          => 'Los Angeles',
                'state_province'=> 'CA',
                'postal_code'   => '90067',
                'country_code'  => 'US',
                'country_name'  => 'United States',
                'currency_code' => 'USD',
                'latitude'      => 34.0573,
                'longitude'     => -118.4168,
                'phone'         => '+1-310-555-0202',
                'email'         => 'lax@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Chicago',
                'address_line1' => '875 N Michigan Ave',
                'city'          => 'Chicago',
                'state_province'=> 'IL',
                'postal_code'   => '60611',
                'country_code'  => 'US',
                'country_name'  => 'United States',
                'currency_code' => 'USD',
                'latitude'      => 41.8977,
                'longitude'     => -87.6240,
                'phone'         => '+1-312-555-0303',
                'email'         => 'chi@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Toronto',
                'address_line1' => '100 King St W',
                'city'          => 'Toronto',
                'state_province'=> 'ON',
                'postal_code'   => 'M5X 1A9',
                'country_code'  => 'CA',
                'country_name'  => 'Canada',
                'currency_code' => 'CAD',
                'latitude'      => 43.6479,
                'longitude'     => -79.3840,
                'phone'         => '+1-416-555-0404',
                'email'         => 'tor@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Vancouver',
                'address_line1' => '1055 W Georgia St',
                'city'          => 'Vancouver',
                'state_province'=> 'BC',
                'postal_code'   => 'V6E 3P3',
                'country_code'  => 'CA',
                'country_name'  => 'Canada',
                'currency_code' => 'CAD',
                'latitude'      => 49.2846,
                'longitude'     => -123.1220,
                'phone'         => '+1-604-555-0505',
                'email'         => 'van@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Mexico City',
                'address_line1' => 'Paseo de la Reforma 222',
                'city'          => 'Mexico City',
                'state_province'=> 'CDMX',
                'postal_code'   => '06600',
                'country_code'  => 'MX',
                'country_name'  => 'Mexico',
                'currency_code' => 'MXN',
                'latitude'      => 19.4270,
                'longitude'     => -99.1676,
                'phone'         => '+52-55-5555-0606',
                'email'         => 'mex@toolshed.test',
            ],

            // ── Europe ───────────────────────────────────────────────────────
            [
                'name'          => 'ToolShed London',
                'address_line1' => '30 St Mary Axe',
                'city'          => 'London',
                'postal_code'   => 'EC3A 8BF',
                'country_code'  => 'GB',
                'country_name'  => 'United Kingdom',
                'currency_code' => 'GBP',
                'latitude'      => 51.5144,
                'longitude'     => -0.0804,
                'phone'         => '+44-20-5555-0707',
                'email'         => 'lon@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Paris',
                'address_line1' => '1 Place du Parvis Notre-Dame',
                'city'          => 'Paris',
                'postal_code'   => '75004',
                'country_code'  => 'FR',
                'country_name'  => 'France',
                'currency_code' => 'EUR',
                'latitude'      => 48.8530,
                'longitude'     => 2.3499,
                'phone'         => '+33-1-5555-0808',
                'email'         => 'par@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Berlin',
                'address_line1' => 'Unter den Linden 77',
                'city'          => 'Berlin',
                'postal_code'   => '10117',
                'country_code'  => 'DE',
                'country_name'  => 'Germany',
                'currency_code' => 'EUR',
                'latitude'      => 52.5166,
                'longitude'     => 13.3806,
                'phone'         => '+49-30-5555-0909',
                'email'         => 'ber@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Amsterdam',
                'address_line1' => 'Damrak 1',
                'city'          => 'Amsterdam',
                'postal_code'   => '1012 LG',
                'country_code'  => 'NL',
                'country_name'  => 'Netherlands',
                'currency_code' => 'EUR',
                'latitude'      => 52.3738,
                'longitude'     => 4.8910,
                'phone'         => '+31-20-5555-1010',
                'email'         => 'ams@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Stockholm',
                'address_line1' => 'Sergels Torg 1',
                'city'          => 'Stockholm',
                'postal_code'   => '111 57',
                'country_code'  => 'SE',
                'country_name'  => 'Sweden',
                'currency_code' => 'SEK',
                'latitude'      => 59.3328,
                'longitude'     => 18.0645,
                'phone'         => '+46-8-5555-1111',
                'email'         => 'sto@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Zurich',
                'address_line1' => 'Bahnhofstrasse 1',
                'city'          => 'Zurich',
                'postal_code'   => '8001',
                'country_code'  => 'CH',
                'country_name'  => 'Switzerland',
                'currency_code' => 'CHF',
                'latitude'      => 47.3769,
                'longitude'     => 8.5417,
                'phone'         => '+41-44-5555-1212',
                'email'         => 'zur@toolshed.test',
            ],

            // ── Middle East ───────────────────────────────────────────────────
            [
                'name'          => 'ToolShed Dubai',
                'address_line1' => 'Sheikh Zayed Rd, DIFC',
                'city'          => 'Dubai',
                'country_code'  => 'AE',
                'country_name'  => 'United Arab Emirates',
                'currency_code' => 'AED',
                'latitude'      => 25.2048,
                'longitude'     => 55.2708,
                'phone'         => '+971-4-555-1313',
                'email'         => 'dxb@toolshed.test',
            ],

            // ── Asia-Pacific ──────────────────────────────────────────────────
            [
                'name'          => 'ToolShed Singapore',
                'address_line1' => '1 Raffles Place',
                'city'          => 'Singapore',
                'postal_code'   => '048616',
                'country_code'  => 'SG',
                'country_name'  => 'Singapore',
                'currency_code' => 'SGD',
                'latitude'      => 1.2847,
                'longitude'     => 103.8514,
                'phone'         => '+65-6555-1414',
                'email'         => 'sin@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Sydney',
                'address_line1' => '1 Macquarie St',
                'city'          => 'Sydney',
                'state_province'=> 'NSW',
                'postal_code'   => '2000',
                'country_code'  => 'AU',
                'country_name'  => 'Australia',
                'currency_code' => 'AUD',
                'latitude'      => -33.8688,
                'longitude'     => 151.2093,
                'phone'         => '+61-2-5555-1515',
                'email'         => 'syd@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Melbourne',
                'address_line1' => '120 Collins St',
                'city'          => 'Melbourne',
                'state_province'=> 'VIC',
                'postal_code'   => '3000',
                'country_code'  => 'AU',
                'country_name'  => 'Australia',
                'currency_code' => 'AUD',
                'latitude'      => -37.8136,
                'longitude'     => 144.9631,
                'phone'         => '+61-3-5555-1616',
                'email'         => 'mel@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Tokyo',
                'address_line1' => '2-1 Marunouchi, Chiyoda',
                'city'          => 'Tokyo',
                'postal_code'   => '100-0005',
                'country_code'  => 'JP',
                'country_name'  => 'Japan',
                'currency_code' => 'JPY',
                'latitude'      => 35.6812,
                'longitude'     => 139.7671,
                'phone'         => '+81-3-5555-1717',
                'email'         => 'tyo@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Mumbai',
                'address_line1' => 'Nariman Point',
                'city'          => 'Mumbai',
                'state_province'=> 'Maharashtra',
                'postal_code'   => '400021',
                'country_code'  => 'IN',
                'country_name'  => 'India',
                'currency_code' => 'INR',
                'latitude'      => 18.9220,
                'longitude'     => 72.8347,
                'phone'         => '+91-22-5555-1818',
                'email'         => 'bom@toolshed.test',
            ],

            // ── Africa ────────────────────────────────────────────────────────
            [
                'name'          => 'ToolShed Cape Town',
                'address_line1' => '1 Heerengracht St',
                'city'          => 'Cape Town',
                'state_province'=> 'Western Cape',
                'postal_code'   => '8001',
                'country_code'  => 'ZA',
                'country_name'  => 'South Africa',
                'currency_code' => 'ZAR',
                'latitude'      => -33.9249,
                'longitude'     => 18.4241,
                'phone'         => '+27-21-555-1919',
                'email'         => 'cpt@toolshed.test',
            ],
            [
                'name'          => 'ToolShed Johannesburg',
                'address_line1' => '1 Sandton Dr, Sandton',
                'city'          => 'Johannesburg',
                'state_province'=> 'Gauteng',
                'postal_code'   => '2196',
                'country_code'  => 'ZA',
                'country_name'  => 'South Africa',
                'currency_code' => 'ZAR',
                'latitude'      => -26.1076,
                'longitude'     => 28.0567,
                'phone'         => '+27-11-555-2020',
                'email'         => 'jnb@toolshed.test',
            ],

            // ── South America ─────────────────────────────────────────────────
            [
                'name'          => 'ToolShed São Paulo',
                'address_line1' => 'Av. Paulista 1374',
                'city'          => 'São Paulo',
                'state_province'=> 'SP',
                'postal_code'   => '01310-100',
                'country_code'  => 'BR',
                'country_name'  => 'Brazil',
                'currency_code' => 'BRL',
                'latitude'      => -23.5613,
                'longitude'     => -46.6562,
                'phone'         => '+55-11-5555-2121',
                'email'         => 'gru@toolshed.test',
            ],
        ];

        foreach ($depots as $data) {
            Depot::firstOrCreate(
                ['name' => $data['name']],
                $data,
            );
        }

        // Distribute existing tools evenly across depots, matching currency
        $allDepots  = Depot::all()->keyBy('currency_code');
        $allTools   = Tool::whereNull('depot_id')->get();
        $depotList  = Depot::all()->values();
        $depotCount = $depotList->count();

        $allTools->each(function (Tool $tool) use ($depotList, $depotCount) {
            $depot = $depotList[$tool->id % $depotCount];
            $tool->update([
                'depot_id'      => $depot->id,
                'currency_code' => $depot->currency_code,
            ]);
        });

        // Assign the staff demo user (Bob) to the first depot
        $bob = \App\Models\User::where('email', 'bob@toolshed.test')->first();
        if ($bob && ! $bob->depot_id) {
            $bob->update(['depot_id' => $depotList->first()->id]);
        }
    }
}
