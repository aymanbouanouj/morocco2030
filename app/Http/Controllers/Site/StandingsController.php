<?php

namespace App\Http\Controllers\Site;

use App\Models\Group;
use Illuminate\View\View;

class StandingsController extends SiteController
{
    public function index(): View
    {
        if ($this->fdWorldCupExists()) {
            return view('public.standings.index', [
                'groups' => $this->fdWorldCupStandingsGroups(),
                'fdWorldCupActive' => true,
            ]);
        }

        $groups = Group::query()
            ->with([
                'standings' => fn ($query) => $query
                    ->with(['team.mediaRelations.mediaFile', 'team.translations.language'])
                    ->orderBy('position'),
            ])
            ->withCount('teams')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('public.standings.index', [
            'groups' => $groups,
            'fdWorldCupActive' => false,
        ]);
    }

    public function show(string $code): View
    {
        if ($this->fdWorldCupExists()) {
            $group = $this->fdWorldCupStandingsGroups()
                ->firstWhere('code', strtoupper($code));

            abort_unless($group, 404);

            return view('public.standings.show', [
                'group' => $group,
                'fdWorldCupActive' => true,
            ]);
        }

        $group = Group::query()
            ->with([
                'teams.translations.language',
                'standings' => fn ($query) => $query
                    ->with(['team.mediaRelations.mediaFile', 'team.translations.language'])
                    ->orderBy('position'),
            ])
            ->where('code', strtoupper($code))
            ->firstOrFail();

        return view('public.standings.show', [
            'group' => $group,
            'fdWorldCupActive' => false,
        ]);
    }
}
