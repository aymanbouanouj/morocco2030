<?php

namespace App\Services\Sports;

use App\Models\KnockoutProgression;
use App\Models\MatchFixture;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KnockoutProgressionService
{
    public function propagateCascade(MatchFixture $match): Collection
    {
        $results = collect();
        $queue = collect([$match]);
        $visited = [];

        while ($queue->isNotEmpty()) {
            /** @var MatchFixture $currentMatch */
            $currentMatch = $queue->shift();

            if (isset($visited[$currentMatch->id])) {
                continue;
            }

            $visited[$currentMatch->id] = true;

            $currentResults = $this->propagateForMatch($currentMatch->fresh([
                'homeTeam',
                'awayTeam',
                'sourceProgressions.targetMatch',
            ]));

            $results = $results->concat($currentResults);

            $currentResults
                ->where('changed', true)
                ->pluck('target_match_id')
                ->unique()
                ->each(function (int $targetMatchId) use ($queue) {
                    $targetMatch = MatchFixture::query()->find($targetMatchId);

                    if ($targetMatch && $targetMatch->isCompleted()) {
                        $queue->push($targetMatch);
                    }
                });
        }

        return $results->values();
    }

    public function propagateForMatch(MatchFixture $match): Collection
    {
        $match->loadMissing([
            'homeTeam',
            'awayTeam',
            'sourceProgressions.targetMatch',
        ]);

        if (! $match->isKnockoutStage() || ! $match->isCompleted()) {
            return collect();
        }

        $winner = $match->winnerTeam();
        $loser = $match->loserTeam();

        if (! $winner && ! $loser) {
            return collect();
        }

        return DB::transaction(function () use ($match, $winner, $loser) {
            $results = collect();

            foreach ($match->sourceProgressions as $progression) {
                $qualifiedTeam = $this->resolveQualifiedTeam($progression, $winner, $loser);
                $targetMatch = $progression->targetMatch;
                $slotColumn = $this->slotColumn($progression);

                if (! $qualifiedTeam || ! $targetMatch || ! $slotColumn) {
                    continue;
                }

                if (! $targetMatch->isKnockoutStage()) {
                    $results->push([
                        'progression_id' => $progression->id,
                        'target_match_id' => $targetMatch->id,
                        'slot' => $progression->team_slot,
                        'progression_type' => $progression->progression_type,
                        'team_id' => $qualifiedTeam->id,
                        'team_name' => $qualifiedTeam->name,
                        'changed' => false,
                        'skipped' => true,
                        'reason' => 'Target fixture is not a knockout fixture.',
                    ]);

                    continue;
                }

                if (in_array($targetMatch->status, ['live', 'completed'], true)) {
                    $results->push([
                        'progression_id' => $progression->id,
                        'target_match_id' => $targetMatch->id,
                        'slot' => $progression->team_slot,
                        'progression_type' => $progression->progression_type,
                        'team_id' => $qualifiedTeam->id,
                        'team_name' => $qualifiedTeam->name,
                        'changed' => false,
                        'skipped' => true,
                        'reason' => 'Target fixture is already live or completed.',
                    ]);

                    continue;
                }

                if (
                    $targetMatch->{$slotColumn}
                    && (int) $targetMatch->{$slotColumn} !== (int) $qualifiedTeam->id
                ) {
                    $results->push([
                        'progression_id' => $progression->id,
                        'target_match_id' => $targetMatch->id,
                        'slot' => $progression->team_slot,
                        'progression_type' => $progression->progression_type,
                        'team_id' => $qualifiedTeam->id,
                        'team_name' => $qualifiedTeam->name,
                        'changed' => false,
                        'skipped' => true,
                        'reason' => 'Target slot already contains a different team.',
                    ]);

                    continue;
                }

                $changed = (int) $targetMatch->{$slotColumn} !== $qualifiedTeam->id;

                if ($changed) {
                    $targetMatch->forceFill([$slotColumn => $qualifiedTeam->id])->save();
                }

                $results->push([
                    'progression_id' => $progression->id,
                    'target_match_id' => $targetMatch->id,
                    'slot' => $progression->team_slot,
                    'progression_type' => $progression->progression_type,
                    'team_id' => $qualifiedTeam->id,
                    'team_name' => $qualifiedTeam->name,
                    'changed' => $changed,
                    'skipped' => false,
                ]);
            }

            return $results;
        });
    }

    protected function resolveQualifiedTeam(
        KnockoutProgression $progression,
        ?Team $winner,
        ?Team $loser
    ): ?Team {
        return match ($progression->progression_type) {
            'winner' => $winner,
            'loser' => $loser,
            default => null,
        };
    }

    protected function slotColumn(KnockoutProgression $progression): ?string
    {
        return match ($progression->team_slot) {
            'home' => 'home_team_id',
            'away' => 'away_team_id',
            default => null,
        };
    }
}
