<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class AdminUsersFinalHardeningTest extends TestCase
{
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    private string $demoPassword = 'Morocco2030-Local-Demo-Only';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_users_index_shows_staff_and_public_tabs(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Staff Accounts', false)
            ->assertSee('Public Audience', false);
    }

    public function test_default_index_shows_staff_accounts_tab(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('superadmin@morocco2030.test', false)
            ->assertDontSee('public.user@morocco2030.test', false);
    }

    public function test_staff_tab_includes_super_admin_and_support_agent(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'staff']))
            ->assertOk()
            ->assertSee('superadmin@morocco2030.test', false)
            ->assertSee('support.agent@morocco2030.test', false)
            ->assertDontSee('public.user@morocco2030.test', false);
    }

    public function test_public_tab_shows_only_public_audience(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'public']))
            ->assertOk()
            ->assertSee('public.user@morocco2030.test', false)
            ->assertDontSee('superadmin@morocco2030.test', false)
            ->assertDontSee('support.agent@morocco2030.test', false);
    }

    public function test_public_tab_shows_no_admin_access_badge(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'public']))
            ->assertOk()
            ->assertSee('No admin access', false);
    }

    public function test_public_tab_has_no_role_assignment_markup(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'public']))
            ->assertOk()
            ->getContent();

        $module = $this->extractUsersModule($html);

        $this->assertStringNotContainsString('name="role_ids[]"', $module);
        $this->assertStringNotContainsString('admin-users-badge--role', $module);
    }

    public function test_super_admin_sees_create_staff_user_on_staff_tab(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'staff']))
            ->assertOk()
            ->assertSee('Create Staff User', false);
    }

    public function test_super_admin_can_open_create_staff_form(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Create Staff User', false);
    }

    public function test_super_admin_can_create_staff_user(): void
    {
        $email = 'phase2.staff.create@morocco2030.test';

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.users.store'), [
                'name' => 'Phase 2 Staff',
                'email' => $email,
                'password' => $this->demoPassword,
                'password_confirmation' => $this->demoPassword,
                'status' => 'active',
            ])
            ->assertRedirect();

        $this->assertSame('staff', User::query()->where('email', $email)->value('user_type'));
    }

    public function test_forged_public_user_type_on_store_is_rejected(): void
    {
        $email = 'phase2.forged.public@morocco2030.test';

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name' => 'Forged Public',
                'email' => $email,
                'password' => $this->demoPassword,
                'password_confirmation' => $this->demoPassword,
                'status' => 'active',
                'user_type' => 'public',
            ])
            ->assertSessionHasErrors('user_type');

        $this->assertDatabaseMissing('users', ['email' => $email]);
    }

    public function test_super_admin_can_assign_valid_staff_role(): void
    {
        $email = 'phase2.staff.role@morocco2030.test';
        $role = Role::query()->where('slug', 'journalist')->firstOrFail();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.users.store'), [
                'name' => 'Staff With Journalist Role',
                'email' => $email,
                'password' => $this->demoPassword,
                'password_confirmation' => $this->demoPassword,
                'status' => 'active',
                'role_ids' => [$role->id],
            ])
            ->assertRedirect();

        $this->assertTrue(User::query()->where('email', $email)->firstOrFail()->hasRole('journalist'));
    }

    public function test_super_admin_cannot_assign_public_user_role(): void
    {
        $publicRole = Role::query()->updateOrCreate(
            ['slug' => 'public-user'],
            [
                'name' => 'Public User',
                'description' => 'Audience role for public registration.',
                'is_system' => true,
            ]
        );

        $email = 'phase2.blocked.role@morocco2030.test';

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name' => 'Blocked Role Staff',
                'email' => $email,
                'password' => $this->demoPassword,
                'password_confirmation' => $this->demoPassword,
                'status' => 'active',
                'role_ids' => [$publicRole->id],
            ])
            ->assertSessionHasErrors('role_ids');

        $this->assertDatabaseMissing('users', ['email' => $email]);
    }

    public function test_platform_admin_cannot_create_staff(): void
    {
        $platformAdmin = $this->demoUser('platform.admin@morocco2030.test');

        $this->actingAs($platformAdmin)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($platformAdmin)->get(route('admin.users.create'))->assertForbidden();
        $this->actingAs($platformAdmin)->post(route('admin.users.store'), [
            'name' => 'Forged Platform Staff',
            'email' => 'phase2.forged.platform@morocco2030.test',
            'password' => $this->demoPassword,
            'password_confirmation' => $this->demoPassword,
            'status' => 'active',
        ])->assertForbidden();
    }

    public function test_chief_editor_cannot_create_staff(): void
    {
        $this->actingAs($this->demoUser('chief.editor@morocco2030.test'))
            ->get(route('admin.users.create'))
            ->assertForbidden();

        $this->actingAs($this->demoUser('chief.editor@morocco2030.test'))
            ->post(route('admin.users.store'), [
                'name' => 'Forged Chief',
                'email' => 'phase2.forged.chief@morocco2030.test',
                'password' => $this->demoPassword,
                'password_confirmation' => $this->demoPassword,
                'status' => 'active',
            ])
            ->assertForbidden();
    }

    public function test_support_agent_cannot_create_staff(): void
    {
        $this->actingAs($this->demoUser('support.agent@morocco2030.test'))
            ->get(route('admin.users.create'))
            ->assertForbidden();

        $this->actingAs($this->demoUser('support.agent@morocco2030.test'))
            ->post(route('admin.users.store'), [
                'name' => 'Forged Support',
                'email' => 'phase2.forged.support@morocco2030.test',
                'password' => $this->demoPassword,
                'password_confirmation' => $this->demoPassword,
                'status' => 'active',
            ])
            ->assertForbidden();
    }

    public function test_non_super_admin_cannot_assign_roles(): void
    {
        $target = $this->demoUser('support.agent@morocco2030.test');

        $this->assertFalse($this->demoUser('chief.editor@morocco2030.test')->can('assignRoles', $target));
    }

    public function test_public_user_cannot_be_converted_to_staff(): void
    {
        $publicUser = $this->demoUser('public.user@morocco2030.test');

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.users.edit', $publicUser))
            ->put(route('admin.users.update', $publicUser), [
                'name' => $publicUser->name,
                'email' => $publicUser->email,
                'status' => 'active',
                'user_type' => 'staff',
            ])
            ->assertSessionHasErrors('user_type');
    }

    public function test_public_user_cannot_receive_admin_roles(): void
    {
        $publicUser = $this->demoUser('public.user@morocco2030.test');
        $role = Role::query()->where('slug', 'journalist')->firstOrFail();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.users.edit', $publicUser))
            ->put(route('admin.users.update', $publicUser), [
                'name' => $publicUser->name,
                'email' => $publicUser->email,
                'status' => 'active',
                'role_ids' => [$role->id],
            ])
            ->assertSessionHasErrors('role_ids');

        $this->assertCount(0, $publicUser->fresh()->roles);
    }

    public function test_public_user_stays_without_roles_after_update(): void
    {
        $publicUser = $this->demoUser('public.user@morocco2030.test');

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->put(route('admin.users.update', $publicUser), [
                'name' => 'Updated Public Phase 2',
                'email' => $publicUser->email,
                'status' => 'active',
            ])
            ->assertRedirect();

        $publicUser->refresh();
        $this->assertCount(0, $publicUser->roles);
        $this->assertTrue($publicUser->isPublic());
    }

    public function test_staff_user_cannot_be_converted_to_public(): void
    {
        $staffUser = $this->demoUser('support.agent@morocco2030.test');

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.users.edit', $staffUser))
            ->put(route('admin.users.update', $staffUser), [
                'name' => $staffUser->name,
                'email' => $staffUser->email,
                'status' => 'active',
                'user_type' => 'public',
            ])
            ->assertSessionHasErrors('user_type');
    }

    public function test_public_user_cannot_access_admin_dashboard(): void
    {
        $this->actingAs($this->demoUser('public.user@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_users_table_renders_inline_actions_with_valid_links(): void
    {
        $staffUser = $this->demoUser('superadmin@morocco2030.test');

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'staff']))
            ->assertOk()
            ->assertSee('admin-users-action-btn', false)
            ->assertSee(route('admin.users.show', $staffUser), false)
            ->assertSee(route('admin.users.edit', $staffUser), false);
    }

    public function test_staff_tab_shows_role_badges(): void
    {
        $html = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'staff']))
            ->assertOk()
            ->getContent();

        $module = $this->extractUsersModule($html);

        $this->assertStringContainsString('admin-users-badge--role', $module);
        $this->assertStringContainsString('Super Admin', $module);
    }

    private function extractUsersModule(string $html): string
    {
        if (preg_match('/<section class="panel admin-users-page.*?<\/section>/s', $html, $matches) !== 1) {
            $this->fail('Admin users module markup was not found.');
        }

        return $matches[0];
    }
}
