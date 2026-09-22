<?php

namespace Tests\Feature\Public;

use App\Models\Language;
use App\Models\Role;
use App\Models\User;
use App\Support\PublicLocale;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class PublicStaffAuthSplitTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_guest_can_access_public_auth_pages_and_account_routes_redirect_to_public_login(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
        $this->get(route('password.request'))->assertOk();

        $this->get(route('account.index'))
            ->assertRedirect(route('login'));
    }

    public function test_public_registration_creates_only_a_public_account_and_redirects_to_the_account_area(): void
    {
        $role = Role::query()->create([
            'name' => 'Internal Staff',
            'slug' => 'internal-staff',
            'description' => 'Test role',
            'is_system' => false,
        ]);

        $response = $this->post(route('register.store'), [
            'name' => 'Public Visitor',
            'email' => 'visitor@example.com',
            'phone' => '+212600000000',
            'password' => 'public-pass-123',
            'password_confirmation' => 'public-pass-123',
            'user_type' => 'staff',
            'role_ids' => [$role->id],
        ]);

        $response->assertRedirect(route('account.index'));

        $user = User::query()->where('email', 'visitor@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertSame('public', $user->user_type);
        $this->assertSame('active', $user->status);
        $this->assertCount(0, $user->roles);
    }

    public function test_public_users_can_sign_in_access_account_pages_and_update_profile_and_settings(): void
    {
        Language::query()->create([
            'name' => 'French',
            'native_name' => 'Francais',
            'code' => 'fr',
            'locale' => 'fr',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $user = $this->makePublicUser([], [
            'email' => 'fan@example.com',
            'password' => 'public-pass-123',
            'preferred_locale' => 'en',
        ]);

        $this->post(route('login.store'), [
            'email' => 'fan@example.com',
            'password' => 'public-pass-123',
        ])->assertRedirect(route('account.index'));

        $this->actingAs($user->fresh())
            ->get(route('account.index'))
            ->assertOk();

        $this->actingAs($user->fresh())
            ->get(route('account.profile'))
            ->assertOk();

        $this->actingAs($user->fresh())
            ->get(route('account.settings'))
            ->assertOk();

        $this->actingAs($user->fresh())
            ->put(route('account.profile.update'), [
                'name' => 'Updated Fan',
                'email' => 'updated-fan@example.com',
                'phone' => '+212611111111',
            ])->assertRedirect(route('account.profile'));

        $this->actingAs($user->fresh())
            ->put(route('account.settings.update'), [
                'preferred_locale' => 'fr',
                'current_password' => 'public-pass-123',
                'password' => 'new-public-pass-123',
                'password_confirmation' => 'new-public-pass-123',
            ])
            ->assertRedirect(route('account.settings'))
            ->assertSessionHas(PublicLocale::SESSION_KEY, 'fr');

        $updatedUser = $user->fresh();

        $this->assertSame('Updated Fan', $updatedUser->name);
        $this->assertSame('updated-fan@example.com', $updatedUser->email);
        $this->assertSame('+212611111111', $updatedUser->phone);
        $this->assertSame('fr', $updatedUser->preferred_locale);
        $this->assertCredentials([
            'email' => 'updated-fan@example.com',
            'password' => 'new-public-pass-123',
        ]);
    }

    public function test_staff_users_can_use_the_shared_login_flow_and_are_redirected_to_admin(): void
    {
        $staff = $this->makeStaffUser([], [
            'email' => 'staff@example.com',
            'password' => 'staff-pass-123',
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'staff@example.com',
                'password' => 'staff-pass-123',
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($staff);
    }

    public function test_shared_login_rate_limits_repeated_failed_attempts(): void
    {
        $email = 'locked@example.com';
        $throttleKey = Str::transliterate(Str::lower($email).'|127.0.0.1');

        RateLimiter::clear($throttleKey);

        foreach (range(1, 5) as $attempt) {
            $this->from(route('login'))
                ->post(route('login.store'), [
                    'email' => $email,
                    'password' => 'wrong-password',
                ])
                ->assertRedirect(route('login'))
                ->assertSessionHasErrors('email');
        }

        $this->assertTrue(RateLimiter::tooManyAttempts($throttleKey, 5));

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $email,
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        RateLimiter::clear($throttleKey);
    }

    public function test_public_password_reset_flow_works_for_public_accounts(): void
    {
        Notification::fake();

        $user = $this->makePublicUser([], [
            'email' => 'recover@example.com',
            'password' => 'old-public-pass-123',
        ]);

        $this->post(route('password.email'), [
            'email' => 'recover@example.com',
        ])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);

        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'recover@example.com',
            'password' => 'new-public-pass-123',
            'password_confirmation' => 'new-public-pass-123',
        ])->assertRedirect(route('login'));

        $this->assertCredentials([
            'email' => 'recover@example.com',
            'password' => 'new-public-pass-123',
        ]);
    }

    public function test_staff_users_are_redirected_back_to_admin_from_public_account_routes(): void
    {
        $staff = $this->makeStaffUser();

        $this->actingAs($staff)
            ->get(route('account.index'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_public_users_keep_the_full_public_portal_while_seeing_account_controls(): void
    {
        $user = $this->makePublicUser();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Morocco 2030')
            ->assertSee(route('matches.index'), false)
            ->assertSee(route('account.index'), false)
            ->assertSee('My Account')
            ->assertSee('Sign Out')
            ->assertDontSee('Create Account');
    }
}
