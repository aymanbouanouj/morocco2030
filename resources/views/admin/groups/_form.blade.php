<div class="panel">
    <h2 class="section-title">Group Details</h2>

    <div class="form-grid">
        <div class="stacked-field">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $group->name ?? '') }}" required>
        </div>

        <div class="stacked-field">
            <label for="code">Code</label>
            <input id="code" name="code" value="{{ old('code', $group->code ?? '') }}" required>
        </div>

        <div class="stacked-field">
            <label for="sort_order">Sort Order</label>
            <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $group->sort_order ?? 0) }}" min="0">
        </div>

        <div class="stacked-field full-span">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $group->description ?? '') }}</textarea>
        </div>
    </div>
</div>

<div class="field-inline" style="margin-top: 18px;">
    <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
    <a class="btn btn-secondary" href="{{ route('admin.groups.index') }}">Cancel</a>
</div>
