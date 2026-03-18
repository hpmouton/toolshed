<?php

namespace Database\Factories;

use App\Enums\ToolStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tool>
 */
class ToolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // FR-2.1 — SKU is unique by construction (UUID-based prefix + counter).
            'sku'                   => strtoupper('TOOL-' . $this->faker->unique()->bothify('??###')),
            'serial_number'         => $this->faker->optional()->bothify('SN-########'),
            'name'                  => $this->faker->words(3, true),
            'description'           => $this->faker->sentence(),
            // Pricing stored as cents (FR-3.3): e.g. $25.00/day → 2500
            'daily_rate_cents'      => $this->faker->numberBetween(500, 10000),
            'maintenance_fee_cents' => $this->faker->numberBetween(0, 2000),
            'status'                => ToolStatus::Available,
            'condition'             => $this->faker->randomElement(['new', 'good', 'fair', 'poor']),
            'last_serviced_date'    => $this->faker->optional()->dateTimeBetween('-6 months', 'now'),
            'weight_kg'             => $this->faker->optional()->randomFloat(2, 0.5, 50),
            'dimensions'            => $this->faker->optional()->numerify('##x##x## cm'),
            'user_id'               => null,
        ];
    }

    /**
     * State: tool is currently reserved.
     */
    public function reserved(): static
    {
        return $this->state(['status' => ToolStatus::Reserved]);
    }

    /**
     * State: tool is currently out on loan.
     */
    public function out(): static
    {
        return $this->state(['status' => ToolStatus::Out]);
    }

    /**
     * DR-2.2 — State: tool has been archived.
     */
    public function archived(): static
    {
        return $this->state(['status' => ToolStatus::Archived]);
    }
}
