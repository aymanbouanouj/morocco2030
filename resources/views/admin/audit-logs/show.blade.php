@extends('admin.layouts.app')

@php($pageTitle = 'Audit Log Detail')
@php($pageDescription = 'Read-only activity entry with redacted value display.')

@section('content')
    <div class="page-stack">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="section-title">{{ $auditLog->action }}</h2>
                    <p class="meta">Occurred {{ $auditLog->occurred_at?->format('Y-m-d H:i:s') }}</p>
                </div>
                <a class="btn btn-secondary" href="{{ route('admin.audit-logs.index') }}">Back To Audit Logs</a>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <strong>User</strong>
                    <span>{{ $auditLog->user?->name ?? 'System / deleted user' }}</span>
                    <small class="meta">{{ $auditLog->user?->email ?? 'N/A' }}</small>
                </div>
                <div class="detail-item">
                    <strong>Auditable</strong>
                    <span>{{ class_basename($auditLog->auditable_type) }} #{{ $auditLog->auditable_id }}</span>
                </div>
                <div class="detail-item">
                    <strong>Route</strong>
                    <span>{{ $auditLog->route_name ?: 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <strong>IP Address</strong>
                    <span>{{ $auditLog->ip_address ?: 'N/A' }}</span>
                </div>
            </div>

            <div class="entity-note" style="margin-top: 18px;">
                <strong>User Agent</strong>
                <p>{{ $auditLog->user_agent ?: 'N/A' }}</p>
            </div>
        </section>

        <section class="grid grid-2">
            <article class="panel">
                <h2 class="section-title">Old Values</h2>
                <pre style="white-space: pre-wrap; overflow-x: auto; margin-top: 14px;">{{ $oldValuesJson }}</pre>
            </article>
            <article class="panel">
                <h2 class="section-title">New Values</h2>
                <pre style="white-space: pre-wrap; overflow-x: auto; margin-top: 14px;">{{ $newValuesJson }}</pre>
            </article>
        </section>
    </div>
@endsection
