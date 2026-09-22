@extends('public.layouts.app')

@php($cityName = \App\Support\PublicContent::field($city, 'name') ?? $city->name)
@php($media = \App\Support\PublicMedia::primaryData($city, null, $cityName))
@php($description = \App\Support\PublicContent::field($city, 'description'))
@php($stadiumCount = $city->stadiums->count())
@php($matchCount = $hostedMatches->count())

@section('title', $cityName.' | '.__('Morocco 2030'))
@section('meta_description', __('Explore city details, venues, and hosted matches for Morocco 2030.'))

@section('content')
    <div class="entity-ref-page entity-ref-page--city-detail">
        <header class="entity-ref-hero entity-ref-hero--detail">
            <div class="entity-ref-hero__media">
                @include('public.partials.local-entity-media', [
                    'type' => 'city',
                    'slug' => $city->slug,
                    'name' => $cityName,
                    'media' => $media,
                    'context' => 'hero',
                    'lazy' => false,
                ])
            </div>
            <div class="entity-ref-hero__overlay" aria-hidden="true"></div>
            <div class="entity-ref-hero__content">
                <a href="{{ route('cities.index') }}" class="entity-ref-back-link">&larr; {{ __('Back to Host Cities') }}</a>
                <span class="entity-ref-hero__eyebrow">{{ __('Host City') }}</span>
                <h1>{{ $cityName }}</h1>
                <div class="entity-ref-meta entity-ref-meta--hero">
                    @if ($city->region)
                        <span>{{ $city->region }}</span>
                    @endif
                    @if ($stadiumCount > 0)
                        <span>{{ number_format($stadiumCount) }} {{ __('stadiums') }}</span>
                    @endif
                    @if ($matchCount > 0)
                        <span>{{ number_format($matchCount) }} {{ __('listed matches') }}</span>
                    @endif
                </div>
                <div class="entity-ref-hero__actions">
                    <a href="{{ route('map.index') }}" class="entity-ref-action entity-ref-action--primary">
                        {{ __('Explore Host Map') }}
                    </a>
                    @if ($stadiumCount > 0)
                        <a href="{{ route('stadiums.index') }}" class="entity-ref-action entity-ref-action--ghost">
                            {{ __('Explore Stadiums') }}
                        </a>
                    @endif
                </div>
            </div>
        </header>

        <div class="entity-ref-detail">
            <section class="entity-ref-section" aria-labelledby="city-detail-stats">
                <h2 id="city-detail-stats" class="entity-ref-section__title">{{ __('City Snapshot') }}</h2>
                <div class="entity-ref-detail-grid">
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Region') }}</span>
                        <strong>{{ $city->region ?: __('Not provided') }}</strong>
                    </article>
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Stadiums') }}</span>
                        <strong>{{ number_format($stadiumCount) }}</strong>
                    </article>
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Hosted fixtures') }}</span>
                        <strong>{{ number_format($matchCount) }}</strong>
                    </article>
                    @if ($city->latitude && $city->longitude)
                        <article class="entity-ref-stat entity-ref-stat--card">
                            <span>{{ __('Coordinates') }}</span>
                            <strong>{{ $city->latitude }}, {{ $city->longitude }}</strong>
                        </article>
                    @endif
                </div>
            </section>

            @if ($description)
                <section class="entity-ref-section entity-ref-section--panel" aria-labelledby="city-about-title">
                    <h2 id="city-about-title" class="entity-ref-section__title">{{ __('About The City') }}</h2>
                    <div class="entity-ref-copy">{{ $description }}</div>
                </section>
            @endif

            <section class="entity-ref-section entity-ref-section--panel" aria-labelledby="city-stadiums-title">
                <div class="entity-ref-section__head">
                    <div>
                        <span class="entity-ref-section__eyebrow">{{ __('Venues') }}</span>
                        <h2 id="city-stadiums-title">{{ __('Stadiums In This City') }}</h2>
                    </div>
                    <a href="{{ route('stadiums.index') }}" class="entity-ref-section__link">{{ __('Explore Stadiums') }}</a>
                </div>

                @if ($city->stadiums->isNotEmpty())
                    <div class="entity-ref-grid entity-ref-grid--compact">
                        @foreach ($city->stadiums as $stadium)
                            @php($stadiumName = \App\Support\PublicContent::field($stadium, 'name') ?? $stadium->name)
                            @php($stadiumMedia = \App\Support\PublicMedia::primaryData($stadium, null, $stadiumName))

                            <article class="entity-ref-card entity-ref-card--compact">
                                <a href="{{ route('stadiums.show', $stadium->slug) }}" class="entity-ref-card__media">
                                    @include('public.partials.local-entity-media', [
                                        'type' => 'stadium',
                                        'slug' => $stadium->slug,
                                        'citySlug' => $city->slug,
                                        'name' => $stadiumName,
                                        'media' => $stadiumMedia,
                                        'context' => 'card',
                                    ])
                                </a>
                                <div class="entity-ref-card__body">
                                    <h3>
                                        <a href="{{ route('stadiums.show', $stadium->slug) }}">{{ $stadiumName }}</a>
                                    </h3>
                                    <div class="entity-ref-meta entity-ref-meta--card">
                                        @if ($stadium->capacity)
                                            <span>{{ number_format($stadium->capacity) }} {{ __('capacity') }}</span>
                                        @else
                                            <span>{{ __('Capacity not provided') }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('stadiums.show', $stadium->slug) }}" class="entity-ref-card__cta">
                                        {{ __('View Stadium') }}
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="entity-ref-empty">
                        @include('public.partials.empty-state', ['title' => __('No stadiums are linked to this city yet.')])
                    </div>
                @endif
            </section>

            <section class="entity-ref-section entity-ref-section--panel" aria-labelledby="city-matches-title">
                <div class="entity-ref-section__head">
                    <div>
                        <span class="entity-ref-section__eyebrow">{{ __('Matches') }}</span>
                        <h2 id="city-matches-title">{{ __('Hosted Fixtures') }}</h2>
                    </div>
                    <a href="{{ route('matches.index') }}" class="entity-ref-section__link">{{ __('View All Matches') }}</a>
                </div>

                @if ($hostedMatches->isNotEmpty())
                    <div class="entity-ref-match-feed">
                        @foreach ($hostedMatches as $match)
                            @include('public.partials.match-card', ['match' => $match])
                        @endforeach
                    </div>
                @else
                    <div class="entity-ref-empty">
                        @include('public.partials.empty-state', ['title' => __('No hosted matches are listed yet.')])
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
