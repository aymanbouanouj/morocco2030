@extends('admin.layouts.app')

@php
    $pageTitle = 'News Details';
    $pageDescription = 'Review content, workflow state, and editorial history.';
@endphp

@section('content')
    <div class="grid grid-2">
        <section class="panel">
            <h2 class="section-title">{{ $news->title }}</h2>
            <div class="field-inline" style="margin-bottom: 12px;">
                <span class="status-badge">{{ str_replace('_', ' ', $news->status) }}</span>
                <span class="status-badge">{{ $news->visibility }}</span>
            </div>
            <p class="meta">Category: {{ $news->category?->name }} | Author: {{ $news->author?->name }} | Editor: {{ $news->editor?->name ?: 'Not assigned' }}</p>
            <p class="meta">Slug: {{ $news->slug }}</p>

            @if ($news->hasMedia())
                <div class="admin-news-media-card" style="margin-top: 16px;">
                    <strong>News Media</strong>
                    @if ($news->coverImageUrl())
                        <div class="admin-news-media-preview" style="margin-top: 8px;">
                            <img src="{{ $news->coverImageUrl() }}" alt="{{ $news->media_alt_text ?: $news->title }}" class="admin-news-media-preview__image">
                        </div>
                    @endif
                    @if (! empty($news->galleryImageUrls()))
                        <div class="admin-news-media-gallery" style="margin-top: 10px;">
                            @foreach ($news->galleryImageUrls() as $galleryUrl)
                                <img src="{{ $galleryUrl }}" alt="" class="admin-news-media-gallery__thumb">
                            @endforeach
                        </div>
                    @endif
                    @if (filled($news->video_url))
                        <p class="meta" style="margin-top: 8px;">
                            <a href="{{ $news->video_url }}" target="_blank" rel="noopener noreferrer">Open video</a>
                        </p>
                    @endif
                </div>
            @endif

            <div style="margin-top: 16px;">
                <strong>Summary</strong>
                <p>{{ $news->summary ?: 'No summary provided.' }}</p>
            </div>
            <div style="margin-top: 16px;">
                <strong>Body</strong>
                <div style="white-space: pre-wrap;">{{ $news->body ?: 'No content provided.' }}</div>
            </div>
            <div class="field-inline" style="margin-top: 18px;">
                @can('update', $news)
                    <a class="btn btn-primary" href="{{ route('admin.news.edit', $news) }}">Edit News</a>
                @endcan
            </div>
        </section>

        <section class="panel">
            <h2 class="section-title">Workflow Actions</h2>
            @include('admin.news._workflow-actions', ['news' => $news])

            <div style="margin-top: 24px;">
                <strong>Publishing</strong>
                <p class="meta">Featured at: {{ optional($news->featured_at)->format('Y-m-d H:i') ?: 'Not scheduled' }}</p>
                <p class="meta">Published at: {{ optional($news->published_at)->format('Y-m-d H:i') ?: 'Not published' }}</p>
            </div>
        </section>
    </div>

    <section class="panel" style="margin-top: 20px;">
        <h2 class="section-title">Workflow History</h2>
        @if ($news->editorialWorkflows->isEmpty())
            <p class="meta">No workflow history recorded.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>Status</th>
                            <th>Step</th>
                            <th>Submitted By</th>
                            <th>Reviewed By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($news->editorialWorkflows->sortByDesc('created_at') as $entry)
                            <tr>
                                <td>{{ $entry->created_at->format('Y-m-d H:i') }}</td>
                                <td><span class="status-badge">{{ str_replace('_', ' ', $entry->status) }}</span></td>
                                <td>{{ str_replace('_', ' ', $entry->current_step) }}</td>
                                <td>{{ $entry->submittedBy?->name ?: 'N/A' }}</td>
                                <td>{{ $entry->reviewedBy?->name ?: 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
