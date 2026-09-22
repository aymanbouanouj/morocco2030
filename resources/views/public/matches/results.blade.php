@extends('public.layouts.app')

@section('title', __('Results').' | '.__('Morocco 2030'))
@section('meta_description', __('Browse completed fixtures and official scorelines from Morocco 2030.'))

@section('content')
    @php
        $totalGoals = $results->getCollection()->sum(fn ($match) => (int) ($match->home_score ?? 0) + (int) ($match->away_score ?? 0));
        $latestMatch = $results->getCollection()->first();
        $latestDate = $latestMatch?->match_date;
    @endphp

    <div class="match-ref-page match-ref-page--results entity-ref-page">
        <header class="entity-ref-hero match-ref-hero">
            <div class="entity-ref-hero__content">
                <span class="entity-ref-hero__eyebrow">{{ __('Results') }}</span>
                <h1>{{ __('Recent Results') }}</h1>
                <p>{{ __('Review completed matches and scores.') }}</p>

                @if ($results->total() > 0)
                    <div class="entity-ref-meta entity-ref-meta--hero">
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($results->total()) }}</strong>
                            <span>{{ __('Completed') }}</span>
                        </div>
                        @if ($totalGoals > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($totalGoals) }}</strong>
                                <span>{{ __('Goals') }}</span>
                            </div>
                        @endif
                        @if ($latestDate)
                            <div class="entity-ref-stat">
                                <strong>{{ $latestDate->translatedFormat('M j') }}</strong>
                                <span>{{ __('Latest matchday') }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </header>

        <section class="match-ref-section entity-ref-section" aria-labelledby="results-list-title">
            <h2 id="results-list-title" class="sr-only">{{ __('Results listings') }}</h2>

            <div class="match-ref-filter entity-ref-hero__actions">
                <a href="{{ route('matches.index') }}" class="entity-ref-action entity-ref-action--ghost">{{ __('Upcoming Fixtures') }}</a>
                <a href="{{ route('standings.index') }}" class="entity-ref-action entity-ref-action--ghost">{{ __('Full Standings') }}</a>
            </div>

            @if ($results->count() > 0)
                <div class="match-feed match-ref-grid">
                    @foreach ($results as $match)
                        @include('public.partials.match-card', [
                            'match' => $match,
                            'refStyle' => true,
                            'context' => 'results',
                        ])
                    @endforeach
                </div>

                <div class="entity-ref-pagination">
                    {{ $results->links('pagination.public') }}
                </div>
            @else
                <div class="match-ref-empty entity-ref-empty">
                    @include('public.partials.empty-state', [
                        'title' => __('No results available yet.'),
                        'message' => __('Completed fixtures will appear here once matches are played.'),
                    ])
                </div>
            @endif
        </section>
    </div>
@endsection
