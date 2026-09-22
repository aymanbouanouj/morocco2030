<?php

namespace App\Models;

use App\Models\Concerns\HasFavorites;
use App\Models\Concerns\HasMediaRelations;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, HasFavorites, HasMediaRelations, HasTranslations, SoftDeletes;

    protected $fillable = [
        'group_id',
        'name',
        'short_name',
        'code',
        'slug',
        'federation_name',
        'founded_year',
        'coach_name',
        'team_type',
        'status',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(MatchFixture::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(MatchFixture::class, 'away_team_id');
    }

    public function matchEvents(): HasMany
    {
        return $this->hasMany(MatchEvent::class);
    }

    public function matchStatistics(): HasMany
    {
        return $this->hasMany(MatchStatistic::class);
    }

    public function matchLineups(): HasMany
    {
        return $this->hasMany(MatchLineup::class);
    }

    public function standings(): HasMany
    {
        return $this->hasMany(Standing::class);
    }
}
