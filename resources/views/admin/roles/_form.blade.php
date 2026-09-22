@php($selectedPermissions = collect(old('permission_ids', isset($role) ? $role->permissions->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all())

<div class="panel">
    <h2 class="section-title">Role Details</h2>

    <div class="form-grid">
        <div class="stacked-field">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $role->name ?? '') }}" required>
        </div>

        <div class="stacked-field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" value="{{ old('slug', $role->slug ?? '') }}" required>
        </div>

        <div class="stacked-field full-span">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $role->description ?? '') }}</textarea>
        </div>

        <label class="checkbox-card full-span">
            <input type="checkbox" name="is_system" value="1" @checked(old('is_system', $role->is_system ?? false))>
            <span>
                <strong>System Role</strong><br>
                <span class="meta">System roles are treated as protected roles in the admin area.</span>
            </span>
        </label>

        <div class="stacked-field full-span">
            <label>Permissions</label>
            @foreach ($permissionsByModule as $module => $permissions)
                <div class="panel" style="margin-bottom: 12px;">
                    <strong style="text-transform: capitalize;">{{ str_replace('-', ' ', $module) }}</strong>
                    <div class="checkbox-list" style="margin-top: 12px;">
                        @foreach ($permissions as $permission)
                            <label class="checkbox-card">
                                <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" @checked(in_array($permission->id, $selectedPermissions, true))>
                                <span>
                                    <strong>{{ $permission->name }}</strong><br>
                                    <span class="meta">{{ $permission->description ?: $permission->slug }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="field-inline" style="margin-top: 18px;">
    <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
    <a class="btn btn-secondary" href="{{ route('admin.roles.index') }}">Cancel</a>
</div>
