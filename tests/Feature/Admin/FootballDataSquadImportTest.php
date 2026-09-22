<?php

namespace Tests\Feature\Admin;

use App\Models\City;
use App\Models\MatchFixture;
use App\Models\Player;
use App\Models\Stadium;
use App\Models\Standing;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class FootballDataSquadImportTest extends TestCase
{
    use BuildsAdminTestData;
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    private ?string $cacheBackupPath = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->backupSquadCache();
        File::deleteDirectory(storage_path('app/football-data'));
        $this->seedDemoAccessFoundation();
        $this->configureFootballData();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path('app/football-data'));
        $this->restoreSquadCache();

        parent::tearDown();
    }

    public function test_super_admin_can_see_guarded_import_section(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.football-data-import.index'))
            ->assertOk()
            ->assertSee('Import Players &amp; Coaches', false)
            ->assertSee('IMPORT FOOTBALL SQUADS')
            ->assertSee('I understand this writes players and coach data to the local demo database')
            ->assertDontSee('test-token-not-secret');
    }

    public function test_journalist_cannot_run_squad_import(): void
    {
        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertForbidden();
    }

    public function test_exact_confirmation_required(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.football-data-import.index'))
            ->post(route('admin.football-data-import.import-squads'), [
                'squad_import_confirmation' => 'IMPORT SQUADS',
                'understands_squad_import' => '1',
            ])
            ->assertRedirect(route('admin.football-data-import.index'))
            ->assertSessionHasErrors('squad_import_confirmation');
    }

    public function test_checkbox_required(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.football-data-import.index'))
            ->post(route('admin.football-data-import.import-squads'), [
                'squad_import_confirmation' => 'IMPORT FOOTBALL SQUADS',
            ])
            ->assertRedirect(route('admin.football-data-import.index'))
            ->assertSessionHasErrors('understands_squad_import');
    }

    public function test_token_never_displayed(): void
    {
        $this->makeReconciledTeams();
        Http::fake($this->squadImportResponses());

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk()
            ->assertDontSee('test-token-not-secret');
    }

    public function test_import_preflight_fetches_wc_teams_with_auth_token(): void
    {
        $this->makeReconciledTeams();
        Http::fake($this->squadImportResponses());

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk();

        Http::assertSent(fn ($request) => $request->url() === 'https://api.football-data.org/v4/competitions/WC/teams'
            && $request->hasHeader('X-Auth-Token'));
    }

    public function test_import_fetches_team_details_for_matched_local_teams(): void
    {
        $this->makeReconciledTeams();
        Http::fake($this->squadImportResponses());

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk();

        Http::assertSent(fn ($request) => $request->url() === 'https://api.football-data.org/v4/teams/1001');
        Http::assertSent(fn ($request) => $request->url() === 'https://api.football-data.org/v4/teams/1048');
    }

    public function test_import_skips_tbd_placeholders(): void
    {
        $this->makeReconciledTeams();
        $placeholder = $this->makePlaceholderTeam();
        Http::fake($this->squadImportResponses());

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk()
            ->assertSee('Skipped Placeholders')
            ->assertSee('1');

        $this->assertSame(0, $placeholder->fresh()->players()->count());
    }

    public function test_import_creates_players_from_squad_payload_and_stores_provenance(): void
    {
        $this->makeReconciledTeams();
        Http::fake($this->squadImportResponses());

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk()
            ->assertSee('Players Created');

        $player = Player::query()->where('external_id', '501001')->firstOrFail();

        $this->assertSame('football-data.org', $player->external_provider);
        $this->assertSame('Source Player 01', $player->display_name);
        $this->assertSame('goalkeeper', $player->position);
        $this->assertSame('Source Team 01', data_get($player->external_payload, 'team.name'));
    }

    public function test_import_updates_coach_name_and_team_coach_provenance(): void
    {
        $this->makeReconciledTeams();
        Http::fake($this->squadImportResponses());

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk()
            ->assertSee('Coaches Updated');

        $team = Team::query()->where('meta->football_data->id', 1001)->firstOrFail();

        $this->assertSame('Coach 01', $team->coach_name);
        $this->assertSame('6001', data_get($team->meta, 'football_data.coach.id'));
        $this->assertSame('Coach 01', data_get($team->meta, 'football_data.coach.name'));
    }

    public function test_api_429_before_full_payload_causes_safe_blocked_result_and_no_db_writes(): void
    {
        $this->makeReconciledTeams();
        Http::fake($this->squadImportResponses(failAtTeamId: 1002));

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk()
            ->assertSee('detail request failed with status 429')
            ->assertSee('Import blocked')
            ->assertDontSee('test-token-not-secret');

        $this->assertSame(0, Player::query()->count());
        $this->assertSame(0, Team::query()->whereNotNull('coach_name')->where('coach_name', '<>', '')->count());
    }

    public function test_simulated_db_failure_rolls_back_players_and_coaches(): void
    {
        $this->makeReconciledTeams();
        Http::fake($this->squadImportResponses());

        Player::creating(function () {
            throw new RuntimeException('Simulated player write failure.');
        });

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk()
            ->assertSee('failed and was rolled back');

        Player::flushEventListeners();

        $this->assertSame(0, Player::query()->count());
        $this->assertSame(0, Team::query()->whereNotNull('coach_name')->where('coach_name', '<>', '')->count());
    }

    public function test_no_match_standing_city_or_stadium_changes(): void
    {
        $this->makeReconciledTeams();
        $city = $this->makeCity('Rabat', ['code' => 'RBA']);
        $stadium = $this->makeStadium($city, 'Test Stadium', ['code' => 'TST']);
        $teams = Team::query()->where('meta->source', 'football-data.org')->take(2)->get();
        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $teams[0]->group_id,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
            'code' => 'FD-WC-TEST',
            'slug' => 'fd-wc-test',
            'stage_type' => 'group',
            'match_date' => now()->addDay(),
            'status' => 'scheduled',
            'published_at' => now(),
        ]);
        Http::fake($this->squadImportResponses());

        $before = [
            'matches' => MatchFixture::query()->count(),
            'standings' => Standing::query()->count(),
            'cities' => City::query()->count(),
            'stadiums' => Stadium::query()->count(),
        ];

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk();

        $this->assertSame($before['matches'], MatchFixture::query()->count());
        $this->assertSame($before['standings'], Standing::query()->count());
        $this->assertSame($before['cities'], City::query()->count());
        $this->assertSame($before['stadiums'], Stadium::query()->count());
    }

    public function test_team_detail_page_shows_imported_coach_and_players(): void
    {
        $this->makeReconciledTeams();
        Http::fake($this->squadImportResponses());

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk();

        $team = Team::query()->where('meta->football_data->id', 1001)->firstOrFail();

        $this->get(route('teams.show', $team->slug))
            ->assertOk()
            ->assertSee('Coach 01')
            ->assertSee('Source Player 01')
            ->assertSee('Goalkeeper');
    }

    public function test_import_from_cache_refuses_incomplete_cache(): void
    {
        $this->makeReconciledTeams();
        $this->writeSquadCache(payloadCount: 1);

        $exitCode = Artisan::call('football-data:import-squads-from-cache');

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('payload count is incomplete', Artisan::output());
        $this->assertSame(0, Player::query()->count());
        $this->assertSame(0, Team::query()->whereNotNull('coach_name')->where('coach_name', '<>', '')->count());
    }

    public function test_import_from_cache_creates_players_and_coaches_without_changing_sports_data(): void
    {
        $this->makeReconciledTeams();
        $city = $this->makeCity('Rabat', ['code' => 'RBA']);
        $stadium = $this->makeStadium($city, 'Test Stadium', ['code' => 'TST']);
        $teams = Team::query()->where('meta->source', 'football-data.org')->take(2)->get();
        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $teams[0]->group_id,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
            'code' => 'FD-WC-CACHE',
            'slug' => 'fd-wc-cache',
            'stage_type' => 'group',
            'match_date' => now()->addDay(),
            'status' => 'scheduled',
            'published_at' => now(),
        ]);
        $this->writeSquadCache();

        $before = [
            'matches' => MatchFixture::query()->count(),
            'standings' => Standing::query()->count(),
            'cities' => City::query()->count(),
            'stadiums' => Stadium::query()->count(),
        ];

        $exitCode = Artisan::call('football-data:import-squads-from-cache');

        $this->assertSame(0, $exitCode);
        $this->assertStringNotContainsString('test-token-not-secret', Artisan::output());
        $this->assertSame(48, Player::query()->count());
        $this->assertSame(48, Team::query()->whereNotNull('coach_name')->where('coach_name', '<>', '')->count());
        $this->assertSame($before['matches'], MatchFixture::query()->count());
        $this->assertSame($before['standings'], Standing::query()->count());
        $this->assertSame($before['cities'], City::query()->count());
        $this->assertSame($before['stadiums'], Stadium::query()->count());
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

    protected function backupSquadCache(): void
    {
        $cachePath = storage_path('app/football-data/wc_squads_payloads.json');

        if (! File::exists($cachePath)) {
            return;
        }

        $this->cacheBackupPath = storage_path('framework/testing/wc_squads_payloads_'.Str::uuid().'.json');
        File::ensureDirectoryExists(dirname($this->cacheBackupPath));
        File::move($cachePath, $this->cacheBackupPath);
    }

    protected function restoreSquadCache(): void
    {
        if (! $this->cacheBackupPath || ! File::exists($this->cacheBackupPath)) {
            return;
        }

        $cachePath = storage_path('app/football-data/wc_squads_payloads.json');
        File::ensureDirectoryExists(dirname($cachePath));
        File::move($this->cacheBackupPath, $cachePath);
    }

    protected function validRequest(): array
    {
        return [
            'squad_import_confirmation' => 'IMPORT FOOTBALL SQUADS',
            'understands_squad_import' => '1',
        ];
    }

    protected function makeReconciledTeams(): void
    {
        $group = $this->makeGroup('FDI');

        foreach ($this->sourceTeams() as $sourceTeam) {
            $team = $this->makeTeam($group, $sourceTeam['name'], ['code' => $sourceTeam['tla']]);
            $team->update([
                'meta' => [
                    'source' => 'football-data.org',
                    'source_competition' => 'WC',
                    'placeholder' => false,
                    'football_data' => [
                        'id' => $sourceTeam['id'],
                        'name' => $sourceTeam['name'],
                        'tla' => $sourceTeam['tla'],
                        'shortName' => $sourceTeam['shortName'],
                        'crest' => $sourceTeam['crest'],
                    ],
                ],
            ]);
        }
    }

    protected function makePlaceholderTeam(): Team
    {
        $team = $this->makeTeam($this->makeGroup('TB1'), 'TBD Home FD-WC-999999', ['code' => 'FDH999999']);
        $team->update([
            'meta' => [
                'source' => 'football-data.org',
                'source_competition' => 'WC',
                'placeholder' => true,
            ],
        ]);

        return $team->fresh();
    }

    protected function squadImportResponses(?int $failAtTeamId = null): array
    {
        $responses = [
            'https://api.football-data.org/v4/competitions/WC/teams' => Http::response([
                'competition' => ['id' => 2000, 'name' => 'FIFA World Cup', 'code' => 'WC'],
                'teams' => $this->sourceTeams(),
            ], 200),
        ];

        foreach ($this->sourceTeams() as $sourceTeam) {
            $responses['https://api.football-data.org/v4/teams/'.$sourceTeam['id']] = $failAtTeamId === $sourceTeam['id']
                ? Http::response(['message' => 'Rate limit exceeded'], 429)
                : Http::response($this->teamDetailPayload($sourceTeam), 200);
        }

        return $responses;
    }

    protected function teamDetailPayload(array $sourceTeam): array
    {
        $number = (int) Str::after($sourceTeam['tla'], 'T');

        return [
            'id' => $sourceTeam['id'],
            'name' => $sourceTeam['name'],
            'tla' => $sourceTeam['tla'],
            'squad' => [
                [
                    'id' => 500000 + $sourceTeam['id'],
                    'name' => 'Source Player '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                    'position' => $number % 4 === 0 ? null : 'Goalkeeper',
                    'dateOfBirth' => '1990-01-01',
                    'nationality' => $sourceTeam['name'],
                    'shirtNumber' => $number,
                ],
            ],
            'coach' => [
                'id' => 6000 + $number,
                'name' => 'Coach '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'dateOfBirth' => '1970-01-01',
                'nationality' => $sourceTeam['name'],
            ],
        ];
    }

    protected function writeSquadCache(int $payloadCount = 48): void
    {
        $teams = collect($this->sourceTeams())
            ->take($payloadCount)
            ->map(fn (array $sourceTeam) => [
                'source_team' => $sourceTeam,
                'payload' => $this->teamDetailPayload($sourceTeam),
            ])
            ->values()
            ->all();

        File::ensureDirectoryExists(storage_path('app/football-data'));
        File::put(storage_path('app/football-data/wc_squads_payloads.json'), json_encode([
            'provider' => 'football-data.org',
            'generated_at' => now()->toIso8601String(),
            'source_team_count' => $payloadCount,
            'matched_local_teams' => $payloadCount,
            'skipped_placeholders' => 0,
            'payload_count' => $payloadCount,
            'teams' => $teams,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    protected function sourceTeams(): array
    {
        return collect(range(1, 48))
            ->map(fn (int $number) => [
                'id' => 1000 + $number,
                'name' => 'Source Team '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'shortName' => 'Team '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'tla' => 'T'.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'crest' => 'https://crests.example/t'.str_pad((string) $number, 2, '0', STR_PAD_LEFT).'.svg',
            ])
            ->all();
    }
}
