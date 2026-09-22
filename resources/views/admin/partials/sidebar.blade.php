<aside id="admin-sidebar" class="admin-sidebar admin-ref-sidebar" aria-label="{{ __('Admin navigation') }}">
    <div class="admin-sidebar__brand admin-ref-sidebar-logo">
        <img
            class="admin-sidebar__logo"
            src="{{ asset('assets/brand/logo.svg') }}"
            alt="{{ config('app.name', 'MOROCCO 2030') }}"
            width="128"
            height="auto"
            decoding="async"
        >
    </div>

    <nav class="admin-sidebar__nav admin-ref-nav" aria-label="{{ __('Admin modules') }}">
        <a href="{{ route('admin.dashboard') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.dashboard')])>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
            <span>Dashboard</span>
        </a>

        @if(auth()->user()?->hasAnyRole(['super-admin', 'platform-admin']))
            <a href="{{ route('admin.football-data-import.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.football-data-import.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v12H4z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M8 10h8M8 14h5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>Football Data Import</span>
            </a>
        @endif

        @can('viewAny', \App\Models\User::class)
            <a href="{{ route('admin.users.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M4 20a8 8 0 0 1 16 0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>Users &amp; Roles</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\News::class)
            <a href="{{ route('admin.news.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.news.*') && ! request()->routeIs('admin.news-categories.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4h12v16H6z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M8 8h8M8 12h8M8 16h5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>News</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\NewsCategory::class)
            <a href="{{ route('admin.news-categories.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.news-categories.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4h12v16H6z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M8 8h8M8 12h5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>News Categories</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\Team::class)
            <a href="{{ route('admin.teams.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.teams.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="9" r="3.5" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="16.5" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
                <span>Teams</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\Player::class)
            <a href="{{ route('admin.players.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.players.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v18M8 7h8M8 17h8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>Players</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\MatchFixture::class)
            <a href="{{ route('admin.matches.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.matches.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4.5" width="18" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M8 3v3M16 3v3M3 10h18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>Matches</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\Group::class)
            <a href="{{ route('admin.groups.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.groups.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>Standings</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\City::class)
            <a href="{{ route('admin.cities.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.cities.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 5 10v9h6v-5h2v5h6v-9z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                <span>Cities</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\Stadium::class)
            <a href="{{ route('admin.stadiums.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.stadiums.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20 12 4l8 16H4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                <span>Stadiums</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\Partner::class)
            <a href="{{ route('admin.partners.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.partners.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h10v10H7z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M4 12h3M17 12h3M12 4v3M12 17v3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>Partners</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\MediaFile::class)
            <a href="{{ route('admin.media-files.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.media-files.*') || request()->routeIs('admin.media-readiness.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="9" cy="10" r="1.6" fill="currentColor"/><path d="m4 16 5-5 4 4 3-3 4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                <span>Media Review</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\InterfaceTranslation::class)
            <a href="{{ route('admin.interface-translations.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.interface-translations.*') || request()->routeIs('admin.languages.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
                <span>Translations</span>
            </a>
        @endcan

        @if(auth()->user()?->hasPermission('analytics.view'))
            <a href="{{ route('admin.dashboard') }}#admin-analytics" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.dashboard') && request()->has('analytics')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 18V6M10 18V10M16 18v-6M22 18V4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>Analytics</span>
            </a>
        @endif

        @can('viewAny', \App\Models\AuditLog::class)
            <a href="{{ route('admin.audit-logs.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.audit-logs.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8v5l3 2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="12" r="8" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
                <span>Audit Logs</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\ContactMessage::class)
            <a href="{{ route('admin.contact-messages.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.contact-messages.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v12H4z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="m4 7 8 6 8-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                <span>Contact Messages</span>
            </a>
        @endcan

        @can('viewAny', \App\Models\Setting::class)
            <a href="{{ route('admin.settings.index') }}" @class(['admin-sidebar__link', 'admin-sidebar__item', 'admin-ref-nav-item', 'is-active' => request()->routeIs('admin.settings.*')])>
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <span>Settings</span>
            </a>
        @endcan
    </nav>

    @can('view', auth()->user())
        <a href="{{ route('admin.users.show', auth()->user()) }}" class="admin-sidebar__user admin-ref-sidebar-user">
    @else
        <div class="admin-sidebar__user admin-ref-sidebar-user">
    @endcan
        <span class="admin-ref-sidebar-user__avatar" aria-hidden="true">{{ strtoupper(mb_substr(auth()->user()?->name ?? 'A', 0, 1)) }}</span>
        <div class="admin-ref-sidebar-user__copy">
            <strong>{{ auth()->user()?->name ?? 'Administrator' }}</strong>
            <span>{{ auth()->user()?->roles->first()?->name ?? 'Staff Account' }}</span>
        </div>
        <svg class="admin-ref-sidebar-user__chev" viewBox="0 0 24 24" aria-hidden="true"><path d="m7 10 5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
    @can('view', auth()->user())
        </a>
    @else
        </div>
    @endcan
</aside>
