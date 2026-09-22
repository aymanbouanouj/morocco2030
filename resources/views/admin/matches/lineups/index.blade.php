@extends('admin.layouts.app')

@php
    $pageTitle = 'Match Lineups';
    $pageDescription = 'Manage squad selections for '.$match->slotLabel('home').' vs '.$match->slotLabel('away').'.';
@endphp

@section('content')
    <section class="panel">
        <div class="toolbar">
            <div class="meta">
                Fixture: <strong>{{ $match->slotLabel('home') }} vs {{ $match->slotLabel('away') }}</strong>
            </div>

            <div class="panel-actions">
                <a class="btn btn-secondary" href="{{ route('admin.matches.show', $match) }}">Back to Match</a>
                <a class="btn btn-primary" href="{{ route('admin.matches.lineups.create', $match) }}">Add Lineup Entry</a>
            </div>
        </div>

        <div class="split-grid">
            @foreach ($teams as $team)
                @php($teamLineups = $lineups->get($team->id, collect()))
                <section class="subtle-card">
                    <h3>{{ $team->name }}</h3>
                    <p class="meta">
                        Starting: {{ $teamLineups->where('lineup_type', 'starting')->count() }}
                        |
                        Bench: {{ $teamLineups->where('lineup_type', 'bench')->count() }}
                    </p>

                    @if ($teamLineups->isEmpty())
                        <p class="meta">No lineup entries recorded yet.</p>
                    @else
                        <div class="table-wrap" style="margin-top: 12px;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Player</th>
                                        <th>Position</th>
                                        <th>Order</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($teamLineups as $lineup)
                                        <tr>
                                            <td>{{ ucfirst($lineup->lineup_type) }}</td>
                                            <td>
                                                {{ $lineup->player?->display_name ?: 'Unknown player' }}<br>
                                                <span class="meta">
                                                    @if ($lineup->shirt_number)
                                                        #{{ $lineup->shirt_number }}
                                                    @endif
                                                    @if ($lineup->is_captain)
                                                        | Captain
                                                    @endif
                                                    @if ($lineup->is_goalkeeper)
                                                        | Goalkeeper
                                                    @endif
                                                </span>
                                            </td>
                                            <td>{{ $lineup->position_label ?: 'N/A' }}</td>
                                            <td>{{ $lineup->sort_order }}</td>
                                            <td class="table-actions">
                                                <a class="btn-link" href="{{ route('admin.matches.lineups.edit', [$match, $lineup]) }}">Edit</a>
                                                <form method="POST" action="{{ route('admin.matches.lineups.destroy', [$match, $lineup]) }}" onsubmit="return confirm('Delete this lineup entry?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn-link" type="submit">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            @endforeach
        </div>
    </section>
@endsection
