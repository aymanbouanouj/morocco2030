@php
    $newsModel = $news ?? null;
    $galleryPaths = $newsModel?->normalizedGalleryPaths() ?? [];
@endphp

<div class="panel admin-news-media-card">
    <h2 class="section-title">News Media</h2>
    <p class="admin-news-media-help meta">
        Upload media here because news assets belong to the article, not to the Media Review page.
    </p>

    <div class="admin-news-media-grid">
        <div class="stacked-field full-span">
            <label for="cover_image">Cover image</label>
            <input id="cover_image" type="file" name="cover_image" accept="image/jpeg,image/png,image/webp">
            <span class="meta">JPG, PNG, or WebP. Max 4 MB.</span>
            @error('cover_image')
                <span class="error">{{ $message }}</span>
            @enderror
            @if ($newsModel?->coverImageUrl())
                <div class="admin-news-media-preview">
                    <img src="{{ $newsModel->coverImageUrl() }}" alt="{{ $newsModel->media_alt_text ?: $newsModel->title }}" class="admin-news-media-preview__image">
                    <span class="meta">Current cover image</span>
                </div>
            @endif
        </div>

        <div class="stacked-field full-span">
            <label for="media_alt_text">Cover image alt text</label>
            <input id="media_alt_text" name="media_alt_text" maxlength="180" value="{{ old('media_alt_text', $newsModel->media_alt_text ?? '') }}" placeholder="Describe the cover for accessibility">
            @error('media_alt_text')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="stacked-field full-span">
            <label for="gallery_images">Gallery images</label>
            <input id="gallery_images" type="file" name="gallery_images[]" accept="image/jpeg,image/png,image/webp" multiple>
            <span class="meta">Optional. Up to 8 images total. JPG, PNG, or WebP. Max 4 MB each.</span>
            @error('gallery_images')
                <span class="error">{{ $message }}</span>
            @enderror
            @error('gallery_images.*')
                <span class="error">{{ $message }}</span>
            @enderror

            @if (! empty($galleryPaths))
                <div class="admin-news-media-gallery" aria-label="Current gallery images">
                    @foreach ($galleryPaths as $path)
                        @php
                            $galleryUrl = \Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])
                                ? $path
                                : \Illuminate\Support\Facades\Storage::disk('public')->url($path);
                        @endphp
                        <label class="admin-news-media-remove">
                            <input type="checkbox" name="remove_gallery_paths[]" value="{{ $path }}" @checked(collect(old('remove_gallery_paths', []))->contains($path))>
                            <img src="{{ $galleryUrl }}" alt="" class="admin-news-media-gallery__thumb">
                            <span class="meta">Remove</span>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="stacked-field full-span">
            <label for="video_url">Video URL</label>
            <input id="video_url" type="url" name="video_url" maxlength="2048" value="{{ old('video_url', $newsModel->video_url ?? '') }}" placeholder="https://www.youtube.com/watch?v=...">
            <span class="meta">Optional external video link (YouTube, Vimeo, etc.). Local video upload is not enabled in this phase.</span>
            @error('video_url')
                <span class="error">{{ $message }}</span>
            @enderror
            @if (filled($newsModel?->video_url))
                <p class="meta"><a href="{{ $newsModel->video_url }}" target="_blank" rel="noopener noreferrer">Open current video</a></p>
            @endif
        </div>
    </div>
</div>
