@extends('admin.layouts.app')

@php
    $pageTitle = 'Roles';
    $pageDescription = 'Configure access roles and permission assignments.';
@endphp

@section('content')
    <section class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.roles.index') }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search roles">
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>

            <a class="btn btn-primary" href="{{ route('admin.roles.create') }}">Create Role</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Permissions</th>
                        <th>Users</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>
                                <strong>{{ $role->name }}</strong><br>
                                @if ($role->is_system)
                                    <span class="meta">System role</span>
                                @endif
                            </td>
                            <td>{{ $role->slug }}</td>
                            <td>{{ $role->permissions_count }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td class="table-actions">
                                <a class="btn-link" href="{{ route('admin.roles.show', $role) }}">View</a>
                                <a class="btn-link" href="{{ route('admin.roles.edit', $role) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-link" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="meta">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $roles->links() }}
    </section>
@endsection
