<?php

namespace Tests\Feature\Admin;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class FootballDataSquadDryRunTest extends TestCase
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

    public function test_super_admin_can_preview_squads(): void
    {
        $this->makeLocalFdTeam('Mexico', 'MEX', 769);
        $this->fakeSquadApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertOk()
            ->assertSee('Players &amp; Coaches Dry Run Summary', false)
            ->assertSee('Guillermo Ochoa')
            ->assertSee('Javier Aguirre')
            ->assertDontSee('test-token-not-secret');
    }

    public function test_journalist_cannot_preview_squads(): void
    {
        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertForbidden();
    }

    public function test_missing_token_shows_safe_error(): void
    {
        config(['services.external_football.token' => null]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertOk()
            ->assertSee('External football API is not fully configured')
            ->assertDontSee('test-token-not-secret');
    }

    public function test_token_never_displayed(): void
    {
        $this->makeLocalFdTeam('Mexico', 'MEX', 769);
        $this->fakeSquadApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertOk()
            ->assertDontSee('test-token-not-secret');
    }

    public function test_world_cup_teams_are_fetched_with_auth_token(): void
    {
        $this->makeLocalFdTeam('Mexico', 'MEX', 769);
        $this->fakeSquadApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertOk();

        Http::assertSent(fn ($request) => $request->hasHeader('X-Auth-Token')
            && $request->url() === 'https://api.football-data.org/v4/competitions/WC/teams');
    }

    public function test_team_endpoint_is_called_for_matched_real_teams(): void
    {
        $this->makeLocalFdTeam('Mexico', 'MEX', 769);
        $this->makeLocalFdTeam('Brazil', 'BRA', 764);
        $this->fakeSquadApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertOk();

        Http::assertSent(fn ($request) => $request->url() === 'https://api.football-data.org/v4/teams/769');
        Http::assertSent(fn ($request) => $request->url() === 'https://api.football-data.org/v4/teams/764');
        Http::assertNotSent(fn ($request) => $request->url() === 'https://api.football-data.org/v4/teams/759');
    }

    public function test_tbd_placeholders_are_skipped(): void
    {
        $this->makeLocalFdTeam('Mexico', 'MEX', 769);
        $this->makeLocalFdTeam('TBD Home FD-WC-537417', 'FDH537417', null, ['placeholder' => true]);
        $this->fakeSquadApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertOk()
            ->assertSee('Skipped Placeholders')
            ->assertSee('1')
            ->assertDontSee('TBD Home FD-WC-537417');
    }

    public function test_players_are_normalized_from_squad_payload(): void
    {
        $this->makeLocalFdTeam('Mexico', 'MEX', 769);
        $this->fakeSquadApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertOk()
            ->assertSee('goalkeeper')
            ->assertSee('1985-07-13')
            ->assertSee('Mexico')
            ->assertSee('would create');
    }

    public function test_coaches_are_detected_if_payload_provides_coach_data(): void
    {
        $this->makeLocalFdTeam('Mexico', 'MEX', 769);
        $this->fakeSquadApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertOk()
            ->assertSee('Javier Aguirre')
            ->assertSee('teams.coach_name');
    }

    public function test_dry_run_does_not_write_player_records(): void
    {
        $this->makeLocalFdTeam('Mexico', 'MEX', 769);
        $this->fakeSquadApi();
        $playerCount = Player::query()->count();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertOk();

        $this->assertSame($playerCount, Player::query()->count());
    }

    public function test_dry_run_does_not_update_team_meta(): void
    {
        $team = $this->makeLocalFdTeam('Mexico', 'MEX', 769);
        $originalMeta = $team->meta;
        $this->fakeSquadApi();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertOk();

        $this->assertSame($originalMeta, $team->fresh()->meta);
    }

    public function test_api_403_or_429_returns_safe_error_not_500(): void
    {
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/teams' => Http::response(['message' => 'Rate limit exceeded'], 429),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.preview-squads'))
            ->assertOk()
            ->assertSee('API request failed with status 429: Rate limit exceeded')
            ->assertDontSee('test-token-not-secret');
    }

    public function test_no_hash_links_introduced(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.football-data-import.index'))
            ->assertOk()
            ->assertSee('Preview squads')
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

    protected function fakeSquadApi(): void
    {
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/teams' => Http::response($this->worldCupTeamsPayload(), 200),
            'https://api.football-data.org/v4/teams/769' => Http::response($this->mexicoTeamPayload(), 200),
            'https://api.football-data.org/v4/teams/764' => Http::response($this->brazilTeamPayload(), 200),
        ]);
    }

    protected function makeLocalFdTeam(string $name, string $code, ?int $sourceId, array $meta = []): Team
    {
        $group = $this->makeGroup('FD'.(Team::query()->count() + 1));
        $team = $this->makeTeam($group, $name, ['code' => $code]);
        $team->update([
            'meta' => array_replace_recursive([
                'source' => 'football-data.org',
                'source_competition' => 'WC',
                'football_data' => [
                    'id' => $sourceId,
                    'tla' => $code,
                    'crest' => 'https://crests.example/'.Strtolower($code).'.svg',
                ],
            ], $meta),
        ]);

        return $team->fresh();
    }

    protected function worldCupTeamsPayload(): array
    {
        return [
            'competition' => ['id' => 2000, 'name' => 'FIFA World Cup', 'code' => 'WC'],
            'teams' => [
                ['id' => 769, 'name' => 'Mexico', 'shortName' => 'Mexico', 'tla' => 'MEX', 'crest' => 'https://crests.example/mex.svg'],
                ['id' => 764, 'name' => 'Brazil', 'shortName' => 'Brazil', 'tla' => 'BRA', 'crest' => 'https://crests.example/bra.svg'],
                ['id' => 759, 'name' => 'Germany', 'shortName' => 'Germany', 'tla' => 'GER', 'crest' => 'https://crests.example/ger.svg'],
            ],
        ];
    }

    protected function mexicoTeamPayload(): array
    {
        return [
            'id' => 769,
            'name' => 'Mexico',
            'tla' => 'MEX',
            'squad' => [
                [
                    'id' => 1001,
                    'name' => 'Guillermo Ochoa',
                    'position' => 'Goalkeeper',
                    'dateOfBirth' => '1985-07-13',
                    'nationality' => 'Mexico',
                    'shirtNumber' => 13,
                ],
                [
                    'id' => 1002,
                    'name' => 'Edson Alvarez',
                    'position' => 'Midfield',
                    'dateOfBirth' => '1997-10-24',
                    'nationality' => 'Mexico',
                    'shirtNumber' => 4,
                ],
            ],
            'coach' => [
                'id' => 2001,
                'firstName' => 'Javier',
                'lastName' => 'Aguirre',
                'name' => 'Javier Aguirre',
                'dateOfBirth' => '1958-12-01',
                'nationality' => 'Mexico',
            ],
        ];
    }

    protected function brazilTeamPayload(): array
    {
        return [
            'id' => 764,
            'name' => 'Brazil',
            'tla' => 'BRA',
            'squad' => [
                [
                    'id' => 1101,
                    'name' => 'Alisson Becker',
                    'position' => 'Goalkeeper',
                    'dateOfBirth' => '1992-10-02',
                    'nationality' => 'Brazil',
                ],
            ],
            'coach' => null,
        ];
    }
}
