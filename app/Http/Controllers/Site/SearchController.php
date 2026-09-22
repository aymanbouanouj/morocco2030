<?php

namespace App\Http\Controllers\Site;

use App\Services\Site\PublicSearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends SiteController
{
    public function __construct(
        protected PublicSearchService $searchService,
    ) {
    }

    public function __invoke(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $query = trim((string) ($validated['q'] ?? ''));
        $results = $this->searchService->search($query);

        $resultGroups = collect([
            [
                'key' => 'news',
                'label' => __('News'),
                'description' => __('Official stories, announcements, and tournament updates.'),
                'route' => route('news.index'),
                'items' => $results['news'],
            ],
            [
                'key' => 'matches',
                'label' => __('Matches'),
                'description' => __('Fixtures, results, and full match coverage.'),
                'route' => route('matches.index'),
                'items' => $results['matches'],
            ],
            [
                'key' => 'teams',
                'label' => __('Teams'),
                'description' => __('National teams featured in the tournament.'),
                'route' => route('teams.index'),
                'items' => $results['teams'],
            ],
            [
                'key' => 'players',
                'label' => __('Players'),
                'description' => __('Player profiles and squad members.'),
                'route' => route('players.index'),
                'items' => $results['players'],
            ],
            [
                'key' => 'cities',
                'label' => __('Cities'),
                'description' => __('Host cities, regions, and destination guides.'),
                'route' => route('cities.index'),
                'items' => $results['cities'],
            ],
            [
                'key' => 'stadiums',
                'label' => __('Stadiums'),
                'description' => __('Stadiums, venues, and scheduled host grounds.'),
                'route' => route('stadiums.index'),
                'items' => $results['stadiums'],
            ],
            [
                'key' => 'partners',
                'label' => __('Partners'),
                'description' => __('Official partners and supporting organisations.'),
                'route' => route('partners.index'),
                'items' => $results['partners'],
            ],
        ]);

        return view('public.search.index', [
            'query' => $query,
            'resultGroups' => $resultGroups,
            'totalResults' => $resultGroups->sum(fn (array $group) => $group['items']->count()),
            'hasQuery' => $query !== '',
        ]);
    }
}
