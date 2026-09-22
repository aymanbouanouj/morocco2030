@extends('public.layouts.app')

@section('title', __('Create Account').' | '.__('Morocco 2030'))

@section('content')
    @include('public.partials.page-header', [
        'eyebrow' => __('Account'),
        'title' => __('Create Your Account'),
        'summary' => __('Join the public Morocco 2030 experience to save favorites, follow updates, and manage your personal settings.'),
    ])

    <section class="page-section auth-shell">
        <div class="auth-grid">
            <div class="panel auth-card">
                <h2>{{ __('Set up your public account') }}</h2>
                <p>{{ __('Registration here is for public supporters and visitors. Staff accounts are provisioned internally.') }}</p>

                <form method="POST" action="{{ route('register.store') }}" class="auth-form">
                    @csrf

                    <div class="form-grid-2">
                        <div class="field-group">
                            <label for="name">{{ __('Full name') }}</label>
                            <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autocomplete="name">
                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-group">
                            <label for="phone">{{ __('Phone number') }}</label>
                            <input id="phone" class="form-control" type="text" name="phone" value="{{ old('phone') }}" autocomplete="tel">
                            @error('phone')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="email">{{ __('Email address') }}</label>
                        <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="preferred_locale">{{ __('Preferred language') }}</label>
                        <select id="preferred_locale" class="form-control" name="preferred_locale">
                            <option value="">{{ __('Use the current site language') }}</option>
                            @foreach (($publicLanguages ?? collect()) as $language)
                                <option value="{{ $language->code }}" @selected(old('preferred_locale') === $language->code)>
                                    {{ $language->native_name ?: $language->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('preferred_locale')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-grid-2">
                        <div class="field-group">
                            <label for="password">{{ __('Password') }}</label>
                            <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password">
                            @error('password')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-group">
                            <label for="password_confirmation">{{ __('Confirm password') }}</label>
                            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="button">{{ __('Create Account') }}</button>
                        <a href="{{ route('login') }}" class="button--subtle">{{ __('Already have an account?') }}</a>
                    </div>
                </form>
            </div>

            <aside class="panel auth-card auth-note">
                <h2>{{ __('What you get') }}</h2>
                <p>{{ __('A public account keeps the experience personal without mixing public access and staff operations.') }}</p>

                <ul class="auth-note__list">
                    <li>
                        <strong>{{ __('Personal account area') }}</strong>
                        <span>{{ __('Review your saved items, profile details, and account preferences.') }}</span>
                    </li>
                    <li>
                        <strong>{{ __('Public-only registration') }}</strong>
                        <span>{{ __('This form does not create staff or admin access.') }}</span>
                    </li>
                    <li>
                        <strong>{{ __('Secure staff onboarding') }}</strong>
                        <span>{{ __('Staff accounts continue to be created internally by authorized administrators.') }}</span>
                    </li>
                </ul>
            </aside>
        </div>
    </section>
@endsection
