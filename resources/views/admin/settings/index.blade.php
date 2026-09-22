@extends('admin.layouts.app')

@php($pageTitle = 'Settings')
@php($pageDescription = 'Manage safe non-secret platform settings stored in the database.')

@section('content')
    <section class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.settings.index') }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search settings">
                <select name="group">
                    <option value="">All groups</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group }}" @selected(($filters['group'] ?? '') === $group)>{{ $group }}</option>
                    @endforeach
                </select>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>
            <span class="meta">Sensitive keys are protected from editing and value display.</span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Key</th>
                        <th>Value</th>
                        <th>Type</th>
                        <th>Public</th>
                        <th>Autoload</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($settings as $setting)
                        <tr>
                            <td><span class="status-badge">{{ $setting->group_name }}</span></td>
                            <td>
                                <strong>{{ $setting->setting_key }}</strong>
                                @if ($setting->description)
                                    <br><span class="meta">{{ $setting->description }}</span>
                                @endif
                            </td>
                            <td>{{ $setting->displayValue() }}</td>
                            <td>{{ $setting->type }}</td>
                            <td>{{ $setting->is_public ? 'Yes' : 'No' }}</td>
                            <td>{{ $setting->autoload ? 'Yes' : 'No' }}</td>
                            <td class="table-actions">
                                @if ($setting->isSensitive())
                                    <span class="meta">Protected</span>
                                @else
                                    <a class="btn-link" href="{{ route('admin.settings.edit', $setting) }}">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="meta">No settings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $settings->links() }}
    </section>
@endsection
