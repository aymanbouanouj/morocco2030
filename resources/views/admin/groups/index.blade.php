@extends('admin.layouts.app')

@php
    $pageTitle = 'Groups';
    $pageDescription = 'Manage competition groups used by teams, fixtures, and later standings logic.';
@endphp

@section('content')
    <section class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.groups.index') }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search groups by name or code">
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>

            <a class="btn btn-primary" href="{{ route('admin.groups.create') }}">Create Group</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Sort Order</th>
                        <th>Teams</th>
                        <th>Matches</th>
                        <th>Standings</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($groups as $group)
                        <tr>
                            <td>
                                <strong>{{ $group->name }}</strong><br>
                                <span class="meta">{{ $group->description ?: 'No description' }}</span>
                            </td>
                            <td>{{ $group->code }}</td>
                            <td>{{ $group->sort_order }}</td>
                            <td>{{ $group->teams_count }}</td>
                            <td>{{ $group->matches_count }}</td>
                            <td>{{ $group->standings_count }}</td>
                            <td class="table-actions">
                                <a class="btn-link" href="{{ route('admin.groups.edit', $group) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.groups.destroy', $group) }}" onsubmit="return confirm('Delete this group? This only works if it is unused.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-link" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="meta">No groups found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $groups->links() }}
    </section>
@endsection
