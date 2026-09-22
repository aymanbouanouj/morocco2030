@extends('admin.layouts.app')

@php
    $pageTitle = 'Role Details';
    $pageDescription = 'Review permissions and assigned users for this role.';
@endphp

@section('content')
    <div class="grid grid-2">
        <section class="panel">
            <h2 class="section-title">{{ $role->name }}</h2>
            <p class="meta">{{ $role->description ?: 'No description provided.' }}</p>
            <div class="field-inline" style="margin-top: 12px;">
                <span class="status-badge">{{ $role->slug }}</span>
                @if ($role->is_system)
                    <span class="status-badge">system</span>
                @endif
            </div>
            <div style="margin-top: 18px;">
                <a class="btn btn-primary" href="{{ route('admin.roles.edit', $role) }}">Edit Role</a>
            </div>
        </section>

        <section class="panel">
            <h2 class="section-title">Assigned Users</h2>
            @if ($role->users->isEmpty())
                <p class="meta">No users assigned to this role.</p>
            @else
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($role->users as $user)
                        <li>{{ $user->name }} ({{ $user->email }})</li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    <section class="panel" style="margin-top: 20px;">
        <h2 class="section-title">Permissions</h2>
        @if ($role->permissions->isEmpty())
            <p class="meta">No permissions assigned.</p>
        @else
            <div class="checkbox-list">
                @foreach ($role->permissions as $permission)
                    <div class="checkbox-card">
                        <span>
                            <strong>{{ $permission->name }}</strong><br>
                            <span class="meta">{{ $permission->module }} / {{ $permission->slug }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
