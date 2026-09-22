<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class MediaManagerMediaReviewTest extends TestCase
{
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_media_manager_can_access_dashboard(): void
    {
        $this->actingAs($this->demoUser('media.manager@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard', false)
            ->assertSee('Content Assets Review', false)
            ->assertSee('Open Media Review', false);
    }

    public function test_media_manager_sidebar_shows_dashboard_and_media_review_only(): void
    {
        $html = $this->actingAs($this->demoUser('media.manager@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $hrefs = $this->extractSidebarHrefs($html);

        $this->assertContains(route('admin.dashboard'), $hrefs);
        $this->assertContains(route('admin.media-files.index'), $hrefs);

        foreach ($this->forbiddenRoutes() as $routeName) {
            if (Route::has($routeName)) {
                $this->assertNotContains(route($routeName), $hrefs, "Forbidden sidebar link for {$routeName}");
            }
        }
    }

    public function test_media_manager_can_access_media_review_routes(): void
    {
        $user = $this->demoUser('media.manager@morocco2030.test');

        $this->actingAs($user)
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->assertSee('Media Review', false);

        $this->actingAs($user)
            ->get(route('admin.media-readiness.index'))
            ->assertOk()
            ->assertSee('Media Readiness', false);
    }

    public function test_media_manager_cannot_access_forbidden_modules(): void
    {
        $user = $this->demoUser('media.manager@morocco2030.test');

        foreach ($this->forbiddenRoutes() as $routeName) {
            if (Route::has($routeName)) {
                $this->actingAs($user)->get(route($routeName))->assertForbidden();
            }
        }
    }

    public function test_media_review_has_no_generic_upload_or_fake_links_for_media_manager(): void
    {
        $html = $this->actingAs($this->demoUser('media.manager@morocco2030.test'))
            ->get(route('admin.media-files.index'))
            ->assertOk()
            ->assertDontSee('Upload Image', false)
            ->assertDontSee('Upload Media', false)
            ->assertDontSee(route('admin.media-files.create'), false)
            ->getContent();

        $this->assertStringNotContainsString('href="#"', $html);
    }

    /**
     * @return list<string>
     */
    private function forbiddenRoutes(): array
    {
        return [
            'admin.users.index',
            'admin.partners.index',
            'admin.football-data-import.index',
            'admin.teams.index',
            'admin.players.index',
            'admin.matches.index',
            'admin.standings.index',
            'admin.settings.index',
            'admin.audit-logs.index',
        ];
    }
}
