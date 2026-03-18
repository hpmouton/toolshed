<?php

namespace App\Services;

use App\Values\Money;
use App\Values\PriceBreakdown;
use InvalidArgumentException;

/**
 * Calculates the full price breakdown for a tool booking.
 *
 * FR-5.1 Tax Calculation
 * ----------------------
 * Tax is applied to the discounted subtotal BEFORE the maintenance fee
 * is added. The tax rate is resolved from the depot at which the tool is
 * located (FR-5.7):
 *     subtotal        = daily_rate × days
 *     discounted_base = subtotal − discount
 *     tax             = discounted_base × tax_rate
 *     total           = discounted_base + tax + maintenance_fee
 *
 * FR-5.2 Weekly Discount
 * ----------------------
 * The standard weekly discount rate is 10%, applied to bookings of 7 or
 * more days.
 *
 * FR-5.3 Discount Ceiling
 * -----------------------
 * The combined discount rate applied to any single booking shall not exceed
 * 25% of the subtotal. The cap is enforced here before any arithmetic.
 *
 * FR-5.4 Currency Formatting
 * --------------------------
 * All monetary output is provided via Money::format(). Callers should use
 * Money::format() or cast to string.
 */
class PricingCalculator
{
    /** FR-5.7 — Default tax rate used when no depot rate is supplied. */
    public const DEFAULT_TAX_RATE = 0.15;

    /** FR-5.3 — Hard ceiling on combined discounts. */
    public const MAX_DISCOUNT_RATE = 0.25;

    /** FR-5.2 — Standard weekly discount rate. */
    public const WEEKLY_DISCOUNT_RATE = 0.10;

    /**
     * Calculate the full price breakdown.
     *
     * @param  Money  $dailyRate       Per-day rental rate for the tool.
     * @param  Money  $maintenanceFee  Fixed maintenance fee charged per booking.
     * @param  int    $days            Number of rental days (must be ≥ 1).
     * @param  float  $discountRate    Fractional discount to apply before tax
     *                                 (e.g. 0.10 for 10%). Defaults to 0.
     * @param  float  $taxRate         Tax rate as a decimal fraction (e.g. 0.15 for 15%).
     *                                 Resolved from the depot's tax_rate column (FR-5.7).
     *
     * @throws InvalidArgumentException if $days < 1 or $discountRate < 0.
     */
    public function calculate(
        Money $dailyRate,
        Money $maintenanceFee,
        int $days,
        float $discountRate = 0.0,
        float $taxRate = self::DEFAULT_TAX_RATE,
    ): PriceBreakdown {
        if ($days < 1) {
            throw new InvalidArgumentException("Days must be at least 1 (got {$days}).");
        }

        if ($discountRate < 0) {
            throw new InvalidArgumentException("Discount rate cannot be negative (got {$discountRate}).");
        }

        // FR-5.3 — clamp the incoming discount rate to the hard ceiling.
        $effectiveDiscountRate = min($discountRate, self::MAX_DISCOUNT_RATE);

        // Step 1 — subtotal: daily rate × number of days.
        $subtotal = $dailyRate->multiplyBy($days);

        // Step 2 — discount amount (applied to subtotal before tax).
        $discount = $subtotal->multiplyBy($effectiveDiscountRate);

        // Step 3 — discounted base.
        $discountedBase = $subtotal->subtract($discount);

        // Step 4 — FR-5.1: tax is computed on the discounted base, before
        //          adding the maintenance fee. Rate comes from the depot (FR-5.7).
        $tax = $subtotal->multiplyBy($taxRate);

        // Step 5 — total: discounted base + tax + maintenance fee.
        $total = $discountedBase->add($tax)->add($maintenanceFee);

        return new PriceBreakdown(
            subtotal:       $subtotal,
            discount:       $discount,
            tax:            $tax,
            maintenanceFee: $maintenanceFee,
            total:          $total,
            days:           $days,
            discountRate:   $effectiveDiscountRate,
            taxRate:        $taxRate,
        );
    }

    /**
     * Convenience factory: compose multiple discount rates and enforce the
     * FR-5.3 combined ceiling before calling calculate().
     *
     * @param  float[]  $rates    Individual discount rates (e.g. [0.10, 0.05]).
     * @param  float    $taxRate  Tax rate from the depot (FR-5.7).
     */
    public function calculateWithDiscounts(
        Money $dailyRate,
        Money $maintenanceFee,
        int $days,
        array $rates = [],
        float $taxRate = self::DEFAULT_TAX_RATE,
    ): PriceBreakdown {
        // Sum all rates but clamp to MAX_DISCOUNT_RATE before passing through.
        $combined = min(array_sum($rates), self::MAX_DISCOUNT_RATE);

        return $this->calculate($dailyRate, $maintenanceFee, $days, $combined, $taxRate);
    }
}
