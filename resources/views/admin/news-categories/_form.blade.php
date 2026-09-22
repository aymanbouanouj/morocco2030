<div class="panel">
    <h2 class="section-title">Category Details</h2>

    <div class="form-grid">
        <div class="stacked-field">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $newsCategory->name ?? '') }}" required>
        </div>

        <div class="stacked-field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" value="{{ old('slug', $newsCategory->slug ?? '') }}" required>
        </div>

        <div class="stacked-field">
            <label for="parent_id">Parent Category</label>
            <select id="parent_id" name="parent_id">
                <option value="">None</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('parent_id', $newsCategory->parent_id ?? '') === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="stacked-field">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $newsCategory->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>

        <div class="stacked-field">
            <label for="sort_order">Sort Order</label>
            <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $newsCategory->sort_order ?? 0) }}" min="0">
        </div>

        <div class="stacked-field full-span">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $newsCategory->description ?? '') }}</textarea>
        </div>
    </div>
</div>

<div class="field-inline" style="margin-top: 18px;">
    <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
    <a class="btn btn-secondary" href="{{ route('admin.news-categories.index') }}">Cancel</a>
</div>
