@extends('admin.layouts.app')

@php
    $pageTitle = $user->isPublic() ? 'Public Audience Profile' : 'Staff Account Profile';
    $pageDescription = $user->isPublic()
        ? 'Public registration account without administrative access.'
        : 'Internal staff account overview and recent audit activity.';
@endphp

@section('content')
    <div class="grid grid-2 admin-users-module">
        <section class="panel">
            <h2 class="section-title">{{ $user->name }}</h2>
            <div class="meta">{{ $user->email }}</div>
            <div class="field-inline" style="margin-top: 16px;">
                @if ($user->isPublic())
                    <span class="status-badge status-badge--public">Public Audience</span>
                    <span class="status-badge status-badge--muted">No admin access</span>
                @else
                    <span class="status-badge">{{ $user->user_type }}</span>
                @endif
                <span class="status-badge">{{ $user->status }}</span>
            </div>

            @if ($user->isStaff())
                <div style="margin-top: 18px;">
                    <strong>Roles</strong>
                    <p class="meta">{{ $user->roles->pluck('name')->join(', ') ?: 'No roles assigned' }}</p>
                </div>
            @else
                <div style="margin-top: 18px;">
                    <strong>Admin roles</strong>
                    <p class="meta">None — public audience accounts never receive admin roles.</p>
                </div>
            @endif

            <div style="margin-top: 18px;">
                <strong>Preferred Locale</strong>
                <p class="meta">{{ $user->preferred_locale ?: 'Not set' }}</p>
            </div>

            <div style="margin-top: 18px;">
                <strong>Registered</strong>
                <p class="meta">{{ optional($user->created_at)->format('Y-m-d H:i') ?: '—' }}</p>
            </div>

            <div style="margin-top: 18px;">
                <a class="btn btn-primary" href="{{ route('admin.users.edit', $user) }}">Edit Account</a>
                <a class="btn btn-secondary" href="{{ route('admin.users.index', ['type' => $user->isPublic() ? 'public' : 'staff']) }}">Back to list</a>
            </div>
        </section>

        <section class="panel">
            <h2 class="section-title">Recent Audit Logs</h2>
            @if ($recentAuditLogs->isEmpty())
                <p class="meta">No audit entries recorded for this user yet.</p>
            @else
                <div class="table-wrap admin-users-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>When</th>
                                <th>Action</th>
                                <th>Route</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentAuditLogs as $activity)
                                <tr>
                                    <td>{{ optional($activity->occurred_at)->format('Y-m-d H:i') }}</td>
                                    <td>{{ $activity->action }}</td>
                                    <td>{{ $activity->route_name ?: 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
