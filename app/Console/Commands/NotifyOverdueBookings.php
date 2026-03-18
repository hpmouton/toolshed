<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Notifications\BookingOverdue;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * FR-10.3 — If a booking remains in active status more than 24 hours
 * past its end date, notify the renter and the depot.
 */
class NotifyOverdueBookings extends Command
{
    protected $signature = 'bookings:notify-overdue';
    protected $description = 'Notify renters and depots about overdue bookings';

    public function handle(): int
    {
        $cutoff = Carbon::today()->subDays(7)->toDateString();

        $bookings = Booking::query()
            ->with(['user', 'tool.depot'])
            ->where('booking_status', 'active')
            ->where('end_date', '<', $cutoff)
            ->get();

        $count = 0;

        foreach ($bookings as $booking) {
            // Notify the renter
            if ($booking->user) {
                $booking->user->notify(new BookingOverdue($booking));
                $count++;
            }

            // Notify the depot contact if email is set
            $depotEmail = $booking->tool->depot?->email;
            if ($depotEmail) {
                \Illuminate\Support\Facades\Mail::raw(
                    __("Overdue booking #:id — :tool (:sku) was due on :date.", [
                        'id'   => $booking->id,
                        'tool' => $booking->tool->name,
                        'sku'  => $booking->tool->sku,
                        'date' => $booking->end_date->format('Y-m-d'),
                    ]),
                    fn ($msg) => $msg->to($depotEmail)->subject(__('Overdue Booking Alert'))
                );
            }
        }

        $this->info("Notified {$count} overdue booking(s).");

        return self::SUCCESS;
    }
}
