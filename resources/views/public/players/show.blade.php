@extends('public.layouts.app')

@php($playerName = \App\Support\PublicContent::field($player, 'display_name') ?? $player->display_name)
@php($playerBio = \App\Support\PublicContent::field($player, 'bio') ?: $player->bio)
@php($teamName = \App\Support\PublicContent::field($player->team, 'name') ?? $player->team?->name)
@php($media = \App\Support\PublicMedia::primaryData($player, null, $playerName))
@php($club = \App\Support\PublicContent::field($player, 'club'))

@section('title', $playerName.' | '.__('Morocco 2030'))
@section('meta_description', __('Official player profile with team assignment and biography where available.'))

@section('content')
    <div class="entity-ref-page entity-ref-page--player-detail">
        <header class="entity-ref-hero entity-ref-hero--detail entity-ref-hero--player">
            <div class="entity-ref-hero__media entity-ref-hero__media--avatar player-ref-avatar player-ref-avatar--hero">
                @include('public.partials.local-entity-media', [
                    'type' => 'player',
                    'slug' => $player->slug,
                    'code' => $player->nationality_code,
                    'name' => $playerName,
                    'media' => $media,
                    'context' => 'hero',
                    'lazy' => false,
                ])
            </div>
            <div class="entity-ref-hero__overlay" aria-hidden="true"></div>
            <div class="entity-ref-hero__content">
                <a href="{{ route('players.index') }}" class="entity-ref-back-link">&larr; {{ __('Back to Players') }}</a>
                <span class="entity-ref-hero__eyebrow">{{ $teamName ?? __('Player Profile') }}</span>
                <h1>{{ $playerName }}</h1>
                <div class="entity-ref-meta entity-ref-meta--hero">
                    @if ($player->shirt_number)
                        <span>#{{ $player->shirt_number }}</span>
                    @endif
                    <span>{{ \App\Support\TournamentFormatting::playerPositionLabel($player->position) }}</span>
                    @if ($player->nationality_code)
                        <span>{{ strtoupper($player->nationality_code) }}</span>
                    @endif
                </div>
                @if ($player->team)
                    <div class="entity-ref-hero__actions">
                        <a href="{{ route('teams.show', $player->team->slug) }}" class="entity-ref-action entity-ref-action--primary">
                            {{ __('View Team') }}
                        </a>
                    </div>
                @endif
            </div>
        </header>

        <div class="entity-ref-detail">
            <section class="entity-ref-section" aria-labelledby="player-detail-stats">
                <h2 id="player-detail-stats" class="entity-ref-section__title">{{ __('Player Snapshot') }}</h2>
                <div class="entity-ref-detail-grid">
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Team') }}</span>
                        <strong>
                            @if ($player->team)
                                <span class="entity-ref-stat__team">
                                    <span class="player-ref-team-flag flag-ref-wrap flag-ref-wrap--small" aria-hidden="true">
                                        @include('public.partials.local-entity-media', [
                                            'type' => 'team',
                                            'slug' => $player->team->slug,
                                            'code' => $player->team->code,
                                            'name' => $teamName,
                                            'media' => \App\Support\PublicMedia::primaryData($player->team, null, $teamName),
                                            'context' => 'table',
                                            'flagSize' => 'small',
                                        ])
                                    </span>
                                    <a href="{{ route('teams.show', $player->team->slug) }}">{{ $teamName }}</a>
                                </span>
                            @else
                                {{ __('Not assigned') }}
                            @endif
                        </strong>
                    </article>
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Position') }}</span>
                        <strong>{{ \App\Support\TournamentFormatting::playerPositionLabel($player->position) ?? __('Not provided') }}</strong>
                    </article>
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Number') }}</span>
                        <strong>{{ $player->shirt_number ? '#'.$player->shirt_number : __('Not assigned') }}</strong>
                    </article>
                    <article class="entity-ref-stat entity-ref-stat--card">
                        <span>{{ __('Nationality') }}</span>
                        <strong>
                            @if ($player->nationality_code)
                                <span class="entity-ref-stat__team">
                                    <span class="player-ref-team-flag flag-ref-wrap flag-ref-wrap--small" aria-hidden="true">
                                        @include('public.partials.local-entity-media', [
                                            'type' => 'player',
                                            'slug' => $player->slug,
                                            'code' => $player->nationality_code,
                                            'name' => $playerName,
                                            'media' => ['url' => null, 'alt' => $playerName],
                                            'context' => 'table',
                                            'flagSize' => 'small',
                                        ])
                                    </span>
                                    <span>{{ strtoupper($player->nationality_code) }}</span>
                                </span>
                            @else
                                {{ __('Not provided') }}
                            @endif
                        </strong>
                    </article>
                    @if ($club)
                        <article class="entity-ref-stat entity-ref-stat--card">
                            <span>{{ __('Club') }}</span>
                            <strong>{{ $club }}</strong>
                        </article>
                    @endif
                    @if ($player->date_of_birth)
                        <article class="entity-ref-stat entity-ref-stat--card">
                            <span>{{ __('Date of Birth') }}</span>
                            <strong>{{ $player->date_of_birth->translatedFormat('M j, Y') }}</strong>
                        </article>
                    @endif
                </div>
            </section>

            @if ($playerBio)
                <section class="entity-ref-section entity-ref-section--panel" aria-labelledby="player-bio-title">
                    <h2 id="player-bio-title" class="entity-ref-section__title">{{ __('Biography') }}</h2>
                    <div class="entity-ref-copy article-ref-content">{{ $playerBio }}</div>
                </section>
            @else
                <section class="entity-ref-section entity-ref-section--panel">
                    <div class="entity-ref-empty">
                        @include('public.partials.empty-state', [
                            'title' => __('No biography is available for this player yet.'),
                        ])
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
