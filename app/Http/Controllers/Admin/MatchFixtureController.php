<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Matches\StoreMatchFixtureRequest;
use App\Http\Requests\Admin\Matches\UpdateMatchFixtureRequest;
use App\Models\City;
use App\Models\Group;
use App\Models\MatchFixture;
use App\Models\Stadium;
use App\Models\Team;
use App\Services\Sports\GroupQualificationService;
use App\Services\Sports\KnockoutProgressionService;
use App\Services\Sports\StandingsRecalculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchFixtureController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(MatchFixture::class, 'match');
    }

    public function index(Request $request): View
    {
        $matches = MatchFixture::query()
            ->with(['homeTeam', 'awayTeam', 'stadium', 'city', 'group', 'targetQualificationRules.group', 'targetProgressions.sourceMatch'])
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('code', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->when($request->string('stage_type')->toString(), fn ($query, $stageType) => $query->where('stage_type', $stageType))
            ->orderBy('match_date')
            ->paginate(104)
            ->withQueryString();

        return view('admin.matches.index', [
            'matches' => $matches,
            'filters' => $request->only(['search', 'status', 'stage_type']),
        ]);
    }

    public function create(): View
    {
        return view('admin.matches.create', $this->formData());
    }

    public function store(
        StoreMatchFixtureRequest $request,
        StandingsRecalculationService $standingsRecalculationService,
        GroupQualificationService $groupQualificationService,
        KnockoutProgressionService $knockoutProgressionService
    ): RedirectResponse
    {
        $data = $request->validated();
        $data['extra_time_played'] = $request->boolean('extra_time_played');

        $match = MatchFixture::query()->create($data);

        $operationSummary = $this->runPostSaveOperations(
            $match,
            null,
            $standingsRecalculationService,
            $groupQualificationService,
            $knockoutProgressionService
        );

        $this->recordAudit($request, $match, 'matches.created', null, $match->toArray());

        return redirect()->route('admin.matches.show', $match)
            ->with('success', $this->successMessage('created', $operationSummary));
    }

    public function show(MatchFixture $match): View
    {
        return view('admin.matches.show', [
            'match' => $match->load([
                'homeTeam',
                'awayTeam',
                'stadium',
                'city',
                'group',
                'events' => fn ($query) => $query
                    ->with(['team', 'player', 'relatedPlayer'])
                    ->orderBy('minute')
                    ->orderBy('extra_minute')
                    ->orderBy('sort_order')
                    ->limit(12),
                'statistics' => fn ($query) => $query
                    ->with('team')
                    ->orderBy('team_id')
                    ->orderBy('metric_key'),
                'lineups' => fn ($query) => $query
                    ->with(['team', 'player'])
                    ->orderBy('team_id')
                    ->orderByRaw("CASE WHEN lineup_type = 'starting' THEN 0 ELSE 1 END")
                    ->orderBy('sort_order'),
                'group.standings.team',
                'targetQualificationRules.group',
                'targetQualificationRules.appliedTeam',
                'sourceProgressions.targetMatch.homeTeam',
                'sourceProgressions.targetMatch.awayTeam',
                'sourceProgressions.targetMatch.targetProgressions.sourceMatch',
                'targetProgressions.sourceMatch.homeTeam',
                'targetProgressions.sourceMatch.awayTeam',
            ]),
        ]);
    }

    public function edit(MatchFixture $match): View
    {
        return view('admin.matches.edit', [
            'match' => $match,
            ...$this->formData(),
        ]);
    }

    public function update(
        UpdateMatchFixtureRequest $request,
        MatchFixture $match,
        StandingsRecalculationService $standingsRecalculationService,
        GroupQualificationService $groupQualificationService,
        KnockoutProgressionService $knockoutProgressionService
    ): RedirectResponse
    {
        $original = $match->toArray();
        $data = $request->validated();
        $data['extra_time_played'] = $request->boolean('extra_time_played');
        $match->update($data);

        $operationSummary = $this->runPostSaveOperations(
            $match,
            $original,
            $standingsRecalculationService,
            $groupQualificationService,
            $knockoutProgressionService
        );

        $this->recordAudit($request, $match, 'matches.updated', $original, $match->fresh()->toArray());

        return redirect()->route('admin.matches.show', $match)
            ->with('success', $this->successMessage('updated', $operationSummary));
    }

    public function destroy(Request $request, MatchFixture $match): RedirectResponse
    {
        $original = $match->toArray();
        $match->delete();

        $this->recordAudit($request, $match, 'matches.deleted', $original);

        return redirect()->route('admin.matches.index')
            ->with('success', 'Match fixture archived successfully.');
    }

    protected function formData(): array
    {
        return [
            'teams' => Team::query()->orderBy('name')->get(),
            'stadiums' => Stadium::query()->orderBy('name')->get(),
            'cities' => City::query()->orderBy('name')->get(),
            'groups' => Group::query()->orderBy('sort_order')->orderBy('name')->get(),
            'stageTypes' => MatchFixture::STAGE_TYPES,
            'statuses' => MatchFixture::STATUSES,
        ];
    }

    protected function runPostSaveOperations(
        MatchFixture $match,
        ?array $originalState,
        StandingsRecalculationService $standingsRecalculationService,
        GroupQualificationService $groupQualificationService,
        KnockoutProgressionService $knockoutProgressionService
    ): array {
        $recalculatedGroups = collect();

        if ($this->qualifiesForStandingsRecalculation($originalState)) {
            $recalculatedGroups->push((int) $originalState['group_id']);
        }

        if ($match->isGroupStage() && $match->group_id && $match->isCompleted() && $match->hasScoreline()) {
            $recalculatedGroups->push((int) $match->group_id);
        }

        $recalculatedGroups = $recalculatedGroups
            ->unique()
            ->filter();

        $recalculatedCount = 0;
        $qualificationResults = collect();

        foreach ($recalculatedGroups as $groupId) {
            $group = Group::query()->with('teams')->find($groupId);

            if (! $group) {
                continue;
            }

            $standingsRecalculationService->recalculateGroup($group);
            $qualificationResults = $qualificationResults->concat(
                $groupQualificationService->applyForGroup($group->fresh(['teams', 'standings.team', 'qualificationRules.targetMatch']))
            );
            $recalculatedCount++;
        }

        $progressionResults = $match->isKnockoutStage() && $match->isCompleted()
            ? $knockoutProgressionService->propagateCascade($match)
            : collect();

        return [
            'recalculated_groups' => $recalculatedCount,
            'qualification_updates' => $qualificationResults->where('changed', true)->count(),
            'progression_updates' => $progressionResults->where('changed', true)->count(),
        ];
    }

    protected function qualifiesForStandingsRecalculation(?array $state): bool
    {
        if (! $state) {
            return false;
        }

        return $state['stage_type'] === 'group'
            && ! empty($state['group_id'])
            && $state['status'] === 'completed'
            && $state['home_score'] !== null
            && $state['away_score'] !== null;
    }

    protected function successMessage(string $action, array $operationSummary): string
    {
        $message = "Match fixture {$action} successfully.";

        if ($operationSummary['recalculated_groups'] > 0) {
            $message .= ' Group standings were recalculated automatically.';
        }

        if (($operationSummary['qualification_updates'] ?? 0) > 0) {
            $message .= ' Group qualification slots were checked automatically.';
        }

        if ($operationSummary['progression_updates'] > 0) {
            $message .= ' Knockout progression targets were updated automatically.';
        }

        return $message;
    }
}
