@php($teamHeader = __('Team'))
@php($playedHeader = __('P'))
@php($wonHeader = __('W'))
@php($drawnHeader = __('D'))
@php($lostHeader = __('L'))
@php($goalsForHeader = __('GF'))
@php($goalsAgainstHeader = __('GA'))
@php($goalDifferenceHeader = __('GD'))
@php($pointsHeader = __('Pts'))

@php($compactFlags = $useCompactFlags ?? false)

<div @class(['table-shell', 'standings-table', 'standings-ref-table', 'standings-ref-table--compact-flags' => $compactFlags])>
    <div class="standings-ref-table__scroll">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ $teamHeader }}</th>
                <th>{{ $playedHeader }}</th>
                <th>{{ $wonHeader }}</th>
                <th>{{ $drawnHeader }}</th>
                <th>{{ $lostHeader }}</th>
                <th>{{ $goalsForHeader }}</th>
                <th>{{ $goalsAgainstHeader }}</th>
                <th>{{ $goalDifferenceHeader }}</th>
                <th>{{ $pointsHeader }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($standings as $row)
                <tr @class(['standings-ref-table__row--qualified' => $row->position <= 2])>
                    <td><span class="rank-pill">{{ $row->position }}</span></td>
                    <td class="standings-table__team">
                        @if ($row->team)
                            @php($teamName = \App\Support\PublicContent::field($row->team, 'name') ?? $row->team->name)
                            <span class="standings-ref-team">
                                <span class="standings-ref-flag" aria-hidden="true">
                                    @include('public.partials.local-entity-media', [
                                        'type' => 'team',
                                        'model' => $row->team,
                                        'slug' => $row->team->slug,
                                        'code' => $row->team->code,
                                        'name' => $teamName,
                                        'media' => $compactFlags
                                            ? ['url' => null, 'alt' => $teamName]
                                            : \App\Support\PublicMedia::primaryData($row->team, null, $teamName),
                                        'context' => 'table',
                                        'flagSize' => 'small',
                                    ])
                                </span>
                                <a href="{{ route('teams.show', $row->team->slug) }}">{{ $teamName }}</a>
                            </span>
                        @else
                            {{ __('Unknown Team') }}
                        @endif
                    </td>
                    <td>{{ $row->played }}</td>
                    <td>{{ $row->won }}</td>
                    <td>{{ $row->drawn }}</td>
                    <td>{{ $row->lost }}</td>
                    <td>{{ $row->goals_for }}</td>
                    <td>{{ $row->goals_against }}</td>
                    <td>{{ $row->goal_difference }}</td>
                    <td class="standings-table__points"><strong>{{ $row->points }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">
                        @include('public.partials.empty-state', [
                            'title' => __('No standings available yet.'),
                            'message' => __('Standings will appear after completed group-stage matches are processed.'),
                        ])
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
