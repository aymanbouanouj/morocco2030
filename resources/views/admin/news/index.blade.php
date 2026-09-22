@extends('admin.layouts.app')

@php
    $pageTitle = 'News';
    $pageDescription = 'Create drafts, attach media, and submit stories for editorial review.';
    $isJournalistWorkspace = auth()->user()?->hasPermission('news.manage')
        && ! auth()->user()?->hasAnyPermission(['news.review', 'news.publish']);
@endphp

@section('content')
    <section class="panel admin-news-page admin-news-workspace">
        <div class="admin-news-card">
            <div class="admin-news-hero">
                <div>
                    <h1>News</h1>
                    <p>{{ $pageDescription }}</p>
                </div>

                @if ($canCreateNews)
                    <a class="btn btn-primary admin-news-create" href="{{ route('admin.news.create') }}">Create News</a>
                @endif
            </div>

            @if ($isJournalistWorkspace)
                <div class="admin-news-workflow" aria-label="Journalist news workflow">
                    <strong>Your workflow:</strong>
                    <span>Draft</span>
                    <span aria-hidden="true">-&gt;</span>
                    <span>Submit for review</span>
                    <span aria-hidden="true">-&gt;</span>
                    <span>Editorial approval</span>
                    <span aria-hidden="true">-&gt;</span>
                    <span>Published</span>
                </div>
            @endif

            <div class="admin-news-stat-grid" aria-label="News workflow summary">
                <a href="{{ route('admin.news.index', array_merge(request()->except('status', 'page'), ['status' => 'draft'])) }}" class="admin-news-stat-card admin-news-stat">
                    <span class="admin-news-stat__label">Drafts</span>
                    <span class="admin-news-stat__count">{{ $workflowCounts['draft'] }}</span>
                </a>
                <a href="{{ route('admin.news.index', array_merge(request()->except('status', 'page'), ['status' => 'pending_review'])) }}" class="admin-news-stat-card admin-news-stat">
                    <span class="admin-news-stat__label">In Review</span>
                    <span class="admin-news-stat__count">{{ $workflowCounts['pending_review'] }}</span>
                </a>
                <a href="{{ route('admin.news.index', array_merge(request()->except('status', 'page'), ['status' => 'published'])) }}" class="admin-news-stat-card admin-news-stat">
                    <span class="admin-news-stat__label">Published</span>
                    <span class="admin-news-stat__count">{{ $workflowCounts['published'] }}</span>
                </a>
                <span class="admin-news-stat-card admin-news-stat admin-news-stat--muted">
                    <span class="admin-news-stat__label">Scheduled</span>
                    <span class="admin-news-stat__count">{{ $workflowCounts['scheduled'] }}</span>
                </span>
                @if (! $isJournalistWorkspace || $workflowCounts['archived'] > 0 || ($filters['status'] ?? null) === 'archived')
                    <a href="{{ route('admin.news.index', array_merge(request()->except('status', 'page'), ['status' => 'archived'])) }}" class="admin-news-stat-card admin-news-stat">
                        <span class="admin-news-stat__label">Archived</span>
                        <span class="admin-news-stat__count">{{ $workflowCounts['archived'] }}</span>
                    </a>
                @endif
            </div>

            <div class="toolbar admin-news-toolbar admin-news-filter-bar">
                <form
                    method="GET"
                    action="{{ route('admin.news.index') }}"
                    @class([
                        'admin-news-filters',
                        'admin-news-filters--has-author' => $authors->isNotEmpty(),
                    ])
                >
                    <input
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search title or slug"
                        class="admin-news-filter admin-news-filter--search"
                    >
                    <select name="category_id" class="admin-news-filter admin-news-filter--select">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="admin-news-filter admin-news-filter--select">
                        <option value="">All statuses</option>
                        @foreach (['draft', 'pending_review', 'approved', 'published', 'rejected', 'archived'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                    @if ($authors->isNotEmpty())
                        <select name="author_id" class="admin-news-filter admin-news-filter--select">
                            <option value="">All authors</option>
                            @foreach ($authors as $author)
                                <option value="{{ $author->id }}" @selected((string) ($filters['author_id'] ?? '') === (string) $author->id)>{{ $author->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    <button class="btn btn-secondary admin-news-filter admin-news-filter--btn" type="submit">Filter</button>
                    @if ($hasActiveFilters)
                        <a class="btn btn-secondary admin-news-filter admin-news-filter--btn admin-news-filter--clear" href="{{ route('admin.news.index') }}">Clear</a>
                    @endif
                </form>

                <span class="admin-news-filter-summary">
                    {{ $newsItems->total() }} {{ Str::plural('story', $newsItems->total()) }}
                </span>
            </div>

            <div class="admin-news-table-wrap admin-news-table-wrap--fit">
                <table class="admin-news-table admin-news-table--fit">
                    <colgroup>
                        <col class="admin-news-col-title">
                        <col class="admin-news-col-category">
                        <col class="admin-news-col-status">
                        <col class="admin-news-col-author">
                        <col class="admin-news-col-published">
                        <col class="admin-news-col-actions">
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Category</th>
                            <th scope="col">Status</th>
                            <th scope="col">Author</th>
                            <th scope="col" class="admin-news-col-published-head">Published / Scheduled</th>
                            <th scope="col" class="admin-news-col-actions-head">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($newsItems as $news)
                            <tr>
                            <td class="admin-news-cell-title" title="{{ $news->title }}">
                                <div class="admin-news-cell-title__row">
                                    @if ($news->coverImageUrl())
                                        <img src="{{ $news->coverImageUrl() }}" alt="" class="admin-news-media-preview__thumb" width="40" height="28">
                                    @endif
                                    <div class="admin-news-cell-title__copy">
                                        <strong>{{ $news->title }}</strong>
                                        <span class="meta">{{ $news->slug }}</span>
                                    </div>
                                </div>
                            </td>
                                <td class="admin-news-cell-category">{{ $news->category?->name ?: '—' }}</td>
                                <td>
                                    <span class="admin-news-status-badge admin-news-status admin-news-status--{{ $news->status }}">{{ str_replace('_', ' ', $news->status) }}</span>
                                </td>
                                <td class="admin-news-cell-author">{{ $news->author?->name ?: '—' }}</td>
                                <td class="admin-news-cell-published admin-news-sticky-col admin-news-sticky-col--published">
                                    @if ($news->published_at && $news->published_at->isFuture())
                                        <span class="admin-news-status admin-news-status--scheduled">Scheduled</span>
                                        <span class="meta">{{ $news->published_at->format('Y-m-d H:i') }}</span>
                                    @elseif ($news->published_at)
                                        {{ $news->published_at->format('Y-m-d H:i') }}
                                    @else
                                        <span class="meta">Not published</span>
                                    @endif
                                </td>
                                <td class="admin-news-actions admin-news-sticky-col admin-news-sticky-col--actions">
                                    @include('admin.news._index-row-actions', ['news' => $news])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="admin-news-empty">
                                        <div class="admin-news-empty__icon" aria-hidden="true">News</div>
                                        <strong>{{ $hasActiveFilters ? 'No news items found.' : 'No news created yet.' }}</strong>
                                        <p class="meta">
                                            @if ($hasActiveFilters)
                                                Try adjusting your filters or clear them to see all items in your scope.
                                            @else
                                                Create your first draft and submit it for review.
                                            @endif
                                        </p>
                                        @if ($canCreateNews)
                                            <a class="btn btn-primary" href="{{ route('admin.news.create') }}">Create News</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $newsItems->links() }}
        </div>
    </section>
@endsection
