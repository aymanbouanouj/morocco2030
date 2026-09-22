<?php

namespace Tests\Feature\Admin;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class TeamCrudFlowTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_authorized_staff_can_complete_the_team_crud_flow(): void
    {
        $user = $this->makeStaffUser(['teams.manage']);
        $groupA = $this->makeGroup('A');
        $groupB = $this->makeGroup('B');

        $this->actingAs($user);

        $this->get(route('admin.teams.index'))->assertOk();
        $this->get(route('admin.teams.create'))->assertOk();

        $this->post(route('admin.teams.store'), [
            'group_id' => $groupA->id,
            'name' => 'Morocco National Team',
            'short_name' => 'MAR',
            'code' => 'MOR',
            'slug' => 'morocco-national-team',
            'federation_name' => 'Royal Moroccan Football Federation',
            'founded_year' => 1955,
            'coach_name' => 'Walid Regragui',
            'team_type' => 'national',
            'status' => 'active',
        ])->assertRedirect(route('admin.teams.index'));

        $team = Team::query()->where('code', 'MOR')->firstOrFail();

        $this->get(route('admin.teams.edit', $team))->assertOk();

        $this->put(route('admin.teams.update', $team), [
            'group_id' => $groupB->id,
            'name' => 'Morocco First Team',
            'short_name' => 'MAR',
            'code' => 'MOR',
            'slug' => 'morocco-first-team',
            'federation_name' => 'Royal Moroccan Football Federation',
            'founded_year' => 1955,
            'coach_name' => 'Updated Coach',
            'team_type' => 'national',
            'status' => 'inactive',
        ])->assertRedirect(route('admin.teams.index'));

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'group_id' => $groupB->id,
            'name' => 'Morocco First Team',
            'coach_name' => 'Updated Coach',
            'status' => 'inactive',
        ]);

        $this->delete(route('admin.teams.destroy', $team))
            ->assertRedirect(route('admin.teams.index'));

        $this->assertSoftDeleted('teams', [
            'id' => $team->id,
        ]);
    }
}
