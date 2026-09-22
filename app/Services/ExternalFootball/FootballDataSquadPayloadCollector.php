<?php

namespace App\Services\ExternalFootball;

use App\Models\Team;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FootballDataSquadPayloadCollector
{
    public const CACHE_PATH = 'football-data/wc_squads_payloads.json';

    protected int $maxRetries = 3;

    protected mixed $sleeper = null;

    public function __construct(
        protected FootballDataClient $client
    ) {
    }

    public function setSleeper(callable $sleeper): self
    {
        $this->sleeper = $sleeper;

        return $this;
    }

    public function cachePath(): string
    {
        return storage_path('app/'.self::CACHE_PATH);
    }

    public function collect(?callable $progress = null): array
    {
        $teamsResult = $this->client->getWorldCupTeams();

        if (! $teamsResult['ok']) {
            return $this->failed([
                'API request failed'.($teamsResult['status'] ? ' with status '.$teamsResult['status'] : '').': '.$teamsResult['error'],
            ], 0, 0, []);
        }

        $sourceTeams = collect($teamsResult['data']['teams'] ?? [])
            ->filter(fn ($team) => is_array($team) && filled($team['id'] ?? null))
            ->map(fn (array $team) => $this->safeSourceTeam($team))
            ->values();

        if ($sourceTeams->count() !== 48) {
            return $this->failed([
                'External World Cup source team count mismatch: expected 48 teams, received '.$sourceTeams->count().'.',
            ], $sourceTeams->count(), 0, []);
        }

        $matched = [];
        $errors = [];

        foreach ($sourceTeams as $sourceTeam) {
            $localTeam = $this->findLocalTeam($sourceTeam['id']);

            if (! $localTeam) {
                $errors[] = 'No reconciled local FD team found for source team '.$sourceTeam['name'].'.';
                continue;
            }

            $matched[] = $sourceTeam;
        }

        if ($errors !== []) {
            return $this->failed($errors, $sourceTeams->count(), count($matched), []);
        }

        $payloads = [];
        $total = count($matched);

        foreach ($matched as $index => $sourceTeam) {
            $progress?->__invoke([
                'type' => 'progress',
                'current' => $index + 1,
                'total' => $total,
                'team' => $sourceTeam['name'],
                'source_id' => $sourceTeam['id'],
            ]);

            $detail = $this->fetchTeamDetailWithRetries($sourceTeam, $progress);

            if (! $detail['ok']) {
                return $this->failed($detail['errors'], $sourceTeams->count(), count($matched), $payloads, $detail['failed_team_detail'] ?? null);
            }

            $payloads[] = [
                'source_team' => $sourceTeam,
                'payload' => $this->safeTeamPayload($detail['data']),
            ];
        }

        if (count($payloads) !== 48) {
            return $this->failed([
                'Squad payload collection incomplete: expected 48 payloads, collected '.count($payloads).'.',
            ], $sourceTeams->count(), count($matched), $payloads);
        }

        $cache = [
            'provider' => 'football-data.org',
            'generated_at' => now()->toIso8601String(),
            'source_team_count' => $sourceTeams->count(),
            'matched_local_teams' => count($matched),
            'skipped_placeholders' => $this->placeholderCount(),
            'payload_count' => count($payloads),
            'teams' => $payloads,
        ];

        File::ensureDirectoryExists(dirname($this->cachePath()));
        File::put($this->cachePath(), json_encode($cache, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return [
            'ok' => true,
            'cache_path' => $this->cachePath(),
            'errors' => [],
            'summary' => [
                'source_teams' => $sourceTeams->count(),
                'matched_local_teams' => count($matched),
                'skipped_placeholders' => $this->placeholderCount(),
                'payload_count' => count($payloads),
                'players_detected' => collect($payloads)->sum(fn (array $team) => count($team['payload']['squad'] ?? [])),
                'coaches_detected' => collect($payloads)->filter(fn (array $team) => filled(data_get($team, 'payload.coach.name')))->count(),
                'cache_written' => true,
            ],
        ];
    }

    public function loadCompleteCache(): array
    {
        if (! File::exists($this->cachePath())) {
            return [
                'ok' => false,
                'cache' => null,
                'errors' => ['Football-data squad cache is missing at '.$this->cachePath().'.'],
            ];
        }

        $decoded = json_decode((string) File::get($this->cachePath()), true);

        if (! is_array($decoded)) {
            return [
                'ok' => false,
                'cache' => null,
                'errors' => ['Football-data squad cache is not valid JSON.'],
            ];
        }

        $errors = $this->validateCache($decoded);

        return [
            'ok' => $errors === [],
            'cache' => $errors === [] ? $decoded : null,
            'errors' => $errors,
        ];
    }

    /**
     * @return array<int, string>
     */
    public function validateCache(array $cache): array
    {
        $errors = [];
        $teams = $cache['teams'] ?? null;

        if (($cache['provider'] ?? null) !== 'football-data.org') {
            $errors[] = 'Football-data squad cache provider is invalid.';
        }

        if (($cache['source_team_count'] ?? null) !== 48) {
            $errors[] = 'Football-data squad cache source team count is incomplete.';
        }

        if (($cache['payload_count'] ?? null) !== 48) {
            $errors[] = 'Football-data squad cache payload count is incomplete.';
        }

        if (! is_array($teams) || count($teams) !== 48) {
            $errors[] = 'Football-data squad cache must contain exactly 48 team payloads.';
        } else {
            foreach ($teams as $index => $team) {
                if (! is_array($team) || ! is_array($team['source_team'] ?? null) || ! is_array($team['payload'] ?? null)) {
                    $errors[] = 'Football-data squad cache team payload at index '.$index.' is invalid.';
                    continue;
                }

                if (blank(data_get($team, 'source_team.id')) || blank(data_get($team, 'payload.id'))) {
                    $errors[] = 'Football-data squad cache team payload at index '.$index.' is missing source ids.';
                }
            }
        }

        return $errors;
    }

    protected function fetchTeamDetailWithRetries(array $sourceTeam, ?callable $progress): array
    {
        $retries = 0;

        while (true) {
            $detail = $this->client->getTeam($sourceTeam['id']);

            if ($detail['ok']) {
                return [
                    'ok' => true,
                    'data' => $detail['data'],
                    'errors' => [],
                ];
            }

            if ((int) $detail['status'] === 429 && $retries < $this->maxRetries) {
                $waitSeconds = $this->waitSeconds($detail);
                $retries++;

                $progress?->__invoke([
                    'type' => 'rate_limit',
                    'team' => $sourceTeam['name'],
                    'source_id' => $sourceTeam['id'],
                    'wait_seconds' => $waitSeconds,
                    'retry' => $retries,
                    'max_retries' => $this->maxRetries,
                ]);

                $this->sleep($waitSeconds);
                continue;
            }

            $message = 'Team '.$sourceTeam['name'].' detail request failed'
                .($detail['status'] ? ' with status '.$detail['status'] : '')
                .': '.$detail['error'];

            return [
                'ok' => false,
                'errors' => [$message],
                'failed_team_detail' => [
                    'source_id' => $sourceTeam['id'],
                    'name' => $sourceTeam['name'],
                    'status' => $detail['status'],
                    'error' => $detail['error'],
                ],
            ];
        }
    }

    protected function waitSeconds(array $detail): int
    {
        $retryAfter = data_get($detail, 'rate_limit.retry_after');

        if (is_numeric($retryAfter) && (int) $retryAfter >= 0) {
            return (int) $retryAfter + 5;
        }

        $error = (string) ($detail['error'] ?? '');

        if (preg_match('/Wait\s+(\d+)\s+seconds/i', $error, $matches) === 1) {
            return (int) $matches[1] + 5;
        }

        return 65;
    }

    protected function sleep(int $seconds): void
    {
        if (is_callable($this->sleeper)) {
            ($this->sleeper)($seconds);

            return;
        }

        sleep($seconds);
    }

    protected function failed(array $errors, int $sourceTeamCount, int $matchedLocalTeams, array $payloads, ?array $failedTeamDetail = null): array
    {
        return [
            'ok' => false,
            'cache_path' => $this->cachePath(),
            'errors' => $errors,
            'failed_team_detail' => $failedTeamDetail,
            'summary' => [
                'source_teams' => $sourceTeamCount,
                'matched_local_teams' => $matchedLocalTeams,
                'skipped_placeholders' => $this->placeholderCount(),
                'payload_count' => count($payloads),
                'cache_written' => false,
            ],
        ];
    }

    protected function safeSourceTeam(array $team): array
    {
        return [
            'id' => (int) $team['id'],
            'name' => (string) ($team['name'] ?? ''),
            'shortName' => $team['shortName'] ?? null,
            'tla' => filled($team['tla'] ?? null) ? Str::upper((string) $team['tla']) : null,
            'crest' => $team['crest'] ?? null,
        ];
    }

    protected function safeTeamPayload(array $payload): array
    {
        return [
            'id' => $payload['id'] ?? null,
            'name' => $payload['name'] ?? null,
            'tla' => $payload['tla'] ?? null,
            'squad' => collect($payload['squad'] ?? [])
                ->filter(fn ($player) => is_array($player) && filled($player['name'] ?? null))
                ->map(fn (array $player) => [
                    'id' => $player['id'] ?? null,
                    'name' => $player['name'] ?? null,
                    'position' => $player['position'] ?? null,
                    'dateOfBirth' => $player['dateOfBirth'] ?? null,
                    'nationality' => $player['nationality'] ?? null,
                    'shirtNumber' => $player['shirtNumber'] ?? null,
                ])
                ->values()
                ->all(),
            'coach' => is_array($payload['coach'] ?? null)
                ? [
                    'id' => $payload['coach']['id'] ?? null,
                    'name' => $payload['coach']['name'] ?? null,
                    'dateOfBirth' => $payload['coach']['dateOfBirth'] ?? null,
                    'nationality' => $payload['coach']['nationality'] ?? null,
                ]
                : null,
        ];
    }

    protected function findLocalTeam(int $sourceTeamId): ?Team
    {
        return Team::query()
            ->where('meta->source', 'football-data.org')
            ->where('meta->football_data->id', $sourceTeamId)
            ->where(function ($query) {
                $query->whereNull('meta->placeholder')
                    ->orWhere('meta->placeholder', false);
            })
            ->first();
    }

    protected function placeholderCount(): int
    {
        return Team::query()
            ->where('meta->source', 'football-data.org')
            ->where('meta->placeholder', true)
            ->count();
    }
}
