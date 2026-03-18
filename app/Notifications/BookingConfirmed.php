<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * FR-10.1 — Transactional email sent to the renter when a booking is confirmed.
 */
class BookingConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tool  = $this->booking->tool;
        $depot = $tool->depot;

        return (new MailMessage)
            ->subject(__('Booking Confirmed — :tool', ['tool' => $tool->name]))
            ->greeting(__('Hi :name,', ['name' => $notifiable->name]))
            ->line(__('Your booking has been confirmed.'))
            ->line(__('**Tool:** :name (:sku)', ['name' => $tool->name, 'sku' => $tool->sku]))
            ->line(__('**Depot:** :address', ['address' => $depot?->shortAddress() ?? '—']))
            ->line(__('**Dates:** :start → :end', [
                'start' => $this->booking->start_date->format('M j, Y'),
                'end'   => $this->booking->end_date->format('M j, Y'),
            ]))
            ->line(__('**Total:** :total', [
                'total' => number_format(
                    $this->booking->total_price_cents / 100, 2
                ),
            ]))
            ->action(__('View Bookings'), route('bookings.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'tool_name'  => $this->booking->tool->name,
            'message'    => __('Your booking for :tool has been confirmed.', [
                'tool' => $this->booking->tool->name,
            ]),
        ];
    }
}
