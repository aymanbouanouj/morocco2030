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

class Stadium extends Model
{
    use HasFactory, HasFavorites, HasMediaRelations, HasTranslations, SoftDeletes;

    protected $fillable = [
        'city_id',
        'name',
        'slug',
        'code',
        'capacity',
        'address',
        'latitude',
        'longitude',
        'opened_year',
        'surface_type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchFixture::class);
    }
}
