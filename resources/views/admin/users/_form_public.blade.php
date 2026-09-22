<div class="panel">
    <h2 class="section-title">Public Audience Profile</h2>
    <p class="meta">This account was created through public registration. It cannot receive admin roles or be converted to staff. Create a new staff account if this person joins the platform team.</p>

    <div class="field-inline" style="margin-bottom: 16px;">
        <span class="status-badge status-badge--public">Public Audience</span>
        <span class="status-badge status-badge--muted">No admin access</span>
    </div>

    <div class="form-grid">
        <div class="stacked-field">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="stacked-field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="stacked-field">
            <label for="phone">Phone</label>
            <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
        </div>

        <div class="stacked-field">
            <label for="preferred_locale">Preferred Locale</label>
            <select id="preferred_locale" name="preferred_locale">
                <option value="">Select locale</option>
                @foreach ($languages as $language)
                    <option value="{{ $language->code }}" @selected(old('preferred_locale', $user->preferred_locale) === $language->code)>
                        {{ $language->name }} ({{ $language->code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="stacked-field">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $user->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="stacked-field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password">
            <div class="meta">Leave blank to keep the current password.</div>
        </div>

        <div class="stacked-field">
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation">
        </div>
    </div>
</div>

<div class="field-inline" style="margin-top: 18px;">
    <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
    <a class="btn btn-secondary" href="{{ route('admin.users.index', ['type' => 'public']) }}">Cancel</a>
</div>
