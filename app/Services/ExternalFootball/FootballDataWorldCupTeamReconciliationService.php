<?php

namespace App\Services\ExternalFootball;

use App\Models\Group;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FootballDataWorldCupTeamReconciliationService
{
    public function __construct(
        protected FootballDataClient $client
    ) {
    }

    public function reconcile(): array
    {
        $result = $this->client->getWorldCupTeams();

        if (! $result['ok']) {
            return $this->emptyResult([
                'API request failed'.($result['status'] ? ' with status '.$result['status'] : '').': '.$result['error'],
            ], $result['status']);
        }

        $sourceTeams = collect($result['data']['teams'] ?? [])
            ->filter(fn ($team) => is_array($team) && filled($team['id'] ?? null) && filled($team['name'] ?? null))
            ->map(fn (array $team) => $this->normalizeSourceTeam($team))
            ->values();

        if ($sourceTeams->count() !== 48) {
            return $this->emptyResult([
                'External World Cup source team count mismatch: expected 48 teams, received '.$sourceTeams->count().'.',
            ], $result['status']);
        }

        $duplicateSourceIds = $sourceTeams->groupBy('source_id')->filter(fn ($items) => $items->count() > 1)->keys()->values();
        $duplicateTlas = $sourceTeams->groupBy('tla')->filter(fn ($items, $key) => filled($key) && $items->count() > 1)->keys()->values();

        if ($duplicateSourceIds->isNotEmpty() || $duplicateTlas->isNotEmpty()) {
            return $this->emptyResult([
                'External World Cup source teams contain duplicate ids or TLA values.',
            ], $result['status']);
        }

        $group = Group::query()->orderBy('sort_order')->orderBy('id')->first();

        if (! $group) {
            return $this->emptyResult([
                'No local group exists for creating unmatched World Cup teams.',
            ], $result['status']);
        }

        $summary = [
            'source_teams' => $sourceTeams->count(),
            'matched_by_source_id' => 0,
            'matched_by_code' => 0,
            'matched_by_name' => 0,
            'upgraded_old_showcase_teams' => 0,
            'created' => 0,
            'skipped' => 0,
            'placeholders_preserved' => $this->placeholderCount(),
            'final_real_fd_team_count' => $this->realFdTeamCount(),
            'final_placeholder_count' => $this->placeholderCount(),
            'errors' => [],
        ];
        $details = [];

        DB::transaction(function () use ($sourceTeams, $group, &$summary, &$details): void {
            $processedTeamIds = [];

            foreach ($sourceTeams as $sourceTeam) {
                $match = $this->findMatch($sourceTeam, $processedTeamIds);

                if ($match['team']) {
                    $team = $match['team'];
                    $wasFootballDataTeam = data_get($team->meta, 'source') === 'football-data.org'
                        && ! (bool) data_get($team->meta, 'placeholder');

                    $this->updateTeamFromSource($team, $sourceTeam);
                    $summary[$match['strategy']]++;

                    if (! $wasFootballDataTeam) {
                        $summary['upgraded_old_showcase_teams']++;
                    }

                    $details[] = [
                        'source_id' => $sourceTeam['source_id'],
                        'source_name' => $sourceTeam['name'],
                        'source_tla' => $sourceTeam['tla'],
                        'local_id' => $team->id,
                        'local_name' => $team->fresh()->name,
                        'action' => $wasFootballDataTeam ? 'matched_existing_fd_team' : 'upgraded_old_showcase_team',
                        'strategy' => str_replace('matched_by_', '', $match['strategy']),
                    ];
                } else {
                    $team = $this->createTeamFromSource($sourceTeam, $group);
                    $summary['created']++;

                    $details[] = [
                        'source_id' => $sourceTeam['source_id'],
                        'source_name' => $sourceTeam['name'],
                        'source_tla' => $sourceTeam['tla'],
                        'local_id' => $team->id,
                        'local_name' => $team->name,
                        'action' => 'created_real_fd_team',
                        'strategy' => 'create',
                    ];
                }

                $processedTeamIds[] = $team->id;
            }

            $summary['placeholders_preserved'] = $this->placeholderCount();
            $summary['final_real_fd_team_count'] = $this->realFdTeamCount();
            $summary['final_placeholder_count'] = $this->placeholderCount();

            $duplicateFinalSourceIds = Team::query()
                ->where('meta->source', 'football-data.org')
                ->where(function ($query) {
                    $query->whereNull('meta->placeholder')
                        ->orWhere('meta->placeholder', false);
                })
                ->whereNotNull('meta->football_data->id')
                ->get()
                ->groupBy(fn (Team $team) => (string) data_get($team->meta, 'football_data.id'))
                ->filter(fn ($items) => $items->count() > 1);

            $duplicateFinalTlas = Team::query()
                ->where('meta->source', 'football-data.org')
                ->where(function ($query) {
                    $query->whereNull('meta->placeholder')
                        ->orWhere('meta->placeholder', false);
                })
                ->whereNotNull('meta->football_data->tla')
                ->get()
                ->groupBy(fn (Team $team) => Str::upper((string) data_get($team->meta, 'football_data.tla')))
                ->filter(fn ($items, $key) => filled($key) && $items->count() > 1);

            if ($summary['final_real_fd_team_count'] !== 48) {
                $summary['errors'][] = 'Final real FD team count mismatch: expected 48, found '.$summary['final_real_fd_team_count'].'.';
            }

            if ($duplicateFinalSourceIds->isNotEmpty()) {
                $summary['errors'][] = 'Duplicate real FD teams by source id detected.';
            }

            if ($duplicateFinalTlas->isNotEmpty()) {
                $summary['errors'][] = 'Duplicate real FD teams by TLA detected.';
            }
        });

        return [
            'ok' => $summary['errors'] === [],
            'status' => $result['status'],
            'summary' => $summary,
            'teams' => array_slice($details, 0, 48),
            'errors' => $summary['errors'],
            'rate_limit' => $result['rate_limit'],
        ];
    }

    protected function emptyResult(array $errors, ?int $status = null): array
    {
        return [
            'ok' => false,
            'status' => $status,
            'summary' => [
                'source_teams' => 0,
                'matched_by_source_id' => 0,
                'matched_by_code' => 0,
                'matched_by_name' => 0,
                'upgraded_old_showcase_teams' => 0,
                'created' => 0,
                'skipped' => 0,
                'placeholders_preserved' => $this->placeholderCount(),
                'final_real_fd_team_count' => $this->realFdTeamCount(),
                'final_placeholder_count' => $this->placeholderCount(),
                'errors' => $errors,
            ],
            'teams' => [],
            'errors' => $errors,
            'rate_limit' => [],
        ];
    }

    protected function normalizeSourceTeam(array $team): array
    {
        return [
            'source_id' => (int) $team['id'],
            'name' => trim((string) $team['name']),
            'short_name' => filled($team['shortName'] ?? null) ? trim((string) $team['shortName']) : null,
            'tla' => filled($team['tla'] ?? null) ? Str::upper((string) $team['tla']) : null,
            'crest' => $this->safeRemoteUrl($team['crest'] ?? null),
        ];
    }

    protected function findMatch(array $sourceTeam, array $processedTeamIds): array
    {
        $query = $this->realCandidateQuery()->whereNotIn('id', $processedTeamIds);
        $team = (clone $query)->where('meta->football_data->id', $sourceTeam['source_id'])->first();

        if ($team) {
            return ['team' => $team, 'strategy' => 'matched_by_source_id'];
        }

        $team = (clone $query)->where('meta->source_team_id', $sourceTeam['source_id'])->first();

        if ($team) {
            return ['team' => $team, 'strategy' => 'matched_by_source_id'];
        }

        if (filled($sourceTeam['tla'])) {
            $team = (clone $query)->where('code', $sourceTeam['tla'])->first();

            if ($team) {
                return ['team' => $team, 'strategy' => data_get($team->meta, 'source') === 'football-data.org' ? 'matched_by_code' : 'matched_by_code'];
            }
        }

        $normalizedName = $this->normalizeName($sourceTeam['name']);
        $team = (clone $query)->get()
            ->first(fn (Team $candidate) => $this->normalizeName($candidate->name) === $normalizedName);

        if ($team) {
            return ['team' => $team, 'strategy' => 'matched_by_name'];
        }

        return ['team' => null, 'strategy' => null];
    }

    protected function updateTeamFromSource(Team $team, array $sourceTeam): void
    {
        $meta = is_array($team->meta) ? $team->meta : [];
        data_set($meta, 'source', 'football-data.org');
        data_set($meta, 'source_competition', 'WC');
        data_set($meta, 'placeholder', false);
        data_set($meta, 'football_data.id', $sourceTeam['source_id']);
        data_set($meta, 'football_data.name', $sourceTeam['name']);
        data_set($meta, 'football_data.tla', $sourceTeam['tla']);
        data_set($meta, 'football_data.shortName', $sourceTeam['short_name']);
        data_set($meta, 'football_data.crest', $sourceTeam['crest']);

        $updates = [
            'meta' => $meta,
            'status' => $team->status ?: 'active',
        ];

        if (blank($team->name) || $this->normalizeName($team->name) === $this->normalizeName($sourceTeam['name'])) {
            $updates['name'] = $sourceTeam['name'];
        }

        if (filled($sourceTeam['short_name']) && blank($team->short_name)) {
            $updates['short_name'] = Str::limit($sourceTeam['short_name'], 32, '');
        }

        if (filled($sourceTeam['tla']) && (blank($team->code) || Str::upper((string) $team->code) === $sourceTeam['tla'])) {
            $updates['code'] = $sourceTeam['tla'];
        }

        if (blank($team->slug)) {
            $updates['slug'] = $this->uniqueSlug($sourceTeam['name'], $team->id);
        }

        $team->update($updates);
    }

    protected function createTeamFromSource(array $sourceTeam, Group $group): Team
    {
        return Team::query()->create([
            'group_id' => $group->id,
            'name' => $sourceTeam['name'],
            'short_name' => Str::limit($sourceTeam['short_name'] ?: $sourceTeam['name'], 32, ''),
            'code' => $this->uniqueCode($sourceTeam),
            'slug' => $this->uniqueSlug($sourceTeam['name']),
            'federation_name' => null,
            'founded_year' => null,
            'coach_name' => null,
            'team_type' => 'national',
            'status' => 'active',
            'meta' => [
                'source' => 'football-data.org',
                'source_competition' => 'WC',
                'placeholder' => false,
                'football_data' => [
                    'id' => $sourceTeam['source_id'],
                    'name' => $sourceTeam['name'],
                    'tla' => $sourceTeam['tla'],
                    'shortName' => $sourceTeam['short_name'],
                    'crest' => $sourceTeam['crest'],
                ],
            ],
        ]);
    }

    protected function realCandidateQuery()
    {
        return Team::query()
            ->where(function ($query) {
                $query->whereNull('meta->placeholder')
                    ->orWhere('meta->placeholder', false);
            });
    }

    protected function realFdTeamCount(): int
    {
        return Team::query()
            ->where('meta->source', 'football-data.org')
            ->where(function ($query) {
                $query->whereNull('meta->placeholder')
                    ->orWhere('meta->placeholder', false);
            })
            ->count();
    }

    protected function placeholderCount(): int
    {
        return Team::query()
            ->where('meta->source', 'football-data.org')
            ->where('meta->placeholder', true)
            ->count();
    }

    protected function uniqueCode(array $sourceTeam): string
    {
        $base = Str::upper((string) ($sourceTeam['tla'] ?: Str::substr(preg_replace('/[^A-Z0-9]/', '', Str::upper($sourceTeam['name'])), 0, 10)));
        $base = Str::limit($base ?: 'FD', 16, '');
        $code = $base;
        $suffix = 1;

        while (Team::query()->where('code', $code)->exists()) {
            $suffixText = (string) $suffix++;
            $code = Str::limit($base, 16 - strlen($suffixText), '').$suffixText;
        }

        return $code;
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'football-data-team';
        $slug = $base;
        $suffix = 1;

        while (Team::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '<>', $ignoreId))
            ->exists()
        ) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    protected function normalizeName(string $name): string
    {
        return Str::of($name)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->toString();
    }

    protected function safeRemoteUrl(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        return str_starts_with($url, 'https://') || str_starts_with($url, 'http://')
            ? $url
            : null;
    }
}
