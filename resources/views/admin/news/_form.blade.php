@php
    $isJournalistForm = auth()->user()?->hasPermission('news.manage')
        && ! auth()->user()?->hasAnyPermission(['news.review', 'news.publish']);
@endphp

<div class="panel admin-news-form-card">
    <div class="admin-news-form-head">
        <div>
            <h2 class="section-title">News Content</h2>
            <p class="meta">
                @if ($isJournalistForm)
                    Save a draft first. You can submit it for editorial review after it exists.
                @else
                    Create and maintain editorial content for the publishing workflow.
                @endif
            </p>
        </div>
        @if ($isJournalistForm)
            <span class="admin-news-status-badge admin-news-status admin-news-status--draft">Draft workspace</span>
        @endif
    </div>

    @if ($categories->isEmpty())
        <div class="alert alert-warning">Create at least one news category before publishing news content.</div>
    @endif

    <div class="form-grid">
        <div class="stacked-field">
            <label for="category_id">Category <span aria-hidden="true">*</span></label>
            <select id="category_id" name="category_id" required>
                <option value="">Select category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $news->category_id ?? '') === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="stacked-field">
            <label for="visibility">Visibility <span aria-hidden="true">*</span></label>
            <select id="visibility" name="visibility" required>
                @foreach (['public' => 'Public', 'private' => 'Private'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('visibility', $news->visibility ?? 'public') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('visibility')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="stacked-field full-span">
            <label for="title">Title <span aria-hidden="true">*</span></label>
            <input id="title" name="title" value="{{ old('title', $news->title ?? '') }}" required>
            @error('title')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="stacked-field full-span">
            <label for="slug">Slug <span aria-hidden="true">*</span></label>
            <input id="slug" name="slug" value="{{ old('slug', $news->slug ?? '') }}" required>
            @error('slug')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="stacked-field full-span">
            <label for="summary">Summary</label>
            <textarea id="summary" name="summary">{{ old('summary', $news->summary ?? '') }}</textarea>
            @error('summary')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="stacked-field full-span">
            <label for="body">Body</label>
            <textarea id="body" name="body" style="min-height: 220px;">{{ old('body', $news->body ?? '') }}</textarea>
            @error('body')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        @if (auth()->user()?->hasPermission('news.publish'))
            <div class="stacked-field">
                <label for="featured_at">Featured At</label>
                <input id="featured_at" type="datetime-local" name="featured_at" value="{{ old('featured_at', isset($news?->featured_at) ? $news->featured_at->format('Y-m-d\TH:i') : '') }}">
            </div>

            <div class="stacked-field">
                <label for="published_at">Planned Publish At</label>
                <input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at', isset($news?->published_at) ? $news->published_at->format('Y-m-d\TH:i') : '') }}">
            </div>
        @endif
    </div>
</div>

@include('admin.news._form_media', ['news' => $news ?? null])

<div class="field-inline" style="margin-top: 18px;">
    <button class="btn btn-primary" type="submit" @disabled($categories->isEmpty())>{{ $submitLabel }}</button>
    <a class="btn btn-secondary" href="{{ route('admin.news.index') }}">Cancel</a>
</div>
