<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\DemoAccessControlSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModuleAuthorizationByRoleTest extends TestCase
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

    public function test_super_admin_can_access_identity_and_sports_modules(): void
    {
        $user = $this->demoUser('superadmin@morocco2030.test');

        $this->actingAs($user)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.matches.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.news.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.settings.index'))->assertOk();
    }

    public function test_platform_admin_can_access_operations_but_not_user_management(): void
    {
        $user = $this->demoUser('platform.admin@morocco2030.test');

        $this->actingAs($user)->get(route('admin.matches.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.news.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.roles.index'))->assertForbidden();
    }

    public function test_journalist_can_access_news_but_not_users_or_matches(): void
    {
        $user = $this->demoUser('journalist@morocco2030.test');

        $this->actingAs($user)->get(route('admin.news.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.news-categories.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.matches.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.settings.index'))->assertForbidden();
    }

    public function test_chief_editor_can_manage_news_categories_but_not_matches(): void
    {
        $user = $this->demoUser('chief.editor@morocco2030.test');

        $this->actingAs($user)->get(route('admin.news-categories.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.audit-logs.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.matches.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_match_manager_can_access_matches_and_groups_not_news_or_users(): void
    {
        $user = $this->demoUser('match.manager@morocco2030.test');

        $this->actingAs($user)->get(route('admin.matches.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.groups.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_venue_manager_can_access_cities_and_stadiums_not_matches(): void
    {
        $user = $this->demoUser('venue.manager@morocco2030.test');

        $this->actingAs($user)->get(route('admin.cities.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.stadiums.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.matches.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_media_manager_can_access_media_not_settings(): void
    {
        $user = $this->demoUser('media.manager@morocco2030.test');

        $this->actingAs($user)->get(route('admin.media-files.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.media-readiness.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.settings.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_translator_can_access_translations_not_matches(): void
    {
        $user = $this->demoUser('translator@morocco2030.test');

        $this->actingAs($user)->get(route('admin.interface-translations.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.languages.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.matches.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_analyst_can_view_audit_logs_but_not_mutate_news(): void
    {
        $user = $this->demoUser('analyst@morocco2030.test');

        $this->actingAs($user)->get(route('admin.audit-logs.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.matches.index'))->assertForbidden();
    }

    public function test_public_demo_user_is_forbidden_from_admin_modules(): void
    {
        $user = $this->demoUser('public.user@morocco2030.test');

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
    }

    private function demoUser(string $email): User
    {
        return User::query()->where('email', $email)->firstOrFail();
    }
}
