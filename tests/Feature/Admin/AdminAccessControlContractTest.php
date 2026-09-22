<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\DemoAccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class AdminAccessControlContractTest extends TestCase
{
    use InteractsWithDemoAccessContract, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDemoAccessFoundation();
    }

    public function test_demo_users_exist_with_expected_roles(): void
    {
        foreach (DemoAccessControlSeeder::DEMO_USERS as $definition) {
            $user = $this->demoUser($definition['email']);

            if ($definition['role_slug'] === null) {
                $this->assertCount(0, $user->roles);

                continue;
            }

            $this->assertTrue($user->hasRole($definition['role_slug']), $definition['email']);
        }
    }

    public function test_demo_access_seeder_is_idempotent(): void
    {
        $this->seed(DemoAccessControlSeeder::class);

        $emails = collect(DemoAccessControlSeeder::DEMO_USERS)->pluck('email');

        $this->assertSame(
            $emails->count(),
            \App\Models\User::query()->whereIn('email', $emails)->count()
        );
    }

    public function test_public_user_has_no_admin_permissions(): void
    {
        $user = $this->demoUser('public.user@morocco2030.test');

        $this->assertFalse($user->canAccessAdmin());
        $this->assertFalse($user->hasPermission('audit-logs.view'));
        $this->assertFalse($user->hasPermission('news.manage'));
    }

    #[DataProvider('rolesForbiddenAuditPermissionProvider')]
    public function test_operational_roles_do_not_carry_audit_logs_permission(string $email): void
    {
        $user = $this->demoUser($email);

        $this->assertFalse($user->hasPermission('audit-logs.view'));
        $this->assertFalse($user->can('viewAny', \App\Models\AuditLog::class));
    }

    public static function rolesForbiddenAuditPermissionProvider(): array
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
            'support-agent' => ['support.agent@morocco2030.test'],
        ];
    }

    public function test_analyst_has_analytics_and_audit_permissions(): void
    {
        $user = $this->demoUser('analyst@morocco2030.test');

        $this->assertTrue($user->hasPermission('analytics.view'));
        $this->assertTrue($user->hasPermission('audit-logs.view'));
        $this->assertTrue($user->can('viewAny', \App\Models\AuditLog::class));
    }

    public function test_platform_admin_has_operational_permissions_but_not_identity_rbac(): void
    {
        $user = $this->demoUser('platform.admin@morocco2030.test');

        $this->assertTrue($user->hasPermission('matches.manage'));
        $this->assertTrue($user->hasPermission('audit-logs.view'));
        $this->assertFalse($user->hasPermission('users.manage'));
        $this->assertFalse($user->hasPermission('roles.manage'));
        $this->assertFalse($user->hasPermission('permissions.manage'));
    }

    public function test_chief_editor_role_seed_has_no_audit_permission(): void
    {
        $role = Role::query()->where('slug', 'chief-editor')->firstOrFail();
        $slugs = $role->permissions()->pluck('slug');

        $this->assertFalse($slugs->contains('audit-logs.view'));
        $this->assertTrue($slugs->contains('news.publish'));
    }

    public function test_legacy_roles_remain_available(): void
    {
        foreach (['content-editor', 'competition-manager'] as $slug) {
            $this->assertNotNull(Role::query()->where('slug', $slug)->first(), $slug);
        }
    }

    public function test_permission_catalog_includes_editorial_and_engagement_modules(): void
    {
        foreach ([
            'news.manage',
            'news.review',
            'news.publish',
            'media.manage',
            'contact-messages.manage',
            'analytics.view',
            'audit-logs.view',
        ] as $slug) {
            $this->assertNotNull(Permission::query()->where('slug', $slug)->first(), $slug);
        }
    }
}
