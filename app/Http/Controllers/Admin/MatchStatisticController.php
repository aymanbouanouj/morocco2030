<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MatchStatistics\StoreMatchStatisticRequest;
use App\Http\Requests\Admin\MatchStatistics\UpdateMatchStatisticRequest;
use App\Models\MatchFixture;
use App\Models\MatchStatistic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchStatisticController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(MatchStatistic::class, 'statistic');
    }

    public function index(MatchFixture $match): View
    {
        $this->authorize('view', $match);

        $statistics = $match->statistics()
            ->with('team')
            ->orderBy('team_id')
            ->orderBy('context')
            ->orderBy('metric_key')
            ->paginate(30);

        return view('admin.matches.statistics.index', [
            'match' => $match->loadMissing(['homeTeam', 'awayTeam', 'targetProgressions.sourceMatch']),
            'statistics' => $statistics,
        ]);
    }

    public function create(MatchFixture $match): View
    {
        $this->authorize('update', $match);

        return view('admin.matches.statistics.create', [
            'match' => $match->loadMissing(['homeTeam', 'awayTeam', 'targetProgressions.sourceMatch']),
            ...$this->formData($match),
        ]);
    }

    public function store(StoreMatchStatisticRequest $request, MatchFixture $match): RedirectResponse
    {
        $this->authorize('update', $match);

        $statistic = $match->statistics()->create($this->normaliseData($request->validated()));

        $this->recordAudit($request, $statistic, 'match-statistics.created', null, $statistic->toArray());

        return redirect()->route('admin.matches.statistics.index', $match)
            ->with('success', 'Match statistic added successfully.');
    }

    public function edit(MatchFixture $match, MatchStatistic $statistic): View
    {
        $this->authorize('update', $match);

        return view('admin.matches.statistics.edit', [
            'match' => $match->loadMissing(['homeTeam', 'awayTeam', 'targetProgressions.sourceMatch']),
            'statistic' => $statistic,
            ...$this->formData($match),
        ]);
    }

    public function update(
        UpdateMatchStatisticRequest $request,
        MatchFixture $match,
        MatchStatistic $statistic
    ): RedirectResponse {
        $this->authorize('update', $match);

        $original = $statistic->toArray();
        $statistic->update($this->normaliseData($request->validated()));

        $this->recordAudit($request, $statistic, 'match-statistics.updated', $original, $statistic->fresh()->toArray());

        return redirect()->route('admin.matches.statistics.index', $match)
            ->with('success', 'Match statistic updated successfully.');
    }

    public function destroy(Request $request, MatchFixture $match, MatchStatistic $statistic): RedirectResponse
    {
        $this->authorize('update', $match);

        $original = $statistic->toArray();
        $statistic->delete();

        $this->recordAudit($request, $statistic, 'match-statistics.deleted', $original);

        return redirect()->route('admin.matches.statistics.index', $match)
            ->with('success', 'Match statistic deleted successfully.');
    }

    protected function formData(MatchFixture $match): array
    {
        return [
            'teams' => collect([$match->homeTeam, $match->awayTeam])->filter(),
            'metricKeys' => MatchStatistic::METRIC_KEYS,
            'contexts' => MatchStatistic::CONTEXTS,
        ];
    }

    protected function normaliseData(array $data): array
    {
        if (($data['display_value'] ?? null) === null || $data['display_value'] === '') {
            $data['display_value'] = $data['metric_value'] !== null
                ? $this->formatMetricValue($data['metric_key'], $data['metric_value'])
                : null;
        }

        return $data;
    }

    protected function formatMetricValue(string $metricKey, mixed $metricValue): string
    {
        $value = rtrim(rtrim((string) $metricValue, '0'), '.');

        if (in_array($metricKey, ['possession_percentage', 'pass_accuracy'], true)) {
            return $value.'%';
        }

        return $value;
    }
}
