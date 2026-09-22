@extends('admin.layouts.app')

@php
    use App\Models\MediaFile;
    use App\Models\MediaRelation;
    use App\Models\News;
    use App\Models\User;

    $pageTitle = 'Media Review';
    $pageDescription = 'Review and trace visual assets linked to news, profiles, partners, cities, stadiums, teams, and players.';

    $contextualNewsMediaCount = News::query()
        ->where(function ($query) {
            $query->whereNotNull('cover_image_path')
                ->orWhereNotNull('video_url')
                ->orWhereNotNull('gallery_image_paths');
        })
        ->count();

    $recentNewsMedia = News::query()
        ->where(function ($query) {
            $query->whereNotNull('cover_image_path')
                ->orWhereNotNull('video_url')
                ->orWhereNotNull('gallery_image_paths');
        })
        ->latest()
        ->limit(8)
        ->get(['id', 'title', 'slug', 'status', 'cover_image_path', 'video_url', 'gallery_image_paths']);

    $mediaReviewCounts = [
        'news' => max($contextualNewsMediaCount, MediaFile::query()->where('meta->category', 'news')->count()),
        'profiles' => MediaRelation::query()->where('mediable_type', User::class)->distinct()->count('media_file_id'),
        'partners' => MediaFile::query()->where('meta->category', 'partners')->count() + $partnerLogoCount,
        'cities' => MediaFile::query()->where('meta->category', 'cities')->count(),
        'stadiums' => MediaFile::query()->where('meta->category', 'stadiums')->count(),
        'teams' => MediaFile::query()->where('meta->category', 'teams')->count(),
        'players' => MediaFile::query()->where('meta->category', 'players')->count(),
    ];
    $mediaReviewCounts['venues'] = $mediaReviewCounts['cities'] + $mediaReviewCounts['stadiums'];
@endphp

@section('content')
    <section class="panel admin-media-review-page admin-media-review-card">
        <div class="admin-media-review-notice" role="note">
            <strong>Contextual uploads only.</strong>
            Media uploads are managed inside their source modules (News, Profile, Partners, Cities, Stadiums, Teams, Players).
            Use this page to review quality and trace linked assets.
            Generic standalone uploads are not offered from this screen.
        </div>

        <div class="admin-media-review-summary" aria-label="Asset summary by source">
            <div class="admin-media-review-source">
                <span class="admin-media-review-source__label">News assets</span>
                <span class="admin-media-review-source__count">{{ $mediaReviewCounts['news'] }}</span>
            </div>
            <div class="admin-media-review-source">
                <span class="admin-media-review-source__label">Profile avatars</span>
                <span class="admin-media-review-source__count">{{ $mediaReviewCounts['profiles'] }}</span>
            </div>
            <div class="admin-media-review-source">
                <span class="admin-media-review-source__label">Partner logos</span>
                <span class="admin-media-review-source__count">{{ $mediaReviewCounts['partners'] }}</span>
            </div>
            <div class="admin-media-review-source">
                <span class="admin-media-review-source__label">City / Stadium</span>
                <span class="admin-media-review-source__count">{{ $mediaReviewCounts['venues'] }}</span>
            </div>
            <div class="admin-media-review-source">
                <span class="admin-media-review-source__label">Team visuals</span>
                <span class="admin-media-review-source__count">{{ $mediaReviewCounts['teams'] }}</span>
            </div>
            <div class="admin-media-review-source">
                <span class="admin-media-review-source__label">Player photos</span>
                <span class="admin-media-review-source__count">{{ $mediaReviewCounts['players'] }}</span>
            </div>
        </div>

        @if ($recentNewsMedia->isNotEmpty())
            <div class="admin-media-review-notice" style="margin-bottom: 10px;">
                <strong>News contextual assets.</strong>
                Uploaded from News create/edit. Open an article to edit its cover, gallery, or video URL.
            </div>
            <div class="admin-media-review-table-wrap" style="margin-bottom: 12px;">
                <table class="admin-media-review-table">
                    <thead>
                        <tr>
                            <th scope="col">Preview</th>
                            <th scope="col">Source</th>
                            <th scope="col">Linked To</th>
                            <th scope="col">Type</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentNewsMedia as $newsItem)
                            <tr>
                                <td>
                                    @if ($newsItem->coverImageUrl())
                                        <img src="{{ $newsItem->coverImageUrl() }}" alt="" class="admin-media-review-preview">
                                    @else
                                        <span class="admin-media-review-status">—</span>
                                    @endif
                                </td>
                                <td>News</td>
                                <td>{{ $newsItem->title }}</td>
                                <td>
                                    @if ($newsItem->cover_image_path)
                                        Cover
                                    @endif
                                    @if (! empty($newsItem->gallery_image_paths))
                                        @if ($newsItem->cover_image_path) / @endif Gallery
                                    @endif
                                    @if ($newsItem->video_url)
                                        @if ($newsItem->cover_image_path || ! empty($newsItem->gallery_image_paths)) / @endif Video
                                    @endif
                                </td>
                                <td><span class="admin-media-review-status">{{ str_replace('_', ' ', $newsItem->status) }}</span></td>
                                <td class="admin-media-review-actions">
                                    @can('view', $newsItem)
                                        <a class="btn-link" href="{{ route('admin.news.show', $newsItem) }}">View</a>
                                    @endcan
                                    @can('update', $newsItem)
                                        <a class="btn-link" href="{{ route('admin.news.edit', $newsItem) }}">Edit</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <p class="meta admin-media-review-meta">
            {{ number_format($totalVisualAssets) }} total visual asset(s).
            @if ($unattachedAssets > 0)
                {{ number_format($unattachedAssets) }} unattached.
            @endif
            @if ($archivedAssets > 0)
                {{ number_format($archivedAssets) }} archived.
            @endif
        </p>

        <div class="toolbar admin-media-review-toolbar">
            <form method="GET" action="{{ route('admin.media-files.index') }}" class="admin-media-review-filters">
                <input
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Search filename, title, alt text, or linked record"
                    class="admin-media-review-filter admin-media-review-filter--search"
                >
                <select name="type" class="admin-media-review-filter">
                    <option value="">All sources</option>
                    @foreach ($typeOptions as $typeValue => $typeLabel)
                        <option value="{{ $typeValue }}" @selected(($filters['type'] ?? '') === $typeValue)>{{ $typeLabel }}</option>
                    @endforeach
                </select>
                <select name="mime_type" class="admin-media-review-filter">
                    <option value="">All MIME types</option>
                    @foreach ($mimeTypes as $mimeType)
                        <option value="{{ $mimeType }}" @selected(($filters['mime_type'] ?? '') === $mimeType)>{{ $mimeType }}</option>
                    @endforeach
                </select>
                <select name="status" class="admin-media-review-filter">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <select name="attached" class="admin-media-review-filter">
                    <option value="">All linkage</option>
                    <option value="yes" @selected(($filters['attached'] ?? '') === 'yes')>Linked to content</option>
                    <option value="no" @selected(($filters['attached'] ?? '') === 'no')>Unlinked</option>
                </select>
                <button class="btn btn-secondary" type="submit">Filter</button>
                @if (collect($filters ?? [])->filter(fn ($value) => filled($value))->isNotEmpty())
                    <a class="btn btn-secondary" href="{{ route('admin.media-files.index') }}">Clear</a>
                @endif
            </form>
        </div>

        <div class="admin-media-review-table-wrap">
            <table class="admin-media-review-table">
                <thead>
                    <tr>
                        <th scope="col">Preview</th>
                        <th scope="col">Source</th>
                        <th scope="col">Owner</th>
                        <th scope="col">Type</th>
                        <th scope="col">Linked To</th>
                        <th scope="col">Status</th>
                        <th scope="col">Uploaded At</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($partnerLogoAssets as $asset)
                        <tr>
                            <td>
                                @if ($asset['preview_url'])
                                    <img
                                        src="{{ $asset['preview_url'] }}"
                                        alt="{{ $asset['alt_text'] }}"
                                        class="admin-media-review-preview"
                                    >
                                @else
                                    <span class="admin-media-review-status">No preview</span>
                                @endif
                            </td>
                            <td>
                                <span class="admin-media-review-source-tag">{{ $asset['source'] }}</span>
                                <span class="meta">{{ $asset['filename'] }}</span>
                            </td>
                            <td>{{ $asset['owner'] }}</td>
                            <td>
                                <span>{{ $asset['type'] }}</span>
                                <span class="meta">{{ $asset['path'] }}</span>
                            </td>
                            <td>{{ $asset['linked_to'] }}</td>
                            <td>
                                <span class="admin-media-review-status admin-media-review-status--{{ $asset['status'] }}">{{ $asset['status'] }}</span>
                                <span class="meta">{{ $asset['visibility'] }}</span>
                            </td>
                            <td>{{ $asset['uploaded_at']?->format('Y-m-d H:i') ?: '—' }}</td>
                            <td class="admin-media-review-actions">
                                <span class="meta">Reviewed from source module</span>
                            </td>
                        </tr>
                    @endforeach

                    @foreach ($mediaFiles as $mediaFile)
                        @php
                            $previewUrl = $previewUrls[$mediaFile->id] ?? null;
                            $sourceCategory = data_get($mediaFile->meta, 'category', 'generic');
                        @endphp
                        <tr>
                            <td>
                                @if ($previewUrl)
                                    <img
                                        src="{{ $previewUrl }}"
                                        alt="{{ $mediaFile->alt_text ?: $mediaFile->title ?: $mediaFile->original_name }}"
                                        class="admin-media-review-preview"
                                    >
                                @else
                                    <span class="admin-media-review-status">No preview</span>
                                @endif
                            </td>
                            <td>
                                <span class="admin-media-review-source-tag">{{ ucfirst($sourceCategory) }}</span>
                                <span class="meta">{{ $mediaFile->filename }}</span>
                            </td>
                            <td>{{ $mediaFile->uploadedBy?->name ?: '—' }}</td>
                            <td>
                                <span>{{ $mediaFile->mime_type }}</span>
                                <span class="meta">{{ strtoupper($mediaFile->extension ?: 'n/a') }}</span>
                            </td>
                            <td>
                                @if ($mediaFile->media_relations_count > 0)
                                    {{ number_format($mediaFile->media_relations_count) }} linked
                                @else
                                    <span class="meta">Not linked</span>
                                @endif
                            </td>
                            <td>
                                <span class="admin-media-review-status admin-media-review-status--{{ $mediaFile->status }}">{{ $mediaFile->status }}</span>
                                <span class="meta">{{ $mediaFile->visibility }}</span>
                            </td>
                            <td>{{ $mediaFile->created_at?->format('Y-m-d H:i') ?: '—' }}</td>
                            <td class="admin-media-review-actions">
                                @can('view', $mediaFile)
                                    <a class="btn-link" href="{{ route('admin.media-files.show', $mediaFile) }}">View</a>
                                @endcan
                                @can('update', $mediaFile)
                                    <a class="btn-link" href="{{ route('admin.media-files.edit', $mediaFile) }}">Edit metadata</a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach

                    @if ($partnerLogoAssets === [] && $mediaFiles->isEmpty())
                        <tr>
                            <td colspan="8">
                                <div class="admin-media-review-empty">
                                    <div class="admin-media-review-empty__icon" aria-hidden="true">🖼</div>
                                    <strong>No visual assets to review yet.</strong>
                                    <p class="meta">
                                        Assets appear here after contextual uploads from News, Profile, Partners, Cities, Stadiums, Teams, or Players.
                                        Legacy records may also exist from protected admin upload routes.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{ $mediaFiles->links() }}
    </section>
@endsection
