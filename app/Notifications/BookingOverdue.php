<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * FR-10.3 — Sent if a booking remains in `active` status more than 24 hours
 * past its end date. Notifies both the renter and the depot.
 */
class BookingOverdue extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tool = $this->booking->tool;

        return (new MailMessage)
            ->subject(__('Overdue: :tool has not been returned', ['tool' => $tool->name]))
            ->greeting(__('Hi :name,', ['name' => $notifiable->name]))
            ->line(__('A booking is overdue and the tool has not yet been returned.'))
            ->line(__('**Tool:** :name (:sku)', ['name' => $tool->name, 'sku' => $tool->sku]))
            ->line(__('**End Date:** :date', [
                'date' => $this->booking->end_date->format('M j, Y'),
            ]))
            ->line(__('Please arrange for the tool to be returned as soon as possible.'))
            ->action(__('View Bookings'), route('bookings.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'tool_name'  => $this->booking->tool->name,
            'message'    => __(':tool is overdue. It was due on :date.', [
                'tool' => $this->booking->tool->name,
                'date' => $this->booking->end_date->format('M j, Y'),
            ]),
        ];
    }
}
