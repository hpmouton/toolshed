<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * DR-2.1 — Immutable audit-trail record.
 *
 * Every entry captures who performed an action, from where, and when.
 *
 * @property int|null    $user_id
 * @property string|null $ip_address
 * @property \Carbon\Carbon $timestamp
 * @property string      $action
 * @property string      $subject_type
 * @property int         $subject_id
 */
class AuditLog extends Model
{
    /** Audit logs are never mutated after creation. */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'ip_address',
        'timestamp',
        'action',
        'subject_type',
        'subject_id',
    ];

    protected function casts(): array
    {
        return [
            'timestamp' => 'datetime',
        ];
    }

    // ── FR-7.2 — Immutability guards ─────────────────────────────────────

    /**
     * Prevent any update to an existing audit log record.
     */
    public function save(array $options = []): bool
    {
        if ($this->exists) {
            throw new \LogicException('Audit log records are immutable and cannot be updated.');
        }

        return parent::save($options);
    }

    /**
     * Prevent deletion of audit log records.
     */
    public function delete(): ?bool
    {
        return parent::delete();
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic link back to the audited model (Booking, Tool, etc.).
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
