<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class AdminUsersStaffPublicSeparationTest extends TestCase
{
    use InteractsWithDemoAccessContract;
    use RefreshDatabase;

    private string $demoPassword = 'Morocco2030-Local-Demo-Only';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDemoAccessFoundation();
    }

    public function test_users_index_shows_staff_and_public_audience_tabs(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Staff Accounts', false)
            ->assertSee('Public Audience', false);
    }

    public function test_default_users_index_shows_staff_accounts(): void
    {
        $response = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index'));

        $response->assertOk()
            ->assertSee('superadmin@morocco2030.test', false)
            ->assertDontSee('public.user@morocco2030.test', false);
    }

    public function test_staff_tab_lists_internal_staff_accounts(): void
    {
        $response = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'staff']));

        $response->assertOk()
            ->assertSee('superadmin@morocco2030.test', false)
            ->assertSee('support.agent@morocco2030.test', false)
            ->assertDontSee('public.user@morocco2030.test', false);
    }

    public function test_public_tab_lists_public_audience_only(): void
    {
        $response = $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'public']));

        $response->assertOk()
            ->assertSee('public.user@morocco2030.test', false)
            ->assertDontSee('superadmin@morocco2030.test', false)
            ->assertDontSee('support.agent@morocco2030.test', false);
    }

    public function test_public_audience_rows_show_no_admin_access_badge(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'public']))
            ->assertOk()
            ->assertSee('No admin access', false)
            ->assertSee('Public Audience', false);
    }

    public function test_public_user_edit_form_has_no_role_assignment_controls(): void
    {
        $publicUser = $this->demoUser('public.user@morocco2030.test');

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.edit', $publicUser))
            ->assertOk()
            ->assertSee('Public Audience Profile', false)
            ->assertDontSee('name="role_ids[]"', false)
            ->assertDontSee('Staff Roles', false);
    }

    public function test_users_table_action_links_resolve(): void
    {
        $staffUser = $this->demoUser('superadmin@morocco2030.test');

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'staff']))
            ->assertOk()
            ->assertSee(route('admin.users.show', $staffUser), false)
            ->assertSee(route('admin.users.edit', $staffUser), false);

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.show', $staffUser))
            ->assertOk();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.edit', $staffUser))
            ->assertOk();
    }

    public function test_super_admin_sees_create_staff_user_button_on_staff_tab(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.index', ['type' => 'staff']))
            ->assertOk()
            ->assertSee('Create Staff User', false);
    }

    public function test_super_admin_can_access_create_staff_form(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Create Staff User', false);
    }

    public function test_super_admin_can_create_staff_user_forced_to_staff_type(): void
    {
        $email = 'new.staff.separation@morocco2030.test';

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name' => 'New Demo Staff',
                'email' => $email,
                'password' => $this->demoPassword,
                'password_confirmation' => $this->demoPassword,
                'status' => 'active',
                'user_type' => 'public',
            ])
            ->assertSessionHasErrors('user_type');

        $this->assertDatabaseMissing('users', ['email' => $email]);
    }

    public function test_super_admin_can_assign_valid_staff_role_on_create(): void
    {
        $email = 'role.staff.separation@morocco2030.test';
        $journalistRole = Role::query()->where('slug', 'journalist')->firstOrFail();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->post(route('admin.users.store'), [
                'name' => 'Staff With Role',
                'email' => $email,
                'password' => $this->demoPassword,
                'password_confirmation' => $this->demoPassword,
                'status' => 'active',
                'role_ids' => [$journalistRole->id],
            ])
            ->assertRedirect();

        $created = User::query()->where('email', $email)->firstOrFail();

        $this->assertTrue($created->hasRole('journalist'));
    }

    public function test_super_admin_cannot_assign_public_user_role_from_admin_form(): void
    {
        $publicRole = Role::query()->updateOrCreate(
            ['slug' => 'public-user'],
            [
                'name' => 'Public User',
                'description' => 'Audience role for public registration.',
                'is_system' => true,
            ]
        );

        $email = 'blocked.public.role@morocco2030.test';

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name' => 'Should Fail Role',
                'email' => $email,
                'password' => $this->demoPassword,
                'password_confirmation' => $this->demoPassword,
                'status' => 'active',
                'role_ids' => [$publicRole->id],
            ])
            ->assertSessionHasErrors('role_ids');

        $this->assertDatabaseMissing('users', ['email' => $email]);
    }

    public function test_platform_admin_cannot_access_users_index_or_create_staff(): void
    {
        $platformAdmin = $this->demoUser('platform.admin@morocco2030.test');

        $this->actingAs($platformAdmin)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($platformAdmin)->get(route('admin.users.create'))->assertForbidden();
        $this->actingAs($platformAdmin)->post(route('admin.users.store'), [
            'name' => 'Forged Staff',
            'email' => 'forged.platform@morocco2030.test',
            'password' => $this->demoPassword,
            'password_confirmation' => $this->demoPassword,
            'status' => 'active',
        ])->assertForbidden();
    }

    public function test_chief_editor_cannot_create_staff_accounts(): void
    {
        $chiefEditor = $this->demoUser('chief.editor@morocco2030.test');

        $this->actingAs($chiefEditor)->get(route('admin.users.create'))->assertForbidden();
        $this->actingAs($chiefEditor)->post(route('admin.users.store'), [
            'name' => 'Forged Chief Staff',
            'email' => 'forged.chief@morocco2030.test',
            'password' => $this->demoPassword,
            'password_confirmation' => $this->demoPassword,
            'status' => 'active',
        ])->assertForbidden();
    }

    public function test_support_agent_cannot_create_staff_accounts(): void
    {
        $supportAgent = $this->demoUser('support.agent@morocco2030.test');

        $this->actingAs($supportAgent)->get(route('admin.users.create'))->assertForbidden();
        $this->actingAs($supportAgent)->post(route('admin.users.store'), [
            'name' => 'Forged Support Staff',
            'email' => 'forged.support@morocco2030.test',
            'password' => $this->demoPassword,
            'password_confirmation' => $this->demoPassword,
            'status' => 'active',
        ])->assertForbidden();
    }

    public function test_non_super_admin_cannot_assign_roles_via_policy(): void
    {
        $staffTarget = $this->demoUser('support.agent@morocco2030.test');
        $chiefEditor = $this->demoUser('chief.editor@morocco2030.test');

        $this->assertFalse($chiefEditor->can('assignRoles', $staffTarget));
        $this->assertFalse($this->demoUser('platform.admin@morocco2030.test')->can('assignRoles', $staffTarget));
    }

    public function test_public_user_cannot_be_converted_to_staff_via_update(): void
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

        $this->assertTrue($publicUser->fresh()->isPublic());
    }

    public function test_public_user_cannot_receive_admin_roles_via_forged_update(): void
    {
        $publicUser = $this->demoUser('public.user@morocco2030.test');
        $journalistRole = Role::query()->where('slug', 'journalist')->firstOrFail();

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->from(route('admin.users.edit', $publicUser))
            ->put(route('admin.users.update', $publicUser), [
                'name' => $publicUser->name,
                'email' => $publicUser->email,
                'status' => 'active',
                'role_ids' => [$journalistRole->id],
            ])
            ->assertSessionHasErrors('role_ids');

        $this->assertCount(0, $publicUser->fresh()->roles);
    }

    public function test_public_user_remains_without_roles_after_safe_profile_update(): void
    {
        $publicUser = $this->demoUser('public.user@morocco2030.test');

        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->put(route('admin.users.update', $publicUser), [
                'name' => 'Updated Public Name',
                'email' => $publicUser->email,
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.users.show', $publicUser));

        $publicUser->refresh();

        $this->assertSame('Updated Public Name', $publicUser->name);
        $this->assertCount(0, $publicUser->roles);
        $this->assertTrue($publicUser->isPublic());
    }

    public function test_staff_user_cannot_be_converted_to_public_via_update(): void
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

        $this->assertTrue($staffUser->fresh()->isStaff());
    }

    public function test_public_demo_user_remains_forbidden_from_admin_dashboard(): void
    {
        $this->actingAs($this->demoUser('public.user@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
