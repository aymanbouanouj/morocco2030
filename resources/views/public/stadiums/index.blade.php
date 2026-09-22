@extends('public.layouts.app')

@section('title', __('Stadiums').' | '.__('Morocco 2030'))
@section('meta_description', __('Browse the stadiums hosting Morocco 2030.'))

@section('content')
    @php
        $totalCapacity = $stadiums->getCollection()->sum(fn ($stadium) => (int) ($stadium->capacity ?? 0));
        $hostCityCount = $stadiums->getCollection()->pluck('city_id')->filter()->unique()->count();
    @endphp

    <div class="entity-ref-page entity-ref-page--stadiums">
        <header class="entity-ref-hero">
            <div class="entity-ref-hero__content">
                <span class="entity-ref-hero__eyebrow">{{ __('Venues') }}</span>
                <h1>{{ __('Stadiums') }}</h1>
                <p>{{ __('Browse the stadiums hosting Morocco 2030.') }}</p>

                @if ($stadiums->total() > 0)
                    <div class="entity-ref-meta entity-ref-meta--hero">
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($stadiums->total()) }}</strong>
                            <span>{{ __('Stadiums') }}</span>
                        </div>
                        @if ($totalCapacity > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($totalCapacity) }}</strong>
                                <span>{{ __('Total capacity') }}</span>
                            </div>
                        @endif
                        @if ($hostCityCount > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($hostCityCount) }}</strong>
                                <span>{{ __('Host cities') }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </header>

        <section class="entity-ref-section" aria-labelledby="stadiums-grid-title">
            <h2 id="stadiums-grid-title" class="sr-only">{{ __('Stadium listings') }}</h2>

            @if ($stadiums->count() > 0)
                <div class="entity-ref-grid">
                    @foreach ($stadiums as $stadium)
                        @php($stadiumName = \App\Support\PublicContent::field($stadium, 'name') ?? $stadium->name)
                        @php($cityName = \App\Support\PublicContent::field($stadium->city, 'name') ?? $stadium->city?->name)
                        @php($media = \App\Support\PublicMedia::primaryData($stadium, null, $stadiumName))

                        <article class="entity-ref-card">
                            <a href="{{ route('stadiums.show', $stadium->slug) }}" class="entity-ref-card__media">
                                @include('public.partials.local-entity-media', [
                                    'type' => 'stadium',
                                    'slug' => $stadium->slug,
                                    'citySlug' => $stadium->city?->slug,
                                    'name' => $stadiumName,
                                    'media' => $media,
                                    'context' => 'card',
                                ])
                            </a>
                            <div class="entity-ref-card__body">
                                <h3>
                                    <a href="{{ route('stadiums.show', $stadium->slug) }}">{{ $stadiumName }}</a>
                                </h3>
                                <p class="entity-ref-card__location">{{ $cityName ?? __('City to be confirmed') }}</p>
                                <div class="entity-ref-meta entity-ref-meta--card">
                                    @if ($stadium->capacity)
                                        <span>{{ number_format($stadium->capacity) }} {{ __('capacity') }}</span>
                                    @endif
                                    @if ($stadium->matches_count)
                                        <span>{{ number_format($stadium->matches_count) }} {{ __('matches') }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('stadiums.show', $stadium->slug) }}" class="entity-ref-card__cta">
                                    {{ __('View Stadium') }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="entity-ref-pagination">
                    {{ $stadiums->links('pagination.public') }}
                </div>
            @else
                <div class="entity-ref-empty">
                    @include('public.partials.empty-state', [
                        'title' => __('No stadiums are available yet.'),
                        'message' => __('Venue details will appear here as stadiums are confirmed.'),
                    ])
                </div>
            @endif
        </section>
    </div>
@endsection
