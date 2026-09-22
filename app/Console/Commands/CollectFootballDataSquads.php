<?php

namespace App\Console\Commands;

use App\Services\ExternalFootball\FootballDataSquadPayloadCollector;
use Illuminate\Console\Command;

class CollectFootballDataSquads extends Command
{
    protected $signature = 'football-data:collect-squads';

    protected $description = 'Collect all football-data.org World Cup squad payloads with rate-limit retries.';

    public function handle(FootballDataSquadPayloadCollector $collector): int
    {
        $this->info('Collecting football-data.org World Cup squad payloads.');

        $result = $collector->collect(function (array $event): void {
            if (($event['type'] ?? null) === 'progress') {
                $this->line($event['current'].'/'.$event['total'].' '.$event['team']);
            }

            if (($event['type'] ?? null) === 'rate_limit') {
                $this->warn('429 received; waiting '.$event['wait_seconds'].' seconds before retry '
                    .$event['retry'].'/'.$event['max_retries'].' for '.$event['team'].'.');
            }
        });

        if (! $result['ok']) {
            foreach ($result['errors'] as $error) {
                $this->error($error);
            }

            $this->warn('Collection incomplete. No complete cache was written.');

            return self::FAILURE;
        }

        $this->info('Collected 48/48 team squad payloads.');
        $this->line('Cache written: '.$result['cache_path']);

        return self::SUCCESS;
    }
}
