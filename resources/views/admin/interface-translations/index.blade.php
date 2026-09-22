@extends('admin.layouts.app')

@php($pageTitle = 'Interface Translations')
@php($pageDescription = 'Edit database-backed public interface translation values without changing keys.')

@section('content')
    <section class="panel">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.interface-translations.index') }}">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search keys or values">
                <select name="language_id">
                    <option value="">All languages</option>
                    @foreach ($languages as $language)
                        <option value="{{ $language->id }}" @selected((string) ($filters['language_id'] ?? '') === (string) $language->id)>{{ $language->name }} ({{ $language->code }})</option>
                    @endforeach
                </select>
                <select name="namespace">
                    <option value="">All namespaces</option>
                    @foreach ($namespaces as $namespace)
                        <option value="{{ $namespace }}" @selected(($filters['namespace'] ?? '') === $namespace)>{{ $namespace }}</option>
                    @endforeach
                </select>
                <select name="group_name">
                    <option value="">All groups</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group }}" @selected(($filters['group_name'] ?? '') === $group)>{{ $group }}</option>
                    @endforeach
                </select>
                <select name="status">
                    <option value="">All values</option>
                    <option value="empty" @selected(($filters['status'] ?? '') === 'empty')>Empty values</option>
                </select>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>
            <a class="btn btn-secondary" href="{{ route('admin.interface-translations.missing') }}">Missing Report</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Language</th>
                        <th>Namespace</th>
                        <th>Group</th>
                        <th>Key</th>
                        <th>Value</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($translations as $translation)
                        <tr>
                            <td>
                                <strong>{{ $translation->language?->name ?? 'Unknown' }}</strong><br>
                                <span class="meta">{{ $translation->language?->code ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $translation->namespace }}</td>
                            <td>{{ $translation->group_name }}</td>
                            <td><strong>{{ $translation->translation_key }}</strong></td>
                            <td>{{ \Illuminate\Support\Str::limit($translation->value, 100) ?: 'Empty' }}</td>
                            <td class="table-actions">
                                <a class="btn-link" href="{{ route('admin.interface-translations.edit', $translation) }}">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="meta">No interface translations found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $translations->links() }}
    </section>
@endsection
