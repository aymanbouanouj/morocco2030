@extends('public.layouts.app')

@section('title', $match->slotLabel('home').' '.__('VS').' '.$match->slotLabel('away').' | '.__('Morocco 2030'))
@section('meta_description', __('Official match centre with scoreline, venue, timeline, statistics, and lineups where available.'))

@section('content')
    @php
        $homeTeamName = $match->slotLabel('home');
        $awayTeamName = $match->slotLabel('away');
        $homeTeam = $match->homeTeam;
        $awayTeam = $match->awayTeam;
        $homeTeamDisplay = $homeTeam ? (\App\Support\PublicContent::field($homeTeam, 'name') ?? $homeTeam->name) : $homeTeamName;
        $awayTeamDisplay = $awayTeam ? (\App\Support\PublicContent::field($awayTeam, 'name') ?? $awayTeam->name) : $awayTeamName;
        $stageLabel = \App\Support\TournamentFormatting::stageLabel($match->stage_type);
        $statusLabel = \App\Support\TournamentFormatting::statusLabel($match->status);
        $matchDate = \App\Support\TournamentFormatting::matchDate($match->match_date, $match->timezone) ?? __('Date TBC');
        $venueName = \App\Support\PublicContent::field($match->stadium, 'name') ?? $match->stadium?->name ?? __('Venue to be confirmed');
        $cityName = \App\Support\PublicContent::field($match->city, 'name') ?? $match->city?->name ?? __('City to be confirmed');
        $statusTone = match ($match->status) {
            'live' => 'live',
            'completed' => 'completed',
            'postponed', 'cancelled' => 'muted',
            default => 'scheduled',
        };
    @endphp

    <div class="match-ref-page match-ref-page--detail entity-ref-page">
        <div class="match-ref-detail-nav">
            <a href="{{ route('matches.index') }}" class="entity-ref-back-link entity-ref-back-link--light">&larr; {{ __('Upcoming Fixtures') }}</a>
            <a href="{{ route('results.index') }}" class="entity-ref-back-link entity-ref-back-link--light">{{ __('Recent Results') }} &rarr;</a>
        </div>

        <section class="page-section match-centre-hero match-centre-hero--{{ $statusTone }} match-ref-scoreboard match-ref-scoreboard--detail" aria-labelledby="match-centre-title">
            <h1 id="match-centre-title" class="sr-only">{{ $homeTeamName }} {{ __('VS') }} {{ $awayTeamName }}</h1>

            <div class="match-centre-hero__topline">
                <span class="match-status-pill match-status-pill--{{ $statusTone }} match-ref-status">{{ $statusLabel }}</span>
                <span>{{ $stageLabel }}</span>
                @if ($match->group)
                    <span>{{ $match->group->name }}</span>
                @endif
                @if ($match->code)
                    <span>{{ $match->code }}</span>
                @endif
            </div>

            <div class="match-scoreboard">
                <article class="match-scoreboard__team match-ref-team">
                    @if ($homeTeam)
                        <span class="match-scoreboard__crest match-ref-flag match-ref-flag--hero" aria-hidden="true">
                            @include('public.partials.local-entity-media', [
                                'type' => 'team',
                                'model' => $homeTeam,
                                'slug' => $homeTeam->slug,
                                'code' => $homeTeam->code,
                                'name' => $homeTeamDisplay,
                                'media' => \App\Support\PublicMedia::primaryData($homeTeam, null, $homeTeamDisplay),
                                'context' => 'card',
                                'lazy' => false,
                            ])
                        </span>
                    @else
                        <span class="match-scoreboard__crest">{{ strtoupper(mb_substr($homeTeamName, 0, 3)) }}</span>
                    @endif
                    <div>
                        @if ($homeTeam)
                            <a href="{{ route('teams.show', $homeTeam->slug) }}"><strong>{{ $homeTeamName }}</strong></a>
                        @else
                            <strong>{{ $homeTeamName }}</strong>
                        @endif
                        <span>{{ __('Home Team') }}</span>
                    </div>
                </article>

                <div class="match-scoreboard__score match-ref-score">
                    @if ($match->hasScoreline())
                        <strong>{{ $match->home_score }} - {{ $match->away_score }}</strong>
                        @if ($match->home_penalty_score !== null && $match->away_penalty_score !== null)
                            <span>{{ __('Pens') }} {{ $match->home_penalty_score }} - {{ $match->away_penalty_score }}</span>
                        @else
                            <span>{{ $match->status === 'completed' ? __('Full Time') : $statusLabel }}</span>
                        @endif
                    @else
                        <strong>{{ __('VS') }}</strong>
                        <span>{{ $match->status === 'scheduled' ? __('Scheduled') : $statusLabel }}</span>
                    @endif
                </div>

                <article class="match-scoreboard__team match-scoreboard__team--away match-ref-team">
                    @if ($awayTeam)
                        <span class="match-scoreboard__crest match-ref-flag match-ref-flag--hero" aria-hidden="true">
                            @include('public.partials.local-entity-media', [
                                'type' => 'team',
                                'model' => $awayTeam,
                                'slug' => $awayTeam->slug,
                                'code' => $awayTeam->code,
                                'name' => $awayTeamDisplay,
                                'media' => \App\Support\PublicMedia::primaryData($awayTeam, null, $awayTeamDisplay),
                                'context' => 'card',
                                'lazy' => false,
                            ])
                        </span>
                    @else
                        <span class="match-scoreboard__crest">{{ strtoupper(mb_substr($awayTeamName, 0, 3)) }}</span>
                    @endif
                    <div>
                        @if ($awayTeam)
                            <a href="{{ route('teams.show', $awayTeam->slug) }}"><strong>{{ $awayTeamName }}</strong></a>
                        @else
                            <strong>{{ $awayTeamName }}</strong>
                        @endif
                        <span>{{ __('Away Team') }}</span>
                    </div>
                </article>
            </div>

            <div class="match-centre-meta match-ref-meta">
                <div>
                    <span>{{ __('Date & Time') }}</span>
                    <strong>{{ $matchDate }}</strong>
                </div>
                <div>
                    <span>{{ __('Venue') }}</span>
                    <strong>
                        @if ($match->stadium)
                            <a href="{{ route('stadiums.show', $match->stadium->slug) }}">{{ $venueName }}</a>
                        @else
                            {{ $venueName }}
                        @endif
                    </strong>
                </div>
                <div>
                    <span>{{ __('Location') }}</span>
                    <strong>
                        @if ($match->city)
                            <a href="{{ route('cities.show', $match->city->slug) }}">{{ $cityName }}</a>
                        @else
                            {{ $cityName }}
                        @endif
                    </strong>
                </div>
            </div>

            <div class="match-ref-filter entity-ref-hero__actions">
                @if ($match->group)
                    <a href="{{ route('standings.show', $match->group->code) }}" class="entity-ref-action entity-ref-action--primary">{{ __('View Standings') }}</a>
                @else
                    <a href="{{ route('standings.index') }}" class="entity-ref-action entity-ref-action--primary">{{ __('View Standings') }}</a>
                @endif
                <a href="{{ route('knockout.index') }}" class="entity-ref-action entity-ref-action--ghost">{{ __('View Bracket') }}</a>
            </div>

            @if (! $match->hasResolvedTeams() && $match->isKnockoutStage())
                <div class="match-centre-notice">
                    <strong>{{ __('This knockout fixture still has unresolved team slots.') }}</strong>
                    <span>{{ __('Teams will appear here as earlier knockout results decide the qualified sides.') }}</span>
                </div>
            @endif
        </section>

        <section class="match-ref-detail entity-ref-detail" aria-labelledby="match-facts-title">
            <h2 id="match-facts-title" class="entity-ref-section__title">{{ __('Match Facts') }}</h2>
            <div class="entity-ref-detail-grid match-ref-facts">
                <article class="entity-ref-stat entity-ref-stat--card">
                    <span>{{ __('Date') }}</span>
                    <strong>{{ $matchDate }}</strong>
                </article>
                <article class="entity-ref-stat entity-ref-stat--card">
                    <span>{{ __('Stage') }}</span>
                    <strong>{{ $stageLabel }}</strong>
                </article>
                <article class="entity-ref-stat entity-ref-stat--card">
                    <span>{{ __('Status') }}</span>
                    <strong>{{ $statusLabel }}</strong>
                </article>
                <article class="entity-ref-stat entity-ref-stat--card">
                    <span>{{ __('Venue') }}</span>
                    <strong>{{ $venueName }}</strong>
                </article>
            </div>
        </section>

        <section class="page-section match-centre-grid match-ref-section">
            <div class="section-stack">
                <section class="panel match-centre-panel entity-ref-section--panel">
                    <div class="section-header">
                        <div>
                            <span class="section-header__eyebrow">{{ __('Timeline') }}</span>
                            <h2>{{ __('Match Events') }}</h2>
                        </div>
                    </div>

                    @if ($match->events->isNotEmpty())
                        <div class="match-timeline">
                            @foreach ($match->events as $event)
                                @php($eventTypeLabel = \App\Support\TournamentFormatting::eventTypeLabel($event->event_type))
                                @php($eventTeamName = \App\Support\PublicContent::field($event->team, 'name') ?? $event->team?->name ?? __('Official'))
                                @php($eventPlayerName = \App\Support\PublicContent::field($event->player, 'display_name') ?? $event->player?->display_name)
                                @php($relatedPlayerName = \App\Support\PublicContent::field($event->relatedPlayer, 'display_name') ?? $event->relatedPlayer?->display_name)

                                <article class="match-timeline__item match-timeline__item--{{ $event->event_type }}">
                                    <div class="match-timeline__minute">
                                        <strong>{{ $event->minuteLabel() }}</strong>
                                        <span>'</span>
                                    </div>
                                    <div class="match-timeline__icon" aria-hidden="true">
                                        {{ strtoupper(mb_substr((string) $eventTypeLabel, 0, 1)) }}
                                    </div>
                                    <div class="match-timeline__body">
                                        <div class="match-timeline__head">
                                            <strong>{{ $eventTypeLabel }}</strong>
                                            <span>{{ $eventTeamName }}</span>
                                        </div>
                                        @if ($eventPlayerName || $relatedPlayerName)
                                            <p>
                                                @if ($eventPlayerName)
                                                    <span>{{ $eventPlayerName }}</span>
                                                @endif
                                                @if ($relatedPlayerName)
                                                    <span>{{ __('Related') }}: {{ $relatedPlayerName }}</span>
                                                @endif
                                            </p>
                                        @endif
                                        @if ($event->description)
                                            <p>{{ $event->description }}</p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="match-ref-empty entity-ref-empty">
                            @include('public.partials.empty-state', [
                                'title' => __('No event timeline is available yet.'),
                            ])
                        </div>
                    @endif
                </section>

                <section class="panel match-centre-panel entity-ref-section--panel">
                    <div class="section-header">
                        <div>
                            <span class="section-header__eyebrow">{{ __('Statistics') }}</span>
                            <h2>{{ __('Match Statistics') }}</h2>
                        </div>
                    </div>

                    @if ($match->statistics->isNotEmpty() && $match->hasResolvedTeams())
                        @php($homeStats = $statisticsByTeam->get($match->homeTeam?->id, collect())->groupBy('context'))
                        @php($awayStats = $statisticsByTeam->get($match->awayTeam?->id, collect())->groupBy('context'))
                        @php($contexts = $match->statistics->pluck('context')->unique()->values())

                        <div class="match-stat-comparison">
                            @foreach ($contexts as $context)
                                @php($contextHomeStats = $homeStats->get($context, collect())->keyBy('metric_key'))
                                @php($contextAwayStats = $awayStats->get($context, collect())->keyBy('metric_key'))
                                @php($metricKeys = $contextHomeStats->keys()->merge($contextAwayStats->keys())->unique()->values())

                                <section class="match-stat-context">
                                    <h3>{{ \App\Support\TournamentFormatting::matchContextLabel($context) }}</h3>
                                    <div class="match-stat-context__teams">
                                        <span>{{ $homeTeamName }}</span>
                                        <span>{{ $awayTeamName }}</span>
                                    </div>

                                    @foreach ($metricKeys as $metricKey)
                                        @php($homeStat = $contextHomeStats->get($metricKey))
                                        @php($awayStat = $contextAwayStats->get($metricKey))
                                        @php($homeNumeric = $homeStat?->metric_value !== null ? (float) $homeStat->metric_value : null)
                                        @php($awayNumeric = $awayStat?->metric_value !== null ? (float) $awayStat->metric_value : null)
                                        @php($total = max(1, ($homeNumeric ?? 0) + ($awayNumeric ?? 0)))
                                        @php($homeShare = $homeNumeric !== null || $awayNumeric !== null ? (($homeNumeric ?? 0) / $total) * 100 : 50)
                                        @php($awayShare = $homeNumeric !== null || $awayNumeric !== null ? (($awayNumeric ?? 0) / $total) * 100 : 50)

                                        <div class="match-stat-row">
                                            <div class="match-stat-row__values">
                                                <strong>{{ $homeStat?->formattedMetricValue() ?? '-' }}</strong>
                                                <span>{{ \App\Support\TournamentFormatting::statisticLabel($metricKey) }}</span>
                                                <strong>{{ $awayStat?->formattedMetricValue() ?? '-' }}</strong>
                                            </div>
                                            <div class="match-stat-row__bars" aria-hidden="true">
                                                <span style="--value: {{ $homeShare }}%;"></span>
                                                <span style="--value: {{ $awayShare }}%;"></span>
                                            </div>
                                        </div>
                                    @endforeach
                                </section>
                            @endforeach
                        </div>
                    @else
                        <div class="match-ref-empty entity-ref-empty">
                            @include('public.partials.empty-state', [
                                'title' => __('No official statistics are available yet.'),
                            ])
                        </div>
                    @endif
                </section>
            </div>

            <aside class="section-stack">
                <section class="panel match-centre-panel entity-ref-section--panel">
                    <div class="section-header">
                        <div>
                            <span class="section-header__eyebrow">{{ __('Lineups') }}</span>
                            <h2>{{ __('Team Sheets') }}</h2>
                        </div>
                    </div>

                    @if ($match->lineups->isNotEmpty() && $match->hasResolvedTeams())
                        <div class="match-lineup-board">
                            @foreach ([$match->homeTeam, $match->awayTeam] as $team)
                                @php($teamName = \App\Support\PublicContent::field($team, 'name') ?? $team?->name ?? __('TBD'))
                                @php($teamLineups = $lineupsByTeam->get($team?->id, collect()))
                                <article class="match-lineup-card">
                                    <h3>{{ $teamName }}</h3>

                                    @foreach (['starting' => __('Starting XI'), 'bench' => __('Bench')] as $lineupType => $label)
                                        @php($entries = $teamLineups->where('lineup_type', $lineupType))
                                        <div class="match-lineup-group">
                                            <div class="match-lineup-group__head">
                                                <strong>{{ $label }}</strong>
                                                <span>{{ $entries->count() }}</span>
                                            </div>

                                            @if ($entries->isNotEmpty())
                                                <ol class="match-lineup-list">
                                                    @foreach ($entries as $entry)
                                                        @php($playerName = \App\Support\PublicContent::field($entry->player, 'display_name') ?? $entry->player?->display_name ?? __('Player pending'))
                                                        <li>
                                                            <span class="match-lineup-list__number">
                                                                {{ $entry->shirt_number ? '#'.$entry->shirt_number : '-' }}
                                                            </span>
                                                            <span class="match-lineup-list__player">
                                                                <strong>{{ $playerName }}</strong>
                                                                <small>
                                                                    {{ $entry->position ?: __('Position') }}
                                                                    @if ($entry->is_captain)
                                                                        / C
                                                                    @endif
                                                                </small>
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            @else
                                                <div class="match-lineup-empty">
                                                    {{ __('No entries listed yet.') }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="match-ref-empty entity-ref-empty">
                            @include('public.partials.empty-state', [
                                'title' => __('No lineups are available yet.'),
                            ])
                        </div>
                    @endif
                </section>
            </aside>
        </section>
    </div>
@endsection
