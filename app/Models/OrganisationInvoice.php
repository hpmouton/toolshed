<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * FR-16.2 — A monthly aggregated invoice for an organisation's bookings.
 *
 * @property int    $id
 * @property int    $organisation_id
 * @property string $period            e.g. "2026-03"
 * @property int    $total_cents
 * @property string $currency_code
 * @property string $status            pending | paid
 */
class OrganisationInvoice extends Model
{
    protected $fillable = [
        'organisation_id',
        'period',
        'total_cents',
        'currency_code',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_cents' => 'integer',
        ];
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }
}
