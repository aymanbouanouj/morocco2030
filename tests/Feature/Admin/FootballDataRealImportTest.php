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

class FootballDataRealImportTest extends TestCase
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

    public function test_super_admin_can_see_guarded_import_section(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.football-data-import.index'))
            ->assertOk()
            ->assertSee('Guarded real import')
            ->assertSee('IMPORT FOOTBALL DATA')
            ->assertSee('I understand this writes teams and matches to the local demo database')
            ->assertSee('This writes football-data.org World Cup demo data and maps fixtures to Moroccan venues')
            ->assertDontSee('test-token-not-secret');
    }

    public function test_journalist_cannot_access_import_run_route(): void
    {
        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->post(route('admin.football-data-import.run'), $this->validImportRequest())
            ->assertForbidden();
    }

    public function test_import_requires_exact_typed_confirmation(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.football-data-import.index'))
            ->post(route('admin.football-data-import.run'), [
                'confirmation' => 'IMPORT DATA',
                'understands_write' => '1',
            ])
            ->assertRedirect(route('admin.football-data-import.index'))
            ->assertSessionHasErrors('confirmation');
    }

    public function test_import_requires_confirmation_checkbox(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.football-data-import.index'))
            ->post(route('admin.football-data-import.run'), [
                'confirmation' => 'IMPORT FOOTBALL DATA',
            ])
            ->assertRedirect(route('admin.football-data-import.index'))
            ->assertSessionHasErrors('understands_write');
    }

    public function test_import_sends_auth_header_and_never_exposes_token(): void
    {
        $this->makeMoroccoVenue();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->worldCupPayload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.run'), $this->validImportRequest())
            ->assertOk()
            ->assertSee('Real import summary')
            ->assertDontSee('test-token-not-secret');

        Http::assertSent(fn ($request) => $request->hasHeader('X-Auth-Token')
            && $request->url() === 'https://api.football-data.org/v4/competitions/WC/matches');
    }

    public function test_real_import_creates_missing_teams_from_fake_wc_payload(): void
    {
        $this->makeMoroccoVenue();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->worldCupPayload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.run'), $this->validImportRequest())
            ->assertOk()
            ->assertSee('Teams Created')
            ->assertSee('4');

        $this->assertDatabaseHas('teams', ['name' => 'Mexico', 'code' => 'MEX']);
        $this->assertDatabaseHas('teams', ['name' => 'South Africa', 'code' => 'RSA']);
        $this->assertDatabaseHas('teams', ['name' => 'Brazil', 'code' => 'BRA']);
        $this->assertDatabaseHas('teams', ['name' => 'Morocco', 'code' => 'MAR']);
    }

    public function test_real_import_creates_missing_matches_from_fake_wc_payload(): void
    {
        $this->makeMoroccoVenue();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->worldCupPayload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.run'), $this->validImportRequest())
            ->assertOk()
            ->assertSee('Matches Created')
            ->assertSee('2');

        $this->assertDatabaseHas('matches', ['code' => 'FD-WC-537327', 'status' => 'completed']);
        $this->assertDatabaseHas('matches', ['code' => 'FD-WC-537339', 'status' => 'scheduled']);
    }

    public function test_real_import_maps_matches_to_existing_moroccan_stadiums(): void
    {
        $city = $this->makeCity('Rabat', ['code' => 'RBA']);
        $stadium = $this->makeStadium($city, 'Prince Moulay Abdellah Stadium', ['code' => 'PMA']);
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->oneMatchPayload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.run'), $this->validImportRequest())
            ->assertOk()
            ->assertSee('Matches Mapped To Moroccan Venues')
            ->assertSee('1');

        $this->assertDatabaseHas('matches', [
            'code' => 'FD-WC-537327',
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
        ]);
    }

    public function test_real_import_does_not_create_foreign_stadiums(): void
    {
        $this->makeMoroccoVenue();
        $stadiumCount = Stadium::query()->count();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->worldCupPayload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.run'), $this->validImportRequest())
            ->assertOk();

        $this->assertSame($stadiumCount, Stadium::query()->count());
    }

    public function test_real_import_creates_deterministic_tbd_teams_for_unresolved_fixture_slots(): void
    {
        $this->makeMoroccoVenue();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->unresolvedSlotPayload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.run'), $this->validImportRequest())
            ->assertOk()
            ->assertSee('Import complete')
            ->assertSee('Skipped')
            ->assertSee('0');

        $this->assertDatabaseHas('teams', ['name' => 'TBD Home FD-WC-537417', 'code' => 'FDH537417']);
        $this->assertDatabaseHas('teams', ['name' => 'TBD Away FD-WC-537417', 'code' => 'FDA537417']);
        $this->assertDatabaseHas('matches', ['code' => 'FD-WC-537417']);
    }

    public function test_api_failure_returns_safe_error_not_500(): void
    {
        $this->makeMoroccoVenue();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response(['message' => 'Rate limit exceeded'], 429),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.run'), $this->validImportRequest())
            ->assertOk()
            ->assertSee('API request failed with status 429: Rate limit exceeded')
            ->assertDontSee('test-token-not-secret');
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
                    'homeTeam' => ['id' => 769, 'name' => 'Mexico', 'tla' => 'MEX', 'crest' => 'https://example.test/mex.png'],
                    'awayTeam' => ['id' => 766, 'name' => 'South Africa', 'tla' => 'RSA', 'crest' => 'https://example.test/rsa.png'],
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

    protected function unresolvedSlotPayload(): array
    {
        return [
            'competition' => ['id' => 2000, 'name' => 'FIFA World Cup', 'code' => 'WC', 'type' => 'CUP'],
            'matches' => [
                [
                    'id' => 537417,
                    'utcDate' => '2026-07-04T19:00:00Z',
                    'status' => 'TIMED',
                    'stage' => 'LAST_16',
                    'group' => null,
                    'matchday' => 5,
                    'homeTeam' => ['id' => null, 'name' => null, 'tla' => null, 'crest' => null],
                    'awayTeam' => ['id' => null, 'name' => null, 'tla' => null, 'crest' => null],
                    'score' => ['fullTime' => ['home' => null, 'away' => null]],
                ],
            ],
        ];
    }
}
