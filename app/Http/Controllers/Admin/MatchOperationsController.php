<?php

namespace App\Http\Controllers\Admin;

use App\Models\MatchFixture;
use App\Models\Standing;
use App\Services\Sports\GroupQualificationService;
use App\Services\Sports\KnockoutProgressionService;
use App\Services\Sports\StandingsRecalculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MatchOperationsController extends AdminController
{
    public function recalculateStandings(
        Request $request,
        MatchFixture $match,
        StandingsRecalculationService $standingsRecalculationService,
        GroupQualificationService $groupQualificationService
    ): RedirectResponse {
        $this->authorize('recalculate', Standing::class);

        if (! $match->isGroupStage() || ! $match->group_id) {
            return back()->with('error', 'Standings recalculation is only available for group-stage matches with an assigned group.');
        }

        $standings = $standingsRecalculationService->recalculateForMatch($match);
        $qualificationResults = $match->group
            ? $groupQualificationService->applyForGroup($match->group->fresh(['teams', 'standings.team', 'qualificationRules.targetMatch']))
            : collect();

        $this->recordAudit($request, $match, 'matches.standings_recalculated', null, [
            'group_id' => $match->group_id,
            'standing_rows' => $standings->count(),
            'qualification_results' => $qualificationResults->values()->all(),
        ]);

        return back()->with(
            $standings->isEmpty() ? 'warning' : 'success',
            $standings->isEmpty()
                ? 'No completed group-stage scorelines were available to calculate standings.'
                : 'Standings recalculated successfully. Group qualification slots were checked.'
        );
    }

    public function propagateKnockout(
        Request $request,
        MatchFixture $match,
        KnockoutProgressionService $knockoutProgressionService
    ): RedirectResponse {
        $this->authorize('update', $match);

        if (! $match->isKnockoutStage()) {
            return back()->with('error', 'Knockout propagation is only available for knockout-stage fixtures.');
        }

        $results = $knockoutProgressionService->propagateCascade($match);

        $this->recordAudit($request, $match, 'matches.knockout_propagated', null, [
            'results' => $results->values()->all(),
        ]);

        return back()->with(
            $results->isEmpty() ? 'warning' : 'success',
            $results->isEmpty()
                ? 'No knockout progression changes were applied. Confirm the match is completed, decisive, and configured with target progressions.'
                : 'Knockout progression updated successfully.'
        );
    }
}
