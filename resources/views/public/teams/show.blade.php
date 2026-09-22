@extends('public.layouts.app')

@php($teamName = \App\Support\PublicContent::field($team, 'name') ?? $team->name)
@php($media = \App\Support\PublicMedia::primaryData($team, null, $teamName))
@php($description = \App\Support\PublicContent::field($team, 'description'))
@php($squadCount = $team->players->count())
@php($fixtureCount = $upcomingMatches->count() + $recentMatches->count())

@section('title', $teamName.' | '.__('Morocco 2030'))
@section('meta_description', __('View squad information, team details, and recent tournament fixtures.'))

@section('content')
    <div class="entity-ref-page entity-ref-page--team-detail">
        <header class="entity-ref-hero entity-ref-hero--detail entity-ref-hero--team">
            <div class="entity-ref-hero__media entity-ref-hero__media--flag team-ref-flag team-ref-flag--hero">
                @include('public.partials.local-entity-media', [
                    'type' => 'team',
                    'model' => $team,
                    'slug' => $team->slug,
                    'code' => $team->code,
                    'name' => $teamName,
                    'media' => $media,
                    'context' => 'hero',
                    'lazy' => false,
                ])
            </div>
            <div class="entity-ref-hero__overlay" aria-hidden="true"></div>
            <div class="entity-ref-hero__content">
                <a href="{{ route('teams.index') }}" class="entity-ref-back-link">&larr; {{ __('Back to Teams') }}</a>
                <span class="entity-ref-hero__eyebrow">{{ $team->group?->name ?? __('Team') }}</span>
                <h1>{{ $teamName }}</h1>
                <div class="entity-ref-meta entity-ref-meta--hero">
                    <span>{{ $team->code }}</span>
                    @if ($squadCount > 0)
                        <span>{{ number_format($squadCount) }} {{ __('players') }}</span>
                    @endif
                    @if ($fixtureCount > 0)
                        <span>{{ number_format($fixtureCount) }} {{ __('listed matches') }}</span>
                    @endif
                </div>
                <div class="entity-ref-hero__actions">
                    <a href="{{ route('matches.index') }}" class="entity-ref-action entity-ref-action--primary">
                        {{ __('View Fixtures') }}
                    </a>
                    @if ($team->group)
                        <a href="{{ route('standings.show', $team->group->code) }}" class="entity-ref-action entity-ref-action--ghost">
                            {{ __('View Standings') }}
                        </a>
                    @else
                        <a href="{{ route('standings.index') }}" class="entity-ref-action entity-ref-action--ghost">
                            {{ __('View Standings') }}
                        </a>
                    @endif
                </div>
            </div>
        </header>

        <div class="entity-ref-detail">
            <section class="entity-ref-section" aria-labelledby="team-detail-stats">
                <h2 id="team-detail-stats" class="entity-ref-section__title">{{ __('Team Snapshot') }}</h2>
                <div class="entity-ref-detail-grid">
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Group') }}</span>
                        <strong>{{ $team->group?->name ?? __('Not assigned') }}</strong>
                    </article>
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Coach') }}</span>
                        <strong>{{ $team->coach_name ?: __('Not provided') }}</strong>
                    </article>
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Federation') }}</span>
                        <strong>{{ $team->federation_name ?: __('Not provided') }}</strong>
                    </article>
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Squad size') }}</span>
                        <strong>{{ number_format($squadCount) }}</strong>
                    </article>
                </div>
            </section>

            @if ($description)
                <section class="entity-ref-section entity-ref-section--panel" aria-labelledby="team-about-title">
                    <h2 id="team-about-title" class="entity-ref-section__title">{{ __('About The Team') }}</h2>
                    <div class="entity-ref-copy">{{ $description }}</div>
                </section>
            @endif

            <section class="entity-ref-section entity-ref-section--panel" aria-labelledby="team-squad-title">
                <div class="entity-ref-section__head">
                    <div>
                        <span class="entity-ref-section__eyebrow">{{ __('Squad') }}</span>
                        <h2 id="team-squad-title">{{ __('Players') }}</h2>
                    </div>
                </div>

                @if ($team->players->isNotEmpty())
                    <div class="entity-ref-grid entity-ref-grid--players entity-ref-grid--compact">
                        @foreach ($team->players as $player)
                            @php($playerName = \App\Support\PublicContent::field($player, 'display_name') ?? $player->display_name)
                            @php($playerMedia = \App\Support\PublicMedia::primaryData($player, null, $playerName))

                            <article class="entity-ref-card entity-ref-card--player entity-ref-card--compact">
                                <a href="{{ route('players.show', $player->slug) }}" class="entity-ref-card__media entity-ref-card__media--avatar player-ref-avatar">
                                    @include('public.partials.local-entity-media', [
                                        'type' => 'player',
                                        'slug' => $player->slug,
                                        'code' => $player->nationality_code,
                                        'name' => $playerName,
                                        'media' => $playerMedia,
                                        'context' => 'card',
                                    ])
                                </a>
                                <div class="entity-ref-card__body">
                                    <h3>
                                        <a href="{{ route('players.show', $player->slug) }}">{{ $playerName }}</a>
                                    </h3>
                                    <div class="entity-ref-meta entity-ref-meta--card">
                                        @if ($player->shirt_number)
                                            <span>#{{ $player->shirt_number }}</span>
                                        @endif
                                        <span>{{ \App\Support\TournamentFormatting::playerPositionLabel($player->position) }}</span>
                                    </div>
                                    <a href="{{ route('players.show', $player->slug) }}" class="entity-ref-card__cta">
                                        {{ __('View Player') }}
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="entity-ref-empty">
                        @include('public.partials.empty-state', ['title' => __('No players are listed for this team yet.')])
                    </div>
                @endif
            </section>

            <section class="entity-ref-section entity-ref-section--panel" aria-labelledby="team-upcoming-title">
                <div class="entity-ref-section__head">
                    <div>
                        <span class="entity-ref-section__eyebrow">{{ __('Fixtures') }}</span>
                        <h2 id="team-upcoming-title">{{ __('Upcoming Matches') }}</h2>
                    </div>
                    <a href="{{ route('matches.index') }}" class="entity-ref-section__link">{{ __('View All Matches') }}</a>
                </div>

                @if ($upcomingMatches->isNotEmpty())
                    <div class="entity-ref-match-feed">
                        @foreach ($upcomingMatches as $match)
                            @include('public.partials.match-card', ['match' => $match])
                        @endforeach
                    </div>
                @else
                    <div class="entity-ref-empty">
                        @include('public.partials.empty-state', ['title' => __('No upcoming matches listed.')])
                    </div>
                @endif
            </section>

            <section class="entity-ref-section entity-ref-section--panel" aria-labelledby="team-results-title">
                <div class="entity-ref-section__head">
                    <div>
                        <span class="entity-ref-section__eyebrow">{{ __('Results') }}</span>
                        <h2 id="team-results-title">{{ __('Recent Results') }}</h2>
                    </div>
                    <a href="{{ route('results.index') }}" class="entity-ref-section__link">{{ __('View All Results') }}</a>
                </div>

                @if ($recentMatches->isNotEmpty())
                    <div class="entity-ref-match-feed">
                        @foreach ($recentMatches as $match)
                            @include('public.partials.match-card', ['match' => $match])
                        @endforeach
                    </div>
                @else
                    <div class="entity-ref-empty">
                        @include('public.partials.empty-state', ['title' => __('No recent completed matches listed.')])
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
