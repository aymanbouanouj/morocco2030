<?php

namespace Tests\Feature\Admin;

use App\Models\MatchFixture;
use App\Models\Stadium;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class FootballDataMatchRefreshTest extends TestCase
{
    use BuildsAdminTestData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.external_football.provider' => 'football-data',
            'services.external_football.base_url' => 'https://api.football-data.org/v4',
            'services.external_football.token' => 'test-token-not-secret',
            'services.external_football.timeout' => 15,
        ]);
    }

    public function test_match_reconcile_command_updates_source_state_without_corrupting_dataset(): void
    {
        $payload = $this->seedImportedWorldCupPayload();
        $venue = MatchFixture::query()->where('code', 'FD-WC-537327')->firstOrFail()->only(['stadium_id', 'city_id']);
        $stadiumCount = Stadium::query()->count();

        MatchFixture::query()->where('code', 'FD-WC-537327')->update([
            'status' => 'scheduled',
            'home_score' => null,
            'away_score' => null,
        ]);
        MatchFixture::query()->where('code', 'FD-WC-537329')->update([
            'home_score' => 9,
            'away_score' => 8,
        ]);

        Http::fake([
            'https://api.football-data.org/v4/competitions/WC/matches' => Http::response($payload, 200),
        ]);

        $this->artisan('football-data:reconcile-matches')
            ->expectsOutputToContain('source matches total: 104')
            ->expectsOutputToContain('Match reconciliation completed.')
            ->assertSuccessful();

        $this->assertSame(104, MatchFixture::query()->where('code', 'like', 'FD-WC-%')->count());
        $this->assertDatabaseHas('matches', [
            'code' => 'FD-WC-537327',
            'status' => 'completed',
            'home_score' => 2,
            'away_score' => 0,
            'stadium_id' => $venue['stadium_id'],
            'city_id' => $venue['city_id'],
        ]);
        $this->assertDatabaseHas('matches', [
            'code' => 'FD-WC-537328',
            'status' => 'live',
        ]);
        $this->assertDatabaseHas('matches', [
            'code' => 'FD-WC-537330',
            'status' => 'completed',
            'home_score' => 0,
            'away_score' => 0,
        ]);

        $scheduled = MatchFixture::query()->where('code', 'FD-WC-537329')->firstOrFail();
        $this->assertSame('scheduled', $scheduled->status);
        $this->assertNull($scheduled->home_score);
        $this->assertNull($scheduled->away_score);
        $this->assertSame($stadiumCount, Stadium::query()->count());
    }

    private function seedImportedWorldCupPayload(): array
    {
        $city = $this->makeCity('Casablanca', ['code' => 'CAS']);
        $stadium = $this->makeStadium($city, 'Mohammed V Stadium', ['code' => 'MO5']);
        $group = $this->makeGroup('A');
        $mexico = $this->makeTeam($group, 'Mexico', ['code' => 'MEX']);
        $southAfrica = $this->makeTeam($group, 'South Africa', ['code' => 'RSA']);
        $brazil = $this->makeTeam($group, 'Brazil', ['code' => 'BRA']);
        $morocco = $this->makeTeam($group, 'Morocco', ['code' => 'MAR']);

        $sourceMatches = [
            $this->sourceMatch(537327, 'FINISHED', 'Mexico', 'South Africa', 2, 0),
            $this->sourceMatch(537328, 'LIVE', 'Brazil', 'Morocco'),
            $this->sourceMatch(537329, 'TIMED', 'Mexico', 'Morocco'),
            $this->sourceMatch(537330, 'FINISHED', 'Brazil', 'South Africa', 0, 0),
        ];
        $teamPairs = [
            [$mexico, $southAfrica],
            [$brazil, $morocco],
            [$mexico, $morocco],
            [$brazil, $southAfrica],
        ];

        for ($i = 4; $i < 104; $i++) {
            $sourceMatches[] = $this->sourceMatch(600000 + $i, 'TIMED', 'Mexico', 'South Africa');
            $teamPairs[] = [$mexico, $southAfrica];
        }

        foreach ($sourceMatches as $index => $sourceMatch) {
            [$home, $away] = $teamPairs[$index];

            MatchFixture::query()->create([
                'stadium_id' => $stadium->id,
                'city_id' => $city->id,
                'group_id' => $group->id,
                'home_team_id' => $home->id,
                'away_team_id' => $away->id,
                'code' => 'FD-WC-'.$sourceMatch['id'],
                'slug' => Str::slug('FD-WC-'.$sourceMatch['id']),
                'stage_type' => 'group',
                'round_number' => 1,
                'match_date' => $sourceMatch['utcDate'],
                'timezone' => 'UTC',
                'status' => 'scheduled',
                'published_at' => now(),
                'meta' => ['source' => 'football-data.org', 'source_match_id' => $sourceMatch['id']],
            ]);
        }

        return ['matches' => $sourceMatches];
    }

    private function sourceMatch(int $id, string $status, string $home, string $away, ?int $homeScore = null, ?int $awayScore = null): array
    {
        return [
            'id' => $id,
            'utcDate' => now()->addDays($id % 20)->utc()->format('Y-m-d\TH:i:s\Z'),
            'status' => $status,
            'stage' => 'GROUP_STAGE',
            'group' => 'GROUP_A',
            'matchday' => 1,
            'homeTeam' => ['id' => $id + 1, 'name' => $home, 'tla' => Str::upper(Str::substr($home, 0, 3)), 'crest' => null],
            'awayTeam' => ['id' => $id + 2, 'name' => $away, 'tla' => Str::upper(Str::substr($away, 0, 3)), 'crest' => null],
            'score' => ['fullTime' => ['home' => $homeScore, 'away' => $awayScore]],
        ];
    }
}
