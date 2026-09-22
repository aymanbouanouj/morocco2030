<?php

namespace App\Services\Sports;

use App\Models\Group;
use App\Models\GroupQualificationRule;
use App\Models\MatchFixture;
use App\Models\Standing;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GroupQualificationService
{
    public function applyForGroup(Group $group): Collection
    {
        $group->loadMissing([
            'teams',
            'standings.team',
            'qualificationRules.targetMatch',
            'qualificationRules.group',
        ]);

        if (! $this->groupIsResolved($group)) {
            return $group->qualificationRules
                ->map(fn (GroupQualificationRule $rule) => $this->result($rule, null, false, true, 'Group outcome is not fully resolved.'));
        }

        return DB::transaction(function () use ($group) {
            return $group->qualificationRules
                ->sortBy([
                    ['target_match_id', 'asc'],
                    ['team_slot', 'asc'],
                ])
                ->map(fn (GroupQualificationRule $rule) => $this->applyRule($rule, $group->standings))
                ->values();
        });
    }

    public function applyAllResolvedGroups(): Collection
    {
        return Group::query()
            ->with([
                'teams',
                'standings.team',
                'qualificationRules.targetMatch',
                'qualificationRules.group',
            ])
            ->whereHas('qualificationRules')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get()
            ->flatMap(fn (Group $group) => $this->applyForGroup($group))
            ->values();
    }

    public function groupIsResolved(Group $group): bool
    {
        $group->loadMissing(['teams', 'standings']);

        $teamIds = $group->teams->pluck('id')->filter()->values();
        $teamCount = $teamIds->count();

        if ($teamCount < 2) {
            return false;
        }

        $expectedMatches = intdiv($teamCount * ($teamCount - 1), 2);

        $completedMatches = MatchFixture::query()
            ->where('stage_type', 'group')
            ->where('group_id', $group->id)
            ->where('status', 'completed')
            ->whereNotNull('home_score')
            ->whereNotNull('away_score')
            ->whereIn('home_team_id', $teamIds)
            ->whereIn('away_team_id', $teamIds)
            ->count();

        if ($completedMatches < $expectedMatches) {
            return false;
        }

        if ($group->standings->count() < $teamCount) {
            return false;
        }

        return $group->standings->every(fn (Standing $standing) => (int) $standing->played >= $teamCount - 1);
    }

    protected function applyRule(GroupQualificationRule $rule, Collection $standings): array
    {
        $qualifiedStanding = $standings->firstWhere('position', (int) $rule->qualifying_position);
        $qualifiedTeam = $qualifiedStanding?->team;
        $targetMatch = $rule->targetMatch;
        $slotColumn = $rule->slotColumn();

        if (! $qualifiedTeam || ! $targetMatch || ! $slotColumn) {
            return $this->result($rule, $qualifiedTeam, false, true, 'Qualification rule is incomplete.');
        }

        if (! $targetMatch->isKnockoutStage()) {
            return $this->result($rule, $qualifiedTeam, false, true, 'Target fixture is not a knockout fixture.');
        }

        if (in_array($targetMatch->status, ['live', 'completed'], true)) {
            return $this->result($rule, $qualifiedTeam, false, true, 'Target fixture is already live or completed.');
        }

        $currentTeamId = $targetMatch->{$slotColumn};

        if (
            $currentTeamId
            && (int) $currentTeamId !== (int) $qualifiedTeam->id
            && (int) $currentTeamId !== (int) $rule->applied_team_id
        ) {
            return $this->result($rule, $qualifiedTeam, false, true, 'Target slot already contains a different manually assigned team.');
        }

        $changed = (int) $currentTeamId !== (int) $qualifiedTeam->id;

        if ($changed) {
            $targetMatch->forceFill([$slotColumn => $qualifiedTeam->id])->save();
        }

        $rule->forceFill([
            'applied_team_id' => $qualifiedTeam->id,
            'applied_at' => now(),
        ])->save();

        return $this->result($rule, $qualifiedTeam, $changed, false);
    }

    protected function result(
        GroupQualificationRule $rule,
        ?Team $team,
        bool $changed,
        bool $skipped,
        ?string $reason = null
    ): array {
        return [
            'qualification_rule_id' => $rule->id,
            'group_id' => $rule->group_id,
            'group_code' => $rule->group?->code,
            'qualifying_position' => $rule->qualifying_position,
            'target_match_id' => $rule->target_match_id,
            'slot' => $rule->team_slot,
            'team_id' => $team?->id,
            'team_name' => $team?->name,
            'changed' => $changed,
            'skipped' => $skipped,
            'reason' => $reason,
        ];
    }
}
