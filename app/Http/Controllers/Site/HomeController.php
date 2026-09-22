<?php

namespace App\Http\Controllers\Site;

use App\Models\City;
use App\Models\Group;
use App\Models\MatchFixture;
use App\Models\Partner;
use App\Models\Team;
use App\Support\PublicContent;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends SiteController
{
    public function __invoke(): View
    {
        $fdWorldCupActive = $this->fdWorldCupExists();
        $matchScope = $fdWorldCupActive ? $this->fdWorldCupMatchesQuery() : MatchFixture::query();

        return view('public.home', [
            'latestNews' => $this->publishedPublicNewsQuery()
                ->with(['category.translations.language', 'mediaRelations.mediaFile', 'translations.language'])
                ->orderByDesc('published_at')
                ->latest('id')
                ->take(3)
                ->get(),
            'upcomingMatches' => (clone $matchScope)
                ->with($this->matchSummaryRelations())
                ->whereIn('status', ['scheduled', 'live', 'postponed'])
                ->orderBy('match_date')
                ->take(3)
                ->get(),
            'recentResults' => (clone $matchScope)
                ->with($this->matchSummaryRelations())
                ->where('status', 'completed')
                ->orderByDesc('match_date')
                ->take(3)
                ->get(),
            'standingsPreview' => $fdWorldCupActive
                ? $this->fdWorldCupStandingsGroups(2)
                : Group::query()
                    ->with([
                        'standings' => fn ($query) => $query
                            ->with(['team.mediaRelations.mediaFile', 'team.translations.language'])
                            ->orderBy('position'),
                    ])
                    ->whereHas('standings')
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->take(2)
                    ->get(),
            'knockoutPreview' => (clone $matchScope)
                ->with($this->matchSummaryRelations())
                ->whereIn('stage_type', MatchFixture::KNOCKOUT_STAGE_TYPES)
                ->orderBy('match_date')
                ->take(3)
                ->get(),
            'teamsPreview' => ($fdWorldCupActive ? $this->fdWorldCupRealTeamsQuery() : $this->activeTeamsQuery())
                ->with(['group', 'mediaRelations.mediaFile', 'translations.language'])
                ->orderBy('name')
                ->take(3)
                ->get(),
            'citiesPreview' => $this->activeCitiesQuery()
                ->with('translations.language')
                ->withCount([
                    'stadiums',
                    'matches' => fn ($query) => $fdWorldCupActive ? $query->where('code', 'like', 'FD-WC-%') : null,
                ])
                ->orderBy('name')
                ->take(3)
                ->get(),
            'stadiumsPreview' => $this->activeStadiumsQuery()
                ->with(['city.translations.language', 'translations.language'])
                ->orderBy('name')
                ->take(3)
                ->get(),
            'partnersPreview' => Partner::query()
                ->publiclyVisible()
                ->with(['mediaRelations.mediaFile', 'translations.language'])
                ->publicOrder()
                ->take(4)
                ->get(),
            'homeMapLocations' => $this->homeMapLocations($fdWorldCupActive),
            'countdown' => $this->countdownData(),
            'tournamentMetrics' => $this->tournamentMetrics($fdWorldCupActive),
        ]);
    }

    private function tournamentMetrics(bool $fdWorldCupActive): array
    {
        $matchScope = $fdWorldCupActive ? $this->fdWorldCupMatchesQuery() : MatchFixture::query();

        $completedMatches = (clone $matchScope)
            ->where('status', 'completed')
            ->get(['home_score', 'away_score']);

        return [
            [
                'label' => __('Matches'),
                'value' => (clone $matchScope)->count(),
                'description' => __('Official fixtures in the tournament database'),
            ],
            [
                'label' => __('Completed'),
                'value' => $completedMatches->count(),
                'description' => __('Matches with confirmed scorelines'),
            ],
            [
                'label' => __('Goals'),
                'value' => $completedMatches->sum(
                    fn (MatchFixture $match) => (int) $match->home_score + (int) $match->away_score
                ),
                'description' => __('Goals from completed fixtures'),
            ],
            [
                'label' => __('Teams'),
                'value' => $fdWorldCupActive
                    ? $this->fdWorldCupRealTeamsQuery()->count()
                    : Team::query()->where('status', 'active')->count(),
                'description' => __('Active teams represented on the platform'),
            ],
            [
                'label' => __('Groups'),
                'value' => $fdWorldCupActive
                    ? $this->fdWorldCupApiGroupCount()
                    : Group::query()->count(),
                'description' => __('Tournament groups represented on the platform'),
            ],
            [
                'label' => __('Host Cities'),
                'value' => City::query()->where('status', 'active')->count(),
                'description' => __('Moroccan destinations in the host footprint'),
            ],
        ];
    }

    private function homeMapLocations(bool $fdWorldCupActive): Collection
    {
        $cities = $this->activeCitiesQuery()
            ->with('translations.language')
            ->withCount([
                'stadiums',
                'matches' => fn ($query) => $fdWorldCupActive ? $query->where('code', 'like', 'FD-WC-%') : null,
            ])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->take(4)
            ->get()
            ->map(fn ($city) => [
                'kind' => 'city',
                'kind_label' => __('Host City'),
                'label' => PublicContent::field($city, 'name') ?? $city->name,
                'meta' => trans_choice('{1}:count stadium|[2,*]:count stadiums', $city->stadiums_count, ['count' => $city->stadiums_count])
                    .' / '
                    .trans_choice('{1}:count match|[2,*]:count matches', $city->matches_count, ['count' => $city->matches_count]),
                'route' => route('cities.show', $city->slug),
                'latitude' => (float) $city->latitude,
                'longitude' => (float) $city->longitude,
            ]);

        $stadiums = $this->activeStadiumsQuery()
            ->with(['city.translations.language', 'translations.language'])
            ->withCount([
                'matches' => fn ($query) => $fdWorldCupActive ? $query->where('code', 'like', 'FD-WC-%') : null,
            ])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->take(4)
            ->get()
            ->map(fn ($stadium) => [
                'kind' => 'stadium',
                'kind_label' => __('Stadium'),
                'label' => PublicContent::field($stadium, 'name') ?? $stadium->name,
                'meta' => PublicContent::field($stadium->city, 'name') ?? $stadium->city?->name ?? __('Host venue'),
                'route' => route('stadiums.show', $stadium->slug),
                'latitude' => (float) $stadium->latitude,
                'longitude' => (float) $stadium->longitude,
            ]);

        $locations = $cities->concat($stadiums)->values();

        if ($locations->isEmpty()) {
            return $locations;
        }

        $latitudes = $locations->pluck('latitude');
        $longitudes = $locations->pluck('longitude');
        $minLatitude = (float) $latitudes->min();
        $maxLatitude = (float) $latitudes->max();
        $minLongitude = (float) $longitudes->min();
        $maxLongitude = (float) $longitudes->max();
        $latitudeRange = max($maxLatitude - $minLatitude, 0.0001);
        $longitudeRange = max($maxLongitude - $minLongitude, 0.0001);

        return $locations
            ->map(function (array $location) use ($minLatitude, $minLongitude, $latitudeRange, $longitudeRange) {
                $x = 12 + ((($location['longitude'] - $minLongitude) / $longitudeRange) * 76);
                $y = 88 - ((($location['latitude'] - $minLatitude) / $latitudeRange) * 76);

                return [
                    ...$location,
                    'x' => round($x, 2),
                    'y' => round($y, 2),
                ];
            })
            ->take(7)
            ->values();
    }

    private function countdownData(): ?array
    {
        $targetValue = config('tournament.countdown.target');

        if (! filled($targetValue)) {
            return null;
        }

        $timezone = config('tournament.timezone', 'Africa/Casablanca');

        try {
            $target = CarbonImmutable::parse($targetValue, $timezone);
        } catch (\Throwable) {
            return null;
        }

        $now = CarbonImmutable::now($timezone);
        $remainingSeconds = max(0, $target->timestamp - $now->timestamp);

        return [
            'title' => config('tournament.countdown.title', 'Tournament Countdown'),
            'label' => config('tournament.countdown.label', 'Opening Match'),
            'target_iso' => $target->toIso8601String(),
            'target_display' => $target->translatedFormat('l, F j, Y \\a\\t H:i'),
            'timezone' => $timezone,
            'is_complete' => $remainingSeconds === 0,
            'units' => $this->countdownUnits($remainingSeconds),
        ];
    }

    private function countdownUnits(int $remainingSeconds): array
    {
        $days = intdiv($remainingSeconds, 86400);
        $hours = intdiv($remainingSeconds % 86400, 3600);
        $minutes = intdiv($remainingSeconds % 3600, 60);
        $seconds = $remainingSeconds % 60;

        return compact('days', 'hours', 'minutes', 'seconds');
    }
}
