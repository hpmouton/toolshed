<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * FR-15.3 — A configurable outbound webhook endpoint that fires on
 * specific events (booking.confirmed, booking.returned, tool.archived).
 */
class WebhookEndpoint extends Model
{
    protected $fillable = [
        'url',
        'secret',
        'events',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'events'    => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check whether this endpoint is subscribed to a given event.
     */
    public function listensTo(string $event): bool
    {
        return in_array($event, $this->events ?? [], true);
    }
}
