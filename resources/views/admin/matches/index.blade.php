@extends('admin.layouts.app')
@php($pageTitle = 'Matches')
@php($pageDescription = 'Manage fixtures, statuses, venues, and scorelines.')
@section('content')
    <section class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.matches.index') }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search code or slug">
                <select name="stage_type">
                    <option value="">All stages</option>
                    @foreach (['group', 'round_of_32', 'round_of_16', 'quarter_final', 'semi_final', 'third_place', 'final'] as $stageType)
                        <option value="{{ $stageType }}" @selected(($filters['stage_type'] ?? '') === $stageType)>{{ ucwords(str_replace('_', ' ', $stageType)) }}</option>
                    @endforeach
                </select>
                <select name="status">
                    <option value="">All statuses</option>
                    @foreach (['scheduled', 'live', 'completed', 'postponed', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>
            <a class="btn btn-primary" href="{{ route('admin.matches.create') }}">Create Match</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Fixture</th><th>Date</th><th>Stage</th><th>Venue</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse ($matches as $match)
                    <tr>
                        <td>
                            <strong>{{ $match->slotLabel('home') }} vs {{ $match->slotLabel('away') }}</strong><br>
                            <span class="meta">{{ $match->code }}</span>
                        </td>
                        <td>{{ $match->match_date?->format('Y-m-d H:i') }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $match->stage_type)) }}</td>
                        <td>{{ $match->stadium?->name ?: ($match->city?->name ?: 'TBD') }}</td>
                        <td><span class="status-badge">{{ $match->status }}</span></td>
                        <td class="table-actions">
                            <a class="btn-link" href="{{ route('admin.matches.show', $match) }}">View</a>
                            <a class="btn-link" href="{{ route('admin.matches.edit', $match) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.matches.destroy', $match) }}" onsubmit="return confirm('Archive this match fixture?');">@csrf @method('DELETE')<button class="btn-link" type="submit">Archive</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="meta">No matches found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $matches->links() }}
    </section>
@endsection
