<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Group;
use App\Models\MatchFixture;
use App\Models\News;
use App\Models\Partner;
use App\Models\Player;
use App\Models\Stadium;
use App\Models\Standing;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class SiteController extends Controller
{
    protected function publishedPublicNewsQuery(): Builder
    {
        return News::query()
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->where(function (Builder $query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    protected function activeTeamsQuery(): Builder
    {
        return Team::query()->where('status', 'active');
    }

    protected function fdWorldCupExists(): bool
    {
        return MatchFixture::query()
            ->where('code', 'like', 'FD-WC-%')
            ->exists();
    }

    protected function fdWorldCupMatchesQuery(): Builder
    {
        return MatchFixture::query()
            ->where('code', 'like', 'FD-WC-%');
    }

    protected function fdWorldCupRealTeamIds(): Collection
    {
        return $this->fdWorldCupMatchesQuery()
            ->pluck('home_team_id')
            ->merge($this->fdWorldCupMatchesQuery()->pluck('away_team_id'))
            ->filter()
            ->unique()
            ->values()
            ->pipe(fn (Collection $ids) => Team::query()
                ->whereIn('id', $ids)
                ->where($this->notPlaceholderTeamConstraint(...))
                ->pluck('id'));
    }

    protected function notPlaceholderTeamConstraint(Builder $query): void
    {
        $query->whereNull('meta->placeholder')
            ->orWhere('meta->placeholder', false)
            ->orWhere('meta->placeholder', 'false');
    }

    protected function fdWorldCupRealTeamsQuery(): Builder
    {
        return $this->activeTeamsQuery()
            ->whereIn('id', $this->fdWorldCupRealTeamIds());
    }

    protected function fdWorldCupStandingsGroups(?int $limit = null): Collection
    {
        $matches = $this->fdWorldCupMatchesQuery()
            ->with([
                'group',
                'homeTeam.mediaRelations.mediaFile',
                'homeTeam.translations.language',
                'awayTeam.mediaRelations.mediaFile',
                'awayTeam.translations.language',
            ])
            ->orderBy('match_date')
            ->get()
            ->filter(fn (MatchFixture $match) => $this->fdWorldCupApiGroup($match) !== null)
            ->values();

        $groups = $matches
            ->groupBy(fn (MatchFixture $match) => $this->fdWorldCupApiGroup($match)['raw'])
            ->map(function (Collection $groupMatches) {
                $apiGroup = $this->fdWorldCupApiGroup($groupMatches->first());
                $group = new Group();
                $group->forceFill([
                    'name' => $apiGroup['label'],
                    'code' => $apiGroup['code'],
                    'sort_order' => $apiGroup['sort_order'],
                ]);

                $teams = $groupMatches
                    ->flatMap(fn (MatchFixture $match) => [$match->homeTeam, $match->awayTeam])
                    ->filter(fn (?Team $team) => $team && ! $this->isPlaceholderTeam($team))
                    ->unique('id')
                    ->sortBy('name')
                    ->values();

                $standings = $this->buildFdWorldCupStandingsRows($teams, $groupMatches);

                $group->setRelation('teams', $teams);
                $group->setRelation('standings', $standings);
                $group->setAttribute('teams_count', $teams->count());

                return $group;
            })
            ->sortBy([
                ['sort_order', 'asc'],
                ['name', 'asc'],
            ])
            ->values();

        return $limit ? $groups->take($limit)->values() : $groups;
    }

    protected function fdWorldCupApiGroupCount(): int
    {
        return $this->fdWorldCupMatchesQuery()
            ->get(['id', 'group_id', 'meta'])
            ->map(fn (MatchFixture $match) => $this->fdWorldCupApiGroup($match)['raw'] ?? null)
            ->filter()
            ->unique()
            ->count();
    }

    protected function fdWorldCupApiGroup(MatchFixture $match): ?array
    {
        $rawGroup = data_get($match->meta, 'football_data.group')
            ?? data_get($match->meta, 'source_group');

        if (! is_string($rawGroup) || trim($rawGroup) === '') {
            if ($match->stage_type !== 'group' || ! $match->group_id || ! $match->relationLoaded('group') || ! $match->group) {
                return null;
            }

            $rawGroup = 'GROUP_'.$match->group->code;
        }

        $normalized = Str::upper(trim($rawGroup));

        if (preg_match('/^GROUP[_\s-]?([A-Z0-9]+)$/', $normalized, $matches) !== 1) {
            return null;
        }

        $code = $matches[1];

        return [
            'raw' => 'GROUP_'.$code,
            'code' => $code,
            'label' => data_get($match->meta, 'football_data.group_label') ?: 'Group '.$code,
            'sort_order' => preg_match('/^[A-Z]$/', $code) === 1 ? ord($code) - 64 : 99,
        ];
    }

    private function buildFdWorldCupStandingsRows(Collection $teams, Collection $matches): Collection
    {
        $stats = $teams->mapWithKeys(fn (Team $team) => [
            $team->id => [
                'team' => $team,
                'played' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'goal_difference' => 0,
                'points' => 0,
            ],
        ])->all();

        $matches
            ->filter(fn (MatchFixture $match) => $match->status === 'completed'
                && $match->home_score !== null
                && $match->away_score !== null
                && $match->homeTeam
                && $match->awayTeam
                && ! $this->isPlaceholderTeam($match->homeTeam)
                && ! $this->isPlaceholderTeam($match->awayTeam))
            ->each(function (MatchFixture $match) use (&$stats): void {
                $homeId = $match->home_team_id;
                $awayId = $match->away_team_id;

                if (! isset($stats[$homeId], $stats[$awayId])) {
                    return;
                }

                $homeScore = (int) $match->home_score;
                $awayScore = (int) $match->away_score;

                $stats[$homeId]['played']++;
                $stats[$awayId]['played']++;
                $stats[$homeId]['goals_for'] += $homeScore;
                $stats[$homeId]['goals_against'] += $awayScore;
                $stats[$awayId]['goals_for'] += $awayScore;
                $stats[$awayId]['goals_against'] += $homeScore;

                if ($homeScore > $awayScore) {
                    $stats[$homeId]['won']++;
                    $stats[$homeId]['points'] += 3;
                    $stats[$awayId]['lost']++;
                } elseif ($awayScore > $homeScore) {
                    $stats[$awayId]['won']++;
                    $stats[$awayId]['points'] += 3;
                    $stats[$homeId]['lost']++;
                } else {
                    $stats[$homeId]['drawn']++;
                    $stats[$awayId]['drawn']++;
                    $stats[$homeId]['points']++;
                    $stats[$awayId]['points']++;
                }

                $stats[$homeId]['goal_difference'] = $stats[$homeId]['goals_for'] - $stats[$homeId]['goals_against'];
                $stats[$awayId]['goal_difference'] = $stats[$awayId]['goals_for'] - $stats[$awayId]['goals_against'];
            });

        return collect($stats)
            ->sortBy([
                ['points', 'desc'],
                ['goal_difference', 'desc'],
                ['goals_for', 'desc'],
                fn (array $a, array $b) => strcmp($a['team']->name, $b['team']->name),
            ])
            ->values()
            ->map(function (array $row, int $index) {
                $standing = new Standing([
                    'position' => $index + 1,
                    'played' => $row['played'],
                    'won' => $row['won'],
                    'drawn' => $row['drawn'],
                    'lost' => $row['lost'],
                    'goals_for' => $row['goals_for'],
                    'goals_against' => $row['goals_against'],
                    'goal_difference' => $row['goal_difference'],
                    'points' => $row['points'],
                ]);

                $standing->setRelation('team', $row['team']);

                return $standing;
            });
    }

    protected function isPlaceholderTeam(Team $team): bool
    {
        return (bool) data_get($team->meta, 'placeholder');
    }

    protected function activePlayersQuery(): Builder
    {
        return Player::query()->where('status', 'active');
    }

    protected function activeCitiesQuery(): Builder
    {
        return City::query()->where('status', 'active');
    }

    protected function activeStadiumsQuery(): Builder
    {
        return Stadium::query()->where('status', 'active');
    }

    protected function activePartnersQuery(): Builder
    {
        return Partner::query()->where('status', 'active');
    }

    protected function matchSummaryRelations(): array
    {
        return [
            'homeTeam.mediaRelations.mediaFile',
            'homeTeam.translations.language',
            'awayTeam.mediaRelations.mediaFile',
            'awayTeam.translations.language',
            'stadium',
            'stadium.translations.language',
            'city',
            'city.translations.language',
            'group',
            'targetQualificationRules.group',
            'targetProgressions.sourceMatch',
        ];
    }

    protected function matchDetailRelations(): array
    {
        return [
            ...$this->matchSummaryRelations(),
            'events' => fn ($query) => $query
                ->with([
                    'team.translations.language',
                    'player.translations.language',
                    'relatedPlayer.translations.language',
                ])
                ->orderBy('minute')
                ->orderBy('extra_minute')
                ->orderBy('sort_order'),
            'statistics' => fn ($query) => $query
                ->with('team.translations.language')
                ->orderBy('team_id')
                ->orderBy('context')
                ->orderBy('metric_key'),
            'lineups' => fn ($query) => $query
                ->with([
                    'team.translations.language',
                    'player.translations.language',
                ])
                ->orderBy('team_id')
                ->orderByRaw("CASE WHEN lineup_type = 'starting' THEN 0 ELSE 1 END")
                ->orderBy('sort_order')
                ->orderBy('shirt_number'),
            'sourceProgressions.targetMatch.targetProgressions.sourceMatch.homeTeam.translations.language',
            'sourceProgressions.targetMatch.targetProgressions.sourceMatch.awayTeam.translations.language',
            'targetProgressions.sourceMatch.homeTeam.translations.language',
            'targetProgressions.sourceMatch.awayTeam.translations.language',
        ];
    }
}
