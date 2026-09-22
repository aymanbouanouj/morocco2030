<?php

namespace App\Http\Controllers\Site;

use Illuminate\View\View;

class StadiumController extends SiteController
{
    public function index(): View
    {
        $stadiums = $this->activeStadiumsQuery()
            ->with(['city.translations.language', 'mediaRelations.mediaFile', 'translations.language'])
            ->withCount('matches')
            ->orderBy('name')
            ->paginate(12);

        return view('public.stadiums.index', [
            'stadiums' => $stadiums,
        ]);
    }

    public function show(string $slug): View
    {
        $stadium = $this->activeStadiumsQuery()
            ->with([
                'city.translations.language',
                'mediaRelations.mediaFile',
                'translations.language',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        $upcomingMatches = $stadium->matches()
            ->with($this->matchSummaryRelations())
            ->where('status', '!=', 'completed')
            ->orderBy('match_date')
            ->take(8)
            ->get();

        $recentResults = $stadium->matches()
            ->with($this->matchSummaryRelations())
            ->where('status', 'completed')
            ->orderByDesc('match_date')
            ->take(8)
            ->get();

        return view('public.stadiums.show', [
            'stadium' => $stadium,
            'upcomingMatches' => $upcomingMatches,
            'recentResults' => $recentResults,
        ]);
    }
}
