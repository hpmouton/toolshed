<?php

namespace App\Services;

use App\Enums\ToolStatus;
use App\Models\Booking;
use App\Models\Tool;
use App\Models\WaitlistEntry;
use App\Notifications\BookingConfirmed;
use App\Notifications\ToolNowAvailable;
use Illuminate\Support\Carbon;
use LogicException;

/**
 * Drives the FR-2.2 tool-status state machine.
 *
 * Allowed transitions
 * -------------------
 *  confirm()  : booking_status pending   → confirmed  |  tool status → Reserved
 *  dispatch() : booking_status confirmed → active     |  tool status → Out
 *  return()   : booking_status active    → returned   |  tool status → Available
 *
 * DR-2.1 — every transition is written to the audit_logs table.
 */
class ToolStatusTransitioner
{
    public function __construct(
        private readonly AuditLogger $audit,
        private readonly WebhookDispatcher $webhooks,
    ) {}

    /**
     * FR-2.2 — Confirm a booking.
     * Moves the booking to "confirmed" and the tool to "Reserved".
     *
     * @throws LogicException if the booking is not in a confirmable state.
     */
    public function confirm(Booking $booking): void
    {
        if (! $booking->isPending()) {
            throw new LogicException(
                "Booking #{$booking->id} cannot be confirmed from status '{$booking->booking_status}'."
            );
        }

        $booking->booking_status = 'confirmed';
        $booking->save();

        $booking->tool->status = ToolStatus::Reserved;
        $booking->tool->save();

        // DR-2.1 — audit the confirmation event
        $this->audit->log('booking.confirmed', $booking);

        // FR-10.1 — send confirmation email + in-app notification
        if ($booking->user) {
            $booking->user->notify(new BookingConfirmed($booking));
        }

        // FR-15.3 — outbound webhook
        $this->webhooks->fire('booking.confirmed', [
            'booking_id' => $booking->id,
            'tool_id'    => $booking->tool_id,
            'user_id'    => $booking->user_id,
        ]);
    }

    /**
     * FR-2.2 — Dispatch a tool on its start date.
     * Moves the booking to "active" and the tool to "Out".
     *
     * @throws LogicException if the booking is not in a dispatchable state or
     *                        the start date has not yet arrived.
     */
    public function dispatch(Booking $booking, ?Carbon $asOf = null): void
    {
        if (! $booking->isConfirmed()) {
            throw new LogicException(
                "Booking #{$booking->id} cannot be dispatched from status '{$booking->booking_status}'."
            );
        }

        $today = ($asOf ?? Carbon::today())->startOfDay();

        if ($today->lt(Carbon::parse($booking->start_date)->startOfDay())) {
            throw new LogicException(
                "Booking #{$booking->id} cannot be dispatched before its start date ({$booking->start_date})."
            );
        }

        $booking->booking_status = 'active';
        $booking->save();

        $booking->tool->status = ToolStatus::Out;
        $booking->tool->save();

        // DR-2.1 — audit the dispatch event
        $this->audit->log('booking.dispatched', $booking);
    }

    /**
     * FR-2.2 — Return a tool after use.
     * Moves the booking to "returned" and the tool back to "Available".
     *
     * @throws LogicException if the booking is not in a returnable state or
     *                        the end date has not yet passed.
     */
    public function return(Booking $booking, ?Carbon $asOf = null): void
    {
        if (! $booking->isActive()) {
            throw new LogicException(
                "Booking #{$booking->id} cannot be returned from status '{$booking->booking_status}'."
            );
        }

        $booking->booking_status = 'returned';
        $booking->save();

        $booking->tool->status = ToolStatus::Available;
        $booking->tool->save();

        // DR-2.1 — audit the return event
        $this->audit->log('booking.returned', $booking);

        // FR-12.1 — notify waitlisted users that the tool is now available
        $this->notifyWaitlist($booking->tool);

        // FR-15.3 — outbound webhook
        $this->webhooks->fire('booking.returned', [
            'booking_id' => $booking->id,
            'tool_id'    => $booking->tool_id,
            'user_id'    => $booking->user_id,
        ]);
    }

    /**
     * FR-3.9 — Cancel a confirmed booking.
     * Moves the booking to "cancelled" and the tool back to "Available".
     *
     * A renter may cancel only if the booking is confirmed and the start date
     * is more than 48 hours in the future. Staff/admin bypass the 48-hour restriction.
     *
     * @param  bool  $byStaff  When true, the 48-hour window restriction is skipped (FR-3.10).
     * @throws LogicException if the booking is not in a cancellable state or
     *                        the 48-hour window has passed (for renters).
     */
    public function cancel(Booking $booking, ?Carbon $asOf = null, bool $byStaff = false): void
    {
        if (! $booking->isConfirmed()) {
            throw new LogicException(
                "Booking #{$booking->id} cannot be cancelled from status '{$booking->booking_status}'."
            );
        }

        $now = ($asOf ?? Carbon::now());

        if (! $byStaff) {
            $hoursUntilStart = $now->diffInHours(Carbon::parse($booking->start_date)->startOfDay(), false);

            if ($hoursUntilStart < 48) {
                throw new LogicException(
                    "Booking #{$booking->id} cannot be cancelled within 48 hours of its start date."
                );
            }
        }

        $booking->booking_status = 'cancelled';
        $booking->save();

        $booking->tool->status = ToolStatus::Available;
        $booking->tool->save();

        // DR-2.1 — audit the cancellation event
        $this->audit->log('booking.cancelled', $booking);

        // FR-12.1 — notify waitlisted users that the tool is now available
        $this->notifyWaitlist($booking->tool);
    }

    // ─────────────────────────────────────────────────────────────────────
    // FR-12.1 — Waitlist notification helper
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Notify all waitlisted users that a tool is now available,
     * then remove all waitlist entries for that tool.
     */
    private function notifyWaitlist(Tool $tool): void
    {
        $entries = WaitlistEntry::with('user')
            ->where('tool_id', $tool->id)
            ->orderBy('created_at')
            ->get();

        foreach ($entries as $entry) {
            if ($entry->user) {
                $entry->user->notify(new ToolNowAvailable($tool));
            }
        }

        WaitlistEntry::where('tool_id', $tool->id)->delete();
    }
}
