<?php

namespace Tests\Feature\Public;

use App\Models\VisitorAnalytic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsAdminTestData;
use Tests\TestCase;

class VisitorAnalyticsTrackingTest extends TestCase
{
    use BuildsAdminTestData, RefreshDatabase;

    public function test_public_homepage_get_creates_privacy_aware_visitor_analytic_record(): void
    {
        $this->withServerVariables([
            'REMOTE_ADDR' => '203.0.113.10',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ])->get(route('home'))->assertOk();

        $record = VisitorAnalytic::query()->firstOrFail();

        $this->assertSame('/', $record->path);
        $this->assertSame('home', $record->route_name);
        $this->assertSame('page_view', $record->event_type);
        $this->assertSame('desktop', $record->device_type);
        $this->assertSame('en', $record->language_code);
        $this->assertNotSame('203.0.113.10', $record->ip_address);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{40}$/', (string) $record->ip_address);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{40}$/', (string) $record->session_id);
        $this->assertNull($record->referrer);
        $this->assertNull($record->user_agent);
        $this->assertTrue((bool) ($record->meta['ip_pseudonymized'] ?? false));
        $this->assertTrue((bool) ($record->meta['session_pseudonymized'] ?? false));
        $this->assertFalse((bool) ($record->meta['referrer_stored'] ?? true));
        $this->assertFalse((bool) ($record->meta['user_agent_stored'] ?? true));
    }

    public function test_public_map_page_creates_a_page_view_record_without_breaking_response(): void
    {
        $this->withServerVariables([
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) Mobile',
        ])->get(route('map.index'))->assertOk();

        $record = VisitorAnalytic::query()->firstOrFail();

        $this->assertSame('/map', $record->path);
        $this->assertSame('map.index', $record->route_name);
        $this->assertSame('mobile', $record->device_type);
    }

    public function test_authenticated_public_account_page_tracks_public_user_id_when_supported(): void
    {
        $user = $this->makePublicUser();

        $this->actingAs($user)
            ->get(route('account.index'))
            ->assertOk();

        $record = VisitorAnalytic::query()->firstOrFail();

        $this->assertSame('/account', $record->path);
        $this->assertSame($user->id, $record->user_id);
    }

    public function test_admin_dashboard_does_not_create_public_visitor_analytic_record(): void
    {
        $user = $this->makeStaffUser();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->assertDatabaseCount('visitor_analytics', 0);
    }

    public function test_login_and_register_pages_are_excluded_from_public_tracking(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();

        $this->assertDatabaseCount('visitor_analytics', 0);
    }

    public function test_asset_like_paths_are_not_tracked(): void
    {
        $this->get('/assets/app.css')->assertNotFound();

        $this->assertDatabaseCount('visitor_analytics', 0);
    }
}
