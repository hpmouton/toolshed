<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * FR-10.2 — Reminder notification dispatched to the renter 48 hours before
 * the booking end date.
 */
class BookingEndingReminder extends Notification implements ShouldQueue
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
            ->subject(__('Reminder: Your booking for :tool ends soon', ['tool' => $tool->name]))
            ->greeting(__('Hi :name,', ['name' => $notifiable->name]))
            ->line(__('This is a friendly reminder that your booking is ending soon.'))
            ->line(__('**Tool:** :name (:sku)', ['name' => $tool->name, 'sku' => $tool->sku]))
            ->line(__('**End Date:** :date', [
                'date' => $this->booking->end_date->format('M j, Y'),
            ]))
            ->line(__('Please make sure to return the tool on time.'))
            ->action(__('View Bookings'), route('bookings.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'tool_name'  => $this->booking->tool->name,
            'message'    => __('Your booking for :tool ends on :date.', [
                'tool' => $this->booking->tool->name,
                'date' => $this->booking->end_date->format('M j, Y'),
            ]),
        ];
    }
}
