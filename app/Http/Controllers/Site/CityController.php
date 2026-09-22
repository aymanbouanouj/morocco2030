<?php

namespace App\Http\Controllers\Site;

use Illuminate\View\View;

class CityController extends SiteController
{
    public function index(): View
    {
        $cities = $this->activeCitiesQuery()
            ->withCount(['stadiums', 'matches'])
            ->with(['mediaRelations.mediaFile', 'translations.language'])
            ->orderBy('name')
            ->paginate(12);

        return view('public.cities.index', [
            'cities' => $cities,
        ]);
    }

    public function show(string $slug): View
    {
        $city = $this->activeCitiesQuery()
            ->with([
                'stadiums' => fn ($query) => $query
                    ->with('translations.language')
                    ->where('status', 'active')
                    ->orderBy('name'),
                'mediaRelations.mediaFile',
                'translations.language',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        $hostedMatches = $city->matches()
            ->with($this->matchSummaryRelations())
            ->orderByDesc('match_date')
            ->take(8)
            ->get();

        return view('public.cities.show', [
            'city' => $city,
            'hostedMatches' => $hostedMatches,
        ]);
    }
}
