<div class="page-stack">
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Validation error.</strong>
            <div class="meta" style="margin-top: 6px;">Review the event fields and try again.</div>
        </div>
    @endif

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Event Details</h2>
                <p class="meta">Fixture: {{ $match->slotLabel('home') }} vs {{ $match->slotLabel('away') }}</p>
            </div>
        </div>

        @if (! $match->hasResolvedTeams())
            <div class="alert alert-warning">
                This fixture does not yet have both teams assigned. Resolve the slots before recording match events.
            </div>
        @endif

        <div class="form-grid">
            <div class="stacked-field">
                <label for="team_id">Team</label>
                <select id="team_id" name="team_id">
                    <option value="">Select team</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" @selected((string) old('team_id', $event->team_id ?? '') === (string) $team->id)>{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="stacked-field">
                <label for="event_type">Event Type</label>
                <select id="event_type" name="event_type" required>
                    @foreach ($eventTypes as $eventType)
                        <option value="{{ $eventType }}" @selected(old('event_type', $event->event_type ?? '') === $eventType)>{{ ucwords(str_replace('_', ' ', $eventType)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="stacked-field">
                <label for="player_id">Player</label>
                <select id="player_id" name="player_id">
                    <option value="">Select player</option>
                    @foreach ($teams as $team)
                        <optgroup label="{{ $team->name }}">
                            @foreach ($teamPlayers->get($team->id, collect()) as $player)
                                <option value="{{ $player->id }}" @selected((string) old('player_id', $event->player_id ?? '') === (string) $player->id)>{{ $player->display_name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="stacked-field">
                <label for="related_player_id">Related Player</label>
                <select id="related_player_id" name="related_player_id">
                    <option value="">Select player</option>
                    @foreach ($teams as $team)
                        <optgroup label="{{ $team->name }}">
                            @foreach ($teamPlayers->get($team->id, collect()) as $player)
                                <option value="{{ $player->id }}" @selected((string) old('related_player_id', $event->related_player_id ?? '') === (string) $player->id)>{{ $player->display_name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <div class="meta">Used for substitutions or paired player actions.</div>
            </div>

            <div class="stacked-field">
                <label for="minute">Minute</label>
                <input id="minute" type="number" name="minute" min="0" max="130" value="{{ old('minute', $event->minute ?? 0) }}" required>
            </div>

            <div class="stacked-field">
                <label for="extra_minute">Extra Minute</label>
                <input id="extra_minute" type="number" name="extra_minute" min="0" max="30" value="{{ old('extra_minute', $event->extra_minute ?? '') }}">
            </div>

            <div class="stacked-field">
                <label for="period">Period</label>
                <select id="period" name="period">
                    <option value="">Select period</option>
                    @foreach ($periods as $period)
                        <option value="{{ $period }}" @selected(old('period', $event->period ?? '') === $period)>{{ ucwords(str_replace('_', ' ', $period)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="stacked-field">
                <label for="sort_order">Sort Order</label>
                <input id="sort_order" type="number" name="sort_order" min="0" value="{{ old('sort_order', $event->sort_order ?? '') }}">
            </div>

            <div class="stacked-field full-span">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description', $event->description ?? '') }}</textarea>
            </div>
        </div>
    </section>

    <div class="panel-actions">
        <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
        <a class="btn btn-secondary" href="{{ route('admin.matches.events.index', $match) }}">Cancel</a>
    </div>
</div>
