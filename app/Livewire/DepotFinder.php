<?php

namespace App\Livewire;

use App\Enums\Currency;
use App\Models\Depot;
use App\Services\DepotProximityService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Lets the user:
 *   1. Enter a latitude/longitude (or pick a preset city) to find nearby depots.
 *   2. Filter by country.
 *   3. See each depot's local currency and available tool count.
 *   4. Save their preferred city/country/currency to their profile.
 */
#[Title('Find a Depot')]
#[Layout('layouts.app')]
class DepotFinder extends Component
{
    #[Url]
    public float $lat = 0.0;
    #[Url]
    public float $lng = 0.0;

    public float  $radiusKm     = 500.0;
    public string $countryFilter = '';

    /** Feedback message after saving preferences. */
    public string $savedMessage = '';

    /** FR-6.6 — Error message if browser geolocation fails. */
    public string $geoError = '';

    // Profile preference fields
    public string $preferredCurrency = 'USD';
    public string $preferredCity     = '';
    public string $preferredCountry  = '';

    public function mount(): void
    {
        $user = Auth::user();

        $this->preferredCurrency = $user->preferred_currency ?? 'USD';
        $this->preferredCity     = $user->city             ?? '';
        $this->preferredCountry  = $user->country_code     ?? '';

        // Pre-populate lat/lng from saved profile if not in URL
        if ($this->lat === 0.0 && $this->lng === 0.0 && $user->latitude) {
            $this->lat = $user->latitude;
            $this->lng = $user->longitude;
        }
    }

    // -------------------------------------------------------------------------

    public function savePreferences(): void
    {
        $this->validate([
            'preferredCurrency' => ['required', 'string', 'size:3'],
            'preferredCity'     => ['nullable', 'string', 'max:100'],
            'preferredCountry'  => ['nullable', 'string', 'size:2'],
        ]);

        Auth::user()->update([
            'preferred_currency' => strtoupper($this->preferredCurrency),
            'city'               => $this->preferredCity   ?: null,
            'country_code'       => $this->preferredCountry ? strtoupper($this->preferredCountry) : null,
            'latitude'           => $this->lat ?: null,
            'longitude'          => $this->lng ?: null,
        ]);

        $this->savedMessage = 'Preferences saved.';
    }

    /**
     * FR-6.6 — Accept coordinates from the browser Geolocation API and use
     * them to pre-populate the latitude and longitude fields.
     */
    public function setGeolocation(float $lat, float $lng): void
    {
        $this->lat = round($lat, 7);
        $this->lng = round($lng, 7);
        $this->geoError = '';
    }

    /**
     * FR-6.6 — Called from JS when the browser geolocation request fails.
     */
    public function geolocationFailed(string $message): void
    {
        $this->geoError = $message;
    }

    // -------------------------------------------------------------------------

    public function getNearbyDepotsProperty(): Collection
    {
        if ($this->lat === 0.0 && $this->lng === 0.0) {
            return collect();
        }

        return app(DepotProximityService::class)
            ->nearest($this->lat, $this->lng, $this->radiusKm)
            ->when(
                $this->countryFilter !== '',
                fn ($c) => $c->filter(
                    fn (Depot $d) => strtoupper($d->country_code) === strtoupper($this->countryFilter)
                )
            );
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.depot-finder', [
            'nearbyDepots'     => $this->nearbyDepots,
            'currencyOptions'  => Currency::options(),
        ]);
    }
}
