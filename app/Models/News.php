<?php

namespace App\Models;

use App\Models\Concerns\HasEditorialWorkflows;
use App\Models\Concerns\HasFavorites;
use App\Models\Concerns\HasMediaRelations;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class News extends Model
{
    use HasEditorialWorkflows, HasFactory, HasFavorites, HasMediaRelations, HasTranslations, SoftDeletes;

    protected $fillable = [
        'category_id',
        'author_id',
        'editor_id',
        'title',
        'slug',
        'summary',
        'body',
        'status',
        'visibility',
        'published_at',
        'featured_at',
        'meta',
        'cover_image_path',
        'gallery_image_paths',
        'video_url',
        'media_alt_text',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'featured_at' => 'datetime',
            'meta' => 'array',
            'gallery_image_paths' => 'array',
        ];
    }

    public function coverImageUrl(): ?string
    {
        $path = $this->normalizeMediaPath($this->cover_image_path);

        if ($path === null) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * @return list<string>
     */
    public function galleryImageUrls(): array
    {
        return collect($this->normalizedGalleryPaths())
            ->filter(fn ($path) => is_string($path) && $path !== '')
            ->map(function (string $path) {
                if (Str::startsWith($path, ['http://', 'https://'])) {
                    return $path;
                }

                return Storage::disk('public')->url($path);
            })
            ->values()
            ->all();
    }

    public function hasMedia(): bool
    {
        return $this->coverImageUrl() !== null
            || filled($this->video_url)
            || $this->normalizedGalleryPaths() !== [];
    }

    /**
     * @return list<string>
     */
    public function normalizedGalleryPaths(): array
    {
        $paths = $this->gallery_image_paths;

        if (is_string($paths)) {
            $decoded = json_decode($paths, true);

            $paths = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($paths)) {
            return [];
        }

        return array_values(array_filter($paths, fn ($path) => is_string($path) && $path !== ''));
    }

    protected function normalizeMediaPath(mixed $path): ?string
    {
        if (is_array($path)) {
            $path = collect($path)->first(fn ($item) => is_string($item) && $item !== '');
        }

        return is_string($path) && $path !== '' ? $path : null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(NewsTag::class, 'news_tag_relations')
            ->using(NewsTagRelation::class)
            ->withTimestamps();
    }
}
