<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class VenueManagerDashboardTest extends TestCase
{
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_venue_manager_dashboard_returns_200_and_shows_relevant_widgets(): void
    {
        $html = $this->actingAs($this->demoUser('venue.manager@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Demo Venue Manager', false)
            ->assertSee('Cities', false)
            ->assertSee('Stadiums', false)
            ->assertSee('Content Assets Review', false)
            ->assertSee('Open Media Review', false)
            ->getContent();

        $this->assertStringNotContainsString('href="#"', $html);
        $this->assertStringNotContainsString(route('admin.football-data-import.index'), $html);
        $this->assertStringNotContainsString(route('admin.users.index'), $html);
        $this->assertStringNotContainsString(route('admin.news.index'), $html);
        $this->assertStringNotContainsString(route('admin.matches.index'), $html);
    }

    public function test_venue_manager_sidebar_contains_only_allowed_modules(): void
    {
        $html = $this->actingAs($this->demoUser('venue.manager@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $sidebarHrefs = $this->extractSidebarHrefs($html);

        $this->assertContains(route('admin.dashboard'), $sidebarHrefs);
        $this->assertContains(route('admin.cities.index'), $sidebarHrefs);
        $this->assertContains(route('admin.stadiums.index'), $sidebarHrefs);
        $this->assertContains(route('admin.media-files.index'), $sidebarHrefs);

        foreach ([
            'admin.users.index',
            'admin.news.index',
            'admin.teams.index',
            'admin.players.index',
            'admin.matches.index',
            'admin.groups.index',
            'admin.settings.index',
            'admin.audit-logs.index',
            'admin.football-data-import.index',
        ] as $routeName) {
            $this->assertNotContains(route($routeName), $sidebarHrefs, "Unexpected sidebar link for {$routeName}");
        }

        $this->assertStringContainsString('Media Review', $html);
        $this->assertStringNotContainsString('Football Data Import', $html);
    }
}
