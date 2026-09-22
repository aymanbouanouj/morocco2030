@extends('public.layouts.app')

@section('title', __('Reset Password').' | '.__('Morocco 2030'))

@section('content')
    @include('public.partials.page-header', [
        'eyebrow' => __('Account'),
        'title' => __('Reset Your Password'),
        'summary' => __('Request a password reset link for an eligible Morocco 2030 account.'),
    ])

    <section class="page-section auth-shell">
        <div class="auth-grid">
            <div class="panel auth-card">
                <h2>{{ __('Send a reset link') }}</h2>
                <p>{{ __('Enter the email address used for your account and we will send a secure reset link if the account is eligible.') }}</p>

                <form method="POST" action="{{ route('password.email') }}" class="auth-form">
                    @csrf

                    <div class="field-group">
                        <label for="email">{{ __('Email address') }}</label>
                        <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="button">{{ __('Email Reset Link') }}</button>
                        <a href="{{ route('login') }}" class="button--subtle">{{ __('Back to sign in') }}</a>
                    </div>
                </form>
            </div>

            <aside class="panel auth-card auth-note">
                <h2>{{ __('Account support') }}</h2>
                <p>{{ __('Public accounts can reset passwords here. Internal accounts remain managed through controlled staff provisioning workflows.') }}</p>
            </aside>
        </div>
    </section>
@endsection
