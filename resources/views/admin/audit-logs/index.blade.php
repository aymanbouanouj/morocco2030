@extends('admin.layouts.app')

@php($pageTitle = 'Audit Logs')
@php($pageDescription = 'Read-only platform activity trail for operational review.')

@section('content')
    <div class="page-stack">
        <section class="stats-grid">
            <article class="panel stats-card">
                <h3>Total Logs</h3>
                <strong>{{ number_format($summary['total']) }}</strong>
                <p>All recorded audit entries.</p>
            </article>
            <article class="panel stats-card">
                <h3>Today</h3>
                <strong>{{ number_format($summary['today']) }}</strong>
                <p>Entries recorded since midnight.</p>
            </article>
            <article class="panel stats-card">
                <h3>Latest Action</h3>
                <strong>{{ $summary['latestAction'] ?: 'N/A' }}</strong>
                <p>Most recent audited event.</p>
            </article>
            <article class="panel stats-card">
                <h3>Most Active User</h3>
                <strong>{{ $summary['mostActiveUser'] ?: 'N/A' }}</strong>
                <p>{{ number_format($summary['mostActiveCount']) }} recorded entries.</p>
            </article>
        </section>

        <section class="panel">
            <div class="toolbar">
                <form method="GET" action="{{ route('admin.audit-logs.index') }}">
                    <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search action, route, user">
                    <select name="user_id">
                        <option value="">All users</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    <select name="action">
                        <option value="">All actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(($filters['action'] ?? '') === $action)>{{ $action }}</option>
                        @endforeach
                    </select>
                    <select name="auditable_type">
                        <option value="">All models</option>
                        @foreach ($auditableTypes as $type)
                            <option value="{{ $type }}" @selected(($filters['auditable_type'] ?? '') === $type)>{{ class_basename($type) }}</option>
                        @endforeach
                    </select>
                    <input name="auditable_id" value="{{ $filters['auditable_id'] ?? '' }}" placeholder="Auditable ID">
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" aria-label="Date from">
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" aria-label="Date to">
                    <button class="btn btn-secondary" type="submit">Filter</button>
                </form>
                <span class="meta">Read-only audit trail. Edit and delete actions are intentionally unavailable.</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>Action</th>
                            <th>User</th>
                            <th>Model</th>
                            <th>Route</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->occurred_at?->format('Y-m-d H:i') }}</td>
                                <td><strong>{{ $log->action }}</strong></td>
                                <td>
                                    {{ $log->user?->name ?? 'System / deleted user' }}<br>
                                    <span class="meta">{{ $log->user?->email ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    {{ class_basename($log->auditable_type) }}<br>
                                    <span class="meta">#{{ $log->auditable_id }}</span>
                                </td>
                                <td>{{ $log->route_name ?: 'N/A' }}</td>
                                <td class="table-actions">
                                    <a class="btn-link" href="{{ route('admin.audit-logs.show', $log) }}">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="meta">No audit logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $logs->links() }}
        </section>
    </div>
@endsection
