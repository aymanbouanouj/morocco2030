<?php

namespace Tests\Feature\Admin;

use App\Models\MatchEvent;
use App\Models\MatchFixture;
use App\Models\Role;
use App\Models\User;
use App\Models\VisitorAnalytic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class DashboardLayoutTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_admin_dashboard_renders_with_the_responsive_layout_structure(): void
    {
        $user = User::factory()->create([
            'user_type' => 'staff',
            'status' => 'active',
        ]);

        $role = Role::query()->create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Full administrative access for dashboard testing.',
            'is_system' => true,
        ]);

        $user->roles()->attach($role->id);

        VisitorAnalytic::query()->create([
            'session_id' => 'analytics-session-1',
            'path' => '/matches',
            'route_name' => 'matches.index',
            'language_code' => 'en',
            'device_type' => 'desktop',
            'event_type' => 'page_view',
            'event_at' => now(),
        ]);

        VisitorAnalytic::query()->create([
            'session_id' => 'analytics-session-2',
            'path' => '/news',
            'route_name' => 'news.index',
            'language_code' => 'fr',
            'device_type' => 'mobile',
            'event_type' => 'page_view',
            'event_at' => now()->subDay(),
        ]);

        $group = $this->makeGroup('D');
        $city = $this->makeCity('Analytics City');
        $stadium = $this->makeStadium($city, 'Analytics Stadium');
        $homeTeam = $this->makeTeam($group, 'Analytics Home');
        $awayTeam = $this->makeTeam($group, 'Analytics Away');
        $match = MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'ANL-01',
            'slug' => 'anl-01',
            'stage_type' => 'group',
            'match_date' => now()->subHour(),
            'timezone' => 'Africa/Casablanca',
            'status' => 'completed',
            'home_score' => 3,
            'away_score' => 1,
        ]);

        MatchEvent::query()->create([
            'match_id' => $match->id,
            'team_id' => $homeTeam->id,
            'minute' => 30,
            'event_type' => 'goal',
            'period' => 'first_half',
            'sort_order' => 30,
        ]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('stats-grid', false)
            ->assertSee('dashboard-shell', false)
            ->assertSee('analytics-overview', false)
            ->assertSee('visitsOverTimeChart', false)
            ->assertSee('matchesByCityChart', false)
            ->assertSee('goalsByTeamChart', false)
            ->assertSee('quick-links-grid', false)
            ->assertSee(route('admin.groups.index'), false)
            ->assertSee('Total Visits')
            ->assertSee('Analytics Home')
            ->assertSee('Analytics City')
            ->assertSee('Recent Activity');
    }
}
