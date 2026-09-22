<?php

namespace Tests\Feature\Admin;

use App\Models\MatchFixture;
use App\Models\Stadium;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class FootballDataMoroccoVenueMappingTest extends TestCase
{
    use BuildsAdminTestData;
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
        config([
            'services.external_football.provider' => 'football-data',
            'services.external_football.base_url' => 'https://api.football-data.org/v4',
            'services.external_football.token' => 'test-token-not-secret',
            'services.external_football.timeout' => 15,
        ]);
    }

    public function test_fixtures_map_to_existing_moroccan_stadiums(): void
    {
        $city = $this->makeCity('Rabat', ['code' => 'RBA']);
        $this->makeStadium($city, 'Prince Moulay Abdellah Stadium', ['code' => 'PMA']);

        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->payload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview'))
            ->assertOk()
            ->assertSee('Prince Moulay Abdellah Stadium')
            ->assertSee('Rabat')
            ->assertSee('Matches Mapped To Moroccan Stadiums')
            ->assertSee('1');
    }

    public function test_no_foreign_stadium_is_created(): void
    {
        $city = $this->makeCity('Marrakech', ['code' => 'MAR']);
        $this->makeStadium($city, 'Marrakech Stadium', ['code' => 'MRK']);
        $stadiumCount = Stadium::query()->count();

        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->payload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview'))
            ->assertOk();

        $this->assertSame($stadiumCount, Stadium::query()->count());
    }

    public function test_mapping_blocked_when_no_local_stadiums_exist(): void
    {
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->payload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview'))
            ->assertOk()
            ->assertSee('No active Moroccan stadiums are available for local venue mapping')
            ->assertSee('Mapping Blocked');
    }

    public function test_duplicate_preview_matches_existing_teams_and_matches(): void
    {
        $city = $this->makeCity('Tangier', ['code' => 'TNG']);
        $stadium = $this->makeStadium($city, 'Grand Stade de Tanger', ['code' => 'GST']);
        $group = $this->makeGroup('A');
        $home = $this->makeTeam($group, 'Mexico', ['code' => 'MEX']);
        $away = $this->makeTeam($group, 'South Africa', ['code' => 'RSA']);
        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $group->id,
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'code' => 'FD-WC-537327',
            'slug' => 'fd-wc-537327',
            'stage_type' => 'group',
            'round_number' => 1,
            'match_date' => '2026-06-11 19:00:00',
            'timezone' => 'UTC',
            'status' => 'completed',
            'home_score' => 2,
            'away_score' => 1,
            'published_at' => now(),
        ]);

        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->payload(), 200),
        ]);

        $teamCount = Team::query()->count();
        $matchCount = MatchFixture::query()->count();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview'))
            ->assertOk()
            ->assertSee('would match existing')
            ->assertSee('Matches Already Matched');

        $this->assertSame($teamCount, Team::query()->count());
        $this->assertSame($matchCount, MatchFixture::query()->count());
    }

    protected function payload(): array
    {
        return [
            'competition' => ['id' => 2000, 'name' => 'FIFA World Cup', 'code' => 'WC'],
            'matches' => [
                [
                    'id' => 537327,
                    'utcDate' => '2026-06-11T19:00:00Z',
                    'status' => 'FINISHED',
                    'stage' => 'GROUP_STAGE',
                    'group' => 'Group A',
                    'matchday' => 1,
                    'homeTeam' => ['id' => 769, 'name' => 'Mexico', 'tla' => 'MEX'],
                    'awayTeam' => ['id' => 766, 'name' => 'South Africa', 'tla' => 'RSA'],
                    'score' => ['fullTime' => ['home' => 2, 'away' => 1]],
                ],
            ],
        ];
    }
}
