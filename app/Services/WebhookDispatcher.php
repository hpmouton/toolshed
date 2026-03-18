<?php

namespace App\Services;

use App\Models\WebhookEndpoint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * FR-15.3 — Dispatches outbound webhook payloads to all active endpoints
 * subscribed to a given event.
 */
class WebhookDispatcher
{
    /**
     * Fire an event payload to all subscribed active webhook endpoints.
     *
     * @param  string  $event   e.g. "booking.confirmed", "booking.returned", "tool.archived"
     * @param  array   $payload The data to send as JSON.
     */
    public function fire(string $event, array $payload): void
    {
        $endpoints = WebhookEndpoint::where('is_active', true)->get();

        foreach ($endpoints as $endpoint) {
            if (! $endpoint->listensTo($event)) {
                continue;
            }

            $body = [
                'event'     => $event,
                'timestamp' => now()->toIso8601String(),
                'data'      => $payload,
            ];

            try {
                $request = Http::timeout(10)->asJson();

                // HMAC signature for verification
                if ($endpoint->secret) {
                    $signature = hash_hmac('sha256', json_encode($body), $endpoint->secret);
                    $request = $request->withHeaders([
                        'X-Webhook-Signature' => $signature,
                    ]);
                }

                $request->post($endpoint->url, $body);
            } catch (\Throwable $e) {
                Log::warning("Webhook delivery failed for endpoint #{$endpoint->id}: {$e->getMessage()}");
            }
        }
    }
}
