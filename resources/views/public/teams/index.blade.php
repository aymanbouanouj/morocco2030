@extends('public.layouts.app')

@section('title', __('Teams').' | '.__('Morocco 2030'))
@section('meta_description', __('Browse the teams competing on the road to Morocco 2030.'))

@section('content')
    @php
        $groupCount = $teams->getCollection()->pluck('group_id')->filter()->unique()->count();
        $playerCount = $teams->getCollection()->sum('players_count');
    @endphp

    <div class="entity-ref-page entity-ref-page--teams">
        <header class="entity-ref-hero">
            <div class="entity-ref-hero__content">
                <span class="entity-ref-hero__eyebrow">{{ __('Teams') }}</span>
                <h1>{{ __('National Teams') }}</h1>
                <p>{{ __('Explore the national teams competing in Morocco 2030.') }}</p>

                @if ($teams->total() > 0)
                    <div class="entity-ref-meta entity-ref-meta--hero">
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($teams->total()) }}</strong>
                            <span>{{ __('Teams') }}</span>
                        </div>
                        @if ($groupCount > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($groupCount) }}</strong>
                                <span>{{ __('Groups') }}</span>
                            </div>
                        @endif
                        @if ($playerCount > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($playerCount) }}</strong>
                                <span>{{ __('Players') }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </header>

        <section class="entity-ref-section" aria-labelledby="teams-grid-title">
            <h2 id="teams-grid-title" class="sr-only">{{ __('Team listings') }}</h2>

            @if ($teams->count() > 0)
                <div class="entity-ref-grid">
                    @foreach ($teams as $team)
                        @php($teamName = \App\Support\PublicContent::field($team, 'name') ?? $team->name)
                        @php($media = \App\Support\PublicMedia::primaryData($team, null, $teamName))

                        <article class="entity-ref-card entity-ref-card--team">
                            <a href="{{ route('teams.show', $team->slug) }}" class="entity-ref-card__media entity-ref-card__media--flag team-ref-flag">
                                @include('public.partials.local-entity-media', [
                                    'type' => 'team',
                                    'model' => $team,
                                    'slug' => $team->slug,
                                    'code' => $team->code,
                                    'name' => $teamName,
                                    'media' => $media,
                                    'context' => 'card',
                                ])
                            </a>
                            <div class="entity-ref-card__body">
                                <h3>
                                    <a href="{{ route('teams.show', $team->slug) }}">{{ $teamName }}</a>
                                </h3>
                                <p class="entity-ref-card__location">{{ $team->group?->name ?? __('Group pending') }}</p>
                                <div class="entity-ref-meta entity-ref-meta--card">
                                    <span>{{ number_format($team->players_count) }} {{ __('players') }}</span>
                                    <span>{{ $team->code }}</span>
                                </div>
                                <a href="{{ route('teams.show', $team->slug) }}" class="entity-ref-card__cta">
                                    {{ __('View Team') }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="entity-ref-pagination">
                    {{ $teams->links('pagination.public') }}
                </div>
            @else
                <div class="entity-ref-empty">
                    @include('public.partials.empty-state', [
                        'title' => __('No teams are available yet.'),
                        'message' => __('Team profiles will appear here as they are confirmed.'),
                    ])
                </div>
            @endif
        </section>
    </div>
@endsection
