@extends('public.layouts.app')

@section('title', __('Settings').' | '.__('Morocco 2030'))

@section('content')
    @include('public.partials.page-header', [
        'eyebrow' => __('Account'),
        'title' => __('Settings'),
        'summary' => __('Choose your preferred language and update your public account password.'),
    ])

    <section class="page-section account-layout">
        <aside class="account-sidebar">
            @include('public.account._nav')
        </aside>

        <div class="panel account-panel">
            <h2>{{ __('Account settings') }}</h2>
            <p>{{ __('Adjust your public account preferences here. Leave the password fields empty if you only want to update your language choice.') }}</p>

            <form method="POST" action="{{ route('account.settings.update') }}" class="account-form">
                @csrf
                @method('PUT')

                <div class="field-group">
                    <label for="preferred_locale">{{ __('Preferred language') }}</label>
                    <select id="preferred_locale" class="form-control" name="preferred_locale">
                        <option value="">{{ __('Use the current site language') }}</option>
                        @foreach (($publicLanguages ?? collect()) as $language)
                            <option value="{{ $language->code }}" @selected(old('preferred_locale', $user->preferred_locale) === $language->code)>
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
                        <label for="current_password">{{ __('Current password') }}</label>
                        <input id="current_password" class="form-control" type="password" name="current_password" autocomplete="current-password">
                        @error('current_password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="field-group">
                        <label for="password">{{ __('New password') }}</label>
                        <input id="password" class="form-control" type="password" name="password" autocomplete="new-password">
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="password_confirmation">{{ __('Confirm new password') }}</label>
                        <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" autocomplete="new-password">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="button">{{ __('Save Settings') }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection
