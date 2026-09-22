<?php

namespace App\Services\ExternalFootball;

use App\Models\AuditLog;
use App\Models\Group;
use App\Models\MatchFixture;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class FootballDataWorldCupImportService
{
    public function __construct(
        protected FootballDataClient $client,
        protected MoroccoVenueMapper $venueMapper
    ) {
    }

    public function import(?int $userId = null, ?string $ipAddress = null, ?string $userAgent = null, ?string $routeName = null): array
    {
        $result = $this->client->getWorldCupMatches();

        if (! $result['ok']) {
            return $this->emptyImport([
                'API request failed'.($result['status'] ? ' with status '.$result['status'] : '').': '.$result['error'],
            ], $result['status']);
        }

        $payload = $result['data'];
        $matches = collect($payload['matches'] ?? [])
            ->filter(fn ($match) => is_array($match))
            ->values();

        $competition = $payload['competition'] ?? [];
        $teams = $this->extractTeams($matches);
        $dbCountsBefore = $this->dbCounts();

        if ($matches->isEmpty()) {
            return $this->emptyImport(['External World Cup data source returned no matches.'], $result['status'], $competition, $dbCountsBefore);
        }

        $mappingErrors = $this->mappingErrors($matches);

        if ($mappingErrors !== []) {
            return $this->emptyImport($mappingErrors, $result['status'], $competition, $dbCountsBefore);
        }

        $summary = [
            'source_matches_total' => $matches->count(),
            'teams_detected_total' => $teams->count() + $this->placeholderTeamSources($matches)->count(),
            'teams_created' => 0,
            'teams_matched_existing' => 0,
            'matches_created' => 0,
            'matches_matched_existing' => 0,
            'matches_updated_safely' => 0,
            'matches_mapped_to_moroccan_venues' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        try {
            DB::transaction(function () use (
                $teams,
                $matches,
                $competition,
                &$summary,
                $dbCountsBefore,
                $userId,
                $ipAddress,
                $userAgent,
                $routeName
            ): void {
                $teamMap = [];

                foreach ($teams as $team) {
                    $imported = $this->importTeam($team);
                    $teamMap[$this->teamKey($team)] = $imported['team'];
                    $summary[$imported['created'] ? 'teams_created' : 'teams_matched_existing']++;
                }

                foreach ($matches as $index => $match) {
                    $homeTeam = $this->resolveMatchTeam($match, 'home', $teamMap, $summary);
                    $awayTeam = $this->resolveMatchTeam($match, 'away', $teamMap, $summary);

                    $imported = $this->importMatch($match, $index, $homeTeam, $awayTeam);
                    $summary[$imported['created'] ? 'matches_created' : 'matches_matched_existing']++;

                    if ($imported['updated']) {
                        $summary['matches_updated_safely']++;
                    }

                    if ($imported['mapped']) {
                        $summary['matches_mapped_to_moroccan_venues']++;
                    }
                }

                if (class_exists(AuditLog::class)) {
                    AuditLog::query()->create([
                        'user_id' => $userId,
                        'auditable_type' => MatchFixture::class,
                        'auditable_id' => 0,
                        'action' => 'football_data_world_cup_import',
                        'old_values' => ['db_counts_before' => $dbCountsBefore],
                        'new_values' => [
                            'competition' => [
                                'name' => $competition['name'] ?? 'FIFA World Cup',
                                'code' => $competition['code'] ?? 'WC',
                            ],
                            'summary' => $summary,
                        ],
                        'ip_address' => $ipAddress,
                        'user_agent' => $userAgent,
                        'route_name' => $routeName,
                        'occurred_at' => now(),
                    ]);
                }
            });
        } catch (Throwable $exception) {
            report($exception);

            return $this->emptyImport([
                'External World Cup import failed and was rolled back.',
            ], $result['status'], $competition, $dbCountsBefore);
        }

        return [
            'ok' => $summary['errors'] === [],
            'status' => $result['status'],
            'competition' => [
                'id' => $competition['id'] ?? null,
                'name' => $competition['name'] ?? 'FIFA World Cup',
                'code' => $competition['code'] ?? 'WC',
            ],
            'summary' => $summary,
            'db_counts_before' => $dbCountsBefore,
            'db_counts_after' => $this->dbCounts(),
            'errors' => $summary['errors'],
            'rate_limit' => $result['rate_limit'],
        ];
    }

    protected function emptyImport(array $errors, ?int $status = null, array $competition = [], ?array $dbCountsBefore = null): array
    {
        $dbCountsBefore ??= $this->dbCounts();

        return [
            'ok' => false,
            'status' => $status,
            'competition' => [
                'id' => $competition['id'] ?? null,
                'name' => $competition['name'] ?? 'FIFA World Cup',
                'code' => $competition['code'] ?? 'WC',
            ],
            'summary' => [
                'source_matches_total' => 0,
                'teams_detected_total' => 0,
                'teams_created' => 0,
                'teams_matched_existing' => 0,
                'matches_created' => 0,
                'matches_matched_existing' => 0,
                'matches_updated_safely' => 0,
                'matches_mapped_to_moroccan_venues' => 0,
                'skipped' => 0,
                'errors' => $errors,
            ],
            'db_counts_before' => $dbCountsBefore,
            'db_counts_after' => $this->dbCounts(),
            'errors' => $errors,
            'rate_limit' => [],
        ];
    }

    protected function dbCounts(): array
    {
        return [
            'teams' => Team::query()->count(),
            'matches' => MatchFixture::query()->count(),
            'cities' => DB::table('cities')->count(),
            'stadiums' => DB::table('stadiums')->count(),
            'audit_logs' => Schema::hasTable('audit_logs') ? AuditLog::query()->count() : null,
        ];
    }

    protected function extractTeams(Collection $matches): Collection
    {
        return $matches
            ->flatMap(fn (array $match) => [$match['homeTeam'] ?? null, $match['awayTeam'] ?? null])
            ->filter(fn ($team) => is_array($team) && filled($team['name'] ?? null))
            ->map(fn (array $team) => [
                'source_id' => $team['id'] ?? null,
                'name' => $team['name'] ?? null,
                'code' => $team['tla'] ?? $team['shortName'] ?? null,
                'crest' => $team['crest'] ?? null,
            ])
            ->unique(fn (array $team) => $this->teamKey($team))
            ->values();
    }

    protected function placeholderTeamSources(Collection $matches): Collection
    {
        return $matches
            ->flatMap(function (array $match) {
                return collect(['home', 'away'])
                    ->filter(fn (string $slot) => ! $this->hasNamedTeam($match[$slot.'Team'] ?? []))
                    ->map(fn (string $slot) => $this->placeholderTeamSource($match, $slot));
            })
            ->unique(fn (array $team) => $this->teamKey($team))
            ->values();
    }

    protected function importTeam(array $sourceTeam): array
    {
        $existing = $this->findExistingTeam($sourceTeam);

        if ($existing) {
            return ['team' => $existing, 'created' => false];
        }

        $code = $this->teamCode($sourceTeam);
        $name = trim((string) $sourceTeam['name']);

        $team = Team::query()->create([
            'group_id' => $this->groupForTeam()->id,
            'name' => $name,
            'short_name' => Str::limit($sourceTeam['code'] ?: $name, 32, ''),
            'code' => $code,
            'slug' => $this->uniqueSlug('teams', $name),
            'federation_name' => null,
            'team_type' => 'national',
            'status' => 'active',
            'meta' => [
                'source' => 'football-data.org',
                'source_competition' => 'WC',
                'source_team_id' => $sourceTeam['source_id'] ?? null,
                'crest' => $sourceTeam['crest'] ?? null,
                'placeholder' => (bool) ($sourceTeam['placeholder'] ?? false),
                'import_label' => 'imported demo football data',
            ],
        ]);

        return ['team' => $team, 'created' => true];
    }

    protected function resolveMatchTeam(array $match, string $slot, array &$teamMap, array &$summary): Team
    {
        $sourceTeam = $match[$slot.'Team'] ?? [];

        if (! $this->hasNamedTeam($sourceTeam)) {
            $sourceTeam = $this->placeholderTeamSource($match, $slot);
        }

        $key = $this->teamKey($sourceTeam);

        if (! isset($teamMap[$key])) {
            $imported = $this->importTeam([
                'source_id' => $sourceTeam['source_id'] ?? $sourceTeam['id'] ?? null,
                'name' => $sourceTeam['name'],
                'code' => $sourceTeam['code'] ?? $sourceTeam['tla'] ?? null,
                'crest' => $sourceTeam['crest'] ?? null,
                'placeholder' => $sourceTeam['placeholder'] ?? false,
            ]);

            $teamMap[$key] = $imported['team'];
            $summary[$imported['created'] ? 'teams_created' : 'teams_matched_existing']++;
        }

        return $teamMap[$key];
    }

    protected function importMatch(array $sourceMatch, int $index, Team $homeTeam, Team $awayTeam): array
    {
        $mapping = $this->venueMapper->map($index);
        $existing = $this->findExistingMatch($sourceMatch, $homeTeam->id, $awayTeam->id);
        $score = $sourceMatch['score']['fullTime'] ?? [];
        $attributes = [
            'stadium_id' => $mapping['local_stadium_id'],
            'city_id' => DB::table('stadiums')->where('id', $mapping['local_stadium_id'])->value('city_id'),
            'group_id' => $this->groupForMatch($sourceMatch)?->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'code' => $this->matchCode($sourceMatch),
            'slug' => Str::slug($this->matchCode($sourceMatch)),
            'stage_type' => $this->normalizeStage($sourceMatch),
            'round_number' => $this->roundNumber($sourceMatch),
            'match_date' => Carbon::parse($sourceMatch['utcDate'])->utc(),
            'timezone' => 'UTC',
            'status' => $this->normalizeStatus($sourceMatch['status'] ?? null),
            'home_score' => $score['home'] ?? null,
            'away_score' => $score['away'] ?? null,
            'published_at' => now(),
            'meta' => [
                'source' => 'football-data.org',
                'source_competition' => 'WC',
                'source_match_id' => $sourceMatch['id'] ?? null,
                'source_stage' => $sourceMatch['stage'] ?? null,
                'source_group' => $sourceMatch['group'] ?? null,
                'source_matchday' => $sourceMatch['matchday'] ?? null,
                'venue_mapping' => [
                    'label' => 'Morocco 2030 local venue mapping',
                    'mapping_index' => $mapping['mapping_index'],
                    'local_stadium_name' => $mapping['local_stadium_name'],
                    'city_name' => $mapping['city_name'],
                ],
                'import_label' => 'imported demo football data',
            ],
        ];

        if (! $existing) {
            MatchFixture::query()->create($attributes);

            return ['created' => true, 'updated' => false, 'mapped' => true];
        }

        $updates = [];

        foreach (['stadium_id', 'city_id'] as $field) {
            if (blank($existing->{$field})) {
                $updates[$field] = $attributes[$field];
            }
        }

        if ($this->statusCanUpdate($existing->status, $attributes['status'])) {
            $updates['status'] = $attributes['status'];
        }

        foreach (['home_score', 'away_score'] as $field) {
            if ($existing->{$field} === null && $attributes[$field] !== null) {
                $updates[$field] = $attributes[$field];
            }
        }

        $meta = is_array($existing->meta) ? $existing->meta : [];
        $updates['meta'] = array_replace_recursive($meta, $attributes['meta']);

        if ($updates !== []) {
            $existing->update($updates);
        }

        return ['created' => false, 'updated' => $updates !== [], 'mapped' => ! blank($existing->stadium_id) || isset($updates['stadium_id'])];
    }

    protected function mappingErrors(Collection $matches): array
    {
        return $matches
            ->map(fn (array $match, int $index) => $this->venueMapper->map($index))
            ->filter(fn (array $mapping) => $mapping['blocked'] ?? false)
            ->map(fn (array $mapping) => $mapping['error'] ?? 'Local Moroccan venue mapping failed.')
            ->unique()
            ->values()
            ->all();
    }

    protected function findExistingTeam(array $sourceTeam): ?Team
    {
        $name = $sourceTeam['name'] ?? null;
        $code = $sourceTeam['code'] ?? $sourceTeam['tla'] ?? null;

        return Team::query()
            ->when(filled($code) && Schema::hasColumn('teams', 'code'), function ($query) use ($code) {
                $query->where('code', Str::upper($code));
            })
            ->when(filled($name), function ($query) use ($name, $code) {
                $method = filled($code) && Schema::hasColumn('teams', 'code') ? 'orWhere' : 'where';
                $query->{$method}(DB::raw('LOWER(name)'), Str::lower($name));
            })
            ->first();
    }

    protected function findExistingMatch(array $sourceMatch, int $homeTeamId, int $awayTeamId): ?MatchFixture
    {
        $query = MatchFixture::query()->where('code', $this->matchCode($sourceMatch));

        if (filled($sourceMatch['utcDate'] ?? null)) {
            $date = Carbon::parse($sourceMatch['utcDate'])->utc()->format('Y-m-d H:i:s');

            $query->orWhere(function ($inner) use ($homeTeamId, $awayTeamId, $date) {
                $inner->where('home_team_id', $homeTeamId)
                    ->where('away_team_id', $awayTeamId)
                    ->where('match_date', $date);
            });
        }

        return $query->first();
    }

    protected function groupForTeam(): Group
    {
        return Group::query()->orderBy('sort_order')->orderBy('id')->first()
            ?? Group::query()->create([
                'name' => 'External World Cup',
                'code' => 'FDWC',
                'description' => 'football-data.org dataset import group for demo football data.',
                'sort_order' => 99,
            ]);
    }

    protected function groupForMatch(array $sourceMatch): ?Group
    {
        $groupName = (string) ($sourceMatch['group'] ?? '');

        if ($groupName !== '' && preg_match('/Group\s+([A-Z0-9]+)/i', $groupName, $matches)) {
            $code = Str::upper($matches[1]);

            return Group::query()->where('code', $code)->first() ?: $this->groupForTeam();
        }

        return $this->groupForTeam();
    }

    protected function teamCode(array $sourceTeam): string
    {
        $candidate = Str::upper((string) ($sourceTeam['code'] ?? ''));

        if ($candidate !== '' && ! Team::query()->where('code', $candidate)->exists()) {
            return Str::limit($candidate, 16, '');
        }

        $base = Str::upper(Str::substr(preg_replace('/[^A-Z0-9]/', '', Str::upper((string) $sourceTeam['name'])) ?: 'FD', 0, 10));
        $code = Str::limit($base, 16, '');
        $suffix = 1;

        while (Team::query()->where('code', $code)->exists()) {
            $suffixText = (string) $suffix++;
            $code = Str::limit($base, 16 - strlen($suffixText), '').$suffixText;
        }

        return $code;
    }

    protected function matchCode(array $sourceMatch): string
    {
        return 'FD-WC-'.($sourceMatch['id'] ?? md5(json_encode($sourceMatch)));
    }

    protected function placeholderTeamSource(array $match, string $slot): array
    {
        $matchId = (string) ($match['id'] ?? md5(json_encode($match).$slot));
        $slotCode = $slot === 'home' ? 'H' : 'A';
        $matchCode = preg_replace('/[^0-9A-Z]/i', '', $matchId) ?: substr(md5($matchId.$slot), 0, 8);

        return [
            'source_id' => 'placeholder-'.$matchId.'-'.$slot,
            'name' => 'TBD '.Str::title($slot).' FD-WC-'.$matchId,
            'code' => 'FD'.$slotCode.Str::limit($matchCode, 13, ''),
            'crest' => null,
            'placeholder' => true,
        ];
    }

    protected function hasNamedTeam(mixed $sourceTeam): bool
    {
        return is_array($sourceTeam) && filled($sourceTeam['name'] ?? null);
    }

    protected function uniqueSlug(string $table, string $value): string
    {
        $base = Str::slug($value) ?: 'football-data-item';
        $slug = $base;
        $suffix = 1;

        while (DB::table($table)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    protected function normalizeStatus(?string $status): string
    {
        return match ($status) {
            'FINISHED' => 'completed',
            'IN_PLAY', 'PAUSED', 'LIVE' => 'live',
            'POSTPONED' => 'postponed',
            'CANCELLED', 'SUSPENDED' => 'cancelled',
            default => 'scheduled',
        };
    }

    protected function statusCanUpdate(?string $current, string $incoming): bool
    {
        return $current === null || $current === 'scheduled' || $current === $incoming;
    }

    protected function normalizeStage(array $match): string
    {
        $stage = Str::lower((string) ($match['stage'] ?? ''));
        $group = $match['group'] ?? null;

        if (filled($group) || str_contains($stage, 'group')) {
            return 'group';
        }

        return match ($stage) {
            'last_16', 'round_of_16' => 'round_of_16',
            'quarter_finals', 'quarter_final' => 'quarter_final',
            'semi_finals', 'semi_final' => 'semi_final',
            'third_place' => 'third_place',
            'final' => 'final',
            default => 'group',
        };
    }

    protected function roundNumber(array $match): ?int
    {
        $matchday = $match['matchday'] ?? null;

        return is_numeric($matchday) ? (int) $matchday : null;
    }

    protected function teamKey(array $team): string
    {
        return ($team['source_id'] ?? $team['id'] ?? '').'|'.Str::lower($team['name'] ?? '');
    }
}
