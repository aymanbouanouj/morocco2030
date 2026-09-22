<?php

namespace App\Http\Controllers\Site;

use App\Models\MatchFixture;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class KnockoutController extends SiteController
{
    public function __invoke(): View
    {
        $query = $this->fdWorldCupExists()
            ? $this->fdWorldCupMatchesQuery()
            : MatchFixture::query();

        $matches = $query
            ->with($this->matchSummaryRelations())
            ->whereIn('stage_type', MatchFixture::KNOCKOUT_STAGE_TYPES)
            ->orderBy('match_date')
            ->orderBy('id')
            ->get();

        $rounds = collect(MatchFixture::KNOCKOUT_STAGE_TYPES)
            ->mapWithKeys(fn ($stage) => [$stage => $matches->where('stage_type', $stage)->values()])
            ->filter(fn (Collection $stageMatches) => $stageMatches->isNotEmpty());

        return view('public.knockout.index', [
            'rounds' => $rounds,
        ]);
    }
}
