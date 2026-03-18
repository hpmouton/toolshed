<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * FR-13.3 — A charge generated when a damage report is accepted.
 */
class DamageCharge extends Model
{
    protected $fillable = [
        'damage_report_id',
        'booking_id',
        'amount_cents',
        'currency_code',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount_cents' => 'integer',
        ];
    }

    public function damageReport(): BelongsTo
    {
        return $this->belongsTo(DamageReport::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
