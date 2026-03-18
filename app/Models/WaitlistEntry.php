<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * FR-12.1 — Waitlist entry: a user expressing interest in a tool that is
 * currently reserved or out.
 */
class WaitlistEntry extends Model
{
    protected $fillable = [
        'tool_id',
        'user_id',
    ];

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
