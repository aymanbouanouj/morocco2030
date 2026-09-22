@extends('admin.layouts.app')
@php($pageTitle = 'Teams')
@php($pageDescription = 'Manage participating teams and their competition grouping.')
@section('content')
    <section class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.teams.index') }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search teams">
                <select name="status"><option value="">All statuses</option>@foreach (['active', 'inactive'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>
            <a class="btn btn-primary" href="{{ route('admin.teams.create') }}">Create Team</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Name</th><th>Group</th><th>Type</th><th>Players</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse ($teams as $team)
                    <tr>
                        <td><strong>{{ $team->name }}</strong><br><span class="meta">{{ $team->code }}</span></td>
                        <td>{{ $team->group?->name }}</td>
                        <td>{{ ucfirst($team->team_type) }}</td>
                        <td>{{ $team->players_count }}</td>
                        <td><span class="status-badge">{{ $team->status }}</span></td>
                        <td class="table-actions">
                            <a class="btn-link" href="{{ route('admin.teams.edit', $team) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.teams.destroy', $team) }}" onsubmit="return confirm('Archive this team?');">@csrf @method('DELETE')<button class="btn-link" type="submit">Archive</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="meta">No teams found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $teams->links() }}
    </section>
@endsection
