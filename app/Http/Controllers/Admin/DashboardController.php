<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use App\Models\City;
use App\Models\Group;
use App\Models\MatchEvent;
use App\Models\MatchFixture;
use App\Models\News;
use App\Models\Player;
use App\Models\SportsAnalyticsSnapshot;
use App\Models\Stadium;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use App\Models\VisitorAnalytic;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends AdminController
{
    public function __invoke(): View
    {
        $fdWorldCupActive = $this->fdWorldCupExists();
        $matchScope = $fdWorldCupActive ? $this->fdWorldCupMatchesQuery() : MatchFixture::query();

        return view('admin.dashboard.index', [
            'stats' => [
                'totalNews' => News::query()->count(),
                'pendingNews' => News::query()->where('status', 'pending_review')->count(),
                'totalGroups' => $fdWorldCupActive
                    ? $this->fdWorldCupApiGroupCount()
                    : Group::query()->count(),
                'totalTeams' => $fdWorldCupActive
                    ? $this->fdWorldCupRealTeamIds()->count()
                    : Team::query()->count(),
                'totalPlayers' => Player::query()->count(),
                'totalCities' => $fdWorldCupActive
                    ? (clone $matchScope)->whereNotNull('city_id')->distinct('city_id')->count('city_id')
                    : City::query()->count(),
                'totalStadiums' => $fdWorldCupActive
                    ? (clone $matchScope)->whereNotNull('stadium_id')->distinct('stadium_id')->count('stadium_id')
                    : Stadium::query()->count(),
                'totalMatches' => (clone $matchScope)->count(),
            ],
            'fdWorldCupActive' => $fdWorldCupActive,
            'recentFixtures' => (clone $matchScope)
                ->with(['homeTeam', 'awayTeam', 'city', 'stadium', 'group'])
                ->orderByDesc('match_date')
                ->limit(5)
                ->get(),
            'recentActivity' => AuditLog::query()
                ->with(['user'])
                ->latest('occurred_at')
                ->limit(10)
                ->get(),
            'visitorAnalytics' => $this->visitorAnalytics(),
            'sportsAnalytics' => $this->sportsAnalytics($fdWorldCupActive),
        ]);
    }

    private function fdWorldCupExists(): bool
    {
        return MatchFixture::query()
            ->where('code', 'like', 'FD-WC-%')
            ->exists();
    }

    private function fdWorldCupMatchesQuery(): Builder
    {
        return MatchFixture::query()
            ->where('code', 'like', 'FD-WC-%');
    }

    private function fdWorldCupRealTeamIds(): Collection
    {
        return $this->fdWorldCupMatchesQuery()
            ->pluck('home_team_id')
            ->merge($this->fdWorldCupMatchesQuery()->pluck('away_team_id'))
            ->filter()
            ->unique()
            ->values()
            ->pipe(fn (Collection $ids) => Team::query()
                ->whereIn('id', $ids)
                ->where(function (Builder $query): void {
                    $query->whereNull('meta->placeholder')
                        ->orWhere('meta->placeholder', false)
                        ->orWhere('meta->placeholder', 'false');
                })
                ->pluck('id'));
    }

    private function fdWorldCupApiGroupCount(): int
    {
        return $this->fdWorldCupMatchesQuery()
            ->get(['id', 'group_id', 'stage_type', 'meta'])
            ->map(fn (MatchFixture $match) => $this->fdWorldCupApiGroup($match))
            ->filter()
            ->unique()
            ->count();
    }

    private function fdWorldCupApiGroup(MatchFixture $match): ?string
    {
        $rawGroup = data_get($match->meta, 'football_data.group')
            ?? data_get($match->meta, 'source_group');

        if (! is_string($rawGroup) || trim($rawGroup) === '') {
            return null;
        }

        $normalized = \Illuminate\Support\Str::upper(trim($rawGroup));

        if (preg_match('/^GROUP[_\s-]?([A-Z0-9]+)$/', $normalized, $matches) !== 1) {
            return null;
        }

        return 'GROUP_'.$matches[1];
    }

    private function visitorAnalytics(): array
    {
        $trendStart = now()->subDays(13)->startOfDay();
        $trendRows = VisitorAnalytic::query()
            ->selectRaw('DATE(event_at) as event_date, COUNT(*) as visits')
            ->where('event_type', 'page_view')
            ->where('event_at', '>=', $trendStart)
            ->groupByRaw('DATE(event_at)')
            ->orderBy('event_date')
            ->pluck('visits', 'event_date');

        $trendLabels = [];
        $trendValues = [];

        for ($index = 13; $index >= 0; $index--) {
            $date = now()->subDays($index)->toDateString();
            $trendLabels[] = Carbon::parse($date)->format('M j');
            $trendValues[] = (int) ($trendRows[$date] ?? 0);
        }

        return [
            'summary' => [
                'totalVisits' => VisitorAnalytic::query()->where('event_type', 'page_view')->count(),
                'recentVisits' => VisitorAnalytic::query()
                    ->where('event_type', 'page_view')
                    ->where('event_at', '>=', now()->subDays(7)->startOfDay())
                    ->count(),
                'uniqueSessions' => VisitorAnalytic::query()
                    ->where('event_type', 'page_view')
                    ->whereNotNull('session_id')
                    ->distinct('session_id')
                    ->count('session_id'),
            ],
            'visitsOverTime' => [
                'labels' => $trendLabels,
                'values' => $trendValues,
            ],
            'topPages' => $this->visitorBreakdownByExpression('COALESCE(route_name, path)', 'label', 6),
            'languages' => $this->visitorBreakdownByColumn('language_code', 6),
            'devices' => $this->visitorBreakdownByColumn('device_type', 6),
        ];
    }

    private function visitorBreakdownByColumn(string $column, int $limit): Collection
    {
        return VisitorAnalytic::query()
            ->select($column, DB::raw('COUNT(*) as total'))
            ->where('event_type', 'page_view')
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'label' => (string) $row->{$column},
                'value' => (int) $row->total,
            ]);
    }

    private function visitorBreakdownByExpression(string $expression, string $alias, int $limit): Collection
    {
        return VisitorAnalytic::query()
            ->selectRaw($expression.' as '.$alias.', COUNT(*) as total')
            ->where('event_type', 'page_view')
            ->groupByRaw($expression)
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'label' => (string) $row->{$alias},
                'value' => (int) $row->total,
            ]);
    }

    private function sportsAnalytics(bool $fdWorldCupActive): array
    {
        $matchScope = $fdWorldCupActive ? $this->fdWorldCupMatchesQuery() : MatchFixture::query();

        $matchesByStatus = (clone $matchScope)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $completedMatches = (clone $matchScope)
            ->where('status', 'completed')
            ->get(['home_team_id', 'away_team_id', 'home_score', 'away_score']);

        $totalGoals = $completedMatches->sum(
            fn (MatchFixture $match) => (int) $match->home_score + (int) $match->away_score
        );

        $eventsByType = $this->eventScope($fdWorldCupActive)
            ->select('event_type', DB::raw('COUNT(*) as total'))
            ->groupBy('event_type')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'label' => str_replace('_', ' ', (string) $row->event_type),
                'value' => (int) $row->total,
            ]);

        $matchesByCity = City::query()
            ->withCount([
                'matches' => fn ($query) => $fdWorldCupActive ? $query->where('code', 'like', 'FD-WC-%') : null,
            ])
            ->orderByDesc('matches_count')
            ->limit(6)
            ->get()
            ->filter(fn (City $city) => $city->matches_count > 0)
            ->map(fn (City $city) => [
                'label' => $city->name,
                'value' => (int) $city->matches_count,
            ])
            ->values();

        $goalsByTeam = $this->goalsByTeam($completedMatches);

        $snapshotTrend = $fdWorldCupActive
            ? collect()
            : SportsAnalyticsSnapshot::query()
                ->selectRaw('snapshot_date, SUM(metric_value) as total')
                ->where('snapshot_date', '>=', now()->subDays(30)->toDateString())
                ->groupBy('snapshot_date')
                ->orderBy('snapshot_date')
                ->limit(30)
                ->get()
                ->map(fn (SportsAnalyticsSnapshot $snapshot) => [
                    'label' => $snapshot->snapshot_date?->format('M j') ?? '',
                    'value' => (float) $snapshot->total,
                ]);

        return [
            'summary' => [
                'completedMatches' => (int) ($matchesByStatus['completed'] ?? 0),
                'upcomingMatches' => (int) (
                    ($matchesByStatus['scheduled'] ?? 0)
                    + ($matchesByStatus['postponed'] ?? 0)
                ),
                'liveMatches' => (int) ($matchesByStatus['live'] ?? 0),
                'totalGoals' => (int) $totalGoals,
                'yellowCards' => $this->eventScope($fdWorldCupActive)->where('event_type', 'yellow_card')->count(),
                'redCards' => $this->eventScope($fdWorldCupActive)->where('event_type', 'red_card')->count(),
            ],
            'matchesByStatus' => $matchesByStatus
                ->map(fn ($value, $label) => ['label' => str_replace('_', ' ', (string) $label), 'value' => (int) $value])
                ->values(),
            'eventsByType' => $eventsByType,
            'matchesByCity' => $matchesByCity,
            'goalsByTeam' => $goalsByTeam,
            'snapshotTrend' => $snapshotTrend,
        ];
    }

    private function eventScope(bool $fdWorldCupActive): Builder
    {
        $query = MatchEvent::query();

        if ($fdWorldCupActive) {
            $query->whereHas('match', fn (Builder $matchQuery) => $matchQuery->where('code', 'like', 'FD-WC-%'));
        }

        return $query;
    }

    private function goalsByTeam(Collection $completedMatches): Collection
    {
        $teamIds = $completedMatches
            ->flatMap(fn (MatchFixture $match) => [$match->home_team_id, $match->away_team_id])
            ->filter()
            ->unique()
            ->values();

        if ($teamIds->isEmpty()) {
            return collect();
        }

        $teamNames = Team::query()
            ->whereIn('id', $teamIds)
            ->pluck('name', 'id');

        $goals = [];

        foreach ($completedMatches as $match) {
            if ($match->home_team_id) {
                $goals[$match->home_team_id] = ($goals[$match->home_team_id] ?? 0) + (int) $match->home_score;
            }

            if ($match->away_team_id) {
                $goals[$match->away_team_id] = ($goals[$match->away_team_id] ?? 0) + (int) $match->away_score;
            }
        }

        arsort($goals);

        return collect($goals)
            ->take(6)
            ->map(fn (int $value, int $teamId) => [
                'label' => $teamNames[$teamId] ?? 'Team #'.$teamId,
                'value' => $value,
            ])
            ->values();
    }
}
