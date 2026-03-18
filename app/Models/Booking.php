<?php

namespace App\Models;

use App\Enums\ToolStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    protected $fillable = [
        'tool_id',
        'user_id',
        'start_date',
        'end_date',
        'booking_status',
        'total_price_cents',
    ];

    protected function casts(): array
    {
        return [
            'start_date'        => 'immutable_date',
            'end_date'          => 'immutable_date',
            'total_price_cents' => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function damageReport(): HasOne
    {
        return $this->hasOne(DamageReport::class);
    }

    // -------------------------------------------------------------------------
    // Status helpers
    // -------------------------------------------------------------------------

    public function isPending(): bool
    {
        return $this->booking_status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->booking_status === 'confirmed';
    }

    public function isActive(): bool
    {
        return $this->booking_status === 'active';
    }

    public function isReturned(): bool
    {
        return $this->booking_status === 'returned';
    }

    public function isCancelled(): bool
    {
        return $this->booking_status === 'cancelled';
    }

    // -------------------------------------------------------------------------
    // FR-3.8 — Overlap detection
    // -------------------------------------------------------------------------

    /**
     * Check whether a proposed date range overlaps with any existing confirmed
     * or active booking for the given tool.
     */
    public static function hasOverlap(int $toolId, string $startDate, string $endDate, ?int $excludeBookingId = null): bool
    {
        return self::query()
            ->where('tool_id', $toolId)
            ->whereIn('booking_status', ['confirmed', 'active'])
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->exists();
    }
}
