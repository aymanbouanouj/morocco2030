<?php

namespace Tests\Feature\Admin;

use App\Models\KnockoutProgression;
use App\Models\GroupQualificationRule;
use App\Models\MatchFixture;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class CompetitionOperationsRegressionTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_completed_group_match_updates_recalculate_standings_automatically(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('A');
        $homeTeam = $this->makeTeam($group, 'Morocco');
        $awayTeam = $this->makeTeam($group, 'Spain');

        $match = MatchFixture::query()->create([
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'M-AUTO-01',
            'slug' => 'm-auto-01',
            'stage_type' => 'group',
            'match_date' => now()->subHour(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($user)
            ->put(route('admin.matches.update', $match), [
                'group_id' => $group->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'code' => 'M-AUTO-01',
                'slug' => 'm-auto-01',
                'stage_type' => 'group',
                'match_date' => $match->match_date->format('Y-m-d H:i:s'),
                'timezone' => 'Africa/Casablanca',
                'status' => 'completed',
                'home_score' => 3,
                'away_score' => 1,
            ])
            ->assertRedirect(route('admin.matches.show', $match));

        $this->assertDatabaseHas('standings', [
            'group_id' => $group->id,
            'team_id' => $homeTeam->id,
            'position' => 1,
            'points' => 3,
            'goal_difference' => 2,
        ]);
    }

    public function test_completed_group_match_auto_applies_standings_qualification_rules_when_group_is_resolved(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('Q');
        $homeTeam = $this->makeTeam($group, 'Qualifier One');
        $awayTeam = $this->makeTeam($group, 'Qualifier Two');
        $targetMatch = MatchFixture::query()->create([
            'code' => 'M-QUAL-TGT',
            'slug' => 'm-qual-tgt',
            'stage_type' => 'round_of_16',
            'match_date' => now()->addDays(3),
            'status' => 'scheduled',
        ]);

        GroupQualificationRule::query()->create([
            'group_id' => $group->id,
            'qualifying_position' => 1,
            'target_match_id' => $targetMatch->id,
            'team_slot' => 'home',
            'label' => 'Winner of Group Q',
        ]);

        $match = MatchFixture::query()->create([
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'M-QUAL-SRC',
            'slug' => 'm-qual-src',
            'stage_type' => 'group',
            'match_date' => now()->subHour(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($user)
            ->put(route('admin.matches.update', $match), [
                'group_id' => $group->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'code' => 'M-QUAL-SRC',
                'slug' => 'm-qual-src',
                'stage_type' => 'group',
                'match_date' => $match->match_date->format('Y-m-d H:i:s'),
                'timezone' => 'Africa/Casablanca',
                'status' => 'completed',
                'home_score' => 2,
                'away_score' => 0,
            ])
            ->assertRedirect(route('admin.matches.show', $match));

        $this->assertDatabaseHas('matches', [
            'id' => $targetMatch->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => null,
        ]);

        $this->assertDatabaseHas('group_qualification_rules', [
            'group_id' => $group->id,
            'qualifying_position' => 1,
            'applied_team_id' => $homeTeam->id,
        ]);
    }

    public function test_knockout_progression_does_not_override_live_target_matches(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('A');
        $winner = $this->makeTeam($group, 'Morocco');
        $loser = $this->makeTeam($group, 'Spain');
        $existingTargetTeam = $this->makeTeam($group, 'Portugal');

        $sourceMatch = MatchFixture::query()->create([
            'home_team_id' => $winner->id,
            'away_team_id' => $loser->id,
            'code' => 'M-KO-SRC',
            'slug' => 'm-ko-src',
            'stage_type' => 'quarter_final',
            'match_date' => now()->subHour(),
            'status' => 'completed',
            'home_score' => 2,
            'away_score' => 0,
        ]);

        $targetMatch = MatchFixture::query()->create([
            'home_team_id' => $existingTargetTeam->id,
            'away_team_id' => $loser->id,
            'code' => 'M-KO-TGT',
            'slug' => 'm-ko-tgt',
            'stage_type' => 'semi_final',
            'match_date' => now()->addHour(),
            'status' => 'live',
        ]);

        KnockoutProgression::query()->create([
            'source_match_id' => $sourceMatch->id,
            'target_match_id' => $targetMatch->id,
            'progression_type' => 'winner',
            'team_slot' => 'home',
        ]);

        $this->actingAs($user)
            ->post(route('admin.matches.propagate-knockout', $sourceMatch))
            ->assertRedirect();

        $this->assertDatabaseHas('matches', [
            'id' => $targetMatch->id,
            'home_team_id' => $existingTargetTeam->id,
            'status' => 'live',
        ]);
    }
}
