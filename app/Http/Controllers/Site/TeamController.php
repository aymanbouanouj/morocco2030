<?php

namespace App\Http\Controllers\Site;

use App\Models\MatchFixture;
use App\Models\Team;
use Illuminate\View\View;

class TeamController extends SiteController
{
    public function index(): View
    {
        $teamsQuery = $this->activeTeamsQuery();

        if ($this->fdWorldCupExists()) {
            $teamIds = $this->fdWorldCupMatchesQuery()
                ->select('home_team_id')
                ->union($this->fdWorldCupMatchesQuery()->select('away_team_id'));

            $teamsQuery
                ->whereIn('id', $teamIds)
                ->where(function ($query) {
                    $query->whereNull('meta->placeholder')
                        ->orWhere('meta->placeholder', false)
                        ->orWhere('meta->placeholder', 'false');
                });
        }

        $teams = $teamsQuery
            ->with(['group', 'mediaRelations.mediaFile', 'translations.language'])
            ->withCount('players')
            ->orderBy('name')
            ->paginate(48);

        return view('public.teams.index', [
            'teams' => $teams,
        ]);
    }

    public function show(string $slug): View
    {
        $teamQuery = $this->fdWorldCupExists()
            ? $this->fdWorldCupRealTeamsQuery()
            : $this->activeTeamsQuery();

        $team = $teamQuery
            ->with([
                'group',
                'players' => fn ($query) => $query
                    ->with('translations.language')
                    ->where('status', 'active')
                    ->orderBy('shirt_number')
                    ->orderBy('display_name'),
                'mediaRelations.mediaFile',
                'translations.language',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        $matchScope = $this->fdWorldCupExists()
            ? $this->fdWorldCupMatchesQuery()
            : MatchFixture::query();

        $recentMatches = (clone $matchScope)
            ->with($this->matchSummaryRelations())
            ->where(function ($query) use ($team) {
                $query->where('home_team_id', $team->id)
                    ->orWhere('away_team_id', $team->id);
            })
            ->where('status', 'completed')
            ->orderByDesc('match_date')
            ->take(5)
            ->get();

        $upcomingMatches = (clone $matchScope)
            ->with($this->matchSummaryRelations())
            ->where(function ($query) use ($team) {
                $query->where('home_team_id', $team->id)
                    ->orWhere('away_team_id', $team->id);
            })
            ->where('status', '!=', 'completed')
            ->orderBy('match_date')
            ->take(5)
            ->get();

        return view('public.teams.show', [
            'team' => $team,
            'recentMatches' => $recentMatches,
            'upcomingMatches' => $upcomingMatches,
        ]);
    }
}
