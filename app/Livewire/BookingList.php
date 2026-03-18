<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Services\ToolStatusTransitioner;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use LogicException;

#[Title('My Bookings')]
#[Layout('layouts.app')]
class BookingList extends Component
{
    /**
     * FR-2.2 — "Return" action triggered by the user clicking the Return button.
     * Guards ensure only the booking owner can return their own tool.
     */
    public function returnTool(int $bookingId): void
    {
        $booking = Booking::with('tool')
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);

        try {
            app(ToolStatusTransitioner::class)->return($booking);
        } catch (LogicException $e) {
            $this->addError('return', $e->getMessage());

            return;
        }

        $this->dispatch('tool-returned', bookingId: $bookingId);
    }

    /**
     * FR-3.9 — "Cancel" action triggered by the user clicking the Cancel button.
     * A renter may cancel a confirmed booking if start date is > 48h away.
     */
    public function cancelBooking(int $bookingId): void
    {
        $booking = Booking::with('tool')
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);

        try {
            app(ToolStatusTransitioner::class)->cancel($booking);
        } catch (LogicException $e) {
            $this->addError('cancel', $e->getMessage());

            return;
        }

        $this->dispatch('booking-cancelled', bookingId: $bookingId);
    }

    public function render(): \Illuminate\View\View
    {
        $bookings = Booking::with('tool')
            ->where('user_id', Auth::id())
            ->orderByDesc('start_date')
            ->get();

        return view('livewire.booking-list', ['bookings' => $bookings]);
    }
}
