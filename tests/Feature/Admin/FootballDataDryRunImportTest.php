<?php

namespace Tests\Feature\Admin;

use App\Models\MatchFixture;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class FootballDataDryRunImportTest extends TestCase
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

    public function test_admin_can_access_dry_run_page(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.football-data-import.index'))
            ->assertOk()
            ->assertSee('Football-Data Import Preview')
            ->assertSee('Dry-run only - no database write');
    }

    public function test_journalist_cannot_access_dry_run_page(): void
    {
        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->get(route('admin.football-data-import.index'))
            ->assertForbidden();
    }

    public function test_missing_token_shows_safe_disabled_state(): void
    {
        config(['services.external_football.token' => null]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.football-data-import.index'))
            ->assertOk()
            ->assertSee('Missing EXTERNAL_FOOTBALL_API_TOKEN')
            ->assertSee('Preview World Cup Dry Run');
    }

    public function test_token_is_never_displayed(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.football-data-import.index'))
            ->assertOk()
            ->assertDontSee('test-token-not-secret');
    }

    public function test_preview_calls_football_data_with_auth_header(): void
    {
        $this->makeMoroccoVenue();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->worldCupPayload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview'))
            ->assertOk()
            ->assertSee('Mexico vs South Africa');

        Http::assertSent(fn ($request) => $request->hasHeader('X-Auth-Token')
            && $request->url() === 'https://api.football-data.org/v4/competitions/WC/matches');
    }

    public function test_preview_normalizes_world_cup_matches_and_extracts_teams(): void
    {
        $this->makeMoroccoVenue();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->worldCupPayload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview'))
            ->assertOk()
            ->assertSee('FIFA World Cup')
            ->assertSee('Mexico')
            ->assertSee('South Africa')
            ->assertSee('completed')
            ->assertSee('group');
    }

    public function test_dry_run_does_not_write_team_or_match_records(): void
    {
        $this->makeMoroccoVenue();
        $teamCount = Team::query()->count();
        $matchCount = MatchFixture::query()->count();

        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($this->worldCupPayload(), 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview'))
            ->assertOk();

        $this->assertSame($teamCount, Team::query()->count());
        $this->assertSame($matchCount, MatchFixture::query()->count());
    }

    public function test_api_errors_return_safe_ui_not_500(): void
    {
        $this->makeMoroccoVenue();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response(['message' => 'Rate limit exceeded'], 429),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview'))
            ->assertOk()
            ->assertSee('API request failed with status 429: Rate limit exceeded')
            ->assertDontSee('test-token-not-secret');
    }

    public function test_no_hash_links_in_new_page(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.football-data-import.index'))
            ->assertOk()
            ->assertDontSee('href="#"', false);
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

    protected function makeMoroccoVenue(): void
    {
        $city = $this->makeCity('Casablanca', ['code' => 'CAS']);
        $this->makeStadium($city, 'Mohammed V Stadium', ['code' => 'MO5']);
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
}
