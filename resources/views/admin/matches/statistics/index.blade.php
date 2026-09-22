@extends('admin.layouts.app')

@php
    $pageTitle = 'Match Statistics';
    $pageDescription = 'Manage team metrics for '.$match->slotLabel('home').' vs '.$match->slotLabel('away').'.';
@endphp

@section('content')
    <section class="panel">
        <div class="toolbar">
            <div class="meta">
                Fixture: <strong>{{ $match->slotLabel('home') }} vs {{ $match->slotLabel('away') }}</strong>
            </div>

            <div class="panel-actions">
                <a class="btn btn-secondary" href="{{ route('admin.matches.show', $match) }}">Back to Match</a>
                <a class="btn btn-primary" href="{{ route('admin.matches.statistics.create', $match) }}">Add Statistic</a>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Team</th>
                        <th>Metric</th>
                        <th>Value</th>
                        <th>Context</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($statistics as $statistic)
                        <tr>
                            <td>{{ $statistic->team?->name ?: 'N/A' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $statistic->metric_key)) }}</td>
                            <td>{{ $statistic->formattedMetricValue() }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $statistic->context)) }}</td>
                            <td class="table-actions">
                                <a class="btn-link" href="{{ route('admin.matches.statistics.edit', [$match, $statistic]) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.matches.statistics.destroy', [$match, $statistic]) }}" onsubmit="return confirm('Delete this match statistic?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-link" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="meta">No statistics recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $statistics->links() }}
    </section>
@endsection
