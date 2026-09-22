<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class AdminAuthAccessTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_admin_login_route_redirects_to_shared_login_and_protected_routes_use_shared_login(): void
    {
        $this->get(route('admin.login'))->assertRedirect(route('login'));

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_staff_user_with_a_role_can_log_in_from_shared_login_and_log_out(): void
    {
        $user = $this->makeStaffUser([], [
            'email' => 'admin@example.com',
            'password' => 'secret-pass-123',
        ]);

        $this->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'secret-pass-123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);

        $this->post(route('admin.logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_public_users_using_shared_login_go_to_public_account_not_admin(): void
    {
        $user = $this->makePublicUser([], [
            'email' => 'public@example.com',
            'password' => 'secret-pass-123',
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'public@example.com',
                'password' => 'secret-pass-123',
            ])
            ->assertRedirect(route('account.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_public_users_are_forbidden_from_admin_pages(): void
    {
        $user = $this->makePublicUser();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
