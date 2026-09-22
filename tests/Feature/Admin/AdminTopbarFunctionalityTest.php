<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\DemoAccessControlSeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTopbarFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            LanguageSeeder::class,
            DemoAccessControlSeeder::class,
        ]);
    }

    public function test_admin_dashboard_does_not_render_ctrl_k(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $topbar = $this->extractTopbar($html);

        $this->assertStringNotContainsString('Ctrl+K', $topbar);
        $this->assertStringNotContainsString('Ctrl K', $topbar);
        $this->assertStringNotContainsString('<kbd', $topbar);
    }

    public function test_admin_dashboard_renders_search_input_and_dropdown_markup(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('id="admin-topbar-search-input"', false)
            ->assertSee('Search anything...', false)
            ->assertSee('id="admin-topbar-search-panel"', false)
            ->assertSee('id="admin-topbar-search-data"', false);
    }

    public function test_admin_dashboard_renders_notification_and_language_controls(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('id="admin-notifications-toggle"', false)
            ->assertSee('id="admin-notification-menu"', false)
            ->assertSee('id="admin-language-toggle"', false)
            ->assertSee('id="admin-language-menu"', false);
    }

    public function test_language_links_use_valid_language_switch_route(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $topbar = $this->extractTopbar($html);

        $this->assertStringContainsString(route('language.switch', 'en'), $topbar);
        $this->assertStringContainsString(route('language.switch', 'fr'), $topbar);
        $this->assertStringContainsString(route('language.switch', 'ar'), $topbar);
    }

    public function test_logout_uses_post_with_csrf_in_topbar(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $topbar = $this->extractTopbar($html);

        $this->assertStringContainsString('method="POST"', $topbar);
        $this->assertStringContainsString(route('admin.logout'), $topbar);
        $this->assertMatchesRegularExpression('/name="_token"/', $topbar);
    }

    public function test_topbar_has_no_fake_hash_links_on_real_actions(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();

        $topbar = $this->extractTopbar($html);

        $this->assertStringNotContainsString('href="#"', $topbar);
    }

    public function test_admin_dashboard_renders_avatar_menu_markup(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('id="admin-user-menu-toggle"', false)
            ->assertSee('id="admin-user-menu"', false);
    }

    private function demoUser(string $email): User
    {
        return User::query()->where('email', $email)->firstOrFail();
    }

    private function extractTopbar(string $html): string
    {
        if (preg_match('/<header[^>]*class="[^"]*admin-topbar[^"]*"[^>]*>.*?<\/header>/s', $html, $matches) !== 1) {
            $this->fail('Admin topbar markup was not found in the dashboard response.');
        }

        return $matches[0];
    }
}
