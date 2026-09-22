<?php

namespace App\Services\ExternalFootball;

use App\Models\MatchFixture;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FootballDataWorldCupReconciliationService
{
    public function __construct(
        protected FootballDataClient $client,
        protected MoroccoVenueMapper $venueMapper
    ) {
    }

    public function reconcile(): array
    {
        $result = $this->client->getWorldCupMatches();

        if (! $result['ok']) {
            return $this->emptyResult([
                'API request failed'.($result['status'] ? ' with status '.$result['status'] : '').': '.$result['error'],
            ], $result['status']);
        }

        $matches = collect($result['data']['matches'] ?? [])
            ->filter(fn ($match) => is_array($match) && filled($match['id'] ?? null))
            ->values();

        if ($matches->count() !== 104) {
            return $this->emptyResult([
                'External World Cup source count mismatch: expected 104 matches, received '.$matches->count().'.',
            ], $result['status']);
        }

        $summary = [
            'source_matches_total' => $matches->count(),
            'teams_updated_with_crests' => 0,
            'placeholders_kept' => 0,
            'matches_status_updated' => 0,
            'scores_updated' => 0,
            'scheduled_scores_cleared' => 0,
            'venues_preserved' => 0,
            'venues_mapped' => 0,
            'conflicts_detected' => $this->publicConflictCount(),
            'errors' => [],
        ];

        DB::transaction(function () use ($matches, &$summary): void {
            foreach ($matches as $index => $sourceMatch) {
                $match = MatchFixture::query()
                    ->with(['homeTeam', 'awayTeam'])
                    ->where('code', $this->matchCode($sourceMatch))
                    ->first();

                if (! $match) {
                    $summary['errors'][] = 'Missing imported FD-WC match '.$this->matchCode($sourceMatch).'.';
                    continue;
                }

                $this->reconcileTeam($match->homeTeam, $sourceMatch['homeTeam'] ?? [], $summary);
                $this->reconcileTeam($match->awayTeam, $sourceMatch['awayTeam'] ?? [], $summary);
                $this->reconcileMatch($match, $sourceMatch, $index, $summary);
            }
        });

        return [
            'ok' => $summary['errors'] === [],
            'status' => $result['status'],
            'summary' => $summary,
            'errors' => $summary['errors'],
            'db_counts_after' => $this->dbCounts(),
        ];
    }

    protected function emptyResult(array $errors, ?int $status): array
    {
        return [
            'ok' => false,
            'status' => $status,
            'summary' => [
                'source_matches_total' => 0,
                'teams_updated_with_crests' => 0,
                'placeholders_kept' => 0,
                'matches_status_updated' => 0,
                'scores_updated' => 0,
                'scheduled_scores_cleared' => 0,
                'venues_preserved' => 0,
                'venues_mapped' => 0,
                'conflicts_detected' => $this->publicConflictCount(),
                'errors' => $errors,
            ],
            'errors' => $errors,
            'db_counts_after' => $this->dbCounts(),
        ];
    }

    protected function reconcileTeam(?Team $team, mixed $sourceTeam, array &$summary): void
    {
        if (! $team) {
            return;
        }

        $meta = is_array($team->meta) ? $team->meta : [];
        $isPlaceholder = (bool) data_get($meta, 'placeholder');

        if (! is_array($sourceTeam) || blank($sourceTeam['name'] ?? null)) {
            data_set($meta, 'placeholder', true);
            data_forget($meta, 'football_data.crest');
            $team->update(['meta' => $meta]);
            $summary['placeholders_kept']++;

            return;
        }

        data_set($meta, 'placeholder', false);
        data_set($meta, 'football_data.id', $sourceTeam['id'] ?? null);
        data_set($meta, 'football_data.name', $sourceTeam['name'] ?? null);
        data_set($meta, 'football_data.tla', $sourceTeam['tla'] ?? null);

        $crest = $this->safeRemoteUrl($sourceTeam['crest'] ?? null);

        if ($crest && data_get($meta, 'football_data.crest') !== $crest) {
            data_set($meta, 'football_data.crest', $crest);
            $summary['teams_updated_with_crests']++;
        }

        $updates = ['meta' => $meta];
        $sourceCode = $sourceTeam['tla'] ?? null;

        if (filled($sourceCode) && blank($team->code)) {
            $updates['code'] = Str::upper($sourceCode);
        }

        $team->update($updates);

        if ($isPlaceholder) {
            $summary['placeholders_kept']++;
        }
    }

    protected function reconcileMatch(MatchFixture $match, array $sourceMatch, int $index, array &$summary): void
    {
        $updates = [];
        $sourceStatus = $this->normalizeStatus($sourceMatch['status'] ?? null);
        $score = $sourceMatch['score']['fullTime'] ?? [];
        $homeScore = $score['home'] ?? null;
        $awayScore = $score['away'] ?? null;

        if ($match->status !== $sourceStatus) {
            $updates['status'] = $sourceStatus;
            $summary['matches_status_updated']++;
        }

        if (filled($sourceMatch['utcDate'] ?? null)) {
            $sourceDate = Carbon::parse($sourceMatch['utcDate'])->utc();

            if (! $match->match_date || ! $match->match_date->equalTo($sourceDate)) {
                $updates['match_date'] = $sourceDate;
            }
        }

        if ($sourceStatus === 'completed' && $homeScore !== null && $awayScore !== null) {
            if ($match->home_score === null || $match->away_score === null || (int) $match->home_score !== (int) $homeScore || (int) $match->away_score !== (int) $awayScore) {
                $updates['home_score'] = (int) $homeScore;
                $updates['away_score'] = (int) $awayScore;
                $summary['scores_updated']++;
            }
        }

        if (in_array($sourceStatus, ['scheduled', 'postponed', 'cancelled'], true)) {
            if ($match->home_score !== null || $match->away_score !== null || $match->home_penalty_score !== null || $match->away_penalty_score !== null) {
                $updates['home_score'] = null;
                $updates['away_score'] = null;
                $updates['home_penalty_score'] = null;
                $updates['away_penalty_score'] = null;
                $summary['scheduled_scores_cleared']++;
            }
        }

        if ($match->stadium_id && $match->city_id) {
            $summary['venues_preserved']++;
        } else {
            $mapping = $this->venueMapper->map($index);

            if (! ($mapping['blocked'] ?? false)) {
                $updates['stadium_id'] = $mapping['local_stadium_id'];
                $updates['city_id'] = DB::table('stadiums')->where('id', $mapping['local_stadium_id'])->value('city_id');
                $summary['venues_mapped']++;
            } else {
                $summary['errors'][] = $mapping['error'] ?? 'Local Moroccan venue mapping failed.';
            }
        }

        $meta = is_array($match->meta) ? $match->meta : [];
        data_set($meta, 'football_data.status', $sourceMatch['status'] ?? null);
        data_set($meta, 'football_data.score_full_time', [
            'home' => $homeScore,
            'away' => $awayScore,
        ]);
        $updates['meta'] = $meta;

        if ($updates !== []) {
            $match->update($updates);
        }
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

    protected function safeRemoteUrl(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        return str_starts_with($url, 'https://') || str_starts_with($url, 'http://')
            ? $url
            : null;
    }

    protected function publicConflictCount(): int
    {
        return MatchFixture::query()
            ->where('code', 'not like', 'FD-WC-%')
            ->count();
    }

    protected function dbCounts(): array
    {
        return [
            'teams' => Team::query()->count(),
            'matches' => MatchFixture::query()->count(),
            'fd_matches' => MatchFixture::query()->where('code', 'like', 'FD-WC-%')->count(),
            'cities' => DB::table('cities')->count(),
            'stadiums' => DB::table('stadiums')->count(),
        ];
    }

    protected function matchCode(array $sourceMatch): string
    {
        return 'FD-WC-'.($sourceMatch['id'] ?? '');
    }
}
