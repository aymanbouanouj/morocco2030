<div class="page-stack">
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Validation error.</strong>
            <div class="meta" style="margin-top: 6px;">Review the statistic fields and try again.</div>
        </div>
    @endif

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Statistic Details</h2>
                <p class="meta">Fixture: {{ $match->slotLabel('home') }} vs {{ $match->slotLabel('away') }}</p>
            </div>
        </div>

        @if (! $match->hasResolvedTeams())
            <div class="alert alert-warning">
                This fixture does not yet have both teams assigned. Resolve the slots before recording match statistics.
            </div>
        @endif

        <div class="form-grid">
            <div class="stacked-field">
                <label for="team_id">Team</label>
                <select id="team_id" name="team_id" required>
                    <option value="">Select team</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" @selected((string) old('team_id', $statistic->team_id ?? '') === (string) $team->id)>{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="stacked-field">
                <label for="metric_key">Metric</label>
                <select id="metric_key" name="metric_key" required>
                    @foreach ($metricKeys as $metricKey)
                        <option value="{{ $metricKey }}" @selected(old('metric_key', $statistic->metric_key ?? '') === $metricKey)>{{ ucwords(str_replace('_', ' ', $metricKey)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="stacked-field">
                <label for="metric_value">Numeric Value</label>
                <input id="metric_value" type="number" step="0.01" min="0" name="metric_value" value="{{ old('metric_value', $statistic->metric_value ?? '') }}">
            </div>

            <div class="stacked-field">
                <label for="display_value">Display Value</label>
                <input id="display_value" name="display_value" value="{{ old('display_value', $statistic->display_value ?? '') }}">
                <div class="meta">Optional formatted value such as 58% or 14/16.</div>
            </div>

            <div class="stacked-field">
                <label for="context">Context</label>
                <select id="context" name="context" required>
                    @foreach ($contexts as $context)
                        <option value="{{ $context }}" @selected(old('context', $statistic->context ?? 'full_time') === $context)>{{ ucwords(str_replace('_', ' ', $context)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </section>

    <div class="panel-actions">
        <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
        <a class="btn btn-secondary" href="{{ route('admin.matches.statistics.index', $match) }}">Cancel</a>
    </div>
</div>
