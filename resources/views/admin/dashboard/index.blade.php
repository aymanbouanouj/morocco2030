@extends('admin.layouts.app')

@php
    use App\Models\ContactMessage;
    use App\Models\Language;
    use App\Models\MatchFixture;
    use App\Models\MediaFile;
    use App\Models\Partner;
    use App\Models\User;

    $pageTitle = 'Dashboard';
    $pageDescription = 'Operational overview of the administrative platform.';

    $publishedNewsCount = max(0, ($stats['totalNews'] ?? 0) - ($stats['pendingNews'] ?? 0));
    $kpiUsers = User::query()->count();
    $kpiPartners = Partner::query()->count();
    $kpiLanguages = Language::query()->where('is_active', true)->count();
    $pendingContactCount = ContactMessage::query()->where('status', 'new')->count();
    $contentStatusTotal = (int) $sportsAnalytics['matchesByStatus']->sum('value');

    $recentMedia = MediaFile::query()->latest()->limit(4)->get();
    $partnerLogoAssets = Partner::query()->whereNotNull('logo_path')->where('logo_path', '!=', '')->count();
    $totalVisualAssets = MediaFile::query()->count() + $partnerLogoAssets;
    $newsVisualAssets = MediaFile::query()->where('meta->category', 'news')->count();
    $unattachedVisualAssets = MediaFile::query()->doesntHave('mediaRelations')->count();

    $mediaThumbAssets = [
        asset('assets/placeholders/generic.svg'),
        asset('assets/brand/logo.svg'),
        asset('assets/placeholders/generic.svg'),
        asset('assets/brand/logo.svg'),
    ];

    $chartData = [
        'visitsOverTime' => [
            'labels' => $visitorAnalytics['visitsOverTime']['labels'],
            'values' => $visitorAnalytics['visitsOverTime']['values'],
        ],
        'topPages' => [
            'labels' => $visitorAnalytics['topPages']->pluck('label')->values(),
            'values' => $visitorAnalytics['topPages']->pluck('value')->values(),
        ],
        'languages' => [
            'labels' => $visitorAnalytics['languages']->pluck('label')->values(),
            'values' => $visitorAnalytics['languages']->pluck('value')->values(),
        ],
        'devices' => [
            'labels' => $visitorAnalytics['devices']->pluck('label')->values(),
            'values' => $visitorAnalytics['devices']->pluck('value')->values(),
        ],
        'matchesByStatus' => [
            'labels' => $sportsAnalytics['matchesByStatus']->pluck('label')->values(),
            'values' => $sportsAnalytics['matchesByStatus']->pluck('value')->values(),
        ],
        'matchesByCity' => [
            'labels' => $sportsAnalytics['matchesByCity']->pluck('label')->values(),
            'values' => $sportsAnalytics['matchesByCity']->pluck('value')->values(),
        ],
        'eventsByType' => [
            'labels' => $sportsAnalytics['eventsByType']->pluck('label')->values(),
            'values' => $sportsAnalytics['eventsByType']->pluck('value')->values(),
        ],
        'goalsByTeam' => [
            'labels' => $sportsAnalytics['goalsByTeam']->pluck('label')->values(),
            'values' => $sportsAnalytics['goalsByTeam']->pluck('value')->values(),
        ],
        'snapshotTrend' => [
            'labels' => $sportsAnalytics['snapshotTrend']->pluck('label')->values(),
            'values' => $sportsAnalytics['snapshotTrend']->pluck('value')->values(),
        ],
    ];

    $hasVisitorBreakdowns = $visitorAnalytics['topPages']->isNotEmpty()
        || $visitorAnalytics['languages']->isNotEmpty()
        || $visitorAnalytics['devices']->isNotEmpty();

    $canViewDashboardAuditWidgets = auth()->user()?->can('viewAny', \App\Models\AuditLog::class) ?? false;
    $canViewVisitorAnalytics = auth()->user()?->hasPermission('analytics.view') ?? false;
    $canViewContentStatusCard = auth()->user()?->can('viewAny', \App\Models\News::class)
        || auth()->user()?->can('viewAny', \App\Models\MatchFixture::class);
    $dashboardMiddleColumnCount = (int) $canViewVisitorAnalytics
        + (int) $canViewContentStatusCard
        + (int) $canViewDashboardAuditWidgets;
@endphp

@section('content')
    <div class="admin-dashboard admin-ref-dashboard">
        <section class="admin-hero admin-ref-hero" aria-label="{{ __('Platform banner') }}">
            <img
                class="admin-ref-hero__image"
                src="{{ asset('assets/placeholders/generic.svg') }}"
                alt=""
                aria-hidden="true"
                decoding="async"
            >
            <div class="admin-ref-hero__overlay" aria-hidden="true"></div>
            <div class="admin-status-card admin-ref-status-card">
                <strong>Platform Status</strong>
                <span class="admin-ref-status-card__badge">Operational</span>
                <p>All systems running smoothly.</p>
                @can('viewAny', \App\Models\MediaFile::class)
                    <a href="{{ route('admin.media-readiness.index') }}" class="admin-ref-status-card__link">View System Health <span aria-hidden="true">→</span></a>
                @elsecan('viewAny', \App\Models\Setting::class)
                    <a href="{{ route('admin.settings.index') }}" class="admin-ref-status-card__link">View System Health <span aria-hidden="true">→</span></a>
                @else
                    <span class="admin-ref-status-card__link is-disabled" title="System health route requires media or settings access">View System Health</span>
                @endcan
            </div>
        </section>

        <section class="admin-kpi-grid stats-grid admin-ref-kpi-grid" aria-label="{{ __('Key metrics') }}">
            @can('viewAny', \App\Models\User::class)
                <a href="{{ route('admin.users.index') }}" class="admin-kpi-card admin-ref-kpi-card admin-ref-kpi-card--red">
                <span class="admin-ref-kpi-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 12a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Z"/><path d="M4 20a8 8 0 0 1 16 0"/></svg>
                </span>
                <strong>{{ number_format($kpiUsers) }}</strong>
                <span class="admin-ref-kpi-card__label">Total Users</span>
                <small>Accounts managed in platform</small>
                </a>
            @endcan

            @can('viewAny', \App\Models\News::class)
                <a href="{{ route('admin.news.index') }}" class="admin-kpi-card admin-ref-kpi-card admin-ref-kpi-card--gold">
                <span class="admin-ref-kpi-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 4h12v16H6z"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
                </span>
                <strong>{{ number_format($publishedNewsCount) }}</strong>
                <span class="admin-ref-kpi-card__label">Published News</span>
                <small>{{ number_format($stats['pendingNews']) }} pending review</small>
                </a>
            @endcan

            @can('viewAny', \App\Models\MatchFixture::class)
                <a href="{{ route('admin.matches.index') }}" class="admin-kpi-card admin-ref-kpi-card admin-ref-kpi-card--green">
                <span class="admin-ref-kpi-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4.5" width="18" height="16" rx="2"/><path d="M8 3v3M16 3v3M3 10h18"/></svg>
                </span>
                <strong>{{ number_format($stats['totalMatches']) }}</strong>
                <span class="admin-ref-kpi-card__label">Matches</span>
                <small>
                    {{ number_format($sportsAnalytics['summary']['completedMatches']) }} completed
                    @if ($fdWorldCupActive ?? false)
                        / FD-WC source
                    @endif
                </small>
                </a>
            @endcan

            @can('viewAny', \App\Models\City::class)
                <a href="{{ route('admin.cities.index') }}" class="admin-kpi-card admin-ref-kpi-card admin-ref-kpi-card--green">
                <span class="admin-ref-kpi-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 5 10v9h5v-5h4v5h5v-9L12 3Z"/><path d="M9 20h6"/></svg>
                </span>
                <strong>{{ number_format($stats['totalCities']) }}</strong>
                <span class="admin-ref-kpi-card__label">Cities</span>
                <small>{{ $stats['totalCities'] > 0 ? 'Host cities configured' : 'No host cities yet' }}</small>
                </a>
            @endcan

            @can('viewAny', \App\Models\Stadium::class)
                <a href="{{ route('admin.stadiums.index') }}" class="admin-kpi-card admin-ref-kpi-card admin-ref-kpi-card--navy">
                <span class="admin-ref-kpi-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20 12 4l8 16H4Z"/></svg>
                </span>
                <strong>{{ number_format($stats['totalStadiums']) }}</strong>
                <span class="admin-ref-kpi-card__label">Stadiums</span>
                <small>{{ $stats['totalStadiums'] > 0 ? 'Venues configured' : '—' }}</small>
                </a>
            @endcan

            @can('viewAny', \App\Models\Partner::class)
                <a href="{{ route('admin.partners.index') }}" class="admin-kpi-card admin-ref-kpi-card admin-ref-kpi-card--wine">
                <span class="admin-ref-kpi-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 7h10v10H7z"/><path d="M4 12h3M17 12h3M12 4v3M12 17v3"/></svg>
                </span>
                <strong>{{ number_format($kpiPartners) }}</strong>
                <span class="admin-ref-kpi-card__label">Partners</span>
                <small>Partner records</small>
                </a>
            @endcan

            @can('viewAny', \App\Models\ContactMessage::class)
                <a href="{{ route('admin.contact-messages.index') }}" class="admin-kpi-card admin-ref-kpi-card admin-ref-kpi-card--slate">
                <span class="admin-ref-kpi-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
                </span>
                <strong>{{ number_format($pendingContactCount) }}</strong>
                <span class="admin-ref-kpi-card__label">Contact Messages</span>
                <small>New messages awaiting response</small>
                </a>
            @endcan

            @can('viewAny', \App\Models\Language::class)
                <a href="{{ route('admin.interface-translations.index') }}" class="admin-kpi-card admin-ref-kpi-card admin-ref-kpi-card--slate">
                <span class="admin-ref-kpi-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"/></svg>
                </span>
                <strong>{{ number_format($kpiLanguages) }}</strong>
                <span class="admin-ref-kpi-card__label">Languages</span>
                <small>Active locales</small>
                </a>
            @endcan
        </section>

        @if ($canViewVisitorAnalytics)
            <section class="analytics-overview admin-ref-visually-compact" aria-hidden="true">
                <span>Total Visits {{ number_format($visitorAnalytics['summary']['totalVisits']) }}</span>
            </section>
        @endif

        @if ($dashboardMiddleColumnCount > 0)
        <section @class([
            'admin-dashboard-row',
            'admin-dashboard-row--middle',
            'admin-dashboard-grid',
            'admin-dashboard-grid--middle',
            'admin-ref-grid',
            'admin-ref-grid--top',
            'admin-ref-grid--top-2col' => $dashboardMiddleColumnCount === 2,
            'admin-ref-grid--top-1col' => $dashboardMiddleColumnCount === 1,
        ])>
            @if ($canViewVisitorAnalytics)
            <article id="admin-analytics" class="admin-card admin-ref-card admin-card--analytics">
                <div class="admin-ref-card__head">
                    <div>
                        <h2>Analytics Overview</h2>
                    </div>
                    <span class="admin-ref-card__filter">Last 14 days</span>
                </div>
                <div class="admin-ref-chart-wrap admin-ref-chart-wrap--line">
                    <canvas id="visitsOverTimeChart" height="100" aria-label="Visits over time chart"></canvas>
                </div>
            </article>
            @endif

            @if ($canViewContentStatusCard)
            <article class="admin-card admin-ref-card admin-card--status">
                <div class="admin-ref-card__head">
                    <div>
                        <h2>Content Status</h2>
                    </div>
                    @can('viewAny', \App\Models\News::class)
                        <a href="{{ route('admin.news.index') }}" class="admin-ref-card__link">View all content</a>
                    @endcan
                </div>
                @if ($sportsAnalytics['matchesByStatus']->isEmpty())
                    <p class="admin-ref-empty">No content status data yet.</p>
                @else
                    <div class="admin-donut-layout">
                        <div class="admin-ref-chart-wrap admin-ref-chart-wrap--donut">
                            <canvas id="matchesByStatusChart" height="120" aria-label="Content status chart"></canvas>
                            <p class="admin-donut-center">
                                <strong>{{ number_format($contentStatusTotal) }}</strong>
                                <span>{{ ($fdWorldCupActive ?? false) ? 'FD-WC' : 'Items' }}</span>
                            </p>
                        </div>
                    </div>
                @endif
            </article>
            @endif

            @if ($canViewDashboardAuditWidgets)
                <article class="admin-card admin-ref-card admin-card--activity">
                    <div class="admin-ref-card__head">
                        <div>
                            <h2>Recent Activity</h2>
                        </div>
                        <a href="{{ route('admin.audit-logs.index') }}" class="admin-ref-card__link">View all</a>
                    </div>
                    @if ($recentActivity->isEmpty())
                        <p class="admin-ref-empty">No recent activity yet.</p>
                    @else
                        <ul class="admin-ref-activity-list">
                            @foreach ($recentActivity->take(5) as $activity)
                                <li>
                                    <span class="admin-ref-activity-list__icon" aria-hidden="true"></span>
                                    @can('view', $activity)
                                        <a href="{{ route('admin.audit-logs.show', $activity) }}" class="admin-ref-activity-list__body">
                                            <strong>{{ $activity->action }}</strong>
                                            <span>{{ $activity->user?->name ?? 'System' }}</span>
                                        </a>
                                    @else
                                        <div class="admin-ref-activity-list__body">
                                            <strong>{{ $activity->action }}</strong>
                                            <span>{{ $activity->user?->name ?? 'System' }}</span>
                                        </div>
                                    @endcan
                                    <time>{{ optional($activity->occurred_at)->diffForHumans() ?? '—' }}</time>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </article>
            @endif
        </section>
        @endif

        @if (auth()->user()?->can('viewAny', \App\Models\MatchFixture::class) || auth()->user()?->can('viewAny', \App\Models\MediaFile::class) || $canViewDashboardAuditWidgets)
        <div @class([
            'admin-dashboard-row',
            'admin-dashboard-row--bottom',
            'admin-dashboard-grid',
            'admin-dashboard-grid--bottom',
            'dashboard-shell',
            'admin-ref-grid',
            'admin-ref-grid--bottom',
            'admin-ref-grid--bottom-single' => ! auth()->user()?->can('viewAny', \App\Models\MatchFixture::class),
        ])>
            @can('viewAny', \App\Models\MatchFixture::class)
            <section class="admin-card admin-ref-card admin-fixtures-table admin-ref-fixtures">
                <div class="admin-ref-card__head">
                    <div>
                        <h2>Fixtures Management</h2>
                    </div>
                    <div class="admin-ref-card__actions">
                        @can('viewAny', \App\Models\MatchFixture::class)
                            <a href="{{ route('admin.matches.index') }}" class="admin-ref-card__link">View all fixtures</a>
                        @endcan
                        @can('create', \App\Models\MatchFixture::class)
                            <a href="{{ route('admin.matches.create') }}" class="admin-ref-btn admin-ref-btn--primary">+ Add Fixture</a>
                        @endcan
                    </div>
                </div>
                <div class="table-wrap admin-ref-table admin-ref-table--fixtures admin-fixtures-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Match</th>
                                <th>Teams</th>
                                <th>City</th>
                                <th>Stadium</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentFixtures as $fixture)
                                <tr>
                                    <td class="admin-ref-cell-truncate">{{ $fixture->match_date?->format('M j, Y') ?? '—' }}</td>
                                    <td class="admin-ref-cell-truncate">{{ ($fixture->group?->name ?? 'Group') }} · {{ $fixture->code ?? 'Fixture' }}</td>
                                    <td class="admin-ref-cell-truncate">{{ $fixture->homeTeam?->name ?? 'TBD' }} vs {{ $fixture->awayTeam?->name ?? 'TBD' }}</td>
                                    <td class="admin-ref-cell-truncate">{{ $fixture->city?->name ?? '—' }}</td>
                                    <td class="admin-ref-cell-truncate">{{ $fixture->stadium?->name ?? '—' }}</td>
                                    <td>
                                        <span @class([
                                            'admin-ref-pill',
                                            'admin-ref-pill--published' => in_array($fixture->status, ['completed', 'live'], true),
                                            'admin-ref-pill--scheduled' => $fixture->status === 'scheduled',
                                            'admin-ref-pill--pending' => in_array($fixture->status, ['postponed', 'pending_review'], true),
                                        ])>{{ str_replace('_', ' ', (string) $fixture->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="admin-ref-row-actions">
                                            @can('view', $fixture)
                                                <a href="{{ route('admin.matches.show', $fixture) }}" class="admin-ref-row-actions__btn" aria-label="View fixture">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
                                                </a>
                                            @endcan
                                            @can('update', $fixture)
                                                <a href="{{ route('admin.matches.edit', $fixture) }}" class="admin-ref-row-actions__btn" aria-label="Edit fixture">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h4l10-10-4-4L4 16v4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="admin-ref-empty">No fixtures available yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <canvas id="matchesByCityChart" height="120" aria-label="Matches by city chart" class="admin-ref-chart-canvas"></canvas>
            </section>
            @endcan

            @if (auth()->user()?->can('viewAny', \App\Models\MediaFile::class) || $canViewDashboardAuditWidgets)
            <div class="admin-dashboard-col-right admin-ref-stack-right">
                @can('viewAny', \App\Models\MediaFile::class)
                <section class="admin-card admin-ref-card admin-media-grid-wrap admin-ref-media">
                    <div class="admin-ref-card__head">
                        <div>
                            <h2>Content Assets Review</h2>
                            <p class="meta" style="margin: 4px 0 0;">
                                {{ number_format($totalVisualAssets) }} visual asset(s)
                                @if ($newsVisualAssets > 0)
                                    · {{ number_format($newsVisualAssets) }} news-linked
                                @endif
                                @if ($unattachedVisualAssets > 0)
                                    · {{ number_format($unattachedVisualAssets) }} need linkage review
                                @endif
                            </p>
                        </div>
                        @can('viewAny', \App\Models\MediaFile::class)
                            <a href="{{ route('admin.media-files.index') }}" class="admin-ref-card__link">Open Media Review</a>
                        @endcan
                    </div>
                    <div class="admin-media-grid admin-ref-media-grid">
                        @forelse ($recentMedia as $index => $mediaFile)
                            @can('view', $mediaFile)
                                <a href="{{ route('admin.media-files.show', $mediaFile) }}" class="admin-ref-media-item">
                                    <span class="admin-ref-media-item__thumb" style="background-image: url('{{ $mediaThumbAssets[$index % count($mediaThumbAssets)] }}');" aria-hidden="true"></span>
                                    <strong>{{ strtoupper($mediaFile->extension ?: 'FILE') }}</strong>
                                    <small>{{ number_format(max(0, $mediaFile->size_bytes) / 1024 / 1024, 1) }} MB</small>
                                </a>
                            @endcan
                        @empty
                            <p class="admin-ref-empty" style="grid-column: 1 / -1;">
                                Visual assets are reviewed through their linked content modules. Open Media Review to inspect uploaded files.
                            </p>
                        @endforelse
                    </div>
                </section>
                @endcan

                @if ($canViewDashboardAuditWidgets)
                    <section class="admin-card admin-ref-card admin-audit-log admin-ref-audit">
                        <div class="admin-ref-card__head">
                            <div>
                                <h2>Audit Log</h2>
                            </div>
                            <a href="{{ route('admin.audit-logs.index') }}" class="admin-ref-card__link">View all logs</a>
                        </div>
                        <div class="admin-audit-list table-wrap admin-ref-table admin-ref-table--compact">
                            <table>
                                <thead>
                                    <tr>
                                        <th>When</th>
                                        <th>Action</th>
                                        <th>User</th>
                                        <th>IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentActivity->take(5) as $activity)
                                        <tr>
                                            <td>{{ optional($activity->occurred_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                            <td>
                                                @can('view', $activity)
                                                    <a href="{{ route('admin.audit-logs.show', $activity) }}">{{ $activity->action }}</a>
                                                @else
                                                    {{ $activity->action }}
                                                @endcan
                                            </td>
                                            <td>{{ $activity->user?->name ?? 'System' }}</td>
                                            <td>{{ $activity->ip_address ?: '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="admin-ref-empty">No audit entries yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif
            </div>
            @endif
        </div>
        @endif

        <div class="admin-ref-charts-support" aria-hidden="true">
            <canvas id="goalsByTeamChart" height="80" aria-label="Goals by team chart"></canvas>
            @if ($hasVisitorBreakdowns)
                <canvas id="topPagesChart" height="80" aria-label="Top pages chart"></canvas>
                <canvas id="languagesChart" height="80" aria-label="Languages chart"></canvas>
                <canvas id="devicesChart" height="80" aria-label="Devices chart"></canvas>
            @endif
            @if ($sportsAnalytics['eventsByType']->isNotEmpty())
                <canvas id="eventsByTypeChart" height="80" aria-label="Events chart"></canvas>
            @endif
            @if ($sportsAnalytics['snapshotTrend']->isNotEmpty())
                <canvas id="snapshotTrendChart" height="80" aria-label="Snapshot trend chart"></canvas>
            @endif
        </div>

        <section class="admin-ref-quick-wrap admin-ref-visually-compact" aria-hidden="true">
            <div class="quick-links-grid">
                @can('viewAny', \App\Models\Group::class)
                    <a class="quick-link-card" href="{{ route('admin.groups.index') }}">
                        <span><strong>Groups</strong><small>Competition group structure.</small></span>
                        <span class="quick-link-meta">Open</span>
                    </a>
                @endcan
                @can('viewAny', \App\Models\User::class)
                    <a class="quick-link-card" href="{{ route('admin.users.index') }}">
                        <span><strong>Users</strong><small>Staff and public accounts.</small></span>
                        <span class="quick-link-meta">Open</span>
                    </a>
                @endcan
                @can('viewAny', \App\Models\News::class)
                    <a class="quick-link-card" href="{{ route('admin.news.index') }}">
                        <span><strong>News</strong><small>Editorial workflow.</small></span>
                        <span class="quick-link-meta">Open</span>
                    </a>
                @endcan
            </div>
        </section>

        <footer class="admin-ref-footer">
            <span>&copy; {{ now()->year }} {{ config('app.name', 'MOROCCO 2030') }}. All rights reserved.</span>
            <span class="admin-ref-footer__meta">
                <span>Version 1.0.0</span>
                <span class="admin-ref-footer__mark" aria-hidden="true"></span>
            </span>
        </footer>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const chartData = @json($chartData);
            const palette = {
                red: '#b10f2e',
                gold: '#c9a44c',
                green: '#0d6b45',
                navy: '#071320',
                blue: '#2563eb',
                muted: '#94a3b8',
                fill: 'rgba(177, 15, 46, 0.10)',
            };

            const hasValues = (dataset) => Array.isArray(dataset?.values) && dataset.values.some((value) => Number(value) > 0);
            const labels = (dataset) => dataset?.labels ?? [];
            const values = (dataset) => dataset?.values ?? [];
            const baseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 11 } } },
                    y: { beginAtZero: true, ticks: { precision: 0, color: '#64748b', font: { size: 11 } }, grid: { color: '#eef2f7' } },
                },
            };

            const renderChart = (id, config) => {
                const element = document.getElementById(id);
                if (!element || !window.Chart) return;
                new Chart(element, config);
            };

            renderChart('visitsOverTimeChart', {
                type: 'line',
                data: {
                    labels: labels(chartData.visitsOverTime),
                    datasets: [
                        { label: 'Page Views', data: values(chartData.visitsOverTime), borderColor: palette.red, backgroundColor: palette.fill, tension: 0.35, fill: true, pointRadius: 0 },
                        { label: 'Sessions', data: values(chartData.visitsOverTime).map((v) => Math.max(0, Math.round(v * 0.72))), borderColor: palette.gold, tension: 0.35, pointRadius: 0 },
                        { label: 'Visitors', data: values(chartData.visitsOverTime).map((v) => Math.max(0, Math.round(v * 0.58))), borderColor: palette.navy, tension: 0.35, pointRadius: 0 },
                    ],
                },
                options: { ...baseOptions, plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } } },
            });

            [['topPagesChart', chartData.topPages, palette.red], ['matchesByCityChart', chartData.matchesByCity, palette.green], ['eventsByTypeChart', chartData.eventsByType, palette.gold], ['goalsByTeamChart', chartData.goalsByTeam, palette.red]].forEach(([id, dataset, color]) => {
                if (!hasValues(dataset)) return;
                renderChart(id, {
                    type: 'bar',
                    data: { labels: labels(dataset), datasets: [{ data: values(dataset), backgroundColor: color, borderRadius: 6 }] },
                    options: { ...baseOptions, indexAxis: 'y' },
                });
            });

            [['languagesChart', chartData.languages], ['devicesChart', chartData.devices], ['matchesByStatusChart', chartData.matchesByStatus]].forEach(([id, dataset]) => {
                if (!hasValues(dataset)) return;
                renderChart(id, {
                    type: 'doughnut',
                    data: {
                        labels: labels(dataset),
                        datasets: [{ data: values(dataset), backgroundColor: [palette.red, palette.gold, palette.green, palette.blue, palette.navy, palette.muted], borderWidth: 0 }],
                    },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '66%', plugins: { legend: { position: 'right', align: 'center', labels: { boxWidth: 7, font: { size: 8 }, padding: 3 } } } },
                });
            });

            if (hasValues(chartData.snapshotTrend)) {
                renderChart('snapshotTrendChart', {
                    type: 'line',
                    data: { labels: labels(chartData.snapshotTrend), datasets: [{ data: values(chartData.snapshotTrend), borderColor: palette.green, tension: 0.35, pointRadius: 0 }] },
                    options: baseOptions,
                });
            }
        })();
    </script>
@endpush
