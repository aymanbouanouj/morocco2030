<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class AdminDirectRouteAuthorizationContractTest extends TestCase
{
    use InteractsWithDemoAccessContract, RefreshDatabase;

    /**
     * @var array<string, string>
     */
    private const MODULE_INDEX_ROUTES = [
        'users' => 'admin.users.index',
        'roles' => 'admin.roles.index',
        'news' => 'admin.news.index',
        'news-categories' => 'admin.news-categories.index',
        'teams' => 'admin.teams.index',
        'players' => 'admin.players.index',
        'matches' => 'admin.matches.index',
        'standings' => 'admin.groups.index',
        'cities' => 'admin.cities.index',
        'stadiums' => 'admin.stadiums.index',
        'partners' => 'admin.partners.index',
        'media' => 'admin.media-files.index',
        'translations' => 'admin.interface-translations.index',
        'audit-logs' => 'admin.audit-logs.index',
        'contact-messages' => 'admin.contact-messages.index',
        'settings' => 'admin.settings.index',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDemoAccessFoundation();
    }

    public function test_super_admin_can_access_all_module_index_routes(): void
    {
        $user = $this->demoUser('superadmin@morocco2030.test');

        foreach (self::MODULE_INDEX_ROUTES as $routeName) {
            if (! $this->routeExists($routeName)) {
                continue;
            }

            $this->actingAs($user)->get(route($routeName))->assertOk();
        }
    }

    public function test_platform_admin_cannot_access_identity_modules(): void
    {
        $user = $this->demoUser('platform.admin@morocco2030.test');

        foreach (['admin.users.index', 'admin.roles.index'] as $routeName) {
            if (! $this->routeExists($routeName)) {
                continue;
            }

            $this->actingAs($user)->get(route($routeName))->assertForbidden();
        }
    }

    public function test_chief_editor_can_access_news_and_categories_but_not_audit_or_settings(): void
    {
        $user = $this->demoUser('chief.editor@morocco2030.test');

        $this->actingAs($user)->get(route('admin.news.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.news-categories.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.audit-logs.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.settings.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.matches.index'))->assertForbidden();
    }

    public function test_journalist_can_access_news_but_not_categories_or_publish_sensitive_modules(): void
    {
        $user = $this->demoUser('journalist@morocco2030.test');

        $this->actingAs($user)->get(route('admin.news.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.news-categories.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.audit-logs.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.settings.index'))->assertForbidden();
    }

    public function test_match_manager_can_access_matches_and_standings_only(): void
    {
        $user = $this->demoUser('match.manager@morocco2030.test');

        $this->actingAs($user)->get(route('admin.matches.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.groups.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.audit-logs.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_team_manager_can_access_teams_and_players_not_matches(): void
    {
        $user = $this->demoUser('team.manager@morocco2030.test');

        $this->actingAs($user)->get(route('admin.teams.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.players.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.matches.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.audit-logs.index'))->assertForbidden();
    }

    public function test_venue_manager_can_access_cities_and_stadiums_not_teams(): void
    {
        $user = $this->demoUser('venue.manager@morocco2030.test');

        $this->actingAs($user)->get(route('admin.cities.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.stadiums.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.teams.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.matches.index'))->assertForbidden();
    }

    public function test_sponsor_manager_can_access_partners_not_news(): void
    {
        $user = $this->demoUser('sponsor.manager@morocco2030.test');

        $this->actingAs($user)->get(route('admin.partners.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.matches.index'))->assertForbidden();
    }

    public function test_media_manager_can_access_media_not_news_or_audit(): void
    {
        $user = $this->demoUser('media.manager@morocco2030.test');

        $this->actingAs($user)->get(route('admin.media-files.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.audit-logs.index'))->assertForbidden();
    }

    public function test_translator_can_access_translations_not_news_or_media(): void
    {
        $user = $this->demoUser('translator@morocco2030.test');

        $this->actingAs($user)->get(route('admin.interface-translations.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.media-files.index'))->assertForbidden();
    }

    public function test_analyst_can_access_audit_logs_but_not_crud_modules(): void
    {
        $user = $this->demoUser('analyst@morocco2030.test');

        $this->actingAs($user)->get(route('admin.audit-logs.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.matches.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.media-files.index'))->assertForbidden();
    }

    public function test_support_agent_can_access_contact_messages_not_news(): void
    {
        $user = $this->demoUser('support.agent@morocco2030.test');

        if (! $this->routeExists('admin.contact-messages.index')) {
            $this->markTestSkipped('Contact messages module route is not registered.');
        }

        $this->actingAs($user)->get(route('admin.contact-messages.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.audit-logs.index'))->assertForbidden();
    }

    public function test_public_user_is_forbidden_from_admin_dashboard_and_modules(): void
    {
        $user = $this->demoUser('public.user@morocco2030.test');

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
    }

    #[DataProvider('auditRouteForbiddenRolesProvider')]
    public function test_unauthorized_roles_receive_forbidden_on_audit_logs_route(string $email): void
    {
        $this->actingAs($this->demoUser($email))
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();
    }

    public static function auditRouteForbiddenRolesProvider(): array
    {
        return [
            'chief-editor' => ['chief.editor@morocco2030.test'],
            'journalist' => ['journalist@morocco2030.test'],
            'match-manager' => ['match.manager@morocco2030.test'],
            'support-agent' => ['support.agent@morocco2030.test'],
        ];
    }
}
