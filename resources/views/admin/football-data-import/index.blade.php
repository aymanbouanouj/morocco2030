@extends('admin.layouts.app')

@php($pageTitle = 'Football-Data Import Preview')
@php($pageDescription = 'Dry-run World Cup dataset preview with Morocco 2030 local venue mapping.')

@section('content')
    <style>
        /* FOOTBALL-DATA PHASE 1 - Dry-run import preview */
        .fd-import { display: grid; gap: 16px; }
        .fd-import__banner { border: 1px solid #f4c542; background: #fff8db; color: #4a3900; border-radius: 8px; padding: 12px 14px; }
        .fd-import__grid { display: grid; gap: 12px; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
        .fd-import__card { border: 1px solid var(--admin-border, #d8dee8); border-radius: 8px; padding: 12px; background: #fff; }
        .fd-import__card strong { display: block; font-size: 1.25rem; }
        .fd-import__table { min-width: 920px; }
        .fd-import__error { border: 1px solid #fecaca; background: #fff1f2; color: #7f1d1d; border-radius: 8px; padding: 12px 14px; }
        .fd-import__success { border: 1px solid #bbf7d0; background: #f0fdf4; color: #14532d; border-radius: 8px; padding: 12px 14px; }
        .fd-import__danger { border: 1px solid #fecaca; background: #fff7f7; border-radius: 8px; padding: 14px; }
        .fd-import__actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .fd-import__field { display: grid; gap: 6px; max-width: 460px; }
        .fd-import__field input[type="text"] { width: 100%; }
        .fd-import__check { display: flex; gap: 8px; align-items: flex-start; }
    </style>

    <div class="fd-import">
        <section class="panel">
            <div class="toolbar">
                <div>
                    <h2>Football-Data Import Preview</h2>
                    <p class="meta">External World Cup data source mapped to Morocco 2030 local venues.</p>
                </div>
                <span class="status-badge">Dry-run only - no database write</span>
            </div>

            <div class="fd-import__banner">
                External World Cup data mapped to Morocco 2030 local venues. This is not official FIFA 2030 data.
            </div>
        </section>

        <section class="panel">
            <h2>Provider status</h2>
            <div class="fd-import__grid">
                <div class="fd-import__card">
                    <span class="meta">Provider</span>
                    <strong>{{ $configStatus['provider'] ?: 'Not configured' }}</strong>
                </div>
                <div class="fd-import__card">
                    <span class="meta">Base URL</span>
                    <strong>{{ $configStatus['base_url_configured'] ? 'Configured' : 'Missing' }}</strong>
                </div>
                <div class="fd-import__card">
                    <span class="meta">Token</span>
                    <strong>{{ $configStatus['token_configured'] ? 'Configured' : 'Missing' }}</strong>
                    <span class="meta">Length: {{ $configStatus['token_length'] }}</span>
                </div>
                <div class="fd-import__card">
                    <span class="meta">Competition</span>
                    <strong>{{ $configStatus['competition_code'] }}</strong>
                </div>
                <div class="fd-import__card">
                    <span class="meta">Local mapping</span>
                    <strong>{{ $mappingStatus['stadiums_count'] }} stadiums</strong>
                    <span class="meta">{{ $mappingStatus['cities_count'] }} cities</span>
                </div>
            </div>

            <div class="fd-import__actions">
                <form method="POST" action="{{ route('admin.football-data-import.preview') }}">
                    @csrf
                    <button class="btn btn-primary" type="submit" @disabled(! $configStatus['token_configured'])>Preview World Cup Dry Run</button>
                </form>
                @unless ($configStatus['token_configured'])
                    <span class="alert-danger">Missing EXTERNAL_FOOTBALL_API_TOKEN.</span>
                @endunless
            </div>
        </section>

        <section class="panel">
            <div class="toolbar">
                <div>
                    <h2>Players &amp; Coaches Dry Run</h2>
                    <p class="meta">Preview players and coaches from football-data.org for the current FD-WC teams. No database write is performed.</p>
                </div>
                <span class="status-badge">Preview only</span>
            </div>

            <div class="fd-import__actions">
                <form method="POST" action="{{ route('admin.football-data-import.preview-squads') }}">
                    @csrf
                    <button class="btn btn-primary" type="submit" @disabled(! $configStatus['token_configured'])>Preview squads</button>
                </form>
                @unless ($configStatus['token_configured'])
                    <span class="alert-danger">Missing EXTERNAL_FOOTBALL_API_TOKEN.</span>
                @endunless
            </div>
        </section>

        <section class="panel">
            <div class="toolbar">
                <div>
                    <h2>Import Players &amp; Coaches</h2>
                    <p class="meta">This writes football-data.org squad data to local players and team coach fields. It skips TBD placeholders and does not alter matches, scores, standings, cities, or stadiums.</p>
                </div>
                <span class="status-badge">Guarded write</span>
            </div>

            <div class="fd-import__danger">
                This imports real squad records only after all 48 team detail payloads are available. If football-data.org rate limits the preflight, no database write is performed.
            </div>

            <form method="POST" action="{{ route('admin.football-data-import.import-squads') }}" class="fd-import" data-fd-squad-import-form>
                @csrf
                <label class="fd-import__field">
                    <span class="meta">Type confirmation</span>
                    <input type="text" name="squad_import_confirmation" value="{{ old('squad_import_confirmation') }}" autocomplete="off" data-fd-squad-import-confirmation>
                    <span class="meta">Required phrase: IMPORT FOOTBALL SQUADS</span>
                </label>

                <label class="fd-import__check">
                    <input type="checkbox" name="understands_squad_import" value="1" @checked(old('understands_squad_import')) data-fd-squad-import-understands>
                    <span>I understand this writes players and coach data to the local demo database.</span>
                </label>

                <div class="fd-import__actions">
                    <button class="btn btn-danger" type="submit" disabled data-fd-squad-import-button>Import Players &amp; Coaches</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="toolbar">
                <div>
                    <h2>Reconcile 48 World Cup Teams</h2>
                    <p class="meta">This matches and upgrades local teams using football-data.org World Cup teams. It does not import players or coaches.</p>
                </div>
                <span class="status-badge">Team metadata only</span>
            </div>

            <div class="fd-import__banner">
                This updates local team metadata and crest URLs from football-data.org. TBD placeholders remain placeholders.
            </div>

            <form method="POST" action="{{ route('admin.football-data-import.reconcile-teams') }}" class="fd-import" data-fd-team-reconcile-form>
                @csrf
                <label class="fd-import__field">
                    <span class="meta">Type confirmation</span>
                    <input type="text" name="team_reconciliation_confirmation" value="{{ old('team_reconciliation_confirmation') }}" autocomplete="off" data-fd-team-reconcile-confirmation>
                    <span class="meta">Required phrase: RECONCILE FOOTBALL TEAMS</span>
                </label>

                <label class="fd-import__check">
                    <input type="checkbox" name="understands_team_reconciliation" value="1" @checked(old('understands_team_reconciliation')) data-fd-team-reconcile-understands>
                    <span>I understand this updates local team metadata from football-data.org.</span>
                </label>

                <div class="fd-import__actions">
                    <button class="btn btn-primary" type="submit" disabled data-fd-team-reconcile-button>Reconcile 48 World Cup Teams</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="toolbar">
                <div>
                    <h2>Guarded real import</h2>
                    <p class="meta">Writes imported demo football data into the local database.</p>
                </div>
                <span class="status-badge">Super Admin / Platform Admin</span>
            </div>

            <div class="fd-import__danger">
                This writes football-data.org World Cup demo data and maps fixtures to Moroccan venues. This is not official FIFA 2030 data.
            </div>

            @if ($errors->any())
                <div class="fd-import__error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.football-data-import.run') }}" class="fd-import" data-fd-import-form>
                @csrf
                <label class="fd-import__field">
                    <span class="meta">Type confirmation</span>
                    <input type="text" name="confirmation" value="{{ old('confirmation') }}" autocomplete="off" data-fd-confirmation>
                    <span class="meta">Required phrase: IMPORT FOOTBALL DATA</span>
                </label>

                <label class="fd-import__check">
                    <input type="checkbox" name="understands_write" value="1" @checked(old('understands_write')) data-fd-understands>
                    <span>I understand this writes teams and matches to the local demo database.</span>
                </label>

                <div class="fd-import__actions">
                    <button class="btn btn-danger" type="submit" disabled data-fd-import-button>Run Guarded Import</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="toolbar">
                <div>
                    <h2>Reconcile flags/results</h2>
                    <p class="meta">Updates imported FD-WC records from the external source.</p>
                </div>
                <span class="status-badge">FD-WC only</span>
            </div>

            <div class="fd-import__banner">
                This updates only FD-WC imported records using football-data.org source data. It does not create foreign venues or fake scores.
            </div>

            <form method="POST" action="{{ route('admin.football-data-import.reconcile') }}" class="fd-import" data-fd-reconcile-form>
                @csrf
                <label class="fd-import__field">
                    <span class="meta">Type confirmation</span>
                    <input type="text" name="reconciliation_confirmation" value="{{ old('reconciliation_confirmation') }}" autocomplete="off" data-fd-reconcile-confirmation>
                    <span class="meta">Required phrase: RECONCILE FOOTBALL DATA</span>
                </label>

                <label class="fd-import__check">
                    <input type="checkbox" name="understands_reconciliation" value="1" @checked(old('understands_reconciliation')) data-fd-reconcile-understands>
                    <span>I understand this updates imported FD-WC teams and matches from the external source.</span>
                </label>

                <div class="fd-import__actions">
                    <button class="btn btn-primary" type="submit" disabled data-fd-reconcile-button>Reconcile flags/results</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="toolbar">
                <div>
                    <h2>Reconcile groups and stages</h2>
                    <p class="meta">Updates FD-WC match structure from the external source.</p>
                </div>
                <span class="status-badge">FD-WC structure only</span>
            </div>

            <div class="fd-import__banner">
                This updates only FD-WC imported matches with group/stage values from football-data.org. It does not fake groups or standings.
            </div>

            <form method="POST" action="{{ route('admin.football-data-import.reconcile-structure') }}" class="fd-import" data-fd-structure-form>
                @csrf
                <label class="fd-import__field">
                    <span class="meta">Type confirmation</span>
                    <input type="text" name="structure_confirmation" value="{{ old('structure_confirmation') }}" autocomplete="off" data-fd-structure-confirmation>
                    <span class="meta">Required phrase: RECONCILE FOOTBALL STRUCTURE</span>
                </label>

                <label class="fd-import__check">
                    <input type="checkbox" name="understands_structure" value="1" @checked(old('understands_structure')) data-fd-structure-understands>
                    <span>I understand this updates FD-WC group and stage metadata from the external source.</span>
                </label>

                <div class="fd-import__actions">
                    <button class="btn btn-primary" type="submit" disabled data-fd-structure-button>Reconcile groups and stages</button>
                </div>
            </form>
        </section>

        @if ($structureResult)
            <section class="panel">
                <div class="toolbar">
                    <div>
                        <h2>Structure reconciliation summary</h2>
                        <p class="meta">football-data.org FD-WC group and stage update.</p>
                    </div>
                    <span class="status-badge">{{ $structureResult['ok'] ? 'Structure complete' : 'Structure blocked' }}</span>
                </div>

                @if (! empty($structureResult['errors']))
                    <div class="fd-import__error">
                        @foreach ($structureResult['errors'] as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @else
                    <div class="fd-import__success">
                        Structure reconciliation completed without errors. FD-WC matches now carry source group and stage metadata.
                    </div>
                @endif

                <div class="fd-import__grid">
                    @foreach ($structureResult['summary'] as $label => $value)
                        @continue(is_array($value))
                        <div class="fd-import__card">
                            <span class="meta">{{ ucwords(str_replace('_', ' ', $label)) }}</span>
                            <strong>{{ $value }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($reconciliationResult)
            <section class="panel">
                <div class="toolbar">
                    <div>
                        <h2>Reconciliation summary</h2>
                        <p class="meta">football-data.org FD-WC source update.</p>
                    </div>
                    <span class="status-badge">{{ $reconciliationResult['ok'] ? 'Reconciliation complete' : 'Reconciliation blocked' }}</span>
                </div>

                @if (! empty($reconciliationResult['errors']))
                    <div class="fd-import__error">
                        @foreach ($reconciliationResult['errors'] as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @else
                    <div class="fd-import__success">
                        Reconciliation completed without errors. FD-WC teams and matches now reflect the external source where available.
                    </div>
                @endif

                <div class="fd-import__grid">
                    @foreach ($reconciliationResult['summary'] as $label => $value)
                        @continue(is_array($value))
                        <div class="fd-import__card">
                            <span class="meta">{{ ucwords(str_replace('_', ' ', $label)) }}</span>
                            <strong>{{ $value }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($importResult)
            <section class="panel">
                <div class="toolbar">
                    <div>
                        <h2>Real import summary</h2>
                        <p class="meta">{{ $importResult['competition']['name'] ?? 'World Cup' }} ({{ $importResult['competition']['code'] ?? 'WC' }})</p>
                    </div>
                    <span class="status-badge">{{ $importResult['ok'] ? 'Import complete' : 'Import blocked' }}</span>
                </div>

                @if (! empty($importResult['errors']))
                    <div class="fd-import__error">
                        @foreach ($importResult['errors'] as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @else
                    <div class="fd-import__success">
                        Import completed without errors. Teams and matches were created or matched idempotently.
                    </div>
                @endif

                <div class="fd-import__grid">
                    @foreach ($importResult['summary'] as $label => $value)
                        @continue(is_array($value))
                        <div class="fd-import__card">
                            <span class="meta">{{ ucwords(str_replace('_', ' ', $label)) }}</span>
                            <strong>{{ $value }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel">
                <h2>DB counts</h2>
                <div class="fd-import__grid">
                    @foreach (($importResult['db_counts_after'] ?? []) as $label => $value)
                        <div class="fd-import__card">
                            <span class="meta">{{ ucwords(str_replace('_', ' ', $label)) }}</span>
                            <strong>{{ $value ?? 'N/A' }}</strong>
                            @if (array_key_exists($label, $importResult['db_counts_before'] ?? []))
                                <span class="meta">Before: {{ $importResult['db_counts_before'][$label] ?? 'N/A' }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($squadPreview ?? null)
            <section class="panel">
                <div class="toolbar">
                    <div>
                        <h2>Players &amp; Coaches Dry Run Summary</h2>
                        <p class="meta">Read-only football-data.org squad preview for matched FD-WC teams.</p>
                    </div>
                    <span class="status-badge">{{ $squadPreview['ok'] ? 'Preview complete' : 'Preview blocked' }}</span>
                </div>

                @if (! empty($squadPreview['errors']))
                    <div class="fd-import__error">
                        @foreach ($squadPreview['errors'] as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @else
                    <div class="fd-import__success">
                        Squad dry run completed without database writes.
                    </div>
                @endif

                <div class="fd-import__grid">
                    @foreach ($squadPreview['summary'] as $label => $value)
                        @continue(is_array($value))
                        <div class="fd-import__card">
                            <span class="meta">{{ ucwords(str_replace('_', ' ', $label)) }}</span>
                            <strong>{{ $value }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel">
                <h2>Team mapping summary</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Source ID</th>
                                <th>Source team</th>
                                <th>Code</th>
                                <th>Local team</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse (($squadPreview['team_mapping']['matched'] ?? []) as $team)
                                <tr>
                                    <td>{{ $team['source_id'] }}</td>
                                    <td>{{ $team['source_name'] }}</td>
                                    <td>{{ $team['source_code'] ?: 'N/A' }}</td>
                                    <td>{{ $team['local_name'] }} <span class="meta">#{{ $team['local_id'] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="meta">No source teams matched local FD-WC real teams.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel">
                <h2>Squad availability summary</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Source team</th>
                                <th>Local team</th>
                                <th>Status</th>
                                <th>Players</th>
                                <th>Coach</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($squadPreview['teams_preview'] as $team)
                                <tr>
                                    <td>{{ $team['source_name'] }}</td>
                                    <td>{{ $team['local_name'] }}</td>
                                    <td>{{ $team['status'] ?: 'N/A' }}</td>
                                    <td>{{ $team['squad_count'] }}</td>
                                    <td>{{ $team['coach_detected'] ? 'Detected' : 'Unavailable' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="meta">No team squad endpoint data available for matched teams.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel">
                <h2>First 12 player previews</h2>
                <div class="table-wrap">
                    <table class="fd-import__table">
                        <thead>
                            <tr>
                                <th>Source person ID</th>
                                <th>Name</th>
                                <th>Team</th>
                                <th>Position</th>
                                <th>Date of birth</th>
                                <th>Nationality</th>
                                <th>Dry-run action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($squadPreview['players_preview'] as $player)
                                <tr>
                                    <td>{{ $player['source_person_id'] ?: 'N/A' }}</td>
                                    <td>{{ $player['name'] }}</td>
                                    <td>{{ $player['team']['name'] }}</td>
                                    <td>{{ $player['position'] ?: ($player['source_position'] ?: 'N/A') }}</td>
                                    <td>{{ $player['date_of_birth'] ?: 'N/A' }}</td>
                                    <td>{{ $player['nationality'] ?: 'N/A' }}</td>
                                    <td><span class="status-badge">{{ $player['would_match_existing'] ? 'would match existing' : 'would create' }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="meta">No players available from the sampled squad payloads.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel">
                <h2>Coach preview summary</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Source person ID</th>
                                <th>Name</th>
                                <th>Team</th>
                                <th>Nationality</th>
                                <th>Storage target</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($squadPreview['coaches_preview'] as $coach)
                                <tr>
                                    <td>{{ $coach['source_person_id'] ?: 'N/A' }}</td>
                                    <td>{{ $coach['name'] }}</td>
                                    <td>{{ $coach['team']['name'] }}</td>
                                    <td>{{ $coach['nationality'] ?: 'N/A' }}</td>
                                    <td>
                                        @if ($coach['storage_target'] === 'blocked')
                                            <span class="alert-danger">{{ $coach['blocked_reason'] }}</span>
                                        @else
                                            {{ $coach['storage_target'] }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="meta">No coach data available from the sampled squad payloads.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if ($squadImportResult ?? null)
            <section class="panel">
                <div class="toolbar">
                    <div>
                        <h2>Players &amp; Coaches Import Summary</h2>
                        <p class="meta">football-data.org squad import for reconciled FD-WC teams.</p>
                    </div>
                    <span class="status-badge">{{ $squadImportResult['ok'] ? 'Import complete' : 'Import blocked' }}</span>
                </div>

                @if (! empty($squadImportResult['errors']))
                    <div class="fd-import__error">
                        @foreach ($squadImportResult['errors'] as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @else
                    <div class="fd-import__success">
                        Squad import completed without changing matches, scores, standings, cities, or stadiums.
                    </div>
                @endif

                <div class="fd-import__grid">
                    @foreach ($squadImportResult['summary'] as $label => $value)
                        @continue(is_array($value))
                        <div class="fd-import__card">
                            <span class="meta">{{ ucwords(str_replace('_', ' ', $label)) }}</span>
                            <strong>{{ $value }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel">
                <h2>Squad import DB counts</h2>
                <div class="fd-import__grid">
                    @foreach (($squadImportResult['db_counts_after'] ?? []) as $label => $value)
                        <div class="fd-import__card">
                            <span class="meta">{{ ucwords(str_replace('_', ' ', $label)) }}</span>
                            <strong>{{ $value ?? 'N/A' }}</strong>
                            @if (array_key_exists($label, $squadImportResult['db_counts_before'] ?? []))
                                <span class="meta">Before: {{ $squadImportResult['db_counts_before'][$label] ?? 'N/A' }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($teamReconciliationResult ?? null)
            <section class="panel">
                <div class="toolbar">
                    <div>
                        <h2>Team reconciliation summary</h2>
                        <p class="meta">football-data.org World Cup team metadata reconciliation.</p>
                    </div>
                    <span class="status-badge">{{ $teamReconciliationResult['ok'] ? 'Reconciliation complete' : 'Reconciliation blocked' }}</span>
                </div>

                @if (! empty($teamReconciliationResult['errors']))
                    <div class="fd-import__error">
                        @foreach ($teamReconciliationResult['errors'] as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @else
                    <div class="fd-import__success">
                        Team reconciliation completed. Players and coaches were not imported.
                    </div>
                @endif

                <div class="fd-import__grid">
                    @foreach ($teamReconciliationResult['summary'] as $label => $value)
                        @continue(is_array($value))
                        <div class="fd-import__card">
                            <span class="meta">{{ ucwords(str_replace('_', ' ', $label)) }}</span>
                            <strong>{{ $value }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel">
                <h2>Teams matched/upgraded/created</h2>
                <div class="table-wrap">
                    <table class="fd-import__table">
                        <thead>
                            <tr>
                                <th>Source ID</th>
                                <th>Source team</th>
                                <th>TLA</th>
                                <th>Local team</th>
                                <th>Strategy</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($teamReconciliationResult['teams'] as $team)
                                <tr>
                                    <td>{{ $team['source_id'] }}</td>
                                    <td>{{ $team['source_name'] }}</td>
                                    <td>{{ $team['source_tla'] ?: 'N/A' }}</td>
                                    <td>{{ $team['local_name'] }} <span class="meta">#{{ $team['local_id'] }}</span></td>
                                    <td>{{ $team['strategy'] }}</td>
                                    <td><span class="status-badge">{{ str_replace('_', ' ', $team['action']) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="meta">No team reconciliation rows returned.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if ($preview)
            <section class="panel">
                <div class="toolbar">
                    <div>
                        <h2>Dry-run summary</h2>
                        <p class="meta">{{ $preview['competition']['name'] ?? 'World Cup' }} ({{ $preview['competition']['code'] ?? 'WC' }})</p>
                    </div>
                    <span class="status-badge">{{ $preview['ok'] ? 'API OK' : 'API unavailable' }}</span>
                </div>

                @if (! empty($preview['errors']))
                    <div class="fd-import__error">
                        @foreach ($preview['errors'] as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="fd-import__grid">
                    @foreach ($preview['summary'] as $label => $value)
                        @continue(is_array($value))
                        <div class="fd-import__card">
                            <span class="meta">{{ ucwords(str_replace('_', ' ', $label)) }}</span>
                            <strong>{{ $value }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel">
                <h2>First 12 teams</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Source ID</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Dry-run action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($preview['teams_preview'] as $team)
                                <tr>
                                    <td>{{ $team['source_id'] }}</td>
                                    <td>{{ $team['name'] }}</td>
                                    <td>{{ $team['code'] ?: 'N/A' }}</td>
                                    <td><span class="status-badge">{{ str_replace('_', ' ', $team['action']) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="meta">No teams returned for preview.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel">
                <h2>First 12 matches</h2>
                <div class="table-wrap">
                    <table class="fd-import__table">
                        <thead>
                            <tr>
                                <th>Source ID</th>
                                <th>Date</th>
                                <th>Fixture</th>
                                <th>Status</th>
                                <th>Stage</th>
                                <th>Morocco venue</th>
                                <th>Dry-run action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($preview['matches_preview'] as $match)
                                <tr>
                                    <td>{{ $match['source_id'] }}</td>
                                    <td>{{ $match['utc_date'] }}</td>
                                    <td>{{ $match['home_team']['name'] }} vs {{ $match['away_team']['name'] }}</td>
                                    <td>{{ $match['normalized_status'] }}</td>
                                    <td>{{ $match['normalized_stage'] }}</td>
                                    <td>
                                        @if ($match['mapping']['blocked'] ?? false)
                                            <span class="alert-danger">{{ $match['mapping']['error'] }}</span>
                                        @else
                                            {{ $match['mapping']['local_stadium_name'] }}<br>
                                            <span class="meta">{{ $match['mapping']['city_name'] }}</span>
                                        @endif
                                    </td>
                                    <td><span class="status-badge">{{ str_replace('_', ' ', $match['action']) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="meta">No matches returned for preview.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </div>

    <script>
        (() => {
            const form = document.querySelector('[data-fd-import-form]');
            if (!form) return;

            const confirmation = form.querySelector('[data-fd-confirmation]');
            const understands = form.querySelector('[data-fd-understands]');
            const button = form.querySelector('[data-fd-import-button]');
            const sync = () => {
                button.disabled = confirmation.value !== 'IMPORT FOOTBALL DATA' || !understands.checked;
            };

            confirmation.addEventListener('input', sync);
            understands.addEventListener('change', sync);
            sync();
        })();

        (() => {
            const form = document.querySelector('[data-fd-reconcile-form]');
            if (!form) return;

            const confirmation = form.querySelector('[data-fd-reconcile-confirmation]');
            const understands = form.querySelector('[data-fd-reconcile-understands]');
            const button = form.querySelector('[data-fd-reconcile-button]');
            const sync = () => {
                button.disabled = confirmation.value !== 'RECONCILE FOOTBALL DATA' || !understands.checked;
            };

            confirmation.addEventListener('input', sync);
            understands.addEventListener('change', sync);
            sync();
        })();

        (() => {
            const form = document.querySelector('[data-fd-structure-form]');
            if (!form) return;

            const confirmation = form.querySelector('[data-fd-structure-confirmation]');
            const understands = form.querySelector('[data-fd-structure-understands]');
            const button = form.querySelector('[data-fd-structure-button]');
            const sync = () => {
                button.disabled = confirmation.value !== 'RECONCILE FOOTBALL STRUCTURE' || !understands.checked;
            };

            confirmation.addEventListener('input', sync);
            understands.addEventListener('change', sync);
            sync();
        })();

        (() => {
            const form = document.querySelector('[data-fd-team-reconcile-form]');
            if (!form) return;

            const confirmation = form.querySelector('[data-fd-team-reconcile-confirmation]');
            const understands = form.querySelector('[data-fd-team-reconcile-understands]');
            const button = form.querySelector('[data-fd-team-reconcile-button]');
            const sync = () => {
                button.disabled = confirmation.value !== 'RECONCILE FOOTBALL TEAMS' || !understands.checked;
            };

            confirmation.addEventListener('input', sync);
            understands.addEventListener('change', sync);
            sync();
        })();

        (() => {
            const form = document.querySelector('[data-fd-squad-import-form]');
            if (!form) return;

            const confirmation = form.querySelector('[data-fd-squad-import-confirmation]');
            const understands = form.querySelector('[data-fd-squad-import-understands]');
            const button = form.querySelector('[data-fd-squad-import-button]');
            const sync = () => {
                button.disabled = confirmation.value !== 'IMPORT FOOTBALL SQUADS' || !understands.checked;
            };

            confirmation.addEventListener('input', sync);
            understands.addEventListener('change', sync);
            sync();
        })();
    </script>
@endsection
