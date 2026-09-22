@extends('admin.layouts.app')

@php($pageTitle = 'Edit Language')
@php($pageDescription = 'Update safe public language presentation fields.')

@section('content')
    <form method="POST" action="{{ route('admin.languages.update', $language) }}">
        @csrf
        @method('PUT')

        <div class="panel">
            <h2 class="section-title">Language Details</h2>
            <div class="form-grid">
                <div class="stacked-field">
                    <label for="name">Display Name</label>
                    <input id="name" name="name" value="{{ old('name', $language->name) }}" required>
                    @error('name')<span class="meta">{{ $message }}</span>@enderror
                </div>
                <div class="stacked-field">
                    <label for="native_name">Native Name</label>
                    <input id="native_name" name="native_name" value="{{ old('native_name', $language->native_name) }}" required>
                    @error('native_name')<span class="meta">{{ $message }}</span>@enderror
                </div>
                <div class="stacked-field">
                    <label for="code">Code</label>
                    <input id="code" value="{{ $language->code }}" disabled>
                    <span class="meta">Protected because public routes and translation lookup depend on this code.</span>
                </div>
                <div class="stacked-field">
                    <label for="locale">Locale</label>
                    <input id="locale" value="{{ $language->locale }}" disabled>
                </div>
                <div class="stacked-field">
                    <label for="direction">Direction</label>
                    <select id="direction" name="direction" required>
                        @foreach (['ltr', 'rtl'] as $direction)
                            <option value="{{ $direction }}" @selected(old('direction', $language->direction) === $direction)>{{ strtoupper($direction) }}</option>
                        @endforeach
                    </select>
                    @error('direction')<span class="meta">{{ $message }}</span>@enderror
                </div>
                <div class="stacked-field">
                    <label for="is_active">Status</label>
                    @php($activeValue = (string) old('is_active', $language->is_active ? '1' : '0'))
                    <select id="is_active" name="is_active" required>
                        <option value="1" @selected($activeValue === '1')>Active</option>
                        <option value="0" @selected($activeValue === '0') @disabled($language->is_default)>Inactive</option>
                    </select>
                    @if ($language->is_default)
                        <span class="meta">The default language cannot be deactivated.</span>
                    @endif
                    @error('is_active')<span class="meta">{{ $message }}</span>@enderror
                </div>
                <div class="stacked-field">
                    <label for="sort_order">Sort Order</label>
                    <input id="sort_order" type="number" min="0" max="65535" name="sort_order" value="{{ old('sort_order', $language->sort_order) }}" required>
                    @error('sort_order')<span class="meta">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="field-inline" style="margin-top: 18px;">
            <button class="btn btn-primary" type="submit">Save Language</button>
            <a class="btn btn-secondary" href="{{ route('admin.languages.index') }}">Cancel</a>
        </div>
    </form>
@endsection
