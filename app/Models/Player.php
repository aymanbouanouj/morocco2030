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

class Player extends Model
{
    use HasFactory, HasFavorites, HasMediaRelations, HasTranslations, SoftDeletes;

    protected $fillable = [
        'team_id',
        'display_name',
        'first_name',
        'last_name',
        'slug',
        'shirt_number',
        'position',
        'date_of_birth',
        'nationality_code',
        'height_cm',
        'weight_kg',
        'bio',
        'is_captain',
        'status',
        'external_provider',
        'external_id',
        'external_payload',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_captain' => 'boolean',
            'external_payload' => 'array',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function matchEvents(): HasMany
    {
        return $this->hasMany(MatchEvent::class);
    }

    public function relatedMatchEvents(): HasMany
    {
        return $this->hasMany(MatchEvent::class, 'related_player_id');
    }

    public function matchLineups(): HasMany
    {
        return $this->hasMany(MatchLineup::class);
    }
}
