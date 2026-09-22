@php($selectedRoles = collect(old('role_ids', isset($user) ? $user->roles->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all())
@php($showRoles = $canAssignRoles ?? (! isset($user) || auth()->user()?->can('assignRoles', $user)))

<div class="panel">
    <h2 class="section-title">Staff Account Details</h2>
    <p class="meta">Staff accounts are created only by the super admin and may receive administrative roles.</p>

    <div class="form-grid">
        <div class="stacked-field">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
        </div>

        <div class="stacked-field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
        </div>

        <div class="stacked-field">
            <label for="phone">Phone</label>
            <input id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
        </div>

        <div class="stacked-field">
            <label for="preferred_locale">Preferred Locale</label>
            <select id="preferred_locale" name="preferred_locale">
                <option value="">Select locale</option>
                @foreach ($languages as $language)
                    <option value="{{ $language->code }}" @selected(old('preferred_locale', $user->preferred_locale ?? '') === $language->code)>
                        {{ $language->name }} ({{ $language->code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="stacked-field">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $user->status ?? 'active') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="stacked-field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" {{ isset($user) ? '' : 'required' }}>
            @isset($user)
                <div class="meta">Leave blank to keep the current password.</div>
            @endisset
        </div>

        <div class="stacked-field">
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" {{ isset($user) ? '' : 'required' }}>
        </div>

        @if ($showRoles)
            <div class="stacked-field full-span">
                <label>Staff Roles</label>
                <div class="checkbox-list">
                    @foreach ($roles as $role)
                        <label class="checkbox-card">
                            <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" @checked(in_array($role->id, $selectedRoles, true))>
                            <span>
                                <strong>{{ $role->name }}</strong><br>
                                <span class="meta">{{ $role->description ?: $role->slug }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @elseif (isset($user))
            <div class="stacked-field full-span">
                <label>Staff Roles</label>
                <p class="meta">{{ $user->roles->pluck('name')->join(', ') ?: 'None' }} — only the super admin can change role assignments.</p>
            </div>
        @endif
    </div>
</div>

<div class="field-inline" style="margin-top: 18px;">
    <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
    <a class="btn btn-secondary" href="{{ route('admin.users.index', ['type' => 'staff']) }}">Cancel</a>
</div>
