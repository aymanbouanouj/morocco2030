<?php

namespace Tests\Feature\Admin;

use App\Models\Group;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_shared_login_for_group_routes(): void
    {
        $this->get(route('admin.groups.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_group_routes_are_forbidden_without_the_groups_permission(): void
    {
        $user = $this->makeStaffUser();
        $group = Group::query()->create([
            'name' => 'Group A',
            'code' => 'A',
            'sort_order' => 1,
        ]);

        $this->actingAs($user);

        $this->get(route('admin.groups.index'))->assertForbidden();
        $this->get(route('admin.groups.create'))->assertForbidden();
        $this->post(route('admin.groups.store'), [
            'name' => 'Group B',
            'code' => 'B',
            'sort_order' => 2,
        ])->assertForbidden();
        $this->get(route('admin.groups.edit', $group))->assertForbidden();
        $this->put(route('admin.groups.update', $group), [
            'name' => 'Group Alpha',
            'code' => 'A1',
            'sort_order' => 1,
        ])->assertForbidden();
        $this->delete(route('admin.groups.destroy', $group))->assertForbidden();
    }

    public function test_authorized_staff_can_manage_groups(): void
    {
        $user = $this->makeStaffUser('groups.manage');
        $group = Group::query()->create([
            'name' => 'Group A',
            'code' => 'A',
            'sort_order' => 1,
        ]);

        $this->actingAs($user);

        $this->get(route('admin.groups.index'))->assertOk();
        $this->get(route('admin.groups.create'))->assertOk();

        $this->post(route('admin.groups.store'), [
            'name' => 'Group B',
            'code' => 'B',
            'description' => 'Second group',
            'sort_order' => 2,
        ])->assertRedirect(route('admin.groups.index'));

        $createdGroup = Group::query()->where('code', 'B')->firstOrFail();

        $this->get(route('admin.groups.edit', $createdGroup))->assertOk();

        $this->put(route('admin.groups.update', $createdGroup), [
            'name' => 'Group Bravo',
            'code' => 'B',
            'description' => 'Updated group',
            'sort_order' => 3,
        ])->assertRedirect(route('admin.groups.index'));

        $this->assertDatabaseHas('groups', [
            'id' => $createdGroup->id,
            'name' => 'Group Bravo',
            'sort_order' => 3,
        ]);

        $this->delete(route('admin.groups.destroy', $createdGroup))
            ->assertRedirect(route('admin.groups.index'));

        $this->assertDatabaseMissing('groups', [
            'id' => $createdGroup->id,
        ]);

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'code' => 'A',
        ]);
    }

    public function test_public_users_are_blocked_from_admin_group_routes(): void
    {
        $user = User::factory()->create([
            'user_type' => 'public',
            'status' => 'active',
        ]);

        $role = Role::query()->create([
            'name' => 'Public Role',
            'slug' => 'public-role',
            'description' => 'Non-staff role for access testing.',
            'is_system' => false,
        ]);

        $user->roles()->attach($role->id);

        $this->actingAs($user)
            ->get(route('admin.groups.index'))
            ->assertForbidden();
    }

    private function makeStaffUser(?string $permissionSlug = null): User
    {
        $user = User::factory()->create([
            'user_type' => 'staff',
            'status' => 'active',
        ]);

        $role = Role::query()->create([
            'name' => $permissionSlug ? 'Groups Manager' : 'Staff Role',
            'slug' => $permissionSlug ? 'groups-manager' : 'staff-role',
            'description' => 'Role used for admin authorization testing.',
            'is_system' => false,
        ]);

        if ($permissionSlug) {
            $permission = Permission::query()->create([
                'name' => 'Manage Groups',
                'slug' => $permissionSlug,
                'module' => 'competition',
                'description' => 'Permission used for group authorization tests.',
            ]);

            $role->permissions()->attach($permission->id);
        }

        $user->roles()->attach($role->id);

        return $user;
    }
}
