<div class="panel">
    <h2 class="section-title">Player Details</h2>
    @if ($teams->isEmpty())
        <div class="alert alert-warning">No teams exist yet. Player creation requires an existing team.</div>
    @endif
    <div class="form-grid">
        <div class="stacked-field">
            <label for="team_id">Team</label>
            <select id="team_id" name="team_id" required>
                <option value="">Select team</option>
                @foreach ($teams as $teamOption)
                    <option value="{{ $teamOption->id }}" @selected((string) old('team_id', $player->team_id ?? '') === (string) $teamOption->id)>{{ $teamOption->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="stacked-field">
            <label for="display_name">Display Name</label>
            <input id="display_name" name="display_name" value="{{ old('display_name', $player->display_name ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="first_name">First Name</label>
            <input id="first_name" name="first_name" value="{{ old('first_name', $player->first_name ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="last_name">Last Name</label>
            <input id="last_name" name="last_name" value="{{ old('last_name', $player->last_name ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" value="{{ old('slug', $player->slug ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="shirt_number">Shirt Number</label>
            <input id="shirt_number" type="number" name="shirt_number" value="{{ old('shirt_number', $player->shirt_number ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="position">Position</label>
            <select id="position" name="position" required>
                @foreach (['goalkeeper', 'defender', 'midfielder', 'forward'] as $position)
                    <option value="{{ $position }}" @selected(old('position', $player->position ?? 'forward') === $position)>{{ ucfirst($position) }}</option>
                @endforeach
            </select>
        </div>
        <div class="stacked-field">
            <label for="date_of_birth">Date of Birth</label>
            <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth', isset($player?->date_of_birth) ? $player->date_of_birth->format('Y-m-d') : '') }}">
        </div>
        <div class="stacked-field">
            <label for="nationality_code">Nationality Code</label>
            <input id="nationality_code" name="nationality_code" value="{{ old('nationality_code', $player->nationality_code ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="height_cm">Height (cm)</label>
            <input id="height_cm" type="number" name="height_cm" value="{{ old('height_cm', $player->height_cm ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="weight_kg">Weight (kg)</label>
            <input id="weight_kg" type="number" name="weight_kg" value="{{ old('weight_kg', $player->weight_kg ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $player->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <label class="checkbox-card">
            <input type="checkbox" name="is_captain" value="1" @checked(old('is_captain', $player->is_captain ?? false))>
            <span><strong>Captain</strong><br><span class="meta">Marks the player as a team captain.</span></span>
        </label>
        <div class="stacked-field full-span">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio">{{ old('bio', $player->bio ?? '') }}</textarea>
        </div>
    </div>
</div>
<div class="field-inline" style="margin-top: 18px;">
    <button class="btn btn-primary" type="submit" @disabled($teams->isEmpty())>{{ $submitLabel }}</button>
    <a class="btn btn-secondary" href="{{ route('admin.players.index') }}">Cancel</a>
</div>
