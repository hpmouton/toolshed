<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Notifications\BookingEndingReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * FR-10.2 — Dispatch reminder notifications to renters 48 hours before
 * the booking end date.
 */
class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';
    protected $description = 'Send reminder notifications for bookings ending within 48 hours';

    public function handle(): int
    {
        $cutoff = Carbon::now()->addHours(48);

        $bookings = Booking::query()
            ->with(['user', 'tool.depot'])
            ->where('booking_status', 'active')
            ->whereBetween('end_date', [
                Carbon::today()->toDateString(),
                $cutoff->toDateString(),
            ])
            ->get();

        $count = 0;

        foreach ($bookings as $booking) {
            if ($booking->user) {
                $booking->user->notify(new BookingEndingReminder($booking));
                $count++;
            }
        }

        $this->info("Sent {$count} reminder(s).");

        return self::SUCCESS;
    }
}
