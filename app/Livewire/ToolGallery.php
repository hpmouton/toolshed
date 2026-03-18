<?php

namespace App\Livewire;

use App\Enums\ToolStatus;
use App\Models\Booking;
use App\Models\Depot;
use App\Models\Tool;
use App\Services\AuditLogger;
use App\Services\PricingCalculator;
use App\Services\ToolStatusTransitioner;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use LogicException;

/**
 * Single-page tool catalogue with:
 *   • UI-2.1  — responsive grid
 *   • UI-1.1/2 — booking modal with real-time date validation & spinner
 *   • Bookings flyout — "My Bookings" slide-over without leaving the page
 */
#[Title('Tools')]
#[Layout('layouts.app')]
class ToolGallery extends Component
{
    use WithPagination;

    // ── Gallery filters ───────────────────────────────────────────────────────
    #[Url]
    public string $search = '';

    #[Url]
    public int $depot = 0;

    #[Url]
    public string $category = '';

    /** FR-2.5 — Sort order: 'name', 'price_asc', 'price_desc', 'distance'. */
    #[Url]
    public string $sort = 'name';

    /** FR-2.6 — Maximum daily rate filter (cents). 0 = no limit. */
    #[Url]
    public int $maxRate = 0;

    public function updatedSearch(): void   { $this->resetPage(); }
    public function updatedDepot(): void    { $this->resetPage(); }
    public function updatedCategory(): void { $this->resetPage(); }
    public function updatedSort(): void     { $this->resetPage(); }
    public function updatedMaxRate(): void  { $this->resetPage(); }

    // ── Booking modal state ───────────────────────────────────────────────────
    public ?int   $bookingToolId = null;
    public string $startDate     = '';
    public string $endDate       = '';
    public string $dateError     = '';
    public bool   $bookingDone   = false;

    // ── Tool detail flyout ────────────────────────────────────────────────────
    public ?int $detailToolId = null;

    // ─────────────────────────────────────────────────────────────────────────

    /** Open the booking modal for a specific tool. */
    public function openBooking(int $toolId): void
    {
        $this->bookingToolId = $toolId;
        $this->startDate     = '';
        $this->endDate       = '';
        $this->dateError     = '';
        $this->bookingDone   = false;
        $this->modal('book-tool')->show();
    }

    /** Open the detail flyout for a specific tool. */
    public function openDetail(int $toolId): void
    {
        $this->detailToolId = $toolId;
        $this->modal('tool-detail')->show();
    }

    // ── UI-1.1 Real-time date validation ──────────────────────────────────────

    public function updatedStartDate(): void { $this->validateDateRange(); }
    public function updatedEndDate(): void   { $this->validateDateRange(); }

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

        return true;
    }

    // ── UI-1.2 Booking action ─────────────────────────────────────────────────

    public function book(): void
    {
        if (! $this->validateDateRange()) {
            return;
        }

        $booked  = false;
        $booking = null;

        try {
            DB::transaction(function () use (&$booked, &$booking): void {
                /** @var Tool $tool */
                $tool = Tool::lockForUpdate()->findOrFail($this->bookingToolId);

                if (! $tool->isAvailable()) {
                    $this->dateError = 'This item was just reserved by another user.';
                    return;
                }

                // FR-3.8 — Prevent overlapping bookings for the same tool.
                if (Booking::hasOverlap($this->bookingToolId, $this->startDate, $this->endDate)) {
                    $this->dateError = 'These dates overlap with an existing booking.';
                    return;
                }

                // FR-5.5 — Calculate total price using PricingCalculator
                $days = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate));
                $taxRate = $tool->depot?->tax_rate ?? \App\Services\PricingCalculator::DEFAULT_TAX_RATE;
                $discountRate = $days >= 7 ? \App\Services\PricingCalculator::WEEKLY_DISCOUNT_RATE : 0.0;
                $breakdown = app(\App\Services\PricingCalculator::class)->calculate(
                    $tool->dailyRate(),
                    $tool->maintenanceFee(),
                    $days,
                    $discountRate,
                    $taxRate,
                );

                $booking = Booking::create([
                    'tool_id'           => $this->bookingToolId,
                    'user_id'           => Auth::id(),
                    'start_date'        => $this->startDate,
                    'end_date'          => $this->endDate,
                    'booking_status'    => 'pending',
                    'total_price_cents' => $breakdown->total->cents,
                ]);

                app(ToolStatusTransitioner::class)->confirm($booking->load('tool'));
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

        $this->bookingDone = true;
        $this->startDate   = '';
        $this->endDate     = '';
        $this->dispatch('booking-confirmed', bookingId: $booking->id);
    }

    public function closeBookingModal(): void
    {
        $this->modal('book-tool')->close();
        $this->bookingToolId = null;
        $this->bookingDone   = false;
    }

    // ── Return tool (from bookings flyout) ────────────────────────────────────

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

    // ── Cancel booking (from bookings flyout) ─────────────────────────────────

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

    // ── Computed helpers ──────────────────────────────────────────────────────

    #[Computed]
    public function bookingTool(): ?Tool
    {
        return $this->bookingToolId
            ? Tool::with('depot')->find($this->bookingToolId)
            : null;
    }

    #[Computed]
    public function detailTool(): ?Tool
    {
        return $this->detailToolId
            ? Tool::with('depot')->find($this->detailToolId)
            : null;
    }

    #[Computed]
    public function myBookings()
    {
        return Booking::with('tool.depot')
            ->where('user_id', Auth::id())
            ->orderByDesc('start_date')
            ->get();
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $tools = Tool::query()
            ->with('depot')
            ->where('status', '!=', ToolStatus::Archived->value)
            // FR-9.2 — hide tools belonging to inactive depots
            ->whereHas('depot', fn ($q) => $q->where('is_active', true))
            ->when(
                $this->search !== '',
                fn ($q) => $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('sku',  'like', "%{$this->search}%");
                }),
            )
            ->when($this->depot > 0,       fn ($q) => $q->where('depot_id', $this->depot))
            ->when($this->category !== '', fn ($q) => $q->where('category', $this->category))
            ->when($this->maxRate > 0,     fn ($q) => $q->where('daily_rate_cents', '<=', $this->maxRate))
            ->when($this->sort === 'price_asc',  fn ($q) => $q->orderBy('daily_rate_cents', 'asc'))
            ->when($this->sort === 'price_desc', fn ($q) => $q->orderBy('daily_rate_cents', 'desc'))
            ->when(! in_array($this->sort, ['price_asc', 'price_desc']), fn ($q) => $q->orderBy('name'))
            ->paginate(12);

        $activeDepot = $this->depot > 0 ? Depot::find($this->depot) : null;

        $categories = Tool::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('livewire.tool-gallery', [
            'tools'       => $tools,
            'activeDepot' => $activeDepot,
            'categories'  => $categories,
        ]);
    }
}
