<?php

namespace Tests\Unit;

use App\Models\Player;
use App\Models\Team;
use App\Services\ExternalFootball\FootballDataSquadPayloadCollector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class FootballDataSquadPayloadCollectorTest extends TestCase
{
    use BuildsAdminTestData;
    use RefreshDatabase;

    private ?string $cacheBackupPath = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->backupSquadCache();
        File::deleteDirectory(storage_path('app/football-data'));
        $this->configureFootballData();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path('app/football-data'));
        $this->restoreSquadCache();

        parent::tearDown();
    }

    public function test_collector_parses_retry_after_header_and_retries_429(): void
    {
        $this->makeReconciledTeams();
        $waits = [];
        $calls = [];

        Http::fake(function ($request) use (&$calls) {
            $url = $request->url();
            $calls[$url] = ($calls[$url] ?? 0) + 1;

            if ($url === 'https://api.football-data.org/v4/competitions/WC/teams') {
                return Http::response(['teams' => $this->sourceTeams()], 200);
            }

            if ($url === 'https://api.football-data.org/v4/teams/1001' && $calls[$url] === 1) {
                return Http::response(['message' => 'Rate limit exceeded'], 429, ['Retry-After' => '7']);
            }

            $sourceTeam = collect($this->sourceTeams())->firstWhere('id', (int) Str::afterLast($url, '/'));

            return Http::response($this->teamDetailPayload($sourceTeam), 200);
        });

        $collector = app(FootballDataSquadPayloadCollector::class)->setSleeper(function (int $seconds) use (&$waits): void {
            $waits[] = $seconds;
        });

        $result = $collector->collect();

        $this->assertTrue($result['ok']);
        $this->assertSame([12], $waits);
        $this->assertSame(2, $calls['https://api.football-data.org/v4/teams/1001']);
        $this->assertFileExists($collector->cachePath());
    }

    public function test_collector_parses_wait_seconds_message_and_retries_429(): void
    {
        $this->makeReconciledTeams();
        $waits = [];
        $calls = [];

        Http::fake(function ($request) use (&$calls) {
            $url = $request->url();
            $calls[$url] = ($calls[$url] ?? 0) + 1;

            if ($url === 'https://api.football-data.org/v4/competitions/WC/teams') {
                return Http::response(['teams' => $this->sourceTeams()], 200);
            }

            if ($url === 'https://api.football-data.org/v4/teams/1002' && $calls[$url] === 1) {
                return Http::response(['message' => 'You reached your request limit. Wait 50 seconds.'], 429);
            }

            $sourceTeam = collect($this->sourceTeams())->firstWhere('id', (int) Str::afterLast($url, '/'));

            return Http::response($this->teamDetailPayload($sourceTeam), 200);
        });

        $collector = app(FootballDataSquadPayloadCollector::class)->setSleeper(function (int $seconds) use (&$waits): void {
            $waits[] = $seconds;
        });

        $result = $collector->collect();

        $this->assertTrue($result['ok']);
        $this->assertSame([55], $waits);
        $this->assertSame(2, $calls['https://api.football-data.org/v4/teams/1002']);
    }

    public function test_collector_does_not_write_players_or_coaches_and_cache_excludes_token(): void
    {
        $this->makeReconciledTeams();
        Http::fake($this->squadResponses());

        $collector = app(FootballDataSquadPayloadCollector::class)->setSleeper(fn () => null);
        $result = $collector->collect();

        $this->assertTrue($result['ok']);
        $this->assertSame(0, Player::query()->count());
        $this->assertSame(0, Team::query()->whereNotNull('coach_name')->where('coach_name', '<>', '')->count());

        $cache = File::get($collector->cachePath());

        $this->assertStringNotContainsString('test-token-not-secret', $cache);
        $this->assertSame(48, json_decode($cache, true)['payload_count']);
    }

    public function test_collector_does_not_write_partial_cache_when_collection_fails(): void
    {
        $this->makeReconciledTeams();

        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/teams' => Http::response(['teams' => $this->sourceTeams()], 200),
            'https://api.football-data.org/v4/teams/1001' => Http::response($this->teamDetailPayload($this->sourceTeams()[0]), 200),
            'https://api.football-data.org/v4/teams/1002' => Http::response(['message' => 'Upstream error'], 500),
            '*' => Http::response(['message' => 'Unexpected'], 500),
        ]);

        $collector = app(FootballDataSquadPayloadCollector::class)->setSleeper(fn () => null);
        $result = $collector->collect();

        $this->assertFalse($result['ok']);
        $this->assertFileDoesNotExist($collector->cachePath());
        $this->assertSame(0, Player::query()->count());
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

    protected function makeReconciledTeams(): void
    {
        $group = $this->makeGroup('FDC');

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

    protected function squadResponses(): array
    {
        $responses = [
            'https://api.football-data.org/v4/competitions/WC/teams' => Http::response(['teams' => $this->sourceTeams()], 200),
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
                    'position' => 'Goalkeeper',
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
