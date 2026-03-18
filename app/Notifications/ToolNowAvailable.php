<?php

namespace App\Notifications;

use App\Models\Tool;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * FR-12.1 — Notify waitlisted users when a tool becomes available.
 */
class ToolNowAvailable extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Tool $tool) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__(':tool is now available!', ['tool' => $this->tool->name]))
            ->greeting(__('Hi :name,', ['name' => $notifiable->name]))
            ->line(__('Good news — a tool you were waiting for is now available.'))
            ->line(__('**Tool:** :name (:sku)', [
                'name' => $this->tool->name,
                'sku'  => $this->tool->sku,
            ]))
            ->action(__('Book Now'), route('tools.show', $this->tool))
            ->line(__('Note: availability is first-come, first-served.'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tool_id'   => $this->tool->id,
            'tool_name' => $this->tool->name,
            'message'   => __(':tool is now available for booking.', [
                'tool' => $this->tool->name,
            ]),
        ];
    }
}
