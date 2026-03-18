<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Depot>
 */
class DepotFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'           => $this->faker->company() . ' Toolshed',
            'address_line1'  => $this->faker->streetAddress(),
            'city'           => $this->faker->city(),
            'country_code'   => 'US',
            'country_name'   => 'United States',
            'currency_code'  => 'USD',
            'tax_rate'       => 0.15,
            'latitude'       => $this->faker->latitude(25, 49),
            'longitude'      => $this->faker->longitude(-125, -66),
            'is_active'      => true,
        ];
    }
}
