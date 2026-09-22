@extends('public.layouts.app')

@section('title', __('My Account').' | '.__('Morocco 2030'))

@section('content')
    @include('public.partials.page-header', [
        'eyebrow' => __('Account'),
        'title' => __('Welcome Back, :name', ['name' => $user->name]),
        'summary' => __('Your personal Morocco 2030 space for saved items, notifications, and account preferences.'),
    ])

    <section class="page-section account-layout">
        <aside class="account-sidebar">
            @include('public.account._nav')
        </aside>

        <div class="section-stack">
            <div class="panel account-panel account-overview-card">
                <div class="account-identity">
                    <span class="account-avatar" aria-hidden="true">{{ str($user->name ?? 'M30')->substr(0, 1)->upper() }}</span>
                    <div>
                        <span class="account-kicker">{{ __('Public user') }}</span>
                        <h2>{{ $user->name }}</h2>
                        <p>{{ $user->email }}</p>
                    </div>
                </div>

                <dl class="account-meta-list" aria-label="{{ __('Account summary') }}">
                    <div>
                        <dt>{{ __('Account Type') }}</dt>
                        <dd>{{ __('Public user') }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('Profile Status') }}</dt>
                        <dd>{{ __(ucfirst($user->status)) }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('Preferred Language') }}</dt>
                        <dd>{{ strtoupper($user->preferred_locale ?? app()->getLocale()) }}</dd>
                    </div>
                </dl>

                <div class="account-quick-actions" aria-label="{{ __('Account quick actions') }}">
                    <a href="{{ route('account.profile') }}" class="button--subtle">{{ __('Edit Profile') }}</a>
                    <a href="{{ route('account.settings') }}" class="button--subtle">{{ __('Account Settings') }}</a>
                </div>
            </div>

            <div class="summary-grid">
                <div class="panel account-panel account-stat">
                    <strong>{{ $favoriteCount }}</strong>
                    <span>{{ __('Saved favorites') }}</span>
                </div>
                <div class="panel account-panel account-stat">
                    <strong>{{ $notificationCount }}</strong>
                    <span>{{ __('Notifications') }}</span>
                </div>
                <div class="panel account-panel account-stat">
                    <strong>{{ $unreadNotificationCount }}</strong>
                    <span>{{ __('Unread updates') }}</span>
                </div>
                <div class="panel account-panel account-stat">
                    <strong>{{ strtoupper($user->preferred_locale ?? app()->getLocale()) }}</strong>
                    <span>{{ __('Preferred language') }}</span>
                </div>
            </div>

            <div class="split-grid">
                <div class="panel account-panel">
                    <div class="section-header">
                        <div>
                            <span class="section-header__eyebrow">{{ __('Favorites') }}</span>
                            <h2>{{ __('Recently Saved') }}</h2>
                        </div>
                        <a href="{{ route('account.favorites') }}" class="button--subtle">{{ __('All Favorites') }}</a>
                    </div>

                    @if ($latestFavorites->isEmpty())
                        <div class="account-empty">
                            @include('public.partials.empty-state', [
                                'title' => __('No favorites yet'),
                                'message' => __('When you save teams, players, cities, or fixtures, they will appear here.'),
                            ])
                        </div>
                    @else
                        <div class="account-inline-list">
                            @foreach ($latestFavorites as $favorite)
                                <article class="list-card">
                                    <small>{{ $favorite['type'] }}</small>
                                    @if ($favorite['url'])
                                        <a href="{{ $favorite['url'] }}"><strong>{{ $favorite['title'] }}</strong></a>
                                    @else
                                        <strong>{{ $favorite['title'] }}</strong>
                                    @endif
                                    <p>{{ $favorite['summary'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="panel account-panel">
                    <div class="section-header">
                        <div>
                            <span class="section-header__eyebrow">{{ __('Inbox') }}</span>
                            <h2>{{ __('Latest Notifications') }}</h2>
                        </div>
                        <a href="{{ route('account.notifications') }}" class="button--subtle">{{ __('All Notifications') }}</a>
                    </div>

                    @if ($latestNotifications->isEmpty())
                        <div class="account-empty">
                            @include('public.partials.empty-state', [
                                'title' => __('No notifications yet'),
                                'message' => __('Account alerts and tournament updates sent to you will appear here.'),
                            ])
                        </div>
                    @else
                        <div class="account-inline-list">
                            @foreach ($latestNotifications as $notification)
                                <article class="list-card">
                                    <small>{{ $notification->type }}</small>
                                    <strong>{{ $notification->title }}</strong>
                                    <p>{{ $notification->body }}</p>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
