@extends('admin.layouts.app')

@php($pageTitle = 'Media Readiness')
@php($pageDescription = 'Read-only asset-control groundwork for a future secure Media Manager.')

@section('content')
    <div class="page-stack">
        <section class="stats-grid">
            <article class="panel stats-card">
                <h3>Uploads</h3>
                <strong>Disabled</strong>
                <p>No upload, edit, delete, or file mutation workflow is enabled.</p>
            </article>
            <article class="panel stats-card">
                <h3>Media Records</h3>
                <strong>{{ number_format($mediaSummary['total']) }}</strong>
                <p>{{ $mediaSummary['available'] ? 'Database media table is available.' : 'Media table unavailable in this environment.' }}</p>
            </article>
            <article class="panel stats-card">
                <h3>Public Files</h3>
                <strong>{{ number_format($mediaSummary['public']) }}</strong>
                <p>Rows marked with public visibility.</p>
            </article>
            <article class="panel stats-card">
                <h3>Relations</h3>
                <strong>{{ number_format($mediaSummary['relations']) }}</strong>
                <p>Polymorphic media attachments recorded.</p>
            </article>
        </section>

        <section class="panel">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">Current asset folders</h2>
                    <p class="meta">This page reports filesystem readiness only. It does not create folders or upload files.</p>
                </div>
                <span class="status-badge">Read-only</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Path</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Files</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assetFolders as $folder)
                            <tr>
                                <td><code>public/{{ $folder['path'] }}</code></td>
                                <td>{{ $folder['description'] }}</td>
                                <td>{{ $folder['exists'] ? 'Ready' : 'Missing' }}</td>
                                <td>{{ number_format($folder['file_count']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">Placeholder coverage</h2>
                    <p class="meta">Fallback SVGs are original local assets prepared for future safe image rendering.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Fallback path</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($placeholderStatuses as $placeholder)
                            <tr>
                                <td>{{ ucfirst($placeholder['type']) }}</td>
                                <td><code>public/{{ $placeholder['path'] }}</code></td>
                                <td>{{ $placeholder['exists'] ? 'Ready' : 'Missing' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">Future Media Manager scope</h2>
                    <p class="meta">These categories are planning targets only. No upload workflow exists in this phase.</p>
                </div>
            </div>

            <div class="detail-grid">
                <article>
                    <h3>Media categories</h3>
                    <ul>
                        @foreach ($futureCategories as $category)
                            <li>{{ $category }}</li>
                        @endforeach
                    </ul>
                </article>
                <article>
                    <h3>Upload security checklist</h3>
                    <ul>
                        @foreach ($securityChecklist as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>
            </div>
        </section>
    </div>
@endsection
