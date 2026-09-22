<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class NewsTagRelation extends Pivot
{
    protected $table = 'news_tag_relations';

    public $incrementing = true;

    protected $fillable = [
        'news_id',
        'news_tag_id',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(NewsTag::class, 'news_tag_id');
    }
}
