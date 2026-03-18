<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\DamageReport;
use App\Models\Review;
use App\Models\Tool;
use App\Models\WaitlistEntry;
use App\Services\AuditLogger;
use App\Services\ToolStatusTransitioner;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use LogicException;

/**
 * Handles tool booking with:
 *   UI-1.1 — inline date-range validation (red border + error message).
 *   UI-1.2 — loading spinner on the submit button to prevent double-booking.
 */
#[Title('Book a Tool')]
#[Layout('layouts.app')]
class BookingDatePicker extends Component
{
    public int    $toolId    = 0;
    public string $startDate = '';
    public string $endDate   = '';

    /** UI-1.1 — driven by the real-time validation hook below. */
    public string $dateError = '';

    public bool $booked = false;

    // -------------------------------------------------------------------------
    // Lifecycle
    // -------------------------------------------------------------------------

    public function mount(Tool $tool): void
    {
        $this->toolId = $tool->id;
    }

    // -------------------------------------------------------------------------
    // UI-1.1 — Real-time validation: runs on every update to either date field.
    // -------------------------------------------------------------------------

    public function updatedStartDate(): void
    {
        $this->validateDateRange();
    }

    public function updatedEndDate(): void
    {
        $this->validateDateRange();
    }

    /**
     * Returns true when the current start/end values form a valid range.
     * Populates $dateError so the view can react immediately.
     */
    private function validateDateRange(): bool
    {
        $this->dateError = '';

        if ($this->startDate === '' || $this->endDate === '') {
            return false;
        }

        try {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end   = Carbon::parse($this->endDate)->startOfDay();
        } catch (\Throwable) {
            $this->dateError = 'Invalid Date Range.';
            return false;
        }

        if ($start->gte($end) || $start->lt(Carbon::today())) {
            $this->dateError = 'Invalid Date Range.';
            return false;
        }

        // FR-12.3 — Prevent selection of overlapping dates in real time.
        if (Booking::hasOverlap($this->toolId, $this->startDate, $this->endDate)) {
            $this->dateError = __('These dates overlap with an existing booking.');
            return false;
        }

        return true;
    }

    // -------------------------------------------------------------------------
    // UI-1.2 — Booking action (wire:loading spinner is on the view side).
    // -------------------------------------------------------------------------

    public function book(): void
    {
        if (! $this->validateDateRange()) {
            return;
        }

        // DR-1.1 — Wrap the entire check-then-write sequence in a transaction
        // and acquire a pessimistic write lock on the tool row.  This prevents
        // a second concurrent request from reading "Available" status between
        // our check and our INSERT / status update.
        $booked  = false;
        $booking = null;

        try {
            DB::transaction(function () use (&$booked, &$booking): void {
                /** @var Tool $tool */
                $tool = Tool::lockForUpdate()->findOrFail($this->toolId);

                if (! $tool->isAvailable()) {
                    // DR-1.1 — exact required error message for race condition
                    $this->dateError = 'This item was just reserved by another user.';
                    return;
                }

                // FR-3.8 — Prevent overlapping bookings for the same tool.
                if (Booking::hasOverlap($this->toolId, $this->startDate, $this->endDate)) {
                    $this->dateError = 'These dates overlap with an existing booking.';
                    return;
                }

                // FR-5.5 — Calculate total price using PricingCalculator
                $days = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate));
                $taxRate = $tool->depot?->tax_rate ?? \App\Services\PricingCalculator::DEFAULT_TAX_RATE;
                $discountRate = $days > 6 ? \App\Services\PricingCalculator::WEEKLY_DISCOUNT_RATE : 0.0;
                $breakdown = app(\App\Services\PricingCalculator::class)->calculate(
                    $tool->dailyRate(),
                    $tool->maintenanceFee(),
                    $days,
                    $discountRate,
                    $taxRate,
                );

                $booking = Booking::create([
                    'tool_id'           => $this->toolId,
                    'user_id'           => Auth::id(),
                    'start_date'        => $this->startDate,
                    'end_date'          => $this->endDate,
                    'booking_status'    => 'pending',
                    'total_price_cents' => $breakdown->total->cents,
                ]);

                app(ToolStatusTransitioner::class)->confirm($booking->load('tool'));

                // DR-2.1 — record a "booking.confirmed" audit entry
                app(AuditLogger::class)->log('booking.confirmed', $booking);

                $booked = true;
            });
        } catch (LogicException $e) {
            $this->dateError = $e->getMessage();
            return;
        }

        if (! $booked) {
            return;
        }

        $this->booked    = true;
        $this->startDate = '';
        $this->endDate   = '';
        $this->dispatch('booking-confirmed', bookingId: $booking->id);
    }

    // ── FR-11.1 — Submit a review after a returned booking ──────────────

    public int $reviewRating = 0;
    public string $reviewBody = '';
    public ?int $reviewBookingId = null;

    public function submitReview(): void
    {
        $this->validate([
            'reviewRating'    => 'required|integer|min:0|max:5',
            'reviewBody'      => 'nullable|string|max:2000',
            'reviewBookingId' => 'required|exists:bookings,id',
        ]);

        // Ensure the user owns the booking and it is returned
        $booking = Booking::where('user_id', Auth::id())
            ->where('booking_status', 'returned')
            ->findOrFail($this->reviewBookingId);

        // Only one review per booking
        if (Review::where('booking_id', $booking->id)->exists()) {
            return;
        }

        Review::create([
            'tool_id'    => $this->toolId,
            'user_id'    => Auth::id(),
            'booking_id' => $booking->id,
            'rating'     => $this->reviewRating,
            'body'       => $this->reviewBody ?: null,
        ]);

        $this->reviewRating = 0;
        $this->reviewBody = '';
        $this->reviewBookingId = null;
    }

    /**
     * FR-11.3 — Staff can hide individual reviews.
     */
    public function hideReview(int $reviewId): void
    {
        $user = Auth::user();
        if (! $user->isStaff() && ! $user->isAdmin()) {
            return;
        }

        $review = Review::where('tool_id', $this->toolId)->findOrFail($reviewId);
        $review->update(['is_visible' => false]);
    }

    // ── FR-12.1 — Waitlist ───────────────────────────────────────────────

    public function joinWaitlist(): void
    {
        WaitlistEntry::firstOrCreate([
            'tool_id' => $this->toolId,
            'user_id' => Auth::id(),
        ]);
    }

    public function leaveWaitlist(): void
    {
        WaitlistEntry::where('tool_id', $this->toolId)
            ->where('user_id', Auth::id())
            ->delete();
    }

    // ── FR-13.1 — Damage declaration on return ──────────────────────────

    public string $damageCondition = 'undamaged';
    public string $damageDescription = '';
    public ?int $returningBookingId = null;

    public function startReturn(int $bookingId): void
    {
        $this->returningBookingId = $bookingId;
        $this->damageCondition = 'undamaged';
        $this->damageDescription = '';
    }

    public function confirmReturn(): void
    {
        $rules = [
            'damageCondition' => 'required|in:undamaged,minor_damage,major_damage',
        ];

        // FR-13.1 — description required for non-undamaged
        if ($this->damageCondition !== 'undamaged') {
            $rules['damageDescription'] = 'required|string|min:10|max:2000';
        }

        $this->validate($rules);

        $booking = Booking::with('tool')
            ->where('user_id', Auth::id())
            ->findOrFail($this->returningBookingId);

        try {
            app(ToolStatusTransitioner::class)->return($booking);
        } catch (LogicException $e) {
            $this->dateError = $e->getMessage();
            return;
        }

        // Create damage report regardless of condition (it tracks undamaged too)
        DamageReport::create([
            'booking_id'         => $booking->id,
            'user_id'            => Auth::id(),
            'condition_declared' => $this->damageCondition,
            'description'        => $this->damageCondition !== 'undamaged' ? $this->damageDescription : null,
        ]);

        $this->returningBookingId = null;
        $this->damageCondition = 'undamaged';
        $this->damageDescription = '';
    }

    public function cancelReturn(): void
    {
        $this->returningBookingId = null;
    }

    // -------------------------------------------------------------------------

    #[Computed]
    public function tool(): Tool
    {
        return Tool::with('depot')->findOrFail($this->toolId);
    }

    /**
     * FR-11.2 — Five most recent visible reviews for this tool.
     */
    #[Computed]
    public function reviews(): \Illuminate\Support\Collection
    {
        return Review::with('user')
            ->where('tool_id', $this->toolId)
            ->where('is_visible', true)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
    }

    /**
     * FR-12.1 — Whether the current user is on the waitlist.
     */
    #[Computed]
    public function isOnWaitlist(): bool
    {
        return WaitlistEntry::where('tool_id', $this->toolId)
            ->where('user_id', Auth::id())
            ->exists();
    }

    /**
     * FR-12.2 — Booked date ranges for the calendar view.
     */
    #[Computed]
    public function bookedRanges(): array
    {
        return Booking::where('tool_id', $this->toolId)
            ->whereIn('booking_status', ['confirmed', 'active'])
            ->select('start_date', 'end_date')
            ->get()
            ->map(fn ($b) => [
                'start' => $b->start_date->format('Y-m-d'),
                'end'   => $b->end_date->format('Y-m-d'),
            ])
            ->toArray();
    }

    /**
     * FR-11.1 — Find a returned booking eligible for review (no review yet).
     */
    #[Computed]
    public function reviewableBooking(): ?Booking
    {
        return Booking::where('tool_id', $this->toolId)
            ->where('user_id', Auth::id())
            ->where('booking_status', 'returned')
            ->whereDoesntHave('reviews')
            ->first();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.booking-date-picker');
    }
}
