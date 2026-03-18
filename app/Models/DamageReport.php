<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * FR-13.1 — A damage report filed by a renter when returning a tool.
 *
 * @property string $condition_declared  undamaged | minor_damage | major_damage
 * @property string $status              pending | accepted | rejected | escalated
 */
class DamageReport extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'condition_declared',
        'description',
        'status',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * FR-13.3 — An accepted damage report may generate a charge.
     */
    public function charge(): HasOne
    {
        return $this->hasOne(DamageCharge::class);
    }
}
