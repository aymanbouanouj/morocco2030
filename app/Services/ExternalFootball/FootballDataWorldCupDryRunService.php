<?php

namespace App\Services\ExternalFootball;

use App\Models\MatchFixture;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FootballDataWorldCupDryRunService
{
    public function __construct(
        protected FootballDataClient $client,
        protected MoroccoVenueMapper $venueMapper
    ) {
    }

    public function preview(): array
    {
        $result = $this->client->getWorldCupMatches();

        if (! $result['ok']) {
            return $this->emptyPreview([
                'API request failed'.($result['status'] ? ' with status '.$result['status'] : '').': '.$result['error'],
            ], $result['status']);
        }

        $payload = $result['data'];
        $matches = collect($payload['matches'] ?? [])
            ->filter(fn ($match) => is_array($match))
            ->values();

        $competition = $payload['competition'] ?? [];
        $teams = $this->extractTeams($matches);
        $teamPreview = $teams->map(fn (array $team) => $this->previewTeam($team))->values();
        $matchPreview = $matches
            ->map(fn (array $match, int $index) => $this->previewMatch($match, $index))
            ->values();

        $mappingBlocked = $matchPreview->where('mapping.blocked', true)->count();

        return [
            'ok' => true,
            'status' => $result['status'],
            'competition' => [
                'id' => $competition['id'] ?? null,
                'name' => $competition['name'] ?? 'FIFA World Cup',
                'code' => $competition['code'] ?? 'WC',
            ],
            'season' => $payload['season'] ?? null,
            'summary' => [
                'source_matches_total' => $matches->count(),
                'teams_detected_total' => $teamPreview->count(),
                'teams_would_create' => $teamPreview->where('action', 'would_create')->count(),
                'teams_already_matched' => $teamPreview->where('action', 'would_match_existing')->count(),
                'matches_would_create' => $matchPreview->where('action', 'would_create')->count(),
                'matches_already_matched' => $matchPreview->where('action', 'would_match_existing')->count(),
                'matches_mapped_to_moroccan_stadiums' => $matchPreview->count() - $mappingBlocked,
                'mapping_blocked' => $mappingBlocked,
                'skipped' => 0,
                'errors' => [],
            ],
            'teams_preview' => $teamPreview->take(12)->all(),
            'matches_preview' => $matchPreview->take(12)->all(),
            'errors' => [],
            'rate_limit' => $result['rate_limit'],
        ];
    }

    protected function emptyPreview(array $errors, ?int $status = null): array
    {
        return [
            'ok' => false,
            'status' => $status,
            'competition' => ['name' => null, 'code' => 'WC'],
            'season' => null,
            'summary' => [
                'source_matches_total' => 0,
                'teams_detected_total' => 0,
                'teams_would_create' => 0,
                'teams_already_matched' => 0,
                'matches_would_create' => 0,
                'matches_already_matched' => 0,
                'matches_mapped_to_moroccan_stadiums' => 0,
                'mapping_blocked' => 0,
                'skipped' => 0,
                'errors' => $errors,
            ],
            'teams_preview' => [],
            'matches_preview' => [],
            'errors' => $errors,
            'rate_limit' => [],
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
            ->unique(fn (array $team) => ($team['source_id'] ?? '').'|'.Str::lower($team['name'] ?? ''))
            ->values();
    }

    protected function previewTeam(array $team): array
    {
        $existing = Team::query()
            ->when(filled($team['code'] ?? null) && Schema::hasColumn('teams', 'code'), function ($query) use ($team) {
                $query->orWhere('code', $team['code']);
            })
            ->orWhere('name', $team['name'])
            ->first();

        return [
            ...$team,
            'existing_id' => $existing?->id,
            'action' => $existing ? 'would_match_existing' : 'would_create',
        ];
    }

    protected function previewMatch(array $match, int $index): array
    {
        $home = $match['homeTeam'] ?? [];
        $away = $match['awayTeam'] ?? [];
        $utcDate = $match['utcDate'] ?? null;
        $homeTeam = $this->findExistingTeam($home);
        $awayTeam = $this->findExistingTeam($away);
        $existingMatch = $this->findExistingMatch($match, $homeTeam?->id, $awayTeam?->id);
        $score = $match['score']['fullTime'] ?? [];
        $mapping = $this->venueMapper->map($index);

        return [
            'source_id' => $match['id'] ?? null,
            'utc_date' => $utcDate,
            'status' => $match['status'] ?? null,
            'normalized_status' => $this->normalizeStatus($match['status'] ?? null),
            'stage' => $match['stage'] ?? null,
            'normalized_stage' => $this->normalizeStage($match),
            'group' => $match['group'] ?? null,
            'matchday' => $match['matchday'] ?? null,
            'home_team' => [
                'source_id' => $home['id'] ?? null,
                'name' => $home['name'] ?? null,
                'code' => $home['tla'] ?? null,
                'crest' => $home['crest'] ?? null,
                'existing_id' => $homeTeam?->id,
            ],
            'away_team' => [
                'source_id' => $away['id'] ?? null,
                'name' => $away['name'] ?? null,
                'code' => $away['tla'] ?? null,
                'crest' => $away['crest'] ?? null,
                'existing_id' => $awayTeam?->id,
            ],
            'score_full_time' => [
                'home' => $score['home'] ?? null,
                'away' => $score['away'] ?? null,
            ],
            'mapping' => $mapping,
            'existing_id' => $existingMatch?->id,
            'action' => $existingMatch ? 'would_match_existing' : 'would_create',
        ];
    }

    protected function findExistingTeam(array $sourceTeam): ?Team
    {
        $name = $sourceTeam['name'] ?? null;
        $code = $sourceTeam['tla'] ?? null;

        return Team::query()
            ->when(filled($code) && Schema::hasColumn('teams', 'code'), function ($query) use ($code) {
                $query->where('code', $code);
            })
            ->when(filled($name), function ($query) use ($name, $code) {
                $method = filled($code) && Schema::hasColumn('teams', 'code') ? 'orWhere' : 'where';
                $query->{$method}('name', $name);
            })
            ->first();
    }

    protected function findExistingMatch(array $match, ?int $homeTeamId, ?int $awayTeamId): ?MatchFixture
    {
        $externalCode = 'FD-WC-'.($match['id'] ?? '');

        $query = MatchFixture::query();

        if (filled($match['id'] ?? null) && Schema::hasColumn('matches', 'code')) {
            $query->where('code', $externalCode);
        }

        if ($homeTeamId && $awayTeamId && filled($match['utcDate'] ?? null)) {
            $date = Carbon::parse($match['utcDate'])->utc()->format('Y-m-d H:i:s');

            $query->orWhere(function ($inner) use ($homeTeamId, $awayTeamId, $date) {
                $inner->where('home_team_id', $homeTeamId)
                    ->where('away_team_id', $awayTeamId)
                    ->where('match_date', $date);
            });
        }

        return $query->first();
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
}
