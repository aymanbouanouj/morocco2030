@php($htmlLocale = $publicCurrentLanguage?->locale ?? app()->getLocale())
@php($htmlDirection = $publicCurrentLanguage?->direction ?? (app()->getLocale() === 'ar' ? 'rtl' : 'ltr'))
@php($publicProductName = config('app.name', 'MOROCCO 2030'))
@php($publicRawTitle = trim($__env->yieldContent('title', $publicProductName)))
@php($publicPageTitle = str_replace([__('Morocco 2030'), 'Morocco 2030', 'Morocco2030', 'Moroccoo2030'], $publicProductName, $publicRawTitle))
@php($publicMetaDescription = trim($__env->yieldContent('meta_description', str_replace('Morocco 2030', $publicProductName, __('Official fixtures, results, news, and host information for Morocco 2030.')))))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $htmlLocale) }}" dir="{{ $htmlDirection }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#b10f2e">
    <meta name="color-scheme" content="light">
    <title>{{ $publicPageTitle }}</title>
    <meta name="description" content="{{ $publicMetaDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $publicPageTitle }}">
    <meta property="og:description" content="{{ $publicMetaDescription }}">
    <meta property="og:locale" content="{{ $htmlLocale }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/brand/favicon.svg') }}">
    <style>
        @include('public.layouts.styles')
        @include('public.layouts.phase3-batch1-styles')
    </style>
    @stack('head')
</head>
<body @class([
    request()->routeIs('home') ? 'page-home' : 'page-inner',
    'page-account' => request()->routeIs('account.*'),
    $htmlDirection === 'rtl' ? 'is-rtl' : 'is-ltr',
    'locale-'.app()->getLocale(),
])>
    <a href="#main-content" class="skip-link">{{ __('Skip to main content') }}</a>

    @include('public.partials.header')

    <main id="main-content" tabindex="-1">
        <div class="page-container">
            @include('public.partials.flash')
            @yield('content')
        </div>
    </main>

    @include('public.partials.footer')
    @include('public.layouts.interactions')
    @stack('scripts')
</body>
</html>
