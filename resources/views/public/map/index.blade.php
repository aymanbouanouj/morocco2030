@extends('public.layouts.app')

@section('title', __('Host Map').' | '.__('Morocco 2030'))
@section('meta_description', __('Explore host cities and stadiums on the Morocco 2030 map.'))

@push('head')
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        crossorigin=""
    >
@endpush

@section('content')
    @php($selectedLocation = $defaultLocation)
    @php($mapPayload = ['locations' => $mapLocations->values(), 'bounds' => $mapBounds])

    <div class="entity-ref-page entity-ref-page--map">
        <header class="entity-ref-hero">
            <div class="entity-ref-hero__content">
                <span class="entity-ref-hero__eyebrow">{{ __('Host Map') }}</span>
                <h1>{{ __('Host Map') }}</h1>
                <p>{{ __('Host Cities & Stadium Map') }}</p>
                <p>{{ __('Explore Morocco 2030 host cities and stadiums.') }}</p>

                @if ($mapLocations->isNotEmpty())
                    <div class="entity-ref-meta entity-ref-meta--hero">
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($mapLocations->count()) }}</strong>
                            <span>{{ __('Mapped locations') }}</span>
                        </div>
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($mappedCitiesCount) }}</strong>
                            <span>{{ __('Host cities') }}</span>
                        </div>
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($mappedStadiumsCount) }}</strong>
                            <span>{{ __('Stadiums') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </header>

    <section class="page-section map-layout map-ref-shell" aria-labelledby="host-map-title">
        <h2 id="host-map-title" class="sr-only">{{ __('Interactive Host Map') }}</h2>
        <div class="map-shell map-ref-container">
            <div class="map-shell__intro map-shell__intro--premium">
                <div>
                    <span class="section-header__eyebrow">{{ __('Explore') }}</span>
                    <h2>{{ __('Interactive Host Map') }}</h2>
                    <p>{{ __('Follow the tournament footprint across Morocco, from host cities to major venues, then jump directly into the full location pages.') }}</p>
                </div>
            </div>

            <div class="map-command-bar">
                <div>
                    <span class="map-command-bar__eyebrow">{{ __('Morocco Host Network') }}</span>
                    <strong>{{ __('Host Cities & Stadium Map') }}</strong>
                </div>
                <div class="map-filter" role="group" aria-label="{{ __('Location Focus') }}">
                    <button type="button" class="map-filter__button is-active" data-map-filter="all">{{ __('Browse All') }}</button>
                    <button type="button" class="map-filter__button" data-map-filter="city">{{ __('Host City') }}</button>
                    <button type="button" class="map-filter__button" data-map-filter="stadium">{{ __('Stadium') }}</button>
                </div>
            </div>

            <div class="map-overview map-overview--premium">
                <article class="map-overview__card">
                    <strong>{{ $mapLocations->count() }}</strong>
                    <span>{{ __('Mapped locations') }}</span>
                </article>
                <article class="map-overview__card">
                    <strong>{{ $mappedCitiesCount }}</strong>
                    <span>{{ __('Host cities on the map') }}</span>
                </article>
                <article class="map-overview__card">
                    <strong>{{ $mappedStadiumsCount }}</strong>
                    <span>{{ __('Stadiums on the map') }}</span>
                </article>
                <article class="map-overview__card">
                    <strong>{{ $missingCoordinateCount }}</strong>
                    <span>{{ __('Locations awaiting coordinates') }}</span>
                </article>
            </div>

            @if ($mapLocations->isNotEmpty())
                <div
                    id="host-map"
                    class="map-canvas map-canvas--interactive"
                    aria-label="{{ __('Interactive host map') }}"
                    data-default-location="{{ $selectedLocation['id'] ?? '' }}"
                    data-ready="false"
                >
                    <div class="map-canvas__hud">
                        <span>{{ __('Mapped locations') }}</span>
                        <strong>{{ $mapLocations->count() }}</strong>
                    </div>
                    <div class="map-canvas__fallback">
                        <strong>{{ __('Interactive map loading') }}</strong>
                        <span>{{ __('If the map does not appear, the host location panels alongside it remain fully available.') }}</span>
                    </div>
                </div>

                <script type="application/json" id="host-map-data">@json($mapPayload)</script>

                <div class="map-legend">
                    <span class="map-legend__item"><span class="map-legend__dot map-legend__dot--city"></span>{{ __('Host City') }}</span>
                    <span class="map-legend__item"><span class="map-legend__dot map-legend__dot--stadium"></span>{{ __('Stadium') }}</span>
                </div>

                <p class="map-shell__note">{{ __('Select a marker or choose a location from the lists to focus the map and update the location panel.') }}</p>

                <noscript>
                    <div style="margin-top: 1rem;">
                        @include('public.partials.empty-state', [
                            'title' => __('Interactive map tools require JavaScript.'),
                            'message' => __('You can still browse every host city and stadium from the location panels on this page.'),
                        ])
                    </div>
                </noscript>
            @else
                @include('public.partials.empty-state', [
                    'title' => __('Map points are not available yet.'),
                    'message' => __('You can still browse every host city and stadium below.'),
                ])
            @endif
        </div>

        <aside class="map-lists map-ref-panel">
            @if ($selectedLocation)
                <section class="panel map-detail-card" id="map-detail-panel">
                    <div class="map-detail-card__visual map-detail-card__visual--{{ $selectedLocation['kind'] }}" data-map-detail="visual">
                        <span data-map-detail="initial">{{ strtoupper(mb_substr($selectedLocation['label'], 0, 1)) }}</span>
                    </div>

                    <div class="map-detail-card__eyebrow">
                        <span class="badge">{{ __('Location Focus') }}</span>
                        <span class="meta-pill meta-pill--soft" data-map-detail="coordinates">{{ $selectedLocation['coordinates'] }}</span>
                    </div>

                    <div class="map-detail-card__kind" data-map-detail="kind">{{ $selectedLocation['kind_label'] }}</div>
                    <h2 data-map-detail="label">{{ $selectedLocation['label'] }}</h2>
                    <p class="map-detail-card__meta" data-map-detail="meta">{{ $selectedLocation['meta'] }}</p>
                    <p class="map-detail-card__summary" data-map-detail="summary">{{ $selectedLocation['summary'] }}</p>

                    <div class="map-detail-card__stats" data-map-detail="stats">
                        @foreach ($selectedLocation['stats'] as $stat)
                            <div class="map-detail-card__stat">
                                <span>{{ $stat['label'] }}</span>
                                <strong>{{ $stat['value'] }}</strong>
                            </div>
                        @endforeach
                    </div>

                    <div class="map-detail-card__actions">
                        <a href="{{ $selectedLocation['route'] }}" class="button--subtle" data-map-detail="route">{{ $selectedLocation['action_label'] }}</a>
                    </div>
                </section>
            @endif

            <section class="panel map-section-panel">
                <div class="section-header" style="margin-bottom: 0.8rem;">
                    <div>
                        <span class="section-header__eyebrow">{{ __('Cities') }}</span>
                        <h2 style="font-size: 1.35rem;">{{ __('Host Cities') }}</h2>
                    </div>
                    <a href="{{ route('cities.index') }}" class="section-link">{{ __('Browse All') }}</a>
                </div>

                @if ($cities->isNotEmpty())
                    <div class="section-stack">
                        @foreach ($cities as $city)
                            @php($cityName = \App\Support\PublicContent::field($city, 'name') ?? $city->name)
                            @php($citySummary = \App\Support\PublicContent::field($city, 'description') ?? $city->description)
                            @php($hasCoordinates = $city->latitude !== null && $city->longitude !== null)
                            <article class="list-card map-location-card" data-map-kind="city" @if ($hasCoordinates) data-map-card="city-{{ $city->id }}" @endif>
                                <div class="map-location-card__header">
                                    <div>
                                        <span class="section-header__eyebrow">{{ __('Host City') }}</span>
                                        <a href="{{ route('cities.show', $city->slug) }}" class="map-location-card__title">{{ $cityName }}</a>
                                    </div>
                                    @if ($hasCoordinates)
                                        <a href="#host-map" class="map-focus-link" data-map-target="city-{{ $city->id }}">{{ __('View on map') }}</a>
                                    @endif
                                </div>
                                <p class="map-location-card__meta">{{ $city->region ?: __('Region to be confirmed') }}</p>
                                <p class="map-location-card__summary">
                                    {{ \Illuminate\Support\Str::limit($citySummary ?: trans_choice('{1}:count stadium|[2,*]:count stadiums', $city->stadiums_count, ['count' => $city->stadiums_count]).' / '.trans_choice('{1}:count match|[2,*]:count matches', $city->matches_count, ['count' => $city->matches_count]), 120) }}
                                </p>
                                <div class="badge-row">
                                    <span class="meta-pill meta-pill--soft">{{ trans_choice('{1}:count stadium|[2,*]:count stadiums', $city->stadiums_count, ['count' => $city->stadiums_count]) }}</span>
                                    <span class="meta-pill meta-pill--soft">{{ trans_choice('{1}:count match|[2,*]:count matches', $city->matches_count, ['count' => $city->matches_count]) }}</span>
                                    @unless ($hasCoordinates)
                                        <span class="meta-pill meta-pill--soft">{{ __('Map point coming soon') }}</span>
                                    @endunless
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    @include('public.partials.empty-state', ['title' => __('Host city information will appear here soon.')])
                @endif
            </section>

            <section class="panel map-section-panel">
                <div class="section-header" style="margin-bottom: 0.8rem;">
                    <div>
                        <span class="section-header__eyebrow">{{ __('Venues') }}</span>
                        <h2 style="font-size: 1.35rem;">{{ __('Stadiums') }}</h2>
                    </div>
                    <a href="{{ route('stadiums.index') }}" class="section-link">{{ __('Browse All') }}</a>
                </div>

                @if ($stadiums->isNotEmpty())
                    <div class="section-stack">
                        @foreach ($stadiums as $stadium)
                            @php($stadiumName = \App\Support\PublicContent::field($stadium, 'name') ?? $stadium->name)
                            @php($cityName = \App\Support\PublicContent::field($stadium->city, 'name') ?? $stadium->city?->name)
                            @php($stadiumSummary = $stadium->address ?: ($cityName ? __('Located in :city', ['city' => $cityName]) : __('Tournament venue')))
                            @php($hasCoordinates = $stadium->latitude !== null && $stadium->longitude !== null)
                            <article class="list-card map-location-card" data-map-kind="stadium" @if ($hasCoordinates) data-map-card="stadium-{{ $stadium->id }}" @endif>
                                <div class="map-location-card__header">
                                    <div>
                                        <span class="section-header__eyebrow">{{ __('Stadium') }}</span>
                                        <a href="{{ route('stadiums.show', $stadium->slug) }}" class="map-location-card__title">{{ $stadiumName }}</a>
                                    </div>
                                    @if ($hasCoordinates)
                                        <a href="#host-map" class="map-focus-link" data-map-target="stadium-{{ $stadium->id }}">{{ __('View on map') }}</a>
                                    @endif
                                </div>
                                <p class="map-location-card__meta">{{ $cityName ?? __('City to be confirmed') }}</p>
                                <p class="map-location-card__summary">{{ \Illuminate\Support\Str::limit($stadiumSummary, 120) }}</p>
                                <div class="badge-row">
                                    <span class="meta-pill meta-pill--soft">{{ trans_choice('{1}:count match|[2,*]:count matches', $stadium->matches_count, ['count' => $stadium->matches_count]) }}</span>
                                    @if ($stadium->capacity)
                                        <span class="meta-pill meta-pill--soft">{{ number_format($stadium->capacity) }} {{ __('capacity') }}</span>
                                    @endif
                                    @unless ($hasCoordinates)
                                        <span class="meta-pill meta-pill--soft">{{ __('Map point coming soon') }}</span>
                                    @endunless
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    @include('public.partials.empty-state', ['title' => __('Stadium information will appear here soon.')])
                @endif
            </section>

            @if ($citiesWithoutCoordinates->isNotEmpty() || $stadiumsWithoutCoordinates->isNotEmpty())
                <section class="panel map-section-panel map-section-panel--fallback">
                    <div class="section-header" style="margin-bottom: 0.8rem;">
                        <div>
                            <span class="section-header__eyebrow">{{ __('Fallback') }}</span>
                            <h2 style="font-size: 1.35rem;">{{ __('Locations Awaiting Map Coordinates') }}</h2>
                        </div>
                    </div>

                    <div class="section-stack">
                        @foreach ($citiesWithoutCoordinates as $city)
                            <a href="{{ route('cities.show', $city->slug) }}" class="list-card map-missing-card">
                                <strong>{{ \App\Support\PublicContent::field($city, 'name') ?? $city->name }}</strong>
                                <span>{{ __('City map point coming soon') }}</span>
                            </a>
                        @endforeach
                        @foreach ($stadiumsWithoutCoordinates as $stadium)
                            <a href="{{ route('stadiums.show', $stadium->slug) }}" class="list-card map-missing-card">
                                <strong>{{ \App\Support\PublicContent::field($stadium, 'name') ?? $stadium->name }}</strong>
                                <span>{{ __('Stadium map point coming soon') }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </aside>
    </section>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        (() => {
            const mapElement = document.getElementById('host-map');
            const payloadElement = document.getElementById('host-map-data');

            if (!mapElement || !payloadElement || typeof window.L === 'undefined') {
                return;
            }

            let payload;

            try {
                payload = JSON.parse(payloadElement.textContent || '{}');
            } catch (error) {
                return;
            }

            const locations = Array.isArray(payload.locations) ? payload.locations : [];

            if (!locations.length) {
                return;
            }

            const map = L.map(mapElement, {
                scrollWheelZoom: false,
                zoomControl: true,
                attributionControl: true,
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 18,
            }).addTo(map);

            const detailPanel = document.getElementById('map-detail-panel');
            const detailKind = detailPanel?.querySelector('[data-map-detail="kind"]');
            const detailLabel = detailPanel?.querySelector('[data-map-detail="label"]');
            const detailMeta = detailPanel?.querySelector('[data-map-detail="meta"]');
            const detailSummary = detailPanel?.querySelector('[data-map-detail="summary"]');
            const detailCoordinates = detailPanel?.querySelector('[data-map-detail="coordinates"]');
            const detailStats = detailPanel?.querySelector('[data-map-detail="stats"]');
            const detailRoute = detailPanel?.querySelector('[data-map-detail="route"]');
            const detailVisual = detailPanel?.querySelector('[data-map-detail="visual"]');
            const detailInitial = detailPanel?.querySelector('[data-map-detail="initial"]');
            const focusLinks = Array.from(document.querySelectorAll('[data-map-target]'));
            const locationCards = Array.from(document.querySelectorAll('[data-map-card]'));
            const filterButtons = Array.from(document.querySelectorAll('[data-map-filter]'));
            const markers = new Map();
            let activeFilter = 'all';

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function markerIcon(kind) {
                return L.divIcon({
                    className: 'leaflet-host-marker-wrap',
                    html: '<span class="leaflet-host-marker leaflet-host-marker--' + kind + '"><span class="leaflet-host-marker__pulse"></span><span class="leaflet-host-marker__core"></span></span>',
                    iconSize: [28, 34],
                    iconAnchor: [14, 32],
                    popupAnchor: [0, -28],
                });
            }

            function popupMarkup(location) {
                return ''
                    + '<div class="map-popup">'
                    + '<strong>' + escapeHtml(location.label) + '</strong>'
                    + '<span>' + escapeHtml(location.meta) + '</span>'
                    + '<a href="' + escapeHtml(location.route) + '">' + escapeHtml(location.action_label) + '</a>'
                    + '</div>';
            }

            function renderStats(stats) {
                if (!detailStats) {
                    return;
                }

                detailStats.innerHTML = (stats || []).map((stat) => {
                    return ''
                        + '<div class="map-detail-card__stat">'
                        + '<span>' + escapeHtml(stat.label) + '</span>'
                        + '<strong>' + escapeHtml(stat.value) + '</strong>'
                        + '</div>';
                }).join('');
            }

            function setActiveLocation(locationId) {
                focusLinks.forEach((link) => {
                    link.classList.toggle('is-active', link.dataset.mapTarget === locationId);
                });

                locationCards.forEach((card) => {
                    card.classList.toggle('is-active', card.dataset.mapCard === locationId);
                });
            }

            function updateDetail(location) {
                if (!detailPanel) {
                    return;
                }

                if (detailKind) detailKind.textContent = location.kind_label;
                if (detailLabel) detailLabel.textContent = location.label;
                if (detailMeta) detailMeta.textContent = location.meta;
                if (detailSummary) detailSummary.textContent = location.summary;
                if (detailCoordinates) detailCoordinates.textContent = location.coordinates;

                if (detailRoute) {
                    detailRoute.textContent = location.action_label;
                    detailRoute.setAttribute('href', location.route);
                }

                if (detailVisual) {
                    detailVisual.classList.toggle('map-detail-card__visual--city', location.kind === 'city');
                    detailVisual.classList.toggle('map-detail-card__visual--stadium', location.kind === 'stadium');
                }

                if (detailInitial) {
                    detailInitial.textContent = String(location.label || '?').slice(0, 1).toUpperCase();
                }

                renderStats(location.stats);
                setActiveLocation(location.id);
            }

            function focusLocation(locationId, flyToZoom) {
                const entry = markers.get(locationId);

                if (!entry) {
                    return;
                }

                updateDetail(entry.location);
                map.flyTo(entry.marker.getLatLng(), flyToZoom ?? (entry.location.kind === 'city' ? 6.6 : 8.5), {
                    duration: 0.65,
                });
                entry.marker.openPopup();
            }

            function filterMatches(location, filter) {
                return filter === 'all' || location.kind === filter;
            }

            function applyFilter(filter) {
                activeFilter = filter;

                filterButtons.forEach((button) => {
                    button.classList.toggle('is-active', button.dataset.mapFilter === filter);
                    button.setAttribute('aria-pressed', button.dataset.mapFilter === filter ? 'true' : 'false');
                });

                markers.forEach((entry) => {
                    if (filterMatches(entry.location, filter)) {
                        if (!map.hasLayer(entry.marker)) {
                            entry.marker.addTo(map);
                        }
                    } else if (map.hasLayer(entry.marker)) {
                        map.removeLayer(entry.marker);
                    }
                });

                document.querySelectorAll('[data-map-kind]').forEach((card) => {
                    const matches = filter === 'all' || card.dataset.mapKind === filter;
                    card.hidden = !matches;
                });

                const visibleLocation = locations.find((location) => filterMatches(location, filter));

                if (visibleLocation) {
                    focusLocation(visibleLocation.id, visibleLocation.kind === 'city' ? 6.4 : 8);
                }
            }

            locations.forEach((location) => {
                const marker = L.marker([location.latitude, location.longitude], {
                    icon: markerIcon(location.kind),
                    title: location.label,
                });

                marker.bindPopup(popupMarkup(location));
                marker.on('click', () => updateDetail(location));
                marker.addTo(map);

                markers.set(location.id, { marker, location });
            });

            if (payload.bounds && Array.isArray(payload.bounds) && payload.bounds.length === 2) {
                const southWest = payload.bounds[0];
                const northEast = payload.bounds[1];

                if (southWest[0] === northEast[0] && southWest[1] === northEast[1]) {
                    map.setView([southWest[0], southWest[1]], locations[0].kind === 'city' ? 6.6 : 8.5);
                } else {
                    map.fitBounds(payload.bounds, {
                        padding: [34, 34],
                        maxZoom: 8.5,
                    });
                }
            } else {
                map.setView([31.7917, -7.0926], 5.8);
            }

            focusLinks.forEach((link) => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    const entry = markers.get(link.dataset.mapTarget);

                    if (entry && !filterMatches(entry.location, activeFilter)) {
                        applyFilter('all');
                    }

                    focusLocation(link.dataset.mapTarget);
                });
            });

            filterButtons.forEach((button) => {
                button.setAttribute('aria-pressed', button.classList.contains('is-active') ? 'true' : 'false');
                button.addEventListener('click', () => applyFilter(button.dataset.mapFilter || 'all'));
            });

            const defaultLocationId = mapElement.dataset.defaultLocation || locations[0].id;
            focusLocation(defaultLocationId, locations[0].kind === 'city' ? 6.2 : 8);
            mapElement.setAttribute('data-ready', 'true');
        })();
    </script>
@endpush
