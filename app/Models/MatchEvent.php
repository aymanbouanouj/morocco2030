<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchEvent extends Model
{
    use HasFactory;

    public const EVENT_TYPES = [
        'goal',
        'own_goal',
        'yellow_card',
        'red_card',
        'substitution',
        'penalty',
        'var',
        'kickoff',
        'halftime',
        'fulltime',
    ];

    public const PERIODS = [
        'pre_match',
        'first_half',
        'half_time',
        'second_half',
        'extra_time_first_half',
        'extra_time_second_half',
        'penalties',
        'post_match',
    ];

    protected $fillable = [
        'match_id',
        'team_id',
        'player_id',
        'related_player_id',
        'minute',
        'extra_minute',
        'period',
        'event_type',
        'description',
        'payload',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchFixture::class, 'match_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function relatedPlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'related_player_id');
    }

    public function minuteLabel(): string
    {
        return $this->minute.($this->extra_minute ? '+'.$this->extra_minute : '');
    }
}
