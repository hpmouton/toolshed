<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * FR-16 — An organisation with a private tool catalogue, monthly invoicing,
 * and a credit limit.
 *
 * @property int    $id
 * @property string $name
 * @property int    $credit_limit_cents
 * @property string $currency_code
 */
class Organisation extends Model
{
    protected $fillable = [
        'name',
        'credit_limit_cents',
        'currency_code',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit_cents' => 'integer',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────

    /**
     * FR-16.1 — Users belonging to this organisation.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * FR-16.2 — Monthly invoices.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(OrganisationInvoice::class);
    }

    /**
     * FR-16.3 — Organisation admins.
     */
    public function admins(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'org_admin');
    }

    /**
     * Check whether a user is an admin of this organisation.
     */
    public function isAdmin(User $user): bool
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'org_admin')
            ->exists();
    }
}
