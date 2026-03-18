<?php

namespace App\Enums;

/**
 * Supported ISO 4217 currencies.
 *
 * Each case carries:
 *   - symbol()      : the conventional display symbol (e.g. '$', '€')
 *   - name()        : the full English name
 *   - decimalDigits(): number of minor-unit digits (almost always 2)
 *
 * Add more cases here as new depots/regions are onboarded.
 */
enum Currency: string
{
    case USD = 'USD'; // United States Dollar
    case EUR = 'EUR'; // Euro
    case GBP = 'GBP'; // British Pound Sterling
    case CAD = 'CAD'; // Canadian Dollar
    case AUD = 'AUD'; // Australian Dollar
    case NZD = 'NZD'; // New Zealand Dollar
    case ZAR = 'ZAR'; // South African Rand
    case JPY = 'JPY'; // Japanese Yen (0 decimal digits)
    case CNY = 'CNY'; // Chinese Yuan
    case INR = 'INR'; // Indian Rupee
    case BRL = 'BRL'; // Brazilian Real
    case MXN = 'MXN'; // Mexican Peso
    case SGD = 'SGD'; // Singapore Dollar
    case HKD = 'HKD'; // Hong Kong Dollar
    case CHF = 'CHF'; // Swiss Franc
    case SEK = 'SEK'; // Swedish Krona
    case NOK = 'NOK'; // Norwegian Krone
    case DKK = 'DKK'; // Danish Krone
    case AED = 'AED'; // UAE Dirham
    case SAR = 'SAR'; // Saudi Riyal

    // -------------------------------------------------------------------------

    public function symbol(): string
    {
        return match ($this) {
            self::USD => '$',
            self::EUR => '€',
            self::GBP => '£',
            self::CAD => 'CA$',
            self::AUD => 'A$',
            self::NZD => 'NZ$',
            self::ZAR => 'R',
            self::JPY => '¥',
            self::CNY => '¥',
            self::INR => '₹',
            self::BRL => 'R$',
            self::MXN => 'MX$',
            self::SGD => 'S$',
            self::HKD => 'HK$',
            self::CHF => 'CHF',
            self::SEK => 'kr',
            self::NOK => 'kr',
            self::DKK => 'kr',
            self::AED => 'د.إ',
            self::SAR => '﷼',
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::USD => 'US Dollar',
            self::EUR => 'Euro',
            self::GBP => 'British Pound',
            self::CAD => 'Canadian Dollar',
            self::AUD => 'Australian Dollar',
            self::NZD => 'New Zealand Dollar',
            self::ZAR => 'South African Rand',
            self::JPY => 'Japanese Yen',
            self::CNY => 'Chinese Yuan',
            self::INR => 'Indian Rupee',
            self::BRL => 'Brazilian Real',
            self::MXN => 'Mexican Peso',
            self::SGD => 'Singapore Dollar',
            self::HKD => 'Hong Kong Dollar',
            self::CHF => 'Swiss Franc',
            self::SEK => 'Swedish Krona',
            self::NOK => 'Norwegian Krone',
            self::DKK => 'Danish Krone',
            self::AED => 'UAE Dirham',
            self::SAR => 'Saudi Riyal',
        };
    }

    /** Number of decimal (minor-unit) digits for this currency. */
    public function decimalDigits(): int
    {
        return match ($this) {
            self::JPY => 0,
            default   => 2,
        };
    }

    /**
     * Format an integer cent-amount in this currency.
     *
     * For currencies with 0 decimal digits (JPY) the amount is treated as
     * whole units already.  For all others the amount is divided by 100.
     *
     *   Currency::USD->format(2500)  →  '$25.00'
     *   Currency::EUR->format(1099)  →  '€10.99'
     *   Currency::JPY->format(1500)  →  '¥1,500'
     */
    public function format(int $cents): string
    {
        $digits = $this->decimalDigits();
        $amount = $digits === 0 ? $cents : $cents / 100;

        return $this->symbol() . number_format($amount, $digits);
    }

    /** Return all currencies as [code => 'CODE — Name'] for select inputs. */
    public static function options(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->value . ' — ' . $case->name();
        }
        return $out;
    }
}
