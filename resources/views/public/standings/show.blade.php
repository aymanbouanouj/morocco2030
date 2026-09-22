@extends('public.layouts.app')

@section('title', $group->name.' '.__('Standings').' | '.__('Morocco 2030'))
@section('meta_description', __('Official standings based on completed group-stage matches.'))

@section('content')
    @php
        $matchesPlayed = $group->standings->sum('played');
        $leader = $group->standings->first();
    @endphp

    <div class="entity-ref-page entity-ref-page--standings-detail">
        <header class="entity-ref-hero">
            <div class="entity-ref-hero__content">
                <a href="{{ route('standings.index') }}" class="entity-ref-back-link entity-ref-back-link--light">&larr; {{ __('Back to Standings') }}</a>
                <span class="entity-ref-hero__eyebrow">{{ __('Group').' '.$group->code }}</span>
                <h1>{{ $group->name }}</h1>
                <p>{{ __('Official standings based on completed group-stage matches.') }}</p>

                <div class="entity-ref-meta entity-ref-meta--hero">
                    <span>{{ number_format($group->teams->count()) }} {{ __('teams') }}</span>
                    @if ($matchesPlayed > 0)
                        <span>{{ number_format($matchesPlayed) }} {{ __('matches played') }}</span>
                    @endif
                    @if ($leader?->team)
                        <span>{{ __('Leader') }}: {{ \App\Support\PublicContent::field($leader->team, 'name') ?? $leader->team->name }}</span>
                    @endif
                </div>
            </div>
        </header>

        <div class="entity-ref-detail entity-ref-detail--standings">
            <section class="entity-ref-section entity-ref-section--panel standings-ref-group" aria-labelledby="group-standings-table">
                <h2 id="group-standings-table" class="entity-ref-section__title">{{ __('Group Table') }}</h2>
                @include('public.partials.standings-table', ['standings' => $group->standings])
            </section>

            <aside class="entity-ref-section entity-ref-section--panel" aria-labelledby="group-details-title">
                <h2 id="group-details-title" class="entity-ref-section__title">{{ __('Group Details') }}</h2>
                <div class="entity-ref-detail-grid entity-ref-detail-grid--compact">
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Code') }}</span>
                        <strong>{{ $group->code }}</strong>
                    </article>
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Teams Registered') }}</span>
                        <strong>{{ number_format($group->teams->count()) }}</strong>
                    </article>
                </div>

                @if ($group->description)
                    <div class="entity-ref-copy" style="margin-top: 0.85rem;">{{ $group->description }}</div>
                @endif

                <div class="entity-ref-section__head" style="margin-top: 1rem;">
                    <div>
                        <span class="entity-ref-section__eyebrow">{{ __('Teams') }}</span>
                        <h3>{{ __('Group Members') }}</h3>
                    </div>
                </div>

                @if ($group->teams->isNotEmpty())
                    <div class="entity-ref-related-list">
                        @foreach ($group->teams->sortBy('name') as $team)
                            @php($teamName = \App\Support\PublicContent::field($team, 'name') ?? $team->name)
                            <a href="{{ route('teams.show', $team->slug) }}" class="entity-ref-related-item entity-ref-related-item--text">
                                <span class="entity-ref-related-item__body">
                                    <strong>{{ $teamName }}</strong>
                                    <span>{{ $team->code }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="entity-ref-empty">
                        @include('public.partials.empty-state', ['title' => __('No teams are assigned to this group yet.')])
                    </div>
                @endif
            </aside>
        </div>
    </div>
@endsection
