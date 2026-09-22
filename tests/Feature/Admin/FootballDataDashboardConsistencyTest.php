<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\DashboardController;
use App\Models\MatchEvent;
use App\Models\MatchFixture;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class FootballDataDashboardConsistencyTest extends TestCase
{
    use BuildsAdminTestData;
    use RefreshDatabase;

    public function test_admin_dashboard_sports_metrics_scope_to_fd_world_cup_when_imported_dataset_exists(): void
    {
        $this->seedFdWorldCupAndShowcaseMatches();

        $view = app(DashboardController::class)();
        $data = $view->getData();

        $this->assertTrue($data['fdWorldCupActive']);
        $this->assertSame(104, $data['stats']['totalMatches']);
        $this->assertSame(2, $data['stats']['totalTeams']);
        $this->assertSame(1, $data['stats']['totalGroups']);
        $this->assertSame(104, $data['sportsAnalytics']['matchesByStatus']->sum('value'));
        $this->assertSame(1, $data['sportsAnalytics']['summary']['completedMatches']);
        $this->assertSame(2, $data['sportsAnalytics']['summary']['totalGoals']);
        $this->assertSame(0, $data['sportsAnalytics']['summary']['yellowCards']);
        $this->assertTrue($data['recentFixtures']->every(fn ($match) => str_starts_with($match->code, 'FD-WC-')));
    }

    public function test_admin_dashboard_rendered_fixture_table_excludes_showcase_matches(): void
    {
        $this->seedFdWorldCupAndShowcaseMatches();
        $user = $this->makeStaffUser(['matches.manage']);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('104', false)
            ->assertSee('FD-WC source', false)
            ->assertSee('FD-WC-537327', false)
            ->assertDontSee('SHOWCASE-001', false)
            ->assertDontSee('Showcase Legends', false);
    }

    private function seedFdWorldCupAndShowcaseMatches(): void
    {
        $city = $this->makeCity('Casablanca', ['code' => 'CAS']);
        $stadium = $this->makeStadium($city, 'Mohammed V Stadium', ['code' => 'MO5']);
        $fdGroup = $this->makeGroup('A');
        $showcaseGroup = $this->makeGroup('Z');

        $mexico = $this->makeTeam($fdGroup, 'Mexico', ['code' => 'MEX']);
        $southAfrica = $this->makeTeam($fdGroup, 'South Africa', ['code' => 'RSA']);
        $showcaseTeam = $this->makeTeam($showcaseGroup, 'Showcase Legends', ['code' => 'OLD']);
        $showcaseOpponent = $this->makeTeam($showcaseGroup, 'Showcase Rivals', ['code' => 'RIV']);

        $mexico->update(['meta' => ['source' => 'football-data.org', 'football_data' => ['crest' => 'https://crests.example/mex.svg']]]);
        $southAfrica->update(['meta' => ['source' => 'football-data.org', 'football_data' => ['crest' => 'https://crests.example/rsa.svg']]]);

        for ($i = 0; $i < 104; $i++) {
            $id = $i === 0 ? 537327 : (600000 + $i);

            MatchFixture::query()->create([
                'stadium_id' => $stadium->id,
                'city_id' => $city->id,
                'group_id' => $fdGroup->id,
                'home_team_id' => $mexico->id,
                'away_team_id' => $southAfrica->id,
                'code' => 'FD-WC-'.$id,
                'slug' => Str::slug('FD-WC-'.$id),
                'stage_type' => 'group',
                'round_number' => 1,
                'match_date' => $i === 0 ? now()->addDays(200) : now()->addDays($i),
                'timezone' => 'UTC',
                'status' => $i === 0 ? 'completed' : 'scheduled',
                'home_score' => $i === 0 ? 2 : null,
                'away_score' => $i === 0 ? 0 : null,
                'published_at' => now(),
                'meta' => [
                    'source' => 'football-data.org',
                    'source_match_id' => $id,
                    'football_data' => [
                        'id' => $id,
                        'group' => 'GROUP_A',
                        'group_label' => 'Group A',
                        'stage' => 'GROUP_STAGE',
                        'stage_label' => 'Group Stage',
                        'matchday' => 1,
                    ],
                ],
            ]);
        }

        $showcaseMatch = MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $showcaseGroup->id,
            'home_team_id' => $showcaseTeam->id,
            'away_team_id' => $showcaseOpponent->id,
            'code' => 'SHOWCASE-001',
            'slug' => 'showcase-001',
            'stage_type' => 'group',
            'round_number' => 1,
            'match_date' => now()->addYears(5),
            'timezone' => 'UTC',
            'status' => 'completed',
            'home_score' => 9,
            'away_score' => 8,
            'published_at' => now(),
        ]);

        MatchEvent::query()->create([
            'match_id' => $showcaseMatch->id,
            'team_id' => $showcaseTeam->id,
            'minute' => 12,
            'period' => 'first_half',
            'event_type' => 'yellow_card',
            'description' => 'Legacy event that must not affect FD-WC dashboard counts.',
            'sort_order' => 1,
        ]);
    }
}
