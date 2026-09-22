<?php

namespace Tests\Feature\Site;

use App\Models\MatchFixture;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class FootballDataMatchRefreshPublicTest extends TestCase
{
    use BuildsAdminTestData;
    use RefreshDatabase;

    public function test_public_match_pages_reflect_reconciled_fd_world_cup_state(): void
    {
        $this->seedReconciledWorldCupMatches();

        $this->get(route('matches.index'))
            ->assertOk()
            ->assertSee('Live Source Home', false)
            ->assertSee('Scheduled Source Home', false);

        $this->get(route('results.index'))
            ->assertOk()
            ->assertSee('Finished Source Home', false)
            ->assertSee('2 - 0', false)
            ->assertDontSee('Showcase Result', false);

        $standings = $this->get(route('standings.index'))
            ->assertOk()
            ->assertSee('Group A', false)
            ->assertSee('Group L', false)
            ->assertSee('Finished Source Home', false)
            ->assertSee('3', false);

        preg_match_all('/standings-card|entity-ref-standings-group|Group [A-L]/', $standings->getContent(), $matches);
        $this->assertSame(12, MatchFixture::query()
            ->where('code', 'like', 'FD-WC-%')
            ->get()
            ->map(fn (MatchFixture $match) => data_get($match->meta, 'football_data.group'))
            ->filter()
            ->unique()
            ->count());
    }

    private function seedReconciledWorldCupMatches(): void
    {
        $city = $this->makeCity('Rabat', ['code' => 'RBT']);
        $stadium = $this->makeStadium($city, 'Rabat World Cup Stadium', ['code' => 'RBT']);
        $groups = collect(range('A', 'L'))->mapWithKeys(fn (string $code) => [$code => $this->makeGroup($code)]);
        $finishedGroup = $groups['A'];
        $finishedHome = $this->makeTeam($finishedGroup, 'Finished Source Home', ['code' => 'FSH']);
        $finishedAway = $this->makeTeam($finishedGroup, 'Finished Source Away', ['code' => 'FSA']);
        $liveHome = $this->makeTeam($finishedGroup, 'Live Source Home', ['code' => 'LSH']);
        $liveAway = $this->makeTeam($finishedGroup, 'Live Source Away', ['code' => 'LSA']);
        $scheduledHome = $this->makeTeam($finishedGroup, 'Scheduled Source Home', ['code' => 'SSH']);
        $scheduledAway = $this->makeTeam($finishedGroup, 'Scheduled Source Away', ['code' => 'SSA']);

        $this->createFdMatch($stadium, $city, $finishedGroup, $finishedHome, $finishedAway, 537327, 'completed', 'GROUP_A', 2, 0);
        $this->createFdMatch($stadium, $city, $finishedGroup, $liveHome, $liveAway, 537328, 'live', 'GROUP_A');
        $this->createFdMatch($stadium, $city, $finishedGroup, $scheduledHome, $scheduledAway, 537329, 'scheduled', 'GROUP_A');

        $index = 3;
        foreach ($groups as $code => $group) {
            $home = $this->makeTeam($group, 'Group '.$code.' Home', ['code' => 'H'.$code.$index]);
            $away = $this->makeTeam($group, 'Group '.$code.' Away', ['code' => 'A'.$code.$index]);
            $this->createFdMatch($stadium, $city, $group, $home, $away, 600000 + $index, 'scheduled', 'GROUP_'.$code);
            $index++;
        }

        for (; $index < 104; $index++) {
            $group = $groups[range('A', 'L')[$index % 12]];
            $home = $this->makeTeam($group, 'FD Home '.$index, ['code' => 'FH'.$index]);
            $away = $this->makeTeam($group, 'FD Away '.$index, ['code' => 'FA'.$index]);
            $this->createFdMatch($stadium, $city, $group, $home, $away, 600000 + $index, 'scheduled', 'GROUP_'.$group->code);
        }

        $showcaseGroup = $this->makeGroup('Z');
        $showcaseHome = $this->makeTeam($showcaseGroup, 'Showcase Result', ['code' => 'SHR']);
        $showcaseAway = $this->makeTeam($showcaseGroup, 'Showcase Away', ['code' => 'SHA']);
        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $showcaseGroup->id,
            'home_team_id' => $showcaseHome->id,
            'away_team_id' => $showcaseAway->id,
            'code' => 'SHOWCASE-RESULT',
            'slug' => 'showcase-result',
            'stage_type' => 'group',
            'match_date' => now()->subDay(),
            'timezone' => 'UTC',
            'status' => 'completed',
            'home_score' => 9,
            'away_score' => 8,
            'published_at' => now(),
        ]);
    }

    private function createFdMatch($stadium, $city, $group, $home, $away, int $id, string $status, string $apiGroup, ?int $homeScore = null, ?int $awayScore = null): void
    {
        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $group->id,
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'code' => 'FD-WC-'.$id,
            'slug' => Str::slug('FD-WC-'.$id),
            'stage_type' => 'group',
            'round_number' => 1,
            'match_date' => $status === 'completed' ? now()->subDay() : now()->addDay(),
            'timezone' => 'UTC',
            'status' => $status,
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'published_at' => now(),
            'meta' => [
                'source' => 'football-data.org',
                'football_data' => [
                    'group' => $apiGroup,
                    'group_label' => 'Group '.Str::after($apiGroup, 'GROUP_'),
                ],
            ],
        ]);
    }
}
