@extends('admin.layouts.app')

@php($pageTitle = 'Missing Translation Report')
@php($pageDescription = 'Review interface translation coverage without mutating translation records.')

@section('content')
    <section class="panel">
        <div class="toolbar">
            <span class="meta"><strong>{{ $totalKeys }}</strong> distinct interface translation keys are currently known.</span>
            <a class="btn btn-secondary" href="{{ route('admin.interface-translations.index') }}">Back To Translations</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Language</th>
                        <th>Complete</th>
                        <th>Missing Rows</th>
                        <th>Empty Values</th>
                        <th>Sample Missing Keys</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>
                                <strong>{{ $row['language']->name }}</strong><br>
                                <span class="meta">{{ $row['language']->code }} / {{ $row['language']->locale }}</span>
                                @if (! $row['language']->is_active)
                                    <br><span class="status-badge">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $row['complete_count'] }} / {{ $row['total_keys'] }}</td>
                            <td><span class="status-badge">{{ $row['missing_count'] }}</span></td>
                            <td><span class="status-badge">{{ $row['empty_count'] }}</span></td>
                            <td>
                                @if ($row['sample_missing']->isEmpty())
                                    <span class="meta">No missing keys in sample.</span>
                                @else
                                    <div class="compact-list">
                                        @foreach ($row['sample_missing'] as $missing)
                                            <span class="entity-note">
                                                {{ $missing->namespace }} / {{ $missing->group_name }} / {{ \Illuminate\Support\Str::limit($missing->translation_key, 70) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="meta">No languages are available for translation reporting.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
