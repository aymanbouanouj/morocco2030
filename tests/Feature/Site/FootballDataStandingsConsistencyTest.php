<?php

namespace Tests\Feature\Site;

use App\Http\Controllers\Site\StandingsController;
use App\Models\MatchFixture;
use App\Models\Standing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class FootballDataStandingsConsistencyTest extends TestCase
{
    use BuildsAdminTestData;
    use RefreshDatabase;

    public function test_public_standings_use_fd_world_cup_matches_instead_of_persisted_showcase_standings(): void
    {
        [$fdGroup, $showcaseGroup] = $this->seedFdWorldCupAndShowcaseStandings();

        $view = app(StandingsController::class)->index();
        $groups = $view->getData()['groups'];
        $standings = $groups->flatMap(fn ($group) => $group->standings);

        $this->assertTrue($view->getData()['fdWorldCupActive']);
        $this->assertTrue($groups->contains(fn ($group) => $group->code === $fdGroup->code));
        $this->assertFalse($groups->contains(fn ($group) => $group->code === $showcaseGroup->code));
        $this->assertSame(['MEX', 'RSA'], $standings->pluck('team.code')->all());
        $this->assertSame([3, 0], $standings->pluck('points')->all());
        $this->assertSame(0, Standing::query()->whereHas('team', fn ($query) => $query->where('code', 'MEX'))->count());

        $this->get(route('standings.index'))
            ->assertOk()
            ->assertSee('Mexico', false)
            ->assertSee('South Africa', false)
            ->assertDontSee('Showcase Legends', false);
    }

    public function test_public_fd_world_cup_standings_detail_rejects_legacy_group(): void
    {
        [, $showcaseGroup] = $this->seedFdWorldCupAndShowcaseStandings();

        $this->get(route('standings.show', $showcaseGroup->code))->assertNotFound();
    }

    private function seedFdWorldCupAndShowcaseStandings(): array
    {
        $city = $this->makeCity('Casablanca', ['code' => 'CAS']);
        $stadium = $this->makeStadium($city, 'Mohammed V Stadium', ['code' => 'MO5']);
        $fdGroup = $this->makeGroup('A');
        $showcaseGroup = $this->makeGroup('Z');

        $mexico = $this->makeTeam($fdGroup, 'Mexico', ['code' => 'MEX']);
        $southAfrica = $this->makeTeam($fdGroup, 'South Africa', ['code' => 'RSA']);
        $placeholder = $this->makeTeam($fdGroup, 'TBD Home FD-WC-537417', ['code' => 'FDH537417']);
        $showcaseTeam = $this->makeTeam($showcaseGroup, 'Showcase Legends', ['code' => 'OLD']);

        $mexico->update(['meta' => ['source' => 'football-data.org', 'football_data' => ['crest' => 'https://crests.example/mex.svg']]]);
        $southAfrica->update(['meta' => ['source' => 'football-data.org', 'football_data' => ['crest' => 'https://crests.example/rsa.svg']]]);
        $placeholder->update(['meta' => ['source' => 'football-data.org', 'placeholder' => true]]);

        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $fdGroup->id,
            'home_team_id' => $mexico->id,
            'away_team_id' => $southAfrica->id,
            'code' => 'FD-WC-537327',
            'slug' => Str::slug('FD-WC-537327'),
            'stage_type' => 'group',
            'round_number' => 1,
            'match_date' => now()->subDay(),
            'timezone' => 'UTC',
            'status' => 'completed',
            'home_score' => 2,
            'away_score' => 0,
            'published_at' => now(),
            'meta' => ['source' => 'football-data.org', 'source_match_id' => 537327],
        ]);

        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $fdGroup->id,
            'home_team_id' => $placeholder->id,
            'away_team_id' => $southAfrica->id,
            'code' => 'FD-WC-537417',
            'slug' => Str::slug('FD-WC-537417'),
            'stage_type' => 'group',
            'round_number' => 1,
            'match_date' => now()->addDay(),
            'timezone' => 'UTC',
            'status' => 'scheduled',
            'published_at' => now(),
            'meta' => ['source' => 'football-data.org', 'source_match_id' => 537417],
        ]);

        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $showcaseGroup->id,
            'home_team_id' => $showcaseTeam->id,
            'away_team_id' => $mexico->id,
            'code' => 'SHOWCASE-001',
            'slug' => 'showcase-001',
            'stage_type' => 'group',
            'round_number' => 1,
            'match_date' => now()->subDays(2),
            'timezone' => 'UTC',
            'status' => 'completed',
            'home_score' => 4,
            'away_score' => 1,
            'published_at' => now(),
        ]);

        Standing::query()->create([
            'group_id' => $showcaseGroup->id,
            'team_id' => $showcaseTeam->id,
            'position' => 1,
            'played' => 1,
            'won' => 1,
            'drawn' => 0,
            'lost' => 0,
            'goals_for' => 4,
            'goals_against' => 1,
            'goal_difference' => 3,
            'points' => 3,
        ]);

        return [$fdGroup, $showcaseGroup];
    }
}
