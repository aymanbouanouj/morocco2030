<?php

namespace Tests\Feature\Admin;

use App\Models\MatchFixture;
use App\Models\MatchLineup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class MatchLineupFlowTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_authorized_staff_can_manage_match_lineups_and_update_existing_entries(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('A');
        $homeTeam = $this->makeTeam($group, 'Morocco');
        $awayTeam = $this->makeTeam($group, 'Spain');
        $homePlayer = $this->makePlayer($homeTeam, 'Yassine Bounou', ['position' => 'goalkeeper', 'shirt_number' => 1]);

        $match = MatchFixture::query()->create([
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'M-LINE-01',
            'slug' => 'm-line-01',
            'stage_type' => 'group',
            'match_date' => now()->addHour(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($user);

        $this->get(route('admin.matches.lineups.index', ['match' => $match]))->assertOk();
        $this->get(route('admin.matches.lineups.create', ['match' => $match]))->assertOk();

        $this->post(route('admin.matches.lineups.store', ['match' => $match]), [
            'team_id' => $homeTeam->id,
            'player_id' => $homePlayer->id,
            'lineup_type' => 'starting',
            'sort_order' => 1,
            'position' => 'GK',
            'shirt_number' => 1,
            'formation_slot' => '1',
            'is_captain' => true,
            'is_goalkeeper' => true,
        ])->assertRedirect(route('admin.matches.lineups.index', ['match' => $match]));

        $lineup = MatchLineup::query()->firstOrFail();

        $this->from(route('admin.matches.lineups.create', ['match' => $match]))
            ->post(route('admin.matches.lineups.store', ['match' => $match]), [
                'team_id' => $homeTeam->id,
                'player_id' => $homePlayer->id,
                'lineup_type' => 'starting',
                'sort_order' => 2,
            ])
            ->assertRedirect(route('admin.matches.lineups.create', ['match' => $match]))
            ->assertSessionHasErrors('player_id');

        $this->get(route('admin.matches.lineups.edit', ['match' => $match, 'lineup' => $lineup]))->assertOk();

        $this->put(route('admin.matches.lineups.update', ['match' => $match, 'lineup' => $lineup]), [
            'team_id' => $homeTeam->id,
            'player_id' => $homePlayer->id,
            'lineup_type' => 'bench',
            'sort_order' => 12,
            'position' => 'GK',
            'shirt_number' => 1,
            'formation_slot' => null,
            'is_captain' => false,
            'is_goalkeeper' => true,
            'minute_in' => 60,
            'minute_out' => null,
        ])->assertRedirect(route('admin.matches.lineups.index', ['match' => $match]));

        $this->assertDatabaseHas('match_lineups', [
            'id' => $lineup->id,
            'lineup_type' => 'bench',
            'sort_order' => 12,
            'minute_in' => 60,
        ]);

        $this->delete(route('admin.matches.lineups.destroy', ['match' => $match, 'lineup' => $lineup]))
            ->assertRedirect(route('admin.matches.lineups.index', ['match' => $match]));

        $this->assertDatabaseMissing('match_lineups', [
            'id' => $lineup->id,
        ]);
    }

    public function test_match_lineups_require_players_from_the_selected_team(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('A');
        $homeTeam = $this->makeTeam($group, 'Morocco');
        $awayTeam = $this->makeTeam($group, 'Spain');
        $awayPlayer = $this->makePlayer($awayTeam, 'Pedri Gonzalez');

        $match = MatchFixture::query()->create([
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'M-LINE-02',
            'slug' => 'm-line-02',
            'stage_type' => 'group',
            'match_date' => now()->addHour(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($user)
            ->from(route('admin.matches.lineups.create', ['match' => $match]))
            ->post(route('admin.matches.lineups.store', ['match' => $match]), [
                'team_id' => $homeTeam->id,
                'player_id' => $awayPlayer->id,
                'lineup_type' => 'starting',
                'sort_order' => 1,
            ])
            ->assertRedirect(route('admin.matches.lineups.create', ['match' => $match]))
            ->assertSessionHasErrors('player_id');
    }
}
