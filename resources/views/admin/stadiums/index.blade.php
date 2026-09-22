@extends('admin.layouts.app')
@php($pageTitle = 'Stadiums')
@php($pageDescription = 'Manage venue records and operational capacity details.')
@section('content')
    <section class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.stadiums.index') }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search stadiums">
                <select name="status"><option value="">All statuses</option>@foreach (['active', 'inactive'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>
            @can('create', \App\Models\Stadium::class)
                <a class="btn btn-primary" href="{{ route('admin.stadiums.create') }}">Create Stadium</a>
            @endcan
        </div>
        <div class="table-wrap admin-venue-table-wrap">
            <table class="admin-venue-table">
                <thead><tr><th>Name</th><th>City</th><th>Capacity</th><th>Status</th><th class="admin-venue-actions-heading">Actions</th></tr></thead>
                <tbody>
                @forelse ($stadiums as $stadium)
                    <tr>
                        <td><strong>{{ $stadium->name }}</strong><br><span class="meta">{{ $stadium->code ?: $stadium->slug }}</span></td>
                        <td>{{ $stadium->city?->name }}</td>
                        <td>{{ $stadium->capacity ? number_format($stadium->capacity) : 'N/A' }}</td>
                        <td><span class="status-badge">{{ $stadium->status }}</span></td>
                        <td class="admin-venue-actions-cell">
                            <div class="table-actions admin-venue-actions" data-testid="stadium-row-actions">
                                @can('update', $stadium)
                                    <a class="btn-link" href="{{ route('admin.stadiums.edit', $stadium) }}">Edit</a>
                                @endcan
                                @can('delete', $stadium)
                                    <form method="POST" action="{{ route('admin.stadiums.destroy', $stadium) }}" onsubmit="return confirm('Archive this stadium?');">@csrf @method('DELETE')<button class="btn-link" type="submit">Archive</button></form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="meta">No stadiums found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $stadiums->links() }}
    </section>
@endsection
