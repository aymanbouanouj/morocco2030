<?php

namespace Tests\Feature\Site;

use App\Http\Controllers\Site\MatchController;
use App\Http\Controllers\Site\PlayerController;
use App\Http\Controllers\Site\StandingsController;
use App\Http\Controllers\Site\TeamController;
use App\Models\MatchFixture;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class FootballDataPlayerTeamDisplayTest extends TestCase
{
    use BuildsAdminTestData;
    use RefreshDatabase;

    public function test_public_teams_index_lists_48_real_fd_teams_only(): void
    {
        $data = $this->seedPublicPlayerTeamDataset();

        $teams = app(TeamController::class)->index()->getData()['teams'];

        $this->assertSame(48, $teams->total());
        $this->assertTrue($teams->getCollection()->every(fn (Team $team) => ! (bool) data_get($team->meta, 'placeholder')));
        $this->assertFalse($teams->getCollection()->contains(fn (Team $team) => $team->is($data['placeholderTeam'])));
        $this->assertFalse($teams->getCollection()->contains(fn (Team $team) => $team->is($data['showcaseTeam'])));
    }

    public function test_public_teams_index_route_hides_placeholders_and_legacy_teams(): void
    {
        $this->seedPublicPlayerTeamDataset();

        $this->get(route('teams.index'))
            ->assertOk()
            ->assertSee('Brazil', false)
            ->assertSee('England', false)
            ->assertSee('Germany', false)
            ->assertSee('Morocco', false)
            ->assertSee('Spain', false)
            ->assertDontSee('TBD Home FD-WC', false)
            ->assertDontSee('Showcase Legends', false);
    }

    public function test_real_team_detail_displays_coach_and_squad(): void
    {
        $data = $this->seedPublicPlayerTeamDataset();

        $this->get(route('teams.show', $data['teams']['Morocco']->slug))
            ->assertOk()
            ->assertSee('Morocco Coach', false)
            ->assertSee('Morocco Player', false)
            ->assertSee('#10', false)
            ->assertSee('Forward', false);
    }

    public function test_real_team_detail_without_coach_uses_safe_empty_copy(): void
    {
        $data = $this->seedPublicPlayerTeamDataset();

        $this->get(route('teams.show', $data['teams']['Brazil']->slug))
            ->assertOk()
            ->assertSee('Coach', false)
            ->assertSee('Not provided', false)
            ->assertSee('Brazil Player', false);
    }

    public function test_placeholder_team_detail_returns_404(): void
    {
        $data = $this->seedPublicPlayerTeamDataset();

        $this->get(route('teams.show', $data['placeholderTeam']->slug))->assertNotFound();
    }

    public function test_legacy_team_detail_returns_404_when_fd_world_cup_exists(): void
    {
        $data = $this->seedPublicPlayerTeamDataset();

        $this->get(route('teams.show', $data['showcaseTeam']->slug))->assertNotFound();
    }

    public function test_public_players_index_lists_real_fd_team_players_only(): void
    {
        $data = $this->seedPublicPlayerTeamDataset();

        $players = app(PlayerController::class)->index()->getData()['players'];

        $this->assertSame(48, $players->total());
        $this->assertTrue($players->getCollection()->every(fn (Player $player) => ! (bool) data_get($player->team?->meta, 'placeholder')));
        $this->assertFalse($players->getCollection()->contains(fn (Player $player) => $player->is($data['placeholderPlayer'])));
        $this->assertFalse($players->getCollection()->contains(fn (Player $player) => $player->is($data['showcasePlayer'])));
    }

    public function test_public_players_index_route_hides_placeholder_and_legacy_players(): void
    {
        $this->seedPublicPlayerTeamDataset();

        $this->get(route('players.index'))
            ->assertOk()
            ->assertSee('Brazil Player', false)
            ->assertSee('England Player', false)
            ->assertSee('Germany Player', false)
            ->assertSee('Morocco Player', false)
            ->assertSee('Spain Player', false)
            ->assertDontSee('Placeholder Player', false)
            ->assertDontSee('Showcase Player', false);
    }

    public function test_real_player_detail_displays_team_number_position_and_nationality(): void
    {
        $data = $this->seedPublicPlayerTeamDataset();

        $this->get(route('players.show', $data['players']['Spain']->slug))
            ->assertOk()
            ->assertSee('Spain Player', false)
            ->assertSee('Spain', false)
            ->assertSee('#8', false)
            ->assertSee('Midfielder', false)
            ->assertSee('ES', false);
    }

    public function test_placeholder_team_player_detail_returns_404(): void
    {
        $data = $this->seedPublicPlayerTeamDataset();

        $this->get(route('players.show', $data['placeholderPlayer']->slug))->assertNotFound();
    }

    public function test_matches_page_still_scopes_to_104_fd_world_cup_matches(): void
    {
        $this->seedPublicPlayerTeamDataset();

        $matches = app(MatchController::class)->index()->getData()['matches'];

        $this->assertSame(104, $matches->total());
        $this->assertTrue($matches->getCollection()->every(fn (MatchFixture $match) => str_starts_with($match->code, 'FD-WC-')));
        $this->get(route('matches.index'))
            ->assertOk()
            ->assertDontSee('SHOWCASE-001', false);
    }

    public function test_standings_page_keeps_12_fd_groups_and_hides_placeholders(): void
    {
        $this->seedPublicPlayerTeamDataset();

        $groups = app(StandingsController::class)->index()->getData()['groups'];

        $this->assertSame(range('A', 'L'), $groups->pluck('code')->all());
        $this->assertFalse($groups->flatMap(fn ($group) => $group->standings)->contains(
            fn ($standing) => str_contains($standing->team->name, 'TBD')
        ));

        $this->get(route('standings.index'))
            ->assertOk()
            ->assertSee('Group A', false)
            ->assertSee('Group L', false)
            ->assertDontSee('TBD Home FD-WC', false)
            ->assertDontSee('Showcase Legends', false);
    }

    private function seedPublicPlayerTeamDataset(): array
    {
        $city = $this->makeCity('Casablanca', ['code' => 'CAS']);
        $stadium = $this->makeStadium($city, 'Mohammed V Stadium', ['code' => 'MO5']);
        $groups = collect(range('A', 'L'))->mapWithKeys(fn (string $code) => [$code => $this->makeGroup($code)]);
        $showcaseGroup = $this->makeGroup('Z');

        $teamNames = collect([
            'Brazil' => ['code' => 'BRA', 'coach' => null, 'number' => 7, 'position' => 'forward', 'nationality' => 'BR'],
            'England' => ['code' => 'ENG', 'coach' => 'England Coach', 'number' => 9, 'position' => 'forward', 'nationality' => 'GB'],
            'Germany' => ['code' => 'GER', 'coach' => 'Germany Coach', 'number' => 6, 'position' => 'defender', 'nationality' => 'DE'],
            'Morocco' => ['code' => 'MAR', 'coach' => 'Morocco Coach', 'number' => 10, 'position' => 'forward', 'nationality' => 'MA'],
            'Spain' => ['code' => 'ESP', 'coach' => 'Spain Coach', 'number' => 8, 'position' => 'midfielder', 'nationality' => 'ES'],
        ])->merge(collect(range(1, 43))->mapWithKeys(fn (int $number) => [
            'World Team '.str_pad((string) $number, 2, '0', STR_PAD_LEFT) => [
                'code' => 'W'.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'coach' => 'World Team '.$number.' Coach',
                'number' => ($number % 23) + 1,
                'position' => 'midfielder',
                'nationality' => 'MA',
            ],
        ]));

        $teams = collect();
        $players = collect();

        foreach ($teamNames->values() as $index => $details) {
            $name = $teamNames->keys()[$index];
            $group = $groups->values()[$index % $groups->count()];
            $team = $this->makeTeam($group, $name, [
                'code' => $details['code'],
                'coach_name' => $details['coach'],
            ]);
            $team->update([
                'meta' => [
                    'source' => 'football-data.org',
                    'football_data' => ['crest' => 'https://crests.example/'.Str::lower($details['code']).'.svg'],
                ],
            ]);

            $player = $this->makePlayer($team, $name.' Player', [
                'shirt_number' => $details['number'],
                'position' => $details['position'],
                'nationality_code' => $details['nationality'],
                'external_provider' => 'football-data.org',
                'external_id' => 'player-'.$details['code'],
            ]);

            $teams->put($name, $team->fresh());
            $players->put($name, $player->fresh('team'));
        }

        $placeholderTeam = $this->makeTeam($groups['A'], 'TBD Home FD-WC-537417', ['code' => 'FDH537417']);
        $placeholderTeam->update(['meta' => ['source' => 'football-data.org', 'placeholder' => true]]);
        $placeholderPlayer = $this->makePlayer($placeholderTeam, 'Placeholder Player', [
            'external_provider' => 'football-data.org',
            'external_id' => 'player-placeholder',
        ]);

        $showcaseTeam = $this->makeTeam($showcaseGroup, 'Showcase Legends', ['code' => 'OLD']);
        $showcasePlayer = $this->makePlayer($showcaseTeam, 'Showcase Player');

        $this->makeFdWorldCupMatches($stadium, $city, $groups, $teams->values(), $placeholderTeam);

        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $showcaseGroup->id,
            'home_team_id' => $showcaseTeam->id,
            'away_team_id' => $teams['Morocco']->id,
            'code' => 'SHOWCASE-001',
            'slug' => 'showcase-001',
            'stage_type' => 'group',
            'round_number' => 1,
            'match_date' => now()->subDays(10),
            'timezone' => 'UTC',
            'status' => 'completed',
            'home_score' => 3,
            'away_score' => 1,
            'published_at' => now(),
        ]);

        return [
            'teams' => $teams,
            'players' => $players,
            'placeholderTeam' => $placeholderTeam->fresh(),
            'placeholderPlayer' => $placeholderPlayer->fresh('team'),
            'showcaseTeam' => $showcaseTeam->fresh(),
            'showcasePlayer' => $showcasePlayer->fresh('team'),
        ];
    }

    private function makeFdWorldCupMatches($stadium, $city, Collection $groups, Collection $teams, Team $placeholderTeam): void
    {
        for ($i = 0; $i < 104; $i++) {
            $group = $groups->values()[$i % $groups->count()];
            $home = $i === 103 ? $placeholderTeam : $teams[$i % $teams->count()];
            $away = $teams[($i + 1) % $teams->count()];
            $sourceId = 537327 + $i;
            $completed = $i % 12 === 0;

            MatchFixture::query()->create([
                'stadium_id' => $stadium->id,
                'city_id' => $city->id,
                'group_id' => $group->id,
                'home_team_id' => $home->id,
                'away_team_id' => $away->id,
                'code' => 'FD-WC-'.$sourceId,
                'slug' => Str::slug('FD-WC-'.$sourceId),
                'stage_type' => 'group',
                'round_number' => 1,
                'match_date' => now()->addDays($i),
                'timezone' => 'UTC',
                'status' => $completed ? 'completed' : 'scheduled',
                'home_score' => $completed ? 2 : null,
                'away_score' => $completed ? 1 : null,
                'published_at' => now(),
                'meta' => [
                    'source' => 'football-data.org',
                    'source_match_id' => $sourceId,
                    'football_data' => [
                        'id' => $sourceId,
                        'group' => 'GROUP_'.$group->code,
                        'group_label' => 'Group '.$group->code,
                        'stage' => 'GROUP_STAGE',
                        'stage_label' => 'Group Stage',
                        'matchday' => 1,
                    ],
                ],
            ]);
        }
    }
}
