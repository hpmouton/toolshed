<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * FR-11 — A rating + optional written review left by a renter after a
 * booking is returned.
 *
 * @property int         $id
 * @property int         $tool_id
 * @property int         $user_id
 * @property int         $booking_id
 * @property int         $rating       1–5
 * @property string|null $body
 * @property bool        $is_visible   FR-11.3 — staff can hide reviews
 */
class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'tool_id',
        'user_id',
        'booking_id',
        'rating',
        'body',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'rating'     => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
