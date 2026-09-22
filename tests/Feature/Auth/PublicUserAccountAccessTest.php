<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class PublicUserAccountAccessTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_guests_are_redirected_to_login_before_viewing_account_pages(): void
    {
        foreach ([
            route('account.index'),
            route('account.profile'),
            route('account.settings'),
            route('account.favorites'),
            route('account.notifications'),
        ] as $route) {
            $this->get($route)->assertRedirect(route('login'));
        }
    }

    public function test_public_users_cannot_access_admin_dashboard(): void
    {
        $user = $this->makePublicUser();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_staff_users_are_redirected_away_from_public_account_pages(): void
    {
        $staff = $this->makeStaffUser();

        $this->actingAs($staff)
            ->get(route('account.index'))
            ->assertRedirect(route('admin.dashboard'));
    }
}
