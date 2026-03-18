<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\ToolStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Tool extends Model
{
    /** @use HasFactory<\Database\Factories\ToolFactory> */
    use HasFactory;

    protected $fillable = [
        'sku',
        'serial_number',
        'name',
        'description',
        'image_url',
        'category',
        'daily_rate_cents',
        'maintenance_fee_cents',
        'currency_code',
        'status',
        'condition',
        'last_serviced_date',
        'weight_kg',
        'dimensions',
        'user_id',
        'depot_id',
    ];

    protected function casts(): array
    {
        return [
            'status'                => ToolStatus::class,
            'daily_rate_cents'      => 'integer',
            'maintenance_fee_cents' => 'integer',
            'last_serviced_date'    => 'date',
            'weight_kg'             => 'float',
        ];
    }

    // -------------------------------------------------------------------------
    // Money accessors (FR-3.3)
    // -------------------------------------------------------------------------

    public function dailyRate(): \App\Values\Money
    {
        return new \App\Values\Money($this->daily_rate_cents);
    }

    public function maintenanceFee(): \App\Values\Money
    {
        return new \App\Values\Money($this->maintenance_fee_cents);
    }

    /** Resolved Currency enum for this tool's denomination. */
    public function currency(): Currency
    {
        return Currency::from($this->currency_code ?? 'USD');
    }

    /**
     * Format the daily rate in the tool's own currency.
     * e.g. "£45.00" for a GBP-denominated tool.
     */
    public function formattedDailyRate(): string
    {
        return $this->currency()->format($this->daily_rate_cents);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * FR-11.2 — Average rating from visible reviews.
     */
    public function averageRating(): ?float
    {
        $avg = $this->reviews()->where('is_visible', true)->avg('rating');

        return $avg !== null ? round((float) $avg, 1) : null;
    }

    /**
     * FR-11.2 — Count of visible reviews.
     */
    public function reviewCount(): int
    {
        return $this->reviews()->where('is_visible', true)->count();
    }

    // -------------------------------------------------------------------------
    // Convenience helpers
    // -------------------------------------------------------------------------

    public function isAvailable(): bool
    {
        return $this->status === ToolStatus::Available;
    }

    public function isReserved(): bool
    {
        return $this->status === ToolStatus::Reserved;
    }

    public function isOut(): bool
    {
        return $this->status === ToolStatus::Out;
    }

    public function isArchived(): bool
    {
        return $this->status === ToolStatus::Archived;
    }

    /**
     * FR-8.4 — Returns true when the tool's last serviced date is more than
     * 90 days in the past (or has never been serviced).
     */
    public function needsService(): bool
    {
        if ($this->last_serviced_date === null) {
            return true;
        }

        return $this->last_serviced_date->diffInDays(Carbon::today()) > 30;
    }

    /** Emoji icon for the tool's category — used in UI cards. */
    public function categoryEmoji(): string
    {
        return match ($this->category) {
            'Power'      => '⚡',
            'Demo'       => '🔨',
            'Access'     => '🪜',
            'Concrete'   => '🏗️',
            'Plumbing'   => '🔧',
            'Electrical' => '⚡',
            'Landscape'  => '🌿',
            'Painting'   => '🎨',
            'Measuring'  => '📐',
            default      => '🛠️',
        };
    }

    /** Tailwind colour classes for the category pill. */
    public function categoryColour(): string
    {
        return match ($this->category) {
            'Power'      => 'bg-orange-500/20 text-orange-300 ring-orange-500/30',
            'Demo'       => 'bg-red-500/20 text-red-300 ring-red-500/30',
            'Access'     => 'bg-sky-500/20 text-sky-300 ring-sky-500/30',
            'Concrete'   => 'bg-stone-500/20 text-stone-300 ring-stone-500/30',
            'Plumbing'   => 'bg-cyan-500/20 text-cyan-300 ring-cyan-500/30',
            'Electrical' => 'bg-yellow-500/20 text-yellow-300 ring-yellow-500/30',
            'Landscape'  => 'bg-green-500/20 text-green-300 ring-green-500/30',
            'Painting'   => 'bg-pink-500/20 text-pink-300 ring-pink-500/30',
            'Measuring'  => 'bg-violet-500/20 text-violet-300 ring-violet-500/30',
            default      => 'bg-zinc-500/20 text-zinc-300 ring-zinc-500/30',
        };
    }

    /** Fallback gradient when no image is set — unique per category. */
    public function categoryGradient(): string
    {
        return match ($this->category) {
            'Power'      => 'from-orange-900 via-orange-800 to-amber-900',
            'Demo'       => 'from-red-900 via-red-800 to-rose-900',
            'Access'     => 'from-sky-900 via-sky-800 to-blue-900',
            'Concrete'   => 'from-stone-800 via-stone-700 to-zinc-800',
            'Plumbing'   => 'from-cyan-900 via-teal-800 to-cyan-900',
            'Electrical' => 'from-yellow-900 via-amber-800 to-yellow-900',
            'Landscape'  => 'from-green-900 via-emerald-800 to-green-900',
            'Painting'   => 'from-pink-900 via-fuchsia-800 to-pink-900',
            'Measuring'  => 'from-violet-900 via-purple-800 to-violet-900',
            default      => 'from-zinc-800 via-zinc-700 to-zinc-800',
        };
    }

    // -------------------------------------------------------------------------
    // DR-2.2 — Archive-instead-of-delete policy
    // -------------------------------------------------------------------------

    /**
     * Returns true when the tool has at least one booking that is currently
     * active OR is scheduled to start in the future (i.e. end_date >= today).
     */
    public function hasActiveOrFutureBookings(): bool
    {
        return $this->bookings()
            ->whereNotIn('booking_status', ['returned', 'cancelled'])
            ->where('end_date', '>=', Carbon::today()->toDateString())
            ->exists();
    }

    /**
     * DR-2.2 — Archive this tool instead of deleting it.
     *
     * Marks the tool as Archived.  Call this when a tool must be removed from
     * active service but has active or future bookings that must be honoured.
     *
     * @throws \LogicException when the tool is already archived.
     */
    public function archive(): void
    {
        if ($this->isArchived()) {
            throw new \LogicException("Tool #{$this->id} is already archived.");
        }

        $this->status = ToolStatus::Archived;
        $this->save();

        // FR-15.3 — outbound webhook
        app(\App\Services\WebhookDispatcher::class)->fire('tool.archived', [
            'tool_id' => $this->id,
            'sku'     => $this->sku,
        ]);
    }

    /**
     * DR-2.2 — Override delete() so that tools with active or future bookings
     * are automatically archived rather than hard-deleted.
     *
     * Tools with no active/future bookings are deleted normally.
     */
    public function delete(): ?bool
    {
        if ($this->hasActiveOrFutureBookings()) {
            $this->archive();
            return true;
        }

        return parent::delete();
    }
}
