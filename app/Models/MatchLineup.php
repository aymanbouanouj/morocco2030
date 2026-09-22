<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchLineup extends Model
{
    use HasFactory;

    public const LINEUP_TYPES = [
        'starting',
        'bench',
    ];

    protected $fillable = [
        'match_id',
        'team_id',
        'player_id',
        'lineup_type',
        'sort_order',
        'position',
        'shirt_number',
        'formation_slot',
        'is_captain',
        'is_goalkeeper',
        'minute_in',
        'minute_out',
    ];

    protected function casts(): array
    {
        return [
            'is_captain' => 'boolean',
            'is_goalkeeper' => 'boolean',
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

    public function getPositionLabelAttribute(): ?string
    {
        return $this->position;
    }
}
