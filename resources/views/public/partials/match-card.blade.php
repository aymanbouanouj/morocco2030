@php
    $statusClass = 'status-pill status-pill--'.($match->status ?? 'scheduled');
    $stadiumName = \App\Support\PublicContent::field($match->stadium, 'name') ?? $match->stadium?->name;
    $cityName = \App\Support\PublicContent::field($match->city, 'name') ?? $match->city?->name;
    $homeLabel = $match->slotLabel('home');
    $awayLabel = $match->slotLabel('away');
    $matchAria = $homeLabel.' '.__('versus').' '.$awayLabel;
    $useRefStyle = $refStyle ?? false;
    $cardContext = $context ?? 'default';
    $homeTeam = $match->homeTeam;
    $awayTeam = $match->awayTeam;
    $homeTeamName = $homeTeam ? (\App\Support\PublicContent::field($homeTeam, 'name') ?? $homeTeam->name) : $homeLabel;
    $awayTeamName = $awayTeam ? (\App\Support\PublicContent::field($awayTeam, 'name') ?? $awayTeam->name) : $awayLabel;
    $homeWon = $match->status === 'completed' && $match->hasScoreline() && $match->home_score > $match->away_score;
    $awayWon = $match->status === 'completed' && $match->hasScoreline() && $match->away_score > $match->home_score;
    $isDraw = $match->status === 'completed' && $match->hasScoreline() && $match->home_score === $match->away_score;
    $ctaLabel = match ($cardContext) {
        'results' => __('Match Details'),
        'fixtures' => __('View Match'),
        default => __('Match Centre'),
    };
@endphp

<article
    @class([
        'match-card',
        'match-ref-card' => $useRefStyle,
        'match-ref-card--results' => $useRefStyle && $cardContext === 'results',
        'match-ref-card--fixtures' => $useRefStyle && $cardContext === 'fixtures',
    ])
    aria-label="{{ $matchAria }}"
>
    <div class="match-card__head match-ref-meta">
        <div class="match-card__competition">
            <span class="{{ $statusClass }} match-ref-status">{{ \App\Support\TournamentFormatting::statusLabel($match->status) }}</span>
            <span class="badge">{{ \App\Support\TournamentFormatting::stageLabel($match->stage_type) }}</span>
            @if ($match->group)
                <span class="badge">{{ $match->group->name }}</span>
            @endif
        </div>

        <div class="match-card__date">
            {{ \App\Support\TournamentFormatting::matchDate($match->match_date, $match->timezone) ?? __('Date TBC') }}
        </div>
    </div>

    <div class="match-card__teams match-ref-scoreboard">
        <div @class(['match-card__team', 'match-ref-team', 'match-ref-team--winner' => $useRefStyle && $homeWon, 'match-ref-team--draw' => $useRefStyle && $isDraw])>
            @if ($homeTeam)
                <span class="match-ref-flag" aria-hidden="true">
                    @include('public.partials.local-entity-media', [
                        'type' => 'team',
                        'model' => $homeTeam,
                        'slug' => $homeTeam->slug,
                        'code' => $homeTeam->code,
                        'name' => $homeTeamName,
                        'media' => \App\Support\PublicMedia::primaryData($homeTeam, null, $homeTeamName),
                        'context' => 'card',
                        'flagSize' => 'card',
                    ])
                </span>
            @endif
            <span class="match-card__label">{{ __('Home Team') }}</span>
            <strong>{{ $homeLabel }}</strong>
        </div>

        <div class="score-box match-ref-score" aria-hidden="false">
            @if ($match->status === 'completed' && $match->hasScoreline())
                {{ $match->home_score }} - {{ $match->away_score }}
                @if ($match->home_penalty_score !== null && $match->away_penalty_score !== null)
                    <div class="score-box__sub">
                        {{ __('Pens') }} {{ $match->home_penalty_score }} - {{ $match->away_penalty_score }}
                    </div>
                @endif
            @elseif ($match->status === 'live' && $match->hasScoreline())
                {{ $match->home_score }} - {{ $match->away_score }}
            @else
                {{ __('VS') }}
            @endif
        </div>

        <div @class(['match-card__team', 'match-card__team--away', 'match-ref-team', 'match-ref-team--winner' => $useRefStyle && $awayWon, 'match-ref-team--draw' => $useRefStyle && $isDraw])>
            @if ($awayTeam)
                <span class="match-ref-flag" aria-hidden="true">
                    @include('public.partials.local-entity-media', [
                        'type' => 'team',
                        'model' => $awayTeam,
                        'slug' => $awayTeam->slug,
                        'code' => $awayTeam->code,
                        'name' => $awayTeamName,
                        'media' => \App\Support\PublicMedia::primaryData($awayTeam, null, $awayTeamName),
                        'context' => 'card',
                        'flagSize' => 'card',
                    ])
                </span>
            @endif
            <span class="match-card__label">{{ __('Away Team') }}</span>
            <strong>{{ $awayLabel }}</strong>
        </div>
    </div>

    <div class="match-card__meta match-ref-meta">
        @if ($stadiumName)
            <span class="meta-pill meta-pill--soft">{{ $stadiumName }}</span>
        @endif

        @if ($cityName)
            <span class="meta-pill meta-pill--soft">{{ $cityName }}</span>
        @endif

        <a href="{{ route('matches.show', $match->slug) }}" class="match-card__cta" aria-label="{{ __('Open match centre for') }} {{ $matchAria }}">
            {{ $ctaLabel }}
        </a>
    </div>
</article>
