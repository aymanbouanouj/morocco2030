<?php

namespace App\Services\Sports;

use App\Models\Group;
use App\Models\MatchFixture;
use App\Models\Standing;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StandingsRecalculationService
{
    public function recalculateForMatch(MatchFixture $match): Collection
    {
        if (! $match->isGroupStage() || ! $match->group_id) {
            return collect();
        }

        $group = $match->group()->with('teams')->first();

        if (! $group) {
            return collect();
        }

        return $this->recalculateGroup($group);
    }

    public function recalculateGroup(Group $group): Collection
    {
        $group->loadMissing('teams');

        $matches = MatchFixture::query()
            ->where('group_id', $group->id)
            ->where('stage_type', 'group')
            ->where('status', 'completed')
            ->whereNotNull('home_score')
            ->whereNotNull('away_score')
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('match_date')
            ->orderBy('id')
            ->get();

        $rows = [];

        foreach ($group->teams as $team) {
            $rows[$team->id] = $this->initialiseRow($team);
        }

        foreach ($matches as $match) {
            if (! $match->homeTeam || ! $match->awayTeam) {
                continue;
            }

            $rows[$match->homeTeam->id] ??= $this->initialiseRow($match->homeTeam);
            $rows[$match->awayTeam->id] ??= $this->initialiseRow($match->awayTeam);

            $this->applyMatchResult(
                $rows[$match->homeTeam->id],
                $rows[$match->awayTeam->id],
                (int) $match->home_score,
                (int) $match->away_score
            );
        }

        $rankedRows = array_values($rows);

        usort($rankedRows, fn (array $left, array $right) => $this->compareRows($left, $right));

        return DB::transaction(function () use ($group, $rankedRows, $matches) {
            Standing::query()->where('group_id', $group->id)->delete();

            foreach ($rankedRows as $index => $row) {
                Standing::query()->create([
                    'group_id' => $group->id,
                    'team_id' => $row['team_id'],
                    'position' => $index + 1,
                    'played' => $row['played'],
                    'won' => $row['won'],
                    'drawn' => $row['drawn'],
                    'lost' => $row['lost'],
                    'goals_for' => $row['goals_for'],
                    'goals_against' => $row['goals_against'],
                    'goal_difference' => $row['goal_difference'],
                    'points' => $row['points'],
                    'form' => implode('', array_slice($row['form'], -5)),
                    'fair_play_points' => 0,
                    'meta' => [
                        'source_match_ids' => $matches->pluck('id')->all(),
                        'generated_at' => now()->toIso8601String(),
                    ],
                ]);
            }

            return Standing::query()
                ->where('group_id', $group->id)
                ->with('team')
                ->orderBy('position')
                ->get();
        });
    }

    protected function initialiseRow(Team $team): array
    {
        return [
            'team_id' => $team->id,
            'team_name' => $team->name,
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
            'points' => 0,
            'form' => [],
        ];
    }

    protected function applyMatchResult(array &$homeRow, array &$awayRow, int $homeScore, int $awayScore): void
    {
        $homeRow['played']++;
        $awayRow['played']++;

        $homeRow['goals_for'] += $homeScore;
        $homeRow['goals_against'] += $awayScore;
        $awayRow['goals_for'] += $awayScore;
        $awayRow['goals_against'] += $homeScore;

        $homeRow['goal_difference'] = $homeRow['goals_for'] - $homeRow['goals_against'];
        $awayRow['goal_difference'] = $awayRow['goals_for'] - $awayRow['goals_against'];

        if ($homeScore > $awayScore) {
            $homeRow['won']++;
            $homeRow['points'] += 3;
            $homeRow['form'][] = 'W';

            $awayRow['lost']++;
            $awayRow['form'][] = 'L';

            return;
        }

        if ($awayScore > $homeScore) {
            $awayRow['won']++;
            $awayRow['points'] += 3;
            $awayRow['form'][] = 'W';

            $homeRow['lost']++;
            $homeRow['form'][] = 'L';

            return;
        }

        $homeRow['drawn']++;
        $awayRow['drawn']++;
        $homeRow['points']++;
        $awayRow['points']++;
        $homeRow['form'][] = 'D';
        $awayRow['form'][] = 'D';
    }

    protected function compareRows(array $left, array $right): int
    {
        return
            ($right['points'] <=> $left['points'])
            ?: ($right['goal_difference'] <=> $left['goal_difference'])
            ?: ($right['goals_for'] <=> $left['goals_for'])
            ?: ($right['won'] <=> $left['won'])
            ?: strcasecmp($left['team_name'], $right['team_name']);
    }
}
