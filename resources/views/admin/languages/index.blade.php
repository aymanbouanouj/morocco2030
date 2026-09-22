@extends('admin.layouts.app')

@php($pageTitle = 'Languages')
@php($pageDescription = 'Manage active public languages without changing protected language codes.')

@section('content')
    <section class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.languages.index') }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search languages">
                <select name="direction">
                    <option value="">All directions</option>
                    @foreach (['ltr', 'rtl'] as $direction)
                        <option value="{{ $direction }}" @selected(($filters['direction'] ?? '') === $direction)>{{ strtoupper($direction) }}</option>
                    @endforeach
                </select>
                <select name="status">
                    <option value="">All statuses</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                </select>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>
            <span class="meta">Language codes and locales are protected from admin edits.</span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Language</th>
                        <th>Code</th>
                        <th>Locale</th>
                        <th>Direction</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($languages as $language)
                        <tr>
                            <td>
                                <strong>{{ $language->name }}</strong><br>
                                <span class="meta">{{ $language->native_name }}</span>
                                @if ($language->is_default)
                                    <br><span class="status-badge">Default</span>
                                @endif
                            </td>
                            <td>{{ $language->code }}</td>
                            <td>{{ $language->locale }}</td>
                            <td>{{ strtoupper($language->direction) }}</td>
                            <td><span class="status-badge">{{ $language->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td>{{ $language->sort_order }}</td>
                            <td class="table-actions">
                                <a class="btn-link" href="{{ route('admin.languages.edit', $language) }}">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="meta">No languages found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $languages->links() }}
    </section>
@endsection
