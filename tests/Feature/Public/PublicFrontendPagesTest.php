<?php

namespace Tests\Feature\Public;

use App\Models\KnockoutProgression;
use App\Models\MatchEvent;
use App\Models\MatchFixture;
use App\Models\MatchLineup;
use App\Models\MatchStatistic;
use App\Models\News;
use App\Models\Partner;
use App\Services\Sports\StandingsRecalculationService;
use Database\Seeders\FullTournamentStructureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class PublicFrontendPagesTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_public_index_pages_render_successfully_with_empty_state_data(): void
    {
        $routes = [
            route('home'),
            route('search.index'),
            route('map.index'),
            route('news.index'),
            route('matches.index'),
            route('results.index'),
            route('standings.index'),
            route('knockout.index'),
            route('teams.index'),
            route('players.index'),
            route('cities.index'),
            route('stadiums.index'),
            route('partners.index'),
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertOk();
        }
    }

    public function test_public_news_pages_only_show_published_public_content(): void
    {
        $author = $this->makeStaffUser();
        $category = $this->makeNewsCategory('Announcements');

        $published = $this->makeNews($author, $category, [
            'title' => 'Published Public Story',
            'slug' => 'published-public-story',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now()->subHour(),
        ]);

        $this->makeNews($author, $category, [
            'title' => 'Draft Story',
            'slug' => 'draft-story',
            'status' => 'draft',
            'visibility' => 'public',
        ]);

        $this->makeNews($author, $category, [
            'title' => 'Private Story',
            'slug' => 'private-story',
            'status' => 'published',
            'visibility' => 'private',
            'published_at' => now()->subHour(),
        ]);

        $this->get(route('news.index'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee('Draft Story')
            ->assertDontSee('Private Story');

        $this->get(route('news.show', $published->slug))
            ->assertOk()
            ->assertSee($published->title);

        $this->get(route('news.show', 'draft-story'))->assertNotFound();
        $this->get(route('news.show', 'private-story'))->assertNotFound();
    }

    public function test_homepage_header_keeps_primary_navigation_and_guest_ctas_available(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('search.index'), false)
            ->assertSee(route('map.index'), false)
            ->assertSee(route('matches.index'), false)
            ->assertSee(route('standings.index'), false)
            ->assertSee(route('news.index'), false)
            ->assertSee(route('login'), false)
            ->assertSee(route('register'), false)
            ->assertDontSee(route('admin.login'), false)
            ->assertSee('Login')
            ->assertSee('Create account')
            ->assertDontSee('Staff Sign In')
            ->assertDontSee('Staff Access')
            ->assertDontSee('Morocco 2030 official tournament portal')
            ->assertSee('Tournament Countdown')
            ->assertSee('Morocco 2030');
    }

    public function test_homepage_hides_low_value_empty_sections_until_public_content_exists(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Start With The Essentials')
            ->assertDontSee('Standings Preview')
            ->assertDontSee('Knockout Preview')
            ->assertDontSee('Official Partners');
    }

    public function test_homepage_uses_curated_previews_and_host_map_entry_point(): void
    {
        $author = $this->makeStaffUser();
        $category = $this->makeNewsCategory('Curated Updates');
        $group = $this->makeGroup('C');
        $city = $this->makeCity('Rabat', [
            'latitude' => 34.0209,
            'longitude' => -6.8416,
        ]);
        $stadium = $this->makeStadium($city, 'Prince Moulay Abdellah Stadium', [
            'latitude' => 33.9597,
            'longitude' => -6.8892,
        ]);

        for ($index = 1; $index <= 4; $index++) {
            $homeTeam = $this->makeTeam($group, 'Upcoming Home '.$index);
            $awayTeam = $this->makeTeam($group, 'Upcoming Away '.$index);

            MatchFixture::query()->create([
                'stadium_id' => $stadium->id,
                'city_id' => $city->id,
                'group_id' => $group->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'code' => 'CUR-UP-'.$index,
                'slug' => 'cur-up-'.$index,
                'stage_type' => 'group',
                'match_date' => now()->addDays($index),
                'timezone' => 'Africa/Casablanca',
                'status' => 'scheduled',
            ]);

            $resultHomeTeam = $this->makeTeam($group, 'Result Home '.$index);
            $resultAwayTeam = $this->makeTeam($group, 'Result Away '.$index);

            MatchFixture::query()->create([
                'stadium_id' => $stadium->id,
                'city_id' => $city->id,
                'group_id' => $group->id,
                'home_team_id' => $resultHomeTeam->id,
                'away_team_id' => $resultAwayTeam->id,
                'code' => 'CUR-RES-'.$index,
                'slug' => 'cur-res-'.$index,
                'stage_type' => 'group',
                'match_date' => now()->subDays($index),
                'timezone' => 'Africa/Casablanca',
                'status' => 'completed',
                'home_score' => $index,
                'away_score' => 0,
            ]);

            $this->makeNews($author, $category, [
                'title' => 'Curated Story '.$index,
                'slug' => 'curated-story-'.$index,
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subMinutes($index),
            ]);

            Partner::query()->create([
                'name' => 'Curated Partner '.$index,
                'slug' => 'curated-partner-'.$index,
                'category' => 'Official Partner',
                'tier' => 'Showcase',
                'status' => 'active',
            ]);
        }

        Partner::query()->create([
            'name' => 'Curated Partner 5',
            'slug' => 'curated-partner-5',
            'category' => 'Official Partner',
            'tier' => 'Showcase',
            'status' => 'active',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Tournament Key Figures')
            ->assertSee('Competition Snapshot')
            ->assertSee('Host Map Preview')
            ->assertSee('Explore Host Map')
            ->assertSee(route('map.index'), false)
            ->assertSee('Rabat')
            ->assertSee('Prince Moulay Abdellah Stadium')
            ->assertSee('View All Matches')
            ->assertSee('View All Results')
            ->assertSee('More News')
            ->assertSee('View All Partners')
            ->assertSee('Upcoming Home 1')
            ->assertSee('Upcoming Home 3')
            ->assertDontSee('Upcoming Home 4')
            ->assertSee('Result Home 1')
            ->assertSee('Result Home 3')
            ->assertDontSee('Result Home 4')
            ->assertSee('Curated Story 1')
            ->assertSee('Curated Story 3')
            ->assertDontSee('Curated Story 4')
            ->assertSee('Curated Partner 4')
            ->assertDontSee('Curated Partner 5');
    }

    public function test_public_tournament_pages_render_real_competition_data(): void
    {
        $author = $this->makeStaffUser();
        $category = $this->makeNewsCategory('Tournament Updates');
        $group = $this->makeGroup('A');
        $city = $this->makeCity('Casablanca');
        $stadium = $this->makeStadium($city, 'Mohammed V Stadium');
        $homeTeam = $this->makeTeam($group, 'Morocco');
        $awayTeam = $this->makeTeam($group, 'Spain');
        $homePlayer = $this->makePlayer($homeTeam, 'Youssef En-Nesyri', ['shirt_number' => 9, 'position' => 'forward']);
        $awayPlayer = $this->makePlayer($awayTeam, 'Pedri Gonzalez', ['shirt_number' => 20, 'position' => 'midfielder']);
        $partner = Partner::query()->create([
            'name' => 'Atlas Sponsor',
            'slug' => 'atlas-sponsor',
            'category' => 'Global Sponsor',
            'tier' => 'Official',
            'website_url' => 'https://example.com',
            'status' => 'active',
        ]);

        News::query()->create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'Morocco Opens The Tournament',
            'slug' => 'morocco-opens-the-tournament',
            'summary' => 'Opening match summary.',
            'body' => 'Opening match body copy.',
            'status' => 'published',
            'visibility' => 'public',
            'published_at' => now()->subHour(),
        ]);

        $groupMatch = MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'GRP-A-01',
            'slug' => 'grp-a-01',
            'stage_type' => 'group',
            'match_date' => now()->subHour(),
            'timezone' => 'Africa/Casablanca',
            'status' => 'completed',
            'home_score' => 2,
            'away_score' => 1,
        ]);

        MatchEvent::query()->create([
            'match_id' => $groupMatch->id,
            'team_id' => $homeTeam->id,
            'player_id' => $homePlayer->id,
            'minute' => 14,
            'event_type' => 'goal',
            'period' => 'first_half',
            'description' => 'Opens the scoring.',
            'sort_order' => 14,
        ]);

        MatchStatistic::query()->create([
            'match_id' => $groupMatch->id,
            'team_id' => $homeTeam->id,
            'metric_key' => 'shots',
            'metric_value' => 9,
            'display_value' => '9',
            'context' => 'full_time',
        ]);

        MatchLineup::query()->create([
            'match_id' => $groupMatch->id,
            'team_id' => $homeTeam->id,
            'player_id' => $homePlayer->id,
            'lineup_type' => 'starting',
            'sort_order' => 1,
            'position' => 'ST',
            'shirt_number' => 9,
        ]);

        $quarterFinal = MatchFixture::query()->create([
            'code' => 'QF-01',
            'slug' => 'qf-01',
            'stage_type' => 'quarter_final',
            'match_date' => now()->addDay(),
            'status' => 'scheduled',
            'home_team_id' => null,
            'away_team_id' => null,
        ]);

        KnockoutProgression::query()->create([
            'source_match_id' => $groupMatch->id,
            'target_match_id' => $quarterFinal->id,
            'progression_type' => 'winner',
            'team_slot' => 'home',
        ]);

        app(StandingsRecalculationService::class)->recalculateGroup($group->fresh('teams'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Morocco Opens The Tournament')
            ->assertSee('Morocco');

        $this->get(route('matches.show', $groupMatch->slug))
            ->assertOk()
            ->assertSee('Opens the scoring.')
            ->assertSee('Youssef En-Nesyri');

        $this->get(route('standings.index'))
            ->assertOk()
            ->assertSee($group->name)
            ->assertSee($homeTeam->name);

        $this->get(route('standings.show', $group->code))
            ->assertOk()
            ->assertSee($awayTeam->name);

        $this->get(route('knockout.index'))
            ->assertOk()
            ->assertSee('Quarter-final')
            ->assertSee('Winner of GRP-A-01');

        $this->get(route('teams.show', $homeTeam->slug))
            ->assertOk()
            ->assertSee($homePlayer->display_name);

        $this->get(route('players.show', $homePlayer->slug))
            ->assertOk()
            ->assertSee($homeTeam->name);

        $this->get(route('cities.show', $city->slug))
            ->assertOk()
            ->assertSee($stadium->name);

        $this->get(route('stadiums.show', $stadium->slug))
            ->assertOk()
            ->assertSee($city->name);

        $this->get(route('partners.index'))
            ->assertOk()
            ->assertSee($partner->name);
    }

    public function test_knockout_page_renders_full_bracket_visual_structure(): void
    {
        $this->seed(FullTournamentStructureSeeder::class);

        $this->get(route('knockout.index'))
            ->assertOk()
            ->assertSee('knockout-shell--polished', false)
            ->assertSee('knockout-shell--strict', false)
            ->assertSee('knockout-shell--world-class', false)
            ->assertSee('knockout-shell--readable', false)
            ->assertSee('data-knockout-fit', false)
            ->assertSee('knockout-fit-canvas', false)
            ->assertSee('knockout-bracket', false)
            ->assertSee('knockout-bracket--symmetric', false)
            ->assertSee('knockout-bracket--fit', false)
            ->assertSee('knockout-bracket--world-class', false)
            ->assertSee('knockout-bracket--readable', false)
            ->assertSee('Left bracket path')
            ->assertSee('Right bracket path')
            ->assertSee('knockout-wing--left', false)
            ->assertSee('knockout-final-axis', false)
            ->assertSee('knockout-wing--right', false)
            ->assertSee('Road To The Final')
            ->assertSee('knockout-round--round-of-32', false)
            ->assertSee('knockout-round--round-of-16', false)
            ->assertSee('knockout-round--quarter-final', false)
            ->assertSee('knockout-round--semi-final', false)
            ->assertSee('knockout-match-card--final', false)
            ->assertSee('knockout-match-card--third', false)
            ->assertSee('knockout-match-card__node', false)
            ->assertSee('MOROCCO 2030 Final')
            ->assertSee('Third Place Play-off')
            ->assertSee('FT-FINAL')
            ->assertSee('FT-THIRD')
            ->assertSee('Winner of Group A')
            ->assertSee('Runner-up of Group B')
            ->assertSee('Third place of Group A')
            ->assertSee('Winner of FT-SF-01')
            ->assertSee('Winner of FT-SF-02');
    }

    public function test_match_detail_page_renders_scheduled_live_and_completed_match_centre_states(): void
    {
        $group = $this->makeGroup('L');
        $city = $this->makeCity('Rabat');
        $stadium = $this->makeStadium($city, 'Rabat Match Centre Stadium');
        $homeTeam = $this->makeTeam($group, 'Atlas Lions', ['code' => 'ATL']);
        $awayTeam = $this->makeTeam($group, 'Sahara Stars', ['code' => 'SAH']);
        $homeScorer = $this->makePlayer($homeTeam, 'Atlas Scorer', [
            'shirt_number' => 9,
            'position' => 'forward',
        ]);
        $homeBench = $this->makePlayer($homeTeam, 'Atlas Bench', [
            'shirt_number' => 19,
            'position' => 'midfielder',
        ]);
        $awayKeeper = $this->makePlayer($awayTeam, 'Sahara Keeper', [
            'shirt_number' => 1,
            'position' => 'goalkeeper',
        ]);

        $scheduled = MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'MCC-SCH',
            'slug' => 'mcc-scheduled',
            'stage_type' => 'group',
            'match_date' => now()->addDay(),
            'timezone' => 'Africa/Casablanca',
            'status' => 'scheduled',
        ]);

        $live = MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'MCC-LIV',
            'slug' => 'mcc-live',
            'stage_type' => 'group',
            'match_date' => now(),
            'timezone' => 'Africa/Casablanca',
            'status' => 'live',
            'home_score' => 1,
            'away_score' => 1,
        ]);

        $finished = MatchFixture::query()->create([
            'stadium_id' => $stadium->id,
            'city_id' => $city->id,
            'group_id' => $group->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => 'MCC-FIN',
            'slug' => 'mcc-finished',
            'stage_type' => 'group',
            'match_date' => now()->subDay(),
            'timezone' => 'Africa/Casablanca',
            'status' => 'completed',
            'home_score' => 2,
            'away_score' => 1,
        ]);

        MatchEvent::query()->create([
            'match_id' => $finished->id,
            'team_id' => $homeTeam->id,
            'player_id' => $homeScorer->id,
            'minute' => 28,
            'event_type' => 'goal',
            'period' => 'first_half',
            'description' => 'Opening goal.',
            'sort_order' => 28,
        ]);

        foreach ([
            [$homeTeam->id, 'possession_percentage', 58, '58%'],
            [$awayTeam->id, 'possession_percentage', 42, '42%'],
            [$homeTeam->id, 'shots', 12, '12'],
            [$awayTeam->id, 'shots', 8, '8'],
        ] as [$teamId, $metricKey, $metricValue, $displayValue]) {
            MatchStatistic::query()->create([
                'match_id' => $finished->id,
                'team_id' => $teamId,
                'metric_key' => $metricKey,
                'metric_value' => $metricValue,
                'display_value' => $displayValue,
                'context' => 'full_time',
            ]);
        }

        MatchLineup::query()->create([
            'match_id' => $finished->id,
            'team_id' => $homeTeam->id,
            'player_id' => $homeScorer->id,
            'lineup_type' => 'starting',
            'sort_order' => 1,
            'position' => 'ST',
            'shirt_number' => 9,
            'is_captain' => true,
        ]);

        MatchLineup::query()->create([
            'match_id' => $finished->id,
            'team_id' => $homeTeam->id,
            'player_id' => $homeBench->id,
            'lineup_type' => 'bench',
            'sort_order' => 12,
            'position' => 'CM',
            'shirt_number' => 19,
        ]);

        MatchLineup::query()->create([
            'match_id' => $finished->id,
            'team_id' => $awayTeam->id,
            'player_id' => $awayKeeper->id,
            'lineup_type' => 'starting',
            'sort_order' => 1,
            'position' => 'GK',
            'shirt_number' => 1,
            'is_goalkeeper' => true,
        ]);

        $this->get(route('matches.show', $scheduled->slug))
            ->assertOk()
            ->assertSee('match-centre-hero--scheduled', false)
            ->assertSee('Scheduled')
            ->assertSee('VS');

        $this->get(route('matches.show', $live->slug))
            ->assertOk()
            ->assertSee('match-centre-hero--live', false)
            ->assertSee('Live')
            ->assertSee('1 - 1');

        $this->get(route('matches.show', $finished->slug))
            ->assertOk()
            ->assertSee('match-centre-hero--completed', false)
            ->assertSee('2 - 1')
            ->assertSee('match-timeline__item--goal', false)
            ->assertSee('Atlas Scorer')
            ->assertSee('Opening goal.')
            ->assertSee('Possession')
            ->assertSee('58%')
            ->assertSee('42%')
            ->assertSee('Starting XI')
            ->assertSee('Bench')
            ->assertSee('#9')
            ->assertSee('Atlas Bench')
            ->assertSee('Sahara Keeper');
    }
}
