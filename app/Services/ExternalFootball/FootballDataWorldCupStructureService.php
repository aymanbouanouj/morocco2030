<?php

namespace App\Services\ExternalFootball;

use App\Models\Group;
use App\Models\MatchFixture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FootballDataWorldCupStructureService
{
    public function __construct(
        protected FootballDataClient $client
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

        $groups = $matches
            ->map(fn (array $match) => $this->normalizeGroup($match['group'] ?? null))
            ->filter()
            ->unique('raw')
            ->values();

        if ($matches->count() !== 104) {
            return $this->emptyResult([
                'External World Cup source count mismatch: expected 104 matches, received '.$matches->count().'.',
            ], $result['status']);
        }

        if ($groups->isEmpty()) {
            return $this->emptyResult([
                'External World Cup source did not provide group values. Structure reconciliation stopped.',
            ], $result['status']);
        }

        $summary = [
            'source_matches_total' => $matches->count(),
            'groups_detected' => $groups->count(),
            'groups_created' => 0,
            'groups_matched' => 0,
            'matches_updated_with_group' => 0,
            'matches_updated_with_stage' => 0,
            'group_stage_matches' => 0,
            'knockout_unassigned_matches' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        DB::transaction(function () use ($matches, &$summary): void {
            $groupMap = [];

            foreach ($matches as $sourceMatch) {
                $normalizedGroup = $this->normalizeGroup($sourceMatch['group'] ?? null);

                if ($normalizedGroup && ! isset($groupMap[$normalizedGroup['raw']])) {
                    $groupMap[$normalizedGroup['raw']] = $this->resolveGroup($normalizedGroup, $summary);
                }

                $match = MatchFixture::query()
                    ->where('code', $this->matchCode($sourceMatch))
                    ->first();

                if (! $match) {
                    $summary['skipped']++;
                    $summary['errors'][] = 'Missing imported FD-WC match '.$this->matchCode($sourceMatch).'.';
                    continue;
                }

                $this->updateMatchStructure($match, $sourceMatch, $normalizedGroup, $groupMap, $summary);
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

    private function updateMatchStructure(
        MatchFixture $match,
        array $sourceMatch,
        ?array $normalizedGroup,
        array $groupMap,
        array &$summary
    ): void {
        $normalizedStage = $this->normalizeStage($sourceMatch['stage'] ?? null, $normalizedGroup);
        $updates = [];
        $meta = is_array($match->meta) ? $match->meta : [];

        data_set($meta, 'football_data.id', $sourceMatch['id'] ?? null);
        data_set($meta, 'football_data.group', $normalizedGroup['raw'] ?? null);
        data_set($meta, 'football_data.group_label', $normalizedGroup['label'] ?? null);
        data_set($meta, 'football_data.stage', $normalizedStage['raw']);
        data_set($meta, 'football_data.stage_label', $normalizedStage['label']);
        data_set($meta, 'football_data.matchday', $sourceMatch['matchday'] ?? null);
        $updates['meta'] = $meta;

        $targetGroupId = null;

        if ($normalizedGroup) {
            $summary['group_stage_matches']++;
            $targetGroupId = $groupMap[$normalizedGroup['raw']]?->id;
        } else {
            $summary['knockout_unassigned_matches']++;
        }

        if ((int) $match->group_id !== (int) $targetGroupId) {
            $updates['group_id'] = $targetGroupId;
            $summary['matches_updated_with_group']++;
        }

        if ($match->stage_type !== $normalizedStage['stage_type']) {
            $updates['stage_type'] = $normalizedStage['stage_type'];
            $summary['matches_updated_with_stage']++;
        }

        $roundNumber = is_numeric($sourceMatch['matchday'] ?? null) ? (int) $sourceMatch['matchday'] : null;

        if ($match->round_number !== $roundNumber) {
            $updates['round_number'] = $roundNumber;
        }

        $match->update($updates);
    }

    private function resolveGroup(array $normalizedGroup, array &$summary): Group
    {
        $group = Group::query()
            ->where('code', $normalizedGroup['code'])
            ->first();

        if ($group) {
            $summary['groups_matched']++;

            return $group;
        }

        $summary['groups_created']++;

        return Group::query()->create([
            'name' => $normalizedGroup['label'],
            'code' => $normalizedGroup['code'],
            'description' => 'football-data.org World Cup API group '.$normalizedGroup['raw'].'.',
            'sort_order' => $normalizedGroup['sort_order'],
        ]);
    }

    private function normalizeGroup(mixed $group): ?array
    {
        if (! is_string($group) || trim($group) === '') {
            return null;
        }

        $raw = Str::upper(trim($group));

        if (preg_match('/^GROUP[_\s-]?([A-Z0-9]+)$/', $raw, $matches) !== 1) {
            return null;
        }

        $code = $matches[1];

        return [
            'raw' => 'GROUP_'.$code,
            'code' => $code,
            'label' => 'Group '.$code,
            'sort_order' => $this->groupSortOrder($code),
        ];
    }

    private function normalizeStage(mixed $stage, ?array $normalizedGroup): array
    {
        $raw = is_string($stage) && trim($stage) !== '' ? Str::upper(trim($stage)) : null;

        if ($normalizedGroup || $raw === 'GROUP_STAGE') {
            return [
                'raw' => $raw ?? 'GROUP_STAGE',
                'label' => 'Group Stage',
                'stage_type' => 'group',
            ];
        }

        return match ($raw) {
            'LAST_32' => ['raw' => 'LAST_32', 'label' => 'Round of 32', 'stage_type' => 'round_of_32'],
            'LAST_16' => ['raw' => 'LAST_16', 'label' => 'Round of 16', 'stage_type' => 'round_of_16'],
            'QUARTER_FINALS', 'QUARTER_FINAL' => ['raw' => $raw, 'label' => 'Quarter-finals', 'stage_type' => 'quarter_final'],
            'SEMI_FINALS', 'SEMI_FINAL' => ['raw' => $raw, 'label' => 'Semi-finals', 'stage_type' => 'semi_final'],
            'THIRD_PLACE' => ['raw' => 'THIRD_PLACE', 'label' => 'Third Place Play-off', 'stage_type' => 'third_place'],
            'FINAL' => ['raw' => 'FINAL', 'label' => 'Final', 'stage_type' => 'final'],
            default => ['raw' => $raw, 'label' => $raw ? Str::headline($raw) : 'Unassigned Stage', 'stage_type' => 'group'],
        };
    }

    private function groupSortOrder(string $code): int
    {
        if (preg_match('/^[A-Z]$/', $code) === 1) {
            return ord($code) - 64;
        }

        return 99;
    }

    private function matchCode(array $sourceMatch): string
    {
        return 'FD-WC-'.($sourceMatch['id'] ?? '');
    }

    private function emptyResult(array $errors, ?int $status): array
    {
        return [
            'ok' => false,
            'status' => $status,
            'summary' => [
                'source_matches_total' => 0,
                'groups_detected' => 0,
                'groups_created' => 0,
                'groups_matched' => 0,
                'matches_updated_with_group' => 0,
                'matches_updated_with_stage' => 0,
                'group_stage_matches' => 0,
                'knockout_unassigned_matches' => 0,
                'skipped' => 0,
                'errors' => $errors,
            ],
            'errors' => $errors,
            'db_counts_after' => $this->dbCounts(),
        ];
    }

    private function dbCounts(): array
    {
        return [
            'matches' => MatchFixture::query()->count(),
            'fd_matches' => MatchFixture::query()->where('code', 'like', 'FD-WC-%')->count(),
            'groups' => Group::query()->count(),
            'fd_groups' => MatchFixture::query()
                ->where('code', 'like', 'FD-WC-%')
                ->whereNotNull('group_id')
                ->distinct('group_id')
                ->count('group_id'),
            'cities' => DB::table('cities')->count(),
            'stadiums' => DB::table('stadiums')->count(),
        ];
    }
}
