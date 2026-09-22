<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MatchLineups\StoreMatchLineupRequest;
use App\Http\Requests\Admin\MatchLineups\UpdateMatchLineupRequest;
use App\Models\MatchFixture;
use App\Models\MatchLineup;
use App\Models\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchLineupController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(MatchLineup::class, 'lineup');
    }

    public function index(MatchFixture $match): View
    {
        $this->authorize('view', $match);

        $match->loadMissing(['homeTeam', 'awayTeam', 'targetProgressions.sourceMatch']);

        $lineups = $match->lineups()
            ->with(['team', 'player'])
            ->orderBy('team_id')
            ->orderByRaw("CASE WHEN lineup_type = 'starting' THEN 0 ELSE 1 END")
            ->orderBy('sort_order')
            ->orderBy('shirt_number')
            ->get()
            ->groupBy('team_id');

        return view('admin.matches.lineups.index', [
            'match' => $match,
            'lineups' => $lineups,
            'teams' => collect([$match->homeTeam, $match->awayTeam])->filter(),
        ]);
    }

    public function create(MatchFixture $match): View
    {
        $this->authorize('update', $match);

        return view('admin.matches.lineups.create', [
            'match' => $match->loadMissing(['homeTeam', 'awayTeam', 'targetProgressions.sourceMatch']),
            ...$this->formData($match),
        ]);
    }

    public function store(StoreMatchLineupRequest $request, MatchFixture $match): RedirectResponse
    {
        $this->authorize('update', $match);

        $lineup = $match->lineups()->create($this->normaliseData($request->validated()));

        $this->recordAudit($request, $lineup, 'match-lineups.created', null, $lineup->toArray());

        return redirect()->route('admin.matches.lineups.index', $match)
            ->with('success', 'Lineup entry added successfully.');
    }

    public function edit(MatchFixture $match, MatchLineup $lineup): View
    {
        $this->authorize('update', $match);

        return view('admin.matches.lineups.edit', [
            'match' => $match->loadMissing(['homeTeam', 'awayTeam', 'targetProgressions.sourceMatch']),
            'lineup' => $lineup,
            ...$this->formData($match),
        ]);
    }

    public function update(UpdateMatchLineupRequest $request, MatchFixture $match, MatchLineup $lineup): RedirectResponse
    {
        $this->authorize('update', $match);

        $original = $lineup->toArray();
        $lineup->update($this->normaliseData($request->validated()));

        $this->recordAudit($request, $lineup, 'match-lineups.updated', $original, $lineup->fresh()->toArray());

        return redirect()->route('admin.matches.lineups.index', $match)
            ->with('success', 'Lineup entry updated successfully.');
    }

    public function destroy(Request $request, MatchFixture $match, MatchLineup $lineup): RedirectResponse
    {
        $this->authorize('update', $match);

        $original = $lineup->toArray();
        $lineup->delete();

        $this->recordAudit($request, $lineup, 'match-lineups.deleted', $original);

        return redirect()->route('admin.matches.lineups.index', $match)
            ->with('success', 'Lineup entry deleted successfully.');
    }

    protected function formData(MatchFixture $match): array
    {
        $match->loadMissing([
            'homeTeam.players',
            'awayTeam.players',
        ]);

        $teams = collect([$match->homeTeam, $match->awayTeam])->filter();

        return [
            'teams' => $teams,
            'teamPlayers' => $teams->mapWithKeys(fn ($team) => [
                $team->id => $team->players->sortBy('display_name')->values(),
            ]),
            'lineupTypes' => MatchLineup::LINEUP_TYPES,
        ];
    }

    protected function normaliseData(array $data): array
    {
        $data['is_captain'] = (bool) ($data['is_captain'] ?? false);
        $data['is_goalkeeper'] = (bool) ($data['is_goalkeeper'] ?? false);
        $data['position'] = $data['position'] ?: null;
        $data['formation_slot'] = $data['formation_slot'] ?: null;

        if (! ($data['shirt_number'] ?? null) && isset($data['player_id'])) {
            $player = Player::query()->find($data['player_id']);
            $data['shirt_number'] = $player?->shirt_number;
        }

        return $data;
    }
}
