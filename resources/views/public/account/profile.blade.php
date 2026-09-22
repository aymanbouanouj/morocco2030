@extends('public.layouts.app')

@section('title', __('Profile').' | '.__('Morocco 2030'))

@section('content')
    @include('public.partials.page-header', [
        'eyebrow' => __('Account'),
        'title' => __('Profile'),
        'summary' => __('Keep your public account contact details current.'),
    ])

    <section class="page-section account-layout">
        <aside class="account-sidebar">
            @include('public.account._nav')
        </aside>

        <div class="panel account-panel">
            <h2>{{ __('Your profile details') }}</h2>
            <p>{{ __('These details are used for your public account only.') }}</p>

            <form method="POST" action="{{ route('account.profile.update') }}" class="account-form">
                @csrf
                @method('PUT')

                <div class="field-group">
                    <label for="name">{{ __('Full name') }}</label>
                    <input id="name" class="form-control" type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-grid-2">
                    <div class="field-group">
                        <label for="email">{{ __('Email address') }}</label>
                        <input id="email" class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="phone">{{ __('Phone number') }}</label>
                        <input id="phone" class="form-control" type="text" name="phone" value="{{ old('phone', $user->phone) }}" autocomplete="tel">
                        @error('phone')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="button">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection
