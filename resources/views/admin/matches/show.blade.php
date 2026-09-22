@extends('admin.layouts.app')

@php
    $pageTitle = 'Match Operations';
    $pageDescription = 'Operational center for fixture setup, match timeline, statistics, lineups, standings, and progression.';
    $statisticsByTeam = $match->statistics->groupBy('team_id');
    $lineupsByTeam = $match->lineups->groupBy('team_id');
    $groupStandings = $match->group?->standings?->sortBy('position') ?? collect();
@endphp

@section('content')
    <div class="page-stack">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="section-title">{{ $match->slotLabel('home') }} vs {{ $match->slotLabel('away') }}</h2>
                    <p class="meta">Fixture code {{ $match->code }} with operational controls for sports staff.</p>
                </div>
                <div class="panel-actions">
                    <a class="btn btn-secondary" href="{{ route('admin.matches.index') }}">Back to Matches</a>
                    <a class="btn btn-primary" href="{{ route('admin.matches.edit', $match) }}">Edit Match</a>
                </div>
            </div>

            <div class="summary-grid">
                <div class="subtle-card">
                    <h3>Fixture Summary</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <strong>Stage</strong>
                            <span>{{ ucwords(str_replace('_', ' ', $match->stage_type)) }}</span>
                        </div>
                        <div class="detail-item">
                            <strong>Status</strong>
                            <span>{{ ucwords(str_replace('_', ' ', $match->status)) }}</span>
                        </div>
                        <div class="detail-item">
                            <strong>Date</strong>
                            <span>{{ $match->match_date?->format('Y-m-d H:i') ?: 'TBD' }}</span>
                        </div>
                        <div class="detail-item">
                            <strong>Timezone</strong>
                            <span>{{ $match->timezone ?: 'Default' }}</span>
                        </div>
                        <div class="detail-item">
                            <strong>Venue</strong>
                            <span>{{ $match->stadium?->name ?: 'TBD' }}</span>
                        </div>
                        <div class="detail-item">
                            <strong>City</strong>
                            <span>{{ $match->city?->name ?: 'TBD' }}</span>
                        </div>
                        <div class="detail-item">
                            <strong>Group</strong>
                            <span>{{ $match->group?->name ?: 'Not assigned' }}</span>
                        </div>
                        <div class="detail-item">
                            <strong>Attendance</strong>
                            <span>{{ $match->attendance ? number_format($match->attendance) : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="subtle-card">
                    <h3>Scoreboard</h3>
                    @if (! $match->hasResolvedTeams())
                        <div class="entity-note" style="margin-bottom: 12px;">
                            <strong>Unresolved Fixture</strong><br>
                            <span class="meta">One or both team slots are still pending. Progression or manual assignment can populate them later.</span>
                        </div>
                    @endif
                    <p style="margin: 0; font-size: 32px; font-weight: 700;">
                        {{ $match->home_score ?? '-' }} - {{ $match->away_score ?? '-' }}
                    </p>
                    <p class="meta" style="margin-top: 8px;">
                        Penalties:
                        {{ $match->home_penalty_score ?? '-' }} - {{ $match->away_penalty_score ?? '-' }}
                    </p>
                    <div class="compact-list" style="margin-top: 14px;">
                        <div class="entity-note">Events logged: {{ $match->events->count() }}</div>
                        <div class="entity-note">Statistics rows: {{ $match->statistics->count() }}</div>
                        <div class="entity-note">Lineup entries: {{ $match->lineups->count() }}</div>
                    </div>
                </div>

                <div class="subtle-card">
                    <h3>Operations</h3>
                    <div class="panel-actions">
                        <a class="btn btn-secondary" href="{{ route('admin.matches.events.index', $match) }}">Manage Events</a>
                        <a class="btn btn-secondary" href="{{ route('admin.matches.statistics.index', $match) }}">Manage Statistics</a>
                        <a class="btn btn-secondary" href="{{ route('admin.matches.lineups.index', $match) }}">Manage Lineups</a>
                    </div>

                    @if (! $match->hasResolvedTeams())
                        <div class="entity-note" style="margin-top: 12px;">
                            <strong>Team Assignment Pending</strong><br>
                            <span class="meta">Events, statistics, and lineups become operational once both teams are resolved.</span>
                        </div>
                    @endif

                    @if ($match->isGroupStage())
                        <div class="entity-note" style="margin-top: 12px;">
                            <strong>Standings</strong><br>
                            <span class="meta">Use manual recalculation if group results were adjusted outside the standard fixture flow.</span>
                            @can('recalculate', \App\Models\Standing::class)
                                <form method="POST" action="{{ route('admin.matches.recalculate-standings', $match) }}" style="margin-top: 10px;">
                                    @csrf
                                    <button class="btn btn-primary" type="submit">Recalculate Standings</button>
                                </form>
                            @endcan
                        </div>
                    @endif

                    @if ($match->isKnockoutStage())
                        <div class="entity-note" style="margin-top: 12px;">
                            <strong>Knockout Progression</strong><br>
                            <span class="meta">Propagate the winner or configured loser to the next fixture slots.</span>
                            <form method="POST" action="{{ route('admin.matches.propagate-knockout', $match) }}" style="margin-top: 10px;">
                                @csrf
                                <button class="btn btn-primary" type="submit">Propagate Knockout Slots</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="section-title">Match Events</h2>
                    <p class="meta">Key timeline entries recorded for the fixture.</p>
                </div>
                <div class="panel-actions">
                    <a class="btn btn-secondary" href="{{ route('admin.matches.events.index', $match) }}">Open Events</a>
                    @if ($match->hasResolvedTeams())
                        <a class="btn btn-primary" href="{{ route('admin.matches.events.create', $match) }}">Add Event</a>
                    @endif
                </div>
            </div>

            @if ($match->events->isEmpty())
                <p class="meta">No match events have been recorded yet.</p>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Minute</th>
                                <th>Type</th>
                                <th>Team</th>
                                <th>Player</th>
                                <th>Related Player</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($match->events as $event)
                                <tr>
                                    <td>{{ $event->minuteLabel() }}</td>
                                    <td>{{ ucwords(str_replace('_', ' ', $event->event_type)) }}</td>
                                    <td>{{ $event->team?->name ?: 'N/A' }}</td>
                                    <td>{{ $event->player?->display_name ?: 'N/A' }}</td>
                                    <td>{{ $event->relatedPlayer?->display_name ?: 'N/A' }}</td>
                                    <td>{{ $event->description ?: 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <div class="split-grid">
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2 class="section-title">Match Statistics</h2>
                        <p class="meta">Team-specific full-time or period-based metrics.</p>
                    </div>
                    <div class="panel-actions">
                        <a class="btn btn-secondary" href="{{ route('admin.matches.statistics.index', $match) }}">Open Statistics</a>
                        @if ($match->hasResolvedTeams())
                            <a class="btn btn-primary" href="{{ route('admin.matches.statistics.create', $match) }}">Add Statistic</a>
                        @endif
                    </div>
                </div>

                <div class="split-grid">
                    @foreach ([$match->homeTeam, $match->awayTeam] as $team)
                        <div class="subtle-card">
                            <h3>{{ $team?->name ?: 'Team' }}</h3>
                            @php
                                $teamStatistics = $statisticsByTeam->get($team?->id, collect());
                            @endphp
                            @if ($teamStatistics->isEmpty())
                                <p class="meta">No statistics recorded.</p>
                            @else
                                <div class="table-wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Metric</th>
                                                <th>Value</th>
                                                <th>Context</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($teamStatistics as $statistic)
                                                <tr>
                                                    <td>{{ ucwords(str_replace('_', ' ', $statistic->metric_key)) }}</td>
                                                    <td>{{ $statistic->formattedMetricValue() }}</td>
                                                    <td>{{ ucwords(str_replace('_', ' ', $statistic->context)) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2 class="section-title">Lineups</h2>
                        <p class="meta">Starting and bench selections for both teams.</p>
                    </div>
                    <div class="panel-actions">
                        <a class="btn btn-secondary" href="{{ route('admin.matches.lineups.index', $match) }}">Open Lineups</a>
                        @if ($match->hasResolvedTeams())
                            <a class="btn btn-primary" href="{{ route('admin.matches.lineups.create', $match) }}">Add Lineup Entry</a>
                        @endif
                    </div>
                </div>

                <div class="split-grid">
                    @foreach ([$match->homeTeam, $match->awayTeam] as $team)
                        <div class="subtle-card">
                            <h3>{{ $team?->name ?: 'Team' }}</h3>
                            @php
                                $teamLineups = $lineupsByTeam->get($team?->id, collect());
                            @endphp
                            <p class="meta">
                                Starting: {{ $teamLineups->where('lineup_type', 'starting')->count() }}
                                |
                                Bench: {{ $teamLineups->where('lineup_type', 'bench')->count() }}
                            </p>

                            @if ($teamLineups->isEmpty())
                                <p class="meta">No lineup entries recorded.</p>
                            @else
                                <div class="compact-list" style="margin-top: 12px;">
                                    @foreach ($teamLineups->take(12) as $lineup)
                                        <div class="entity-note">
                                            <strong>{{ $lineup->player?->display_name ?: 'Unknown Player' }}</strong><br>
                                            <span class="meta">
                                                {{ ucfirst($lineup->lineup_type) }}
                                                @if ($lineup->shirt_number)
                                                    | #{{ $lineup->shirt_number }}
                                                @endif
                                                @if ($lineup->position)
                                                    | {{ $lineup->position }}
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        @if ($match->isGroupStage())
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2 class="section-title">Group Standings</h2>
                        <p class="meta">Current ranking for {{ $match->group?->name ?: 'the assigned group' }}.</p>
                    </div>
                    @can('recalculate', \App\Models\Standing::class)
                        <form method="POST" action="{{ route('admin.matches.recalculate-standings', $match) }}">
                            @csrf
                            <button class="btn btn-primary" type="submit">Recalculate Now</button>
                        </form>
                    @endcan
                </div>

                @if ($groupStandings->isEmpty())
                    <p class="meta">No standings rows exist yet for this group.</p>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Team</th>
                                    <th>P</th>
                                    <th>W</th>
                                    <th>D</th>
                                    <th>L</th>
                                    <th>GF</th>
                                    <th>GA</th>
                                    <th>GD</th>
                                    <th>Pts</th>
                                    <th>Form</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupStandings as $standing)
                                    <tr>
                                        <td>{{ $standing->position }}</td>
                                        <td>{{ $standing->team?->name ?: 'Team '.$standing->team_id }}</td>
                                        <td>{{ $standing->played }}</td>
                                        <td>{{ $standing->won }}</td>
                                        <td>{{ $standing->drawn }}</td>
                                        <td>{{ $standing->lost }}</td>
                                        <td>{{ $standing->goals_for }}</td>
                                        <td>{{ $standing->goals_against }}</td>
                                        <td>{{ $standing->goal_difference }}</td>
                                        <td><strong>{{ $standing->points }}</strong></td>
                                        <td>{{ $standing->form ?: 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        @endif

        @if ($match->isKnockoutStage())
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2 class="section-title">Knockout Progression</h2>
                        <p class="meta">Configured incoming and outgoing slot mappings for knockout progression.</p>
                    </div>
                    <form method="POST" action="{{ route('admin.matches.propagate-knockout', $match) }}">
                        @csrf
                        <button class="btn btn-primary" type="submit">Run Progression</button>
                    </form>
                </div>

                <div class="split-grid">
                    <div class="subtle-card">
                        <h3>Outgoing Progressions</h3>
                        @if ($match->sourceProgressions->isEmpty())
                            <p class="meta">No target progression records are configured for this fixture.</p>
                        @else
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Target Match</th>
                                            <th>Slot</th>
                                            <th>Current Team</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($match->sourceProgressions as $progression)
                                            @php
                                                $targetMatch = $progression->targetMatch;
                                                $slotLabel = $targetMatch?->slotLabel($progression->team_slot);
                                            @endphp
                                            <tr>
                                                <td>{{ ucfirst($progression->progression_type) }}</td>
                                                <td>
                                                    {{ $targetMatch?->code ?: 'Unknown match' }}<br>
                                                    <span class="meta">
                                                        {{ $targetMatch?->homeTeam?->name ?: 'TBD' }}
                                                        vs
                                                        {{ $targetMatch?->awayTeam?->name ?: 'TBD' }}
                                                    </span>
                                                </td>
                                                <td>{{ ucfirst($progression->team_slot) }}</td>
                                                <td>{{ $slotLabel ?: 'Unassigned' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="subtle-card">
                        <h3>Incoming Progressions</h3>
                        @if ($match->targetProgressions->isEmpty() && $match->targetQualificationRules->isEmpty())
                            <p class="meta">No prior knockout matches feed into this fixture yet.</p>
                        @else
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Source</th>
                                            <th>Type</th>
                                            <th>Slot</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($match->targetQualificationRules as $rule)
                                            <tr>
                                                <td>
                                                    {{ $rule->publicLabel() }}<br>
                                                    <span class="meta">
                                                        {{ $rule->appliedTeam?->name ?: 'Pending standings resolution' }}
                                                    </span>
                                                </td>
                                                <td>Group standing</td>
                                                <td>{{ ucfirst($rule->team_slot) }}</td>
                                            </tr>
                                        @endforeach
                                        @foreach ($match->targetProgressions as $progression)
                                            <tr>
                                                <td>
                                                    {{ $progression->sourceMatch?->code ?: 'Unknown match' }}<br>
                                                    <span class="meta">
                                                        {{ $progression->sourceMatch?->homeTeam?->name ?: 'TBD' }}
                                                        vs
                                                        {{ $progression->sourceMatch?->awayTeam?->name ?: 'TBD' }}
                                                    </span>
                                                </td>
                                                <td>{{ ucfirst($progression->progression_type) }}</td>
                                                <td>{{ ucfirst($progression->team_slot) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection
