<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Services\ToolStatusTransitioner;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use LogicException;

/**
 * FR-3.10 — Staff shall be able to advance a booking through its states
 * (confirm, dispatch, return) and cancel a booking with a mandatory reason.
 *
 * FR-0.7 — Staff are scoped to their depot; admins see all bookings.
 */
#[Layout('layouts.app')]
class StaffBookingManager extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    public string $cancelReason = '';
    public ?int $cancellingBookingId = null;

    public string $actionError = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * FR-3.10 — Confirm a pending booking.
     */
    public function confirm(int $bookingId): void
    {
        $this->actionError = '';
        $booking = $this->findBooking($bookingId);

        try {
            app(ToolStatusTransitioner::class)->confirm($booking);
        } catch (LogicException $e) {
            $this->actionError = $e->getMessage();
        }
    }

    /**
     * FR-3.10 — Dispatch a confirmed booking.
     */
    public function dispatchBooking(int $bookingId): void
    {
        $this->actionError = '';
        $booking = $this->findBooking($bookingId);

        try {
            app(ToolStatusTransitioner::class)->dispatch($booking);
        } catch (LogicException $e) {
            $this->actionError = $e->getMessage();
        }
    }

    /**
     * FR-3.10 — Return an active booking.
     */
    public function returnBooking(int $bookingId): void
    {
        $this->actionError = '';
        $booking = $this->findBooking($bookingId);

        try {
            app(ToolStatusTransitioner::class)->return($booking);
        } catch (LogicException $e) {
            $this->actionError = $e->getMessage();
        }
    }

    /**
     * FR-3.10 — Open the cancel dialog (requires a reason).
     */
    public function startCancel(int $bookingId): void
    {
        $this->cancellingBookingId = $bookingId;
        $this->cancelReason = '';
    }

    /**
     * FR-3.10 — Cancel a booking with a mandatory reason.
     */
    public function confirmCancel(): void
    {
        $this->validate([
            'cancelReason' => 'required|string|min:5|max:500',
        ]);

        $this->actionError = '';
        $booking = $this->findBooking($this->cancellingBookingId);

        try {
            app(ToolStatusTransitioner::class)->cancel($booking, byStaff: true);
        } catch (LogicException $e) {
            $this->actionError = $e->getMessage();
        }

        $this->cancellingBookingId = null;
        $this->cancelReason = '';
    }

    public function cancelCancel(): void
    {
        $this->cancellingBookingId = null;
        $this->cancelReason = '';
    }

    private function findBooking(int $bookingId): Booking
    {
        $query = Booking::with('tool.depot', 'user');

        // FR-0.7 — Staff scoped to their depot; admin sees all.
        $user = Auth::user();
        if ($user->isStaff() && $user->depot_id) {
            $query->whereHas('tool', fn ($q) => $q->where('depot_id', $user->depot_id));
        }

        return $query->findOrFail($bookingId);
    }

    public function render()
    {
        $user = Auth::user();

        $bookings = Booking::query()
            ->with(['tool.depot', 'user'])
            ->when($user->isStaff() && $user->depot_id, function ($q) use ($user) {
                $q->whereHas('tool', fn ($q) => $q->where('depot_id', $user->depot_id));
            })
            ->when($this->search !== '', fn ($q) => $q->where(function ($q) {
                $q->whereHas('tool', fn ($tq) => $tq->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%"))
                  ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->statusFilter !== '', fn ($q) => $q->where('booking_status', $this->statusFilter))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.staff-booking-manager', [
            'bookings' => $bookings,
        ]);
    }
}
