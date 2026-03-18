<?php

namespace App\Models;

use App\Enums\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A physical location where tools are stored and can be collected/returned.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $address_line1
 * @property string|null $address_line2
 * @property string      $city
 * @property string|null $state_province
 * @property string|null $postal_code
 * @property string      $country_code   ISO 3166-1 alpha-2
 * @property string      $country_name
 * @property string      $currency_code  ISO 4217
 * @property float       $tax_rate       Decimal fraction (e.g. 0.15 for 15%)
 * @property float       $latitude
 * @property float       $longitude
 * @property string|null $phone
 * @property string|null $email
 * @property bool        $is_active
 */
class Depot extends Model
{
    /** @use HasFactory<\Database\Factories\DepotFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'address_line1',
        'address_line2',
        'city',
        'state_province',
        'postal_code',
        'country_code',
        'country_name',
        'currency_code',
        'tax_rate',
        'latitude',
        'longitude',
        'phone',
        'email',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude'  => 'float',
            'longitude' => 'float',
            'tax_rate'  => 'float',
            'is_active' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Resolved Currency enum for this depot's local currency. */
    public function currency(): Currency
    {
        return Currency::from($this->currency_code);
    }

    /**
     * One-line formatted address for display.
     * e.g. "123 Main St, Cape Town, ZA"
     */
    public function shortAddress(): string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->city,
            $this->state_province,
            $this->country_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Haversine distance in kilometres from this depot to a given coordinate.
     */
    public function distanceTo(float $lat, float $lng): float
    {
        $earthRadius = 6371.0;

        $dLat = deg2rad($lat - $this->latitude);
        $dLng = deg2rad($lng - $this->longitude);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($this->latitude))
           * cos(deg2rad($lat))
           * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * asin(sqrt($a));
    }
}
