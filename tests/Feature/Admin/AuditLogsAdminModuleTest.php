<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class AuditLogsAdminModuleTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
    }

    public function test_audit_logs_admin_routes_are_protected_and_permission_controlled(): void
    {
        $auditLog = $this->makeAuditLog();

        $this->get(route('admin.audit-logs.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($this->makePublicUser())
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser())
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();

        $this->actingAs($this->makeStaffUser(['audit-logs.view']))
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();

        $this->actingAs($this->makeAuditTrailViewer())
            ->get(route('admin.audit-logs.index'))
            ->assertOk()
            ->assertSee($auditLog->action);
    }

    public function test_authorized_staff_can_view_audit_log_detail(): void
    {
        $user = $this->makeAuditTrailViewer();
        $auditLog = $this->makeAuditLog([
            'action' => 'users.updated',
            'route_name' => 'admin.users.update',
        ]);

        $this->actingAs($user)
            ->get(route('admin.audit-logs.show', $auditLog))
            ->assertOk()
            ->assertSee('users.updated')
            ->assertSee('admin.users.update')
            ->assertSee('Old Values')
            ->assertSee('New Values');
    }

    public function test_audit_logs_admin_routes_are_read_only(): void
    {
        $this->assertFalse(Route::has('admin.audit-logs.create'));
        $this->assertFalse(Route::has('admin.audit-logs.store'));
        $this->assertFalse(Route::has('admin.audit-logs.edit'));
        $this->assertFalse(Route::has('admin.audit-logs.update'));
        $this->assertFalse(Route::has('admin.audit-logs.destroy'));
    }

    public function test_sensitive_historical_values_are_masked_at_display_time(): void
    {
        $user = $this->makeAuditTrailViewer();
        $auditLog = $this->makeAuditLog([
            'old_values' => [
                'email' => 'admin@example.com',
                'password' => 'plain-password',
                'nested' => [
                    'remember_token' => 'remember-me',
                ],
            ],
            'new_values' => [
                'name' => 'Admin User',
                'DB_PASSWORD' => 'db-secret',
                'metadata' => [
                    'api_key' => 'api-secret',
                    'safe' => 'visible',
                ],
            ],
        ]);

        $this->actingAs($user)
            ->get(route('admin.audit-logs.show', $auditLog))
            ->assertOk()
            ->assertSee('[redacted]')
            ->assertSee('admin@example.com')
            ->assertSee('visible')
            ->assertDontSee('plain-password')
            ->assertDontSee('remember-me')
            ->assertDontSee('db-secret')
            ->assertDontSee('api-secret');
    }

    public function test_audit_log_filters_render_and_limit_results(): void
    {
        $user = $this->makeAuditTrailViewer();

        $this->makeAuditLog([
            'action' => 'users.updated',
            'route_name' => 'admin.users.update',
            'occurred_at' => now()->subDay(),
        ]);

        $this->makeAuditLog([
            'action' => 'news.updated',
            'route_name' => 'admin.news.update',
            'occurred_at' => now()->subDays(3),
        ]);

        $this->actingAs($user)
            ->get(route('admin.audit-logs.index', [
                'action' => 'users.updated',
                'date_from' => now()->subDays(2)->toDateString(),
                'date_to' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('users.updated')
            ->assertSee('admin.users.update')
            ->assertDontSee('admin.news.update');
    }

    private function makeAuditTrailViewer(): User
    {
        $user = User::factory()->create([
            'user_type' => 'staff',
            'status' => 'active',
        ]);

        $role = Role::query()->where('slug', 'analyst')->firstOrFail();
        $user->roles()->attach($role->id);

        return $user->fresh(['roles.permissions']);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeAuditLog(array $attributes = []): AuditLog
    {
        $actor = $attributes['user'] ?? User::factory()->create([
            'user_type' => 'staff',
            'status' => 'active',
        ]);

        $auditable = $attributes['auditable'] ?? $actor;

        return AuditLog::query()->create([
            'user_id' => $attributes['user_id'] ?? $actor->id,
            'auditable_type' => $attributes['auditable_type'] ?? $auditable::class,
            'auditable_id' => $attributes['auditable_id'] ?? $auditable->getKey(),
            'action' => $attributes['action'] ?? 'users.updated',
            'old_values' => $attributes['old_values'] ?? ['name' => 'Old Name'],
            'new_values' => $attributes['new_values'] ?? ['name' => 'New Name'],
            'ip_address' => $attributes['ip_address'] ?? '127.0.0.1',
            'user_agent' => $attributes['user_agent'] ?? 'Feature Test',
            'route_name' => $attributes['route_name'] ?? 'admin.users.update',
            'occurred_at' => $attributes['occurred_at'] ?? now(),
        ]);
    }
}
