<div class="page-stack">
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Validation error.</strong>
            <div class="meta" style="margin-top: 6px;">Review the lineup fields and try again.</div>
        </div>
    @endif

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Lineup Entry Details</h2>
                <p class="meta">Fixture: {{ $match->slotLabel('home') }} vs {{ $match->slotLabel('away') }}</p>
            </div>
        </div>

        @if (! $match->hasResolvedTeams())
            <div class="alert alert-warning">
                This fixture does not yet have both teams assigned. Resolve the slots before managing lineups.
            </div>
        @endif

        <div class="form-grid">
            <div class="stacked-field">
                <label for="team_id">Team</label>
                <select id="team_id" name="team_id" required>
                    <option value="">Select team</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" @selected((string) old('team_id', $lineup->team_id ?? '') === (string) $team->id)>{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="stacked-field">
                <label for="player_id">Player</label>
                <select id="player_id" name="player_id" required>
                    <option value="">Select player</option>
                    @foreach ($teams as $team)
                        <optgroup label="{{ $team->name }}">
                            @foreach ($teamPlayers->get($team->id, collect()) as $player)
                                <option value="{{ $player->id }}" @selected((string) old('player_id', $lineup->player_id ?? '') === (string) $player->id)>{{ $player->display_name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="stacked-field">
                <label for="lineup_type">Lineup Type</label>
                <select id="lineup_type" name="lineup_type" required>
                    @foreach ($lineupTypes as $lineupType)
                        <option value="{{ $lineupType }}" @selected(old('lineup_type', $lineup->lineup_type ?? 'starting') === $lineupType)>{{ ucfirst($lineupType) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="stacked-field">
                <label for="sort_order">Sort Order</label>
                <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $lineup->sort_order ?? 0) }}" required>
                <div class="meta">Use lower values for starters or higher-priority entries.</div>
            </div>

            <div class="stacked-field">
                <label for="position">Position Label</label>
                <input id="position" name="position" value="{{ old('position', $lineup->position ?? '') }}">
            </div>

            <div class="stacked-field">
                <label for="formation_slot">Formation Slot</label>
                <input id="formation_slot" name="formation_slot" value="{{ old('formation_slot', $lineup->formation_slot ?? '') }}">
            </div>

            <div class="stacked-field">
                <label for="shirt_number">Shirt Number</label>
                <input id="shirt_number" type="number" min="1" max="99" name="shirt_number" value="{{ old('shirt_number', $lineup->shirt_number ?? '') }}">
            </div>

            <div class="stacked-field">
                <label for="minute_in">Minute In</label>
                <input id="minute_in" type="number" min="0" max="130" name="minute_in" value="{{ old('minute_in', $lineup->minute_in ?? '') }}">
            </div>

            <div class="stacked-field">
                <label for="minute_out">Minute Out</label>
                <input id="minute_out" type="number" min="0" max="130" name="minute_out" value="{{ old('minute_out', $lineup->minute_out ?? '') }}">
            </div>

            <label class="checkbox-card">
                <input type="checkbox" name="is_captain" value="1" @checked(old('is_captain', $lineup->is_captain ?? false))>
                <span><strong>Captain</strong><br><span class="meta">Marks the player as captain for this fixture.</span></span>
            </label>

            <label class="checkbox-card">
                <input type="checkbox" name="is_goalkeeper" value="1" @checked(old('is_goalkeeper', $lineup->is_goalkeeper ?? false))>
                <span><strong>Goalkeeper</strong><br><span class="meta">Marks the player as goalkeeper for this fixture.</span></span>
            </label>
        </div>
    </section>

    <div class="panel-actions">
        <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
        <a class="btn btn-secondary" href="{{ route('admin.matches.lineups.index', $match) }}">Cancel</a>
    </div>
</div>
