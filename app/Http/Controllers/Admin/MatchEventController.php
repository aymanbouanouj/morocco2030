<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MatchEvents\StoreMatchEventRequest;
use App\Http\Requests\Admin\MatchEvents\UpdateMatchEventRequest;
use App\Models\MatchEvent;
use App\Models\MatchFixture;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MatchEventController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(MatchEvent::class, 'event');
    }

    public function index(MatchFixture $match): View
    {
        $this->authorize('view', $match);

        return view('admin.matches.events.index', [
            'match' => $match->loadMissing(['homeTeam', 'awayTeam', 'targetProgressions.sourceMatch']),
            'events' => $match->events()
                ->with(['team', 'player', 'relatedPlayer'])
                ->orderBy('minute')
                ->orderBy('extra_minute')
                ->orderBy('sort_order')
                ->paginate(25),
        ]);
    }

    public function create(MatchFixture $match): View
    {
        $this->authorize('update', $match);

        return view('admin.matches.events.create', [
            'match' => $match->loadMissing(['homeTeam', 'awayTeam', 'targetProgressions.sourceMatch']),
            ...$this->formData($match),
        ]);
    }

    public function store(StoreMatchEventRequest $request, MatchFixture $match): RedirectResponse
    {
        $this->authorize('update', $match);

        $data = $request->validated();
        $data['sort_order'] = $data['sort_order'] ?? $data['minute'];

        $event = $match->events()->create($data);

        $this->recordAudit($request, $event, 'match-events.created', null, $event->toArray());

        return redirect()->route('admin.matches.events.index', $match)
            ->with('success', 'Match event added successfully.');
    }

    public function edit(MatchFixture $match, MatchEvent $event): View
    {
        $this->authorize('update', $match);

        return view('admin.matches.events.edit', [
            'match' => $match->loadMissing(['homeTeam', 'awayTeam', 'targetProgressions.sourceMatch']),
            'event' => $event,
            ...$this->formData($match),
        ]);
    }

    public function update(UpdateMatchEventRequest $request, MatchFixture $match, MatchEvent $event): RedirectResponse
    {
        $this->authorize('update', $match);

        $original = $event->toArray();
        $data = $request->validated();
        $data['sort_order'] = $data['sort_order'] ?? $data['minute'];

        $event->update($data);

        $this->recordAudit($request, $event, 'match-events.updated', $original, $event->fresh()->toArray());

        return redirect()->route('admin.matches.events.index', $match)
            ->with('success', 'Match event updated successfully.');
    }

    public function destroy(Request $request, MatchFixture $match, MatchEvent $event): RedirectResponse
    {
        $this->authorize('update', $match);

        $original = $event->toArray();
        $event->delete();

        $this->recordAudit($request, $event, 'match-events.deleted', $original);

        return redirect()->route('admin.matches.events.index', $match)
            ->with('success', 'Match event deleted successfully.');
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
            'teamPlayers' => $this->teamPlayers($teams),
            'eventTypes' => MatchEvent::EVENT_TYPES,
            'periods' => MatchEvent::PERIODS,
        ];
    }

    protected function teamPlayers(Collection $teams): Collection
    {
        return $teams->mapWithKeys(fn ($team) => [
            $team->id => $team->players->sortBy('display_name')->values(),
        ]);
    }
}
