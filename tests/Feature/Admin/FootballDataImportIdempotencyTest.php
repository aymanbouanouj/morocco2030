<?php

namespace Tests\Feature\Admin;

use App\Models\MatchFixture;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class FootballDataImportIdempotencyTest extends TestCase
{
    use BuildsAdminTestData;
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
        $this->configureFootballData();
    }

    public function test_running_import_twice_does_not_duplicate_teams(): void
    {
        $this->makeMoroccoVenue();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::sequence()
                ->push($this->worldCupPayload(), 200)
                ->push($this->worldCupPayload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.run'), $this->validImportRequest())
            ->assertOk();

        $teamCount = Team::query()->count();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.run'), $this->validImportRequest())
            ->assertOk()
            ->assertSee('Teams Matched Existing')
            ->assertSee('4');

        $this->assertSame($teamCount, Team::query()->count());
    }

    public function test_running_import_twice_does_not_duplicate_matches(): void
    {
        $this->makeMoroccoVenue();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::sequence()
                ->push($this->worldCupPayload(), 200)
                ->push($this->worldCupPayload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.run'), $this->validImportRequest())
            ->assertOk();

        $matchCount = MatchFixture::query()->count();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.run'), $this->validImportRequest())
            ->assertOk()
            ->assertSee('Matches Matched Existing')
            ->assertSee('2');

        $this->assertSame($matchCount, MatchFixture::query()->count());
    }

    public function test_import_uses_transaction_and_rolls_back_created_teams_and_matches_on_failure(): void
    {
        $city = $this->makeCity('Tangier', ['code' => 'TNG']);
        $stadium = $this->makeStadium($city, 'Grand Stade de Tanger', ['code' => 'GST']);
        $group = $this->makeGroup('Z');
        $home = $this->makeTeam($group, 'Manual Home', ['code' => 'MHO']);
        $away = $this->makeTeam($group, 'Manual Away', ['code' => 'MAW']);

        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $group->id,
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'code' => 'MANUAL-537327',
            'slug' => 'fd-wc-537327',
            'stage_type' => 'group',
            'round_number' => 1,
            'match_date' => '2026-06-10 19:00:00',
            'timezone' => 'UTC',
            'status' => 'scheduled',
            'published_at' => now(),
        ]);

        $teamCount = Team::query()->count();
        $matchCount = MatchFixture::query()->count();

        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->oneMatchPayload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.run'), $this->validImportRequest())
            ->assertOk()
            ->assertSee('External World Cup import failed and was rolled back');

        $this->assertSame($teamCount, Team::query()->count());
        $this->assertSame($matchCount, MatchFixture::query()->count());
        $this->assertDatabaseMissing('teams', ['code' => 'MEX']);
        $this->assertDatabaseMissing('matches', ['code' => 'FD-WC-537327']);
    }

    protected function configureFootballData(): void
    {
        config([
            'services.external_football.provider' => 'football-data',
            'services.external_football.base_url' => 'https://api.football-data.org/v4',
            'services.external_football.token' => 'test-token-not-secret',
            'services.external_football.timeout' => 15,
        ]);
    }

    protected function validImportRequest(): array
    {
        return [
            'confirmation' => 'IMPORT FOOTBALL DATA',
            'understands_write' => '1',
        ];
    }

    protected function makeMoroccoVenue(): void
    {
        $city = $this->makeCity('Casablanca', ['code' => 'CAS']);
        $this->makeStadium($city, 'Mohammed V Stadium', ['code' => 'MO5']);
    }

    protected function oneMatchPayload(): array
    {
        return [
            'competition' => ['id' => 2000, 'name' => 'FIFA World Cup', 'code' => 'WC', 'type' => 'CUP'],
            'season' => ['id' => 2026, 'startDate' => '2026-06-11', 'endDate' => '2026-07-19'],
            'matches' => [$this->worldCupPayload()['matches'][0]],
        ];
    }

    protected function worldCupPayload(): array
    {
        return [
            'competition' => ['id' => 2000, 'name' => 'FIFA World Cup', 'code' => 'WC', 'type' => 'CUP'],
            'season' => ['id' => 2026, 'startDate' => '2026-06-11', 'endDate' => '2026-07-19'],
            'matches' => [
                [
                    'id' => 537327,
                    'utcDate' => '2026-06-11T19:00:00Z',
                    'status' => 'FINISHED',
                    'stage' => 'GROUP_STAGE',
                    'group' => 'Group A',
                    'matchday' => 1,
                    'homeTeam' => ['id' => 769, 'name' => 'Mexico', 'tla' => 'MEX', 'crest' => null],
                    'awayTeam' => ['id' => 766, 'name' => 'South Africa', 'tla' => 'RSA', 'crest' => null],
                    'score' => ['fullTime' => ['home' => 2, 'away' => 1]],
                ],
                [
                    'id' => 537339,
                    'utcDate' => '2026-06-13T22:00:00Z',
                    'status' => 'TIMED',
                    'stage' => 'GROUP_STAGE',
                    'group' => 'Group C',
                    'matchday' => 1,
                    'homeTeam' => ['id' => 764, 'name' => 'Brazil', 'tla' => 'BRA', 'crest' => null],
                    'awayTeam' => ['id' => 815, 'name' => 'Morocco', 'tla' => 'MAR', 'crest' => null],
                    'score' => ['fullTime' => ['home' => null, 'away' => null]],
                ],
            ],
        ];
    }
}
