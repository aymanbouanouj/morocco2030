<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\DemoAccessControlSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminDashboardWidgetVisibilityByRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            DemoAccessControlSeeder::class,
        ]);
    }

    public function test_super_admin_dashboard_shows_audit_widgets(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Recent Activity', false)
            ->assertSee('Audit Log', false);
    }

    public function test_analyst_dashboard_shows_audit_widgets_and_can_open_audit_logs(): void
    {
        $user = $this->demoUser('analyst@morocco2030.test');

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Recent Activity', false)
            ->assertSee('Audit Log', false);

        $this->actingAs($user)
            ->get(route('admin.audit-logs.index'))
            ->assertOk();
    }

    public function test_platform_admin_dashboard_shows_audit_widgets(): void
    {
        $this->actingAs($this->demoUser('platform.admin@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Recent Activity', false)
            ->assertSee('Audit Log', false);
    }

    #[DataProvider('operationalRolesWithoutAuditTrailProvider')]
    public function test_operational_roles_do_not_see_sensitive_dashboard_widgets(string $email): void
    {
        $this->actingAs($this->demoUser($email))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Recent Activity', false)
            ->assertDontSee('Audit Log', false);
    }

    #[DataProvider('operationalRolesWithoutAuditTrailProvider')]
    public function test_operational_roles_are_forbidden_from_audit_logs_index(string $email): void
    {
        $this->actingAs($this->demoUser($email))
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();
    }

    public static function operationalRolesWithoutAuditTrailProvider(): array
    {
        return [
            'chief-editor' => ['chief.editor@morocco2030.test'],
            'journalist' => ['journalist@morocco2030.test'],
            'match-manager' => ['match.manager@morocco2030.test'],
            'team-manager' => ['team.manager@morocco2030.test'],
            'venue-manager' => ['venue.manager@morocco2030.test'],
            'sponsor-manager' => ['sponsor.manager@morocco2030.test'],
            'media-manager' => ['media.manager@morocco2030.test'],
            'translator' => ['translator@morocco2030.test'],
        ];
    }

    public function test_public_user_cannot_access_admin_dashboard(): void
    {
        $this->actingAs($this->demoUser('public.user@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    private function demoUser(string $email): User
    {
        return User::query()->where('email', $email)->firstOrFail();
    }
}
