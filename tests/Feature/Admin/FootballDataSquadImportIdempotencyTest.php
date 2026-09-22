<?php

namespace Tests\Feature\Admin;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\Concerns\BuildsAdminTestData;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class FootballDataSquadImportIdempotencyTest extends TestCase
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

    public function test_running_import_twice_does_not_duplicate_players(): void
    {
        $this->makeReconciledTeams();
        Http::fake($this->squadImportResponses());

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk();

        $firstCount = Player::query()->count();

        Http::fake($this->squadImportResponses());

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk()
            ->assertSee('Players Matched Existing');

        $this->assertSame($firstCount, Player::query()->count());
        $this->assertSame(48, $firstCount);
    }

    public function test_running_import_twice_does_not_duplicate_coaches_or_provenance(): void
    {
        $this->makeReconciledTeams();
        Http::fake($this->squadImportResponses());

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk();

        $firstCoachNames = Team::query()->whereNotNull('coach_name')->where('coach_name', '<>', '')->count();
        $firstCoachMeta = Team::query()
            ->where('meta->source', 'football-data.org')
            ->whereNotNull('meta->football_data->coach->id')
            ->count();

        Http::fake($this->squadImportResponses());

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.football-data-import.import-squads'), $this->validRequest())
            ->assertOk();

        $this->assertSame($firstCoachNames, Team::query()->whereNotNull('coach_name')->where('coach_name', '<>', '')->count());
        $this->assertSame($firstCoachMeta, Team::query()
            ->where('meta->source', 'football-data.org')
            ->whereNotNull('meta->football_data->coach->id')
            ->count());
        $this->assertSame(48, $firstCoachNames);
        $this->assertSame(48, $firstCoachMeta);
    }

    public function test_running_import_from_cache_twice_does_not_duplicate_players(): void
    {
        $this->makeReconciledTeams();
        $this->writeSquadCache();

        $firstExitCode = Artisan::call('football-data:import-squads-from-cache');
        $this->assertSame(0, $firstExitCode);

        $firstCount = Player::query()->count();
        $firstCoachNames = Team::query()->whereNotNull('coach_name')->where('coach_name', '<>', '')->count();

        $secondExitCode = Artisan::call('football-data:import-squads-from-cache');
        $this->assertSame(0, $secondExitCode);

        $this->assertSame(48, $firstCount);
        $this->assertSame($firstCount, Player::query()->count());
        $this->assertSame($firstCoachNames, Team::query()->whereNotNull('coach_name')->where('coach_name', '<>', '')->count());
        $this->assertStringNotContainsString('test-token-not-secret', Artisan::output());
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

    protected function squadImportResponses(): array
    {
        $responses = [
            'https://api.football-data.org/v4/competitions/WC/teams' => Http::response([
                'competition' => ['id' => 2000, 'name' => 'FIFA World Cup', 'code' => 'WC'],
                'teams' => $this->sourceTeams(),
            ], 200),
        ];

        foreach ($this->sourceTeams() as $sourceTeam) {
            $responses['https://api.football-data.org/v4/teams/'.$sourceTeam['id']] = Http::response($this->teamDetailPayload($sourceTeam), 200);
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
                    'position' => 'Midfield',
                    'dateOfBirth' => '1990-01-01',
                    'nationality' => $sourceTeam['name'],
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

    protected function writeSquadCache(): void
    {
        File::ensureDirectoryExists(storage_path('app/football-data'));
        File::put(storage_path('app/football-data/wc_squads_payloads.json'), json_encode([
            'provider' => 'football-data.org',
            'generated_at' => now()->toIso8601String(),
            'source_team_count' => 48,
            'matched_local_teams' => 48,
            'skipped_placeholders' => 0,
            'payload_count' => 48,
            'teams' => collect($this->sourceTeams())
                ->map(fn (array $sourceTeam) => [
                    'source_team' => $sourceTeam,
                    'payload' => $this->teamDetailPayload($sourceTeam),
                ])
                ->values()
                ->all(),
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
