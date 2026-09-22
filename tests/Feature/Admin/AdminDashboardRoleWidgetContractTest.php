<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Admin\Concerns\InteractsWithDemoAccessContract;
use Tests\TestCase;

class AdminDashboardRoleWidgetContractTest extends TestCase
{
    use InteractsWithDemoAccessContract, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDemoAccessFoundation();
    }

    public function test_super_admin_sees_audit_and_analytics_widgets(): void
    {
        $this->actingAs($this->demoUser('superadmin@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Recent Activity', false)
            ->assertSee('Audit Log', false)
            ->assertSee('Analytics Overview', false);
    }

    public function test_analyst_sees_analytics_and_audit_widgets(): void
    {
        $this->actingAs($this->demoUser('analyst@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Analytics Overview', false)
            ->assertSee('Recent Activity', false)
            ->assertSee('Audit Log', false)
            ->assertDontSee('Fixtures Management', false);
    }

    public function test_support_agent_sees_contact_messages_kpi_not_audit_widgets(): void
    {
        $this->actingAs($this->demoUser('support.agent@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Contact Messages', false)
            ->assertDontSee('Recent Activity', false)
            ->assertDontSee('Audit Log', false)
            ->assertDontSee('Analytics Overview', false);
    }

    #[DataProvider('rolesWithoutSensitiveDashboardWidgetsProvider')]
    public function test_operational_roles_do_not_see_sensitive_dashboard_widgets(string $email): void
    {
        $this->actingAs($this->demoUser($email))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Recent Activity', false)
            ->assertDontSee('Audit Log', false);
    }

    public function test_journalist_dashboard_focuses_on_editorial_kpis(): void
    {
        $this->actingAs($this->demoUser('journalist@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Published News', false)
            ->assertDontSee('Total Users', false)
            ->assertDontSee('Analytics Overview', false);
    }

    public function test_match_manager_dashboard_shows_match_kpis_not_news(): void
    {
        $this->actingAs($this->demoUser('match.manager@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Matches', false)
            ->assertSee('Fixtures Management', false)
            ->assertDontSee('Published News', false);
    }

    public function test_public_user_cannot_access_admin_dashboard(): void
    {
        $this->actingAs($this->demoUser('public.user@morocco2030.test'))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public static function rolesWithoutSensitiveDashboardWidgetsProvider(): array
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
}
