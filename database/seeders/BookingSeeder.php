<?php

namespace Database\Seeders;

use App\Enums\ToolStatus;
use App\Models\Booking;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if bookings already exist (idempotent)
        if (Booking::count() > 0) {
            return;
        }

        $users = User::all();
        $today = Carbon::today();

        // ── 1. Past returned bookings (history / audit trail) ────────────────
        // Each of 15 random users has 1–4 completed rentals spread over the
        // last 90 days.
        $availableTools = Tool::where('status', ToolStatus::Available)->get();

        $users->random(15)->each(function (User $user) use ($availableTools, $today) {
            $count = rand(1, 4);
            $pool  = $availableTools->random(min($count, $availableTools->count()));

            foreach ($pool as $tool) {
                $daysAgo   = rand(5, 90);
                $duration  = rand(1, 7);
                $startDate = $today->copy()->subDays($daysAgo);
                $endDate   = $startDate->copy()->addDays($duration);

                // Keep end date in the past
                if ($endDate->gte($today)) {
                    $endDate = $today->copy()->subDay();
                }
                if ($endDate->lte($startDate)) {
                    continue;
                }

                Booking::create([
                    'tool_id'        => $tool->id,
                    'user_id'        => $user->id,
                    'start_date'     => $startDate->toDateString(),
                    'end_date'       => $endDate->toDateString(),
                    'booking_status' => 'returned',
                ]);
            }
        });

        // ── 2. Active bookings (currently out on loan) ───────────────────────
        // Use the two tools already set to Out status from ToolSeeder.
        $outTools = Tool::where('status', ToolStatus::Out)->get();

        $outTools->each(function (Tool $tool) use ($users, $today) {
            $user = $users->random();
            Booking::create([
                'tool_id'        => $tool->id,
                'user_id'        => $user->id,
                'start_date'     => $today->copy()->subDays(rand(1, 3))->toDateString(),
                'end_date'       => $today->copy()->addDays(rand(1, 4))->toDateString(),
                'booking_status' => 'active',
            ]);
        });

        // ── 3. Confirmed upcoming bookings (tools marked Reserved) ───────────
        $reservedTools = Tool::where('status', ToolStatus::Reserved)->get();

        $reservedTools->each(function (Tool $tool) use ($users, $today) {
            $user  = $users->random();
            $start = $today->copy()->addDays(rand(1, 7));
            $end   = $start->copy()->addDays(rand(2, 10));
            Booking::create([
                'tool_id'        => $tool->id,
                'user_id'        => $user->id,
                'start_date'     => $start->toDateString(),
                'end_date'       => $end->toDateString(),
                'booking_status' => 'confirmed',
            ]);
        });

        // ── 4. Future pending bookings for the fixed demo accounts ───────────
        // Bob and Carol each have a pending booking for a different available tool.
        $bob   = User::where('email', 'bob@toolshed.test')->first();
        $carol = User::where('email', 'carol@toolshed.test')->first();

        $demoTools = Tool::where('status', ToolStatus::Available)
            ->inRandomOrder()
            ->take(4)
            ->get();

        if ($bob && $demoTools->count() >= 2) {
            Booking::create([
                'tool_id'        => $demoTools[0]->id,
                'user_id'        => $bob->id,
                'start_date'     => $today->copy()->addDays(3)->toDateString(),
                'end_date'       => $today->copy()->addDays(8)->toDateString(),
                'booking_status' => 'pending',
            ]);

            Booking::create([
                'tool_id'        => $demoTools[1]->id,
                'user_id'        => $bob->id,
                'start_date'     => $today->copy()->addDays(14)->toDateString(),
                'end_date'       => $today->copy()->addDays(17)->toDateString(),
                'booking_status' => 'pending',
            ]);
        }

        if ($carol && $demoTools->count() >= 4) {
            Booking::create([
                'tool_id'        => $demoTools[2]->id,
                'user_id'        => $carol->id,
                'start_date'     => $today->copy()->addDays(5)->toDateString(),
                'end_date'       => $today->copy()->addDays(12)->toDateString(),
                'booking_status' => 'pending',
            ]);

            Booking::create([
                'tool_id'        => $demoTools[3]->id,
                'user_id'        => $carol->id,
                'start_date'     => $today->copy()->subDays(10)->toDateString(),
                'end_date'       => $today->copy()->subDays(3)->toDateString(),
                'booking_status' => 'returned',
            ]);
        }
    }
}
