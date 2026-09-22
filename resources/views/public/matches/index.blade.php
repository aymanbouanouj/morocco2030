@extends('public.layouts.app')

@section('title', __('Matches').' | '.__('Morocco 2030'))
@section('meta_description', __('Track scheduled, live, postponed, and unresolved fixtures using official tournament data.'))

@section('content')
    @php
        $liveCount = $matches->getCollection()->where('status', 'live')->count();
        $venueCount = $matches->getCollection()->pluck('stadium_id')->filter()->unique()->count();
        $cityCount = $matches->getCollection()->pluck('city_id')->filter()->unique()->count();
        $matchesByDate = $matches->getCollection()->groupBy(fn ($match) => $match->match_date?->format('Y-m-d') ?? 'tbc');
    @endphp

    <div class="match-ref-page match-ref-page--fixtures entity-ref-page">
        <header class="entity-ref-hero match-ref-hero">
            <div class="entity-ref-hero__content">
                <span class="entity-ref-hero__eyebrow">{{ __('Fixtures') }}</span>
                <h1>{{ __('Matches') }}</h1>
                <p>{{ __('Explore upcoming Morocco 2030 matches.') }}</p>

                @if ($matches->total() > 0)
                    <div class="entity-ref-meta entity-ref-meta--hero">
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($matches->total()) }}</strong>
                            <span>{{ __('Upcoming Matches') }}</span>
                        </div>
                        @if ($liveCount > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($liveCount) }}</strong>
                                <span>{{ __('Live') }}</span>
                            </div>
                        @endif
                        @if ($venueCount > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($venueCount) }}</strong>
                                <span>{{ __('Venues') }}</span>
                            </div>
                        @elseif ($cityCount > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($cityCount) }}</strong>
                                <span>{{ __('Host cities') }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </header>

        <section class="match-ref-section entity-ref-section" aria-labelledby="fixtures-list-title">
            <h2 id="fixtures-list-title" class="sr-only">{{ __('Fixture listings') }}</h2>

            <div class="match-ref-filter entity-ref-hero__actions">
                <a href="{{ route('results.index') }}" class="entity-ref-action entity-ref-action--ghost">{{ __('View All Results') }}</a>
                <a href="{{ route('knockout.index') }}" class="entity-ref-action entity-ref-action--ghost">{{ __('View Bracket') }}</a>
                <a href="{{ route('standings.index') }}" class="entity-ref-action entity-ref-action--ghost">{{ __('Full Standings') }}</a>
            </div>

            @if (($stageTypes ?? collect())->isNotEmpty())
                <div class="entity-ref-meta" aria-label="{{ __('Fixture stages') }}">
                    @foreach ($stageTypes as $stageType)
                        <span class="meta-pill meta-pill--soft">{{ \App\Support\TournamentFormatting::stageLabel($stageType) }}</span>
                    @endforeach
                </div>
            @endif

            @if ($matches->count() > 0)
                <div class="match-ref-grid">
                    @foreach ($matchesByDate as $dateKey => $dateMatches)
                        <section class="match-ref-day entity-ref-section entity-ref-section--panel">
                            <h3 class="match-ref-day__title">
                                @if ($dateKey === 'tbc')
                                    {{ __('Date TBC') }}
                                @else
                                    {{ \Illuminate\Support\Carbon::parse($dateKey)->translatedFormat('l, M j, Y') }}
                                @endif
                            </h3>
                            <div class="match-feed">
                                @foreach ($dateMatches as $match)
                                    @include('public.partials.match-card', [
                                        'match' => $match,
                                        'refStyle' => true,
                                        'context' => 'fixtures',
                                    ])
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>

                <div class="entity-ref-pagination">
                    {{ $matches->links('pagination.public') }}
                </div>
            @else
                <div class="match-ref-empty entity-ref-empty">
                    @include('public.partials.empty-state', [
                        'title' => __('No fixtures scheduled yet.'),
                        'message' => __('Upcoming and live matches will appear here once they are scheduled.'),
                    ])
                </div>
            @endif
        </section>
    </div>
@endsection
