@extends('admin.layouts.app')
@php($pageTitle = 'Partners')
@php($pageDescription = 'Maintain sponsor and partner records for the platform.')
@section('content')
    <section class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.partners.index') }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search partners">
                <select name="status"><option value="">All statuses</option>@foreach (['active', 'inactive'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>
            @can('create', \App\Models\Partner::class)
                <a class="btn btn-primary" href="{{ route('admin.partners.create') }}">Create Partner</a>
            @endcan
        </div>
        <div class="table-wrap admin-venue-table-wrap">
            <table class="admin-venue-table">
                <thead><tr><th>Logo</th><th>Name</th><th>Category</th><th>Tier</th><th>Status</th><th class="admin-venue-actions-heading">Actions</th></tr></thead>
                <tbody>
                @forelse ($partners as $partner)
                    <tr>
                        <td>
                            @if ($partner->hasLogo())
                                <img src="{{ $partner->logoUrl() }}" alt="{{ $partner->logoAlt() }}" style="width: 56px; height: 40px; object-fit: contain;">
                            @else
                                <span class="status-badge" aria-label="{{ $partner->name }} logo fallback">{{ str($partner->name)->substr(0, 2)->upper() }}</span>
                            @endif
                        </td>
                        <td><strong>{{ $partner->name }}</strong><br><span class="meta">{{ $partner->slug }}</span></td>
                        <td>{{ $partner->category }}</td>
                        <td>{{ $partner->tier ?: 'N/A' }}</td>
                        <td><span class="status-badge">{{ $partner->status }}</span></td>
                        <td class="admin-venue-actions-cell">
                            <div class="table-actions admin-venue-actions" data-testid="partner-row-actions">
                                @can('update', $partner)
                                    <a class="btn-link" href="{{ route('admin.partners.edit', $partner) }}">Edit</a>
                                @endcan
                                @can('delete', $partner)
                                    <form method="POST" action="{{ route('admin.partners.destroy', $partner) }}" onsubmit="return confirm('Archive this partner?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-link" type="submit">Archive</button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="meta">
                            No partners found.
                            @can('create', \App\Models\Partner::class)
                                <a class="btn-link" href="{{ route('admin.partners.create') }}">Create the first partner</a>
                            @endcan
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $partners->links() }}
    </section>
@endsection
