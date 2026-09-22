@extends('public.layouts.app')

@section('title', config('app.name', 'MOROCCO 2030'))
@section('meta_description', __('Official fixtures, results, news, and host information for Morocco 2030.'))

@section('content')
    @php($featuredMatch = $upcomingMatches->first() ?? $recentResults->first())
    @php($featuredMatchVenue = $featuredMatch ? (\App\Support\PublicContent::field($featuredMatch->stadium, 'name') ?? $featuredMatch->stadium?->name) : null)
    @php($featuredMatchStadiumImage = \App\Support\AssetFallback::placeholderUrl('stadium'))
    @php($hasNewsSection = $latestNews->isNotEmpty())
    @php($hasMatchesSection = $upcomingMatches->isNotEmpty() || $recentResults->isNotEmpty())
    @php($hasStandingsSection = $standingsPreview->isNotEmpty())
    @php($hasKnockoutSection = $knockoutPreview->isNotEmpty())
    @php($hasPartnersSection = $partnersPreview->isNotEmpty())
    @php($hasMapSection = $homeMapLocations->isNotEmpty())
    @php($hasTournamentMetrics = collect($tournamentMetrics)->sum('value') > 0)
    @php($countdownCompleteMessage = __('The countdown has completed. Tournament coverage continues live.'))
    @php($countdownActiveMessage = __('Time remaining until the opening fixture.'))
    @php($designerHeroBg = \App\Support\AssetFallback::placeholderUrl('generic'))
    @php($designerHostMap = \App\Support\AssetFallback::placeholderUrl('city'))
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

    <section class="home-final-hero" aria-labelledby="home-final-hero-title">
        <div class="home-final-hero__background" style="--home-hero-bg: url('{{ $designerHeroBg }}');" aria-hidden="true"></div>

        <div class="home-final-hero__layout">
            <aside class="home-final-next-match">
                <span class="home-final-next-match__eyebrow">{{ __('Next Match') }}</span>
                @if ($featuredMatch)
                    <p class="home-final-next-match__stage">
                        {{ \App\Support\TournamentFormatting::stageLabel($featuredMatch->stage_type) }}
                        @if ($featuredMatch->group)
                            &middot; {{ $featuredMatch->group->name }}
                        @endif
                    </p>
                    <div class="home-final-next-match__teams">
                        @if ($featuredMatch->homeTeam)
                            @php($featuredHomeName = \App\Support\PublicContent::field($featuredMatch->homeTeam, 'name') ?? $featuredMatch->homeTeam->name)
                            <span class="home-final-next-match__team">
                                <span class="flag-ref-wrap flag-ref-wrap--medium team-ref-flag" aria-hidden="true">
                                    @include('public.partials.local-entity-media', [
                                        'type' => 'team',
                                        'slug' => $featuredMatch->homeTeam->slug,
                                        'code' => $featuredMatch->homeTeam->code,
                                        'name' => $featuredHomeName,
                                        'media' => \App\Support\PublicMedia::primaryData($featuredMatch->homeTeam, null, $featuredHomeName),
                                        'context' => 'card',
                                        'flagSize' => 'medium',
                                        'lazy' => false,
                                    ])
                                </span>
                                <strong>{{ $featuredMatch->slotLabel('home') }}</strong>
                            </span>
                        @else
                            <strong>{{ $featuredMatch->slotLabel('home') }}</strong>
                        @endif
                        <span class="home-final-next-match__vs">{{ __('VS') }}</span>
                        @if ($featuredMatch->awayTeam)
                            @php($featuredAwayName = \App\Support\PublicContent::field($featuredMatch->awayTeam, 'name') ?? $featuredMatch->awayTeam->name)
                            <span class="home-final-next-match__team">
                                <span class="flag-ref-wrap flag-ref-wrap--medium team-ref-flag" aria-hidden="true">
                                    @include('public.partials.local-entity-media', [
                                        'type' => 'team',
                                        'slug' => $featuredMatch->awayTeam->slug,
                                        'code' => $featuredMatch->awayTeam->code,
                                        'name' => $featuredAwayName,
                                        'media' => \App\Support\PublicMedia::primaryData($featuredMatch->awayTeam, null, $featuredAwayName),
                                        'context' => 'card',
                                        'flagSize' => 'medium',
                                        'lazy' => false,
                                    ])
                                </span>
                                <strong>{{ $featuredMatch->slotLabel('away') }}</strong>
                            </span>
                        @else
                            <strong>{{ $featuredMatch->slotLabel('away') }}</strong>
                        @endif
                    </div>
                    <ul class="home-final-next-match__meta">
                        <li>{{ \App\Support\TournamentFormatting::matchDate($featuredMatch->match_date, $featuredMatch->timezone) ?? __('Date TBC') }}</li>
                        <li>{{ $featuredMatchVenue ?? __('Venue pending') }}</li>
                    </ul>
                    <a href="{{ route('matches.show', $featuredMatch->slug) }}" class="home-final-btn home-final-btn--gold">
                        {{ __('View Match Centre') }}
                    </a>
                    <img class="home-final-next-match__venue" src="{{ $featuredMatchStadiumImage }}" alt="" aria-hidden="true">
                @else
                    <p class="home-final-next-match__empty">
                        {{ __('The next headline fixture will appear here once the calendar is confirmed.') }}
                    </p>
                @endif
            </aside>

            <div class="home-final-brand">
                <img
                    class="home-final-brand__logo"
                    src="{{ asset('assets/brand/logo.svg') }}"
                    alt="MOROCCO 2030"
                    width="156"
                    height="108"
                    decoding="async"
                >
                <h1 id="home-final-hero-title" class="home-final-brand__title">{{ config('app.name', 'MOROCCO 2030') }}</h1>
                <p class="home-final-brand__slogan">
                    <span>{{ __('UNITING PASSIONS. INSPIRING GENERATIONS.') }}</span>
                    <span>{{ __('THE WORLD COMES TO MOROCCO.') }}</span>
                </p>
            </div>

            @if ($countdown)
                <aside
                    class="home-final-countdown"
                    data-countdown-target="{{ $countdown['target_iso'] }}"
                    data-countdown-complete="{{ $countdownCompleteMessage }}"
                    data-countdown-active="{{ $countdownActiveMessage }}"
                >
                    <span class="home-final-countdown__eyebrow">{{ __('The Journey Begins In') }}</span>
                    <strong class="home-final-countdown__title">{{ $countdown['title'] }}</strong>
                    <div class="home-final-countdown__grid">
                        @foreach ($countdown['units'] as $unit => $value)
                            <div class="home-final-countdown__unit">
                                <strong data-countdown-unit="{{ $unit }}">{{ str_pad((string) $value, 2, '0', STR_PAD_LEFT) }}</strong>
                                <span>{{ __(ucfirst($unit)) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="home-final-countdown__status" data-countdown-status>
                        {{ $countdown['is_complete'] ? $countdownCompleteMessage : $countdownActiveMessage }}
                    </p>
                    <a href="{{ route('matches.index') }}" class="home-final-btn home-final-btn--primary">
                        {{ __('Explore Morocco 2030') }}
                    </a>
                </aside>
            @endif
        </div>
    </section>

    <section class="home-final-quick home-final-quick--official" aria-labelledby="home-final-quick-title">
        <h2 id="home-final-quick-title" class="home-final-quick__heading">{{ __('Start With The Essentials') }}</h2>
        <div class="home-final-quick__grid" role="list" aria-label="{{ __('Quick tournament links') }}">
                <a href="{{ route('matches.index') }}" class="home-final-quick-card home-final-quick-card--official home-final-quick-card--compact" role="listitem">
                    <span class="home-final-quick-card__icon home-final-quick-icon home-final-quick-icon--red" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="4.5" width="18" height="16" rx="2"/><path d="M8 3v3M16 3v3M3 10h18"/></svg>
                    </span>
                    <span class="home-final-quick-card__body">
                        <strong>{{ __('Fixtures') }}</strong>
                        <span class="home-final-quick-card__meta">{{ __('View all') }}</span>
                    </span>
                </a>
                <a href="{{ route('results.index') }}" class="home-final-quick-card home-final-quick-card--official home-final-quick-card--compact" role="listitem">
                    <span class="home-final-quick-card__icon home-final-quick-icon home-final-quick-icon--green" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5 10 17.5 19 7.5"/></svg>
                    </span>
                    <span class="home-final-quick-card__body">
                        <strong>{{ __('Results') }}</strong>
                        <span class="home-final-quick-card__meta">{{ __('View all') }}</span>
                    </span>
                </a>
                <a href="{{ route('standings.index') }}" class="home-final-quick-card home-final-quick-card--official home-final-quick-card--compact" role="listitem">
                    <span class="home-final-quick-card__icon home-final-quick-icon home-final-quick-icon--gold" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
                    </span>
                    <span class="home-final-quick-card__body">
                        <strong>{{ __('Standings') }}</strong>
                        <span class="home-final-quick-card__meta">{{ __('View all') }}</span>
                    </span>
                </a>
                <a href="{{ route('teams.index') }}" class="home-final-quick-card home-final-quick-card--official home-final-quick-card--compact" role="listitem">
                    <span class="home-final-quick-card__icon home-final-quick-icon home-final-quick-icon--red" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="9" r="3.5"/><circle cx="16.5" cy="12" r="3"/><path d="M4.5 19.5c.8-2.4 2.6-3.8 4.5-3.8s3.7 1.4 4.5 3.8M13.5 16.2c.7 1.6 2 2.8 3.8 3.3" stroke-linecap="round"/></svg>
                    </span>
                    <span class="home-final-quick-card__body">
                        <strong>{{ __('Teams') }}</strong>
                        <span class="home-final-quick-card__meta">{{ __('View all') }}</span>
                    </span>
                </a>
                <a href="{{ route('cities.index') }}" class="home-final-quick-card home-final-quick-card--official home-final-quick-card--compact" role="listitem">
                    <span class="home-final-quick-card__icon home-final-quick-icon home-final-quick-icon--green" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 10v9h6v-5h2v5h6v-9z"/></svg>
                    </span>
                    <span class="home-final-quick-card__body">
                        <strong>{{ __('Host Cities') }}</strong>
                        <span class="home-final-quick-card__meta">{{ __('View all') }}</span>
                    </span>
                </a>
                <a href="{{ route('map.index') }}" class="home-final-quick-card home-final-quick-card--official home-final-quick-card--compact home-final-quick-card--map" role="listitem">
                    <span class="home-final-quick-card__icon home-final-quick-icon home-final-quick-icon--map" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7.5 9 4l6 3.5 6-3.5v11L15 18l-6-3.5L3 18.5z"/><path d="M9 4v11M15 7.5V18.5"/></svg>
                    </span>
                    <span class="home-final-quick-card__body">
                        <strong>{{ __('Host Map') }}</strong>
                        <span class="home-final-quick-card__meta">{{ __('Explore venues') }}</span>
                    </span>
                </a>
                <a href="{{ route('knockout.index') }}" class="home-final-quick-card home-final-quick-card--official home-final-quick-card--compact" role="listitem">
                    <span class="home-final-quick-card__icon home-final-quick-icon home-final-quick-icon--gold" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h10v3a5 5 0 0 1-10 0V4z"/><path d="M9 14h6v2H9zM10 16v4M14 16v4"/></svg>
                    </span>
                    <span class="home-final-quick-card__body">
                        <strong>{{ __('Knockout Bracket') }}</strong>
                        <span class="home-final-quick-card__meta">{{ __('View all') }}</span>
                    </span>
                </a>
        </div>
    </section>

    <section class="home-final-grid">
        @if ($hasMatchesSection)
            <article class="home-final-card home-final-card--fixtures home-final-fixtures">
                <div class="home-final-card__head">
                    <div>
                        <h2>{{ __('Fixtures & Results') }}</h2>
                    </div>
                    <div class="home-final-card__links">
                        <a href="{{ route('matches.index') }}" class="home-final-link">{{ __('View All Matches') }}</a>
                        <a href="{{ route('results.index') }}" class="home-final-link">{{ __('View All Results') }}</a>
                    </div>
                </div>
                @if ($upcomingMatches->isNotEmpty())
                    <div class="home-final-fixtures__preview">
                        <h3 class="home-final-fixtures__subhead">{{ __('Upcoming Matches') }}</h3>
                        <div class="home-final-fixtures-list home-final-fixtures-list--upcoming home-final-stack">
                            @foreach ($upcomingMatches->take(3) as $match)
                                @include('public.partials.match-card', ['match' => $match])
                            @endforeach
                        </div>
                    </div>
                @endif
                @if ($recentResults->isNotEmpty())
                    <div class="home-final-fixtures__sr-data" aria-hidden="true">
                        @foreach ($recentResults->take(3) as $match)
                            @include('public.partials.match-card', ['match' => $match])
                        @endforeach
                    </div>
                @endif
            </article>
        @endif

        @if ($hasMapSection || $citiesPreview->isNotEmpty())
            <article class="home-final-card home-final-card--map home-final-cities">
                <div class="home-final-card__head">
                    <div>
                        <span class="home-final-card__eyebrow">{{ __('Hosts') }}</span>
                        <h2>{{ __('Host Map Preview') }}</h2>
                    </div>
                    <a href="{{ route('map.index') }}" class="home-final-btn home-final-btn--outline">{{ __('Explore Host Map') }}</a>
                </div>
                <div class="home-final-map-preview home-final-map">
                    <div class="home-final-map-frame">
                        <img
                            class="home-final-map-frame__image home-final-map-preview__image"
                            src="{{ $designerHostMap }}"
                            alt="{{ __('Morocco host cities map') }}"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>
                </div>
                <div class="home-final-city-strip-shell scroll-carousel" data-scroll-carousel>
                    <button class="home-final-city-scroll-btn home-final-scroll-btn home-final-scroll-btn--prev scroll-nav scroll-nav--prev" type="button" aria-label="{{ __('Previous host cities') }}" data-scroll-prev disabled aria-disabled="true">&#8249;</button>
                    <div class="home-final-city-strip" data-scroll-track role="list" aria-label="{{ __('Host cities') }}">
                        @if ($hasMapSection)
                            @foreach ($homeMapLocations as $location)
                                <a href="{{ $location['route'] }}" class="home-final-city-chip home-final-cities__item" role="listitem">
                                    <span>{{ $location['kind_label'] }}</span>
                                    <strong>{{ $location['label'] }}</strong>
                                </a>
                            @endforeach
                        @else
                            @foreach ($citiesPreview as $city)
                                @php($cityName = \App\Support\PublicContent::field($city, 'name') ?? $city->name)
                                <a href="{{ route('cities.show', $city->slug) }}" class="home-final-city-chip home-final-cities__item" role="listitem">
                                    <span>{{ __('Host City') }}</span>
                                    <strong>{{ $cityName }}</strong>
                                </a>
                            @endforeach
                        @endif
                    </div>
                    <button class="home-final-city-scroll-btn home-final-scroll-btn home-final-scroll-btn--next scroll-nav scroll-nav--next" type="button" aria-label="{{ __('Next host cities') }}" data-scroll-next>&#8250;</button>
                </div>
            </article>
        @endif

        @if ($stadiumsPreview->isNotEmpty())
            <article class="home-final-card home-final-card--stadiums home-final-stadiums">
                <div class="home-final-card__head">
                    <div>
                        <span class="home-final-card__eyebrow">{{ __('Stadiums') }}</span>
                        <h2>{{ __('Tournament Venues') }}</h2>
                    </div>
                    <a href="{{ route('stadiums.index') }}" class="home-final-link">{{ __('Explore Stadiums') }}</a>
                </div>
                <div class="home-final-stadiums__layout">
                    @php($featuredStadium = $stadiumsPreview->first())
                    @php($featuredStadiumName = \App\Support\PublicContent::field($featuredStadium, 'name') ?? $featuredStadium->name)
                    @php($featuredStadiumCity = \App\Support\PublicContent::field($featuredStadium->city, 'name') ?? $featuredStadium->city?->name)
                    @php($featuredStadiumImage = \App\Support\AssetFallback::placeholderUrl('stadium'))
                    <a href="{{ route('stadiums.show', $featuredStadium->slug) }}" class="home-final-stadiums__featured">
                        <img src="{{ $featuredStadiumImage }}" alt="{{ $featuredStadiumName }}">
                        <div>
                            <strong>{{ $featuredStadiumName }}</strong>
                            <span>{{ $featuredStadiumCity ?? __('City to be confirmed') }}</span>
                        </div>
                    </a>
                    <div class="home-final-stadiums__thumbs">
                        @foreach ($stadiumsPreview->skip(1)->take(2) as $stadiumIndex => $stadium)
                            @php($stadiumName = \App\Support\PublicContent::field($stadium, 'name') ?? $stadium->name)
                            @php($stadiumImage = \App\Support\AssetFallback::placeholderUrl('stadium'))
                            <a href="{{ route('stadiums.show', $stadium->slug) }}" class="home-final-stadiums__thumb">
                                <img src="{{ $stadiumImage }}" alt="{{ $stadiumName }}">
                                <span>{{ $stadiumName }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </article>
        @endif

        @if ($hasStandingsSection)
            <article class="home-final-card home-final-card--standings home-final-standings">
                <div class="home-final-card__head">
                    <div>
                        <span class="home-final-card__eyebrow">{{ __('Groups') }}</span>
                        <h2>{{ __('Standings Preview') }}</h2>
                    </div>
                    <a href="{{ route('standings.index') }}" class="home-final-btn home-final-btn--outline">{{ __('See Full Tables') }}</a>
                </div>
                <div class="home-final-standings__groups">
                    @foreach ($standingsPreview->take(1) as $group)
                        <div class="home-final-standings__group">
                            <div class="home-final-card__head home-final-card__head--compact">
                                <div>
                                    <span class="home-final-card__eyebrow">{{ $group->code }}</span>
                                    <h3>{{ $group->name }}</h3>
                                </div>
                                <a href="{{ route('standings.show', $group->code) }}" class="home-final-link">{{ __('View Group') }}</a>
                            </div>
                            @include('public.partials.standings-table', [
                                'standings' => $group->standings->take(4),
                                'useCompactFlags' => true,
                            ])
                        </div>
                    @endforeach
                </div>
            </article>
        @endif

        @if ($hasKnockoutSection)
            <article class="home-final-card home-final-card--bracket home-final-bracket">
                <div class="home-final-card__head">
                    <div>
                        <span class="home-final-card__eyebrow">{{ __('Bracket') }}</span>
                        <h2>{{ __('Knockout Preview') }}</h2>
                    </div>
                    <a href="{{ route('knockout.index') }}" class="home-final-link">{{ __('View Bracket') }}</a>
                </div>
                <div class="home-final-bracket-list home-final-stack">
                    @foreach ($knockoutPreview->take(2) as $match)
                        @include('public.partials.match-card', ['match' => $match])
                    @endforeach
                </div>
            </article>
        @endif
    </section>

    @if ($hasTournamentMetrics)
        <section class="home-final-card home-final-metrics home-final-metrics--secondary" aria-label="{{ __('Tournament Key Figures') }}">
            <div class="home-final-card__head home-final-card__head--compact">
                <div>
                    <span class="home-final-card__eyebrow">{{ __('Tournament Key Figures') }}</span>
                    <h2>{{ __('Competition Snapshot') }}</h2>
                </div>
            </div>
            <div class="home-final-metrics__grid">
                @foreach ($tournamentMetrics as $metric)
                    <div class="home-final-metrics__item">
                        <strong>{{ number_format($metric['value']) }}</strong>
                        <span>{{ $metric['label'] }}</span>
                        <small>{{ $metric['description'] }}</small>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if ($hasNewsSection)
        <section class="home-final-news">
            <div class="home-final-card__head home-final-card__head--inline">
                <div>
                    <span class="home-final-card__eyebrow">{{ __('Editorial') }}</span>
                    <h2>{{ __('Latest News') }}</h2>
                </div>
                <a href="{{ route('news.index') }}" class="home-final-link">{{ __('More News') }}</a>
            </div>
            <div class="home-final-news__grid">
                @foreach ($latestNews as $news)
                    @include('public.partials.news-card', ['news' => $news])
                @endforeach
            </div>
        </section>
    @endif

    @if ($hasPartnersSection)
        <section class="home-final-partners home-final-card">
            <div class="home-final-card__head home-final-card__head--inline">
                <div>
                    <span class="home-final-card__eyebrow">{{ __('Partners') }}</span>
                    <h2>{{ __('Official Partners') }}</h2>
                </div>
                <a href="{{ route('partners.index') }}" class="home-final-link">{{ __('View All Partners') }}</a>
            </div>
            <div class="home-final-partners__row">
                @foreach ($partnersPreview as $partner)
                    @php($partnerName = \App\Support\PublicContent::field($partner, 'name') ?? $partner->name)
                    @php($media = \App\Support\PublicMedia::primaryData($partner, null, $partnerName))
                    @php($logoUrl = $partner->logoUrl())
                    <article class="home-final-partners__item">
                        <div class="home-final-partners__logo">
                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $partner->logoAlt($partnerName) }}">
                            @elseif ($media['url'])
                                <img src="{{ $media['url'] }}" alt="{{ $media['alt'] }}">
                            @else
                                <span>{{ str($partnerName)->substr(0, 2)->upper() }}</span>
                            @endif
                        </div>
                        <strong>{{ $partnerName }}</strong>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="fifa-partners-section fifa-partners-section--home fifa-partners-section--strip" aria-labelledby="home-fifa-partners-title">
        <div class="fifa-partners-section__head">
            <div>
                <span class="fifa-partners-section__eyebrow">{{ __('Global Partners') }}</span>
                <h2 id="home-fifa-partners-title">{{ __('FIFA Global Partners') }}</h2>
            </div>
        </div>

        <div class="fifa-partners-carousel scroll-carousel" data-scroll-carousel>
            <button class="scroll-nav scroll-nav--prev" type="button" aria-label="{{ __('Previous partners') }}" data-scroll-prev disabled aria-disabled="true">&#8249;</button>

            <div class="fifa-partners-track fifa-partners-track--strip" data-scroll-track tabindex="0" role="region" aria-label="{{ __('FIFA Global Partners') }}">
                <div class="fifa-partners-grid fifa-partners-grid--home fifa-partners-grid--strip" aria-label="{{ __('FIFA Global Partners') }}">
                    @foreach ($fifaGlobalPartners as $fifaPartner)
                        @php($logoExists = $fifaPartner['logo'] && file_exists(public_path($fifaPartner['logo'])))

                        <article class="fifa-partner-card fifa-partner-card--home fifa-partner-card--strip" aria-label="{{ $fifaPartner['name'] }}">
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
                        </article>
                    @endforeach
                </div>
            </div>

            <button class="scroll-nav scroll-nav--next" type="button" aria-label="{{ __('Next partners') }}" data-scroll-next>&#8250;</button>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const countdownElement = document.querySelector('[data-countdown-target]');

            if (!countdownElement) {
                return;
            }

            const target = new Date(countdownElement.dataset.countdownTarget);

            if (Number.isNaN(target.getTime())) {
                return;
            }

            const completeMessage = countdownElement.dataset.countdownComplete;
            const activeMessage = countdownElement.dataset.countdownActive;
            const statusNode = countdownElement.querySelector('[data-countdown-status]');

            const format = (value) => String(value).padStart(2, '0');

            const updateCountdown = () => {
                const remaining = Math.max(0, Math.floor((target.getTime() - Date.now()) / 1000));
                const days = Math.floor(remaining / 86400);
                const hours = Math.floor((remaining % 86400) / 3600);
                const minutes = Math.floor((remaining % 3600) / 60);
                const seconds = remaining % 60;

                const values = { days, hours, minutes, seconds };

                Object.entries(values).forEach(([unit, value]) => {
                    const node = countdownElement.querySelector(`[data-countdown-unit="${unit}"]`);

                    if (node) {
                        node.textContent = format(value);
                    }
                });

                if (statusNode) {
                    statusNode.textContent = remaining === 0 ? completeMessage : activeMessage;
                }
            };

            updateCountdown();
            window.setInterval(updateCountdown, 1000);
        })();
    </script>
@endpush
