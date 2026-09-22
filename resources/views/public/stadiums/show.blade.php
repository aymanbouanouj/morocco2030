@extends('public.layouts.app')

@php($stadiumName = \App\Support\PublicContent::field($stadium, 'name') ?? $stadium->name)
@php($cityName = \App\Support\PublicContent::field($stadium->city, 'name') ?? $stadium->city?->name)
@php($media = \App\Support\PublicMedia::primaryData($stadium, null, $stadiumName))
@php($description = \App\Support\PublicContent::field($stadium, 'description'))
@php($listedMatches = $upcomingMatches->count() + $recentResults->count())

@section('title', $stadiumName.' | '.__('Morocco 2030'))
@section('meta_description', __('Explore venue information and related fixtures for this stadium.'))

@section('content')
    <div class="entity-ref-page entity-ref-page--stadium-detail">
        <header class="entity-ref-hero entity-ref-hero--detail">
            <div class="entity-ref-hero__media">
                @include('public.partials.local-entity-media', [
                    'type' => 'stadium',
                    'slug' => $stadium->slug,
                    'citySlug' => $stadium->city?->slug,
                    'name' => $stadiumName,
                    'media' => $media,
                    'context' => 'hero',
                    'lazy' => false,
                ])
            </div>
            <div class="entity-ref-hero__overlay" aria-hidden="true"></div>
            <div class="entity-ref-hero__content">
                <a href="{{ route('stadiums.index') }}" class="entity-ref-back-link">&larr; {{ __('Back to Stadiums') }}</a>
                <span class="entity-ref-hero__eyebrow">{{ __('Venue') }}</span>
                <h1>{{ $stadiumName }}</h1>
                <div class="entity-ref-meta entity-ref-meta--hero">
                    @if ($stadium->city)
                        <span>{{ $cityName }}</span>
                    @endif
                    @if ($stadium->capacity)
                        <span>{{ number_format($stadium->capacity) }} {{ __('capacity') }}</span>
                    @endif
                    @if ($listedMatches > 0)
                        <span>{{ number_format($listedMatches) }} {{ __('listed matches') }}</span>
                    @endif
                </div>
                <div class="entity-ref-hero__actions">
                    <a href="{{ route('matches.index') }}" class="entity-ref-action entity-ref-action--primary">
                        {{ __('View Fixtures') }}
                    </a>
                    @if ($stadium->city)
                        <a href="{{ route('cities.show', $stadium->city->slug) }}" class="entity-ref-action entity-ref-action--ghost">
                            {{ __('Explore Host City') }}
                        </a>
                    @endif
                </div>
            </div>
        </header>

        <div class="entity-ref-detail">
            <section class="entity-ref-section" aria-labelledby="stadium-detail-stats">
                <h2 id="stadium-detail-stats" class="entity-ref-section__title">{{ __('Venue Snapshot') }}</h2>
                <div class="entity-ref-detail-grid">
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('City') }}</span>
                        <strong>
                            @if ($stadium->city)
                                <a href="{{ route('cities.show', $stadium->city->slug) }}">{{ $cityName }}</a>
                            @else
                                {{ __('Not assigned') }}
                            @endif
                        </strong>
                    </article>
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Capacity') }}</span>
                        <strong>{{ $stadium->capacity ? number_format($stadium->capacity) : __('Not provided') }}</strong>
                    </article>
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Upcoming fixtures') }}</span>
                        <strong>{{ number_format($upcomingMatches->count()) }}</strong>
                    </article>
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Recent results') }}</span>
                        <strong>{{ number_format($recentResults->count()) }}</strong>
                    </article>
                </div>
            </section>

            @if ($description)
                <section class="entity-ref-section entity-ref-section--panel" aria-labelledby="stadium-about-title">
                    <h2 id="stadium-about-title" class="entity-ref-section__title">{{ __('About The Stadium') }}</h2>
                    <div class="entity-ref-copy">{{ $description }}</div>
                </section>
            @endif

            <section class="entity-ref-section entity-ref-section--panel" aria-labelledby="stadium-upcoming-title">
                <div class="entity-ref-section__head">
                    <div>
                        <span class="entity-ref-section__eyebrow">{{ __('Fixtures') }}</span>
                        <h2 id="stadium-upcoming-title">{{ __('Scheduled Matches') }}</h2>
                    </div>
                    <a href="{{ route('matches.index') }}" class="entity-ref-section__link">{{ __('View All Matches') }}</a>
                </div>

                @if ($upcomingMatches->isNotEmpty())
                    <div class="entity-ref-match-feed">
                        @foreach ($upcomingMatches as $match)
                            @include('public.partials.match-card', ['match' => $match])
                        @endforeach
                    </div>
                @else
                    <div class="entity-ref-empty">
                        @include('public.partials.empty-state', ['title' => __('No scheduled matches are listed for this venue yet.')])
                    </div>
                @endif
            </section>

            <section class="entity-ref-section entity-ref-section--panel" aria-labelledby="stadium-results-title">
                <div class="entity-ref-section__head">
                    <div>
                        <span class="entity-ref-section__eyebrow">{{ __('Results') }}</span>
                        <h2 id="stadium-results-title">{{ __('Recent Venue Results') }}</h2>
                    </div>
                    <a href="{{ route('results.index') }}" class="entity-ref-section__link">{{ __('View All Results') }}</a>
                </div>

                @if ($recentResults->isNotEmpty())
                    <div class="entity-ref-match-feed">
                        @foreach ($recentResults as $match)
                            @include('public.partials.match-card', ['match' => $match])
                        @endforeach
                    </div>
                @else
                    <div class="entity-ref-empty">
                        @include('public.partials.empty-state', ['title' => __('No completed matches are listed for this venue yet.')])
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
