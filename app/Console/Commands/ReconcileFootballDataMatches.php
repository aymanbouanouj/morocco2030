<?php

namespace App\Console\Commands;

use App\Services\ExternalFootball\FootballDataWorldCupReconciliationService;
use Illuminate\Console\Command;

class ReconcileFootballDataMatches extends Command
{
    protected $signature = 'football-data:reconcile-matches';

    protected $description = 'Safely reconcile football-data.org World Cup match statuses and scores.';

    public function handle(FootballDataWorldCupReconciliationService $reconciliationService): int
    {
        $result = $reconciliationService->reconcile();
        $summary = $result['summary'] ?? [];

        $this->line('Football-data World Cup match reconciliation');
        $this->line('HTTP status: '.($result['status'] ?? 'n/a'));

        foreach ([
            'source_matches_total',
            'matches_status_updated',
            'scores_updated',
            'scheduled_scores_cleared',
            'venues_preserved',
            'venues_mapped',
            'conflicts_detected',
        ] as $key) {
            $this->line(str_replace('_', ' ', $key).': '.($summary[$key] ?? 0));
        }

        foreach (($result['db_counts_after'] ?? []) as $key => $value) {
            $this->line('db '.$key.': '.$value);
        }

        if (! ($result['ok'] ?? false)) {
            foreach (($result['errors'] ?? []) as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $this->info('Match reconciliation completed.');

        return self::SUCCESS;
    }
}
