<div class="panel">
    <h2 class="section-title">City Details</h2>
    <div class="form-grid">
        <div class="stacked-field">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $city->name ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" value="{{ old('slug', $city->slug ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="code">Code</label>
            <input id="code" name="code" value="{{ old('code', $city->code ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="country_code">Country Code</label>
            <input id="country_code" name="country_code" value="{{ old('country_code', $city->country_code ?? 'MA') }}" required>
        </div>
        <div class="stacked-field">
            <label for="region">Region</label>
            <input id="region" name="region" value="{{ old('region', $city->region ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $city->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="stacked-field">
            <label for="latitude">Latitude</label>
            <input id="latitude" name="latitude" value="{{ old('latitude', $city->latitude ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="longitude">Longitude</label>
            <input id="longitude" name="longitude" value="{{ old('longitude', $city->longitude ?? '') }}">
        </div>
        <div class="stacked-field full-span">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $city->description ?? '') }}</textarea>
        </div>
    </div>
</div>
<div class="field-inline" style="margin-top: 18px;">
    <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
    <a class="btn btn-secondary" href="{{ route('admin.cities.index') }}">Cancel</a>
</div>
