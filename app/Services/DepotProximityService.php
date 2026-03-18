<?php

namespace App\Services;

use App\Models\Depot;
use Illuminate\Database\Eloquent\Collection;

/**
 * Finds depots near a given coordinate using the Haversine formula.
 *
 * SQLite does not expose SQRT/POW/ASIN/RADIANS by default, so we use a
 * lat/lng bounding-box to pre-filter rows in SQL (cheap index scan) and then
 * compute the exact haversine distance in PHP before sorting and capping.
 *
 * This strategy is also fast on MySQL/PostgreSQL because the bounding-box
 * WHERE clause benefits from composite (latitude, longitude) indexes.
 */
class DepotProximityService
{
    private const EARTH_RADIUS_KM = 3959.0;

    /**
     * Return depots ordered by distance from ($lat, $lng), within $radiusKm.
     *
     * Each returned Depot model has a virtual `distance_km` attribute added.
     *
     * @param  float  $lat       Latitude of the search origin
     * @param  float  $lng       Longitude of the search origin
     * @param  float  $radiusKm  Maximum distance in kilometres (default 250 km)
     * @param  int    $limit     Maximum number of results (default 20)
     * @return Collection<int, Depot>
     */
    public function nearest(
        float $lat,
        float $lng,
        float $radiusKm = 250.0,
        int   $limit    = 20,
    ): Collection {
        // 1 degree of latitude ≈ 111.32 km — use this to build a bounding box
        // that is slightly larger than the requested radius so we never miss a
        // depot that lies just inside the circle.
        $latDelta = $radiusKm / 111.32;
        $lngDelta = $radiusKm / (111.32 * cos(deg2rad($lat)));

        $candidates = Depot::query()
            ->where('is_active', true)
            ->whereBetween('latitude',  [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta])
            ->get();

        // 2. Compute exact haversine distance in PHP, keep only those within
        //    the true circle, sort, cap, and stamp each model with distance_km.
        return $candidates
            ->map(function (Depot $depot) use ($lat, $lng): Depot {
                $depot->distance_km = $this->haversine(
                    $lat, $lng,
                    (float) $depot->latitude,
                    (float) $depot->longitude,
                );
                return $depot;
            })
            ->filter(fn (Depot $depot): bool => $depot->distance_km <= $radiusKm)
            ->sortBy('distance_km')
            ->take($limit)
            ->values()
            ->pipe(fn ($collection) => new Collection($collection));
    }

    /**
     * Great-circle distance between two coordinates (km).
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * self::EARTH_RADIUS_KM * asin(sqrt($a));
    }

    /**
     * Return all depots in a given country, ordered alphabetically by city.
     *
     * @return Collection<int, Depot>
     */
    public function inCountry(string $countryCode): Collection
    {
        return Depot::query()
            ->where('is_active', true)
            ->where('country_code', strtoupper($countryCode))
            ->orderBy('city')
            ->get();
    }
}
