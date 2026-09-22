<?php

namespace Tests\Feature\Public;

use App\Models\Group;
use App\Models\MatchFixture;
use App\Models\News;
use App\Models\Partner;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ShowcaseTournamentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowcaseTournamentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_showcase_tournament_seeder_populates_public_pages_with_realistic_demo_data(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            LanguageSeeder::class,
            ShowcaseTournamentSeeder::class,
        ]);

        $this->assertGreaterThanOrEqual(4, Group::query()->count());
        $this->assertGreaterThanOrEqual(16, Team::query()->count());
        $this->assertGreaterThanOrEqual(100, Player::query()->count());
        $this->assertGreaterThanOrEqual(5, News::query()->count());
        $this->assertGreaterThanOrEqual(5, Partner::query()->count());
        $this->assertGreaterThanOrEqual(30, MatchFixture::query()->count());

        $semiFinalOne = MatchFixture::query()->where('code', 'M2030-SF1')->firstOrFail();
        $semiFinalTwo = MatchFixture::query()->where('code', 'M2030-SF2')->firstOrFail();

        $this->assertNotNull($semiFinalOne->home_team_id);
        $this->assertNotNull($semiFinalOne->away_team_id);
        $this->assertNull($semiFinalTwo->home_team_id);
        $this->assertNull($semiFinalTwo->away_team_id);

        $latestPublicNewsTitle = News::query()
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->orderByDesc('published_at')
            ->latest('id')
            ->value('title');

        $featuredUpcomingMatch = MatchFixture::query()
            ->whereIn('status', ['scheduled', 'live', 'postponed'])
            ->orderBy('match_date')
            ->firstOrFail();

        $homeResponse = $this->get(route('home'))
            ->assertOk()
            ->assertSee($latestPublicNewsTitle)
            ->assertSee($featuredUpcomingMatch->slotLabel('home'))
            ->assertSee('Host Map Preview');

        $homePartnerSection = $this->extractHomePartnerSection($homeResponse->getContent());
        $homepagePartners = Partner::query()
            ->publiclyVisible()
            ->publicOrder()
            ->pluck('name');

        $homepagePartners
            ->take(4)
            ->each(fn (string $partnerName) => $this->assertStringContainsString($partnerName, $homePartnerSection));

        $homepagePartners
            ->skip(4)
            ->each(fn (string $partnerName) => $this->assertStringNotContainsString($partnerName, $homePartnerSection));

        $this->get(route('partners.index'))
            ->assertOk()
            ->assertSee('Royal Air Maroc');

        $this->get(route('standings.index'))
            ->assertOk()
            ->assertSee('Group A')
            ->assertSee('Group D');

        $this->get(route('knockout.index'))
            ->assertOk()
            ->assertSee('Semi-final')
            ->assertSee('Winner of M2030-QF3');
    }

    public function test_seeded_showcase_records_remain_usable_inside_admin_modules(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            LanguageSeeder::class,
            ShowcaseTournamentSeeder::class,
        ]);

        $competitionManager = User::query()
            ->where('email', 'karim.alaoui@morocco2030-showcase.test')
            ->firstOrFail();

        $chiefEditor = User::query()
            ->where('email', 'sara.bennani@morocco2030-showcase.test')
            ->firstOrFail();

        $quarterFinal = MatchFixture::query()
            ->where('code', 'M2030-QF1')
            ->firstOrFail();

        $this->actingAs($competitionManager)
            ->get('/admin/teams')
            ->assertOk()
            ->assertSee('Morocco');

        $this->actingAs($competitionManager)
            ->get("/admin/matches/{$quarterFinal->id}")
            ->assertOk()
            ->assertSee('M2030-QF1');

        $this->actingAs($chiefEditor)
            ->get('/admin/news')
            ->assertOk()
            ->assertSee('Morocco 2030 Host Network Moves Into Match Operations Mode');
    }

    private function extractHomePartnerSection(string $html): string
    {
        if (preg_match('/<section class="home-final-partners home-final-card">.*?<\/section>/s', $html, $matches) !== 1) {
            $this->fail('Homepage partner preview section was not found.');
        }

        return $matches[0];
    }
}
