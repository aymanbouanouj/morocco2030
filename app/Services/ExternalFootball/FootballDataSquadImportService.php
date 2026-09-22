<?php

namespace App\Services\ExternalFootball;

use App\Models\MatchFixture;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class FootballDataSquadImportService
{
    public function __construct(
        protected FootballDataClient $client
    ) {
    }

    public function import(): array
    {
        $dbCountsBefore = $this->dbCounts();
        $teamsResult = $this->client->getWorldCupTeams();

        if (! $teamsResult['ok']) {
            return $this->blockedResult([
                'API request failed'.($teamsResult['status'] ? ' with status '.$teamsResult['status'] : '').': '.$teamsResult['error'],
            ], $teamsResult['status'], $dbCountsBefore);
        }

        $sourceTeams = collect($teamsResult['data']['teams'] ?? [])
            ->filter(fn ($team) => is_array($team) && filled($team['id'] ?? null))
            ->map(fn (array $team) => $this->normalizeSourceTeam($team))
            ->values();

        if ($sourceTeams->count() !== 48) {
            return $this->blockedResult([
                'External World Cup source team count mismatch: expected 48 teams, received '.$sourceTeams->count().'.',
            ], $teamsResult['status'], $dbCountsBefore, $sourceTeams->count());
        }

        $matchedTeams = [];
        $errors = [];

        foreach ($sourceTeams as $sourceTeam) {
            $localTeam = $this->findLocalTeam($sourceTeam['source_id']);

            if (! $localTeam) {
                $errors[] = 'No reconciled local FD team found for source team '.$sourceTeam['name'].'.';
                continue;
            }

            $matchedTeams[] = [
                'source_team' => $sourceTeam,
                'local_team' => $localTeam,
            ];
        }

        if ($errors !== []) {
            return $this->blockedResult($errors, $teamsResult['status'], $dbCountsBefore, $sourceTeams->count(), count($matchedTeams));
        }

        $teamDetails = [];
        $failedDetails = [];
        $playersDetected = 0;
        $coachesDetected = 0;

        foreach ($matchedTeams as $mapping) {
            $sourceTeam = $mapping['source_team'];
            $detail = $this->client->getTeam($sourceTeam['source_id']);

            if (! $detail['ok']) {
                $failedDetails[] = [
                    'source_id' => $sourceTeam['source_id'],
                    'name' => $sourceTeam['name'],
                    'status' => $detail['status'],
                    'error' => $detail['error'],
                ];
                break;
            }

            $squad = collect($detail['data']['squad'] ?? [])
                ->filter(fn ($player) => is_array($player) && filled($player['name'] ?? null))
                ->values();
            $coach = $detail['data']['coach'] ?? null;

            $playersDetected += $squad->count();

            if (is_array($coach) && filled($coach['name'] ?? null)) {
                $coachesDetected++;
            }

            $teamDetails[] = [
                'source_team' => $sourceTeam,
                'local_team' => $mapping['local_team'],
                'squad' => $squad->all(),
                'coach' => is_array($coach) ? $coach : null,
            ];
        }

        if ($failedDetails !== []) {
            $failed = $failedDetails[0];

            return $this->blockedResult([
                'Team '.$failed['name'].' detail request failed'.($failed['status'] ? ' with status '.$failed['status'] : '').': '.$failed['error'],
            ], $failed['status'], $dbCountsBefore, $sourceTeams->count(), count($matchedTeams), count($teamDetails), $playersDetected, $coachesDetected, $failedDetails);
        }

        return $this->writeTeamDetails(
            dbCountsBefore: $dbCountsBefore,
            status: $teamsResult['status'],
            rateLimit: $teamsResult['rate_limit'],
            sourceTeamCount: $sourceTeams->count(),
            matchedTeamCount: count($matchedTeams),
            teamDetails: $teamDetails,
            playersDetected: $playersDetected,
            coachesDetected: $coachesDetected
        );
    }

    public function importFromPayloadCache(array $cache): array
    {
        $dbCountsBefore = $this->dbCounts();
        $errors = $this->validatePayloadCache($cache);

        if ($errors !== []) {
            return $this->blockedResult($errors, null, $dbCountsBefore);
        }

        $sourceTeams = collect($cache['teams'])
            ->map(fn (array $team) => [
                'source_id' => (int) data_get($team, 'source_team.id'),
                'name' => (string) data_get($team, 'source_team.name'),
                'tla' => filled(data_get($team, 'source_team.tla')) ? Str::upper((string) data_get($team, 'source_team.tla')) : null,
            ])
            ->values();

        $matchedTeams = [];

        foreach ($sourceTeams as $index => $sourceTeam) {
            $localTeam = $this->findLocalTeam($sourceTeam['source_id']);

            if (! $localTeam) {
                return $this->blockedResult([
                    'No reconciled local FD team found for cached source team '.$sourceTeam['name'].'.',
                ], null, $dbCountsBefore, $sourceTeams->count(), count($matchedTeams));
            }

            $payload = $cache['teams'][$index]['payload'];
            $squad = collect($payload['squad'] ?? [])
                ->filter(fn ($player) => is_array($player) && filled($player['name'] ?? null))
                ->values();
            $coach = $payload['coach'] ?? null;

            $matchedTeams[] = [
                'source_team' => $sourceTeam,
                'local_team' => $localTeam,
                'squad' => $squad->all(),
                'coach' => is_array($coach) ? $coach : null,
            ];
        }

        if (count($matchedTeams) !== 48) {
            return $this->blockedResult([
                'Cached squad payload import requires 48 matched local teams, found '.count($matchedTeams).'.',
            ], null, $dbCountsBefore, $sourceTeams->count(), count($matchedTeams));
        }

        return $this->writeTeamDetails(
            dbCountsBefore: $dbCountsBefore,
            status: 200,
            rateLimit: [],
            sourceTeamCount: $sourceTeams->count(),
            matchedTeamCount: count($matchedTeams),
            teamDetails: $matchedTeams,
            playersDetected: collect($matchedTeams)->sum(fn (array $detail) => count($detail['squad'])),
            coachesDetected: collect($matchedTeams)->filter(fn (array $detail) => is_array($detail['coach']) && filled($detail['coach']['name'] ?? null))->count()
        );
    }

    protected function writeTeamDetails(
        array $dbCountsBefore,
        ?int $status,
        array $rateLimit,
        int $sourceTeamCount,
        int $matchedTeamCount,
        array $teamDetails,
        int $playersDetected,
        int $coachesDetected
    ): array {
        $summary = [
            'source_teams' => $sourceTeamCount,
            'matched_local_teams' => $matchedTeamCount,
            'skipped_placeholders' => $this->placeholderCount(),
            'team_details_fetched' => count($teamDetails),
            'players_detected' => $playersDetected,
            'players_created' => 0,
            'players_matched_existing' => 0,
            'players_updated_safely' => 0,
            'players_missing_position' => 0,
            'coaches_detected' => $coachesDetected,
            'coaches_updated' => 0,
            'teams_without_coach' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        try {
            DB::transaction(function () use ($teamDetails, &$summary): void {
                foreach ($teamDetails as $detail) {
                    /** @var Team $team */
                    $team = $detail['local_team'];

                    foreach ($detail['squad'] as $sourcePlayer) {
                        $normalized = $this->normalizePlayer($sourcePlayer, $team);

                        if ($normalized['position'] === 'unknown') {
                            $summary['players_missing_position']++;
                        }

                        $player = $this->findExistingPlayer($normalized, $team);
                        $attributes = $this->playerAttributes($normalized, $team, $player);

                        if ($player) {
                            $summary['players_matched_existing']++;
                            $player->fill($attributes);

                            if ($player->isDirty()) {
                                $player->save();
                                $summary['players_updated_safely']++;
                            }
                        } else {
                            Player::query()->create($attributes);
                            $summary['players_created']++;
                        }
                    }

                    $coach = $this->normalizeCoach($detail['coach']);

                    if ($coach) {
                        $updates = [];

                        if ($team->coach_name !== $coach['name']) {
                            $updates['coach_name'] = $coach['name'];
                        }

                        $meta = is_array($team->meta) ? $team->meta : [];
                        $currentCoach = data_get($meta, 'football_data.coach');
                        data_set($meta, 'football_data.coach', $coach);

                        if ($currentCoach !== $coach) {
                            $updates['meta'] = $meta;
                        }

                        if ($updates !== []) {
                            $team->update($updates);
                            $summary['coaches_updated']++;
                        }
                    } else {
                        $summary['teams_without_coach']++;
                    }
                }
            });
        } catch (Throwable $exception) {
            report($exception);

            return $this->blockedResult([
                'Football-data squad import failed and was rolled back.',
            ], $status, $dbCountsBefore, $sourceTeamCount, $matchedTeamCount, count($teamDetails), $playersDetected, $coachesDetected);
        }

        return [
            'ok' => $summary['errors'] === [],
            'status' => $status,
            'summary' => $summary,
            'db_counts_before' => $dbCountsBefore,
            'db_counts_after' => $this->dbCounts(),
            'errors' => $summary['errors'],
            'failed_team_details' => [],
            'rate_limit' => $rateLimit,
        ];
    }

    protected function validatePayloadCache(array $cache): array
    {
        $errors = [];

        if (($cache['provider'] ?? null) !== 'football-data.org') {
            $errors[] = 'Football-data squad cache provider is invalid.';
        }

        if (($cache['source_team_count'] ?? null) !== 48) {
            $errors[] = 'Football-data squad cache source team count is incomplete.';
        }

        if (($cache['payload_count'] ?? null) !== 48) {
            $errors[] = 'Football-data squad cache payload count is incomplete.';
        }

        if (! is_array($cache['teams'] ?? null) || count($cache['teams']) !== 48) {
            $errors[] = 'Football-data squad cache must contain exactly 48 team payloads.';
        }

        return $errors;
    }

    protected function blockedResult(
        array $errors,
        ?int $status,
        array $dbCountsBefore,
        int $sourceTeams = 0,
        int $matchedLocalTeams = 0,
        int $teamDetailsFetched = 0,
        int $playersDetected = 0,
        int $coachesDetected = 0,
        array $failedTeamDetails = []
    ): array {
        return [
            'ok' => false,
            'status' => $status,
            'summary' => [
                'source_teams' => $sourceTeams,
                'matched_local_teams' => $matchedLocalTeams,
                'skipped_placeholders' => $this->placeholderCount(),
                'team_details_fetched' => $teamDetailsFetched,
                'players_detected' => $playersDetected,
                'players_created' => 0,
                'players_matched_existing' => 0,
                'players_updated_safely' => 0,
                'players_missing_position' => 0,
                'coaches_detected' => $coachesDetected,
                'coaches_updated' => 0,
                'teams_without_coach' => 0,
                'skipped' => 0,
                'errors' => $errors,
            ],
            'db_counts_before' => $dbCountsBefore,
            'db_counts_after' => $this->dbCounts(),
            'errors' => $errors,
            'failed_team_details' => $failedTeamDetails,
            'rate_limit' => [],
        ];
    }

    protected function normalizeSourceTeam(array $team): array
    {
        return [
            'source_id' => (int) $team['id'],
            'name' => (string) ($team['name'] ?? ''),
            'tla' => filled($team['tla'] ?? null) ? Str::upper((string) $team['tla']) : null,
        ];
    }

    protected function normalizePlayer(array $player, Team $team): array
    {
        $sourceId = filled($player['id'] ?? null) ? (string) $player['id'] : null;
        $name = trim((string) ($player['name'] ?? ''));
        [$firstName, $lastName] = $this->splitName($name);
        $position = $this->normalizePosition($player['position'] ?? null);
        $nationality = filled($player['nationality'] ?? null) ? (string) $player['nationality'] : null;

        return [
            'source_id' => $sourceId,
            'name' => $name,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'slug' => $this->playerSlug($team, $name, $sourceId),
            'shirt_number' => is_numeric($player['shirtNumber'] ?? null) ? (int) $player['shirtNumber'] : null,
            'position' => $position,
            'date_of_birth' => filled($player['dateOfBirth'] ?? null) ? $player['dateOfBirth'] : null,
            'nationality_code' => is_string($nationality) && strlen($nationality) === 2 ? Str::upper($nationality) : null,
            'external_payload' => [
                'id' => $sourceId,
                'name' => $name,
                'position' => $player['position'] ?? null,
                'dateOfBirth' => $player['dateOfBirth'] ?? null,
                'nationality' => $nationality,
                'shirtNumber' => $player['shirtNumber'] ?? null,
                'team' => [
                    'id' => data_get($team->meta, 'football_data.id'),
                    'name' => $team->name,
                    'tla' => data_get($team->meta, 'football_data.tla'),
                ],
            ],
        ];
    }

    protected function normalizeCoach(mixed $coach): ?array
    {
        if (! is_array($coach) || blank($coach['name'] ?? null)) {
            return null;
        }

        return [
            'id' => filled($coach['id'] ?? null) ? (string) $coach['id'] : null,
            'name' => (string) $coach['name'],
            'dateOfBirth' => $coach['dateOfBirth'] ?? null,
            'nationality' => $coach['nationality'] ?? null,
        ];
    }

    protected function playerAttributes(array $normalized, Team $team, ?Player $existing = null): array
    {
        return [
            'team_id' => $team->id,
            'display_name' => $normalized['name'],
            'first_name' => $normalized['first_name'],
            'last_name' => $normalized['last_name'],
            'slug' => $existing?->slug ?: $this->uniqueSlug($normalized['slug']),
            'shirt_number' => $normalized['shirt_number'],
            'position' => $normalized['position'],
            'date_of_birth' => $normalized['date_of_birth'],
            'nationality_code' => $normalized['nationality_code'],
            'height_cm' => $existing?->height_cm,
            'weight_kg' => $existing?->weight_kg,
            'bio' => $existing?->bio,
            'is_captain' => $existing?->is_captain ?? false,
            'status' => 'active',
            'external_provider' => 'football-data.org',
            'external_id' => $normalized['source_id'],
            'external_payload' => $normalized['external_payload'],
        ];
    }

    protected function findExistingPlayer(array $normalized, Team $team): ?Player
    {
        if (filled($normalized['source_id'])) {
            $external = Player::query()
                ->where('external_provider', 'football-data.org')
                ->where('external_id', $normalized['source_id'])
                ->first();

            if ($external) {
                return $external;
            }
        }

        $slug = $normalized['slug'];
        $bySlug = Player::query()->where('slug', $slug)->first();

        if ($bySlug) {
            return $bySlug;
        }

        return Player::query()
            ->where('team_id', $team->id)
            ->whereRaw('LOWER(display_name) = ?', [Str::lower($normalized['name'])])
            ->first();
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

    protected function dbCounts(): array
    {
        return [
            'teams' => Team::query()->count(),
            'players' => Player::query()->count(),
            'matches' => MatchFixture::query()->count(),
            'fd_wc_matches' => MatchFixture::query()->where('code', 'like', 'FD-WC-%')->count(),
            'real_fd_teams' => Team::query()
                ->where('meta->source', 'football-data.org')
                ->where(function ($query) {
                    $query->whereNull('meta->placeholder')
                        ->orWhere('meta->placeholder', false);
                })
                ->count(),
            'placeholders' => $this->placeholderCount(),
            'teams_with_coach_name' => Team::query()->whereNotNull('coach_name')->where('coach_name', '<>', '')->count(),
            'cities' => DB::table('cities')->count(),
            'stadiums' => DB::table('stadiums')->count(),
        ];
    }

    protected function placeholderCount(): int
    {
        return Team::query()
            ->where('meta->source', 'football-data.org')
            ->where('meta->placeholder', true)
            ->count();
    }

    protected function normalizePosition(mixed $position): string
    {
        $value = Str::lower((string) $position);

        return match (true) {
            str_contains($value, 'keeper') => 'goalkeeper',
            str_contains($value, 'defence'), str_contains($value, 'defender'), str_contains($value, 'back') => 'defender',
            str_contains($value, 'midfield') => 'midfielder',
            str_contains($value, 'offence'), str_contains($value, 'forward'), str_contains($value, 'winger'), str_contains($value, 'striker') => 'forward',
            default => 'unknown',
        };
    }

    protected function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        if (! is_array($parts) || count($parts) === 0 || $parts[0] === '') {
            return [null, null];
        }

        return [$parts[0], $parts[1] ?? null];
    }

    protected function playerSlug(Team $team, string $name, ?string $sourceId): string
    {
        if (filled($sourceId)) {
            return 'football-data-'.$sourceId;
        }

        return Str::slug($team->code.'-'.$name) ?: 'football-data-player';
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base;
        $suffix = 1;

        while (Player::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
