<?php

namespace App\Models;

use App\Models\Concerns\HasMediaRelations;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsTag extends Model
{
    use HasFactory, HasMediaRelations, HasTranslations, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_tag_relations')
            ->using(NewsTagRelation::class)
            ->withTimestamps();
    }
}
