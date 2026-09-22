<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\DemoAccessControlSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class DemoBrowserLoginFlowTest extends TestCase
{
    use RefreshDatabase;

    private string $demoPassword;

    protected function setUp(): void
    {
        parent::setUp();

        $this->demoPassword = (string) env('MOROCCO2030_DEMO_USERS_PASSWORD', 'Morocco2030-Local-Demo-Only');

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            DemoAccessControlSeeder::class,
        ]);
    }

    public function test_super_admin_can_log_in_via_shared_login_post(): void
    {
        $this->clearLoginThrottle('superadmin@morocco2030.test');

        $response = $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'superadmin@morocco2030.test',
                'password' => $this->demoPassword,
            ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs(User::query()->where('email', 'superadmin@morocco2030.test')->firstOrFail());
    }

    public function test_chief_editor_can_log_in_via_shared_login_post(): void
    {
        $this->clearLoginThrottle('chief.editor@morocco2030.test');

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'chief.editor@morocco2030.test',
                'password' => $this->demoPassword,
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_public_user_can_log_in_via_shared_login_post(): void
    {
        $this->clearLoginThrottle('public.user@morocco2030.test');

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'public.user@morocco2030.test',
                'password' => $this->demoPassword,
            ])
            ->assertRedirect(route('account.index'));

        $this->assertAuthenticated();
    }

    public function test_wrong_password_fails_shared_login(): void
    {
        $this->clearLoginThrottle('superadmin@morocco2030.test');

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'superadmin@morocco2030.test',
                'password' => 'definitely-wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_legacy_admin_login_post_route_authenticates_via_shared_controller(): void
    {
        $this->clearLoginThrottle('superadmin@morocco2030.test');

        $this->from(route('login'))
            ->post(route('admin.login.store'), [
                'email' => 'superadmin@morocco2030.test',
                'password' => $this->demoPassword,
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_shared_login_form_posts_to_login_store_not_admin_login_store(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('login.store'), false)
            ->assertDontSee(route('admin.login.store'), false);
    }

    private function clearLoginThrottle(string $email): void
    {
        RateLimiter::clear(Str::transliterate(Str::lower($email).'|127.0.0.1'));
    }
}
