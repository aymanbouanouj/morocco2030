<?php

namespace App\Models;

use App\Models\Concerns\HasMediaRelations;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsCategory extends Model
{
    use HasFactory, HasMediaRelations, HasTranslations, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'status',
        'sort_order',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public static function ensureTournamentUpdatesCategory(): self
    {
        $category = self::query()
            ->where('slug', 'tournament-updates')
            ->orWhere('name', 'Tournament Updates')
            ->first();

        if (! $category) {
            return self::query()->create([
                'name' => 'Tournament Updates',
                'slug' => 'tournament-updates',
                'description' => 'Official updates and editorial news for the Morocco 2030 demo platform.',
                'status' => 'active',
                'sort_order' => 1,
            ]);
        }

        if ($category->status !== 'active') {
            $category->update(['status' => 'active']);
        }

        return $category->fresh();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'category_id');
    }
}
