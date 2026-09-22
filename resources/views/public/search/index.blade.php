@extends('public.layouts.app')

@section('title', __('Search').' | '.__('Morocco 2030'))
@section('meta_description', __('Search official news, fixtures, teams, players, host cities, stadiums, and partners for Morocco 2030.'))

@section('content')
    <div class="entity-ref-page entity-ref-page--search">
        <header class="entity-ref-hero">
            <div class="entity-ref-hero__content">
                <span class="entity-ref-hero__eyebrow">{{ __('Search') }}</span>
                <h1>{{ __('Global Search') }}</h1>
                <p>{{ __('Find teams, players, matches, cities, stadiums and news.') }}</p>
            </div>
        </header>

        <section class="entity-ref-section search-ref-shell" aria-labelledby="search-panel-title">
            <h2 id="search-panel-title" class="sr-only">{{ __('Search panel') }}</h2>

            <div class="entity-ref-section entity-ref-section--panel search-ref-form">
                <form action="{{ route('search.index') }}" method="GET" class="search-form search-ref-form__inner">
                    <div class="search-form__field">
                        <label for="search-query">{{ __('Search Morocco 2030') }}</label>
                        <input
                            id="search-query"
                            type="search"
                            name="q"
                            value="{{ $query }}"
                            placeholder="{{ __('Try Morocco, Casablanca, quarter-final, or a team name') }}"
                            aria-label="{{ __('Search Morocco 2030') }}"
                            autocomplete="off"
                        >
                    </div>
                    <button type="submit" class="entity-ref-action entity-ref-action--primary">{{ __('Search') }}</button>
                </form>
            </div>

            @if ($hasQuery)
                <div class="search-ref-summary">
                    <span class="search-summary__pill">
                        {{ trans_choice('{1}:count result|[2,*]:count results', $totalResults, ['count' => $totalResults]) }}
                    </span>
                    <span class="search-summary__pill">{{ __('Query') }}: "{{ $query }}"</span>
                </div>

                @if ($totalResults > 0)
                    <div class="search-ref-results">
                        @foreach ($resultGroups as $group)
                            @continue($group['items']->isEmpty())

                            <section class="entity-ref-section entity-ref-section--panel search-ref-group">
                                <div class="entity-ref-section__head">
                                    <div>
                                        <span class="entity-ref-section__eyebrow">{{ $group['label'] }}</span>
                                        <h2>{{ $group['label'] }}</h2>
                                        <p class="entity-ref-card__excerpt">{{ $group['description'] }}</p>
                                    </div>
                                    <a href="{{ $group['route'] }}" class="entity-ref-section__link">{{ __('Browse All') }}</a>
                                </div>

                                <div class="search-ref-results__list">
                                    @foreach ($group['items'] as $item)
                                        @php($title = match ($group['key']) {
                                            'news' => \App\Support\PublicContent::field($item, 'title') ?? $item->title,
                                            'matches' => $item->slotLabel('home').' '.__('VS').' '.$item->slotLabel('away'),
                                            'teams' => \App\Support\PublicContent::field($item, 'name') ?? $item->name,
                                            'players' => \App\Support\PublicContent::field($item, 'display_name') ?? $item->display_name,
                                            'cities' => \App\Support\PublicContent::field($item, 'name') ?? $item->name,
                                            'stadiums' => \App\Support\PublicContent::field($item, 'name') ?? $item->name,
                                            'partners' => \App\Support\PublicContent::field($item, 'name') ?? $item->name,
                                        })
                                        @php($url = match ($group['key']) {
                                            'news' => route('news.show', $item->slug),
                                            'matches' => route('matches.show', $item->slug),
                                            'teams' => route('teams.show', $item->slug),
                                            'players' => route('players.show', $item->slug),
                                            'cities' => route('cities.show', $item->slug),
                                            'stadiums' => route('stadiums.show', $item->slug),
                                            'partners' => route('partners.index'),
                                        })
                                        @php($summary = match ($group['key']) {
                                            'news' => \App\Support\PublicContent::field($item, 'summary') ?: \Illuminate\Support\Str::limit(strip_tags((string) (\App\Support\PublicContent::field($item, 'body') ?? $item->body)), 140),
                                            'matches' => \App\Support\TournamentFormatting::stageLabel($item->stage_type).' / '.(\App\Support\TournamentFormatting::matchDate($item->match_date, $item->timezone) ?? __('Date TBC')),
                                            'teams' => \App\Support\PublicContent::field($item, 'description') ?: ($item->group?->name ?? __('Group pending')),
                                            'players' => \App\Support\PublicContent::field($item, 'club') ?: (\App\Support\PublicContent::field($item->team, 'name') ?? $item->team?->name ?? __('Team pending')),
                                            'cities' => \App\Support\PublicContent::field($item, 'description') ?: ($item->region ?: __('Host city profile')),
                                            'stadiums' => \App\Support\PublicContent::field($item, 'description') ?: (\App\Support\PublicContent::field($item->city, 'name') ?? $item->city?->name ?? __('Venue profile')),
                                            'partners' => \App\Support\PublicContent::field($item, 'description') ?: trim(collect([$item->category, $item->tier])->filter()->implode(' / ')),
                                        })
                                        @php($metaParts = (match ($group['key']) {
                                            'news' => collect([\App\Support\PublicContent::field($item->category, 'name') ?? $item->category?->name, $item->published_at?->translatedFormat('M j, Y')]),
                                            'matches' => collect([$item->code, \App\Support\TournamentFormatting::statusLabel($item->status)]),
                                            'teams' => collect([$item->code, $item->coach_name]),
                                            'players' => collect([\App\Support\TournamentFormatting::playerPositionLabel($item->position), \App\Support\PublicContent::field($item->team, 'name') ?? $item->team?->name]),
                                            'cities' => collect([$item->code, trans_choice('{1}:count stadium|[2,*]:count stadiums', $item->stadiums_count ?? 0, ['count' => $item->stadiums_count ?? 0])]),
                                            'stadiums' => collect([$item->code, \App\Support\PublicContent::field($item->city, 'name') ?? $item->city?->name]),
                                            'partners' => collect([$item->category, $item->tier]),
                                        })->filter())
                                        @php($showSearchFlag = in_array($group['key'], ['teams', 'players', 'matches'], true))

                                        <article class="entity-ref-card entity-ref-card--search-result">
                                            @if ($showSearchFlag)
                                                <div class="search-ref-result__media" aria-hidden="true">
                                                    @if ($group['key'] === 'teams')
                                                        @include('public.partials.local-entity-media', [
                                                            'type' => 'team',
                                                            'slug' => $item->slug,
                                                            'code' => $item->code,
                                                            'name' => $title,
                                                            'media' => \App\Support\PublicMedia::primaryData($item, null, $title),
                                                            'context' => 'table',
                                                            'flagSize' => 'small',
                                                        ])
                                                    @elseif ($group['key'] === 'players')
                                                        @include('public.partials.local-entity-media', [
                                                            'type' => 'player',
                                                            'slug' => $item->slug,
                                                            'code' => $item->nationality_code,
                                                            'name' => $title,
                                                            'media' => \App\Support\PublicMedia::primaryData($item, null, $title),
                                                            'context' => 'table',
                                                            'flagSize' => 'small',
                                                        ])
                                                    @elseif ($group['key'] === 'matches')
                                                        <div class="search-ref-match-flags">
                                                            @if ($item->homeTeam)
                                                                @php($searchHomeName = \App\Support\PublicContent::field($item->homeTeam, 'name') ?? $item->homeTeam->name)
                                                                <span class="match-ref-flag flag-ref-wrap flag-ref-wrap--small">
                                                                    @include('public.partials.local-entity-media', [
                                                                        'type' => 'team',
                                                                        'slug' => $item->homeTeam->slug,
                                                                        'code' => $item->homeTeam->code,
                                                                        'name' => $searchHomeName,
                                                                        'media' => \App\Support\PublicMedia::primaryData($item->homeTeam, null, $searchHomeName),
                                                                        'context' => 'table',
                                                                        'flagSize' => 'small',
                                                                    ])
                                                                </span>
                                                            @endif
                                                            @if ($item->awayTeam)
                                                                @php($searchAwayName = \App\Support\PublicContent::field($item->awayTeam, 'name') ?? $item->awayTeam->name)
                                                                <span class="match-ref-flag flag-ref-wrap flag-ref-wrap--small">
                                                                    @include('public.partials.local-entity-media', [
                                                                        'type' => 'team',
                                                                        'slug' => $item->awayTeam->slug,
                                                                        'code' => $item->awayTeam->code,
                                                                        'name' => $searchAwayName,
                                                                        'media' => \App\Support\PublicMedia::primaryData($item->awayTeam, null, $searchAwayName),
                                                                        'context' => 'table',
                                                                        'flagSize' => 'small',
                                                                    ])
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="entity-ref-card__body">
                                                <div class="badge-row">
                                                    <span class="meta-pill">{{ $group['label'] }}</span>
                                                    @foreach ($metaParts as $meta)
                                                        <span class="meta-pill">{{ $meta }}</span>
                                                    @endforeach
                                                </div>
                                                <h3><a href="{{ $url }}">{{ $title }}</a></h3>
                                                @if (filled($summary))
                                                    <p class="entity-ref-card__excerpt">{{ \Illuminate\Support\Str::limit($summary, 160) }}</p>
                                                @endif
                                                <a href="{{ $url }}" class="entity-ref-card__cta">{{ __('View Details') }}</a>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>
                @else
                    <div class="entity-ref-empty">
                        @include('public.partials.empty-state', [
                            'title' => __('No results matched your search.'),
                            'message' => __('Try a team name, city, stadium, match code, or news headline.'),
                        ])
                    </div>
                @endif
            @else
                <div class="entity-ref-grid entity-ref-grid--search-scope">
                    @foreach ($resultGroups as $group)
                        <article class="entity-ref-card entity-ref-card--search-scope">
                            <div class="entity-ref-card__body">
                                <span class="entity-ref-section__eyebrow">{{ $group['label'] }}</span>
                                <h3>{{ $group['label'] }}</h3>
                                <p class="entity-ref-card__excerpt">{{ $group['description'] }}</p>
                                <a href="{{ $group['route'] }}" class="entity-ref-card__cta">{{ __('Browse All') }}</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
