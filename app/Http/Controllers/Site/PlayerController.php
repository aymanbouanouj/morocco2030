<?php

namespace App\Http\Controllers\Site;

use Illuminate\View\View;

class PlayerController extends SiteController
{
    public function index(): View
    {
        $playersQuery = $this->activePlayersQuery();

        if ($this->fdWorldCupExists()) {
            $playersQuery->whereHas('team', fn ($query) => $query->whereIn('id', $this->fdWorldCupRealTeamIds()));
        }

        $players = $playersQuery
            ->with(['team.group', 'team.translations.language', 'mediaRelations.mediaFile', 'translations.language'])
            ->orderBy('display_name')
            ->paginate(18);

        return view('public.players.index', [
            'players' => $players,
        ]);
    }

    public function show(string $slug): View
    {
        $playerQuery = $this->activePlayersQuery();

        if ($this->fdWorldCupExists()) {
            $playerQuery->whereHas('team', fn ($query) => $query->whereIn('id', $this->fdWorldCupRealTeamIds()));
        }

        $player = $playerQuery
            ->with([
                'team.group',
                'team.translations.language',
                'mediaRelations.mediaFile',
                'translations.language',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.players.show', [
            'player' => $player,
        ]);
    }
}
