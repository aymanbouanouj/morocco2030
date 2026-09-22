@extends('admin.layouts.app')

@php($pageTitle = 'Legacy Image Upload')
@php($pageDescription = 'Protected legacy route. Prefer contextual uploads from News, Partners, Cities, Stadiums, Teams, or Players.')

@section('content')
    <div class="admin-media-review-notice" role="note" style="margin-bottom: 1rem;">
        <strong>Legacy upload route.</strong>
        Generic uploads are disabled from Media Review. Upload assets from their related module when that workflow is available.
        This protected route remains for operational MVP use only.
    </div>

    <form method="POST" action="{{ route('admin.media-files.store') }}" enctype="multipart/form-data" class="panel">
        @csrf

        <div class="toolbar">
            <div>
                <h2 style="margin: 0;">Safe image upload MVP</h2>
                <p class="meta">One image only. Initial attachment during upload, replace, edit, delete, SVG, documents, video, and remote imports are not available.</p>
            </div>
            <a class="btn btn-secondary" href="{{ route('admin.media-files.index') }}">Back to media files</a>
        </div>

        <div class="form-grid">
            <label>
                Image file
                <input type="file" name="file" accept="image/jpeg,image/png,image/webp" required>
                @error('file')
                    <span class="error">{{ $message }}</span>
                @enderror
                <span class="meta">Allowed: JPG, PNG, WebP. Maximum {{ number_format($maxUploadMegabytes, 0) }} MB. SVG, PDF, documents, audio, and video are blocked.</span>
            </label>

            <label>
                Category
                <select name="category" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(old('category', 'generic') === $category)>{{ ucfirst($category) }}</option>
                    @endforeach
                </select>
                @error('category')
                    <span class="error">{{ $message }}</span>
                @enderror
            </label>

            <label>
                Title
                <input name="title" value="{{ old('title') }}" maxlength="255" placeholder="Optional admin-facing title">
                @error('title')
                    <span class="error">{{ $message }}</span>
                @enderror
            </label>

            <label>
                Alt text
                <input name="alt_text" value="{{ old('alt_text') }}" maxlength="255" placeholder="Describe the image for accessibility">
                @error('alt_text')
                    <span class="error">{{ $message }}</span>
                @enderror
            </label>

            <label style="grid-column: 1 / -1;">
                Caption
                <textarea name="caption" rows="4" maxlength="2000" placeholder="Optional caption">{{ old('caption') }}</textarea>
                @error('caption')
                    <span class="error">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <div class="toolbar" style="margin-top: 1rem;">
            <span class="meta">Files are stored on the Laravel public disk under <code>media/{category}/{year}/{month}</code> with generated filenames.</span>
            <button class="btn" type="submit">Upload Image</button>
        </div>
    </form>
@endsection
