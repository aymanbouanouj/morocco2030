<?php

namespace App\Support;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class NewsMediaStorage
{
    private const COVER_PREFIX = 'news/covers/';

    private const GALLERY_PREFIX = 'news/gallery/';

    public function apply(Request $request, News $news, bool $isUpdate): void
    {
        if ($request->hasFile('cover_image')) {
            $this->deleteLocalPath($news->cover_image_path);
            $news->cover_image_path = $request->file('cover_image')->store('news/covers', 'public');
        }

        if ($request->has('media_alt_text')) {
            $news->media_alt_text = $request->input('media_alt_text');
        }

        if ($request->exists('video_url')) {
            $news->video_url = $request->input('video_url');
        }

        $gallery = $this->resolveGallery($request, $news, $isUpdate);
        $news->gallery_image_paths = $gallery === [] ? null : $gallery;

        $news->save();
    }

    /**
     * @return list<string>
     */
    public function resolveGallery(Request $request, News $news, bool $isUpdate): array
    {
        $paths = $isUpdate ? array_values($news->gallery_image_paths ?? []) : [];

        if ($isUpdate) {
            $remove = array_values(array_unique(array_filter(
                (array) $request->input('remove_gallery_paths', []),
                fn ($path) => is_string($path) && $path !== ''
            )));

            foreach ($remove as $path) {
                if (! in_array($path, $paths, true)) {
                    continue;
                }

                $paths = array_values(array_filter($paths, fn (string $existing) => $existing !== $path));
                $this->deleteLocalPath($path);
            }
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                if (! $file instanceof UploadedFile || ! $file->isValid()) {
                    continue;
                }

                if (count($paths) >= 8) {
                    throw ValidationException::withMessages([
                        'gallery_images' => 'A news article may have at most 8 gallery images.',
                    ]);
                }

                $paths[] = $file->store('news/gallery', 'public');
            }
        }

        return $paths;
    }

    public function deleteLocalPath(?string $path): void
    {
        if (! $this->isDeletableNewsPath($path)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    public function isDeletableNewsPath(?string $path): bool
    {
        if (! is_string($path) || $path === '') {
            return false;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return false;
        }

        return Str::startsWith($path, [self::COVER_PREFIX, self::GALLERY_PREFIX]);
    }

    public static function mediaValidationRules(bool $isUpdate = false): array
    {
        $rules = [
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'gallery_images' => ['nullable', 'array', 'max:8'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'video_url' => ['nullable', 'url', 'max:2048'],
            'media_alt_text' => ['nullable', 'string', 'max:180'],
        ];

        if ($isUpdate) {
            $rules['remove_gallery_paths'] = ['nullable', 'array', 'max:8'];
            $rules['remove_gallery_paths.*'] = ['string', 'max:255'];
        }

        return $rules;
    }
}
