@extends('admin.layouts.app')

@php
    $pageTitle = 'Match Events';
    $pageDescription = 'Manage the timeline for '.$match->slotLabel('home').' vs '.$match->slotLabel('away').'.';
@endphp

@section('content')
    <section class="panel">
        <div class="toolbar">
            <div class="meta">
                Fixture: <strong>{{ $match->slotLabel('home') }} vs {{ $match->slotLabel('away') }}</strong>
            </div>

            <div class="panel-actions">
                <a class="btn btn-secondary" href="{{ route('admin.matches.show', $match) }}">Back to Match</a>
                <a class="btn btn-primary" href="{{ route('admin.matches.events.create', $match) }}">Add Event</a>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Minute</th>
                        <th>Type</th>
                        <th>Team</th>
                        <th>Player</th>
                        <th>Related Player</th>
                        <th>Period</th>
                        <th>Description</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td>{{ $event->minuteLabel() }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $event->event_type)) }}</td>
                            <td>{{ $event->team?->name ?: 'N/A' }}</td>
                            <td>{{ $event->player?->display_name ?: 'N/A' }}</td>
                            <td>{{ $event->relatedPlayer?->display_name ?: 'N/A' }}</td>
                            <td>{{ $event->period ? ucwords(str_replace('_', ' ', $event->period)) : 'N/A' }}</td>
                            <td>{{ $event->description ?: 'N/A' }}</td>
                            <td class="table-actions">
                                <a class="btn-link" href="{{ route('admin.matches.events.edit', [$match, $event]) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.matches.events.destroy', [$match, $event]) }}" onsubmit="return confirm('Delete this match event?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-link" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="meta">No match events recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $events->links() }}
    </section>
@endsection
