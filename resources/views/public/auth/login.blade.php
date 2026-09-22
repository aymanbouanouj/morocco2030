@extends('public.layouts.app')

@section('title', __('Sign In').' | '.__('Morocco 2030'))

@section('content')
    @include('public.partials.page-header', [
        'eyebrow' => __('Account'),
        'title' => __('Sign In'),
        'summary' => __('Sign in with your Morocco 2030 account. Your destination is selected automatically after authentication.'),
    ])

    <section class="page-section auth-shell">
        <div class="auth-grid">
            <div class="panel auth-card">
                <h2>{{ __('Welcome back') }}</h2>
                <p>{{ __('Use the email address and password connected to your Morocco 2030 account.') }}</p>

                <form method="POST" action="{{ route('login.store') }}" class="auth-form">
                    @csrf

                    <div class="field-group">
                        <label for="email">{{ __('Email address') }}</label>
                        <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="password">{{ __('Password') }}</label>
                        <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password">
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <label class="form-helper">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        {{ __('Keep me signed in on this device') }}
                    </label>

                    <div class="form-actions">
                        <button type="submit" class="button">{{ __('Sign In') }}</button>
                        <a href="{{ route('password.request') }}" class="button--subtle">{{ __('Forgot your password?') }}</a>
                    </div>

                    <div class="form-helper">
                        {{ __('New here?') }}
                        <a href="{{ route('register') }}" class="section-link">{{ __('Create your account') }}</a>
                    </div>
                </form>
            </div>

            <aside class="panel auth-card auth-note">
                <h2>{{ __('One secure sign in') }}</h2>
                <p>{{ __('Supporters continue to their account area. Internal staff accounts are routed to the administrative dashboard automatically.') }}</p>

                <ul class="auth-note__list">
                    <li>
                        <strong>{{ __('Save your favorites') }}</strong>
                        <span>{{ __('Keep teams, players, cities, and fixtures close at hand.') }}</span>
                    </li>
                    <li>
                        <strong>{{ __('Manage your profile') }}</strong>
                        <span>{{ __('Update your contact details and preferred language settings.') }}</span>
                    </li>
                    <li>
                        <strong>{{ __('Automatic routing') }}</strong>
                        <span>{{ __('The platform checks your account type and sends you to the right destination.') }}</span>
                    </li>
                </ul>
            </aside>
        </div>
    </section>
@endsection
