@extends('public.layouts.app')

@section('title', __('Privacy Notice').' | '.__('Morocco 2030'))
@section('meta_description', __('Privacy notice for MOROCCO 2030 visitors, account users, and staff activity records.'))

@section('content')
    @include('public.partials.page-header', [
        'eyebrow' => __('Data Protection'),
        'title' => __('Privacy Notice'),
        'summary' => __('How MOROCCO 2030 handles visitor analytics, account data, contact messages, sessions, and administrative audit records.'),
    ])

    <section class="page-section">
        <div class="section-shell page-stack">
            <article class="panel">
                <h2 class="section-title">{{ __('Purpose Of This Platform') }}</h2>
                <p>{{ __('MOROCCO 2030 is an informational tournament portal and administration platform for fixtures, results, standings, news, teams, venues, partners, maps, public accounts, and staff operations.') }}</p>
                <p>{{ __('This notice explains the current engineering privacy posture. It is not a final legal policy and does not claim full legal compliance without review by the project operator and qualified legal counsel.') }}</p>
            </article>

            <article class="panel">
                <h2 class="section-title">{{ __('Visitor Analytics') }}</h2>
                <p>{{ __('The platform records successful public page views to understand which pages are used and to support the admin analytics dashboard. Admin pages, login, registration, password reset, logout, assets, storage, build, vendor, health, favicon, and robots paths are excluded from public visitor analytics.') }}</p>
                <p>{{ __('Analytics records may include the page path, route name, referrer, language, device type, event time, user agent, and limited technical metadata. When a public account user views public/account pages, the account identifier may be recorded where supported by the database schema.') }}</p>
                <p>{{ __('Raw IP addresses are not stored in visitor analytics. The system stores a pseudonymous hash of the IP address so analytics can remain useful without saving the raw IP value.') }}</p>
            </article>

            <article class="panel">
                <h2 class="section-title">{{ __('Cookies And Sessions') }}</h2>
                <p>{{ __('The site uses Laravel session cookies to keep users signed in and to protect forms. Session cookies are required for authentication, account pages, staff access, and CSRF protection.') }}</p>
                <p>{{ __('A production deployment should use HTTPS and secure cookie settings for the deployed domain.') }}</p>
            </article>

            <article class="panel">
                <h2 class="section-title">{{ __('Contact Messages') }}</h2>
                <p>{{ __('If contact message records are used, they may include the sender name, email address, phone number, subject, message, language, source, status, assigned staff user, internal notes, response time, and related technical metadata.') }}</p>
                <p>{{ __('Contact messages are intended for authorized staff triage only. Internal notes are not public content.') }}</p>
            </article>

            <article class="panel">
                <h2 class="section-title">{{ __('Public Accounts') }}</h2>
                <p>{{ __('Public account records may include name, email address, password hash, locale preference, account status, profile/settings data, favorites, and notifications where enabled.') }}</p>
                <p>{{ __('Public registration creates public users only. Staff accounts must be created through controlled internal workflows and remain protected by admin access rules.') }}</p>
            </article>

            <article class="panel">
                <h2 class="section-title">{{ __('Administrative Audit Logs') }}</h2>
                <p>{{ __('Authorized staff operations may be recorded in audit logs for security and accountability. Audit records can include the actor, affected record, action, route name, user agent, IP address, timestamp, and redacted old/new values.') }}</p>
                <p>{{ __('Sensitive values such as passwords, tokens, API keys, APP_KEY, DB_PASSWORD, remember tokens, and secrets are masked before display in the audit log interface. The audit log module is read-only for authorized staff.') }}</p>
            </article>

            <article class="panel">
                <h2 class="section-title">{{ __('Retention Summary') }}</h2>
                <p>{{ __('The project has an engineering retention plan for analytics, audit logs, contact messages, accounts, future media, application logs, and backups. Automated deletion is not enabled in this phase.') }}</p>
                <p>{{ __('A production operator must approve retention periods before launch and test backups and restore procedures before deleting or pruning data.') }}</p>
            </article>

            <article class="panel">
                <h2 class="section-title">{{ __('Rights, Legal Review, And Contact') }}</h2>
                <p>{{ __('A real deployment should publish operator contact details and explain how users can request access, correction, deletion, or review of their personal data where applicable.') }}</p>
                <p>{{ __('Morocco has a personal-data protection framework associated with the CNDP and Law 09-08. This page provides platform transparency only and does not claim full legal compliance without formal legal review.') }}</p>
            </article>
        </div>
    </section>
@endsection
