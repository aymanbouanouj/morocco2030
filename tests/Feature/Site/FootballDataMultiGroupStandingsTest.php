<?php

namespace Tests\Feature\Site;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\StandingsController;
use App\Models\MatchFixture;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class FootballDataMultiGroupStandingsTest extends TestCase
{
    use BuildsAdminTestData;
    use RefreshDatabase;

    public function test_public_standings_show_multiple_api_groups_not_one_merged_group(): void
    {
        $this->seedMultiGroupFdWorldCup();

        $view = app(StandingsController::class)->index();
        $groups = $view->getData()['groups'];

        $this->assertSame(['A', 'B', 'C'], $groups->pluck('code')->all());
        $this->assertSame(['Mexico', 'South Africa'], $groups->firstWhere('code', 'A')->standings->pluck('team.name')->all());
        $this->assertSame(['Bosnia-Herzegovina', 'Canada'], $groups->firstWhere('code', 'B')->standings->pluck('team.name')->all());

        $this->get(route('standings.index'))
            ->assertOk()
            ->assertSee('Group A', false)
            ->assertSee('Group B', false)
            ->assertDontSee('Showcase Legends', false)
            ->assertDontSee('TBD Home FD-WC', false);
    }

    public function test_completed_scores_affect_only_the_correct_api_group_and_scheduled_matches_do_not_fake_points(): void
    {
        $this->seedMultiGroupFdWorldCup();

        $groups = app(StandingsController::class)->index()->getData()['groups'];
        $groupA = $groups->firstWhere('code', 'A');
        $groupB = $groups->firstWhere('code', 'B');
        $mexico = $groupA->standings->firstWhere('team.code', 'MEX');
        $southAfrica = $groupA->standings->firstWhere('team.code', 'RSA');
        $canada = $groupB->standings->firstWhere('team.code', 'CAN');
        $bosnia = $groupB->standings->firstWhere('team.code', 'BIH');
        $scheduledOnly = $groups->firstWhere('code', 'C')->standings->firstWhere('team.code', 'BRA');

        $this->assertSame(3, $mexico->points);
        $this->assertSame(2, $mexico->goals_for);
        $this->assertSame(0, $southAfrica->points);
        $this->assertSame(1, $canada->points);
        $this->assertSame(1, $bosnia->points);
        $this->assertNull($groupB->standings->firstWhere('team.code', 'MEX'));
        $this->assertSame(0, $scheduledOnly->points);
        $this->assertSame(0, $scheduledOnly->played);
    }

    public function test_standings_exclude_knockout_unassigned_matches_and_tbd_placeholders(): void
    {
        $this->seedMultiGroupFdWorldCup();

        $groups = app(StandingsController::class)->index()->getData()['groups'];
        $allTeamNames = $groups->flatMap(fn ($group) => $group->standings->pluck('team.name'));

        $this->assertFalse($groups->contains(fn ($group) => $group->code === 'LAST_32'));
        $this->assertFalse($allTeamNames->contains(fn ($name) => str_contains($name, 'TBD')));
    }

    public function test_home_and_dashboard_group_counts_use_api_groups_not_old_groups(): void
    {
        $this->seedMultiGroupFdWorldCup();

        $homeMetrics = collect(app(HomeController::class)()->getData()['tournamentMetrics'])->pluck('value', 'label');
        $dashboardData = app(DashboardController::class)()->getData();

        $this->assertSame(3, $homeMetrics['Groups']);
        $this->assertSame(3, $dashboardData['stats']['totalGroups']);
        $this->assertSame(6, $homeMetrics['Teams']);
        $this->assertSame(6, $dashboardData['stats']['totalTeams']);
    }

    private function seedMultiGroupFdWorldCup(): void
    {
        $city = $this->makeCity('Casablanca', ['code' => 'CAS']);
        $stadium = $this->makeStadium($city, 'Mohammed V Stadium', ['code' => 'MO5']);
        $groupA = $this->makeGroup('A');
        $groupB = $this->makeGroup('B');
        $groupC = $this->makeGroup('C');
        $showcaseGroup = $this->makeGroup('Z');

        $mexico = $this->makeTeam($groupA, 'Mexico', ['code' => 'MEX']);
        $southAfrica = $this->makeTeam($groupA, 'South Africa', ['code' => 'RSA']);
        $canada = $this->makeTeam($groupB, 'Canada', ['code' => 'CAN']);
        $bosnia = $this->makeTeam($groupB, 'Bosnia-Herzegovina', ['code' => 'BIH']);
        $brazil = $this->makeTeam($groupC, 'Brazil', ['code' => 'BRA']);
        $morocco = $this->makeTeam($groupC, 'Morocco', ['code' => 'MAR']);
        $tbdHome = $this->makeTeam($groupA, 'TBD Home FD-WC-537417', ['code' => 'FDH537417']);
        $tbdAway = $this->makeTeam($groupA, 'TBD Away FD-WC-537417', ['code' => 'FDA537417']);
        $showcase = $this->makeTeam($showcaseGroup, 'Showcase Legends', ['code' => 'OLD']);
        $showcaseOpponent = $this->makeTeam($showcaseGroup, 'Showcase Rivals', ['code' => 'RIV']);

        $tbdHome->update(['meta' => ['source' => 'football-data.org', 'placeholder' => true]]);
        $tbdAway->update(['meta' => ['source' => 'football-data.org', 'placeholder' => true]]);

        $this->makeFdMatch($stadium, $city, $groupA, $mexico, $southAfrica, 537327, 'GROUP_A', 'FINISHED', 2, 0);
        $this->makeFdMatch($stadium, $city, $groupB, $canada, $bosnia, 537333, 'GROUP_B', 'FINISHED', 1, 1);
        $this->makeFdMatch($stadium, $city, $groupC, $brazil, $morocco, 537339, 'GROUP_C', 'TIMED', null, null);
        $this->makeFdMatch($stadium, $city, null, $tbdHome, $tbdAway, 537417, null, 'TIMED', null, null, 'LAST_32', 'Round of 32', 'round_of_32');

        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $showcaseGroup->id,
            'home_team_id' => $showcase->id,
            'away_team_id' => $showcaseOpponent->id,
            'code' => 'SHOWCASE-001',
            'slug' => 'showcase-001',
            'stage_type' => 'group',
            'round_number' => 1,
            'match_date' => now()->subDay(),
            'timezone' => 'UTC',
            'status' => 'completed',
            'home_score' => 9,
            'away_score' => 8,
            'published_at' => now(),
        ]);
    }

    private function makeFdMatch(
        $stadium,
        $city,
        $group,
        $home,
        $away,
        int $sourceId,
        ?string $rawGroup,
        string $sourceStatus,
        ?int $homeScore,
        ?int $awayScore,
        string $rawStage = 'GROUP_STAGE',
        string $stageLabel = 'Group Stage',
        string $stageType = 'group'
    ): void {
        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $group?->id,
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'code' => 'FD-WC-'.$sourceId,
            'slug' => Str::slug('FD-WC-'.$sourceId),
            'stage_type' => $stageType,
            'round_number' => 1,
            'match_date' => now()->addDays($sourceId % 30),
            'timezone' => 'UTC',
            'status' => $sourceStatus === 'FINISHED' ? 'completed' : 'scheduled',
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'published_at' => now(),
            'meta' => [
                'source' => 'football-data.org',
                'source_match_id' => $sourceId,
                'football_data' => [
                    'id' => $sourceId,
                    'group' => $rawGroup,
                    'group_label' => $rawGroup ? str_replace('_', ' ', Str::title(Str::lower($rawGroup))) : null,
                    'stage' => $rawStage,
                    'stage_label' => $stageLabel,
                    'matchday' => $rawGroup ? 1 : null,
                ],
            ],
        ]);
    }
}
