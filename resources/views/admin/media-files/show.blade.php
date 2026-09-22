@extends('admin.layouts.app')

@php($pageTitle = 'Media File #'.$mediaFile->id)
@php($pageDescription = 'Media metadata, storage details, and usage relationships.')

@section('content')
    <div class="page-stack">
        <section class="panel">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">{{ $mediaFile->filename }}</h2>
                    <p class="meta">Metadata detail page. Archive/restore changes status only; no media record or physical file is deleted.</p>
                </div>
                <div class="field-inline">
                    @can('update', $mediaFile)
                        <a class="btn btn-secondary" href="{{ route('admin.media-files.edit', $mediaFile) }}">Edit Metadata</a>
                    @endcan
                    @if ($mediaFile->status === 'active')
                        @can('archive', $mediaFile)
                            <form method="POST" action="{{ route('admin.media-files.archive', $mediaFile) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-danger" type="submit">Archive</button>
                            </form>
                        @endcan
                    @elseif ($mediaFile->status === 'archived')
                        @can('restore', $mediaFile)
                            <form method="POST" action="{{ route('admin.media-files.restore', $mediaFile) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-primary" type="submit">Restore</button>
                            </form>
                        @endcan
                    @endif
                    <a class="btn btn-secondary" href="{{ route('admin.media-files.index') }}">Back to media files</a>
                </div>
            </div>

            <div class="detail-grid">
                <article>
                    <h3>Preview</h3>
                    @if ($previewUrl)
                        <img src="{{ $previewUrl }}" alt="{{ $mediaFile->alt_text ?: $mediaFile->title ?: $mediaFile->original_name }}" style="width: 100%; max-height: 320px; object-fit: contain; border-radius: 18px; background: #f8fafc;">
                    @else
                        <p class="meta">Preview unavailable. Only active public JPEG, PNG, and WebP files are previewed here.</p>
                    @endif

                    @if ($publicUrl)
                        <p><a class="btn-link" href="{{ $publicUrl }}" target="_blank" rel="noopener">Open public URL</a></p>
                    @else
                        <p class="meta">Public URL hidden because the record is not an active public file on the public disk.</p>
                    @endif
                </article>

                <article>
                    <h3>Metadata</h3>
                    <dl class="meta-list">
                        <dt>ID</dt>
                        <dd>{{ $mediaFile->id }}</dd>
                        <dt>Filename</dt>
                        <dd>{{ $mediaFile->filename }}</dd>
                        <dt>Original name</dt>
                        <dd>{{ $mediaFile->original_name }}</dd>
                        <dt>Title</dt>
                        <dd>{{ $mediaFile->title ?: 'N/A' }}</dd>
                        <dt>Alt text</dt>
                        <dd>{{ $mediaFile->alt_text ?: 'N/A' }}</dd>
                        <dt>Caption</dt>
                        <dd>{{ $mediaFile->caption ?: 'N/A' }}</dd>
                    </dl>
                </article>

                <article>
                    <h3>Storage</h3>
                    <dl class="meta-list">
                        <dt>Disk</dt>
                        <dd>{{ $mediaFile->disk }}</dd>
                        <dt>Path</dt>
                        <dd>{{ $mediaFile->path }}</dd>
                        <dt>MIME type</dt>
                        <dd>{{ $mediaFile->mime_type }}</dd>
                        <dt>Extension</dt>
                        <dd>{{ $mediaFile->extension ?: 'N/A' }}</dd>
                        <dt>Checksum</dt>
                        <dd>{{ $mediaFile->checksum ?: 'N/A' }}</dd>
                    </dl>
                </article>

                <article>
                    <h3>State</h3>
                    <dl class="meta-list">
                        <dt>Visibility</dt>
                        <dd>{{ $mediaFile->visibility }}</dd>
                        <dt>Status</dt>
                        <dd>{{ $mediaFile->status }}</dd>
                        <dt>Size</dt>
                        <dd>{{ number_format($mediaFile->size_bytes / 1024, 1) }} KB</dd>
                        <dt>Dimensions</dt>
                        <dd>{{ $mediaFile->width && $mediaFile->height ? $mediaFile->width.'x'.$mediaFile->height : 'N/A' }}</dd>
                        <dt>Uploaded by</dt>
                        <dd>{{ $mediaFile->uploadedBy?->name ?? 'N/A' }}</dd>
                        <dt>Created</dt>
                        <dd>{{ $mediaFile->created_at?->format('Y-m-d H:i') }}</dd>
                        <dt>Updated</dt>
                        <dd>{{ $mediaFile->updated_at?->format('Y-m-d H:i') }}</dd>
                    </dl>
                </article>
            </div>
        </section>

        @can('attach', $mediaFile)
            <section class="panel">
                <div class="toolbar">
                    <div>
                        <h2 style="margin: 0;">Attach to content</h2>
                        <p class="meta">Creates a primary media relation for an existing record. Existing primary media for the same target and role is kept as non-primary; no files are deleted.</p>
                    </div>
                </div>

                @if (! $canAttachMedia)
                    <div class="alert alert-warning">
                        Only active public JPEG, PNG, and WebP media files can be attached to public content.
                    </div>
                @elseif (collect($attachmentTargets)->every(fn ($target) => empty($target['records'])))
                    <p class="meta">No supported content records are available for attachment.</p>
                @else
                    <form method="POST" action="{{ route('admin.media-files.attach', $mediaFile) }}">
                        @csrf
                        <div class="form-grid">
                            <div class="stacked-field full-span">
                                <label for="target_ref">Target record</label>
                                <select id="target_ref" name="target_ref" required>
                                    <option value="">Select content record</option>
                                    @foreach ($attachmentTargets as $targetType => $target)
                                        <optgroup label="{{ $target['label'] }} - role: {{ $target['role'] }}">
                                            @forelse ($target['records'] as $record)
                                                @php($value = $targetType.':'.$record['id'])
                                                <option value="{{ $value }}" @selected(old('target_ref') === $value)>
                                                    {{ $record['label'] }}
                                                </option>
                                            @empty
                                                <option value="" disabled>No records available</option>
                                            @endforelse
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('target_ref') <span class="alert-danger">{{ $message }}</span> @enderror
                                @error('target_type') <span class="alert-danger">{{ $message }}</span> @enderror
                                @error('target_id') <span class="alert-danger">{{ $message }}</span> @enderror
                                <div class="meta">The target type is derived from the selected record through a strict allowlist.</div>
                            </div>

                            <div class="stacked-field">
                                <label for="role">Role</label>
                                <select id="role" name="role" required>
                                    @foreach ($attachmentRoles as $role)
                                        <option value="{{ $role }}" @selected(old('role', $defaultAttachmentRole) === $role)>{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                                @error('role') <span class="alert-danger">{{ $message }}</span> @enderror
                                <div class="meta">Allowed roles are target-specific: team logo, player photo, city image, stadium image, news cover, partner logo.</div>
                            </div>

                            <div class="stacked-field">
                                <label>Attachment mode</label>
                                <input value="Primary media only" disabled>
                                <div class="meta">This phase supports one primary relation per target and role. Gallery, replace, archive, and delete workflows are future work.</div>
                            </div>
                        </div>

                        @error('media_file') <div class="alert alert-danger" style="margin-top: 16px;">{{ $message }}</div> @enderror

                        <div class="field-inline" style="margin-top: 18px;">
                            <button class="btn btn-primary" type="submit">Attach Media</button>
                        </div>
                    </form>
                @endif
            </section>
        @endcan

        <section class="panel">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">Usage relationships</h2>
                    <p class="meta">Detaching removes only the `media_relations` row. It does not delete the media file or physical storage file.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Model</th>
                            <th>Record</th>
                            <th>Role</th>
                            <th>Primary</th>
                            <th>Sort</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mediaFile->mediaRelations as $relation)
                            <tr>
                                <td>{{ class_basename($relation->mediable_type) }}</td>
                                <td>
                                    @if ($relation->mediable)
                                        {{ $relation->mediable->name ?? $relation->mediable->title ?? $relation->mediable->display_name ?? 'Record #'.$relation->mediable_id }}
                                    @else
                                        Missing/deleted record #{{ $relation->mediable_id }}
                                    @endif
                                    <br><span class="meta">{{ $relation->mediable_type }}</span>
                                </td>
                                <td>{{ $relation->role ?: 'default' }}</td>
                                <td>{{ $relation->is_primary ? 'Yes' : 'No' }}</td>
                                <td>{{ $relation->sort_order }}</td>
                                <td>{{ $relation->created_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if ($relation->mediable && auth()->user()?->can('detach', $mediaFile) && auth()->user()?->can('update', $relation->mediable))
                                        <form method="POST" action="{{ route('admin.media-relations.destroy', $relation) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" type="submit">Detach</button>
                                        </form>
                                        <span class="meta">File is not deleted.</span>
                                    @else
                                        <span class="meta">No action available</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="meta">This media file is not attached to any record.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <h2 style="margin-top: 0;">Raw metadata</h2>
            <pre style="white-space: pre-wrap; overflow-x: auto;">{{ $metaJson }}</pre>
        </section>
    </div>
@endsection
