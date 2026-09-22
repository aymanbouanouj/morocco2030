@extends('admin.layouts.app')

@php($pageTitle = 'Contact Messages')
@php($pageDescription = 'Review and triage incoming platform contact messages.')

@section('content')
    <section class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.contact-messages.index') }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search messages">
                <select name="status">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Message</th>
                        <th>From</th>
                        <th>Status</th>
                        <th>Assigned</th>
                        <th>Received</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($messages as $message)
                        <tr>
                            <td>
                                <strong>{{ $message->subject }}</strong><br>
                                <span class="meta">{{ \Illuminate\Support\Str::limit($message->message, 90) }}</span>
                            </td>
                            <td>
                                {{ $message->name }}<br>
                                <span class="meta">{{ $message->email }}</span>
                            </td>
                            <td><span class="status-badge">{{ str_replace('_', ' ', $message->status) }}</span></td>
                            <td>{{ $message->assignedTo?->name ?? 'Unassigned' }}</td>
                            <td>{{ $message->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="table-actions">
                                <a class="btn-link" href="{{ route('admin.contact-messages.show', $message) }}">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="meta">No contact messages found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $messages->links() }}
    </section>
@endsection
