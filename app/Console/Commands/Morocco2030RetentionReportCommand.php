<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\ContactMessage;
use App\Models\MediaFile;
use App\Models\User;
use App\Models\VisitorAnalytic;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class Morocco2030RetentionReportCommand extends Command
{
    protected $signature = 'morocco2030:retention-report
        {--analytics-days=180 : Visitor analytics dry-run threshold in days}
        {--audit-days=365 : Audit log dry-run threshold in days}
        {--contact-days=365 : Contact message dry-run threshold in days}
        {--resolved-contact-days=180 : Resolved contact message dry-run threshold in days}
        {--inactive-user-days=365 : Public account manual-review threshold in days}
        {--json : Output the dry-run report as JSON}';

    protected $description = 'Generate a read-only MOROCCO 2030 data retention dry-run report.';

    public function handle(): int
    {
        $thresholds = $this->thresholds();
        $report = [
            'generated_at' => now()->toIso8601String(),
            'mode' => 'dry-run',
            'read_only' => true,
            'destructive_actions_available' => false,
            'thresholds' => $thresholds,
            'datasets' => [
                'visitor_analytics' => $this->visitorAnalytics($thresholds['analytics_days']),
                'audit_logs' => $this->auditLogs($thresholds['audit_days']),
                'contact_messages' => $this->contactMessages(
                    $thresholds['contact_days'],
                    $thresholds['resolved_contact_days']
                ),
                'public_accounts' => $this->publicAccounts($thresholds['inactive_user_days']),
                'staff_accounts' => $this->staffAccounts(),
                'media_files' => $this->mediaFiles(),
                'application_logs' => $this->manualReview(
                    'Application log files are outside this database report and require operational log rotation review.'
                ),
                'database_backups' => $this->manualReview(
                    'Database backup files are outside this database report and require manual backup retention review.'
                ),
            ],
            'safety' => [
                'records_updated' => 0,
                'records_deleted' => 0,
                'files_pruned' => 0,
                'sensitive_values_printed' => false,
                'notes' => [
                    'Counts only. No IDs, emails, IP addresses, tokens, user agents, or raw payloads are printed.',
                    'This command does not include delete, force, prune, anonymize, or mutation options.',
                ],
            ],
        ];

        if ((bool) $this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}');

            return self::SUCCESS;
        }

        $this->info('MOROCCO 2030 Retention Dry-Run Report');
        $this->line('Mode: read-only dry-run. No records or files were changed.');
        $this->newLine();

        $this->table(
            ['Dataset', 'Status', 'Total', 'Candidates', 'Threshold', 'Action'],
            collect($report['datasets'])
                ->map(fn (array $dataset, string $key): array => [
                    $key,
                    $dataset['status'],
                    $dataset['total_records'] ?? 'manual',
                    $dataset['candidate_records'] ?? 'manual',
                    $dataset['threshold'] ?? 'manual review',
                    $dataset['recommended_action'],
                ])
                ->values()
                ->all()
        );

        $this->newLine();
        $this->warn('Dry-run only: no update, delete, prune, anonymize, or file operation was executed.');

        return self::SUCCESS;
    }

    /**
     * @return array<string, int>
     */
    private function thresholds(): array
    {
        return [
            'analytics_days' => $this->positiveIntegerOption('analytics-days', 180),
            'audit_days' => $this->positiveIntegerOption('audit-days', 365),
            'contact_days' => $this->positiveIntegerOption('contact-days', 365),
            'resolved_contact_days' => $this->positiveIntegerOption('resolved-contact-days', 180),
            'inactive_user_days' => $this->positiveIntegerOption('inactive-user-days', 365),
        ];
    }

    private function positiveIntegerOption(string $name, int $default): int
    {
        $value = filter_var($this->option($name), FILTER_VALIDATE_INT);

        if (! is_int($value) || $value < 1) {
            return $default;
        }

        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    private function visitorAnalytics(int $days): array
    {
        if (! $this->tableReady('visitor_analytics', ['event_at'])) {
            return $this->unavailable('visitor_analytics table or event_at column is unavailable.');
        }

        $cutoff = now()->subDays($days);

        return $this->countableDataset(
            total: VisitorAnalytic::query()->count(),
            candidates: VisitorAnalytic::query()->where('event_at', '<', $cutoff)->count(),
            threshold: $this->thresholdLabel($days, $cutoff),
            action: 'Review for approved analytics pruning or aggregation. No automatic deletion.'
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function auditLogs(int $days): array
    {
        if (! $this->tableReady('audit_logs', ['occurred_at'])) {
            return $this->unavailable('audit_logs table or occurred_at column is unavailable.');
        }

        $cutoff = now()->subDays($days);

        return $this->countableDataset(
            total: AuditLog::query()->count(),
            candidates: AuditLog::query()->where('occurred_at', '<', $cutoff)->count(),
            threshold: $this->thresholdLabel($days, $cutoff),
            action: 'Manual security/legal review before any future audit retention action.'
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function contactMessages(int $contactDays, int $resolvedDays): array
    {
        if (! $this->tableReady('contact_messages', ['created_at', 'status'])) {
            return $this->unavailable('contact_messages table, created_at column, or status column is unavailable.');
        }

        $contactCutoff = now()->subDays($contactDays);
        $resolvedCutoff = now()->subDays($resolvedDays);
        $resolvedDateColumn = Schema::hasColumn('contact_messages', 'responded_at') ? 'responded_at' : 'updated_at';

        $resolvedQuery = ContactMessage::query()
            ->whereIn('status', ['resolved', 'archived'])
            ->whereNotNull($resolvedDateColumn)
            ->where($resolvedDateColumn, '<', $resolvedCutoff);

        return [
            ...$this->countableDataset(
                total: ContactMessage::withTrashed()->count(),
                candidates: ContactMessage::withTrashed()->where('created_at', '<', $contactCutoff)->count(),
                threshold: $this->thresholdLabel($contactDays, $contactCutoff),
                action: 'Review old contact messages for approved anonymization, archival, or deletion.'
            ),
            'resolved_candidate_records' => $resolvedQuery->count(),
            'resolved_threshold' => $this->thresholdLabel($resolvedDays, $resolvedCutoff),
            'resolved_date_column' => $resolvedDateColumn,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function publicAccounts(int $days): array
    {
        if (! $this->tableReady('users', ['user_type', 'status', 'created_at'])) {
            return $this->unavailable('users table or required user columns are unavailable.');
        }

        $cutoff = now()->subDays($days);

        $query = User::withTrashed()->where('user_type', 'public');
        $candidateQuery = (clone $query)
            ->where(function (Builder $inner) use ($cutoff): void {
                $inner->where('status', '!=', 'active')
                    ->orWhereNull('email_verified_at')
                    ->orWhere('last_login_at', '<', $cutoff)
                    ->orWhere(function (Builder $neverLoggedIn) use ($cutoff): void {
                        $neverLoggedIn->whereNull('last_login_at')
                            ->where('created_at', '<', $cutoff);
                    });
            });

        return $this->countableDataset(
            total: $query->count(),
            candidates: $candidateQuery->count(),
            threshold: $this->thresholdLabel($days, $cutoff),
            action: 'Manual account review only. Do not delete users automatically because accounts may link to audits, messages, favorites, and notifications.'
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function staffAccounts(): array
    {
        if (! $this->tableReady('users', ['user_type', 'status'])) {
            return $this->unavailable('users table or required staff account columns are unavailable.');
        }

        return [
            'status' => 'manual_review',
            'total_records' => User::withTrashed()->where('user_type', 'staff')->count(),
            'candidate_records' => User::withTrashed()
                ->where('user_type', 'staff')
                ->where('status', '!=', 'active')
                ->count(),
            'threshold' => 'manual review',
            'recommended_action' => 'Review inactive staff accounts manually. Never auto-delete staff users because audit integrity may depend on them.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mediaFiles(): array
    {
        if (! Schema::hasTable('media_files')) {
            return $this->manualReview('Media uploads are not implemented in this phase and media_files table is unavailable.');
        }

        return [
            'status' => 'future_scope',
            'total_records' => MediaFile::withTrashed()->count(),
            'candidate_records' => 0,
            'threshold' => 'future media policy',
            'recommended_action' => 'Media manager and upload retention are future scope. Do not prune files from this command.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function countableDataset(int $total, int $candidates, string $threshold, string $action): array
    {
        return [
            'status' => 'available',
            'total_records' => $total,
            'candidate_records' => $candidates,
            'threshold' => $threshold,
            'recommended_action' => $action,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function unavailable(string $reason): array
    {
        return [
            'status' => 'unavailable',
            'total_records' => null,
            'candidate_records' => null,
            'threshold' => 'not evaluated',
            'recommended_action' => $reason,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function manualReview(string $message): array
    {
        return [
            'status' => 'manual_review',
            'total_records' => null,
            'candidate_records' => null,
            'threshold' => 'manual review',
            'recommended_action' => $message,
        ];
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function tableReady(string $table, array $columns): bool
    {
        if (! Schema::hasTable($table)) {
            return false;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    private function thresholdLabel(int $days, Carbon $cutoff): string
    {
        return "older than {$days} days before ".$cutoff->toDateString();
    }
}
