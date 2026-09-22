<?php

namespace App\Models;

use App\Models\Concerns\HasFavorites;
use App\Models\Concerns\HasMediaRelations;
use App\Models\Concerns\HasTranslations;
use App\Support\PublicContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchFixture extends Model
{
    use HasFactory, HasFavorites, HasMediaRelations, HasTranslations, SoftDeletes;

    public const STAGE_TYPES = [
        'group',
        'round_of_32',
        'round_of_16',
        'quarter_final',
        'semi_final',
        'third_place',
        'final',
    ];

    public const KNOCKOUT_STAGE_TYPES = [
        'round_of_32',
        'round_of_16',
        'quarter_final',
        'semi_final',
        'third_place',
        'final',
    ];

    public const STATUSES = [
        'scheduled',
        'live',
        'completed',
        'postponed',
        'cancelled',
    ];

    protected $table = 'matches';

    protected $fillable = [
        'stadium_id',
        'city_id',
        'group_id',
        'home_team_id',
        'away_team_id',
        'code',
        'slug',
        'stage_type',
        'round_number',
        'match_date',
        'timezone',
        'status',
        'attendance',
        'home_score',
        'away_score',
        'home_penalty_score',
        'away_penalty_score',
        'extra_time_played',
        'meta',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'match_date' => 'datetime',
            'published_at' => 'datetime',
            'extra_time_played' => 'boolean',
            'meta' => 'array',
        ];
    }

    public function stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(MatchEvent::class, 'match_id');
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(MatchStatistic::class, 'match_id');
    }

    public function lineups(): HasMany
    {
        return $this->hasMany(MatchLineup::class, 'match_id');
    }

    public function sourceProgressions(): HasMany
    {
        return $this->hasMany(KnockoutProgression::class, 'source_match_id');
    }

    public function targetProgressions(): HasMany
    {
        return $this->hasMany(KnockoutProgression::class, 'target_match_id');
    }

    public function targetQualificationRules(): HasMany
    {
        return $this->hasMany(GroupQualificationRule::class, 'target_match_id');
    }

    public function isGroupStage(): bool
    {
        return $this->stage_type === 'group';
    }

    public function isKnockoutStage(): bool
    {
        return in_array($this->stage_type, self::KNOCKOUT_STAGE_TYPES, true);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function hasResolvedTeams(): bool
    {
        return (bool) ($this->home_team_id && $this->away_team_id);
    }

    public function hasScoreline(): bool
    {
        return $this->home_score !== null && $this->away_score !== null;
    }

    public function winnerTeam(): ?Team
    {
        if (! $this->isCompleted() || ! $this->hasScoreline()) {
            return null;
        }

        if ($this->home_score > $this->away_score) {
            return $this->homeTeam;
        }

        if ($this->away_score > $this->home_score) {
            return $this->awayTeam;
        }

        if (
            $this->home_penalty_score !== null
            && $this->away_penalty_score !== null
            && $this->home_penalty_score !== $this->away_penalty_score
        ) {
            return $this->home_penalty_score > $this->away_penalty_score
                ? $this->homeTeam
                : $this->awayTeam;
        }

        return null;
    }

    public function loserTeam(): ?Team
    {
        $winner = $this->winnerTeam();

        if (! $winner) {
            return null;
        }

        return $winner->is($this->homeTeam) ? $this->awayTeam : $this->homeTeam;
    }

    public function slotLabel(string $slot): string
    {
        $team = $slot === 'home' ? $this->homeTeam : $this->awayTeam;

        if ($team) {
            return PublicContent::field($team, 'name') ?? $team->name;
        }

        $qualificationRule = $this->incomingQualificationRuleForSlot($slot);

        if ($qualificationRule) {
            return $qualificationRule->publicLabel();
        }

        $progression = $this->incomingProgressionForSlot($slot);

        if (! $progression) {
            return __('TBD');
        }

        $sourceCode = $progression->sourceMatch?->code ?: 'source fixture';

        return match ($progression->progression_type) {
            'winner' => __('Winner of :code', ['code' => $sourceCode]),
            'loser' => __('Loser of :code', ['code' => $sourceCode]),
            default => __('TBD'),
        };
    }

    protected function incomingProgressionForSlot(string $slot): ?KnockoutProgression
    {
        if ($this->relationLoaded('targetProgressions')) {
            return $this->targetProgressions->firstWhere('team_slot', $slot);
        }

        return $this->targetProgressions()
            ->with('sourceMatch')
            ->where('team_slot', $slot)
            ->first();
    }

    protected function incomingQualificationRuleForSlot(string $slot): ?GroupQualificationRule
    {
        if ($this->relationLoaded('targetQualificationRules')) {
            return $this->targetQualificationRules->firstWhere('team_slot', $slot);
        }

        return $this->targetQualificationRules()
            ->with('group')
            ->where('team_slot', $slot)
            ->first();
    }
}
