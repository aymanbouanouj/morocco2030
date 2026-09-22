<?php

namespace Tests\Feature\Admin;

use App\Models\Group;
use App\Models\KnockoutProgression;
use App\Models\MatchFixture;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_admin_can_create_an_unresolved_knockout_fixture(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);

        $this->actingAs($user)
            ->post(route('admin.matches.store'), [
                'code' => 'M-KO-NEW',
                'slug' => 'm-ko-new',
                'stage_type' => 'semi_final',
                'match_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'status' => 'scheduled',
                'timezone' => 'Africa/Casablanca',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('matches', [
            'code' => 'M-KO-NEW',
            'stage_type' => 'semi_final',
            'home_team_id' => null,
            'away_team_id' => null,
        ]);
    }

    public function test_group_stage_fixture_creation_still_requires_assigned_teams(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('A');

        $this->actingAs($user)
            ->from(route('admin.matches.create'))
            ->post(route('admin.matches.store'), [
                'group_id' => $group->id,
                'code' => 'M-GRP-NEW',
                'slug' => 'm-grp-new',
                'stage_type' => 'group',
                'match_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'status' => 'scheduled',
                'timezone' => 'Africa/Casablanca',
            ])
            ->assertRedirect(route('admin.matches.create'))
            ->assertSessionHasErrors(['home_team_id', 'away_team_id']);
    }

    public function test_group_stage_fixture_creation_succeeds_with_valid_teams(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('D');
        $homeTeam = $this->makeTeam($group, 'Casablanca');
        $awayTeam = $this->makeTeam($group, 'Tangier');

        $this->actingAs($user)
            ->post(route('admin.matches.store'), [
                'group_id' => $group->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'code' => 'M-GRP-NEW',
                'slug' => 'm-grp-new',
                'stage_type' => 'group',
                'match_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'status' => 'scheduled',
                'timezone' => 'Africa/Casablanca',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('matches', [
            'code' => 'M-GRP-NEW',
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);
    }

    public function test_group_stage_fixture_requires_teams_from_the_selected_group(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $groupA = $this->makeGroup('A');
        $groupB = $this->makeGroup('B');
        $homeTeam = $this->makeTeam($groupA, 'Casablanca');
        $awayTeam = $this->makeTeam($groupB, 'Tangier');

        $this->actingAs($user)
            ->from(route('admin.matches.create'))
            ->post(route('admin.matches.store'), [
                'group_id' => $groupA->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'code' => 'M-GRP-MISMATCH',
                'slug' => 'm-grp-mismatch',
                'stage_type' => 'group',
                'match_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'status' => 'scheduled',
                'timezone' => 'Africa/Casablanca',
            ])
            ->assertRedirect(route('admin.matches.create'))
            ->assertSessionHasErrors(['away_team_id']);
    }

    public function test_completed_knockout_fixture_requires_resolved_teams_and_scores(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);

        $this->actingAs($user)
            ->from(route('admin.matches.create'))
            ->post(route('admin.matches.store'), [
                'code' => 'M-KO-COMP',
                'slug' => 'm-ko-comp',
                'stage_type' => 'quarter_final',
                'match_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'status' => 'completed',
                'timezone' => 'Africa/Casablanca',
            ])
            ->assertRedirect(route('admin.matches.create'))
            ->assertSessionHasErrors(['home_team_id', 'away_team_id', 'home_score', 'away_score']);
    }

    public function test_completed_knockout_fixture_requires_a_decisive_result(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('K');
        $homeTeam = $this->makeTeam($group, 'Atlas');
        $awayTeam = $this->makeTeam($group, 'Sahara');

        $this->actingAs($user)
            ->from(route('admin.matches.create'))
            ->post(route('admin.matches.store'), [
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'code' => 'M-KO-TIE',
                'slug' => 'm-ko-tie',
                'stage_type' => 'quarter_final',
                'match_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'status' => 'completed',
                'timezone' => 'Africa/Casablanca',
                'home_score' => 1,
                'away_score' => 1,
            ])
            ->assertRedirect(route('admin.matches.create'))
            ->assertSessionHasErrors(['home_penalty_score']);
    }

    public function test_match_operations_page_renders_sports_sections(): void
    {
        $user = $this->makeStaffUser(['matches.manage', 'standings.manage']);
        $group = $this->makeGroup('A');
        $homeTeam = $this->makeTeam($group, 'Atlas');
        $awayTeam = $this->makeTeam($group, 'Sahara');

        $match = MatchFixture::query()->create([
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'M-OPS-01',
            'slug' => 'm-ops-01',
            'stage_type' => 'group',
            'match_date' => now()->addDay(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($user)
            ->get(route('admin.matches.show', $match))
            ->assertOk()
            ->assertSee('Manage Events')
            ->assertSee('Manage Statistics')
            ->assertSee('Manage Lineups')
            ->assertSee('Group Standings');
    }

    public function test_authorized_admin_can_recalculate_group_standings_from_match_route(): void
    {
        $user = $this->makeStaffUser(['matches.manage', 'standings.manage']);
        $group = $this->makeGroup('B');
        $winner = $this->makeTeam($group, 'Rif');
        $loser = $this->makeTeam($group, 'Draa');

        $match = MatchFixture::query()->create([
            'group_id' => $group->id,
            'home_team_id' => $winner->id,
            'away_team_id' => $loser->id,
            'code' => 'M-GRP-01',
            'slug' => 'm-grp-01',
            'stage_type' => 'group',
            'match_date' => now()->subDay(),
            'status' => 'completed',
            'home_score' => 2,
            'away_score' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('admin.matches.recalculate-standings', $match))
            ->assertRedirect();

        $this->assertDatabaseHas('standings', [
            'group_id' => $group->id,
            'team_id' => $winner->id,
            'position' => 1,
            'played' => 1,
            'won' => 1,
            'points' => 3,
            'goal_difference' => 2,
        ]);

        $this->assertDatabaseHas('standings', [
            'group_id' => $group->id,
            'team_id' => $loser->id,
            'position' => 2,
            'played' => 1,
            'lost' => 1,
            'points' => 0,
            'goal_difference' => -2,
        ]);
    }

    public function test_authorized_admin_can_propagate_knockout_results_from_match_route(): void
    {
        $user = $this->makeStaffUser(['matches.manage']);
        $group = $this->makeGroup('C');
        $winner = $this->makeTeam($group, 'Marrakech');
        $loser = $this->makeTeam($group, 'Agadir');

        $sourceMatch = MatchFixture::query()->create([
            'home_team_id' => $winner->id,
            'away_team_id' => $loser->id,
            'code' => 'M-KO-01',
            'slug' => 'm-ko-01',
            'stage_type' => 'quarter_final',
            'match_date' => now()->subHour(),
            'status' => 'completed',
            'home_score' => 1,
            'away_score' => 0,
        ]);

        $targetMatch = MatchFixture::query()->create([
            'code' => 'M-KO-02',
            'slug' => 'm-ko-02',
            'stage_type' => 'semi_final',
            'match_date' => now()->addHour(),
            'status' => 'scheduled',
        ]);

        KnockoutProgression::query()->create([
            'source_match_id' => $sourceMatch->id,
            'target_match_id' => $targetMatch->id,
            'progression_type' => 'winner',
            'team_slot' => 'home',
        ]);

        KnockoutProgression::query()->create([
            'source_match_id' => $sourceMatch->id,
            'target_match_id' => $targetMatch->id,
            'progression_type' => 'loser',
            'team_slot' => 'away',
        ]);

        $this->actingAs($user)
            ->post(route('admin.matches.propagate-knockout', $sourceMatch))
            ->assertRedirect();

        $this->assertDatabaseHas('matches', [
            'id' => $targetMatch->id,
            'home_team_id' => $winner->id,
            'away_team_id' => $loser->id,
        ]);
    }

    private function makeStaffUser(array $permissionSlugs): User
    {
        $user = User::factory()->create([
            'user_type' => 'staff',
            'status' => 'active',
        ]);

        $role = Role::query()->create([
            'name' => 'Sports Operator',
            'slug' => 'sports-operator',
            'description' => 'Sports operations test role.',
            'is_system' => false,
        ]);

        foreach ($permissionSlugs as $slug) {
            $permission = Permission::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => ucwords(str_replace(['.', '-'], ' ', $slug)),
                    'module' => 'competition',
                    'description' => 'Permission used for sports engine tests.',
                ]
            );

            $role->permissions()->syncWithoutDetaching($permission->id);
        }

        $user->roles()->attach($role->id);

        return $user;
    }

    private function makeGroup(string $code): Group
    {
        return Group::query()->create([
            'name' => 'Group '.$code,
            'code' => $code,
            'sort_order' => 1,
        ]);
    }

    private function makeTeam(Group $group, string $name): Team
    {
        $slug = str($name)->slug()->toString();
        $sequence = Team::query()->count() + 1;

        return Team::query()->create([
            'group_id' => $group->id,
            'name' => $name,
            'code' => strtoupper(substr(preg_replace('/[^a-z0-9]/i', '', $slug) ?: 'tm', 0, 3)).str_pad((string) $sequence, 2, '0', STR_PAD_LEFT),
            'slug' => $slug.'-'.$sequence,
        ]);
    }
}
