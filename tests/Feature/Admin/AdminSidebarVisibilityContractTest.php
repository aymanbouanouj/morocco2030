<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class AdminSidebarVisibilityContractTest extends TestCase
{
    use InteractsWithDemoAccessContract, RefreshDatabase;

    private const SIDEBAR_ROUTES = [
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
        $this->seedDemoAccessFoundation();
    }

    #[DataProvider('sidebarContractProvider')]
    public function test_sidebar_links_match_access_contract(
        string $email,
        array $visibleRoutes,
        array $hiddenRoutes,
        bool $expectsAnalyticsNav,
    ): void {
        $response = $this->actingAs($this->demoUser($email))->get(route('admin.dashboard'));
        $response->assertOk();

        $hrefs = $this->extractSidebarHrefs($response->getContent());

        foreach ($visibleRoutes as $routeName) {
            if (! $this->routeExists($routeName)) {
                continue;
            }

            $this->assertContains(route($routeName), $hrefs, "Expected {$routeName}");
        }

        foreach ($hiddenRoutes as $routeName) {
            if (! $this->routeExists($routeName)) {
                continue;
            }

            $this->assertNotContains(route($routeName), $hrefs, "Forbidden {$routeName}");
        }

        $analyticsHref = route('admin.dashboard').'#admin-analytics';

        if ($expectsAnalyticsNav) {
            $this->assertContains($analyticsHref, $hrefs);
        } else {
            $this->assertNotContains($analyticsHref, $hrefs);
        }
    }

    public static function sidebarContractProvider(): array
    {
        $identity = ['admin.users.index'];
        $audit = ['admin.audit-logs.index'];
        $settings = ['admin.settings.index'];
        $contact = ['admin.contact-messages.index'];
        $news = ['admin.news.index'];
        $categories = ['admin.news-categories.index'];
        $sports = ['admin.teams.index', 'admin.players.index', 'admin.matches.index', 'admin.groups.index'];
        $venues = ['admin.cities.index', 'admin.stadiums.index'];
        $partners = ['admin.partners.index'];
        $media = ['admin.media-files.index'];
        $translations = ['admin.interface-translations.index'];

        return [
            'super-admin' => [
                'superadmin@morocco2030.test',
                array_merge($news, $categories, $sports, $venues, $partners, $media, $translations, $audit, $contact, $settings, $identity),
                [],
                true,
            ],
            'platform-admin' => [
                'platform.admin@morocco2030.test',
                array_merge($news, $categories, $sports, $venues, $partners, $media, $translations, $audit, $contact, $settings),
                $identity,
                true,
            ],
            'chief-editor' => [
                'chief.editor@morocco2030.test',
                array_merge($news, $categories, $media),
                array_merge($identity, $audit, $settings, $contact, $sports, $venues, $partners, $translations),
                false,
            ],
            'journalist' => [
                'journalist@morocco2030.test',
                array_merge($news, $media),
                array_merge($identity, $categories, $audit, $settings, $contact, $sports, $venues, $partners, $translations),
                false,
            ],
            'match-manager' => [
                'match.manager@morocco2030.test',
                ['admin.matches.index', 'admin.groups.index'],
                array_merge($identity, $news, $categories, $audit, $settings, $contact, $venues, $partners, $media, $translations, ['admin.teams.index', 'admin.players.index']),
                false,
            ],
            'team-manager' => [
                'team.manager@morocco2030.test',
                ['admin.teams.index', 'admin.players.index', 'admin.media-files.index'],
                array_merge($identity, $news, $categories, $audit, $settings, $contact, $venues, $partners, $translations, ['admin.matches.index', 'admin.groups.index']),
                false,
            ],
            'venue-manager' => [
                'venue.manager@morocco2030.test',
                array_merge($venues, $media),
                array_merge($identity, $news, $categories, $audit, $settings, $contact, $partners, $translations, $sports),
                false,
            ],
            'sponsor-manager' => [
                'sponsor.manager@morocco2030.test',
                array_merge($partners, $media),
                array_merge($identity, $news, $categories, $audit, $settings, $contact, $venues, $translations, $sports),
                false,
            ],
            'media-manager' => [
                'media.manager@morocco2030.test',
                $media,
                array_merge($identity, $news, $categories, $audit, $settings, $contact, $venues, $partners, $translations, $sports),
                false,
            ],
            'translator' => [
                'translator@morocco2030.test',
                $translations,
                array_merge($identity, $news, $categories, $audit, $settings, $contact, $venues, $partners, $media, $sports),
                false,
            ],
            'analyst' => [
                'analyst@morocco2030.test',
                $audit,
                array_merge($identity, $news, $categories, $settings, $contact, $venues, $partners, $media, $translations, $sports),
                true,
            ],
            'support-agent' => [
                'support.agent@morocco2030.test',
                $contact,
                array_merge($identity, $news, $categories, $audit, $settings, $venues, $partners, $media, $translations, $sports),
                false,
            ],
        ];
    }
}
