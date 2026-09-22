<?php

namespace App\Console\Commands;

use App\Services\ExternalFootball\FootballDataSquadImportService;
use App\Services\ExternalFootball\FootballDataSquadPayloadCollector;
use Illuminate\Console\Command;

class ImportFootballDataSquadsFromCache extends Command
{
    protected $signature = 'football-data:import-squads-from-cache';

    protected $description = 'Import football-data.org World Cup squads from the complete private cache.';

    public function handle(
        FootballDataSquadPayloadCollector $collector,
        FootballDataSquadImportService $importService
    ): int {
        $loaded = $collector->loadCompleteCache();

        if (! $loaded['ok']) {
            foreach ($loaded['errors'] as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $result = $importService->importFromPayloadCache($loaded['cache']);

        if (! $result['ok']) {
            foreach ($result['errors'] as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $this->info('Football-data squad import from cache completed.');
        $this->table(
            ['Metric', 'Value'],
            collect($result['summary'])
                ->reject(fn ($value) => is_array($value))
                ->map(fn ($value, string $key) => [str_replace('_', ' ', $key), $value])
                ->values()
                ->all()
        );

        return self::SUCCESS;
    }
}
