<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * DR-2.1 — Records every significant mutation with:
 *   - the authenticated user's ID
 *   - the IP address of the originating HTTP request
 *   - a UTC timestamp
 *   - a human-readable action name (e.g. "booking.confirmed")
 *   - a polymorphic reference to the subject model
 */
class AuditLogger
{
    public function __construct(private readonly ?Request $request = null) {}

    /**
     * Write one audit-log entry.
     *
     * @param  string  $action   e.g. "booking.confirmed", "tool.archived"
     * @param  Model   $subject  the Eloquent model being acted upon
     */
    public function log(string $action, Model $subject): AuditLog
    {
        return AuditLog::create([
            'user_id'      => Auth::id(),
            'ip_address'   => $this->request?->ip(),
            'timestamp'    => now(),
            'action'       => $action,
            'subject_type' => $subject->getMorphClass(),
            'subject_id'   => $subject->getKey(),
        ]);
    }
}
