@extends('admin.layouts.app')

@php($pageTitle = 'Edit Setting')
@php($pageDescription = 'Update safe database-backed platform configuration.')

@section('content')
    <form method="POST" action="{{ route('admin.settings.update', $setting) }}">
        @csrf
        @method('PUT')

        <div class="panel">
            <h2 class="section-title">Setting Details</h2>
            <div class="form-grid">
                <div class="stacked-field">
                    <label for="group_name">Group</label>
                    <input id="group_name" value="{{ $setting->group_name }}" disabled>
                </div>
                <div class="stacked-field">
                    <label for="setting_key">Key</label>
                    <input id="setting_key" value="{{ $setting->setting_key }}" disabled>
                </div>
                <div class="stacked-field">
                    <label for="type">Type</label>
                    <input id="type" value="{{ $setting->type }}" disabled>
                </div>
                <div class="stacked-field">
                    <label for="value">Value</label>
                    @if (in_array($setting->type, ['boolean', 'bool'], true))
                        <select id="value" name="value">
                            <option value="1" @selected(old('value', $setting->value) === '1' || old('value', $setting->value) === true)>True</option>
                            <option value="0" @selected(old('value', $setting->value) === '0' || old('value', $setting->value) === false)>False</option>
                        </select>
                    @else
                        <textarea id="value" name="value">{{ old('value', $setting->value) }}</textarea>
                    @endif
                    @error('value')<span class="meta">{{ $message }}</span>@enderror
                </div>
                <div class="stacked-field">
                    <label for="is_public">Public Visibility</label>
                    <select id="is_public" name="is_public">
                        <option value="0" @selected(! old('is_public', $setting->is_public))>Private</option>
                        <option value="1" @selected((bool) old('is_public', $setting->is_public))>Public</option>
                    </select>
                    @error('is_public')<span class="meta">{{ $message }}</span>@enderror
                </div>
                <div class="stacked-field">
                    <label for="autoload">Autoload</label>
                    <select id="autoload" name="autoload">
                        <option value="1" @selected((bool) old('autoload', $setting->autoload))>Yes</option>
                        <option value="0" @selected(! old('autoload', $setting->autoload))>No</option>
                    </select>
                    @error('autoload')<span class="meta">{{ $message }}</span>@enderror
                </div>
                <div class="stacked-field full-span">
                    <label for="description">Description</label>
                    <textarea id="description" name="description">{{ old('description', $setting->description) }}</textarea>
                    @error('description')<span class="meta">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="field-inline" style="margin-top: 18px;">
            <button class="btn btn-primary" type="submit">Save Setting</button>
            <a class="btn btn-secondary" href="{{ route('admin.settings.index') }}">Cancel</a>
        </div>
    </form>
@endsection
