<?php

namespace Tests\Feature\Release;

use App\Http\Controllers\Site\MatchController;
use App\Http\Controllers\Site\PlayerController;
use App\Http\Controllers\Site\StandingsController;
use App\Http\Controllers\Site\TeamController;
use App\Models\City;
use App\Models\Group;
use App\Models\MatchFixture;
use App\Models\NewsCategory;
use App\Models\Partner;
use App\Models\Player;
use App\Models\Stadium;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class FinalEngineeringSmokeTest extends TestCase
{
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_public_core_pages_return_200_without_fake_links(): void
    {
        foreach ([
            route('home'),
            route('news.index'),
            route('matches.index'),
            route('results.index'),
            route('standings.index'),
            route('teams.index'),
            route('players.index'),
            route('partners.index'),
            route('cities.index'),
            route('stadiums.index'),
            route('map.index'),
            route('knockout.index'),
            route('search.index'),
        ] as $route) {
            $this->get($route)
                ->assertOk()
                ->assertSee('Ayman Bounaouj', false)
                ->assertDontSee('href="#"', false)
                ->assertDontSee('Whoops', false);
        }
    }

    public function test_public_football_data_pages_keep_release_scoping(): void
    {
        $data = $this->seedFootballDataReleaseDataset();

        $matches = app(MatchController::class)->index()->getData()['matches'];
        $this->assertSame(104, $matches->total());
        $this->assertTrue($matches->getCollection()->every(
            fn (MatchFixture $match) => str_starts_with($match->code, 'FD-WC-')
        ));

        $groups = app(StandingsController::class)->index()->getData()['groups'];
        $this->assertSame(range('A', 'L'), $groups->pluck('code')->all());

        $teams = app(TeamController::class)->index()->getData()['teams'];
        $this->assertSame(48, $teams->total());
        $this->assertFalse($teams->getCollection()->contains(
            fn (Team $team) => (bool) data_get($team->meta, 'placeholder')
        ));

        $players = app(PlayerController::class)->index()->getData()['players'];
        $this->assertSame(48, $players->total());
        $this->assertTrue($players->getCollection()->every(
            fn (Player $player) => $player->external_provider === 'football-data.org'
        ));

        $this->get(route('matches.index'))->assertOk()->assertDontSee('SHOWCASE-001', false);
        $this->get(route('standings.index'))->assertOk()->assertSee('Group L', false);
        $this->get(route('teams.index'))->assertOk()->assertDontSee($data['placeholderTeam']->name, false);
        $this->get(route('players.index'))->assertOk()->assertDontSee($data['placeholderPlayer']->display_name, false);
    }

    public function test_partner_visibility_and_sponsor_manager_access_remain_hardened(): void
    {
        $active = Partner::query()->create([
            'name' => 'Release Active Partner',
            'slug' => 'release-active-partner',
            'category' => 'Official Partner',
            'tier' => 'Gold',
            'logo_path' => 'partners/logos/release-active.png',
            'logo_alt' => 'Release active partner logo',
            'status' => 'active',
        ]);

        Partner::query()->create([
            'name' => 'Release Inactive Partner',
            'slug' => 'release-inactive-partner',
            'category' => 'Official Partner',
            'tier' => 'Silver',
            'status' => 'inactive',
        ]);

        $archived = Partner::query()->create([
            'name' => 'Release Archived Partner',
            'slug' => 'release-archived-partner',
            'category' => 'Official Partner',
            'tier' => 'Bronze',
            'status' => 'active',
        ]);
        $archived->delete();

        $sponsorManager = $this->demoUser('sponsor.manager@morocco2030.test');

        $this->actingAs($sponsorManager)
            ->get(route('admin.partners.index'))
            ->assertOk()
            ->assertSee('Create Partner', false)
            ->assertDontSee('href="#"', false);

        $this->get(route('partners.index'))
            ->assertOk()
            ->assertSee($active->name, false)
            ->assertSee($active->logoUrl(), false)
            ->assertDontSee('Release Inactive Partner', false)
            ->assertDontSee('Release Archived Partner', false)
            ->assertDontSee('href="#"', false);
    }

    public function test_media_review_counts_partner_logo_contextual_asset(): void
    {
        Storage::fake('public');

        $path = UploadedFile::fake()
            ->image('release-partner-logo.png', 240, 120)
            ->store('partners/logos', 'public');

        Partner::query()->create([
            'name' => 'Release Logo Partner',
            'slug' => 'release-logo-partner',
            'category' => 'Official Partner',
            'tier' => 'Gold',
            'logo_path' => $path,
            'logo_alt' => 'Release logo partner mark',
            'status' => 'active',
        ]);

        $this->actingAs($this->demoUser('media.manager@morocco2030.test'))
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->assertSee('Partner logos', false)
            ->assertSee('1 total visual asset', false)
            ->assertSee('Release Logo Partner', false)
            ->assertDontSee('href="#"', false);
    }

    public function test_public_and_operational_rbac_remain_enforced(): void
    {
        $this->actingAs($this->demoUser('public.user@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->get(route('admin.football-data-import.index'))
            ->assertForbidden();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('href="#"', false);
    }

    public function test_news_create_form_has_tournament_updates_and_draft_workflow(): void
    {
        NewsCategory::ensureTournamentUpdatesCategory();

        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->get(route('admin.news.create'))
            ->assertOk()
            ->assertSee('Tournament Updates', false)
            ->assertSee('Save Draft', false)
            ->assertDontSee('Publish', false)
            ->assertDontSee('href="#"', false);
    }

    private function seedFootballDataReleaseDataset(): array
    {
        $city = City::query()->create([
            'name' => 'Casablanca',
            'slug' => 'casablanca',
            'code' => 'CAS',
            'country_code' => 'MA',
            'region' => 'Casablanca-Settat',
            'status' => 'active',
        ]);

        $stadium = Stadium::query()->create([
            'city_id' => $city->id,
            'name' => 'Mohammed V Stadium',
            'slug' => 'mohammed-v-stadium',
            'code' => 'MO5',
            'capacity' => 67000,
            'address' => 'Casablanca',
            'surface_type' => 'grass',
            'status' => 'active',
        ]);

        $groups = collect(range('A', 'L'))->mapWithKeys(fn (string $code) => [
            $code => Group::query()->create([
                'name' => 'Group '.$code,
                'code' => $code,
                'sort_order' => ord($code) - 64,
            ]),
        ]);

        $teams = collect(range(1, 48))->map(function (int $number) use ($groups): Team {
            $group = $groups->values()[($number - 1) % $groups->count()];
            $name = $number === 1 ? 'Morocco' : 'Release Team '.str_pad((string) $number, 2, '0', STR_PAD_LEFT);
            $code = $number === 1 ? 'MOR' : 'R'.str_pad((string) $number, 2, '0', STR_PAD_LEFT);

            return Team::query()->create([
                'group_id' => $group->id,
                'name' => $name,
                'short_name' => $code,
                'code' => $code,
                'slug' => Str::slug($name),
                'coach_name' => $number <= 46 ? $name.' Coach' : null,
                'team_type' => 'national',
                'status' => 'active',
                'meta' => [
                    'source' => 'football-data.org',
                    'placeholder' => false,
                    'football_data' => [
                        'id' => 800 + $number,
                        'crest' => 'https://crests.example/'.$code.'.svg',
                    ],
                ],
            ]);
        });

        $players = $teams->map(function (Team $team, int $index): Player {
            return Player::query()->create([
                'team_id' => $team->id,
                'display_name' => $team->name.' Player',
                'first_name' => $team->name,
                'last_name' => 'Player',
                'slug' => Str::slug($team->name.' Player'),
                'shirt_number' => ($index % 23) + 1,
                'position' => 'midfielder',
                'nationality_code' => $team->code,
                'status' => 'active',
                'external_provider' => 'football-data.org',
                'external_id' => 'release-player-'.$team->code,
            ]);
        });

        $placeholderTeam = Team::query()->create([
            'group_id' => $groups['A']->id,
            'name' => 'TBD Home FD-WC Release',
            'short_name' => 'TBD',
            'code' => 'TBDREL',
            'slug' => 'tbd-home-fd-wc-release',
            'team_type' => 'national',
            'status' => 'active',
            'meta' => [
                'source' => 'football-data.org',
                'placeholder' => true,
            ],
        ]);

        $placeholderPlayer = Player::query()->create([
            'team_id' => $placeholderTeam->id,
            'display_name' => 'Placeholder Release Player',
            'first_name' => 'Placeholder',
            'last_name' => 'Player',
            'slug' => 'placeholder-release-player',
            'shirt_number' => 99,
            'position' => 'midfielder',
            'nationality_code' => 'TBD',
            'status' => 'active',
            'external_provider' => 'football-data.org',
            'external_id' => 'placeholder-release-player',
        ]);

        $this->makeFdMatches($stadium, $city, $groups, $teams, $placeholderTeam);

        $showcaseGroup = Group::query()->create([
            'name' => 'Showcase Group',
            'code' => 'Z',
            'sort_order' => 99,
        ]);

        $showcaseTeam = Team::query()->create([
            'group_id' => $showcaseGroup->id,
            'name' => 'Showcase Legends',
            'short_name' => 'OLD',
            'code' => 'OLD',
            'slug' => 'showcase-legends',
            'team_type' => 'national',
            'status' => 'active',
        ]);

        MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $showcaseGroup->id,
            'home_team_id' => $showcaseTeam->id,
            'away_team_id' => $teams->first()->id,
            'code' => 'SHOWCASE-001',
            'slug' => 'showcase-001',
            'stage_type' => 'group',
            'match_date' => now()->subDay(),
            'timezone' => 'Africa/Casablanca',
            'status' => 'completed',
            'home_score' => 2,
            'away_score' => 1,
            'published_at' => now(),
        ]);

        return [
            'teams' => $teams,
            'players' => $players,
            'placeholderTeam' => $placeholderTeam,
            'placeholderPlayer' => $placeholderPlayer,
        ];
    }

    private function makeFdMatches(
        Stadium $stadium,
        City $city,
        Collection $groups,
        Collection $teams,
        Team $placeholderTeam
    ): void {
        for ($index = 0; $index < 104; $index++) {
            $group = $groups->values()[$index % $groups->count()];
            $homeTeam = $index === 103 ? $placeholderTeam : $teams[$index % $teams->count()];
            $awayTeam = $teams[($index + 1) % $teams->count()];
            $sourceId = 537327 + $index;
            $completed = $index % 12 === 0;

            MatchFixture::query()->create([
                'stadium_id' => $stadium->id,
                'city_id' => $city->id,
                'group_id' => $group->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'code' => 'FD-WC-'.$sourceId,
                'slug' => Str::slug('FD-WC-'.$sourceId),
                'stage_type' => 'group',
                'round_number' => 1,
                'match_date' => now()->addDays($index),
                'timezone' => 'Africa/Casablanca',
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
