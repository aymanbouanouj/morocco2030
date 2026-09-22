@extends('admin.layouts.app')

@php($pageTitle = 'Contact Message')
@php($pageDescription = 'Review message details and update triage status.')

@section('content')
    <div class="grid grid-2">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="section-title">{{ $contactMessage->subject }}</h2>
                    <p class="meta">Received {{ $contactMessage->created_at?->format('Y-m-d H:i') }}</p>
                </div>
                <span class="status-badge">{{ str_replace('_', ' ', $contactMessage->status) }}</span>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <strong>Name</strong>
                    <span>{{ $contactMessage->name }}</span>
                </div>
                <div class="detail-item">
                    <strong>Email</strong>
                    <span>{{ $contactMessage->email }}</span>
                </div>
                <div class="detail-item">
                    <strong>Phone</strong>
                    <span>{{ $contactMessage->phone ?: 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <strong>Source</strong>
                    <span>{{ $contactMessage->source }}</span>
                </div>
                <div class="detail-item">
                    <strong>Language</strong>
                    <span>{{ $contactMessage->language?->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <strong>Assigned To</strong>
                    <span>{{ $contactMessage->assignedTo?->name ?? 'Unassigned' }}</span>
                </div>
            </div>

            <div class="entity-note" style="margin-top: 18px;">
                <strong>Message</strong>
                <p>{{ $contactMessage->message }}</p>
            </div>
        </section>

        <section class="panel">
            <h2 class="section-title">Triage</h2>
            <form method="POST" action="{{ route('admin.contact-messages.update', $contactMessage) }}" style="margin-top: 16px;">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="stacked-field">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(old('status', $contactMessage->status) === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                            @endforeach
                        </select>
                        @error('status')<span class="meta">{{ $message }}</span>@enderror
                    </div>
                    <div class="stacked-field">
                        <label for="assigned_to">Assigned Staff</label>
                        <select id="assigned_to" name="assigned_to">
                            <option value="">Unassigned</option>
                            @foreach ($staffUsers as $staff)
                                <option value="{{ $staff->id }}" @selected((int) old('assigned_to', $contactMessage->assigned_to) === $staff->id)>
                                    {{ $staff->name }} &mdash; {{ $staff->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')<span class="meta">{{ $message }}</span>@enderror
                    </div>
                    <div class="stacked-field full-span">
                        <label for="internal_note">Internal Note</label>
                        <textarea id="internal_note" name="internal_note">{{ old('internal_note', $contactMessage->meta['internal_note'] ?? '') }}</textarea>
                        @error('internal_note')<span class="meta">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="field-inline" style="margin-top: 18px;">
                    <button class="btn btn-primary" type="submit">Update Message</button>
                    <a class="btn btn-secondary" href="{{ route('admin.contact-messages.index') }}">Back To Messages</a>
                </div>
            </form>
        </section>
    </div>
@endsection
