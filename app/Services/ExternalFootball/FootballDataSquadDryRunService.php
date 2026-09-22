<?php

namespace App\Services\ExternalFootball;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FootballDataSquadDryRunService
{
    public function __construct(
        protected FootballDataClient $client
    ) {
    }

    public function preview(): array
    {
        $teamsResult = $this->client->getWorldCupTeams();

        if (! $teamsResult['ok']) {
            return $this->emptyPreview([
                'API request failed'.($teamsResult['status'] ? ' with status '.$teamsResult['status'] : '').': '.$teamsResult['error'],
            ], $teamsResult['status']);
        }

        $sourceTeams = collect($teamsResult['data']['teams'] ?? [])
            ->filter(fn ($team) => is_array($team) && filled($team['name'] ?? null))
            ->map(fn (array $team) => $this->normalizeSourceTeam($team))
            ->values();

        $localTeams = $this->localFootballDataTeams();
        $placeholders = $this->localFootballDataPlaceholders();
        $matchedTeams = $sourceTeams
            ->map(fn (array $sourceTeam) => $this->matchSourceTeam($sourceTeam, $localTeams))
            ->filter(fn (array $mapping) => $mapping['local_team'] instanceof Team)
            ->values();
        $unmatchedSourceTeams = $sourceTeams
            ->reject(fn (array $sourceTeam) => $matchedTeams->contains(fn (array $mapping) => $mapping['source_team']['source_id'] === $sourceTeam['source_id']))
            ->values();

        $summary = [
            'source_teams' => $sourceTeams->count(),
            'matched_local_teams' => $matchedTeams->count(),
            'unmatched_source_teams' => $unmatchedSourceTeams->count(),
            'skipped_placeholders' => $placeholders->count(),
            'teams_with_squads' => 0,
            'total_players_detected' => 0,
            'players_would_create' => 0,
            'players_would_match_existing' => 0,
            'coaches_detected' => 0,
            'coaches_would_store_update' => 0,
            'blocked_coaches' => 0,
            'errors' => [],
        ];

        $teamPreviews = [];
        $playerPreviews = [];
        $coachPreviews = [];
        $errors = [];

        foreach ($matchedTeams as $mapping) {
            /** @var Team $localTeam */
            $localTeam = $mapping['local_team'];
            $sourceTeam = $mapping['source_team'];
            $sourceId = $sourceTeam['source_id'];

            if (! is_int($sourceId)) {
                $errors[] = 'Skipped team '.$sourceTeam['name'].' because football-data team id is missing.';
                continue;
            }

            $teamResult = $this->client->getTeam($sourceId);

            if (! $teamResult['ok']) {
                $errors[] = 'Team '.$sourceTeam['name'].' request failed'.($teamResult['status'] ? ' with status '.$teamResult['status'] : '').': '.$teamResult['error'];
                $teamPreviews[] = $this->teamPreview($sourceTeam, $localTeam, [], null, $teamResult['status']);
                continue;
            }

            $teamPayload = $teamResult['data'];
            $players = collect($teamPayload['squad'] ?? [])
                ->filter(fn ($player) => is_array($player) && filled($player['name'] ?? null))
                ->map(fn (array $player) => $this->normalizePlayer($player, $localTeam))
                ->values();
            $coach = $this->normalizeCoach($teamPayload['coach'] ?? null, $localTeam);

            if ($players->isNotEmpty()) {
                $summary['teams_with_squads']++;
            }

            foreach ($players as $player) {
                $summary['total_players_detected']++;
                $summary[$player['would_match_existing'] ? 'players_would_match_existing' : 'players_would_create']++;
                $playerPreviews[] = $player;
            }

            if ($coach) {
                $summary['coaches_detected']++;

                if ($coach['storage_target'] === 'blocked') {
                    $summary['blocked_coaches']++;
                } else {
                    $summary['coaches_would_store_update']++;
                }

                $coachPreviews[] = $coach;
            }

            $teamPreviews[] = $this->teamPreview($sourceTeam, $localTeam, $players->all(), $coach, $teamResult['status']);
        }

        $summary['errors'] = $errors;

        return [
            'ok' => $errors === [],
            'status' => $teamsResult['status'],
            'summary' => $summary,
            'team_mapping' => [
                'source_sample' => $sourceTeams->take(10)->all(),
                'matched' => $matchedTeams->map(fn (array $mapping) => [
                    'source_id' => $mapping['source_team']['source_id'],
                    'source_name' => $mapping['source_team']['name'],
                    'source_code' => $mapping['source_team']['code'],
                    'local_id' => $mapping['local_team']->id,
                    'local_name' => $mapping['local_team']->name,
                    'local_code' => $mapping['local_team']->code,
                ])->take(12)->all(),
                'unmatched' => $unmatchedSourceTeams->take(12)->all(),
            ],
            'teams_preview' => array_slice($teamPreviews, 0, 12),
            'players_preview' => array_slice($playerPreviews, 0, 12),
            'coaches_preview' => array_slice($coachPreviews, 0, 12),
            'errors' => $errors,
            'rate_limit' => $teamsResult['rate_limit'],
        ];
    }

    protected function emptyPreview(array $errors, ?int $status = null): array
    {
        return [
            'ok' => false,
            'status' => $status,
            'summary' => [
                'source_teams' => 0,
                'matched_local_teams' => 0,
                'unmatched_source_teams' => 0,
                'skipped_placeholders' => $this->localFootballDataPlaceholders()->count(),
                'teams_with_squads' => 0,
                'total_players_detected' => 0,
                'players_would_create' => 0,
                'players_would_match_existing' => 0,
                'coaches_detected' => 0,
                'coaches_would_store_update' => 0,
                'blocked_coaches' => 0,
                'errors' => $errors,
            ],
            'team_mapping' => ['source_sample' => [], 'matched' => [], 'unmatched' => []],
            'teams_preview' => [],
            'players_preview' => [],
            'coaches_preview' => [],
            'errors' => $errors,
            'rate_limit' => [],
        ];
    }

    protected function normalizeSourceTeam(array $team): array
    {
        return [
            'source_id' => isset($team['id']) && is_numeric($team['id']) ? (int) $team['id'] : null,
            'name' => $team['name'] ?? null,
            'short_name' => $team['shortName'] ?? null,
            'code' => $team['tla'] ?? null,
            'crest_available' => filled($team['crest'] ?? null),
        ];
    }

    protected function normalizePlayer(array $player, Team $team): array
    {
        $existing = $this->findExistingPlayer($player, $team);
        $sourceId = isset($player['id']) && is_numeric($player['id']) ? (int) $player['id'] : null;

        return [
            'source_person_id' => $sourceId,
            'name' => $player['name'] ?? null,
            'position' => $this->normalizePosition($player['position'] ?? null),
            'source_position' => $player['position'] ?? null,
            'date_of_birth' => $player['dateOfBirth'] ?? null,
            'nationality' => $player['nationality'] ?? null,
            'shirt_number' => $player['shirtNumber'] ?? null,
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'code' => $team->code,
            ],
            'would_create' => ! $existing,
            'would_match_existing' => (bool) $existing,
            'existing_id' => $existing?->id,
            'duplicate_key' => $sourceId ? 'source:'.$sourceId : 'name-team:'.Str::lower(($player['name'] ?? '').'|'.$team->id),
        ];
    }

    protected function normalizeCoach(mixed $coach, Team $team): ?array
    {
        if (! is_array($coach) || blank($coach['name'] ?? null)) {
            return null;
        }

        $sourceId = isset($coach['id']) && is_numeric($coach['id']) ? (int) $coach['id'] : null;
        $storageTarget = $this->coachStorageTarget();

        return [
            'source_person_id' => $sourceId,
            'name' => $coach['name'] ?? null,
            'date_of_birth' => $coach['dateOfBirth'] ?? null,
            'nationality' => $coach['nationality'] ?? null,
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'code' => $team->code,
            ],
            'storage_target' => $storageTarget,
            'would_store_update' => $storageTarget !== 'blocked',
            'blocked_reason' => $storageTarget === 'blocked' ? 'No coach table, team coach field, or team meta storage is available.' : null,
            'duplicate_key' => $sourceId ? 'source:'.$sourceId : 'name-team:'.Str::lower(($coach['name'] ?? '').'|'.$team->id),
        ];
    }

    protected function teamPreview(array $sourceTeam, Team $localTeam, array $players, ?array $coach, ?int $status): array
    {
        return [
            'source_id' => $sourceTeam['source_id'],
            'source_name' => $sourceTeam['name'],
            'source_code' => $sourceTeam['code'],
            'local_id' => $localTeam->id,
            'local_name' => $localTeam->name,
            'local_code' => $localTeam->code,
            'status' => $status,
            'squad_count' => count($players),
            'coach_detected' => (bool) $coach,
        ];
    }

    protected function matchSourceTeam(array $sourceTeam, Collection $localTeams): array
    {
        $sourceId = $sourceTeam['source_id'];
        $sourceCode = Str::upper((string) ($sourceTeam['code'] ?? ''));
        $sourceName = Str::lower((string) ($sourceTeam['name'] ?? ''));

        $localTeam = $localTeams->first(function (Team $team) use ($sourceId, $sourceCode, $sourceName) {
            $meta = is_array($team->meta) ? $team->meta : [];

            if ($sourceId && ((int) data_get($meta, 'football_data.id') === $sourceId || (int) data_get($meta, 'source_team_id') === $sourceId)) {
                return true;
            }

            if ($sourceCode !== '' && Str::upper((string) $team->code) === $sourceCode) {
                return true;
            }

            return $sourceName !== '' && Str::lower($team->name) === $sourceName;
        });

        return [
            'source_team' => $sourceTeam,
            'local_team' => $localTeam,
        ];
    }

    protected function localFootballDataTeams(): Collection
    {
        return Team::query()
            ->where('meta->source', 'football-data.org')
            ->where(function ($query) {
                $query->whereNull('meta->placeholder')
                    ->orWhere('meta->placeholder', false);
            })
            ->orderBy('name')
            ->get();
    }

    protected function localFootballDataPlaceholders(): Collection
    {
        return Team::query()
            ->where('meta->source', 'football-data.org')
            ->where('meta->placeholder', true)
            ->orderBy('name')
            ->get();
    }

    protected function findExistingPlayer(array $player, Team $team): ?Player
    {
        $name = trim((string) ($player['name'] ?? ''));

        if ($name === '') {
            return null;
        }

        return Player::query()
            ->where('team_id', $team->id)
            ->whereRaw('LOWER(display_name) = ?', [Str::lower($name)])
            ->first();
    }

    protected function coachStorageTarget(): string
    {
        if (Schema::hasTable('coaches')) {
            return 'coach table';
        }

        if (Schema::hasColumn('teams', 'coach_name')) {
            return 'teams.coach_name';
        }

        if (Schema::hasColumn('teams', 'meta')) {
            return 'team meta';
        }

        return 'blocked';
    }

    protected function normalizePosition(?string $position): ?string
    {
        $value = Str::lower((string) $position);

        return match (true) {
            str_contains($value, 'keeper') => 'goalkeeper',
            str_contains($value, 'defence'), str_contains($value, 'defender'), str_contains($value, 'back') => 'defender',
            str_contains($value, 'midfield') => 'midfielder',
            str_contains($value, 'offence'), str_contains($value, 'forward'), str_contains($value, 'winger'), str_contains($value, 'striker') => 'forward',
            default => null,
        };
    }
}
