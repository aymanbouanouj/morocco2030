<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class PublicLayoutOverlapTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_account_pages_render_public_shell_in_safe_document_order(): void
    {
        $user = $this->makePublicUser();

        foreach ($this->accountRoutes() as $routeName) {
            $response = $this->actingAs($user)->get(route($routeName));
            $response->assertOk();

            $html = $response->getContent();

            $header = strpos($html, '<header class="site-header"');
            $main = strpos($html, '<main id="main-content"');
            $hero = strpos($html, '<section class="page-header"');
            $accountLayout = strpos($html, '<section class="page-section account-layout"');
            $footer = strpos($html, '<footer class="m2030-footer-final"');

            $this->assertNotFalse($header, $routeName.' is missing the public header.');
            $this->assertNotFalse($main, $routeName.' is missing main content.');
            $this->assertNotFalse($hero, $routeName.' is missing the account hero.');
            $this->assertNotFalse($accountLayout, $routeName.' is missing account layout.');
            $this->assertNotFalse($footer, $routeName.' is missing footer.');

            $this->assertLessThan($main, $header, $routeName.' renders header after main.');
            $this->assertLessThan($hero, $main, $routeName.' renders hero before main.');
            $this->assertLessThan($accountLayout, $hero, $routeName.' renders account layout before hero.');
            $this->assertLessThan($footer, $accountLayout, $routeName.' renders footer before account content.');

            $this->assertTrue(str_contains($html, 'page-account'), $routeName.' is missing account shell class.');
            $this->assertSame(0, preg_match('/href=([\'"])#\1/', $html), $routeName.' contains an exact placeholder link.');
            $this->assertFalse(str_contains($html, route('admin.dashboard')), $routeName.' exposes an admin dashboard URL.');
            $this->assertFalse(str_contains($html, 'Admin Dashboard'), $routeName.' exposes an admin dashboard label.');
        }
    }

    public function test_account_pages_render_real_navigation_links_and_active_state(): void
    {
        $user = $this->makePublicUser();

        foreach ($this->accountRoutes() as $routeName) {
            $html = $this->actingAs($user)->get(route($routeName))->getContent();

            foreach ($this->accountRoutes() as $navRouteName) {
                $this->assertTrue(
                    str_contains($html, route($navRouteName)),
                    $routeName.' is missing account nav route '.$navRouteName
                );
            }

            $this->assertTrue(str_contains($html, 'aria-current="page"'), $routeName.' is missing active account nav state.');
        }
    }

    public function test_account_flash_renders_inside_main_before_hero(): void
    {
        $user = $this->makePublicUser();

        $html = $this->actingAs($user)
            ->withSession(['success' => 'Welcome back.'])
            ->get(route('account.index'))
            ->getContent();

        $main = strpos($html, '<main id="main-content"');
        $flash = strpos($html, '<div class="flash-stack flash-stack--account"');
        $hero = strpos($html, '<section class="page-header"');

        $this->assertNotFalse($main);
        $this->assertNotFalse($flash);
        $this->assertNotFalse($hero);
        $this->assertLessThan($flash, $main);
        $this->assertLessThan($hero, $flash);
        $this->assertTrue(str_contains($html, 'role="status"'));
    }

    public function test_public_tournament_pages_still_render_for_public_users(): void
    {
        $user = $this->makePublicUser();

        foreach ([route('teams.index'), route('matches.index'), route('standings.index')] as $route) {
            $this->actingAs($user)->get($route)->assertOk();
        }
    }

    /**
     * @return list<string>
     */
    private function accountRoutes(): array
    {
        return [
            'account.index',
            'account.profile',
            'account.settings',
            'account.favorites',
            'account.notifications',
        ];
    }
}
