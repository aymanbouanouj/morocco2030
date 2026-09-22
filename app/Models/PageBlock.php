<?php

namespace App\Models;

use App\Models\Concerns\HasEditorialWorkflows;
use App\Models\Concerns\HasMediaRelations;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageBlock extends Model
{
    use HasEditorialWorkflows, HasFactory, HasMediaRelations, HasTranslations, SoftDeletes;

    protected $fillable = [
        'created_by',
        'updated_by',
        'title',
        'slug',
        'page_key',
        'block_type',
        'status',
        'layout',
        'sort_order',
        'content',
        'meta',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'meta' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }
}
