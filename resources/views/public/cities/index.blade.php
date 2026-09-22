@extends('public.layouts.app')

@section('title', __('Host Cities').' | '.__('Morocco 2030'))
@section('meta_description', __('Discover the host cities welcoming Morocco 2030.'))

@section('content')
    @php
        $totalStadiums = $cities->getCollection()->sum('stadiums_count');
        $totalMatches = $cities->getCollection()->sum('matches_count');
    @endphp

    <div class="entity-ref-page entity-ref-page--cities">
        <header class="entity-ref-hero">
            <div class="entity-ref-hero__content">
                <span class="entity-ref-hero__eyebrow">{{ __('Hosts') }}</span>
                <h1>{{ __('Host Cities') }}</h1>
                <p>{{ __('Discover the host cities welcoming Morocco 2030.') }}</p>

                @if ($cities->total() > 0)
                    <div class="entity-ref-meta entity-ref-meta--hero">
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($cities->total()) }}</strong>
                            <span>{{ __('Host cities') }}</span>
                        </div>
                        @if ($totalStadiums > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($totalStadiums) }}</strong>
                                <span>{{ __('Stadiums') }}</span>
                            </div>
                        @endif
                        @if ($totalMatches > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($totalMatches) }}</strong>
                                <span>{{ __('Matches') }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </header>

        <section class="entity-ref-section" aria-labelledby="cities-grid-title">
            <h2 id="cities-grid-title" class="sr-only">{{ __('Host city listings') }}</h2>

            @if ($cities->count() > 0)
                <div class="entity-ref-grid">
                    @foreach ($cities as $city)
                        @php($cityName = \App\Support\PublicContent::field($city, 'name') ?? $city->name)
                        @php($media = \App\Support\PublicMedia::primaryData($city, null, $cityName))

                        <article class="entity-ref-card">
                            <a href="{{ route('cities.show', $city->slug) }}" class="entity-ref-card__media">
                                @include('public.partials.local-entity-media', [
                                    'type' => 'city',
                                    'slug' => $city->slug,
                                    'name' => $cityName,
                                    'media' => $media,
                                    'context' => 'card',
                                ])
                            </a>
                            <div class="entity-ref-card__body">
                                <h3>
                                    <a href="{{ route('cities.show', $city->slug) }}">{{ $cityName }}</a>
                                </h3>
                                <p class="entity-ref-card__location">{{ $city->region ?: __('Region to be confirmed') }}</p>
                                <div class="entity-ref-meta entity-ref-meta--card">
                                    <span>{{ number_format($city->stadiums_count) }} {{ __('stadiums') }}</span>
                                    <span>{{ number_format($city->matches_count) }} {{ __('matches') }}</span>
                                </div>
                                <a href="{{ route('cities.show', $city->slug) }}" class="entity-ref-card__cta">
                                    {{ __('Explore City') }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="entity-ref-pagination">
                    {{ $cities->links('pagination.public') }}
                </div>
            @else
                <div class="entity-ref-empty">
                    @include('public.partials.empty-state', [
                        'title' => __('No host cities are available yet.'),
                        'message' => __('Host city information will appear here as locations are announced.'),
                    ])
                </div>
            @endif
        </section>
    </div>
@endsection
