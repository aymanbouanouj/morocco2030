@extends('public.layouts.app')

@section('title', __('Notifications').' | '.__('Morocco 2030'))

@section('content')
    @include('public.partials.page-header', [
        'eyebrow' => __('Account'),
        'title' => __('Notifications'),
        'summary' => __('Tournament-related messages sent to your public account appear here.'),
    ])

    <section class="page-section account-layout">
        <aside class="account-sidebar">
            @include('public.account._nav')
        </aside>

        <div class="panel account-panel">
            <div class="section-header">
                <div>
                    <h2>{{ __('Notification history') }}</h2>
                    <p>{{ __('Unread items: :count', ['count' => $unreadNotificationCount]) }}</p>
                </div>
            </div>

            @if ($notifications->isEmpty())
                <div class="account-empty">
                    @include('public.partials.empty-state', [
                        'title' => __('No notifications yet'),
                        'message' => __('When your account receives tournament updates, they will appear here.'),
                    ])
                </div>
            @else
                <div class="account-inline-list" style="margin-top: 1.25rem;">
                    @foreach ($notifications as $notification)
                        <article class="list-card">
                            <small>{{ $notification->type }}</small>
                            <strong>{{ $notification->title }}</strong>
                            <p>{{ $notification->body }}</p>
                            <small>
                                {{ __('Status: :status', ['status' => $notification->read_at ? __('Read') : __('Unread')]) }}
                                ·
                                {{ optional($notification->sent_at ?? $notification->created_at)->translatedFormat('M j, Y H:i') }}
                            </small>
                            @if ($notification->action_url)
                                <a href="{{ $notification->action_url }}" class="section-link">{{ __('Open linked page') }}</a>
                            @endif
                        </article>
                    @endforeach
                </div>

                <div style="margin-top: 1.25rem;">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
