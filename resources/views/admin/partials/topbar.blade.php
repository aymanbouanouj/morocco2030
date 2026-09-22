@php
    use App\Models\AuditLog;
    use App\Models\City;
    use App\Models\ContactMessage;
    use App\Models\Group;
    use App\Models\InterfaceTranslation;
    use App\Models\Language;
    use App\Models\MatchFixture;
    use App\Models\MediaFile;
    use App\Models\News;
    use App\Models\Partner;
    use App\Models\Player;
    use App\Models\Setting;
    use App\Models\Stadium;
    use App\Models\Team;
    use App\Models\User;
    use App\Support\PublicLocale;

    $publicLocale = app(PublicLocale::class);
    $adminCurrentLanguage = $publicLocale->currentLanguage();
    $currentLocale = app()->getLocale();
    $activeLanguages = $publicLocale->availableLanguages();
    $authUser = auth()->user();

    $pendingContactCount = $authUser?->can('viewAny', ContactMessage::class)
        ? ContactMessage::query()->where('status', 'new')->count()
        : 0;

    $adminSearchSections = collect([
        ['label' => __('admin.topbar.nav.dashboard'), 'url' => route('admin.dashboard'), 'keywords' => 'dashboard home overview'],
    ]);

    if ($authUser?->can('viewAny', User::class)) {
        $adminSearchSections->push(['label' => __('admin.topbar.nav.users'), 'url' => route('admin.users.index'), 'keywords' => 'users roles accounts staff administrators']);
    }
    if ($authUser?->can('viewAny', News::class)) {
        $adminSearchSections->push(['label' => __('admin.topbar.nav.news'), 'url' => route('admin.news.index'), 'keywords' => 'news articles posts content']);
    }
    if ($authUser?->can('viewAny', Team::class)) {
        $adminSearchSections->push(['label' => __('admin.topbar.nav.teams'), 'url' => route('admin.teams.index'), 'keywords' => 'teams squads nations']);
    }
    if ($authUser?->can('viewAny', Player::class)) {
        $adminSearchSections->push(['label' => __('admin.topbar.nav.players'), 'url' => route('admin.players.index'), 'keywords' => 'players roster athletes']);
    }
    if ($authUser?->can('viewAny', MatchFixture::class)) {
        $adminSearchSections->push(['label' => __('admin.topbar.nav.matches'), 'url' => route('admin.matches.index'), 'keywords' => 'matches fixtures games schedule']);
    }
    if ($authUser?->can('viewAny', Group::class)) {
        $adminSearchSections->push(['label' => __('admin.topbar.nav.standings'), 'url' => route('admin.groups.index'), 'keywords' => 'standings groups tables rankings']);
    }
    if ($authUser?->can('viewAny', City::class)) {
        $adminSearchSections->push(['label' => __('admin.topbar.nav.cities'), 'url' => route('admin.cities.index'), 'keywords' => 'cities locations hosts']);
    }
    if ($authUser?->can('viewAny', Stadium::class)) {
        $adminSearchSections->push(['label' => __('admin.topbar.nav.stadiums'), 'url' => route('admin.stadiums.index'), 'keywords' => 'stadiums venues arenas']);
    }
    if ($authUser?->can('viewAny', Partner::class)) {
        $adminSearchSections->push(['label' => __('admin.topbar.nav.partners'), 'url' => route('admin.partners.index'), 'keywords' => 'partners sponsors']);
    }
    if ($authUser?->can('viewAny', MediaFile::class)) {
        $adminSearchSections->push(['label' => __('admin.topbar.nav.media_review'), 'url' => route('admin.media-files.index'), 'keywords' => 'media review asset image video visual content traceability quality']);
    }
    if ($authUser?->can('viewAny', InterfaceTranslation::class)) {
        $adminSearchSections->push(['label' => __('admin.topbar.nav.translations'), 'url' => route('admin.interface-translations.index'), 'keywords' => 'translations locales interface i18n languages']);
    }
    if ($authUser?->can('viewAny', AuditLog::class)) {
        $adminSearchSections->push(['label' => __('admin.topbar.nav.audit_logs'), 'url' => route('admin.audit-logs.index'), 'keywords' => 'audit logs activity history security']);
    }
    if ($authUser?->can('viewAny', Setting::class)) {
        $adminSearchSections->push(['label' => __('admin.topbar.nav.settings'), 'url' => route('admin.settings.index'), 'keywords' => 'settings configuration system']);
    }

    $canViewContactMessages = $authUser?->can('viewAny', ContactMessage::class) ?? false;
    $canViewProfile = $authUser && $authUser->can('view', $authUser);
    $canViewSettings = $authUser?->can('viewAny', Setting::class) ?? false;
    $canViewAuditLogs = $authUser?->can('viewAny', AuditLog::class) ?? false;
    $canSwitchLanguage = Route::has('language.switch') && $activeLanguages->isNotEmpty();

    $adminNotifications = collect();
    $actionableNotificationCount = 0;

    if ($canViewAuditLogs) {
        $recentAuditLogs = AuditLog::query()
            ->with('user')
            ->latest('occurred_at')
            ->limit(3)
            ->get();

        foreach ($recentAuditLogs as $auditLog) {
            $adminNotifications->push([
                'kind' => 'audit',
                'dot' => 'info',
                'title' => $auditLog->action,
                'description' => $auditLog->user?->name ?? __('admin.topbar.system'),
                'time' => optional($auditLog->occurred_at)->diffForHumans(),
                'url' => $authUser?->can('view', $auditLog)
                    ? route('admin.audit-logs.show', $auditLog)
                    : route('admin.audit-logs.index'),
                'actionable' => false,
            ]);
        }
    }

    if ($authUser?->can('viewAny', MatchFixture::class)) {
        $matchesAwaitingResults = MatchFixture::query()
            ->where('status', 'scheduled')
            ->where('match_date', '<', now())
            ->count();

        if ($matchesAwaitingResults > 0) {
            $adminNotifications->push([
                'kind' => 'action',
                'dot' => 'warn',
                'title' => trans_choice('admin.topbar.matches_awaiting_results', $matchesAwaitingResults, ['count' => $matchesAwaitingResults]),
                'description' => __('admin.topbar.notif_matches_desc'),
                'time' => null,
                'url' => route('admin.matches.index', ['status' => 'scheduled']),
                'actionable' => true,
            ]);
            $actionableNotificationCount++;
        }
    }

    if ($authUser?->can('viewAny', News::class)) {
        $draftNewsCount = News::query()->whereIn('status', ['pending_review', 'draft'])->count();
        if ($draftNewsCount > 0) {
            $adminNotifications->push([
                'kind' => 'action',
                'dot' => 'warn',
                'title' => trans_choice('admin.topbar.draft_news', $draftNewsCount, ['count' => $draftNewsCount]),
                'description' => __('admin.topbar.notif_news_desc'),
                'time' => null,
                'url' => route('admin.news.index', ['status' => 'pending_review']),
                'actionable' => true,
            ]);
            $actionableNotificationCount++;
        }
    }

    if ($canViewContactMessages && $pendingContactCount > 0) {
        $adminNotifications->push([
            'kind' => 'action',
            'dot' => 'warn',
            'title' => trans_choice('admin.topbar.notif_contact_title', $pendingContactCount, ['count' => $pendingContactCount]),
            'description' => __('admin.topbar.notif_contact_desc'),
            'time' => null,
            'url' => route('admin.contact-messages.index'),
            'actionable' => true,
        ]);
        $actionableNotificationCount++;
    }

    if ($actionableNotificationCount === 0 && $authUser?->can('viewAny', MediaFile::class)) {
        $adminNotifications->push([
            'kind' => 'overview',
            'dot' => 'muted',
            'title' => __('admin.topbar.media_review'),
            'description' => __('admin.topbar.notif_media_desc'),
            'time' => null,
            'url' => route('admin.media-files.index'),
            'actionable' => false,
        ]);
    }

    if ($actionableNotificationCount === 0 && $adminNotifications->where('kind', 'audit')->isEmpty()) {
        $adminNotifications->prepend([
            'kind' => 'overview',
            'dot' => 'ok',
            'title' => __('admin.topbar.system_overview'),
            'description' => __('admin.topbar.no_urgent_alerts'),
            'time' => null,
            'url' => null,
            'actionable' => false,
        ]);
    }

    if ($canViewAuditLogs) {
        $adminNotifications->push([
            'kind' => 'overview',
            'dot' => 'info',
            'title' => __('admin.topbar.recent_activity'),
            'description' => __('admin.topbar.view_audit_logs'),
            'time' => null,
            'url' => route('admin.audit-logs.index'),
            'actionable' => false,
        ]);
    } elseif ($authUser?->can('viewAny', MatchFixture::class)) {
        $adminNotifications->push([
            'kind' => 'overview',
            'dot' => 'info',
            'title' => __('admin.topbar.recent_activity'),
            'description' => __('admin.topbar.notif_review_matches'),
            'time' => null,
            'url' => route('admin.matches.index'),
            'actionable' => false,
        ]);
    }

    $notificationBadgeCount = $actionableNotificationCount > 0 ? min($actionableNotificationCount, 9) : 0;
    $topbarLanguageCode = strtoupper($adminCurrentLanguage?->code ?? $currentLocale);
@endphp

<header class="admin-topbar admin-ref-topbar" role="banner">
    <div class="admin-ref-topbar__left">
        <button type="button" class="admin-sidebar-toggle admin-ref-topbar__menu" aria-label="{{ __('Toggle navigation') }}" aria-controls="admin-sidebar" aria-expanded="true">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
        <a href="{{ route('admin.dashboard') }}" class="admin-ref-topbar__brand" aria-label="{{ config('app.name', 'MOROCCO 2030') }} Admin">
            <img src="{{ asset('assets/brand/logo.svg') }}" alt="" width="40" height="40" decoding="async">
        </a>
    </div>

    <div class="admin-ref-topbar__right admin-topbar__actions">
        <div class="admin-search admin-ref-search admin-topbar-search" role="search">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="m16.5 16.5 4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <input
                type="search"
                id="admin-topbar-search-input"
                class="admin-topbar-search__input"
                placeholder="{{ __('admin.topbar.search_placeholder') }}"
                aria-label="{{ __('admin.topbar.search_placeholder') }}"
                aria-controls="admin-topbar-search-panel"
                aria-expanded="false"
                autocomplete="off"
                spellcheck="false"
            >
            <div
                id="admin-topbar-search-panel"
                class="admin-topbar-search__panel admin-topbar-dropdown admin-topbar-command"
                role="listbox"
                aria-label="{{ __('admin.topbar.search_sections') }}"
                data-empty-message="{{ __('admin.topbar.no_section_found') }}"
                data-section-label="{{ __('admin.topbar.search_section_hint') }}"
                hidden
            ></div>
        </div>
        <script type="application/json" id="admin-topbar-search-data">@json($adminSearchSections->values())</script>

        <div class="admin-topbar-action admin-topbar-action--notifications">
            <button
                type="button"
                id="admin-notifications-toggle"
                class="admin-ref-topbar__icon-btn admin-topbar-action__btn"
                aria-label="{{ __('admin.topbar.notifications') }}"
                aria-controls="admin-notification-menu"
                aria-expanded="false"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4a5 5 0 0 1 5 5v3.5l1.5 2.5H5.5L7 12.5V9a5 5 0 0 1 5-5Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M10 18a2 2 0 0 0 4 0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                @if ($notificationBadgeCount > 0)
                    <span class="admin-ref-topbar__badge admin-ref-topbar__badge--subtle">{{ $notificationBadgeCount > 9 ? '9+' : $notificationBadgeCount }}</span>
                @endif
            </button>
            <div id="admin-notification-menu" class="admin-topbar-dropdown admin-notification-menu" role="menu" aria-label="{{ __('admin.topbar.notifications') }}" hidden>
                <div class="admin-topbar-dropdown__head admin-notification-menu__head">
                    <strong>{{ __('admin.topbar.notifications') }}</strong>
                    @if ($actionableNotificationCount > 0)
                        <span class="admin-notification-menu__summary">{{ trans_choice('admin.topbar.notif_action_summary', $actionableNotificationCount, ['count' => $actionableNotificationCount]) }}</span>
                    @else
                        <span class="admin-notification-menu__summary">{{ __('admin.topbar.no_urgent_alerts') }}</span>
                    @endif
                </div>
                <ul class="admin-notification-menu__list">
                    @foreach ($adminNotifications as $notification)
                        <li>
                            @if (! empty($notification['url']))
                                <a href="{{ $notification['url'] }}" @class([
                                    'admin-notification-menu__item',
                                    'admin-notification-menu__item--link',
                                    'admin-notification-menu__item--audit' => ($notification['kind'] ?? '') === 'audit',
                                ]) role="menuitem">
                            @else
                                <div class="admin-notification-menu__item" role="menuitem">
                            @endif
                                <span class="admin-notification-menu__dot admin-notification-menu__dot--{{ $notification['dot'] }}" aria-hidden="true"></span>
                                <div>
                                    <strong>{{ $notification['title'] }}</strong>
                                    <span>{{ $notification['description'] }}</span>
                                    @if (! empty($notification['time']))
                                        <span class="admin-notification-menu__time">{{ $notification['time'] }}</span>
                                    @endif
                                </div>
                            @if (! empty($notification['url']))
                                </a>
                            @else
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="admin-topbar-action admin-topbar-action--language">
            <button
                type="button"
                id="admin-language-toggle"
                class="admin-ref-topbar__lang admin-topbar-action__btn"
                aria-label="{{ __('admin.topbar.language') }}"
                aria-controls="admin-language-menu"
                aria-expanded="false"
                @disabled(! $canSwitchLanguage)
            >
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
                <span id="admin-topbar-language-label">{{ $topbarLanguageCode }}</span>
            </button>
            @if ($canSwitchLanguage)
                <div id="admin-language-menu" class="admin-topbar-dropdown admin-language-menu" role="menu" aria-label="{{ __('admin.topbar.choose_language') }}" hidden>
                    <div class="admin-topbar-dropdown__head">
                        <strong>{{ __('admin.topbar.language') }}</strong>
                    </div>
                    <ul class="admin-language-menu__list">
                        @foreach ($activeLanguages as $language)
                            <li>
                                <a
                                    href="{{ route('language.switch', $language->code) }}"
                                    role="menuitem"
                                    lang="{{ str_replace('_', '-', $language->locale) }}"
                                    @class([
                                        'admin-language-menu__item',
                                        'is-active' => $adminCurrentLanguage && $adminCurrentLanguage->is($language),
                                    ])
                                >
                                    <span>{{ strtoupper($language->code) }}</span>
                                    <strong>{{ $language->native_name ?: $language->name }}</strong>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="admin-topbar-action admin-topbar-action--user admin-ref-topbar__user">
            <button
                type="button"
                id="admin-user-menu-toggle"
                class="admin-ref-topbar__avatar admin-topbar-action__btn"
                aria-label="{{ __('admin.topbar.account_menu') }}"
                aria-controls="admin-user-menu"
                aria-expanded="false"
            >
                {{ strtoupper(mb_substr($authUser?->name ?? 'A', 0, 1)) }}
            </button>
            <div id="admin-user-menu" class="admin-topbar-dropdown admin-user-menu" role="menu" aria-label="{{ __('admin.topbar.account_menu') }}" hidden>
                <div class="admin-user-menu__head">
                    <strong>{{ $authUser?->name ?? __('admin.topbar.administrator') }}</strong>
                    <span>{{ $authUser?->roles->first()?->name ?? __('admin.topbar.staff_account') }}</span>
                </div>
                <ul class="admin-user-menu__list">
                    @if ($canViewProfile)
                        <li>
                            <a href="{{ route('admin.users.show', $authUser) }}" role="menuitem">{{ __('admin.topbar.profile') }}</a>
                        </li>
                    @endif
                    @if ($canViewSettings)
                        <li>
                            <a href="{{ route('admin.settings.index') }}" role="menuitem">{{ __('admin.topbar.settings') }}</a>
                        </li>
                    @endif
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}" class="admin-user-menu__logout">
                            @csrf
                            <button type="submit" role="menuitem">{{ __('admin.topbar.logout') }}</button>
                        </form>
                    </li>
                </ul>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" class="admin-ref-topbar__logout">
                @csrf
                <button type="submit" class="btn btn-secondary">{{ __('admin.topbar.logout') }}</button>
            </form>
        </div>
    </div>
</header>

@if (! request()->routeIs('admin.dashboard') && ! empty($pageTitle))
    <div class="admin-ref-page-head">
        <h1>{{ $pageTitle }}</h1>
        @isset($pageDescription)
            <p>{{ $pageDescription }}</p>
        @endisset
    </div>
@endif
