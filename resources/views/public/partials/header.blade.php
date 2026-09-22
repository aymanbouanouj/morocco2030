@php
    $publicNavItems = [
        ['label' => __('Home'), 'url' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => __('News'), 'url' => route('news.index'), 'active' => request()->routeIs('news.*')],
        ['label' => __('Fixtures'), 'url' => route('matches.index'), 'active' => request()->routeIs('matches.*') || request()->routeIs('results.*')],
        ['label' => __('Tables'), 'url' => route('standings.index'), 'active' => request()->routeIs('standings.*')],
        ['label' => __('Bracket'), 'url' => route('knockout.index'), 'active' => request()->routeIs('knockout.*')],
        ['label' => __('Teams & Players'), 'url' => route('teams.index'), 'active' => request()->routeIs('teams.*') || request()->routeIs('players.*')],
        ['label' => __('Host Cities'), 'url' => route('cities.index'), 'active' => request()->routeIs('cities.*') || request()->routeIs('stadiums.*')],
        ['label' => __('Partners'), 'url' => route('partners.index'), 'active' => request()->routeIs('partners.*')],
    ];
@endphp

<header class="site-header" data-public-shell role="banner">
    <div class="site-header__inner">
        <a href="{{ route('home') }}" class="brand" aria-label="{{ config('app.name', 'MOROCCO 2030') }} - {{ __('Tournament Portal home') }}">
            <img
                class="brand__logo brand__logo--horizontal"
                src="{{ asset('assets/brand/logo.svg') }}"
                alt="MOROCCO 2030"
                width="58"
                height="58"
                decoding="async"
            >
            <img
                class="brand__logo brand__logo--stacked"
                src="{{ asset('assets/brand/logo.svg') }}"
                alt="MOROCCO 2030"
                width="56"
                height="56"
                decoding="async"
            >
        </a>

        <nav class="site-nav site-nav--desktop" aria-label="{{ __('Primary navigation') }}">
            @foreach ($publicNavItems as $item)
                <a
                    href="{{ $item['url'] }}"
                    @class(['is-active' => $item['active']])
                    @if ($item['active']) aria-current="page" @endif
                >{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="site-header__utility-tools" role="group" aria-label="{{ __('Public portal tools') }}">
            <button
                type="button"
                class="shell-icon-button"
                data-search-open
                aria-controls="public-search-overlay"
                aria-expanded="false"
                aria-label="{{ __('Open search') }}"
            >
                <svg class="shell-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M10.8 18.1a7.3 7.3 0 1 1 5.1-2.1l4.1 4.1-1.5 1.5-4.1-4.1a7.2 7.2 0 0 1-3.6.6Zm0-2.1a5.2 5.2 0 1 0 0-10.4 5.2 5.2 0 0 0 0 10.4Z" />
                </svg>
                <span class="sr-only">{{ __('Search') }}</span>
            </button>

            <a
                href="{{ route('map.index') }}"
                @class(['shell-icon-button', 'header-map-action', 'is-active' => request()->routeIs('map.*')])
                aria-label="{{ __('Open host map') }}"
            >
                <svg class="shell-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="m9 18.7-6 2.1V5.2l6-2.1 6 2.2 6-2.1v15.6l-6 2.1-6-2.2Zm1-2 4 1.5V7.2l-4-1.5v11Zm-2 0V5.8L5 6.9V18l3-1.3Zm8 1.5 3-1.1V6l-3 1.1v11.1Z" />
                </svg>
                <span class="sr-only">{{ __('Host Map') }}</span>
            </a>

            @if (($publicLanguages ?? collect())->isNotEmpty())
                <button
                    type="button"
                    class="shell-icon-button shell-icon-button--language"
                    data-shell-toggle="language"
                    aria-controls="public-language-panel"
                    aria-expanded="false"
                    aria-label="{{ __('Language switcher') }}"
                >
                    <svg class="shell-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20Zm6.9-9h-3.1a14 14 0 0 1-1.3 5.2A8.1 8.1 0 0 0 18.9 13ZM8.2 13H5.1a8.1 8.1 0 0 0 4.4 5.2A14 14 0 0 1 8.2 13Zm0-2a14 14 0 0 1 1.3-5.2A8.1 8.1 0 0 0 5.1 11h3.1Zm2 0h3.6C13.5 6.8 12.3 4.4 12 4.4S10.5 6.8 10.2 11Zm3.6 2h-3.6c.3 4.2 1.5 6.6 1.8 6.6s1.5-2.4 1.8-6.6Zm.7-7.2a14 14 0 0 1 1.3 5.2h3.1a8.1 8.1 0 0 0-4.4-5.2Z" />
                    </svg>
                    <span class="shell-current-language">{{ strtoupper($publicCurrentLanguage?->code ?? app()->getLocale()) }}</span>
                </button>
            @endif

            <span class="site-header__divider" aria-hidden="true"></span>

            @auth
                @if (request()->user()->canAccessPublicAccount())
                    <button
                        type="button"
                        class="header-auth__account"
                        data-shell-toggle="account"
                        aria-controls="public-account-panel"
                        aria-expanded="false"
                        aria-label="{{ __('Account menu') }}"
                    >
                        <span class="header-auth__account-mark" aria-hidden="true">{{ str(request()->user()->name ?? 'M30')->substr(0, 1)->upper() }}</span>
                        <span class="header-auth__account-name" title="{{ request()->user()->name }}">{{ \Illuminate\Support\Str::limit(request()->user()->name, 18, '...') }}</span>
                    </button>
                @elseif (request()->user()->canAccessAdmin())
                    <button
                        type="button"
                        class="header-auth__account"
                        data-shell-toggle="account"
                        aria-controls="public-account-panel"
                        aria-expanded="false"
                        aria-label="{{ __('Account menu') }}"
                    >
                        <span class="header-auth__account-mark" aria-hidden="true">{{ str(request()->user()->name ?? 'M30')->substr(0, 1)->upper() }}</span>
                        @php($adminNavLabel = __('Admin'))
                        <span class="header-auth__account-name">{{ is_string($adminNavLabel) ? $adminNavLabel : 'Admin' }}</span>
                    </button>
                @endif
            @else
                <button
                    type="button"
                    class="header-auth__trigger"
                    data-shell-toggle="account"
                    aria-controls="public-account-panel"
                    aria-expanded="false"
                    aria-label="{{ __('Login and account options') }}"
                >
                    <svg class="shell-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 12a4.8 4.8 0 1 1 0-9.6 4.8 4.8 0 0 1 0 9.6Zm0-2a2.8 2.8 0 1 0 0-5.6A2.8 2.8 0 0 0 12 10Zm8.8 11H3.2v-1a7.8 7.8 0 0 1 15.6 0v1Zm-15.4-2h13.2a5.8 5.8 0 0 0-13.2 0Z" />
                    </svg>
                    <span class="header-auth__trigger-text">{{ __('Login') }}</span>
                </button>
            @endauth

            <button
                type="button"
                class="shell-icon-button shell-menu-button"
                data-shell-toggle="menu"
                aria-controls="public-mobile-menu"
                aria-expanded="false"
                aria-label="{{ __('Open main menu') }}"
            >
                <svg class="shell-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 6.5h16v2H4v-2Zm0 4.5h16v2H4v-2Zm0 4.5h16v2H4v-2Z" />
                </svg>
                <span class="sr-only">{{ __('Menu') }}</span>
            </button>
        </div>
    </div>

    <div class="shell-popovers">
        @if (($publicLanguages ?? collect())->isNotEmpty())
            <div id="public-language-panel" class="shell-panel shell-panel--language" data-shell-panel="language" hidden>
                <span class="shell-panel__label">{{ __('Choose language') }}</span>
                <div class="shell-language-list">
                    @foreach ($publicLanguages as $language)
                        <a
                            href="{{ route('language.switch', $language->code) }}"
                            lang="{{ str_replace('_', '-', $language->locale) }}"
                            hreflang="{{ str_replace('_', '-', $language->locale) }}"
                            title="{{ $language->native_name }}"
                            aria-label="{{ $language->name }}"
                            @class([
                                'shell-language-list__item',
                                'is-active' => ($publicCurrentLanguage?->id === $language->id),
                            ])
                        >
                            <span>{{ strtoupper($language->code) }}</span>
                            <strong>{{ $language->native_name }}</strong>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div id="public-account-panel" class="shell-panel shell-panel--account" data-shell-panel="account" hidden>
            <span class="shell-panel__label">{{ __('Account') }}</span>
            @auth
                @if (request()->user()->canAccessPublicAccount())
                    <a href="{{ route('account.index') }}" class="shell-menu-link">{{ __('My Account') }}</a>
                    <a href="{{ route('account.profile') }}" class="shell-menu-link">{{ __('Profile') }}</a>
                    <form action="{{ route('logout') }}" method="POST" class="shell-menu-form">
                        @csrf
                        <button type="submit" class="shell-menu-link shell-menu-link--button">{{ __('Sign Out') }}</button>
                    </form>
                @elseif (request()->user()->canAccessAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="shell-menu-link">{{ __('Admin Dashboard') }}</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="shell-menu-link shell-menu-link--primary">{{ __('Sign in') }}</a>
                <a href="{{ route('register') }}" class="shell-menu-link shell-menu-link--secondary">{{ __('Create account') }}</a>
            @endauth
        </div>

        <div id="public-mobile-menu" class="shell-panel shell-panel--drawer" data-shell-panel="menu" hidden>
            <div class="shell-drawer__head">
                <span>{{ __('Tournament Menu') }}</span>
                <button type="button" class="shell-drawer__close" data-shell-close>{{ __('Close') }}</button>
            </div>
            <nav class="shell-drawer__nav" aria-label="{{ __('Mobile navigation') }}">
                @foreach ($publicNavItems as $item)
                    <a href="{{ $item['url'] }}" @class(['is-active' => $item['active']])>{{ $item['label'] }}</a>
                @endforeach
            </nav>
            <div class="shell-drawer__actions">
                <a href="{{ route('search.index') }}">{{ __('Search') }}</a>
                <a href="{{ route('map.index') }}" @class(['is-active' => request()->routeIs('map.*')])>{{ __('Host Map') }}</a>
                @guest
                    <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
                    <a href="{{ route('register') }}">{{ __('Create account') }}</a>
                @endguest
            </div>
        </div>
    </div>

    <div
        id="public-search-overlay"
        class="search-overlay"
        data-search-overlay
        hidden
        aria-hidden="true"
        role="dialog"
        aria-modal="true"
        aria-labelledby="search-overlay-label"
    >
        <div class="search-overlay__backdrop" data-search-close tabindex="-1" aria-hidden="true"></div>
        <div class="search-overlay__panel" data-search-panel>
            <div class="search-overlay__header">
                <p id="search-overlay-label" class="search-overlay__label">
                    {{ __('Search') }} {{ config('app.name', 'MOROCCO 2030') }}
                </p>
                <button
                    type="button"
                    class="search-overlay__close"
                    data-search-close
                    aria-label="{{ __('Close search') }}"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path d="M6.4 6.4 17.6 17.6M17.6 6.4 6.4 17.6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('search.index') }}" method="GET" class="search-overlay__form">
                <label class="sr-only" for="search-overlay-input">{{ __('Search') }}</label>
                <input
                    id="search-overlay-input"
                    class="search-overlay__input"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    data-search-input
                    placeholder="{{ __('Search teams, matches, cities, stadiums, news...') }}"
                    autocomplete="off"
                >
                <button type="submit" class="search-overlay__submit">{{ __('Search') }}</button>
            </form>
            <nav class="search-overlay__quick" aria-label="{{ __('Quick search links') }}">
                <a href="{{ route('matches.index') }}" class="search-overlay__quick-link">{{ __('Fixtures') }}</a>
                <a href="{{ route('teams.index') }}" class="search-overlay__quick-link">{{ __('Teams') }}</a>
                <a href="{{ route('cities.index') }}" class="search-overlay__quick-link">{{ __('Host Cities') }}</a>
                <a href="{{ route('stadiums.index') }}" class="search-overlay__quick-link">{{ __('Stadiums') }}</a>
                <a href="{{ route('news.index') }}" class="search-overlay__quick-link">{{ __('News') }}</a>
                <a href="{{ route('map.index') }}" class="search-overlay__quick-link">{{ __('Host Map') }}</a>
            </nav>
        </div>
    </div>
</header>
