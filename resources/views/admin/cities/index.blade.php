@extends('admin.layouts.app')
@php($pageTitle = 'Cities')
@php($pageDescription = 'Maintain host city records for competition and venue planning.')
@section('content')
    <section class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.cities.index') }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search cities">
                <select name="status">
                    <option value="">All statuses</option>
                    @foreach (['active', 'inactive'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>
            @can('create', \App\Models\City::class)
                <a class="btn btn-primary" href="{{ route('admin.cities.create') }}">Create City</a>
            @endcan
        </div>
        <div class="table-wrap admin-venue-table-wrap">
            <table class="admin-venue-table">
                <thead><tr><th>Name</th><th>Region</th><th>Status</th><th>Stadiums</th><th class="admin-venue-actions-heading">Actions</th></tr></thead>
                <tbody>
                @forelse ($cities as $city)
                    <tr>
                        <td><strong>{{ $city->name }}</strong><br><span class="meta">{{ $city->code ?: $city->slug }}</span></td>
                        <td>{{ $city->region ?: 'N/A' }}</td>
                        <td><span class="status-badge">{{ $city->status }}</span></td>
                        <td>{{ $city->stadiums_count }}</td>
                        <td class="admin-venue-actions-cell">
                            <div class="table-actions admin-venue-actions" data-testid="city-row-actions">
                                @can('update', $city)
                                    <a class="btn-link" href="{{ route('admin.cities.edit', $city) }}">Edit</a>
                                @endcan
                                @can('delete', $city)
                                    <form method="POST" action="{{ route('admin.cities.destroy', $city) }}" onsubmit="return confirm('Archive this city?');">@csrf @method('DELETE')<button class="btn-link" type="submit">Archive</button></form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="meta">No cities found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $cities->links() }}
    </section>
@endsection
