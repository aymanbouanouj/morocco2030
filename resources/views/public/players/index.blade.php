@extends('public.layouts.app')

@section('title', __('Players').' | '.__('Morocco 2030'))
@section('meta_description', __('Browse player profiles from the teams competing in Morocco 2030.'))

@section('content')
    @php
        $teamCount = $players->getCollection()->pluck('team_id')->filter()->unique()->count();
    @endphp

    <div class="entity-ref-page entity-ref-page--players">
        <header class="entity-ref-hero">
            <div class="entity-ref-hero__content">
                <span class="entity-ref-hero__eyebrow">{{ __('Players') }}</span>
                <h1>{{ __('Players') }}</h1>
                <p>{{ __('Discover player profiles across the tournament.') }}</p>

                @if ($players->total() > 0)
                    <div class="entity-ref-meta entity-ref-meta--hero">
                        <div class="entity-ref-stat">
                            <strong>{{ number_format($players->total()) }}</strong>
                            <span>{{ __('Players') }}</span>
                        </div>
                        @if ($teamCount > 0)
                            <div class="entity-ref-stat">
                                <strong>{{ number_format($teamCount) }}</strong>
                                <span>{{ __('Teams') }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </header>

        <section class="entity-ref-section" aria-labelledby="players-grid-title">
            <h2 id="players-grid-title" class="sr-only">{{ __('Player listings') }}</h2>

            @if ($players->count() > 0)
                <div class="entity-ref-grid entity-ref-grid--players">
                    @foreach ($players as $player)
                        @php($playerName = \App\Support\PublicContent::field($player, 'display_name') ?? $player->display_name)
                        @php($teamName = \App\Support\PublicContent::field($player->team, 'name') ?? $player->team?->name)
                        @php($media = \App\Support\PublicMedia::primaryData($player, null, $playerName))

                        <article class="entity-ref-card entity-ref-card--player">
                            <a href="{{ route('players.show', $player->slug) }}" class="entity-ref-card__media entity-ref-card__media--avatar player-ref-avatar">
                                @include('public.partials.local-entity-media', [
                                    'type' => 'player',
                                    'slug' => $player->slug,
                                    'code' => $player->nationality_code,
                                    'name' => $playerName,
                                    'media' => $media,
                                    'context' => 'card',
                                ])
                            </a>
                            <div class="entity-ref-card__body">
                                <h3>
                                    <a href="{{ route('players.show', $player->slug) }}">{{ $playerName }}</a>
                                </h3>
                                <p class="entity-ref-card__location">
                                    @if ($player->team)
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
                                    @endif
                                    <span>{{ $teamName ?? __('Team pending') }}</span>
                                </p>
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

                <div class="entity-ref-pagination">
                    {{ $players->links('pagination.public') }}
                </div>
            @else
                <div class="entity-ref-empty">
                    @include('public.partials.empty-state', [
                        'title' => __('No players are available yet.'),
                        'message' => __('Player profiles will appear here as squads are confirmed.'),
                    ])
                </div>
            @endif
        </section>
    </div>
@endsection
