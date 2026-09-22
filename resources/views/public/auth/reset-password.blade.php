@extends('public.layouts.app')

@section('title', __('Choose A New Password').' | '.__('Morocco 2030'))

@section('content')
    @include('public.partials.page-header', [
        'eyebrow' => __('Account'),
        'title' => __('Choose A New Password'),
        'summary' => __('Set a new password for your Morocco 2030 account.'),
    ])

    <section class="page-section auth-shell">
        <div class="auth-grid">
            <div class="panel auth-card">
                <h2>{{ __('Create your new password') }}</h2>
                <p>{{ __('Use a strong password that you do not use elsewhere.') }}</p>

                <form method="POST" action="{{ route('password.update') }}" class="auth-form">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="field-group">
                        <label for="email">{{ __('Email address') }}</label>
                        <input id="email" class="form-control" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email">
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-grid-2">
                        <div class="field-group">
                            <label for="password">{{ __('New password') }}</label>
                            <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password">
                            @error('password')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-group">
                            <label for="password_confirmation">{{ __('Confirm new password') }}</label>
                            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="button">{{ __('Reset Password') }}</button>
                    </div>
                </form>
            </div>

            <aside class="panel auth-card auth-note">
                <h2>{{ __('Secure account recovery') }}</h2>
                <p>{{ __('Use the reset link issued to your account email, then sign in from the shared Morocco 2030 login page.') }}</p>
            </aside>
        </div>
    </section>
@endsection
