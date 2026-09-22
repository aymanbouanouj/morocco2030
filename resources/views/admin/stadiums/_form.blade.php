<div class="panel">
    <h2 class="section-title">Stadium Details</h2>
    <div class="form-grid">
        <div class="stacked-field">
            <label for="city_id">City</label>
            <select id="city_id" name="city_id" required>
                <option value="">Select city</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}" @selected((string) old('city_id', $stadium->city_id ?? '') === (string) $city->id)>{{ $city->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="stacked-field">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $stadium->name ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" value="{{ old('slug', $stadium->slug ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="code">Code</label>
            <input id="code" name="code" value="{{ old('code', $stadium->code ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="capacity">Capacity</label>
            <input id="capacity" type="number" name="capacity" value="{{ old('capacity', $stadium->capacity ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="opened_year">Opened Year</label>
            <input id="opened_year" type="number" name="opened_year" value="{{ old('opened_year', $stadium->opened_year ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="surface_type">Surface Type</label>
            <input id="surface_type" name="surface_type" value="{{ old('surface_type', $stadium->surface_type ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $stadium->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="stacked-field full-span">
            <label for="address">Address</label>
            <input id="address" name="address" value="{{ old('address', $stadium->address ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="latitude">Latitude</label>
            <input id="latitude" name="latitude" value="{{ old('latitude', $stadium->latitude ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="longitude">Longitude</label>
            <input id="longitude" name="longitude" value="{{ old('longitude', $stadium->longitude ?? '') }}">
        </div>
    </div>
</div>
<div class="field-inline" style="margin-top: 18px;">
    <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
    <a class="btn btn-secondary" href="{{ route('admin.stadiums.index') }}">Cancel</a>
</div>
