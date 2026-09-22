<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\DemoAccessControlSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNavigationVisibilityByRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            DemoAccessControlSeeder::class,
        ]);
    }

    public function test_super_admin_sees_full_admin_navigation(): void
    {
        $response = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.dashboard'));

        $response->assertOk();

        foreach ([
            'admin.dashboard',
            'admin.users.index',
            'admin.news.index',
            'admin.news-categories.index',
            'admin.teams.index',
            'admin.players.index',
            'admin.matches.index',
            'admin.groups.index',
            'admin.cities.index',
            'admin.stadiums.index',
            'admin.partners.index',
            'admin.media-files.index',
            'admin.interface-translations.index',
            'admin.audit-logs.index',
            'admin.contact-messages.index',
            'admin.settings.index',
        ] as $routeName) {
            $response->assertSee(route($routeName), false);
        }

        $response->assertSee('#admin-analytics', false);
    }

    public function test_journalist_sees_news_but_not_identity_or_settings_navigation(): void
    {
        $response = $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee(route('admin.news.index'), false)
            ->assertDontSee(route('admin.news-categories.index'), false)
            ->assertDontSee(route('admin.users.index'), false)
            ->assertDontSee(route('admin.settings.index'), false)
            ->assertDontSee(route('admin.matches.index'), false);
    }

    public function test_chief_editor_sees_editorial_modules_not_sports_or_identity(): void
    {
        $response = $this->actingAs($this->demoUser('chief.editor@morocco2030.test'))
            ->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee(route('admin.news.index'), false)
            ->assertSee(route('admin.news-categories.index'), false)
            ->assertDontSee(route('admin.audit-logs.index'), false)
            ->assertDontSee(route('admin.users.index'), false)
            ->assertDontSee(route('admin.matches.index'), false)
            ->assertDontSee(route('admin.settings.index'), false);
    }

    public function test_match_manager_sees_matches_and_standings_not_news_or_users(): void
    {
        $response = $this->actingAs($this->demoUser('match.manager@morocco2030.test'))
            ->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee(route('admin.matches.index'), false)
            ->assertSee(route('admin.groups.index'), false)
            ->assertDontSee(route('admin.users.index'), false)
            ->assertDontSee(route('admin.news.index'), false)
            ->assertDontSee(route('admin.settings.index'), false);
    }

    public function test_venue_manager_sees_cities_and_stadiums_not_matches_or_users(): void
    {
        $response = $this->actingAs($this->demoUser('venue.manager@morocco2030.test'))
            ->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee(route('admin.cities.index'), false)
            ->assertSee(route('admin.stadiums.index'), false)
            ->assertDontSee(route('admin.matches.index'), false)
            ->assertDontSee(route('admin.users.index'), false)
            ->assertDontSee(route('admin.teams.index'), false);
    }

    public function test_media_manager_sees_media_not_users_or_settings(): void
    {
        $response = $this->actingAs($this->demoUser('media.manager@morocco2030.test'))
            ->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee(route('admin.media-files.index'), false)
            ->assertDontSee(route('admin.users.index'), false)
            ->assertDontSee(route('admin.settings.index'), false);
    }

    public function test_translator_sees_translations_not_matches_or_users(): void
    {
        $response = $this->actingAs($this->demoUser('translator@morocco2030.test'))
            ->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee(route('admin.interface-translations.index'), false)
            ->assertDontSee(route('admin.matches.index'), false)
            ->assertDontSee(route('admin.users.index'), false);
    }

    public function test_analyst_sees_analytics_and_audit_logs_not_crud_modules(): void
    {
        $response = $this->actingAs($this->demoUser('analyst@morocco2030.test'))
            ->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('#admin-analytics', false)
            ->assertSee(route('admin.audit-logs.index'), false)
            ->assertDontSee(route('admin.users.index'), false)
            ->assertDontSee(route('admin.news.index'), false)
            ->assertDontSee(route('admin.teams.index'), false)
            ->assertDontSee(route('admin.settings.index'), false);
    }

    public function test_public_demo_user_cannot_access_admin_dashboard(): void
    {
        $this->actingAs($this->demoUser('public.user@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    private function demoUser(string $email): User
    {
        return User::query()->where('email', $email)->firstOrFail();
    }
}
