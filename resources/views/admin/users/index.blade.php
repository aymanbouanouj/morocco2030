@extends('admin.layouts.app')

@php
    $pageTitle = 'Users';
    $pageDescription = $accountType === 'public'
        ? 'Review public audience accounts registered on the public site.'
        : 'Manage internal staff accounts and administrative role assignments.';
@endphp

@section('content')
    <section class="panel admin-users-page admin-users-module admin-users-card">
        <nav class="admin-users-tabs" aria-label="User account categories">
            <a
                href="{{ route('admin.users.index', array_merge(request()->except('type', 'page'), ['type' => 'staff'])) }}"
                @class(['admin-users-tabs__item', 'is-active' => $accountType === 'staff'])
            >
                Staff Accounts
                <span class="admin-users-tabs__badge">{{ $staffCount }}</span>
            </a>
            <a
                href="{{ route('admin.users.index', array_merge(request()->except('type', 'page'), ['type' => 'public'])) }}"
                @class(['admin-users-tabs__item', 'is-active' => $accountType === 'public'])
            >
                Public Audience
                <span class="admin-users-tabs__badge">{{ $publicCount }}</span>
            </a>
        </nav>

        <div class="toolbar admin-users-toolbar">
            <form method="GET" action="{{ route('admin.users.index') }}" class="admin-users-filters">
                <input type="hidden" name="type" value="{{ $accountType }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, email, or phone">
                <select name="status">
                    <option value="">All statuses</option>
                    @foreach (['active', 'inactive', 'suspended'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>

            @if ($accountType === 'staff' && $canCreateStaff)
                <a class="btn btn-primary" href="{{ route('admin.users.create') }}">Create Staff User</a>
            @endif
        </div>

        <div class="admin-users-table-wrap admin-users-table-wrap--fit">
            <table class="admin-users-table admin-users-table--fit">
                <colgroup>
                    <col class="admin-users-col-name">
                    <col class="admin-users-col-email">
                    <col class="admin-users-col-type">
                    <col class="admin-users-col-status">
                    <col class="admin-users-col-meta">
                    <col class="admin-users-col-login">
                    <col class="admin-users-col-actions">
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Type</th>
                        <th scope="col">Status</th>
                        @if ($accountType === 'staff')
                            <th scope="col">Roles</th>
                        @else
                            <th scope="col">Registered</th>
                        @endif
                        <th scope="col" class="admin-users-col-login-head admin-users-sticky-col admin-users-sticky-col--login">Last login</th>
                        <th scope="col" class="admin-users-col-actions-head admin-users-sticky-col admin-users-sticky-col--actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="admin-users-cell-name" title="{{ $user->name }}">
                                <strong>{{ $user->name }}</strong>
                            </td>
                            <td class="admin-users-cell-email" title="{{ $user->email }}">
                                <span class="meta">{{ $user->email }}</span>
                            </td>
                            <td class="admin-users-table__type-cell">
                                @if ($user->isPublic())
                                    <span class="admin-users-badge admin-users-badge--public">Public Audience</span>
                                    <span class="admin-users-badge admin-users-badge--muted">No admin access</span>
                                @else
                                    <span class="admin-users-badge admin-users-badge--staff">Staff</span>
                                @endif
                            </td>
                            <td>
                                <span class="admin-users-badge admin-users-badge--status">{{ $user->status }}</span>
                            </td>
                            @if ($accountType === 'staff')
                                <td class="admin-users-table__roles">
                                    @forelse ($user->roles as $role)
                                        <span class="admin-users-badge admin-users-badge--role">{{ $role->name }}</span>
                                    @empty
                                        <span class="meta">None</span>
                                    @endforelse
                                </td>
                            @else
                                <td class="admin-users-cell-date">{{ optional($user->created_at)->format('Y-m-d') ?: '—' }}</td>
                            @endif
                            <td class="admin-users-cell-login admin-users-sticky-col admin-users-sticky-col--login">{{ optional($user->last_login_at)->format('Y-m-d H:i') ?: 'Never' }}</td>
                            <td class="admin-users-actions admin-users-sticky-col admin-users-sticky-col--actions">
                                <div class="admin-users-actions__group">
                                    <a class="admin-users-action-btn" href="{{ route('admin.users.show', $user) }}">View</a>
                                    <a class="admin-users-action-btn" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="admin-users-action-form" onsubmit="return confirm('Archive this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-users-action-btn admin-users-action-btn--danger">Archive</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="meta">
                                {{ $accountType === 'staff' ? 'No staff accounts found.' : 'No public audience accounts found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </section>
@endsection
