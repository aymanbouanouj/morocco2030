<div class="panel">
    <h2 class="section-title">Team Details</h2>
    @if ($groups->isEmpty())
        <div class="alert alert-warning">
            No groups exist yet. Team creation requires existing group records.
            <a class="btn-link" href="{{ route('admin.groups.create') }}">Create the first group</a>
        </div>
    @endif
    <div class="form-grid">
        <div class="stacked-field">
            <label for="group_id">Competition Group</label>
            <select id="group_id" name="group_id" required>
                <option value="">Select group</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}" @selected((string) old('group_id', $team->group_id ?? '') === (string) $group->id)>{{ $group->name }} ({{ $group->code }})</option>
                @endforeach
            </select>
            <div class="meta">Each team must be assigned to a competition group before standings logic is introduced.</div>
        </div>
        <div class="stacked-field">
            <label for="name">Name</label>
            <input id="name" name="name" value="{{ old('name', $team->name ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="short_name">Short Name</label>
            <input id="short_name" name="short_name" value="{{ old('short_name', $team->short_name ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="code">Code</label>
            <input id="code" name="code" value="{{ old('code', $team->code ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" value="{{ old('slug', $team->slug ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="federation_name">Federation Name</label>
            <input id="federation_name" name="federation_name" value="{{ old('federation_name', $team->federation_name ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="coach_name">Coach Name</label>
            <input id="coach_name" name="coach_name" value="{{ old('coach_name', $team->coach_name ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="founded_year">Founded Year</label>
            <input id="founded_year" type="number" name="founded_year" value="{{ old('founded_year', $team->founded_year ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="team_type">Team Type</label>
            <select id="team_type" name="team_type" required>
                @foreach (['national', 'club'] as $type)
                    <option value="{{ $type }}" @selected(old('team_type', $team->team_type ?? 'national') === $type)>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div class="stacked-field">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $team->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="field-inline" style="margin-top: 18px;">
    <button class="btn btn-primary" type="submit" @disabled($groups->isEmpty())>{{ $submitLabel }}</button>
    <a class="btn btn-secondary" href="{{ route('admin.teams.index') }}">Cancel</a>
</div>
