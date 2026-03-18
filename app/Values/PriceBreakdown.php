<?php

namespace App\Values;

/**
 * Immutable value object holding the full price breakdown for a booking.
 *
 * Calculation order (FR-5.1):
 *   1. subtotal        = daily_rate × days
 *   2. discount        = subtotal × discount_rate  (capped per FR-5.3)
 *   3. discounted_base = subtotal − discount
 *   4. tax             = discounted_base × tax_rate  (FR-5.7: from depot)
 *   5. total           = discounted_base + tax + maintenance_fee
 */
final class PriceBreakdown
{
    public function __construct(
        public readonly Money $subtotal,
        public readonly Money $discount,
        public readonly Money $tax,
        public readonly Money $maintenanceFee,
        public readonly Money $total,
        public readonly int   $days,
        public readonly float $discountRate,
        public readonly float $taxRate,
    ) {}
}
