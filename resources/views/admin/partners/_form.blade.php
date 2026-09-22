<div class="panel">
    <h2 class="section-title">Partner Details</h2>
    <div class="form-grid">
        <div class="stacked-field">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $partner->name ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" value="{{ old('slug', $partner->slug ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="category">Category</label>
            <input id="category" name="category" value="{{ old('category', $partner->category ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="tier">Tier</label>
            <input id="tier" name="tier" value="{{ old('tier', $partner->tier ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="website_url">Website URL</label>
            <input id="website_url" name="website_url" value="{{ old('website_url', $partner->website_url ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="logo">Partner logo</label>
            <input id="logo" type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            <small class="meta">PNG, JPG, WEBP. Recommended transparent logo.</small>
        </div>
        <div class="stacked-field">
            <label for="logo_alt">Logo alt text</label>
            <input id="logo_alt" name="logo_alt" value="{{ old('logo_alt', $partner->logo_alt ?? '') }}" placeholder="{{ old('name', $partner->name ?? 'Partner name') }}">
        </div>
        @if (isset($partner) && $partner->hasLogo())
            <div class="stacked-field full-span">
                <label>Current logo</label>
                <div class="field-inline">
                    <img src="{{ $partner->logoUrl() }}" alt="{{ $partner->logoAlt() }}" style="max-width: 160px; max-height: 72px; object-fit: contain;">
                    <span class="meta">{{ $partner->logo_path }}</span>
                </div>
            </div>
        @endif
        <div class="stacked-field">
            <label for="contact_email">Contact Email</label>
            <input id="contact_email" type="email" name="contact_email" value="{{ old('contact_email', $partner->contact_email ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="contact_phone">Contact Phone</label>
            <input id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $partner->contact_phone ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $partner->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="stacked-field full-span">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $partner->description ?? '') }}</textarea>
        </div>
    </div>
</div>
<div class="field-inline" style="margin-top: 18px;">
    <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
    <a class="btn btn-secondary" href="{{ route('admin.partners.index') }}">Cancel</a>
</div>
