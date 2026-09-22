<div class="panel account-panel account-menu-card">
    <span class="account-kicker">{{ __('Public Account') }}</span>
    <h2>{{ __('My Account') }}</h2>
    <p>{{ __('Manage your public profile, saved items, notifications, and account preferences.') }}</p>

    <nav class="account-nav" aria-label="{{ __('Account navigation') }}">
        <a href="{{ route('account.index') }}" @class(['account-nav__link', 'is-active' => request()->routeIs('account.index')]) @if (request()->routeIs('account.index')) aria-current="page" @endif>
            <span>{{ __('Overview') }}</span>
        </a>
        <a href="{{ route('account.profile') }}" @class(['account-nav__link', 'is-active' => request()->routeIs('account.profile')]) @if (request()->routeIs('account.profile')) aria-current="page" @endif>
            <span>{{ __('Profile') }}</span>
        </a>
        <a href="{{ route('account.settings') }}" @class(['account-nav__link', 'is-active' => request()->routeIs('account.settings')]) @if (request()->routeIs('account.settings')) aria-current="page" @endif>
            <span>{{ __('Settings') }}</span>
        </a>
        <a href="{{ route('account.favorites') }}" @class(['account-nav__link', 'is-active' => request()->routeIs('account.favorites')]) @if (request()->routeIs('account.favorites')) aria-current="page" @endif>
            <span>{{ __('Favorites') }}</span>
        </a>
        <a href="{{ route('account.notifications') }}" @class(['account-nav__link', 'is-active' => request()->routeIs('account.notifications')]) @if (request()->routeIs('account.notifications')) aria-current="page" @endif>
            <span>{{ __('Notifications') }}</span>
        </a>
    </nav>
</div>
