<?php

namespace Tests\Feature\Admin;

use App\Models\MatchFixture;
use App\Models\Stadium;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class FootballDataReconciliationTest extends TestCase
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

    public function test_super_admin_can_access_reconciliation_action(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.football-data-import.index'))
            ->assertOk()
            ->assertSee('Reconcile flags/results')
            ->assertSee('RECONCILE FOOTBALL DATA');
    }

    public function test_journalist_cannot_reconcile(): void
    {
        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile'), $this->validRequest())
            ->assertForbidden();
    }

    public function test_reconcile_requires_exact_confirmation(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.football-data-import.index'))
            ->post(route('admin.football-data-import.reconcile'), [
                'reconciliation_confirmation' => 'RECONCILE',
                'understands_reconciliation' => '1',
            ])
            ->assertRedirect(route('admin.football-data-import.index'))
            ->assertSessionHasErrors('reconciliation_confirmation');
    }

    public function test_reconcile_requires_checkbox(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.football-data-import.index'))
            ->post(route('admin.football-data-import.reconcile'), [
                'reconciliation_confirmation' => 'RECONCILE FOOTBALL DATA',
            ])
            ->assertRedirect(route('admin.football-data-import.index'))
            ->assertSessionHasErrors('understands_reconciliation');
    }

    public function test_token_is_never_displayed(): void
    {
        $payload = $this->seedImportedWorldCup();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($payload, 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile'), $this->validRequest())
            ->assertOk()
            ->assertDontSee('test-token-not-secret');
    }

    public function test_real_team_crest_from_api_is_stored_while_local_flag_is_displayed_safely(): void
    {
        $payload = $this->seedImportedWorldCup();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($payload, 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile'), $this->validRequest())
            ->assertOk()
            ->assertSee('Teams Updated With Crests');

        $team = Team::query()->where('code', 'MEX')->firstOrFail();
        $this->assertSame('https://crests.example/mex.svg', data_get($team->meta, 'football_data.crest'));

        $this->get(route('teams.index'))
            ->assertOk()
            ->assertSee('/assets/images/flag/flags/4x3/mx.svg', false)
            ->assertDontSee('https://crests.example/mex.svg', false);
    }

    public function test_tbd_placeholder_does_not_receive_fake_crest(): void
    {
        $payload = $this->seedImportedWorldCup();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($payload, 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile'), $this->validRequest())
            ->assertOk();

        $placeholder = Team::query()->where('code', 'FDH537417')->firstOrFail();
        $this->assertTrue((bool) data_get($placeholder->meta, 'placeholder'));
        $this->assertNull(data_get($placeholder->meta, 'football_data.crest'));

        $this->get(route('matches.index'))
            ->assertOk()
            ->assertSee('TBD')
            ->assertDontSee('FDH537417.svg');
    }

    public function test_finished_match_writes_full_time_source_score(): void
    {
        $payload = $this->seedImportedWorldCup();
        MatchFixture::query()->where('code', 'FD-WC-537327')->update(['home_score' => null, 'away_score' => null]);
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($payload, 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile'), $this->validRequest())
            ->assertOk();

        $this->assertDatabaseHas('matches', [
            'code' => 'FD-WC-537327',
            'home_score' => 2,
            'away_score' => 0,
            'status' => 'completed',
        ]);
    }

    public function test_scheduled_match_clears_conflicting_imported_score(): void
    {
        $payload = $this->seedImportedWorldCup();
        MatchFixture::query()->where('code', 'FD-WC-537328')->update(['home_score' => 9, 'away_score' => 8]);
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($payload, 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile'), $this->validRequest())
            ->assertOk();

        $match = MatchFixture::query()->where('code', 'FD-WC-537328')->firstOrFail();
        $this->assertSame('scheduled', $match->status);
        $this->assertNull($match->home_score);
        $this->assertNull($match->away_score);
    }

    public function test_moroccan_venue_is_preserved_and_no_foreign_stadium_or_city_is_created(): void
    {
        $payload = $this->seedImportedWorldCup();
        $match = MatchFixture::query()->where('code', 'FD-WC-537327')->firstOrFail();
        $stadiumId = $match->stadium_id;
        $cityId = $match->city_id;
        $stadiumCount = Stadium::query()->count();
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($payload, 200),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile'), $this->validRequest())
            ->assertOk();

        $match->refresh();
        $this->assertSame($stadiumId, $match->stadium_id);
        $this->assertSame($cityId, $match->city_id);
        $this->assertSame($stadiumCount, Stadium::query()->count());
    }

    public function test_api_failure_returns_safe_error_not_500(): void
    {
        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response(['message' => 'Rate limit exceeded'], 429),
        ]);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.reconcile'), $this->validRequest())
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

    protected function validRequest(): array
    {
        return [
            'reconciliation_confirmation' => 'RECONCILE FOOTBALL DATA',
            'understands_reconciliation' => '1',
        ];
    }

    protected function seedImportedWorldCup(): array
    {
        $city = $this->makeCity('Casablanca', ['code' => 'CAS']);
        $stadium = $this->makeStadium($city, 'Mohammed V Stadium', ['code' => 'MO5']);
        $group = $this->makeGroup('A');
        $mexico = $this->makeTeam($group, 'Mexico', ['code' => 'MEX']);
        $southAfrica = $this->makeTeam($group, 'South Africa', ['code' => 'RSA']);
        $brazil = $this->makeTeam($group, 'Brazil', ['code' => 'BRA']);
        $morocco = $this->makeTeam($group, 'Morocco', ['code' => 'MAR']);
        $homeTbd = $this->makeTeam($group, 'TBD Home FD-WC-537417', ['code' => 'FDH537417']);
        $awayTbd = $this->makeTeam($group, 'TBD Away FD-WC-537417', ['code' => 'FDA537417']);
        $homeTbd->update(['meta' => ['source' => 'football-data.org', 'placeholder' => true]]);
        $awayTbd->update(['meta' => ['source' => 'football-data.org', 'placeholder' => true]]);

        $payloadMatches = [
            [
                'id' => 537327,
                'utcDate' => '2026-06-11T19:00:00Z',
                'status' => 'FINISHED',
                'stage' => 'GROUP_STAGE',
                'group' => 'Group A',
                'matchday' => 1,
                'homeTeam' => ['id' => 769, 'name' => 'Mexico', 'tla' => 'MEX', 'crest' => 'https://crests.example/mex.svg'],
                'awayTeam' => ['id' => 766, 'name' => 'South Africa', 'tla' => 'RSA', 'crest' => 'https://crests.example/rsa.svg'],
                'score' => ['fullTime' => ['home' => 2, 'away' => 0]],
            ],
            [
                'id' => 537328,
                'utcDate' => '2026-06-12T19:00:00Z',
                'status' => 'TIMED',
                'stage' => 'GROUP_STAGE',
                'group' => 'Group A',
                'matchday' => 1,
                'homeTeam' => ['id' => 764, 'name' => 'Brazil', 'tla' => 'BRA', 'crest' => 'https://crests.example/bra.svg'],
                'awayTeam' => ['id' => 815, 'name' => 'Morocco', 'tla' => 'MAR', 'crest' => 'https://crests.example/mar.svg'],
                'score' => ['fullTime' => ['home' => null, 'away' => null]],
            ],
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
        ];

        $teamPairs = [
            [$mexico, $southAfrica],
            [$brazil, $morocco],
            [$homeTbd, $awayTbd],
        ];

        for ($i = 3; $i < 104; $i++) {
            $id = 600000 + $i;
            $payloadMatches[] = [
                'id' => $id,
                'utcDate' => now()->addDays($i)->utc()->format('Y-m-d\TH:i:s\Z'),
                'status' => 'TIMED',
                'stage' => 'GROUP_STAGE',
                'group' => 'Group A',
                'matchday' => 1,
                'homeTeam' => ['id' => 769, 'name' => 'Mexico', 'tla' => 'MEX', 'crest' => 'https://crests.example/mex.svg'],
                'awayTeam' => ['id' => 766, 'name' => 'South Africa', 'tla' => 'RSA', 'crest' => 'https://crests.example/rsa.svg'],
                'score' => ['fullTime' => ['home' => null, 'away' => null]],
            ];
            $teamPairs[] = [$mexico, $southAfrica];
        }

        foreach ($payloadMatches as $index => $sourceMatch) {
            [$home, $away] = $teamPairs[$index];
            MatchFixture::query()->create([
                'stadium_id' => $stadium->id,
                'city_id' => $city->id,
                'group_id' => $group->id,
                'home_team_id' => $home->id,
                'away_team_id' => $away->id,
                'code' => 'FD-WC-'.$sourceMatch['id'],
                'slug' => Str::slug('FD-WC-'.$sourceMatch['id']),
                'stage_type' => $sourceMatch['stage'] === 'LAST_16' ? 'round_of_16' : 'group',
                'round_number' => $sourceMatch['matchday'] ?? null,
                'match_date' => $sourceMatch['utcDate'],
                'timezone' => 'UTC',
                'status' => 'scheduled',
                'home_score' => null,
                'away_score' => null,
                'published_at' => now(),
                'meta' => ['source' => 'football-data.org', 'source_match_id' => $sourceMatch['id']],
            ]);
        }

        return [
            'competition' => ['id' => 2000, 'name' => 'FIFA World Cup', 'code' => 'WC'],
            'matches' => $payloadMatches,
        ];
    }
}
