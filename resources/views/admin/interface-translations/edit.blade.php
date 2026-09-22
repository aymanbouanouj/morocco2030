@extends('admin.layouts.app')

@php($pageTitle = 'Edit Interface Translation')
@php($pageDescription = 'Update a translation value while preserving its lookup key.')

@section('content')
    <form method="POST" action="{{ route('admin.interface-translations.update', $interfaceTranslation) }}">
        @csrf
        @method('PUT')

        <div class="panel">
            <h2 class="section-title">Translation Details</h2>
            <div class="form-grid">
                <div class="stacked-field">
                    <label for="language">Language</label>
                    <input id="language" value="{{ $interfaceTranslation->language?->name }} ({{ $interfaceTranslation->language?->code }})" disabled>
                </div>
                <div class="stacked-field">
                    <label for="namespace">Namespace</label>
                    <input id="namespace" value="{{ $interfaceTranslation->namespace }}" disabled>
                </div>
                <div class="stacked-field">
                    <label for="group_name">Group</label>
                    <input id="group_name" value="{{ $interfaceTranslation->group_name }}" disabled>
                </div>
                <div class="stacked-field">
                    <label for="translation_key">Key</label>
                    <input id="translation_key" value="{{ $interfaceTranslation->translation_key }}" disabled>
                    <span class="meta">Keys are protected because Blade calls and the database translation loader depend on them.</span>
                </div>
                <div class="stacked-field full-span">
                    <label for="value">Translated Value</label>
                    <textarea id="value" name="value">{{ old('value', $interfaceTranslation->value) }}</textarea>
                    @error('value')<span class="meta">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="field-inline" style="margin-top: 18px;">
            <button class="btn btn-primary" type="submit">Save Translation</button>
            <a class="btn btn-secondary" href="{{ route('admin.interface-translations.index', ['language_id' => $interfaceTranslation->language_id]) }}">Cancel</a>
        </div>
    </form>
@endsection
