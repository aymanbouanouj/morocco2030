<div class="panel">
    <h2 class="section-title">Match Fixture Details</h2>
    @if ($teams->isEmpty())
        <div class="alert alert-warning">
            No teams exist yet. Knockout fixtures may still be created with unresolved slots, but group-stage, live, and completed fixtures require teams.
        </div>
    @endif
    @if ($groups->isEmpty())
        <div class="alert alert-warning">
            No groups exist yet. Group-stage fixtures require a group assignment.
            <a class="btn-link" href="{{ route('admin.groups.create') }}">Create a group</a>
        </div>
    @endif
    <div class="form-grid">
        <div class="stacked-field">
            <label for="home_team_id">Home Team / Slot</label>
            <select id="home_team_id" name="home_team_id">
                <option value="">Leave unresolved</option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}" @selected((string) old('home_team_id', $match->home_team_id ?? '') === (string) $team->id)>{{ $team->name }}</option>
                @endforeach
            </select>
            <div class="meta">Required for group-stage, live, and completed fixtures. Optional for future knockout slots.</div>
        </div>
        <div class="stacked-field">
            <label for="away_team_id">Away Team / Slot</label>
            <select id="away_team_id" name="away_team_id">
                <option value="">Leave unresolved</option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}" @selected((string) old('away_team_id', $match->away_team_id ?? '') === (string) $team->id)>{{ $team->name }}</option>
                @endforeach
            </select>
            <div class="meta">Leave empty for unresolved knockout targets until progression fills the slot.</div>
        </div>
        <div class="stacked-field">
            <label for="stadium_id">Stadium</label>
            <select id="stadium_id" name="stadium_id">
                <option value="">Not assigned</option>
                @foreach ($stadiums as $stadium)
                    <option value="{{ $stadium->id }}" @selected((string) old('stadium_id', $match->stadium_id ?? '') === (string) $stadium->id)>{{ $stadium->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="stacked-field">
            <label for="city_id">City</label>
            <select id="city_id" name="city_id">
                <option value="">Not assigned</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}" @selected((string) old('city_id', $match->city_id ?? '') === (string) $city->id)>{{ $city->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="stacked-field">
            <label for="group_id">Group</label>
            <select id="group_id" name="group_id">
                <option value="">Not assigned</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}" @selected((string) old('group_id', $match->group_id ?? '') === (string) $group->id)>{{ $group->name }} ({{ $group->code }})</option>
                @endforeach
            </select>
            <div class="meta">Required when stage type is set to group. Optional for knockout fixtures.</div>
        </div>
        <div class="stacked-field">
            <label for="stage_type">Stage Type</label>
            <select id="stage_type" name="stage_type" required>
                @foreach ($stageTypes as $stageType)
                    <option value="{{ $stageType }}" @selected(old('stage_type', $match->stage_type ?? 'group') === $stageType)>{{ ucwords(str_replace('_', ' ', $stageType)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="stacked-field">
            <label for="code">Code</label>
            <input id="code" name="code" value="{{ old('code', $match->code ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" value="{{ old('slug', $match->slug ?? '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="round_number">Round Number</label>
            <input id="round_number" type="number" name="round_number" value="{{ old('round_number', $match->round_number ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="match_date">Match Date</label>
            <input id="match_date" type="datetime-local" name="match_date" value="{{ old('match_date', isset($match?->match_date) ? $match->match_date->format('Y-m-d\TH:i') : '') }}" required>
        </div>
        <div class="stacked-field">
            <label for="timezone">Timezone</label>
            <input id="timezone" name="timezone" value="{{ old('timezone', $match->timezone ?? config('app.timezone')) }}">
        </div>
        <div class="stacked-field">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $match->status ?? 'scheduled') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <div class="meta">Live and completed fixtures require both teams. Completed fixtures also require a scoreline.</div>
        </div>
        <div class="stacked-field">
            <label for="home_score">Home Score</label>
            <input id="home_score" type="number" name="home_score" value="{{ old('home_score', $match->home_score ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="away_score">Away Score</label>
            <input id="away_score" type="number" name="away_score" value="{{ old('away_score', $match->away_score ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="home_penalty_score">Home Penalty Score</label>
            <input id="home_penalty_score" type="number" name="home_penalty_score" value="{{ old('home_penalty_score', $match->home_penalty_score ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="away_penalty_score">Away Penalty Score</label>
            <input id="away_penalty_score" type="number" name="away_penalty_score" value="{{ old('away_penalty_score', $match->away_penalty_score ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="attendance">Attendance</label>
            <input id="attendance" type="number" name="attendance" value="{{ old('attendance', $match->attendance ?? '') }}">
        </div>
        <div class="stacked-field">
            <label for="published_at">Published At</label>
            <input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at', isset($match?->published_at) ? $match->published_at->format('Y-m-d\TH:i') : '') }}">
        </div>
        <label class="checkbox-card">
            <input type="checkbox" name="extra_time_played" value="1" @checked(old('extra_time_played', $match->extra_time_played ?? false))>
            <span><strong>Extra Time Played</strong><br><span class="meta">Enable if the match went beyond regular time.</span></span>
        </label>
    </div>
</div>
<div class="field-inline" style="margin-top: 18px;">
    <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
    <a class="btn btn-secondary" href="{{ route('admin.matches.index') }}">Cancel</a>
</div>
