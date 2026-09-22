<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class PublicAccountPageTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_public_account_overview_renders_real_account_controls_without_admin_links(): void
    {
        $user = $this->makePublicUser([], [
            'name' => 'Ayman Public Fan',
            'email' => 'ayman.public@example.com',
            'preferred_locale' => 'en',
        ]);

        $response = $this->actingAs($user)->get(route('account.index'));
        $response->assertOk();

        $html = $response->getContent();

        foreach ([
            'Ayman Public Fan',
            'ayman.public@example.com',
            'Public Account',
            'Public user',
            'Overview',
            'Profile',
            'Settings',
            'Favorites',
            'Notifications',
            route('account.profile'),
            route('account.settings'),
            'header-auth__account-name',
            'account-overview-card',
            'aria-current="page"',
        ] as $expected) {
            $this->assertTrue(str_contains($html, $expected), 'Missing account page fragment: '.$expected);
        }

        $this->assertFalse(str_contains($html, route('admin.dashboard')), 'Public account page exposes the admin dashboard URL.');
        $this->assertFalse(str_contains($html, 'Admin Dashboard'), 'Public account page exposes an admin dashboard label.');
        if (preg_match('/href=([\'"])#\1/', $html, $match, PREG_OFFSET_CAPTURE) === 1) {
            $this->fail('Public account page contains placeholder links near: '.substr($html, max(0, $match[0][1] - 80), 180));
        }
    }

    public function test_account_flash_messages_are_scoped_and_compact_for_public_account_pages(): void
    {
        $user = $this->makePublicUser();

        $this->actingAs($user)
            ->withSession(['success' => 'Welcome back.'])
            ->get(route('account.index'))
            ->assertOk()
            ->assertSee('Welcome back.')
            ->assertSee('flash-stack--account', false)
            ->assertSee('flash-banner--success', false)
            ->assertSee('role="status"', false);
    }

    public function test_account_routes_keep_public_tournament_pages_available(): void
    {
        $user = $this->makePublicUser();

        foreach ([route('teams.index'), route('matches.index'), route('standings.index')] as $route) {
            $this->actingAs($user)
                ->get($route)
                ->assertOk();
        }
    }

    public function test_public_user_can_open_each_account_section(): void
    {
        $user = $this->makePublicUser();

        foreach ([
            route('account.index'),
            route('account.profile'),
            route('account.settings'),
            route('account.favorites'),
            route('account.notifications'),
        ] as $route) {
            $this->actingAs($user)
                ->get($route)
                ->assertOk();
        }
    }
}
