<?php

namespace App\Http\Controllers\Site;

use App\Models\MatchFixture;
use Illuminate\View\View;

class MatchController extends SiteController
{
    public function index(): View
    {
        $query = $this->fdWorldCupExists()
            ? $this->fdWorldCupMatchesQuery()
            : MatchFixture::query()->where('status', '!=', 'completed');

        $stageTypes = (clone $query)
            ->select('stage_type')
            ->distinct()
            ->pluck('stage_type')
            ->filter()
            ->sortBy(fn (string $stageType) => array_search($stageType, MatchFixture::STAGE_TYPES, true))
            ->values();

        $matches = $query
            ->with($this->matchSummaryRelations())
            ->orderByRaw("CASE WHEN status = 'live' THEN 0 WHEN status = 'scheduled' THEN 1 WHEN status = 'completed' THEN 2 ELSE 3 END")
            ->orderBy('match_date')
            ->paginate(24);

        return view('public.matches.index', [
            'matches' => $matches,
            'stageTypes' => $stageTypes,
        ]);
    }

    public function show(string $slug): View
    {
        $query = $this->fdWorldCupExists()
            ? $this->fdWorldCupMatchesQuery()
            : MatchFixture::query();

        $match = $query
            ->with($this->matchDetailRelations())
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.matches.show', [
            'match' => $match,
            'statisticsByTeam' => $match->statistics->groupBy('team_id'),
            'lineupsByTeam' => $match->lineups->groupBy('team_id'),
        ]);
    }

    public function results(): View
    {
        $query = $this->fdWorldCupExists()
            ? $this->fdWorldCupMatchesQuery()
            : MatchFixture::query();

        $results = $query
            ->with($this->matchSummaryRelations())
            ->where('status', 'completed')
            ->orderByDesc('match_date')
            ->paginate(12);

        return view('public.matches.results', [
            'results' => $results,
        ]);
    }
}
