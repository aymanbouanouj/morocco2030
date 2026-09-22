<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class AdminUsersTableUiTest extends TestCase
{
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_users_index_renders_for_super_admin(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_users_index_shows_staff_and_public_tabs(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index'))
            ->assertSee('Staff Accounts', false)
            ->assertSee('Public Audience', false);
    }

    public function test_super_admin_sees_create_staff_user_button(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'staff']))
            ->assertSee('Create Staff User', false);
    }

    public function test_users_table_renders_inline_action_buttons(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'staff']))
            ->assertOk()
            ->getContent();

        $module = $this->extractUsersModule($html);

        $this->assertStringContainsString('admin-users-action-btn', $module);
        $this->assertStringContainsString('View', $module);
        $this->assertStringContainsString('Edit', $module);
        $this->assertStringContainsString('Archive', $module);
        $this->assertStringNotContainsString('admin-users-actions-menu', $module);
    }

    public function test_users_table_has_sidebar_fit_wrapper_and_sticky_action_columns(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'staff']))
            ->assertOk()
            ->getContent();

        $module = $this->extractUsersModule($html);

        $this->assertStringContainsString('admin-users-table-wrap--fit', $module);
        $this->assertStringContainsString('admin-users-table--fit', $module);
        $this->assertStringContainsString('admin-users-sticky-col--actions', $module);
        $this->assertStringContainsString('admin-users-sticky-col--login', $module);
    }

    public function test_users_actions_do_not_use_fake_hash_links(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index'))
            ->assertOk()
            ->getContent();

        $module = $this->extractUsersModule($html);

        $this->assertStringNotContainsString('href="#"', $module);
    }

    public function test_public_tab_has_no_role_assignment_controls(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'public']))
            ->assertOk()
            ->getContent();

        $module = $this->extractUsersModule($html);

        $this->assertStringNotContainsString('name="role_ids[]"', $module);
        $this->assertStringNotContainsString('admin-users-badge--role', $module);
    }

    public function test_staff_tab_excludes_public_demo_user(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'staff']))
            ->assertDontSee('public.user@morocco2030.test', false);
    }

    public function test_public_tab_shows_public_demo_user(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'public']))
            ->assertSee('public.user@morocco2030.test', false);
    }

    private function extractUsersModule(string $html): string
    {
        if (preg_match('/<section class="panel admin-users-page.*?<\/section>/s', $html, $matches) !== 1) {
            $this->fail('Admin users module markup was not found.');
        }

        return $matches[0];
    }
}
