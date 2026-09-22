<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uploaded_by',
        'disk',
        'path',
        'filename',
        'original_name',
        'mime_type',
        'extension',
        'size_bytes',
        'width',
        'height',
        'duration_seconds',
        'checksum',
        'title',
        'alt_text',
        'caption',
        'visibility',
        'status',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function mediaRelations(): HasMany
    {
        return $this->hasMany(MediaRelation::class);
    }
}
