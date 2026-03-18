<?php

namespace Database\Factories;

use App\Models\Tool;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::today()->addDays($this->faker->numberBetween(1, 14));

        return [
            'tool_id'           => Tool::factory(),
            'user_id'           => User::factory(),
            'start_date'        => $start->toDateString(),
            'end_date'          => $start->addDays($this->faker->numberBetween(1, 7))->toDateString(),
            'booking_status'    => 'pending',
            'total_price_cents' => $this->faker->numberBetween(1000, 50000),
        ];
    }

    /**
     * State: booking is confirmed (tool should be Reserved).
     */
    public function confirmed(): static
    {
        return $this->state(['booking_status' => 'confirmed']);
    }

    /**
     * State: booking is active / in-progress (tool should be Out).
     */
    public function active(): static
    {
        return $this->state(function () {
            $start = Carbon::today()->subDays($this->faker->numberBetween(1, 3));

            return [
                'booking_status' => 'active',
                'start_date'     => $start->toDateString(),
                'end_date'       => Carbon::today()->toDateString(),
            ];
        });
    }

    /**
     * State: booking has been returned.
     */
    public function returned(): static
    {
        return $this->state(function () {
            $start = Carbon::today()->subDays(7);

            return [
                'booking_status' => 'returned',
                'start_date'     => $start->toDateString(),
                'end_date'       => Carbon::today()->subDay()->toDateString(),
            ];
        });
    }
}
