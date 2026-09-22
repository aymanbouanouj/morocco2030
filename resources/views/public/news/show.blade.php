@extends('public.layouts.app')

@php($newsTitle = \App\Support\PublicContent::field($news, 'title') ?? $news->title)
@php($newsSummary = \App\Support\PublicContent::field($news, 'summary') ?: $news->summary)
@php($newsBody = \App\Support\PublicContent::field($news, 'body') ?: $news->body)
@php($newsCategory = \App\Support\PublicContent::field($news->category, 'name') ?? $news->category?->name)
@php($media = \App\Support\PublicMedia::primaryData($news, null, $newsTitle))

@section('title', $newsTitle.' | '.__('Morocco 2030'))
@section('meta_description', $newsSummary ?: \Illuminate\Support\Str::limit(strip_tags((string) $newsBody), 150))

@section('content')
    <div class="entity-ref-page entity-ref-page--news-detail">
        <header class="entity-ref-hero entity-ref-hero--detail entity-ref-hero--news">
            <div class="entity-ref-hero__media news-ref-cover">
                @include('public.partials.local-entity-media', [
                    'type' => 'news',
                    'slug' => $news->slug,
                    'name' => $newsTitle,
                    'media' => $media,
                    'context' => 'hero',
                    'lazy' => false,
                ])
            </div>
            <div class="entity-ref-hero__overlay" aria-hidden="true"></div>
            <div class="entity-ref-hero__content">
                <a href="{{ route('news.index') }}" class="entity-ref-back-link">&larr; {{ __('Back to News') }}</a>
                <span class="entity-ref-hero__eyebrow">{{ $newsCategory ?? __('News') }}</span>
                <h1>{{ $newsTitle }}</h1>
                <div class="entity-ref-meta entity-ref-meta--hero">
                    @if ($news->published_at)
                        <time datetime="{{ $news->published_at->toIso8601String() }}">{{ $news->published_at->translatedFormat('M j, Y') }}</time>
                    @endif
                    @if ($news->author)
                        <span>{{ __('By') }} {{ $news->author->name }}</span>
                    @endif
                </div>
            </div>
        </header>

        <div class="entity-ref-detail entity-ref-detail--article">
            <article class="entity-ref-section entity-ref-section--panel article-ref-content">
                @if ($newsSummary)
                    <p class="entity-ref-lead">{{ $newsSummary }}</p>
                @endif

                @if (! empty($news->galleryImageUrls()))
                    <div class="news-ref-gallery" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 0 0 18px;">
                        @foreach ($news->galleryImageUrls() as $galleryUrl)
                            <img src="{{ $galleryUrl }}" alt="" style="width: 140px; height: 94px; object-fit: cover; border-radius: 12px;">
                        @endforeach
                    </div>
                @endif

                @if (filled($news->video_url))
                    <p class="entity-ref-meta" style="margin: 0 0 18px;">
                        <a href="{{ $news->video_url }}" target="_blank" rel="noopener noreferrer">{{ __('Watch video') }}</a>
                    </p>
                @endif

                @if ($newsBody)
                    <div class="entity-ref-copy">{!! nl2br(e($newsBody)) !!}</div>
                @else
                    <div class="entity-ref-empty">
                        @include('public.partials.empty-state', [
                            'title' => __('This article does not have body content yet.'),
                            'message' => __('The full story will appear here when it is published.'),
                        ])
                    </div>
                @endif
            </article>

            <aside class="entity-ref-section entity-ref-section--panel" aria-labelledby="news-related-title">
                <div class="entity-ref-section__head">
                    <div>
                        <span class="entity-ref-section__eyebrow">{{ __('More Coverage') }}</span>
                        <h2 id="news-related-title">{{ __('Related Stories') }}</h2>
                    </div>
                    <a href="{{ route('news.index') }}" class="entity-ref-section__link">{{ __('More News') }}</a>
                </div>

                @if ($relatedNews->isNotEmpty())
                    <div class="entity-ref-related-list">
                        @foreach ($relatedNews as $related)
                            @php($relatedTitle = \App\Support\PublicContent::field($related, 'title') ?? $related->title)
                            @php($relatedMedia = \App\Support\PublicMedia::primaryData($related, null, $relatedTitle))

                            <a href="{{ route('news.show', $related->slug) }}" class="entity-ref-related-item">
                                <span class="entity-ref-related-item__media news-ref-cover news-ref-cover--thumb">
                                    @include('public.partials.local-entity-media', [
                                        'type' => 'news',
                                        'slug' => $related->slug,
                                        'name' => $relatedTitle,
                                        'media' => $relatedMedia,
                                        'context' => 'card',
                                    ])
                                </span>
                                <span class="entity-ref-related-item__body">
                                    <strong>{{ $relatedTitle }}</strong>
                                    <span>{{ $related->published_at?->translatedFormat('M j, Y') ?? __('Published') }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="entity-ref-empty">
                        @include('public.partials.empty-state', [
                            'title' => __('More stories will appear here soon.'),
                        ])
                    </div>
                @endif
            </aside>
        </div>
    </div>
@endsection
