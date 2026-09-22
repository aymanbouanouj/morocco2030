<?php

namespace App\Http\Controllers\Site;

use App\Models\City;
use App\Models\Stadium;
use App\Support\PublicContent;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MapController extends SiteController
{
    public function __invoke(): View
    {
        $cities = $this->activeCitiesQuery()
            ->with('translations.language')
            ->withCount(['stadiums', 'matches'])
            ->orderBy('name')
            ->get();

        $stadiums = $this->activeStadiumsQuery()
            ->with(['city.translations.language', 'translations.language'])
            ->withCount('matches')
            ->orderBy('name')
            ->get();

        $citiesWithoutCoordinates = $cities
            ->filter(fn ($city) => $city->latitude === null || $city->longitude === null)
            ->values();
        $stadiumsWithoutCoordinates = $stadiums
            ->filter(fn ($stadium) => $stadium->latitude === null || $stadium->longitude === null)
            ->values();

        $cityLocations = $cities
            ->filter(fn ($city) => $city->latitude !== null && $city->longitude !== null)
            ->map(fn (City $city) => $this->cityLocation($city))
            ->values();
        $stadiumLocations = $stadiums
            ->filter(fn ($stadium) => $stadium->latitude !== null && $stadium->longitude !== null)
            ->map(fn (Stadium $stadium) => $this->stadiumLocation($stadium))
            ->values();
        $mapLocations = $cityLocations
            ->concat($stadiumLocations)
            ->values();

        return view('public.map.index', [
            'cities' => $cities,
            'stadiums' => $stadiums,
            'mapLocations' => $mapLocations,
            'defaultLocation' => $mapLocations->first(),
            'mapBounds' => $this->mapBounds($mapLocations),
            'mappedCitiesCount' => $cityLocations->count(),
            'mappedStadiumsCount' => $stadiumLocations->count(),
            'missingCoordinateCount' => $citiesWithoutCoordinates->count() + $stadiumsWithoutCoordinates->count(),
            'citiesWithoutCoordinates' => $citiesWithoutCoordinates,
            'stadiumsWithoutCoordinates' => $stadiumsWithoutCoordinates,
        ]);
    }

    protected function cityLocation(City $city): array
    {
        $cityName = PublicContent::field($city, 'name') ?? $city->name;
        $cityDescription = PublicContent::field($city, 'description') ?? $city->description;

        return [
            'id' => 'city-'.$city->id,
            'kind' => 'city',
            'kind_label' => __('Host City'),
            'label' => $cityName,
            'route' => route('cities.show', $city->slug),
            'action_label' => __('Open City'),
            'latitude' => (float) $city->latitude,
            'longitude' => (float) $city->longitude,
            'coordinates' => $this->formatCoordinates((float) $city->latitude, (float) $city->longitude),
            'meta' => $city->region ?: __('Host city'),
            'summary' => $cityDescription
                ?: trans_choice('{1}:count stadium|[2,*]:count stadiums', $city->stadiums_count, ['count' => $city->stadiums_count])
                    .' / '
                    .trans_choice('{1}:count match|[2,*]:count matches', $city->matches_count, ['count' => $city->matches_count]),
            'stats' => [
                [
                    'label' => __('Stadiums'),
                    'value' => trans_choice('{1}:count stadium|[2,*]:count stadiums', $city->stadiums_count, ['count' => $city->stadiums_count]),
                ],
                [
                    'label' => __('Hosted Matches'),
                    'value' => trans_choice('{1}:count match|[2,*]:count matches', $city->matches_count, ['count' => $city->matches_count]),
                ],
            ],
        ];
    }

    protected function stadiumLocation(Stadium $stadium): array
    {
        $stadiumName = PublicContent::field($stadium, 'name') ?? $stadium->name;
        $cityName = PublicContent::field($stadium->city, 'name') ?? $stadium->city?->name;
        $stats = [
            [
                'label' => __('Hosted Matches'),
                'value' => trans_choice('{1}:count match|[2,*]:count matches', $stadium->matches_count, ['count' => $stadium->matches_count]),
            ],
        ];

        if ($stadium->capacity) {
            $stats[] = [
                'label' => __('Capacity'),
                'value' => number_format((int) $stadium->capacity),
            ];
        }

        return [
            'id' => 'stadium-'.$stadium->id,
            'kind' => 'stadium',
            'kind_label' => __('Stadium'),
            'label' => $stadiumName,
            'route' => route('stadiums.show', $stadium->slug),
            'action_label' => __('Open Stadium'),
            'latitude' => (float) $stadium->latitude,
            'longitude' => (float) $stadium->longitude,
            'coordinates' => $this->formatCoordinates((float) $stadium->latitude, (float) $stadium->longitude),
            'meta' => $cityName ?: __('Host venue'),
            'summary' => $stadium->address
                ?: ($cityName ? __('Located in :city', ['city' => $cityName]) : __('Tournament venue')),
            'stats' => $stats,
        ];
    }

    protected function mapBounds(Collection $locations): ?array
    {
        if ($locations->isEmpty()) {
            return null;
        }

        $latitudes = $locations->pluck('latitude')->map(fn ($value) => (float) $value);
        $longitudes = $locations->pluck('longitude')->map(fn ($value) => (float) $value);

        return [
            [(float) $latitudes->min(), (float) $longitudes->min()],
            [(float) $latitudes->max(), (float) $longitudes->max()],
        ];
    }

    protected function formatCoordinates(float $latitude, float $longitude): string
    {
        return number_format($latitude, 4).', '.number_format($longitude, 4);
    }
}
