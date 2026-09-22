<?php

namespace App\Models;

use App\Models\Concerns\HasMediaRelations;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, HasMediaRelations, HasTranslations, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'country_code',
        'region',
        'latitude',
        'longitude',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function stadiums(): HasMany
    {
        return $this->hasMany(Stadium::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchFixture::class);
    }
}
