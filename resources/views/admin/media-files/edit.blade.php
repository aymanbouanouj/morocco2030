@extends('admin.layouts.app')

@php($pageTitle = 'Edit Media Metadata')
@php($pageDescription = 'Update safe metadata only. Files, paths, status, and relations are not editable here.')

@section('content')
    <form method="POST" action="{{ route('admin.media-files.update', $mediaFile) }}" class="panel">
        @csrf
        @method('PATCH')

        <div class="toolbar">
            <div>
                <h2 style="margin: 0;">{{ $mediaFile->filename }}</h2>
                <p class="meta">Metadata-only edit. No file input, replacement, deletion, status change, or relation change is available on this page.</p>
            </div>
            <div class="field-inline">
                <a class="btn btn-secondary" href="{{ route('admin.media-files.show', $mediaFile) }}">Back to media file</a>
            </div>
        </div>

        <div class="detail-grid" style="margin-bottom: 18px;">
            <article>
                <h3>Preview</h3>
                @if ($previewUrl)
                    <img src="{{ $previewUrl }}" alt="{{ $mediaFile->alt_text ?: $mediaFile->title ?: $mediaFile->original_name }}" style="width: 100%; max-height: 280px; object-fit: contain; border-radius: 18px; background: #f8fafc;">
                @else
                    <p class="meta">Preview unavailable. Only active public JPEG, PNG, and WebP files are previewed here.</p>
                @endif
            </article>

            <article>
                <h3>Read-only file summary</h3>
                <dl class="meta-list">
                    <dt>Disk</dt>
                    <dd>{{ $mediaFile->disk }}</dd>
                    <dt>Path</dt>
                    <dd>{{ $mediaFile->path }}</dd>
                    <dt>MIME type</dt>
                    <dd>{{ $mediaFile->mime_type }}</dd>
                    <dt>Status</dt>
                    <dd>{{ $mediaFile->status }} <span class="meta">Managed by archive/restore only.</span></dd>
                </dl>
            </article>
        </div>

        <div class="form-grid">
            <label>
                Alt text
                <input name="alt_text" value="{{ old('alt_text', $mediaFile->alt_text) }}" maxlength="255" placeholder="Describe the image for accessibility">
                @error('alt_text')
                    <span class="error">{{ $message }}</span>
                @enderror
                <span class="meta">Used as public image accessibility text where the media is displayed.</span>
            </label>

            <label>
                Visibility
                <select name="visibility">
                    @foreach ($visibilities as $visibility)
                        <option value="{{ $visibility }}" @selected(old('visibility', $mediaFile->visibility) === $visibility)>{{ ucfirst($visibility) }}</option>
                    @endforeach
                </select>
                @error('visibility')
                    <span class="error">{{ $message }}</span>
                @enderror
                <span class="meta">Private media is not exposed as a public URL by the admin preview helper.</span>
            </label>

            <label style="grid-column: 1 / -1;">
                Caption
                <textarea name="caption" rows="4" maxlength="1000" placeholder="Optional caption">{{ old('caption', $mediaFile->caption) }}</textarea>
                @error('caption')
                    <span class="error">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <div class="alert alert-warning" style="margin-top: 18px;">
            Storage fields, filename, MIME type, checksum, status, uploaded user, and media relations are intentionally read-only in this workflow.
        </div>

        <div class="toolbar" style="margin-top: 1rem;">
            <span class="meta">A `media_updated` audit entry is recorded when metadata changes.</span>
            <button class="btn" type="submit">Save Metadata</button>
        </div>
    </form>
@endsection
