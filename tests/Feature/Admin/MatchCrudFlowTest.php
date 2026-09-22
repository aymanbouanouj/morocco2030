<?php

namespace Tests\Feature\Admin;

use App\Models\MatchFixture;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class MatchCrudFlowTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_authorized_staff_can_complete_the_match_crud_flow_and_auto_recalculate_standings(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('A');
        $city = $this->makeCity('Casablanca');
        $stadium = $this->makeStadium($city, 'Mohammed V Stadium');
        $homeTeam = $this->makeTeam($group, 'Morocco');
        $awayTeam = $this->makeTeam($group, 'Spain');

        $this->actingAs($user);

        $this->get(route('admin.matches.index'))->assertOk();
        $this->get(route('admin.matches.create'))->assertOk();

        $this->post(route('admin.matches.store'), [
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'M-A-01',
            'slug' => 'm-a-01',
            'stage_type' => 'group',
            'round_number' => 1,
            'match_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'timezone' => 'Africa/Casablanca',
            'status' => 'scheduled',
            'attendance' => 51000,
        ])->assertRedirect();

        $match = MatchFixture::query()->where('code', 'M-A-01')->firstOrFail();

        $this->get(route('admin.matches.show', $match))->assertOk();
        $this->get(route('admin.matches.edit', $match))->assertOk();

        $this->put(route('admin.matches.update', $match), [
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'M-A-01',
            'slug' => 'm-a-01',
            'stage_type' => 'group',
            'round_number' => 1,
            'match_date' => $match->match_date->format('Y-m-d H:i:s'),
            'timezone' => 'Africa/Casablanca',
            'status' => 'completed',
            'attendance' => 52500,
            'home_score' => 2,
            'away_score' => 1,
        ])->assertRedirect(route('admin.matches.show', $match));

        $this->assertDatabaseHas('matches', [
            'id' => $match->id,
            'status' => 'completed',
            'home_score' => 2,
            'away_score' => 1,
            'attendance' => 52500,
        ]);

        $this->assertDatabaseHas('standings', [
            'group_id' => $group->id,
            'team_id' => $homeTeam->id,
            'position' => 1,
            'points' => 3,
        ]);

        $this->assertDatabaseHas('standings', [
            'group_id' => $group->id,
            'team_id' => $awayTeam->id,
            'position' => 2,
            'points' => 0,
        ]);

        $this->delete(route('admin.matches.destroy', $match))
            ->assertRedirect(route('admin.matches.index'));

        $this->assertSoftDeleted('matches', [
            'id' => $match->id,
        ]);
    }
}
