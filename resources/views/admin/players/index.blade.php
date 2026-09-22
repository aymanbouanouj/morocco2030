@extends('admin.layouts.app')
@php($pageTitle = 'Players')
@php($pageDescription = 'Maintain squad rosters and core player identity records.')
@section('content')
    <section class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.players.index') }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search players">
                <select name="team_id"><option value="">All teams</option>@foreach ($teams as $team)<option value="{{ $team->id }}" @selected((string) ($filters['team_id'] ?? '') === (string) $team->id)>{{ $team->name }}</option>@endforeach</select>
                <select name="status"><option value="">All statuses</option>@foreach (['active', 'inactive'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>
            <a class="btn btn-primary" href="{{ route('admin.players.create') }}">Create Player</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Player</th><th>Team</th><th>Position</th><th>Captain</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse ($players as $player)
                    <tr>
                        <td><strong>{{ $player->display_name }}</strong><br><span class="meta">{{ $player->shirt_number ? '#'.$player->shirt_number : $player->slug }}</span></td>
                        <td>{{ $player->team?->name }}</td>
                        <td>{{ ucfirst($player->position) }}</td>
                        <td>{{ $player->is_captain ? 'Yes' : 'No' }}</td>
                        <td><span class="status-badge">{{ $player->status }}</span></td>
                        <td class="table-actions">
                            <a class="btn-link" href="{{ route('admin.players.edit', $player) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.players.destroy', $player) }}" onsubmit="return confirm('Archive this player?');">@csrf @method('DELETE')<button class="btn-link" type="submit">Archive</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="meta">No players found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $players->links() }}
    </section>
@endsection
