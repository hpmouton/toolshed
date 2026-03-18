<?php

namespace App\Values;

use InvalidArgumentException;

/**
 * Immutable value object representing a monetary amount stored as integer cents.
 *
 * Keeping amounts as cents prevents float-precision drift in tax and discount
 * calculations (FR-3.1, FR-3.2) and ensures FR-3.3 formatting is consistent.
 */
final class Money
{
    /**
     * @param  int  $cents  Amount in the smallest currency unit (e.g. cents for USD).
     *
     * @throws InvalidArgumentException if $cents is negative.
     */
    public function __construct(public readonly int $cents)
    {
        if ($cents < 0) {
            throw new InvalidArgumentException("Money amount cannot be negative (got {$cents} cents).");
        }
    }

    /**
     * Create from a whole-unit float/int (e.g. 25.00 → 2500 cents).
     * Half-cents are rounded up (standard commercial rounding).
     */
    public static function fromAmount(float $amount): self
    {
        return new self((int) round($amount * 100));
    }

    // -------------------------------------------------------------------------
    // Arithmetic — always returns a new immutable instance
    // -------------------------------------------------------------------------

    public function add(Money $other): self
    {
        return new self($this->cents + $other->cents);
    }

    public function subtract(Money $other): self
    {
        if ($other->cents > $this->cents) {
            throw new InvalidArgumentException('Subtraction would result in a negative monetary value.');
        }

        return new self($this->cents - $other->cents);
    }

    /**
     * Multiply by a scalar (e.g. number of days, a tax/discount rate).
     * The result is rounded to the nearest cent.
     */
    public function multiplyBy(float $factor): self
    {
        return new self((int) round($this->cents * $factor));
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /** Amount as a decimal float (e.g. 2500 → 25.0). */
    public function toFloat(): float
    {
        return $this->cents / 100;
    }

    /**
     * FR-3.3 — Format as $X,XXX.XX.
     * Uses PHP's number_format so thousands separators and two decimal places
     * are always present, regardless of locale settings.
     */
    public function format(): string
    {
        return '$' . number_format($this->toFloat(), 2);
    }

    public function equals(Money $other): bool
    {
        return $this->cents === $other->cents;
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
