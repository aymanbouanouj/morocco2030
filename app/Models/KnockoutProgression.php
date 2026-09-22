<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnockoutProgression extends Model
{
    use HasFactory;

    public const PROGRESSION_TYPES = [
        'winner',
        'loser',
    ];

    public const TEAM_SLOTS = [
        'home',
        'away',
    ];

    protected $fillable = [
        'source_match_id',
        'target_match_id',
        'progression_type',
        'team_slot',
        'notes',
    ];

    public function sourceMatch(): BelongsTo
    {
        return $this->belongsTo(MatchFixture::class, 'source_match_id');
    }

    public function targetMatch(): BelongsTo
    {
        return $this->belongsTo(MatchFixture::class, 'target_match_id');
    }
}
