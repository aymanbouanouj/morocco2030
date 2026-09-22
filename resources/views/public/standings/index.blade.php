@extends('public.layouts.app')

@section('title', __('Standings').' | '.__('Morocco 2030'))
@section('meta_description', __('View official group standings calculated from completed group-stage results.'))

@section('content')
    @php
        $teamCount = $groups->sum(fn ($group) => $group->standings->count());
        $matchesPlayed = $groups->sum(fn ($group) => $group->standings->sum('played'));
    @endphp

    <div class="entity-ref-page entity-ref-page--standings">
        <header class="entity-ref-hero">
            <div class="entity-ref-hero__content">
                <span class="entity-ref-hero__eyebrow">{{ __('Standings') }}</span>
                <h1>{{ __('Group Standings') }}</h1>
                <p>{{ __('Follow group tables and qualification race.') }}</p>

                @if ($groups->isNotEmpty())
                    <div class="entity-ref-meta entity-ref-meta--hero">
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($groups->count()) }}</strong>
                            <span>{{ __('Groups') }}</span>
                        </div>
                        @if ($teamCount > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($teamCount) }}</strong>
                                <span>{{ __('Teams') }}</span>
                            </div>
                        @endif
                        @if ($matchesPlayed > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($matchesPlayed) }}</strong>
                                <span>{{ __('Matches played') }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </header>

        <section class="entity-ref-section" aria-labelledby="standings-groups-title">
            <h2 id="standings-groups-title" class="sr-only">{{ __('Group standings tables') }}</h2>

            @if ($groups->isNotEmpty())
                <div class="entity-ref-grid entity-ref-grid--standings">
                    @foreach ($groups as $group)
                        <section class="entity-ref-section entity-ref-section--panel standings-ref-group">
                            <div class="entity-ref-section__head">
                                <div>
                                    <span class="entity-ref-section__eyebrow">{{ $group->code }}</span>
                                    <h2>{{ $group->name }}</h2>
                                </div>
                                <a href="{{ route('standings.show', $group->code) }}" class="entity-ref-section__link">{{ __('Open Group') }}</a>
                            </div>

                            @include('public.partials.standings-table', ['standings' => $group->standings])
                        </section>
                    @endforeach
                </div>
            @else
                <div class="entity-ref-empty">
                    @include('public.partials.empty-state', [
                        'title' => __('No groups are available yet.'),
                        'message' => __('Standings will appear here once group data and results are available.'),
                    ])
                </div>
            @endif
        </section>
    </div>
@endsection
