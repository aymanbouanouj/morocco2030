<?php

namespace App\Support;

use App\Models\MediaFile;
use App\Models\MediaRelation;
use App\Models\News;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PublicMedia
{
    public static function primaryData(?Model $model, ?string $role = null, ?string $altFallback = null): array
    {
        if ($model instanceof News) {
            $coverUrl = $model->coverImageUrl();

            if (is_string($coverUrl) && $coverUrl !== '') {
                return [
                    'file' => null,
                    'url' => $coverUrl,
                    'alt' => is_string($model->media_alt_text) && $model->media_alt_text !== ''
                        ? $model->media_alt_text
                        : $altFallback,
                    'caption' => null,
                ];
            }
        }

        $file = self::primaryFile($model, $role);

        return [
            'file' => $file,
            'url' => self::url($file),
            'alt' => $file?->alt_text ?: $file?->title ?: $altFallback,
            'caption' => $file?->caption,
        ];
    }

    public static function primaryFile(?Model $model, ?string $role = null): ?MediaFile
    {
        if (! $model || ! method_exists($model, 'mediaRelations')) {
            return null;
        }

        $relations = $model->relationLoaded('mediaRelations')
            ? $model->getRelation('mediaRelations')->loadMissing('mediaFile')
            : $model->mediaRelations()->with('mediaFile')->get();

        /** @var MediaRelation|null $relation */
        $relation = $relations
            ->filter(function (MediaRelation $relation) use ($role) {
                $file = $relation->mediaFile;

                return $file
                    && $file->status === 'active'
                    && $file->visibility === 'public'
                    && ($role === null || $relation->role === $role);
            })
            ->sort(function (MediaRelation $left, MediaRelation $right) {
                return
                    ($right->is_primary <=> $left->is_primary)
                    ?: ($left->sort_order <=> $right->sort_order)
                    ?: ($left->id <=> $right->id);
            })
            ->first();

        return $relation?->mediaFile;
    }

    public static function url(?MediaFile $file): ?string
    {
        if (! $file) {
            return null;
        }

        return Storage::disk($file->disk)->url($file->path);
    }
}
