@php
    $homeLabel = $match->slotLabel('home');
    $awayLabel = $match->slotLabel('away');
    $homeResolved = (bool) $match->homeTeam;
    $awayResolved = (bool) $match->awayTeam;
    $hasScore = $match->hasScoreline() && in_array($match->status, ['live', 'completed'], true);
    $formattedMatchDate = \App\Support\TournamentFormatting::matchDate($match->match_date, $match->timezone, 'M j, H:i') ?? __('Date TBC');
    $variant = $variant ?? 'standard';
    $cardClasses = collect([
        'knockout-match-card',
        'knockout-match-card--'.$variant,
        $match->hasResolvedTeams() ? 'is-resolved' : 'is-unresolved',
        $variant === 'final' || $variant === 'third' ? 'is-terminal' : null,
    ])->filter()->implode(' ');
@endphp

<article class="{{ $cardClasses }}">
    <span class="knockout-match-card__node" aria-hidden="true"></span>

    @if ($variant === 'final')
        <div class="knockout-match-card__kicker">{{ config('app.name', 'MOROCCO 2030') }} {{ __('Final') }}</div>
    @elseif ($variant === 'third')
        <div class="knockout-match-card__kicker">{{ __('Third Place Play-off') }}</div>
    @endif

    <div class="knockout-match-card__top">
        <span class="knockout-match-card__code">{{ $match->code }}</span>
        <span class="knockout-match-card__date">{{ $formattedMatchDate }}</span>
        <span class="knockout-match-card__status knockout-match-card__status--{{ $match->status }}">
            {{ \App\Support\TournamentFormatting::statusLabel($match->status) }}
        </span>
    </div>

    <div class="knockout-match-card__body">
        <div class="knockout-team-row {{ $homeResolved ? 'is-resolved' : 'is-pending' }}">
            @if ($match->homeTeam)
                @php($homeTeamName = \App\Support\PublicContent::field($match->homeTeam, 'name') ?? $match->homeTeam->name)
                <span class="knockout-ref-flag" aria-hidden="true">
                    @include('public.partials.local-entity-media', [
                        'type' => 'team',
                        'model' => $match->homeTeam,
                        'slug' => $match->homeTeam->slug,
                        'code' => $match->homeTeam->code,
                        'name' => $homeTeamName,
                        'media' => \App\Support\PublicMedia::primaryData($match->homeTeam, null, $homeTeamName),
                        'context' => 'table',
                        'flagSize' => 'small',
                    ])
                </span>
            @endif
            <span class="knockout-team-row__name">{{ $homeLabel }}</span>
            <span class="knockout-team-row__score">
                @if ($hasScore)
                    {{ $match->home_score }}
                @else
                    &mdash;
                @endif
            </span>
        </div>

        <div class="knockout-team-row {{ $awayResolved ? 'is-resolved' : 'is-pending' }}">
            @if ($match->awayTeam)
                @php($awayTeamName = \App\Support\PublicContent::field($match->awayTeam, 'name') ?? $match->awayTeam->name)
                <span class="knockout-ref-flag" aria-hidden="true">
                    @include('public.partials.local-entity-media', [
                        'type' => 'team',
                        'model' => $match->awayTeam,
                        'slug' => $match->awayTeam->slug,
                        'code' => $match->awayTeam->code,
                        'name' => $awayTeamName,
                        'media' => \App\Support\PublicMedia::primaryData($match->awayTeam, null, $awayTeamName),
                        'context' => 'table',
                        'flagSize' => 'small',
                    ])
                </span>
            @endif
            <span class="knockout-team-row__name">{{ $awayLabel }}</span>
            <span class="knockout-team-row__score">
                @if ($hasScore)
                    {{ $match->away_score }}
                @else
                    &mdash;
                @endif
            </span>
        </div>

        @if ($match->home_penalty_score !== null && $match->away_penalty_score !== null)
            <div class="knockout-match-card__penalties">
                {{ __('Penalties') }} {{ $match->home_penalty_score }} - {{ $match->away_penalty_score }}
            </div>
        @endif
    </div>

    <div class="knockout-match-card__meta">
        <span>{{ $formattedMatchDate }}</span>
        <a href="{{ route('matches.show', $match->slug) }}">{{ __('Match Centre') }}</a>
    </div>
</article>
