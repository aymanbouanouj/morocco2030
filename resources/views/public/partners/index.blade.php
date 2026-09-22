@extends('public.layouts.app')

@section('title', __('Partners').' | '.__('Morocco 2030'))
@section('meta_description', __('FIFA Global Partners and official partner information for Morocco 2030.'))

@section('content')
    @php($fifaGlobalPartners = [
        ['name' => 'Aramco', 'logo' => null],
        ['name' => 'adidas', 'logo' => null],
        ['name' => 'ADI Predictstreet', 'logo' => null],
        ['name' => 'Coca-Cola', 'logo' => null],
        ['name' => 'Hyundai', 'logo' => null],
        ['name' => 'Kia', 'logo' => null],
        ['name' => 'Lenovo', 'logo' => null],
        ['name' => 'Qatar Airways', 'logo' => null],
        ['name' => 'Visa', 'logo' => null],
    ])

    <div class="entity-ref-page entity-ref-page--partners">
        <header class="entity-ref-hero">
            <div class="entity-ref-hero__content">
                <span class="entity-ref-hero__eyebrow">{{ __('Partners') }}</span>
                <h1>{{ __('Official Partners') }}</h1>
                <p>{{ __('Meet the partners supporting Morocco 2030.') }}</p>

                @if ($partners->total() > 0)
                    <div class="entity-ref-meta entity-ref-meta--hero">
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($partners->total()) }}</strong>
                            <span>{{ __('Partners') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </header>

        <section class="entity-ref-section fifa-partners-section fifa-partners-section--page fifa-partners-section--strip" aria-labelledby="partners-fifa-global-title">
            <div class="fifa-partners-section__head">
                <div>
                    <span class="fifa-partners-section__eyebrow">{{ __('Official Partners') }}</span>
                    <h2 id="partners-fifa-global-title">{{ __('FIFA Global Partners') }}</h2>
                </div>
                <p>{{ __('Global partners displayed for tournament context.') }}</p>
            </div>

            <div class="fifa-partners-track fifa-partners-track--page" tabindex="0" role="region" aria-label="{{ __('FIFA Global Partners') }}">
                <div class="fifa-partners-grid fifa-partners-grid--page fifa-partners-grid--strip" aria-label="{{ __('FIFA Global Partners') }}">
                @foreach ($fifaGlobalPartners as $fifaPartner)
                    @php($logoExists = $fifaPartner['logo'] && file_exists(public_path($fifaPartner['logo'])))

                    <article class="fifa-partner-card fifa-partner-card--page fifa-partner-card--strip" aria-label="{{ $fifaPartner['name'] }}">
                        <span class="fifa-partner-label">{{ __('Global Partner') }}</span>
                        <div class="fifa-partner-logo">
                            @if ($logoExists)
                                <img
                                    src="{{ asset($fifaPartner['logo']) }}"
                                    alt="{{ $fifaPartner['name'] }}"
                                    loading="lazy"
                                    decoding="async"
                                >
                            @else
                                <span class="fifa-partner-text-logo" aria-label="{{ $fifaPartner['name'] }}">{{ $fifaPartner['name'] }}</span>
                            @endif
                        </div>
                        <strong class="fifa-partner-name">{{ $fifaPartner['name'] }}</strong>
                    </article>
                @endforeach
                </div>
            </div>
        </section>

        <section class="entity-ref-section" aria-labelledby="partners-grid-title">
            <div class="fifa-partners-section__head">
                <div>
                    <span class="fifa-partners-section__eyebrow">{{ __('Local Partners') }}</span>
                    <h2 id="partners-grid-title">{{ __('Morocco 2030 Official Partners') }}</h2>
                </div>
                <p>{{ __('Admin-managed active partners are listed separately from global tournament partners.') }}</p>
            </div>

            @if ($partners->count() > 0)
                <div class="entity-ref-grid entity-ref-grid--partners" data-local-partner-count="{{ $partners->total() }}">
                    @foreach ($partners as $partner)
                        @php($partnerName = \App\Support\PublicContent::field($partner, 'name') ?? $partner->name)
                        @php($media = \App\Support\PublicMedia::primaryData($partner, null, $partnerName))
                        @php($description = \App\Support\PublicContent::field($partner, 'description'))
                        @php($logoUrl = $partner->logoUrl())

                        <article class="entity-ref-card entity-ref-card--partner">
                            <div class="entity-ref-card__media entity-ref-card__media--logo partner-ref-logo">
                                @if ($logoUrl)
                                    <img class="partner-logo" src="{{ $logoUrl }}" alt="{{ $partner->logoAlt($partnerName) }}" loading="lazy" decoding="async">
                                @elseif ($media['url'])
                                    <img class="partner-logo" src="{{ $media['url'] }}" alt="{{ $media['alt'] }}" loading="lazy" decoding="async">
                                @else
                                    <span class="partner-logo partner-logo--text" aria-label="{{ $partnerName }}">{{ str($partnerName)->substr(0, 2)->upper() }}</span>
                                @endif
                            </div>
                            <div class="entity-ref-card__body">
                                <h3>{{ $partnerName }}</h3>
                                <p class="entity-ref-card__location">
                                    {{ $partner->category }}
                                    @if ($partner->tier)
                                        / {{ $partner->tier }}
                                    @endif
                                </p>
                                @if ($description)
                                    <p class="entity-ref-card__excerpt">{{ \Illuminate\Support\Str::limit($description, 120) }}</p>
                                @endif
                                @if ($partner->website_url)
                                    <a href="{{ $partner->website_url }}" target="_blank" rel="noopener noreferrer" class="entity-ref-card__cta">
                                        {{ __('Visit Website') }}
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="entity-ref-pagination">
                    {{ $partners->links('pagination.public') }}
                </div>
            @else
                <div class="entity-ref-empty">
                    @include('public.partials.empty-state', [
                        'title' => __('No active partners are available yet.'),
                        'message' => __('Official partners will appear here as they are announced.'),
                    ])
                </div>
            @endif
        </section>
    </div>
@endsection
