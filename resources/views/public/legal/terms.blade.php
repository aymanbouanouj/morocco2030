@extends('public.layouts.app')

@section('title', __('Terms Of Use').' | '.__('Morocco 2030'))
@section('meta_description', __('Usage notice for the MOROCCO 2030 public tournament portal.'))

@section('content')
    @include('public.partials.page-header', [
        'eyebrow' => __('Usage Notice'),
        'title' => __('Terms Of Use'),
        'summary' => __('General rules for using the MOROCCO 2030 public information portal and account features.'),
    ])

    <section class="page-section">
        <div class="section-shell page-stack">
            <article class="panel">
                <h2 class="section-title">{{ __('Informational Platform') }}</h2>
                <p>{{ __('MOROCCO 2030 is provided as an informational tournament portal and administration project for fixtures, results, standings, news, teams, host cities, stadiums, partners, maps, and account features.') }}</p>
                <p>{{ __('Content may be updated, corrected, expanded, or removed as tournament information and demonstration data changes.') }}</p>
            </article>

            <article class="panel">
                <h2 class="section-title">{{ __('Responsible Use') }}</h2>
                <p>{{ __('Users should use account, search, contact, and public browsing features responsibly. Abuse, spam, unauthorized access attempts, automated scraping, or attempts to bypass access controls are not permitted.') }}</p>
            </article>

            <article class="panel">
                <h2 class="section-title">{{ __('Accounts And Access') }}</h2>
                <p>{{ __('Public accounts are for public-facing features only. Staff and admin access is restricted to authorized internal users and is protected by authentication, roles, permissions, and policies.') }}</p>
            </article>

            <article class="panel">
                <h2 class="section-title">{{ __('No Official Affiliation Claim') }}</h2>
                <p>{{ __('Unless explicitly confirmed by the project operator, this platform should not be interpreted as an official FIFA, government, ticketing, accreditation, payment, or legal service.') }}</p>
            </article>

            <article class="panel">
                <h2 class="section-title">{{ __('Privacy') }}</h2>
                <p>{{ __('Use of this platform is also subject to the public privacy notice, which explains analytics, sessions, contact messages, accounts, audit logs, and data-retention planning.') }}</p>
                <p><a href="{{ route('public.privacy') }}" class="button button--secondary">{{ __('Read Privacy Notice') }}</a></p>
            </article>
        </div>
    </section>
@endsection
