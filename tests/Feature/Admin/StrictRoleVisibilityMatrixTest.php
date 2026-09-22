<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\DemoAccessControlSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StrictRoleVisibilityMatrixTest extends TestCase
{
    use RefreshDatabase;

    private const ALL_INDEX_ROUTES = [
        'admin.users.index',
        'admin.roles.index',
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
        'admin.media-readiness.index',
        'admin.interface-translations.index',
        'admin.languages.index',
        'admin.audit-logs.index',
        'admin.settings.index',
        'admin.contact-messages.index',
    ];

    /** Routes that appear as sidebar links in `admin.partials.sidebar`. */
    private const SIDEBAR_NAV_ROUTES = [
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
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            DemoAccessControlSeeder::class,
        ]);
    }

    public function test_demo_users_exist_after_seeder(): void
    {
        foreach (DemoAccessControlSeeder::DEMO_USERS as $definition) {
            $this->assertNotNull(User::query()->where('email', $definition['email'])->first());
        }
    }

    public function test_demo_access_seeder_is_idempotent(): void
    {
        $this->seed(DemoAccessControlSeeder::class);

        $emails = collect(DemoAccessControlSeeder::DEMO_USERS)->pluck('email');

        $this->assertSame($emails->count(), User::query()->whereIn('email', $emails)->count());
    }

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_public_user_is_forbidden_from_admin_dashboard(): void
    {
        $this->actingAs($this->demoUser('public.user@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_super_admin_can_access_every_admin_index_module(): void
    {
        $user = $this->demoUser('superadmin@morocco2030.test');

        foreach (self::ALL_INDEX_ROUTES as $routeName) {
            $this->actingAs($user)->get(route($routeName))->assertOk();
        }
    }

    #[DataProvider('strictRoleMatrixProvider')]
    public function test_role_sidebar_visibility_matches_matrix(
        string $email,
        array $visibleRoutes,
        array $hiddenRoutes,
        bool $expectsAnalyticsNav,
    ): void {
        $response = $this->actingAs($this->demoUser($email))->get(route('admin.dashboard'));

        $response->assertOk();

        $sidebarHrefs = $this->extractSidebarHrefs($response->getContent());
        $visibleNavRoutes = $this->navRoutesFromAllowed($visibleRoutes);

        foreach ($visibleNavRoutes as $routeName) {
            $this->assertContains(
                route($routeName),
                $sidebarHrefs,
                "Expected sidebar link for {$routeName}"
            );
        }

        foreach ($this->navRoutesFromAllowed($hiddenRoutes) as $routeName) {
            $this->assertNotContains(
                route($routeName),
                $sidebarHrefs,
                "Forbidden sidebar link for {$routeName}"
            );
        }

        if ($expectsAnalyticsNav) {
            $this->assertContains(route('admin.dashboard').'#admin-analytics', $sidebarHrefs);
        } else {
            $this->assertNotContains(route('admin.dashboard').'#admin-analytics', $sidebarHrefs);
        }
    }

    #[DataProvider('strictRoleMatrixProvider')]
    public function test_role_direct_url_access_matches_matrix(
        string $email,
        array $visibleRoutes,
        array $hiddenRoutes,
        bool $expectsAnalyticsNav,
    ): void {
        unset($expectsAnalyticsNav);
        $user = $this->demoUser($email);

        foreach ($visibleRoutes as $routeName) {
            $this->actingAs($user)->get(route($routeName))->assertOk();
        }

        foreach ($hiddenRoutes as $routeName) {
            $this->actingAs($user)->get(route($routeName))->assertForbidden();
        }
    }

    public static function strictRoleMatrixProvider(): array
    {
        $all = self::ALL_INDEX_ROUTES;

        $mediaRoutes = ['admin.media-files.index', 'admin.media-readiness.index'];

        $platformAllowed = [
            'admin.news.index',
            'admin.news-categories.index',
            'admin.teams.index',
            'admin.players.index',
            'admin.matches.index',
            'admin.groups.index',
            'admin.cities.index',
            'admin.stadiums.index',
            'admin.partners.index',
            ...$mediaRoutes,
            'admin.interface-translations.index',
            'admin.languages.index',
            'admin.audit-logs.index',
            'admin.settings.index',
            'admin.contact-messages.index',
        ];

        $platformHidden = [
            'admin.users.index',
            'admin.roles.index',
        ];

        $chiefEditorAllowed = [
            'admin.news.index',
            'admin.news-categories.index',
            ...$mediaRoutes,
        ];

        $chiefEditorHidden = array_values(array_diff($all, $chiefEditorAllowed));

        $journalistAllowed = [
            'admin.news.index',
            ...$mediaRoutes,
        ];

        $journalistHidden = array_values(array_diff($all, $journalistAllowed));

        $matchManagerAllowed = [
            'admin.matches.index',
            'admin.groups.index',
        ];

        $matchManagerHidden = array_values(array_diff($all, $matchManagerAllowed));

        $teamManagerAllowed = [
            'admin.teams.index',
            'admin.players.index',
            ...$mediaRoutes,
        ];

        $teamManagerHidden = array_values(array_diff($all, $teamManagerAllowed));

        $venueManagerAllowed = [
            'admin.cities.index',
            'admin.stadiums.index',
            ...$mediaRoutes,
        ];

        $venueManagerHidden = array_values(array_diff($all, $venueManagerAllowed));

        $sponsorManagerAllowed = [
            'admin.partners.index',
            ...$mediaRoutes,
        ];

        $sponsorManagerHidden = array_values(array_diff($all, $sponsorManagerAllowed));

        $mediaManagerAllowed = $mediaRoutes;

        $mediaManagerHidden = array_values(array_diff($all, $mediaManagerAllowed));

        $translatorAllowed = [
            'admin.interface-translations.index',
            'admin.languages.index',
        ];

        $translatorHidden = array_values(array_diff($all, $translatorAllowed));

        $analystAllowed = [
            'admin.audit-logs.index',
        ];

        $analystHidden = array_values(array_diff($all, $analystAllowed));

        return [
            'platform-admin' => ['platform.admin@morocco2030.test', $platformAllowed, $platformHidden, true],
            'chief-editor' => ['chief.editor@morocco2030.test', $chiefEditorAllowed, $chiefEditorHidden, false],
            'journalist' => ['journalist@morocco2030.test', $journalistAllowed, $journalistHidden, false],
            'match-manager' => ['match.manager@morocco2030.test', $matchManagerAllowed, $matchManagerHidden, false],
            'team-manager' => ['team.manager@morocco2030.test', $teamManagerAllowed, $teamManagerHidden, false],
            'venue-manager' => ['venue.manager@morocco2030.test', $venueManagerAllowed, $venueManagerHidden, false],
            'sponsor-manager' => ['sponsor.manager@morocco2030.test', $sponsorManagerAllowed, $sponsorManagerHidden, false],
            'media-manager' => ['media.manager@morocco2030.test', $mediaManagerAllowed, $mediaManagerHidden, false],
            'translator' => ['translator@morocco2030.test', $translatorAllowed, $translatorHidden, false],
            'analyst' => ['analyst@morocco2030.test', $analystAllowed, $analystHidden, true],
        ];
    }

    /**
     * @param  list<string>  $allowedIndexRoutes
     * @return list<string>
     */
    private static function navRoutesFromAllowed(array $allowedIndexRoutes): array
    {
        return array_values(array_intersect(self::SIDEBAR_NAV_ROUTES, $allowedIndexRoutes));
    }

    private function demoUser(string $email): User
    {
        return User::query()->where('email', $email)->firstOrFail();
    }

    /**
     * @return list<string>
     */
    private function extractSidebarHrefs(string $html): array
    {
        if (preg_match('/<aside id="admin-sidebar"[^>]*>(.*)<\/aside>/s', $html, $matches) !== 1) {
            $this->fail('Admin sidebar markup was not found in the dashboard response.');
        }

        preg_match_all('/href="([^"]+)"/', $matches[1], $hrefMatches);

        return $hrefMatches[1] ?? [];
    }
}
