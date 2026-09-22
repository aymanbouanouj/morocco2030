@extends('public.layouts.app')

@section('title', __('News').' | '.__('Morocco 2030'))
@section('meta_description', __('Official stories, updates, and tournament coverage from Morocco 2030.'))

@section('content')
    <div class="entity-ref-page entity-ref-page--news">
        <header class="entity-ref-hero">
            <div class="entity-ref-hero__content">
                <span class="entity-ref-hero__eyebrow">{{ __('News') }}</span>
                <h1>{{ __('Latest News') }}</h1>
                <p>{{ __('Follow the latest Morocco 2030 updates.') }}</p>

                @if ($newsItems->total() > 0)
                    <div class="entity-ref-meta entity-ref-meta--hero">
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($newsItems->total()) }}</strong>
                            <span>{{ __('Stories') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </header>

        <section class="entity-ref-section" aria-labelledby="news-grid-title">
            <h2 id="news-grid-title" class="sr-only">{{ __('News listings') }}</h2>

            @if ($newsItems->count() > 0)
                <div class="entity-ref-grid entity-ref-grid--news">
                    @foreach ($newsItems as $news)
                        @include('public.partials.news-card', [
                            'news' => $news,
                            'refStyle' => true,
                            'featured' => $loop->first,
                        ])
                    @endforeach
                </div>

                <div class="entity-ref-pagination">
                    {{ $newsItems->links('pagination.public') }}
                </div>
            @else
                <div class="entity-ref-empty">
                    @include('public.partials.empty-state', [
                        'title' => __('No published news is available yet.'),
                        'message' => __('As soon as public stories are published, they will appear here.'),
                    ])
                </div>
            @endif
        </section>
    </div>
@endsection
