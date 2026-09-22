<?php

namespace Tests\Feature\Console;

use App\Models\AuditLog;
use App\Models\ContactMessage;
use App\Models\User;
use App\Models\VisitorAnalytic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RetentionReportCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_retention_report_command_runs_and_outputs_dry_run_summary(): void
    {
        $this->seedRetentionRecords();

        $this->artisan('morocco2030:retention-report')
            ->expectsOutputToContain('MOROCCO 2030 Retention Dry-Run Report')
            ->expectsOutputToContain('Mode: read-only dry-run. No records or files were changed.')
            ->expectsOutputToContain('visitor_analytics')
            ->expectsOutputToContain('audit_logs')
            ->expectsOutputToContain('contact_messages')
            ->expectsOutputToContain('Dry-run only')
            ->assertExitCode(0);
    }

    public function test_retention_report_counts_candidates_without_mutating_records(): void
    {
        $this->seedRetentionRecords();

        $before = $this->tableCounts();

        Artisan::call('morocco2030:retention-report', ['--json' => true]);

        $after = $this->tableCounts();
        $report = json_decode(Artisan::output(), true);

        $this->assertSame($before, $after);
        $this->assertTrue($report['read_only']);
        $this->assertFalse($report['destructive_actions_available']);
        $this->assertSame(2, $report['datasets']['visitor_analytics']['total_records']);
        $this->assertSame(1, $report['datasets']['visitor_analytics']['candidate_records']);
        $this->assertSame(2, $report['datasets']['audit_logs']['total_records']);
        $this->assertSame(1, $report['datasets']['audit_logs']['candidate_records']);
        $this->assertSame(2, $report['datasets']['contact_messages']['total_records']);
        $this->assertSame(1, $report['datasets']['contact_messages']['candidate_records']);
        $this->assertSame(1, $report['datasets']['contact_messages']['resolved_candidate_records']);
        $this->assertSame(0, $report['safety']['records_updated']);
        $this->assertSame(0, $report['safety']['records_deleted']);
        $this->assertSame(0, $report['safety']['files_pruned']);
    }

    public function test_retention_report_does_not_expose_sensitive_values(): void
    {
        $this->seedRetentionRecords();

        Artisan::call('morocco2030:retention-report', ['--json' => true]);

        $output = Artisan::output();

        $this->assertStringNotContainsString('private@example.com', $output);
        $this->assertStringNotContainsString('support@example.com', $output);
        $this->assertStringNotContainsString('203.0.113.77', $output);
        $this->assertStringNotContainsString('Sensitive Browser', $output);
        $this->assertStringNotContainsString('plain-secret', $output);
        $this->assertStringNotContainsString('token-secret', $output);
    }

    public function test_retention_report_supports_custom_thresholds(): void
    {
        VisitorAnalytic::query()->create([
            'path' => '/matches',
            'event_type' => 'page_view',
            'event_at' => now()->subDays(10),
        ]);

        Artisan::call('morocco2030:retention-report', [
            '--analytics-days' => 5,
            '--json' => true,
        ]);

        $report = json_decode(Artisan::output(), true);

        $this->assertSame(5, $report['thresholds']['analytics_days']);
        $this->assertSame(1, $report['datasets']['visitor_analytics']['candidate_records']);

        Artisan::call('morocco2030:retention-report', [
            '--analytics-days' => 20,
            '--json' => true,
        ]);

        $report = json_decode(Artisan::output(), true);

        $this->assertSame(20, $report['thresholds']['analytics_days']);
        $this->assertSame(0, $report['datasets']['visitor_analytics']['candidate_records']);
    }

    /**
     * @return array<string, int>
     */
    private function tableCounts(): array
    {
        return [
            'visitor_analytics' => VisitorAnalytic::query()->count(),
            'audit_logs' => AuditLog::query()->count(),
            'contact_messages' => ContactMessage::withTrashed()->count(),
            'users' => User::withTrashed()->count(),
        ];
    }

    private function seedRetentionRecords(): void
    {
        $user = User::factory()->create([
            'name' => 'Private User',
            'email' => 'private@example.com',
            'user_type' => 'public',
            'status' => 'active',
            'last_login_at' => now()->subDays(500),
        ]);

        User::factory()->create([
            'user_type' => 'staff',
            'status' => 'inactive',
        ]);

        VisitorAnalytic::query()->create([
            'user_id' => $user->id,
            'path' => '/privacy',
            'route_name' => 'public.privacy',
            'ip_address' => '203.0.113.77',
            'user_agent' => 'Sensitive Browser',
            'language_code' => 'en',
            'device_type' => 'desktop',
            'event_type' => 'page_view',
            'event_at' => now()->subDays(220),
        ]);

        VisitorAnalytic::query()->create([
            'path' => '/terms',
            'event_type' => 'page_view',
            'event_at' => now()->subDays(20),
        ]);

        AuditLog::query()->create([
            'user_id' => $user->id,
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'action' => 'users.updated',
            'old_values' => ['password' => 'plain-secret'],
            'new_values' => ['remember_token' => 'token-secret'],
            'ip_address' => '203.0.113.77',
            'user_agent' => 'Sensitive Browser',
            'route_name' => 'admin.users.update',
            'occurred_at' => now()->subDays(400),
        ]);

        AuditLog::query()->create([
            'user_id' => $user->id,
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'action' => 'users.viewed',
            'occurred_at' => now()->subDays(20),
        ]);

        $oldContactMessage = ContactMessage::query()->create([
            'user_id' => $user->id,
            'subject' => 'Old Support Message',
            'name' => 'Support Sender',
            'email' => 'support@example.com',
            'message' => 'Sensitive support text.',
            'status' => 'resolved',
            'responded_at' => now()->subDays(220),
        ]);

        ContactMessage::query()
            ->whereKey($oldContactMessage->id)
            ->update([
                'created_at' => now()->subDays(400),
                'updated_at' => now()->subDays(220),
            ]);

        $newContactMessage = ContactMessage::query()->create([
            'subject' => 'New Support Message',
            'name' => 'New Sender',
            'email' => 'new-support@example.com',
            'message' => 'Recent support text.',
            'status' => 'new',
        ]);

        ContactMessage::query()
            ->whereKey($newContactMessage->id)
            ->update([
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ]);
    }
}
