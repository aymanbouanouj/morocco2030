<?php

namespace Tests\Feature\Site;

use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\KnockoutController;
use App\Http\Controllers\Site\MatchController;
use App\Http\Controllers\Site\TeamController;
use App\Models\MatchFixture;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class FootballDataPublicConsistencyTest extends TestCase
{
    use BuildsAdminTestData;
    use RefreshDatabase;

    public function test_public_matches_page_scopes_to_fd_world_cup_matches_not_mixed_showcase_matches(): void
    {
        $this->seedFdWorldCupAndShowcaseMatches();

        $view = app(MatchController::class)->index();
        $matches = $view->getData()['matches'];

        $this->assertSame(104, $matches->total());
        $this->assertTrue($matches->getCollection()->every(fn ($match) => str_starts_with($match->code, 'FD-WC-')));
        $this->assertFalse($matches->getCollection()->contains(fn ($match) => str_starts_with($match->code, 'SHOWCASE-')));
    }

    public function test_public_knockout_page_does_not_mix_fd_and_showcase_brackets(): void
    {
        $this->seedFdWorldCupAndShowcaseMatches();

        $fdKnockout = MatchFixture::query()->where('stage_type', 'round_of_16')->firstOrFail();
        $showcaseKnockout = $fdKnockout->replicate();
        $showcaseKnockout->forceFill([
            'code' => 'SHOWCASE-KNOCKOUT',
            'slug' => 'showcase-knockout',
        ])->save();

        $rounds = app(KnockoutController::class)()->getData()['rounds'];
        $knockoutMatches = $rounds->flatten(1);

        $this->assertCount(1, $knockoutMatches);
        $this->assertTrue($knockoutMatches->every(fn (MatchFixture $match) => str_starts_with($match->code, 'FD-WC-')));
        $this->assertFalse($knockoutMatches->contains(fn (MatchFixture $match) => $match->code === 'SHOWCASE-KNOCKOUT'));
    }

    public function test_public_team_display_shows_crest_or_tbd_fallback(): void
    {
        $this->seedFdWorldCupAndShowcaseMatches();

        $view = app(TeamController::class)->index();
        $teams = $view->getData()['teams'];
        $mexico = Team::query()->where('code', 'MEX')->firstOrFail();
        $placeholder = Team::query()->where('code', 'FDH537417')->firstOrFail();

        $this->assertSame(2, $teams->total());
        $this->assertFalse($teams->getCollection()->contains(fn ($team) => (bool) data_get($team->meta, 'placeholder')));

        $crestHtml = Blade::render("@include('public.partials.local-entity-media', ['type' => 'team', 'model' => \$team, 'slug' => \$team->slug, 'code' => \$team->code, 'name' => \$team->name, 'media' => ['url' => null, 'alt' => \$team->name]])", ['team' => $mexico]);
        $placeholderHtml = Blade::render("@include('public.partials.local-entity-media', ['type' => 'team', 'model' => \$team, 'slug' => \$team->slug, 'code' => \$team->code, 'name' => \$team->name, 'media' => ['url' => null, 'alt' => \$team->name]])", ['team' => $placeholder]);

        $this->assertStringContainsString('/assets/images/flag/flags/4x3/mx.svg', $crestHtml);
        $this->assertStringNotContainsString('https://crests.example/mex.svg', $crestHtml);
        $this->assertStringContainsString('TBD', $placeholderHtml);
        $this->assertStringNotContainsString('FDH537417.svg', $placeholderHtml);
    }

    public function test_public_detail_routes_reject_showcase_records_when_fd_world_cup_exists(): void
    {
        $this->seedFdWorldCupAndShowcaseMatches();

        $showcaseMatch = MatchFixture::query()->where('code', 'SHOWCASE-001')->firstOrFail();
        $showcaseTeam = Team::query()->where('code', 'OLD')->firstOrFail();

        $this->get(route('matches.show', $showcaseMatch->slug))->assertNotFound();
        $this->get(route('teams.show', $showcaseTeam->slug))->assertNotFound();
    }

    public function test_public_home_metrics_scope_to_fd_world_cup_dataset(): void
    {
        $this->seedFdWorldCupAndShowcaseMatches();

        $view = app(HomeController::class)();
        $data = $view->getData();
        $metrics = collect($data['tournamentMetrics'])->pluck('value', 'label');

        $this->assertSame(104, $metrics['Matches']);
        $this->assertSame(1, $metrics['Completed']);
        $this->assertSame(2, $metrics['Goals']);
        $this->assertSame(2, $metrics['Teams']);
        $this->assertSame(1, $metrics['Groups']);
        $this->assertTrue($data['upcomingMatches']->every(fn ($match) => str_starts_with($match->code, 'FD-WC-')));
        $this->assertTrue($data['recentResults']->every(fn ($match) => str_starts_with($match->code, 'FD-WC-')));
        $this->assertFalse($data['teamsPreview']->contains(fn ($team) => $team->code === 'OLD'));
    }

    protected function seedFdWorldCupAndShowcaseMatches(): void
    {
        $city = $this->makeCity('Casablanca', ['code' => 'CAS']);
        $stadium = $this->makeStadium($city, 'Mohammed V Stadium', ['code' => 'MO5']);
        $group = $this->makeGroup('A');
        $showcaseGroup = $this->makeGroup('Z');
        $mexico = $this->makeTeam($group, 'Mexico', ['code' => 'MEX']);
        $southAfrica = $this->makeTeam($group, 'South Africa', ['code' => 'RSA']);
        $showcaseTeam = $this->makeTeam($showcaseGroup, 'Showcase Legends', ['code' => 'OLD']);
        $showcaseOpponent = $this->makeTeam($showcaseGroup, 'Showcase Rivals', ['code' => 'RIV']);
        $homeTbd = $this->makeTeam($group, 'TBD Home FD-WC-537417', ['code' => 'FDH537417']);
        $awayTbd = $this->makeTeam($group, 'TBD Away FD-WC-537417', ['code' => 'FDA537417']);
        $mexico->update(['meta' => ['source' => 'football-data.org', 'football_data' => ['crest' => 'https://crests.example/mex.svg']]]);
        $southAfrica->update(['meta' => ['source' => 'football-data.org', 'football_data' => ['crest' => 'https://crests.example/rsa.svg']]]);
        $homeTbd->update(['meta' => ['source' => 'football-data.org', 'placeholder' => true]]);
        $awayTbd->update(['meta' => ['source' => 'football-data.org', 'placeholder' => true]]);

        for ($i = 0; $i < 104; $i++) {
            $id = $i === 0 ? 537327 : (600000 + $i);
            $home = $i === 1 ? $homeTbd : $mexico;
            $away = $i === 1 ? $awayTbd : $southAfrica;

            MatchFixture::query()->create([
                'stadium_id' => $stadium->id,
                'city_id' => $city->id,
                'group_id' => $group->id,
                'home_team_id' => $home->id,
                'away_team_id' => $away->id,
                'code' => 'FD-WC-'.$id,
                'slug' => Str::slug('FD-WC-'.$id),
                'stage_type' => $i === 1 ? 'round_of_16' : 'group',
                'round_number' => 1,
                'match_date' => now()->addDays($i),
                'timezone' => 'UTC',
                'status' => $i === 0 ? 'completed' : 'scheduled',
                'home_score' => $i === 0 ? 2 : null,
                'away_score' => $i === 0 ? 0 : null,
                'published_at' => now(),
                'meta' => [
                    'source' => 'football-data.org',
                    'source_match_id' => $id,
                    'football_data' => [
                        'id' => $id,
                        'group' => $i === 1 ? null : 'GROUP_A',
                        'group_label' => $i === 1 ? null : 'Group A',
                        'stage' => $i === 1 ? 'LAST_16' : 'GROUP_STAGE',
                        'stage_label' => $i === 1 ? 'Round of 16' : 'Group Stage',
                        'matchday' => $i === 1 ? null : 1,
                    ],
                ],
            ]);
        }

        for ($i = 1; $i <= 4; $i++) {
            MatchFixture::query()->create([
                'stadium_id' => $stadium->id,
                'city_id' => $city->id,
                'group_id' => $showcaseGroup->id,
                'home_team_id' => $showcaseTeam->id,
                'away_team_id' => $showcaseOpponent->id,
                'code' => 'SHOWCASE-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'slug' => 'showcase-'.$i,
                'stage_type' => 'group',
                'round_number' => 1,
                'match_date' => now()->addDays($i + 200),
                'timezone' => 'UTC',
                'status' => $i === 1 ? 'completed' : 'scheduled',
                'home_score' => $i === 1 ? 5 : null,
                'away_score' => $i === 1 ? 4 : null,
                'published_at' => now(),
            ]);
        }
    }
}
