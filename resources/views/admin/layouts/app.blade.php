@php
    use App\Support\PublicLocale;

    $adminCurrentLanguage = app(PublicLocale::class)->apply();
    $adminLocale = app()->getLocale();
    $adminHtmlLang = str_replace('_', '-', $adminCurrentLanguage?->locale ?? $adminLocale);
    $adminDirection = $adminCurrentLanguage?->direction ?? (in_array($adminLocale, ['ar'], true) ? 'rtl' : 'ltr');
@endphp
<!DOCTYPE html>
<html lang="{{ $adminHtmlLang }}" dir="{{ $adminDirection }}" class="admin-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin' }} | {{ config('app.name', 'MOROCCO 2030') }} Admin</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/brand/favicon.svg') }}">
    <style>
        :root {
            --bg: #eef3f8;
            --shell: #0c1a2c;
            --shell-muted: #8ea2bf;
            --panel: #ffffff;
            --line: #d8e0eb;
            --ink: #132238;
            --muted: #61738a;
            --primary: #0f5cc0;
            --primary-dark: #0a458f;
            --danger: #c0392b;
            --success: #1f7a45;
            --warning: #b97316;
            --sidebar-width: clamp(220px, 17vw, 248px);
            --page-gutter: clamp(16px, 2vw, 28px);
            --content-max-width: 1540px;
            --panel-radius: 16px;
            --panel-padding: clamp(16px, 1.4vw, 22px);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink);
            background: var(--bg);
            line-height: 1.5;
        }
        a { color: inherit; text-decoration: none; }
        .admin-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: var(--sidebar-width) minmax(0, 1fr);
        }
        .sidebar {
            background: linear-gradient(180deg, #0d1d31 0%, #102540 100%);
            color: #fff;
            padding: 22px 18px;
            position: sticky;
            top: 0;
            align-self: start;
            min-height: 100vh;
            max-height: 100vh;
            overflow-y: auto;
        }
        .brand {
            margin-bottom: 22px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .brand strong {
            display: block;
            font-size: 20px;
            letter-spacing: 0.02em;
        }
        .brand span {
            display: block;
            margin-top: 4px;
            color: var(--shell-muted);
            font-size: 12px;
        }
        .nav-section { margin-bottom: 18px; }
        .nav-section h2 {
            margin: 0 0 8px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--shell-muted);
        }
        .nav-link {
            display: flex;
            align-items: center;
            min-height: 40px;
            padding: 8px 12px;
            border-radius: 12px;
            color: #dbe5f3;
            margin-bottom: 4px;
            font-size: 13px;
            font-weight: 500;
        }
        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }
        .content {
            min-width: 0;
            padding: var(--page-gutter);
        }
        .content-inner {
            width: min(100%, var(--content-max-width));
            margin-inline: auto;
        }
        .topbar {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: var(--panel-radius);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }
        .topbar h1 {
            margin: 0;
            font-size: 26px;
            line-height: 1.2;
        }
        .topbar p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 13px;
        }
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: var(--panel-radius);
            padding: var(--panel-padding);
            box-shadow: 0 14px 32px rgba(19, 34, 56, 0.05);
            min-width: 0;
        }
        .grid {
            display: grid;
            gap: 18px;
            align-items: start;
        }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .page-stack {
            display: grid;
            gap: 18px;
        }
        .stats-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }
        .stats-card h3 {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .stats-card strong {
            display: block;
            margin-top: 8px;
            font-size: clamp(28px, 2.6vw, 34px);
            line-height: 1.1;
        }
        .stats-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 132px;
            gap: 12px;
        }
        .stats-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
        }
        .dashboard-shell {
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(320px, 0.95fr);
            gap: 18px;
            align-items: start;
        }
        .analytics-overview {
            background:
                radial-gradient(circle at top right, rgba(15, 92, 192, 0.10), transparent 34%),
                var(--panel);
        }
        .analytics-metrics {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
        .analytics-metric {
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.78);
            min-width: 0;
        }
        .analytics-metric span,
        .analytics-metric small {
            display: block;
            color: var(--muted);
            font-size: 12px;
        }
        .analytics-metric span {
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }
        .analytics-metric strong {
            display: block;
            margin: 6px 0 3px;
            font-size: clamp(24px, 2.2vw, 32px);
            line-height: 1.05;
        }
        .analytics-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: stretch;
        }
        .chart-card {
            min-height: 300px;
            display: flex;
            flex-direction: column;
        }
        .chart-card--wide {
            grid-column: 1 / -1;
            min-height: 340px;
        }
        .chart-card canvas {
            width: 100% !important;
            min-height: 210px;
            max-height: 280px;
        }
        .chart-card--wide canvas {
            min-height: 230px;
        }
        .mini-chart-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            min-height: 220px;
        }
        .mini-chart-grid canvas {
            min-height: 190px;
        }
        .analytics-empty {
            margin: auto 0;
            padding: 18px;
            border: 1px dashed var(--line);
            border-radius: 14px;
            background: #f8fbff;
            color: var(--muted);
            font-size: 13px;
            text-align: center;
        }
        .panel-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }
        .panel-header .meta {
            margin-top: 4px;
        }
        .quick-links-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        }
        .quick-link-card {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            min-height: 78px;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, #fbfdff 0%, #f2f6fb 100%);
        }
        .quick-link-card:hover {
            border-color: #bfd0e8;
            background: linear-gradient(180deg, #ffffff 0%, #edf3fa 100%);
        }
        .quick-link-card strong {
            display: block;
            font-size: 15px;
        }
        .quick-link-card small {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.45;
        }
        .quick-link-meta {
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .summary-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
        .split-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: start;
        }
        .subtle-card {
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: linear-gradient(180deg, #fbfdff 0%, #f4f8fc 100%);
            min-width: 0;
        }
        .subtle-card h3 {
            margin: 0 0 10px;
            font-size: 16px;
        }
        .panel-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .detail-grid {
            display: grid;
            gap: 12px 16px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .detail-item strong {
            display: block;
            margin-bottom: 4px;
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .compact-list {
            display: grid;
            gap: 10px;
        }
        .entity-note {
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #f8fbff;
        }
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }
        .toolbar form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .field,
        .stacked-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .field-inline {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        label {
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
        }
        input,
        select,
        textarea {
            width: 100%;
            min-height: 40px;
            padding: 9px 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            font: inherit;
            color: var(--ink);
        }
        textarea { min-height: 120px; resize: vertical; }
        input:focus,
        select:focus,
        textarea:focus {
            outline: 2px solid rgba(15, 92, 192, 0.12);
            border-color: var(--primary);
        }
        .btn,
        .btn-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            padding: 0 15px;
            border-radius: 10px;
            border: 0;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-secondary { background: #edf2f8; color: var(--ink); }
        .btn-danger { background: #fff1ef; color: var(--danger); }
        .btn-link { background: transparent; color: var(--primary); padding: 0; min-height: auto; }
        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid transparent;
            margin-bottom: 16px;
        }
        .alert-success { background: #edf8f1; color: var(--success); border-color: #c7e5d2; }
        .alert-danger { background: #fff2f0; color: var(--danger); border-color: #f2c1bb; }
        .alert-warning { background: #fff7ec; color: var(--warning); border-color: #ead4aa; }
        .table-wrap { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th,
        td {
            padding: 14px 10px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: top;
            font-size: 14px;
        }
        .activity-table td:first-child,
        .activity-table th:first-child {
            white-space: nowrap;
        }
        th {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .table-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #eef2f7;
            color: var(--ink);
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .section-title {
            margin: 0;
            font-size: 18px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }
        .form-grid .full-span {
            grid-column: 1 / -1;
        }
        .checkbox-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }
        .checkbox-card {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fbfcfe;
        }
        .checkbox-card input {
            width: auto;
            min-height: auto;
            margin-top: 3px;
        }
        .meta {
            color: var(--muted);
            font-size: 13px;
        }
        .pagination {
            margin-top: 18px;
        }
        @media (max-width: 1400px) {
            .grid-4 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (max-width: 1180px) {
            .admin-shell { grid-template-columns: clamp(208px, 22vw, 228px) minmax(0, 1fr); }
            .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .dashboard-shell { grid-template-columns: 1fr; }
            .analytics-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 980px) {
            .admin-shell { grid-template-columns: 1fr; }
            .sidebar {
                position: static;
                min-height: auto;
                max-height: none;
                padding-bottom: 10px;
            }
            .topbar,
            .toolbar {
                flex-direction: column;
                align-items: flex-start;
            }
            .topbar-actions {
                width: 100%;
                justify-content: space-between;
            }
            .grid-2,
            .grid-3,
            .grid-4,
            .mini-chart-grid,
            .split-grid,
            .detail-grid,
            .form-grid,
            .checkbox-list {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 720px) {
            .content { padding: 14px; }
            .topbar,
            .panel { padding: 16px; }
            .panel,
            .topbar { border-radius: 14px; }
            .quick-links-grid,
            .stats-grid { grid-template-columns: 1fr; }
            .nav-link { min-height: 38px; }
            .chart-card,
            .chart-card--wide {
                min-height: 280px;
            }
        }

        /* PHASE 4 ADMIN UI REDESIGN */
        :root {
            --admin-red: #b10f2e;
            --admin-red-dark: #6f0d1b;
            --admin-gold: #c9a44c;
            --admin-navy: #071320;
            --admin-navy-soft: #101d31;
            --admin-bg: #f4f6f8;
            --admin-panel: #ffffff;
            --admin-line: #e1e7ef;
            --admin-ring: rgba(201, 164, 76, 0.28);
            --admin-shadow: 0 18px 48px rgba(12, 26, 44, 0.08);
            --bg: var(--admin-bg);
            --shell: var(--admin-navy);
            --panel: var(--admin-panel);
            --line: var(--admin-line);
            --ink: #111827;
            --muted: #64748b;
            --primary: var(--admin-red);
            --primary-dark: var(--admin-red-dark);
            --sidebar-width: clamp(236px, 18vw, 272px);
            --panel-radius: 20px;
            --panel-padding: clamp(17px, 1.55vw, 24px);
        }

        html {
            background: var(--admin-bg);
        }

        body {
            background:
                radial-gradient(circle at 12% -6%, rgba(177, 15, 46, 0.07), transparent 26rem),
                radial-gradient(circle at 92% 0%, rgba(201, 164, 76, 0.10), transparent 24rem),
                linear-gradient(180deg, #f7f4ee 0%, var(--admin-bg) 24rem);
            color: var(--ink);
            font-size: 14px;
        }

        a,
        button,
        input,
        select,
        textarea {
            transition: border-color 160ms ease, background 160ms ease, color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .admin-shell {
            grid-template-columns: var(--sidebar-width) minmax(0, 1fr);
        }

        .sidebar {
            padding: 18px 14px;
            background:
                radial-gradient(circle at 20% 0%, rgba(177, 15, 46, 0.22), transparent 18rem),
                linear-gradient(180deg, #071320 0%, #0b1728 54%, #120914 100%);
            border-inline-end: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 18px 0 42px rgba(7, 19, 32, 0.12);
            scrollbar-width: thin;
            scrollbar-color: rgba(201, 164, 76, 0.42) transparent;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            margin-bottom: 18px;
            padding: 0 4px 18px;
        }

        .brand-mark {
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            width: 42px;
            height: 42px;
            border: 1px solid rgba(201, 164, 76, 0.46);
            border-radius: 14px;
            background:
                radial-gradient(circle at 28% 20%, rgba(255, 255, 255, 0.24), transparent 34%),
                linear-gradient(135deg, var(--admin-red), var(--admin-red-dark));
            color: #fff;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.08em;
            box-shadow: 0 12px 26px rgba(177, 15, 46, 0.28);
        }

        .brand-copy {
            min-width: 0;
        }

        .brand strong {
            font-size: 18px;
            letter-spacing: -0.03em;
            line-height: 1.05;
        }

        .brand span:not(.brand-mark):not(.brand-copy) {
            color: rgba(226, 232, 240, 0.68);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .nav-section {
            margin-bottom: 14px;
        }

        .nav-section h2 {
            margin: 0 8px 7px;
            color: rgba(201, 164, 76, 0.78);
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0.15em;
        }

        .nav-link {
            position: relative;
            min-height: 38px;
            padding: 8px 10px 8px 13px;
            border: 1px solid transparent;
            border-radius: 12px;
            color: rgba(226, 232, 240, 0.78);
            font-size: 13px;
            font-weight: 750;
        }

        .nav-link::before {
            content: "";
            width: 6px;
            height: 6px;
            margin-inline-end: 9px;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.48);
        }

        .nav-link:hover {
            border-color: rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.07);
            color: #fff;
            transform: translateX(2px);
        }

        .nav-link.active {
            border-color: rgba(201, 164, 76, 0.24);
            background:
                linear-gradient(135deg, rgba(177, 15, 46, 0.88), rgba(111, 13, 27, 0.88));
            color: #fff;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1), 0 10px 24px rgba(177, 15, 46, 0.18);
        }

        .nav-link.active::before {
            background: var(--admin-gold);
            box-shadow: 0 0 0 4px rgba(201, 164, 76, 0.14);
        }

        .content {
            padding: clamp(16px, 2vw, 30px);
        }

        .content-inner {
            width: min(100%, 1580px);
        }

        .topbar {
            position: sticky;
            top: 12px;
            z-index: 10;
            padding: clamp(14px, 1.4vw, 20px);
            border-color: rgba(225, 231, 239, 0.86);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: var(--admin-shadow);
            backdrop-filter: blur(14px);
        }

        .topbar-heading {
            min-width: 0;
        }

        .topbar h1 {
            font-size: clamp(24px, 2.4vw, 34px);
            letter-spacing: -0.05em;
        }

        .topbar p {
            max-width: 68rem;
            color: var(--muted);
        }

        .admin-user-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            padding: 7px 10px;
            border: 1px solid var(--admin-line);
            border-radius: 999px;
            background: #f8fafc;
        }

        .admin-user-chip > span {
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            width: 30px;
            height: 30px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--admin-navy), var(--admin-red-dark));
            color: #fff;
            font-size: 12px;
            font-weight: 900;
        }

        .admin-user-chip small,
        .admin-user-chip strong {
            display: block;
            line-height: 1.1;
        }

        .admin-user-chip small {
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .admin-user-chip strong {
            margin-top: 2px;
            max-width: 14rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: var(--ink);
            font-size: 13px;
        }

        .panel {
            border-color: rgba(225, 231, 239, 0.94);
            background: rgba(255, 255, 255, 0.94);
            box-shadow: var(--admin-shadow);
        }

        .panel:hover {
            border-color: rgba(201, 164, 76, 0.24);
        }

        .page-stack,
        .grid,
        .analytics-grid,
        .dashboard-shell {
            gap: clamp(16px, 1.5vw, 22px);
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(205px, 1fr));
            gap: clamp(14px, 1.25vw, 18px);
        }

        .stats-card {
            position: relative;
            overflow: hidden;
            min-height: 136px;
            background:
                radial-gradient(circle at 100% 0%, rgba(201, 164, 76, 0.14), transparent 34%),
                linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
        }

        .stats-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: linear-gradient(180deg, var(--admin-red), var(--admin-gold));
        }

        .stats-card h3,
        .analytics-metric span,
        th,
        .detail-item strong {
            color: #6b7280;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.1em;
        }

        .stats-card strong,
        .analytics-metric strong {
            color: #111827;
            letter-spacing: -0.055em;
        }

        .stats-card p,
        .analytics-metric small,
        .meta {
            color: var(--muted);
        }

        .analytics-overview {
            background:
                radial-gradient(circle at top right, rgba(177, 15, 46, 0.10), transparent 34%),
                linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
        }

        .analytics-metric,
        .quick-link-card,
        .subtle-card,
        .checkbox-card,
        .entity-note {
            border-color: var(--admin-line);
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .analytics-metric {
            border-radius: 16px;
        }

        .analytics-metric strong {
            color: var(--admin-red-dark);
        }

        .chart-card {
            min-height: 318px;
        }

        .chart-card--wide {
            min-height: 360px;
        }

        .chart-card canvas {
            max-width: 100%;
        }

        .analytics-empty {
            border-color: rgba(201, 164, 76, 0.36);
            background: #fffaf0;
            color: #7a5b16;
        }

        .section-title {
            color: #111827;
            font-size: clamp(17px, 1.4vw, 20px);
            letter-spacing: -0.035em;
        }

        .panel-header {
            align-items: center;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(225, 231, 239, 0.82);
        }

        .toolbar {
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(225, 231, 239, 0.82);
        }

        .toolbar form {
            flex: 1 1 auto;
            min-width: min(100%, 28rem);
        }

        .toolbar input,
        .toolbar select {
            width: auto;
            min-width: min(100%, 12rem);
        }

        label {
            color: #374151;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.04em;
        }

        input,
        select,
        textarea {
            min-height: 42px;
            border-color: #d9e1ec;
            border-radius: 12px;
            background: #fff;
        }

        input:hover,
        select:hover,
        textarea:hover {
            border-color: #c8d3e1;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: 0;
            border-color: var(--admin-red);
            box-shadow: 0 0 0 4px rgba(177, 15, 46, 0.10);
        }

        .btn,
        .btn-link {
            border-radius: 12px;
            font-size: 13px;
            font-weight: 800;
        }

        .btn {
            border: 1px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--admin-red), var(--admin-red-dark));
            box-shadow: 0 10px 22px rgba(177, 15, 46, 0.18);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #c81938, var(--admin-red-dark));
            transform: translateY(-1px);
        }

        .btn-secondary {
            border-color: #d9e1ec;
            background: #f8fafc;
            color: #1f2937;
        }

        .btn-secondary:hover {
            border-color: rgba(177, 15, 46, 0.22);
            background: #fff;
        }

        .btn-danger {
            border-color: #ffd4ce;
        }

        .btn-link {
            color: var(--admin-red-dark);
        }

        .btn-link:hover {
            color: var(--admin-red);
            text-decoration: underline;
            text-underline-offset: 0.22em;
        }

        .alert {
            border-radius: 14px;
            box-shadow: 0 10px 24px rgba(12, 26, 44, 0.04);
        }

        .table-wrap {
            border: 1px solid var(--admin-line);
            border-radius: 16px;
            background: #fff;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            min-width: 760px;
        }

        th {
            position: sticky;
            top: 0;
            z-index: 1;
            padding: 12px 12px;
            background: #f8fafc;
        }

        td {
            padding: 13px 12px;
        }

        tbody tr {
            background: #fff;
        }

        tbody tr:hover {
            background: #fff8f8;
        }

        .table-actions {
            align-items: center;
        }

        .status-badge {
            border: 1px solid rgba(100, 116, 139, 0.16);
            background: #f1f5f9;
            color: #334155;
            font-size: 11px;
            font-weight: 850;
        }

        .quick-links-grid {
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        }

        .quick-link-card {
            min-height: 92px;
        }

        .quick-link-card:hover {
            border-color: rgba(177, 15, 46, 0.20);
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(12, 26, 44, 0.08);
        }

        .quick-link-meta {
            color: var(--admin-red-dark);
        }

        .pagination nav,
        nav[role="navigation"] {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }

        .pagination a,
        .pagination span,
        nav[role="navigation"] a,
        nav[role="navigation"] span {
            border-radius: 10px;
        }

        .form-grid {
            gap: clamp(14px, 1.35vw, 20px);
        }

        .checkbox-card:hover {
            border-color: rgba(177, 15, 46, 0.18);
            background: #fff;
        }

        @media (max-width: 1280px) {
            :root {
                --sidebar-width: 224px;
            }

            .nav-link {
                padding-inline: 10px;
            }

            .chart-card,
            .chart-card--wide {
                min-height: 300px;
            }
        }

        @media (max-width: 1024px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: relative;
                min-height: auto;
                max-height: none;
                padding: 14px;
            }

            .brand {
                padding-bottom: 14px;
            }

            .nav-section {
                margin-bottom: 12px;
            }

            .nav-section:not(:first-of-type) {
                padding-top: 2px;
            }

            .nav-section h2 {
                margin-inline: 4px;
            }

            .nav-section {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(144px, 1fr));
                gap: 6px;
            }

            .nav-section h2 {
                grid-column: 1 / -1;
            }

            .nav-link {
                margin-bottom: 0;
            }

            .topbar {
                position: static;
            }

            .topbar-actions {
                align-items: stretch;
            }
        }

        @media (max-width: 768px) {
            .content {
                padding: 14px;
            }

            .topbar {
                align-items: stretch;
            }

            .topbar-actions,
            .topbar-actions form,
            .topbar-actions .btn {
                width: 100%;
            }

            .admin-user-chip {
                width: 100%;
                border-radius: 14px;
            }

            .toolbar {
                align-items: stretch;
            }

            .toolbar form,
            .toolbar input,
            .toolbar select,
            .toolbar .btn,
            .toolbar > .btn,
            .toolbar > a.btn {
                width: 100%;
            }

            table {
                min-width: 680px;
            }

            .chart-card,
            .chart-card--wide {
                min-height: 285px;
            }

            .mini-chart-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .content {
                padding: 10px;
            }

            .sidebar,
            .topbar,
            .panel {
                border-radius: 16px;
            }

            .sidebar {
                margin: 0;
                border-radius: 0 0 18px 18px;
            }

            .brand {
                gap: 10px;
            }

            .brand-mark {
                width: 38px;
                height: 38px;
                border-radius: 12px;
            }

            .nav-section {
                grid-template-columns: 1fr;
            }

            .topbar h1 {
                font-size: 24px;
            }

            .panel-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .stats-card {
                min-height: 118px;
            }

            .analytics-metrics {
                grid-template-columns: 1fr;
            }
        }

        /* PHASE 4 ADMIN SHELL LAYOUT PATCH 1.1 */
        @media (min-width: 769px) {
            :root {
                --sidebar-width: clamp(280px, 20vw, 320px);
            }

            .admin-shell {
                display: grid;
                grid-template-columns: var(--sidebar-width) minmax(0, 1fr);
                align-items: start;
                min-height: 100vh;
            }

            .sidebar {
                width: var(--sidebar-width);
                min-height: 100vh;
                max-height: 100vh;
                position: sticky;
                top: 0;
                align-self: start;
                overflow-y: auto;
            }

            .sidebar .nav-section {
                display: block;
                margin-bottom: 14px;
                padding-top: 0;
            }

            .sidebar .nav-section h2 {
                display: block;
                margin: 0 8px 7px;
            }

            .sidebar .nav-link {
                display: flex;
                width: 100%;
                margin-bottom: 4px;
            }

            .content {
                min-width: 0;
                align-self: start;
            }

            .content-inner {
                min-width: 0;
            }
        }

        @media (max-width: 768px) {
            .admin-shell {
                display: grid;
                grid-template-columns: 1fr;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
                max-height: min(24rem, 52vh);
                position: relative;
                overflow-y: auto;
                border-radius: 0 0 18px 18px;
            }

            .sidebar .brand {
                margin-bottom: 12px;
                padding-bottom: 12px;
            }

            .sidebar .nav-section {
                margin-bottom: 10px;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                max-height: min(20rem, 48vh);
            }
        }

        /* PHASE 4 ADMIN MOBILE NAVIGATION PATCH 1.2 */
        @media (max-width: 768px) {
            .sidebar {
                padding: 10px;
                max-height: min(16rem, 34vh);
                overscroll-behavior: contain;
            }

            .sidebar .brand {
                gap: 9px;
                margin-bottom: 8px;
                padding: 0 2px 9px;
            }

            .sidebar .brand-mark {
                width: 34px;
                height: 34px;
                border-radius: 11px;
                font-size: 11px;
            }

            .sidebar .brand strong {
                font-size: 15px;
                line-height: 1;
            }

            .sidebar .brand span:not(.brand-mark):not(.brand-copy) {
                margin-top: 2px;
                font-size: 9px;
                letter-spacing: 0.06em;
            }

            .sidebar .nav-section {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 4px;
                margin-bottom: 8px;
            }

            .sidebar .nav-section h2 {
                grid-column: 1 / -1;
                margin: 1px 2px 2px;
                font-size: 9px;
                letter-spacing: 0.1em;
            }

            .sidebar .nav-link {
                min-height: 31px;
                margin-bottom: 0;
                padding: 6px 8px;
                border-radius: 10px;
                font-size: 12px;
                line-height: 1.1;
            }

            .sidebar .nav-link::before {
                width: 5px;
                height: 5px;
                margin-inline-end: 7px;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                max-height: min(13.25rem, 30vh);
            }

            .sidebar .nav-section {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .sidebar .nav-link {
                min-height: 30px;
                padding-inline: 7px;
                font-size: 11.5px;
            }
        }

        /* PHASE 4 ADMIN CRUD SURFACES VISUAL CONSISTENCY */
        .panel > .toolbar:first-child {
            margin: calc(var(--panel-padding) * -1) calc(var(--panel-padding) * -1) 18px;
            padding: 14px var(--panel-padding);
            border-bottom: 1px solid rgba(225, 231, 239, 0.9);
            border-radius: var(--panel-radius) var(--panel-radius) 0 0;
            background:
                linear-gradient(135deg, rgba(248, 250, 252, 0.96), rgba(255, 255, 255, 0.96)),
                radial-gradient(circle at 0% 0%, rgba(201, 164, 76, 0.12), transparent 18rem);
        }

        .toolbar > .btn,
        .toolbar > a.btn {
            flex: 0 0 auto;
            white-space: nowrap;
        }

        .toolbar form {
            align-items: stretch;
        }

        .toolbar form .btn {
            min-width: 5.75rem;
        }

        .table-wrap {
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        table {
            font-size: 13px;
        }

        th,
        td {
            padding: 12px 11px;
            vertical-align: middle;
        }

        tbody tr {
            transition: background 140ms ease;
        }

        tbody tr:hover {
            background: #fbfcfe;
        }

        td strong {
            color: #101827;
            font-weight: 850;
            letter-spacing: -0.01em;
        }

        td .meta,
        .stacked-field .meta,
        .checkbox-card .meta {
            color: #697789;
            font-size: 12px;
            line-height: 1.45;
        }

        td[colspan].meta {
            padding: 22px 16px;
            text-align: center;
            color: #748195;
            background:
                linear-gradient(135deg, rgba(248, 250, 252, 0.92), rgba(255, 255, 255, 0.96));
        }

        .table-actions {
            gap: 6px;
            justify-content: flex-end;
            min-width: 10rem;
        }

        .table-actions form {
            display: inline-flex;
            margin: 0;
        }

        .table-actions .btn-link {
            min-height: 30px;
            padding: 0 9px;
            border: 1px solid transparent;
            border-radius: 999px;
            background: #f8fafc;
            text-decoration: none;
            white-space: nowrap;
        }

        .table-actions .btn-link:hover {
            border-color: rgba(177, 15, 46, 0.16);
            background: #fff;
            text-decoration: none;
        }

        .table-actions form .btn-link {
            color: #b42318;
            background: #fff7f5;
        }

        .admin-venue-table-wrap {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: visible;
            padding-right: 8px;
        }

        .admin-venue-table {
            min-width: 760px;
            table-layout: auto;
        }

        .admin-venue-actions-heading,
        .admin-venue-actions-cell {
            width: 190px;
            min-width: 190px;
            text-align: right;
            white-space: nowrap;
        }

        .admin-venue-actions-cell {
            padding-right: 14px;
            vertical-align: middle;
        }

        .admin-venue-actions {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: nowrap;
            gap: 8px;
            min-width: max-content;
            width: 100%;
        }

        .admin-venue-actions form {
            flex: 0 0 auto;
        }

        .admin-venue-actions .btn-link {
            white-space: nowrap;
        }

        .status-badge {
            min-height: 25px;
            padding: 4px 9px;
            border-radius: 999px;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .form-grid {
            align-items: start;
        }

        .stacked-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }

        .stacked-field label,
        .field label,
        .checkbox-card strong {
            color: #1f2937;
        }

        input,
        select,
        textarea {
            font-size: 14px;
        }

        textarea {
            line-height: 1.55;
        }

        input[type="file"] {
            padding: 8px;
            background: #f8fafc;
        }

        .checkbox-list {
            gap: 9px;
        }

        .checkbox-card {
            align-items: flex-start;
            min-width: 0;
            padding: 12px;
            border-radius: 14px;
        }

        .checkbox-card input {
            flex: 0 0 auto;
        }

        .field-inline {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        form > .field-inline:last-child,
        .panel + .field-inline {
            padding: 14px 0 0;
        }

        .detail-grid {
            gap: 12px;
        }

        .detail-item,
        .entity-note,
        .subtle-card {
            border-color: rgba(225, 231, 239, 0.92);
        }

        .detail-item strong,
        .entity-note strong,
        .subtle-card h3 {
            color: #17212b;
        }

        .alert ul {
            line-height: 1.55;
        }

        .pagination,
        nav[role="navigation"] {
            margin-top: 16px;
        }

        @media (max-width: 1024px) {
            .table-actions {
                justify-content: flex-start;
                min-width: 9rem;
            }
        }

        @media (max-width: 768px) {
            .panel > .toolbar:first-child {
                margin: -16px -16px 16px;
                padding: 13px 16px;
            }

            .toolbar form {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                width: 100%;
            }

            .toolbar form input,
            .toolbar form select,
            .toolbar form .btn {
                width: 100%;
                min-width: 0;
            }

            th,
            td {
                padding: 11px 10px;
            }

            .table-actions .btn-link {
                min-height: 29px;
            }

            .form-grid {
                gap: 13px;
            }
        }

        @media (max-width: 480px) {
            .panel > .toolbar:first-child {
                margin: -16px -16px 14px;
            }

            .toolbar form {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .toolbar > .btn,
            .toolbar > a.btn {
                width: 100%;
            }

            table {
                font-size: 12.5px;
            }

            th,
            td {
                padding: 10px 9px;
            }

            .table-actions {
                gap: 5px;
            }

            .table-actions .btn-link {
                min-height: 28px;
                padding-inline: 8px;
            }

            .checkbox-list {
                grid-template-columns: 1fr;
            }

            .field-inline .btn,
            .field-inline a.btn {
                flex: 1 1 100%;
            }
        }

        /* PHASE 4 ADMIN SPORTS OPERATIONS VISUAL QA */
        .page-stack > .panel .panel-header {
            gap: 16px;
        }

        .summary-grid {
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        }

        .summary-grid .subtle-card,
        .split-grid .subtle-card {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98)),
                radial-gradient(circle at 100% 0%, rgba(201, 164, 76, 0.1), transparent 16rem);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.045);
        }

        .summary-grid .subtle-card::before,
        .split-grid .subtle-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 3px;
            background: linear-gradient(180deg, var(--admin-red), var(--admin-gold));
            opacity: 0.75;
        }

        .subtle-card h3 {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font-size: 15px;
            letter-spacing: -0.01em;
        }

        .subtle-card h3::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--admin-gold);
            box-shadow: 0 0 0 4px rgba(201, 164, 76, 0.14);
        }

        .detail-grid {
            grid-template-columns: repeat(auto-fit, minmax(132px, 1fr));
        }

        .detail-item {
            padding: 11px 12px;
            border: 1px solid rgba(225, 231, 239, 0.92);
            border-radius: 13px;
            background: rgba(255, 255, 255, 0.72);
        }

        .detail-item span {
            display: block;
            color: #111827;
            font-size: 13px;
            font-weight: 750;
            line-height: 1.35;
        }

        .compact-list {
            gap: 8px;
        }

        .entity-note {
            border-radius: 14px;
            background:
                linear-gradient(135deg, #ffffff, #f8fafc);
        }

        .entity-note .meta,
        .subtle-card .meta {
            line-height: 1.5;
        }

        .panel-actions {
            align-items: center;
        }

        .panel-actions form {
            display: inline-flex;
            margin: 0;
        }

        .panel-actions .btn {
            min-height: 38px;
            white-space: nowrap;
        }

        .toolbar > .meta {
            flex: 1 1 auto;
            min-width: min(100%, 18rem);
            color: #4b5563;
            line-height: 1.45;
        }

        .toolbar > .meta strong {
            color: #111827;
            font-weight: 850;
        }

        .toolbar > .panel-actions {
            flex: 0 1 auto;
            justify-content: flex-end;
        }

        .split-grid .subtle-card .table-wrap {
            margin-top: 10px;
            border-radius: 14px;
            box-shadow: none;
        }

        .split-grid .subtle-card table {
            min-width: 560px;
            font-size: 12.5px;
        }

        .split-grid .subtle-card th,
        .split-grid .subtle-card td {
            padding: 10px 9px;
        }

        .page-stack .alert {
            border-radius: 16px;
        }

        @media (max-width: 1024px) {
            .summary-grid {
                grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            }

            .panel-actions {
                gap: 8px;
            }
        }

        @media (max-width: 768px) {
            .page-stack > .panel .panel-header {
                align-items: stretch;
            }

            .panel-header .panel-actions,
            .toolbar > .panel-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .panel-actions .btn,
            .panel-actions form,
            .panel-actions form button {
                flex: 1 1 auto;
            }

            .summary-grid {
                grid-template-columns: 1fr;
                gap: 13px;
            }

            .subtle-card {
                padding: 14px;
            }

            .detail-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 480px) {
            .panel-actions {
                width: 100%;
            }

            .panel-actions .btn,
            .panel-actions form,
            .panel-actions form button,
            .toolbar > .panel-actions .btn {
                width: 100%;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .summary-grid .subtle-card,
            .split-grid .subtle-card {
                border-radius: 16px;
            }

            .split-grid .subtle-card table {
                min-width: 540px;
            }
        }

        /* DESIGN HELL ADMIN PHASE 1.1 — Exact dashboard reference rebuild */
        :root {
            --admin-ref-sidebar-w: 248px;
            --admin-ref-topbar-h: 64px;
            --admin-ref-bg: #f3f4f6;
            --admin-ref-red: #8f0f1f;
            --admin-ref-red-dark: #5c0818;
            --admin-ref-gold: #c9a44c;
            --admin-ref-green: #0d6b45;
        }

        body {
            background: var(--admin-ref-bg);
        }

        .admin-ref-shell {
            min-height: 100vh;
            background: var(--admin-ref-bg);
        }

        .admin-ref-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 60;
            width: var(--admin-ref-sidebar-w);
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 18px 14px 14px;
            background: linear-gradient(180deg, #8f0f1f 0%, #6f0d1b 52%, #420612 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 8px 0 28px rgba(66, 6, 18, 0.18);
            overflow: hidden;
        }

        .admin-ref-sidebar-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .admin-ref-sidebar-logo img {
            width: 56px;
            height: auto;
            object-fit: contain;
        }

        .admin-ref-sidebar-logo strong {
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .admin-ref-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            padding-right: 2px;
            scrollbar-width: thin;
            scrollbar-color: rgba(201, 164, 76, 0.45) transparent;
        }

        .admin-ref-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 38px;
            padding: 8px 12px;
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.82);
            font-size: 13px;
            font-weight: 650;
            text-decoration: none;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .admin-ref-nav-item svg {
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
            opacity: 0.92;
        }

        .admin-ref-nav-item:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .admin-ref-nav-item.is-active {
            background: linear-gradient(135deg, rgba(177, 15, 46, 0.95), rgba(201, 164, 76, 0.82));
            color: #fff;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.18);
        }

        .admin-ref-sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
            padding: 10px 12px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            background: rgba(0, 0, 0, 0.22);
        }

        .admin-ref-sidebar-user__avatar {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--admin-ref-gold), var(--admin-ref-red));
            color: #fff;
            font-size: 12px;
            font-weight: 900;
        }

        .admin-ref-sidebar-user__copy {
            flex: 1 1 auto;
            min-width: 0;
        }

        .admin-ref-sidebar-user__copy strong,
        .admin-ref-sidebar-user__copy span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-ref-sidebar-user__copy strong {
            color: #fff;
            font-size: 12px;
        }

        .admin-ref-sidebar-user__copy span {
            margin-top: 2px;
            color: rgba(255, 255, 255, 0.66);
            font-size: 10px;
        }

        .admin-ref-sidebar-user__chev {
            width: 16px;
            height: 16px;
            color: rgba(255, 255, 255, 0.72);
        }

        .admin-ref-main {
            margin-left: var(--admin-ref-sidebar-w);
            min-width: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-ref-topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: var(--admin-ref-topbar-h);
            padding: 10px 20px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 0 rgba(15, 23, 42, 0.04);
        }

        .admin-ref-topbar__left,
        .admin-ref-topbar__right {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .admin-ref-topbar__right {
            margin-left: auto;
        }

        .admin-ref-topbar__menu {
            display: none;
            place-items: center;
            width: 38px;
            height: 38px;
            padding: 0;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
            color: #334155;
            cursor: pointer;
        }

        .admin-ref-topbar__menu svg {
            width: 18px;
            height: 18px;
        }

        .admin-ref-topbar__brand img {
            display: block;
            height: 38px;
            width: auto;
            object-fit: contain;
        }

        .admin-ref-search {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: min(100%, 280px);
            padding: 0 12px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #f8fafc;
            color: #64748b;
        }

        .admin-ref-search svg {
            width: 16px;
            height: 16px;
            flex: 0 0 auto;
        }

        .admin-ref-search input {
            width: 100%;
            min-height: 38px;
            padding: 0;
            border: 0;
            background: transparent;
            font-size: 13px;
            color: #334155;
        }

        .admin-ref-topbar__icon-btn,
        .admin-ref-topbar__lang {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 38px;
            padding: 0 10px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .admin-ref-topbar__icon-btn {
            position: relative;
            width: 38px;
            padding: 0;
        }

        .admin-ref-topbar__icon-btn svg,
        .admin-ref-topbar__lang svg {
            width: 18px;
            height: 18px;
        }

        .admin-ref-topbar__badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 16px;
            height: 16px;
            padding: 0 4px;
            border-radius: 999px;
            background: #b10f2e;
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            line-height: 16px;
            text-align: center;
        }

        .admin-ref-topbar__user {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-ref-topbar__avatar {
            display: grid;
            place-items: center;
            width: 36px;
            height: 36px;
            border-radius: 999px;
            background: linear-gradient(135deg, #071320, #b10f2e);
            color: #fff;
            font-size: 12px;
            font-weight: 900;
        }

        .admin-ref-topbar__logout .btn {
            min-height: 36px;
            padding: 0 12px;
            font-size: 12px;
        }

        .admin-ref-page-head {
            padding: 14px 20px 0;
        }

        .admin-ref-page-head h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: -0.03em;
        }

        .admin-ref-page-head p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .admin-ref-content {
            flex: 1 1 auto;
            padding: 16px 20px 20px;
            min-width: 0;
        }

        .admin-ref-dashboard {
            display: grid;
            gap: 14px;
            min-width: 0;
        }

        .admin-ref-hero {
            position: relative;
            overflow: hidden;
            min-height: 170px;
            max-height: 190px;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            background: #111827;
        }

        .admin-ref-hero__image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .admin-ref-hero__overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(7, 19, 32, 0.35), rgba(7, 19, 32, 0.08) 55%, rgba(7, 19, 32, 0.2));
        }

        .admin-ref-status-card {
            position: absolute;
            right: 16px;
            bottom: 16px;
            width: min(100%, 250px);
            padding: 14px 16px;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        }

        .admin-ref-status-card__badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
            padding: 3px 8px;
            border-radius: 999px;
            background: rgba(13, 107, 69, 0.12);
            color: #0d6b45;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .admin-ref-status-card__badge::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: #0d6b45;
        }

        .admin-ref-status-card strong {
            display: block;
            color: #111827;
            font-size: 14px;
        }

        .admin-ref-status-card p {
            margin: 4px 0 10px;
            color: #64748b;
            font-size: 12px;
        }

        .admin-ref-status-card__link {
            color: #8f0f1f;
            font-size: 12px;
            font-weight: 800;
        }

        .admin-ref-kpi-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 12px;
        }

        .admin-ref-kpi-card {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 2px 10px;
            padding: 14px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }

        .admin-ref-kpi-card__icon {
            grid-row: 1 / span 3;
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            color: #fff;
        }

        .admin-ref-kpi-card__icon svg {
            width: 18px;
            height: 18px;
        }

        .admin-ref-kpi-card--red .admin-ref-kpi-card__icon { background: linear-gradient(135deg, #c81938, #8f0f1f); }
        .admin-ref-kpi-card--gold .admin-ref-kpi-card__icon { background: linear-gradient(135deg, #d4b35f, #9a7728); }
        .admin-ref-kpi-card--green .admin-ref-kpi-card__icon { background: linear-gradient(135deg, #128052, #0a5a38); }
        .admin-ref-kpi-card--navy .admin-ref-kpi-card__icon { background: linear-gradient(135deg, #334155, #071320); }
        .admin-ref-kpi-card--wine .admin-ref-kpi-card__icon { background: linear-gradient(135deg, #8f0f1f, #420612); }
        .admin-ref-kpi-card--slate .admin-ref-kpi-card__icon { background: linear-gradient(135deg, #64748b, #334155); }

        .admin-ref-kpi-card strong {
            grid-column: 2;
            font-size: 24px;
            line-height: 1;
            color: #111827;
            letter-spacing: -0.04em;
        }

        .admin-ref-kpi-card__label {
            grid-column: 2;
            color: #64748b;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .admin-ref-kpi-card small {
            grid-column: 2;
            color: #94a3b8;
            font-size: 11px;
        }

        .admin-ref-visually-compact {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
        }

        .admin-ref-grid {
            display: grid;
            gap: 14px;
            min-width: 0;
        }

        .admin-ref-grid--top {
            grid-template-columns: minmax(0, 1.5fr) minmax(260px, 0.75fr) minmax(260px, 0.75fr);
        }

        .admin-ref-grid--top-2col {
            grid-template-columns: minmax(0, 1.5fr) minmax(260px, 0.75fr);
        }

        .admin-ref-grid--top-1col {
            grid-template-columns: minmax(0, 1fr);
        }

        .admin-ref-grid--bottom-single {
            grid-template-columns: minmax(0, 1fr);
        }

        .admin-ref-grid--bottom {
            grid-template-columns: minmax(0, 1.45fr) minmax(300px, 0.85fr);
        }

        .admin-ref-card {
            padding: 14px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
            min-width: 0;
        }

        .admin-ref-card__head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .admin-ref-card__head h2 {
            margin: 0;
            font-size: 15px;
            color: #111827;
            letter-spacing: -0.02em;
        }

        .admin-ref-card__head p {
            margin: 3px 0 0;
            color: #64748b;
            font-size: 12px;
        }

        .admin-ref-card__filter,
        .admin-ref-card__link {
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .admin-ref-card__metrics {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
        }

        .admin-ref-card__metrics div span {
            display: block;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .admin-ref-card__metrics div strong {
            color: #8f0f1f;
            font-size: 16px;
        }

        .admin-ref-card canvas {
            width: 100% !important;
            max-height: 240px;
        }

        .admin-ref-activity-list {
            display: grid;
            gap: 0;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .admin-ref-activity-list li {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 10px;
            align-items: start;
            padding: 10px 0;
            border-bottom: 1px solid #eef2f7;
        }

        .admin-ref-activity-list li:last-child {
            border-bottom: 0;
        }

        .admin-ref-activity-list__icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(177, 15, 46, 0.12), rgba(201, 164, 76, 0.16));
        }

        .admin-ref-activity-list strong {
            display: block;
            font-size: 12px;
            color: #111827;
        }

        .admin-ref-activity-list span {
            display: block;
            margin-top: 2px;
            color: #64748b;
            font-size: 11px;
        }

        .admin-ref-activity-list time {
            color: #94a3b8;
            font-size: 10px;
            white-space: nowrap;
        }

        .admin-ref-stack-right {
            display: grid;
            gap: 14px;
            min-width: 0;
        }

        .admin-ref-btn {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            white-space: nowrap;
        }

        .admin-ref-btn--primary {
            background: linear-gradient(135deg, #b10f2e, #8f0f1f);
            color: #fff;
        }

        .admin-ref-table {
            overflow-x: auto;
        }

        .admin-ref-table table {
            width: 100%;
            min-width: 680px;
            border-collapse: collapse;
            font-size: 12px;
        }

        .admin-ref-table th,
        .admin-ref-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #eef2f7;
            text-align: left;
            vertical-align: middle;
        }

        .admin-ref-table th {
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            background: #f8fafc;
        }

        .admin-ref-pill {
            display: inline-flex;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            text-transform: capitalize;
        }

        .admin-ref-pill--published {
            background: rgba(13, 107, 69, 0.12);
            color: #0d6b45;
        }

        .admin-ref-media-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .admin-ref-media-item {
            display: grid;
            gap: 4px;
            padding: 8px;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            background: #fafbfd;
            text-decoration: none;
            color: inherit;
        }

        .admin-ref-media-item__thumb {
            display: block;
            height: 56px;
            border-radius: 8px;
            background: linear-gradient(135deg, #e5e7eb, #f8fafc);
        }

        .admin-ref-media-item strong {
            font-size: 11px;
            color: #111827;
        }

        .admin-ref-media-item small {
            color: #94a3b8;
            font-size: 10px;
        }

        .admin-ref-empty {
            color: #64748b;
            font-size: 12px;
            text-align: center;
        }

        .admin-ref-card canvas.admin-ref-chart-canvas {
            position: absolute;
            width: 1px !important;
            height: 1px !important;
            opacity: 0;
            pointer-events: none;
        }

        .admin-ref-charts-support {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
        }

        .admin-ref-quick-wrap {
            padding-top: 2px;
        }

        .admin-ref-quick-wrap .quick-links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
        }

        .admin-ref-quick-wrap .quick-link-card {
            min-height: 72px;
            padding: 12px;
            border-radius: 12px;
        }

        .admin-ref-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding-top: 4px;
            color: #64748b;
            font-size: 11px;
        }

        @media (max-width: 1280px) {
            .admin-ref-kpi-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .admin-ref-grid--top,
            .admin-ref-grid--bottom {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .admin-ref-sidebar {
                transform: translateX(-100%);
            }

            .admin-ref-main {
                margin-left: 0;
            }

            .admin-ref-topbar__menu {
                display: inline-grid;
            }

            .admin-ref-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .admin-ref-content {
                padding: 12px;
            }

            .admin-ref-topbar {
                padding: 10px 12px;
            }

            .admin-ref-search {
                display: none;
            }

            .admin-ref-kpi-grid {
                grid-template-columns: 1fr;
            }

            .admin-ref-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* ADMIN DASHBOARD HELL PHASE 1.2 — Compact reference microfix */
        :root {
            --admin-ref-sidebar-w: 240px;
            --admin-ref-topbar-h: 64px;
        }

        .admin-ref-sidebar {
            padding: 14px 12px 12px;
        }

        .admin-ref-sidebar-logo {
            gap: 6px;
            margin-bottom: 12px;
            padding-bottom: 10px;
        }

        .admin-ref-sidebar-logo img {
            width: 48px;
        }

        .admin-ref-sidebar-logo strong {
            font-size: 12px;
        }

        .admin-ref-nav {
            gap: 2px;
            scrollbar-width: none;
        }

        .admin-ref-nav::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        .admin-ref-nav-item {
            min-height: 44px;
            padding: 7px 11px;
            border-radius: 11px;
            font-size: 12.5px;
        }

        .admin-ref-nav-item svg {
            width: 17px;
            height: 17px;
        }

        .admin-ref-nav-item.is-active {
            padding: 7px 12px;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
        }

        .admin-ref-sidebar-user {
            margin-top: 10px;
            padding: 10px 11px;
            border-radius: 12px;
        }

        .admin-ref-sidebar-user strong {
            font-size: 12px;
        }

        .admin-ref-sidebar-user small {
            font-size: 10px;
        }

        .admin-ref-topbar {
            min-height: var(--admin-ref-topbar-h);
            padding: 10px 20px;
        }

        .admin-ref-topbar__brand img {
            max-height: 34px;
        }

        .admin-ref-search input {
            min-height: 38px;
            padding: 0 12px 0 36px;
            font-size: 12.5px;
        }

        .admin-ref-content {
            padding: 20px 24px 18px;
            max-width: 1480px;
        }

        .admin-ref-dashboard {
            gap: 16px;
        }

        .admin-ref-hero {
            min-height: 158px;
            max-height: 168px;
            border-radius: 16px;
        }

        .admin-ref-status-card {
            right: 16px;
            bottom: 14px;
            width: min(220px, 34%);
            padding: 12px 14px;
            border-radius: 14px;
        }

        .admin-ref-status-card strong {
            font-size: 14px;
        }

        .admin-ref-status-card p {
            margin: 2px 0 8px;
            font-size: 11px;
        }

        .admin-ref-status-card__badge {
            font-size: 9px;
            padding: 2px 7px;
        }

        .admin-ref-status-card__link {
            font-size: 11px;
        }

        .stats-grid.admin-ref-kpi-grid,
        .admin-ref-kpi-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
            gap: 10px !important;
        }

        .admin-ref-kpi-card {
            min-height: 96px;
            max-height: 108px;
            padding: 11px 12px;
            border-radius: 14px;
            align-content: center;
        }

        .admin-ref-kpi-card__icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
        }

        .admin-ref-kpi-card__icon svg {
            width: 16px;
            height: 16px;
        }

        .admin-ref-kpi-card strong {
            font-size: 20px;
        }

        .admin-ref-kpi-card__label {
            font-size: 10px;
        }

        .admin-ref-kpi-card small {
            font-size: 10px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .admin-ref-grid {
            gap: 16px;
        }

        .admin-ref-grid--top {
            grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) minmax(0, 1.25fr);
            align-items: stretch;
        }

        .admin-ref-grid--bottom {
            grid-template-columns: minmax(0, 1.55fr) minmax(280px, 0.85fr);
            gap: 16px;
        }

        .admin-ref-card {
            padding: 16px 18px;
            border-radius: 16px;
        }

        .admin-ref-card__head {
            margin-bottom: 10px;
        }

        .admin-ref-card__head h2 {
            font-size: 14px;
        }

        .admin-ref-card__metrics {
            margin-bottom: 8px;
            gap: 8px;
        }

        .admin-ref-card__metrics div strong {
            font-size: 14px;
        }

        .admin-ref-card__metrics--inline div span {
            font-size: 9px;
        }

        .admin-ref-chart-wrap {
            position: relative;
            width: 100%;
            min-width: 0;
        }

        .admin-ref-chart-wrap--line {
            height: 220px;
            max-height: 240px;
        }

        .admin-ref-chart-wrap--donut {
            height: 190px;
            max-height: 210px;
            display: grid;
            place-items: center;
        }

        .admin-ref-chart-wrap canvas {
            width: 100% !important;
            height: 100% !important;
            max-height: none !important;
        }

        .admin-ref-card--status .admin-ref-chart-wrap--donut canvas {
            max-width: 180px;
            max-height: 180px;
        }

        .admin-ref-card canvas:not(.admin-ref-chart-wrap canvas) {
            max-height: 220px;
        }

        .admin-ref-activity-list li {
            grid-template-columns: auto 1fr auto;
            gap: 8px;
            padding: 7px 0;
        }

        .admin-ref-activity-list__icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
        }

        .admin-ref-activity-list strong {
            font-size: 11.5px;
        }

        .admin-ref-activity-list span {
            font-size: 10px;
            margin-top: 1px;
        }

        .admin-ref-activity-list time {
            font-size: 9.5px;
        }

        .admin-ref-table--fixtures {
            max-height: 210px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .admin-ref-table--compact table {
            min-width: 0;
        }

        .admin-ref-table th,
        .admin-ref-table td {
            padding: 7px 6px;
            font-size: 11px;
        }

        .admin-ref-table th {
            font-size: 9px;
        }

        .admin-ref-table--compact th,
        .admin-ref-table--compact td {
            padding: 6px 5px;
        }

        .admin-ref-media-grid {
            gap: 8px;
        }

        .admin-ref-media-item {
            padding: 6px;
            border-radius: 10px;
        }

        .admin-ref-media-item__thumb {
            height: 42px;
            border-radius: 7px;
        }

        .admin-ref-media-item strong {
            font-size: 10px;
        }

        .admin-ref-media-item small {
            font-size: 9px;
        }

        .admin-ref-stack-right {
            gap: 16px;
        }

        .admin-ref-quick-wrap {
            padding-top: 0;
        }

        .admin-ref-quick-wrap .quick-link-card {
            min-height: 58px;
            padding: 10px;
        }

        .admin-ref-footer {
            padding-top: 2px;
            font-size: 10px;
        }

        @media (max-width: 1180px) {
            .stats-grid.admin-ref-kpi-grid,
            .admin-ref-kpi-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }

            .admin-ref-kpi-card {
                max-height: none;
            }
        }

        @media (max-width: 1280px) {
            .admin-ref-grid--top,
            .admin-ref-grid--bottom {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .stats-grid.admin-ref-kpi-grid,
            .admin-ref-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 640px) {
            .admin-ref-content {
                padding: 14px 16px;
            }

            .stats-grid.admin-ref-kpi-grid,
            .admin-ref-kpi-grid {
                grid-template-columns: 1fr !important;
            }

            .admin-ref-hero {
                min-height: 140px;
                max-height: none;
            }

            .admin-ref-status-card {
                width: min(200px, 42%);
            }
        }

        /* ADMIN DASHBOARD HELL PHASE 2 — Pixel-close reference dashboard */
        .admin-ref-sidebar {
            background:
                radial-gradient(circle at 20% 0%, rgba(201, 164, 76, 0.12), transparent 42%),
                radial-gradient(circle at 80% 100%, rgba(0, 0, 0, 0.22), transparent 48%),
                linear-gradient(180deg, #8f0f1f 0%, #6f0d1b 52%, #420612 100%);
        }

        .admin-ref-sidebar__brand img,
        .admin-ref-sidebar-logo img {
            width: 58px;
            filter: drop-shadow(0 6px 14px rgba(0, 0, 0, 0.22));
        }

        .admin-ref-nav-item.is-disabled {
            opacity: 0.55;
            cursor: not-allowed;
            pointer-events: none;
        }

        .admin-ref-sidebar-user {
            margin-top: auto;
            text-decoration: none;
            color: inherit;
        }

        a.admin-ref-sidebar-user:hover {
            background: rgba(0, 0, 0, 0.28);
        }

        .admin-ref-topbar__icon-btn,
        .admin-ref-topbar__lang {
            text-decoration: none;
        }

        .admin-ref-topbar__icon-btn.is-disabled,
        .admin-ref-topbar__lang.is-disabled,
        .admin-ref-status-card__link.is-disabled {
            opacity: 0.55;
            cursor: not-allowed;
            pointer-events: none;
        }

        a.admin-ref-topbar__avatar {
            text-decoration: none;
        }

        .admin-ref-search {
            flex: 0 1 320px;
        }

        .admin-ref-hero {
            min-height: 152px;
            max-height: 162px;
        }

        .admin-ref-status-card strong {
            display: block;
            margin-bottom: 4px;
        }

        .admin-ref-status-card__badge {
            margin-bottom: 6px;
        }

        a.admin-ref-kpi-card {
            display: grid;
            grid-template-columns: auto 1fr;
            grid-template-rows: auto auto auto;
            gap: 2px 10px;
            text-decoration: none;
            color: inherit;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        a.admin-ref-kpi-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .admin-ref-dashboard-grid,
        .admin-ref-grid--top {
            grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) minmax(0, 1.2fr);
        }

        .admin-ref-card__actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .admin-ref-donut__total {
            margin: 0 0 6px;
            color: #64748b;
            font-size: 11px;
        }

        .admin-ref-donut__total strong {
            color: #111827;
            font-size: 18px;
        }

        .admin-ref-chart-wrap--line {
            height: 240px;
            max-height: 250px;
        }

        .admin-ref-chart-wrap--donut {
            height: 170px;
            max-height: 180px;
        }

        .admin-ref-activity-list__body {
            display: block;
            min-width: 0;
            text-decoration: none;
            color: inherit;
        }

        a.admin-ref-activity-list__body:hover strong {
            color: #8f0f1f;
        }

        .admin-ref-row-actions {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .admin-ref-row-actions__btn {
            display: inline-grid;
            place-items: center;
            width: 28px;
            height: 28px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            color: #64748b;
            text-decoration: none;
        }

        .admin-ref-row-actions__btn svg {
            width: 14px;
            height: 14px;
        }

        .admin-ref-row-actions__btn:hover {
            color: #8f0f1f;
            border-color: rgba(143, 15, 31, 0.25);
        }

        .admin-ref-pill--scheduled {
            background: rgba(37, 99, 235, 0.12);
            color: #2563eb;
        }

        .admin-ref-pill--pending {
            background: rgba(201, 164, 76, 0.16);
            color: #9a7728;
        }

        .admin-ref-media-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .admin-ref-footer__meta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .admin-ref-footer__mark {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            background: linear-gradient(135deg, #8f0f1f, #c9a44c);
            box-shadow: 0 4px 10px rgba(143, 15, 31, 0.18);
        }

        @media (max-width: 1100px) {
            .admin-ref-media-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .admin-ref-sidebar.is-open {
                transform: translateX(0);
            }
        }

        /* ADMIN DASHBOARD HELL PHASE 3 — Reference logic dashboard */
        body:has(.admin-ref-shell) {
            overflow-x: clip;
        }

        .admin-ref-shell {
            --admin-ref-sidebar-w: 240px;
            display: grid;
            grid-template-columns: var(--admin-ref-sidebar-w) minmax(0, 1fr);
            width: 100%;
            max-width: 100vw;
            min-height: 100vh;
            overflow-x: clip;
            background: var(--admin-ref-bg, #f3f4f6);
        }

        .admin-ref-shell .admin-ref-sidebar {
            position: sticky;
            top: 0;
            left: auto;
            inset: auto;
            z-index: 60;
            width: 100%;
            max-width: var(--admin-ref-sidebar-w);
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
        }

        .admin-ref-shell .admin-ref-main {
            margin-left: 0 !important;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            overflow-x: hidden;
        }

        .admin-ref-shell .admin-ref-topbar {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            flex-wrap: nowrap;
            gap: 10px;
            padding: 8px 16px;
        }

        .admin-ref-shell .admin-ref-topbar__left {
            flex: 0 1 auto;
            min-width: 0;
        }

        .admin-ref-shell .admin-ref-topbar__right {
            flex: 1 1 auto;
            justify-content: flex-end;
            min-width: 0;
            gap: 8px;
        }

        .admin-ref-shell .admin-ref-topbar__brand img {
            max-height: 32px;
            width: auto;
        }

        .admin-ref-shell .admin-ref-search {
            flex: 1 1 140px;
            max-width: 260px;
            min-width: 0;
        }

        .admin-ref-shell .admin-ref-search input {
            min-width: 0;
            width: 100%;
        }

        .admin-ref-shell .admin-ref-content {
            width: 100%;
            max-width: 100% !important;
            padding: 16px 18px 14px;
            min-width: 0;
            overflow-x: hidden;
        }

        .admin-ref-shell .admin-ref-dashboard {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            display: grid;
            gap: 14px;
        }

        .admin-ref-shell .admin-ref-hero {
            width: 100%;
            min-height: 150px;
            max-height: 160px;
        }

        .admin-ref-shell .admin-ref-status-card {
            width: min(210px, 38%);
            padding: 10px 12px;
        }

        .admin-ref-shell .stats-grid.admin-ref-kpi-grid,
        .admin-ref-shell .admin-ref-kpi-grid {
            display: grid !important;
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
            gap: 8px !important;
            width: 100%;
            min-width: 0;
        }

        .admin-ref-shell .admin-ref-kpi-card {
            min-height: 92px;
            max-height: 106px;
            padding: 10px 11px;
            min-width: 0;
        }

        .admin-ref-shell .admin-ref-kpi-card strong {
            font-size: 18px;
        }

        .admin-ref-shell .admin-ref-kpi-card small {
            max-width: 100%;
        }

        .admin-ref-shell .admin-ref-grid {
            width: 100%;
            min-width: 0;
            gap: 12px;
        }

        .admin-ref-shell .admin-ref-grid--top {
            grid-template-columns: minmax(0, 2fr) minmax(0, 0.92fr) minmax(0, 1.15fr) !important;
            align-items: stretch;
        }

        .admin-ref-shell .admin-ref-grid--bottom {
            grid-template-columns: minmax(0, 1.6fr) minmax(0, 0.84fr) !important;
        }

        .admin-ref-shell .admin-ref-card {
            min-width: 0;
            max-width: 100%;
            padding: 14px 15px;
            overflow: hidden;
        }

        .admin-ref-shell .admin-ref-card__head {
            margin-bottom: 8px;
            min-width: 0;
        }

        .admin-ref-shell .admin-ref-card__head h2 {
            font-size: 13px;
        }

        .admin-ref-shell .admin-ref-card__actions {
            gap: 8px;
        }

        .admin-ref-shell .admin-ref-chart-wrap {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            overflow: hidden;
        }

        .admin-ref-shell .admin-ref-chart-wrap--line {
            height: 228px;
            max-height: 235px;
        }

        .admin-ref-shell .admin-ref-chart-wrap--donut {
            height: 158px;
            max-height: 165px;
        }

        .admin-ref-shell .admin-ref-card--status .admin-ref-chart-wrap--donut canvas {
            max-width: 150px;
            max-height: 150px;
            margin-inline: auto;
        }

        .admin-ref-shell .admin-ref-donut__total {
            margin-bottom: 4px;
        }

        .admin-ref-shell .admin-ref-activity-list li {
            padding: 6px 0;
            min-width: 0;
        }

        .admin-ref-shell .admin-ref-activity-list time {
            flex: 0 0 auto;
        }

        .admin-ref-shell .admin-ref-stack-right {
            min-width: 0;
            gap: 12px;
        }

        .admin-ref-shell .admin-ref-media-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .admin-ref-shell .admin-ref-table {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .admin-ref-shell .admin-ref-dashboard .admin-ref-table table {
            min-width: 0;
            width: 100%;
            table-layout: fixed;
        }

        .admin-ref-shell .admin-ref-table--fixtures table {
            min-width: 560px;
        }

        .admin-ref-shell .admin-ref-table--compact table {
            min-width: 0;
            table-layout: fixed;
        }

        .admin-ref-shell .admin-ref-cell-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 1px;
        }

        .admin-ref-shell .admin-ref-table th,
        .admin-ref-shell .admin-ref-table td {
            padding: 6px 5px;
            font-size: 10.5px;
        }

        .admin-ref-shell .admin-ref-table--fixtures {
            max-height: 196px;
        }

        .admin-ref-shell .admin-ref-sidebar-user__chev {
            pointer-events: none;
            opacity: 0.65;
        }

        .admin-ref-shell .admin-ref-nav-item {
            min-height: 42px;
            padding: 6px 10px;
            font-size: 12px;
        }

        .admin-ref-shell .admin-ref-sidebar-logo img,
        .admin-ref-shell .admin-ref-sidebar__brand img {
            width: 52px;
        }

        @media (max-width: 1360px) {
            .admin-ref-shell .stats-grid.admin-ref-kpi-grid,
            .admin-ref-shell .admin-ref-kpi-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }

            .admin-ref-shell .admin-ref-kpi-card {
                max-height: none;
            }
        }

        @media (max-width: 1180px) {
            .admin-ref-shell .admin-ref-grid--top,
            .admin-ref-shell .admin-ref-grid--bottom {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }

        @media (max-width: 900px) {
            .admin-ref-shell {
                grid-template-columns: minmax(0, 1fr);
            }

            .admin-ref-shell .admin-ref-sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                width: min(var(--admin-ref-sidebar-w), 86vw);
                transform: translateX(-100%);
                transition: transform 0.2s ease;
            }

            .admin-ref-shell .admin-ref-topbar__menu {
                display: inline-grid;
            }

            .admin-ref-shell .stats-grid.admin-ref-kpi-grid,
            .admin-ref-shell .admin-ref-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 640px) {
            .admin-ref-shell .admin-ref-content {
                padding: 12px 14px;
            }

            .admin-ref-shell .admin-ref-topbar__brand {
                display: none;
            }

            .admin-ref-shell .admin-ref-search {
                max-width: none;
            }

            .admin-ref-shell .stats-grid.admin-ref-kpi-grid,
            .admin-ref-shell .admin-ref-kpi-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }

        /* ADMIN DASHBOARD EXACT REFERENCE REBUILD */
        .admin-shell {
            --admin-sidebar-w: 244px;
            --admin-bg: #eef1f5;
            --admin-card-bg: #ffffff;
            --admin-red: #9b1b2e;
            --admin-red-dark: #5c0818;
            --admin-gold: #c9a44c;
            --admin-green: #0d6b45;
            --admin-line: #e5e7eb;
            --admin-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px rgba(15, 23, 42, 0.04);
            display: grid !important;
            grid-template-columns: var(--admin-sidebar-w) minmax(0, 1fr) !important;
            width: 100% !important;
            max-width: 100vw !important;
            min-height: 100vh;
            overflow-x: clip !important;
            background: var(--admin-bg);
            font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
        }

        body:has(.admin-shell) {
            overflow-x: clip;
            background: var(--admin-bg, #eef1f5);
        }

        .admin-shell .admin-sidebar,
        .admin-shell .admin-ref-sidebar {
            position: sticky;
            top: 0;
            width: 100%;
            max-width: var(--admin-sidebar-w);
            height: 100vh;
            padding: 16px 12px 12px;
            background:
                radial-gradient(circle at 18% 0%, rgba(201, 164, 76, 0.14), transparent 40%),
                radial-gradient(circle at 82% 100%, rgba(0, 0, 0, 0.18), transparent 45%),
                linear-gradient(180deg, #9b1b2e 0%, #7a1424 50%, #4a0812 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 6px 0 24px rgba(74, 8, 18, 0.16);
        }

        .admin-shell .admin-sidebar__logo img,
        .admin-shell .admin-ref-sidebar-logo img {
            width: 64px;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2));
        }

        .admin-shell .admin-sidebar__logo strong,
        .admin-shell .admin-ref-sidebar-logo strong {
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.1em;
        }

        .admin-shell .admin-sidebar__nav,
        .admin-shell .admin-ref-nav {
            gap: 2px;
            scrollbar-width: none;
        }

        .admin-shell .admin-sidebar__nav::-webkit-scrollbar,
        .admin-shell .admin-ref-nav::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        .admin-shell .admin-sidebar__item,
        .admin-shell .admin-ref-nav-item {
            min-height: 40px;
            padding: 7px 10px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
        }

        .admin-shell .admin-sidebar__item.is-active,
        .admin-shell .admin-ref-nav-item.is-active {
            background: rgba(255, 255, 255, 0.14);
            box-shadow: inset 0 0 0 1px rgba(201, 164, 76, 0.35);
            color: #fff;
        }

        .admin-shell .admin-sidebar__item.is-disabled,
        .admin-shell .admin-ref-nav-item.is-disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        .admin-shell .admin-sidebar__user,
        .admin-shell .admin-ref-sidebar-user {
            margin-top: auto;
            padding: 9px 10px;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.24);
            text-decoration: none;
            color: inherit;
        }

        .admin-shell .admin-main,
        .admin-shell .admin-ref-main {
            margin-left: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            overflow-x: hidden !important;
            background: var(--admin-bg);
        }

        .admin-shell .admin-content,
        .admin-shell .admin-ref-content {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            padding: 14px 16px 12px !important;
            overflow-x: hidden;
        }

        .admin-shell .admin-topbar,
        .admin-shell .admin-ref-topbar {
            min-height: 64px;
            padding: 8px 16px;
            background: #fff;
            border-bottom: 1px solid var(--admin-line);
            box-shadow: none;
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        .admin-shell .admin-search,
        .admin-shell .admin-ref-search {
            flex: 1 1 120px;
            max-width: 248px;
            min-width: 0;
            height: 38px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid var(--admin-line);
        }

        .admin-shell .admin-search input,
        .admin-shell .admin-ref-search input {
            min-width: 0;
            font-size: 12px;
        }

        .admin-shell .admin-ref-topbar__brand img {
            max-height: 30px;
        }

        .admin-shell .admin-ref-topbar__logout .btn {
            min-height: 34px;
            padding: 0 10px;
            font-size: 11px;
            border-radius: 8px;
        }

        .admin-shell .admin-dashboard,
        .admin-shell .admin-ref-dashboard {
            display: grid;
            gap: 12px;
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        .admin-shell .admin-hero,
        .admin-shell .admin-ref-hero {
            width: 100%;
            min-height: 152px;
            max-height: 158px;
            border-radius: 14px;
            overflow: hidden;
        }

        .admin-shell .admin-status-card,
        .admin-shell .admin-ref-status-card {
            right: 14px;
            bottom: 12px;
            width: min(200px, 36%);
            padding: 10px 12px;
            border-radius: 12px;
            box-shadow: var(--admin-shadow);
        }

        .admin-shell .admin-status-card strong,
        .admin-shell .admin-ref-status-card strong {
            font-size: 13px;
            margin-bottom: 2px;
        }

        .admin-shell .admin-status-card p,
        .admin-shell .admin-ref-status-card p {
            font-size: 10px;
            margin: 0 0 6px;
        }

        .admin-shell .admin-ref-status-card__link {
            font-size: 10px;
            font-weight: 700;
            color: var(--admin-red);
        }

        .admin-shell .admin-kpi-grid,
        .admin-shell .stats-grid.admin-ref-kpi-grid {
            display: grid !important;
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
            gap: 8px !important;
            width: 100%;
            min-width: 0;
        }

        .admin-shell .admin-kpi-card,
        .admin-shell .admin-ref-kpi-card {
            display: grid !important;
            grid-template-columns: auto 1fr;
            grid-template-rows: auto auto auto;
            gap: 1px 8px;
            align-content: center;
            min-height: 98px;
            max-height: 104px;
            padding: 10px;
            border: 1px solid var(--admin-line);
            border-radius: 14px;
            background: var(--admin-card-bg);
            box-shadow: var(--admin-shadow);
            min-width: 0;
            text-decoration: none;
            color: inherit;
        }

        .admin-shell .admin-kpi-card__icon,
        .admin-shell .admin-ref-kpi-card__icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
        }

        .admin-shell .admin-kpi-card strong,
        .admin-shell .admin-ref-kpi-card strong {
            font-size: 19px;
            line-height: 1;
        }

        .admin-shell .admin-kpi-card__label,
        .admin-shell .admin-ref-kpi-card__label {
            font-size: 9px;
        }

        .admin-shell .admin-kpi-card small,
        .admin-shell .admin-ref-kpi-card small {
            font-size: 9px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .admin-shell .admin-dashboard-grid--middle,
        .admin-shell .admin-ref-grid--top {
            display: grid !important;
            grid-template-columns: minmax(0, 2fr) minmax(0, 0.9fr) minmax(0, 1.15fr) !important;
            gap: 12px !important;
            width: 100%;
            min-width: 0;
        }

        .admin-shell .admin-dashboard-grid--bottom,
        .admin-shell .admin-ref-grid--bottom {
            display: grid !important;
            grid-template-columns: minmax(0, 1.62fr) minmax(0, 0.82fr) !important;
            gap: 12px !important;
            width: 100%;
            min-width: 0;
        }

        .admin-shell .admin-card,
        .admin-shell .admin-ref-card {
            min-width: 0;
            max-width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--admin-line);
            border-radius: 14px;
            background: var(--admin-card-bg);
            box-shadow: var(--admin-shadow);
            overflow: hidden;
        }

        .admin-shell .admin-ref-card__head {
            margin-bottom: 8px;
        }

        .admin-shell .admin-ref-card__head h2 {
            font-size: 13px;
            font-weight: 700;
        }

        .admin-shell .admin-ref-chart-wrap--line {
            height: 210px;
            max-height: 220px;
        }

        .admin-shell .admin-donut-layout,
        .admin-shell .admin-ref-chart-wrap--donut {
            position: relative;
            height: 150px;
            max-height: 158px;
            min-width: 0;
        }

        .admin-shell .admin-donut-center {
            position: absolute;
            inset: 0;
            display: grid;
            place-content: center;
            pointer-events: none;
            text-align: center;
            margin-right: 38%;
        }

        .admin-shell .admin-donut-center strong {
            display: block;
            font-size: 16px;
            color: #111827;
            line-height: 1;
        }

        .admin-shell .admin-donut-center span {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .admin-shell .admin-ref-chart-wrap canvas {
            max-width: 100% !important;
        }

        .admin-shell .admin-ref-activity-list li {
            padding: 5px 0;
            min-width: 0;
        }

        .admin-shell .admin-ref-stack-right {
            min-width: 0;
            gap: 10px;
        }

        .admin-shell .admin-media-grid,
        .admin-shell .admin-ref-media-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .admin-shell .admin-ref-media-item__thumb {
            height: 44px;
            border-radius: 8px;
            background: linear-gradient(135deg, #e2e8f0, #f8fafc);
        }

        .admin-shell .admin-fixtures-table,
        .admin-shell .admin-ref-table--fixtures {
            max-width: 100%;
            overflow-x: auto;
            max-height: 188px;
        }

        .admin-shell .admin-fixtures-table table,
        .admin-shell .admin-ref-table--fixtures table {
            min-width: 520px;
            width: 100%;
            table-layout: fixed;
        }

        .admin-shell .admin-audit-log table,
        .admin-shell .admin-ref-table--compact table {
            width: 100%;
            table-layout: fixed;
            min-width: 0;
        }

        .admin-shell .admin-ref-cell-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-shell .admin-ref-table th,
        .admin-shell .admin-ref-table td {
            padding: 5px 4px;
            font-size: 10px;
        }

        .admin-shell .admin-ref-footer {
            font-size: 10px;
            color: #64748b;
            padding-top: 2px;
        }

        .admin-shell .stats-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
        }

        @media (max-width: 992px) {
            .admin-shell .admin-kpi-grid,
            .admin-shell .stats-grid.admin-ref-kpi-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }

            .admin-shell .admin-kpi-card,
            .admin-shell .admin-ref-kpi-card {
                max-height: none;
            }
        }

        @media (max-width: 1140px) {
            .admin-shell .admin-dashboard-grid--middle,
            .admin-shell .admin-ref-grid--top,
            .admin-shell .admin-dashboard-grid--bottom,
            .admin-shell .admin-ref-grid--bottom {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .admin-shell .admin-donut-center {
                margin-right: 0;
            }
        }

        @media (max-width: 900px) {
            .admin-shell {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .admin-shell .admin-sidebar,
            .admin-shell .admin-ref-sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                width: min(var(--admin-sidebar-w), 88vw);
                transform: translateX(-100%);
                transition: transform 0.2s ease;
                z-index: 80;
            }

            .admin-shell .admin-sidebar.is-open,
            .admin-shell .admin-ref-sidebar.is-open {
                transform: translateX(0);
            }

            .admin-shell .admin-ref-topbar__menu {
                display: inline-grid;
            }

            .admin-shell .admin-kpi-grid,
            .admin-shell .stats-grid.admin-ref-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 640px) {
            .admin-shell .admin-kpi-grid,
            .admin-shell .stats-grid.admin-ref-kpi-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .admin-shell .admin-ref-topbar__brand {
                display: none;
            }
        }

        /* ADMIN DASHBOARD REFERENCE-EXACT SECOND PASS */
        .admin-shell {
            --admin-sidebar-w: 252px;
            --admin-gap: 10px;
        }

        .admin-shell .admin-sidebar,
        .admin-shell .admin-ref-sidebar {
            padding: 14px 10px 10px;
        }

        .admin-shell .admin-sidebar__item,
        .admin-shell .admin-ref-nav-item {
            min-height: 38px;
            padding: 6px 9px;
            font-size: 11.5px;
        }

        .admin-shell .admin-topbar,
        .admin-shell .admin-ref-topbar {
            min-height: 70px;
            max-height: 70px;
            padding: 0 16px;
            gap: 8px;
        }

        .admin-shell .admin-ref-topbar__left,
        .admin-shell .admin-ref-topbar__right {
            gap: 8px;
        }

        .admin-shell .admin-search,
        .admin-shell .admin-ref-search {
            flex: 1 1 160px;
            max-width: 300px;
            height: 36px;
        }

        .admin-shell .admin-content,
        .admin-shell .admin-ref-content {
            padding: 12px 14px 10px !important;
        }

        .admin-shell .admin-dashboard,
        .admin-shell .admin-ref-dashboard {
            gap: var(--admin-gap);
        }

        .admin-shell .admin-hero,
        .admin-shell .admin-ref-hero {
            min-height: 156px;
            max-height: 168px;
            border-radius: 12px;
        }

        .admin-shell .admin-status-card,
        .admin-shell .admin-ref-status-card {
            width: min(196px, 34%);
            padding: 9px 11px;
            border-radius: 11px;
        }

        .admin-shell .admin-kpi-grid,
        .admin-shell .stats-grid.admin-ref-kpi-grid,
        .admin-shell .stats-grid.admin-kpi-grid {
            display: grid !important;
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
            gap: 8px !important;
        }

        .admin-shell .admin-kpi-card,
        .admin-shell .admin-ref-kpi-card {
            min-height: 108px;
            max-height: 114px;
            padding: 9px 10px;
            border-radius: 12px;
        }

        .admin-shell .admin-kpi-card strong,
        .admin-shell .admin-ref-kpi-card strong {
            font-size: 18px;
        }

        .admin-shell .admin-kpi-card__icon,
        .admin-shell .admin-ref-kpi-card__icon {
            width: 32px;
            height: 32px;
        }

        .admin-shell .admin-dashboard-row--middle,
        .admin-shell .admin-dashboard-grid--middle,
        .admin-shell .admin-ref-grid--top {
            display: grid !important;
            grid-template-columns: minmax(0, 2fr) minmax(0, 0.86fr) minmax(0, 1.08fr) !important;
            gap: var(--admin-gap) !important;
            align-items: stretch;
        }

        .admin-shell .admin-dashboard-row--bottom,
        .admin-shell .admin-dashboard-grid--bottom,
        .admin-shell .admin-ref-grid--bottom {
            display: grid !important;
            grid-template-columns: minmax(0, 1.48fr) minmax(0, 0.92fr) !important;
            gap: var(--admin-gap) !important;
        }

        .admin-shell .admin-dashboard-col-right,
        .admin-shell .admin-ref-stack-right {
            display: grid;
            gap: var(--admin-gap);
            min-width: 0;
        }

        .admin-shell .admin-card,
        .admin-shell .admin-ref-card {
            padding: 11px 12px;
            border-radius: 12px;
        }

        .admin-shell .admin-ref-card__head {
            margin-bottom: 6px;
        }

        .admin-shell .admin-ref-card__head h2 {
            font-size: 12px;
        }

        .admin-shell .admin-ref-chart-wrap--line {
            height: 188px;
            max-height: 192px;
        }

        .admin-shell .admin-donut-layout,
        .admin-shell .admin-ref-chart-wrap--donut {
            height: 132px;
            max-height: 136px;
        }

        .admin-shell .admin-donut-center {
            margin-right: 42%;
        }

        .admin-shell .admin-donut-center strong {
            font-size: 14px;
        }

        .admin-shell .admin-ref-activity-list li {
            padding: 4px 0;
        }

        .admin-shell .admin-ref-activity-list__icon {
            width: 24px;
            height: 24px;
            border-radius: 7px;
        }

        .admin-shell .admin-media-grid,
        .admin-shell .admin-ref-media-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 6px;
        }

        .admin-shell .admin-ref-media-item {
            padding: 4px;
            gap: 2px;
            border-radius: 8px;
        }

        .admin-shell .admin-ref-media-item__thumb {
            height: 36px;
            border-radius: 6px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-color: #e2e8f0;
        }

        .admin-shell .admin-ref-media-item strong {
            font-size: 9px;
        }

        .admin-shell .admin-ref-media-item small {
            font-size: 8px;
        }

        .admin-shell .admin-fixtures-table,
        .admin-shell .admin-ref-table--fixtures {
            max-height: 176px;
        }

        .admin-shell .admin-audit-list table,
        .admin-shell .admin-ref-table--compact table {
            font-size: 9.5px;
        }

        .admin-shell .admin-ref-table th,
        .admin-shell .admin-ref-table td {
            padding: 4px 3px;
        }

        .admin-shell .admin-ref-btn--primary {
            min-height: 30px;
            padding: 0 10px;
            font-size: 11px;
        }

        .admin-shell .admin-ref-footer {
            font-size: 9px;
            padding-top: 0;
        }

        @media (max-width: 1100px) {
            .admin-shell .admin-media-grid,
            .admin-shell .admin-ref-media-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1080px) {
            .admin-shell .admin-dashboard-row--middle,
            .admin-shell .admin-dashboard-grid--middle,
            .admin-shell .admin-ref-grid--top,
            .admin-shell .admin-dashboard-row--bottom,
            .admin-shell .admin-dashboard-grid--bottom,
            .admin-shell .admin-ref-grid--bottom {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .admin-shell .admin-donut-center {
                margin-right: 0;
            }
        }

        /* SECOND PASS — final specificity overrides (beats legacy admin-ref-shell KPI rules) */
        @media (min-width: 993px) {
            .admin-shell.admin-ref-shell .admin-kpi-grid,
            .admin-shell.admin-ref-shell .stats-grid.admin-ref-kpi-grid,
            .admin-shell.admin-ref-shell .stats-grid {
                display: grid !important;
                grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
                gap: 8px !important;
            }

            .admin-shell.admin-ref-shell .admin-kpi-card,
            .admin-shell.admin-ref-shell .admin-ref-kpi-card {
                min-height: 108px !important;
                max-height: 114px !important;
            }
        }

        .admin-shell.admin-ref-shell .admin-ref-nav-item.is-active {
            background: rgba(255, 255, 255, 0.15) !important;
            box-shadow: inset 0 0 0 1px rgba(201, 164, 76, 0.32) !important;
        }

        .admin-shell.admin-ref-shell .admin-ref-chart-wrap--line {
            height: 188px !important;
            max-height: 192px !important;
        }

        .admin-shell.admin-ref-shell .admin-ref-chart-wrap--donut {
            height: 132px !important;
            max-height: 136px !important;
        }

        .admin-shell.admin-ref-shell .admin-ref-card canvas {
            max-width: 100% !important;
            max-height: 100% !important;
        }

        .admin-shell.admin-ref-shell .admin-dashboard-row--middle,
        .admin-shell.admin-ref-shell .admin-dashboard-grid--middle,
        .admin-shell.admin-ref-shell .admin-ref-grid--top {
            align-items: stretch !important;
        }

        .admin-shell.admin-ref-shell .admin-dashboard-row--middle > .admin-card,
        .admin-shell.admin-ref-shell .admin-ref-grid--top > .admin-ref-card {
            display: flex;
            flex-direction: column;
            min-height: 0;
            max-height: 272px;
            overflow: hidden;
        }

        .admin-shell.admin-ref-shell .admin-ref-chart-wrap {
            flex: 1 1 auto;
            min-height: 0;
        }

        @media (max-width: 992px) {
            .admin-shell.admin-ref-shell .admin-kpi-grid,
            .admin-shell.admin-ref-shell .stats-grid.admin-ref-kpi-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 640px) {
            .admin-shell.admin-ref-shell .admin-kpi-grid,
            .admin-shell.admin-ref-shell .stats-grid.admin-ref-kpi-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .admin-shell.admin-ref-shell .admin-media-grid,
            .admin-shell.admin-ref-shell .admin-ref-media-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        /* ADMIN SIDEBAR ONLY — REFERENCE EXACT FIX */
        .admin-shell {
            --admin-sidebar-w: 252px;
        }

        .admin-shell .admin-sidebar,
        .admin-shell .admin-ref-sidebar {
            display: flex !important;
            flex-direction: column;
            position: sticky !important;
            top: 0;
            inset: auto !important;
            width: 100%;
            max-width: var(--admin-sidebar-w);
            height: 100vh;
            max-height: 100vh;
            padding: 20px 14px 14px;
            overflow: hidden;
            z-index: 60;
            background:
                radial-gradient(circle at 20% 0%, rgba(201, 164, 76, 0.14), transparent 42%),
                radial-gradient(circle at 80% 100%, rgba(0, 0, 0, 0.2), transparent 48%),
                linear-gradient(180deg, #9b1b2e 0%, #7a1424 48%, #4a0812 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 6px 0 24px rgba(74, 8, 18, 0.16);
            transition: transform 0.22s ease, opacity 0.22s ease, max-width 0.22s ease;
        }

        .admin-shell .admin-sidebar__brand,
        .admin-shell .admin-ref-sidebar-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 0 0 auto;
            margin: 0 0 14px;
            padding: 0 4px 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            text-align: center;
        }

        .admin-shell .admin-sidebar__brand .admin-sidebar__logo,
        .admin-shell .admin-sidebar__logo,
        .admin-shell .admin-ref-sidebar-logo img {
            display: block;
            width: min(132px, 100%);
            max-width: 132px;
            height: auto;
            margin: 0 auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.22));
        }

        .admin-shell .admin-sidebar__brand strong,
        .admin-shell .admin-ref-sidebar-logo strong {
            display: none !important;
        }

        .admin-shell .admin-sidebar__nav,
        .admin-shell .admin-ref-nav {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-height: 0;
            gap: 3px;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 2px;
            scrollbar-width: thin;
            scrollbar-color: rgba(201, 164, 76, 0.4) transparent;
        }

        .admin-shell .admin-sidebar__link,
        .admin-shell .admin-sidebar__item,
        .admin-shell .admin-ref-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 38px;
            padding: 8px 12px;
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.86);
            font-size: 12.5px;
            font-weight: 600;
            line-height: 1.2;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
        }

        .admin-shell .admin-sidebar__link svg,
        .admin-shell .admin-sidebar__item svg,
        .admin-shell .admin-ref-nav-item svg {
            width: 17px;
            height: 17px;
            flex: 0 0 auto;
            opacity: 0.92;
        }

        .admin-shell .admin-sidebar__link:hover,
        .admin-shell .admin-sidebar__item:hover,
        .admin-shell .admin-ref-nav-item:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .admin-shell .admin-sidebar__link.is-active,
        .admin-shell .admin-sidebar__item.is-active,
        .admin-shell .admin-ref-nav-item.is-active {
            background: rgba(255, 255, 255, 0.16) !important;
            box-shadow: inset 0 0 0 1px rgba(201, 164, 76, 0.42) !important;
            color: #fff !important;
        }

        .admin-shell .admin-sidebar__link.is-disabled,
        .admin-shell .admin-sidebar__item.is-disabled,
        .admin-shell .admin-ref-nav-item.is-disabled {
            opacity: 0.48;
            cursor: not-allowed;
            pointer-events: none;
        }

        .admin-shell .admin-sidebar__user,
        .admin-shell .admin-ref-sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 0 0 auto;
            margin-top: auto;
            padding: 10px 12px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.26);
            text-decoration: none;
            color: inherit;
        }

        .admin-shell .admin-ref-sidebar-user__avatar {
            flex: 0 0 auto;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
        }

        .admin-shell .admin-ref-sidebar-user__copy {
            flex: 1 1 auto;
            min-width: 0;
        }

        .admin-shell .admin-ref-sidebar-user__copy strong,
        .admin-shell .admin-ref-sidebar-user__copy span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-shell .admin-ref-sidebar-user__copy strong {
            color: #fff;
            font-size: 12px;
            font-weight: 700;
        }

        .admin-shell .admin-ref-sidebar-user__copy span {
            color: rgba(255, 255, 255, 0.68);
            font-size: 10px;
        }

        .admin-shell .admin-ref-sidebar-user__chev {
            flex: 0 0 auto;
            width: 16px;
            height: 16px;
            color: rgba(255, 255, 255, 0.55);
        }

        .admin-shell .admin-sidebar-toggle,
        .admin-shell .admin-ref-topbar__menu {
            display: inline-grid !important;
            place-items: center;
            width: 36px;
            height: 36px;
            padding: 0;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
            color: #334155;
            cursor: pointer;
            flex: 0 0 auto;
        }

        .admin-shell .admin-sidebar-toggle svg,
        .admin-shell .admin-ref-topbar__menu svg {
            width: 18px;
            height: 18px;
        }

        .admin-shell .admin-ref-main {
            margin-left: 0 !important;
        }

        .admin-shell.is-sidebar-collapsed {
            grid-template-columns: 0 minmax(0, 1fr) !important;
        }

        .admin-shell.is-sidebar-collapsed .admin-sidebar,
        .admin-shell.is-sidebar-collapsed .admin-ref-sidebar {
            max-width: 0;
            width: 0;
            min-width: 0;
            padding-left: 0;
            padding-right: 0;
            opacity: 0;
            pointer-events: none;
            border-right: none;
            box-shadow: none;
            overflow: hidden;
        }

        .admin-shell .admin-sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 55;
            border: 0;
            padding: 0;
            margin: 0;
            background: rgba(15, 23, 42, 0.45);
            cursor: pointer;
        }

        .admin-shell.is-sidebar-mobile-open .admin-sidebar-backdrop {
            display: block;
        }

        @media (max-width: 900px) {
            .admin-shell {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .admin-shell .admin-sidebar,
            .admin-shell .admin-ref-sidebar {
                position: fixed !important;
                top: 0;
                left: 0;
                width: min(var(--admin-sidebar-w), 88vw);
                max-width: min(var(--admin-sidebar-w), 88vw);
                transform: translateX(-100%);
                opacity: 1;
                pointer-events: auto;
            }

            .admin-shell .admin-sidebar.is-open,
            .admin-shell .admin-ref-sidebar.is-open {
                transform: translateX(0);
            }

            .admin-shell.is-sidebar-collapsed {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .admin-shell.is-sidebar-collapsed .admin-sidebar,
            .admin-shell.is-sidebar-collapsed .admin-ref-sidebar {
                max-width: min(var(--admin-sidebar-w), 88vw);
                width: min(var(--admin-sidebar-w), 88vw);
                opacity: 1;
            }
        }

        /* ADMIN SIDEBAR FIX — Fixed sidebar and scrolling content */
        html.admin-root,
        body.admin-body {
            height: 100%;
            overflow: hidden;
        }

        body.admin-body .admin-shell {
            display: flex !important;
            flex-direction: row;
            align-items: stretch;
            width: 100%;
            max-width: 100vw;
            height: 100vh;
            max-height: 100vh;
            min-height: 0;
            overflow: hidden;
        }

        body.admin-body .admin-shell .admin-sidebar,
        body.admin-body .admin-shell .admin-ref-sidebar {
            flex: 0 0 var(--admin-sidebar-w);
            width: var(--admin-sidebar-w);
            max-width: var(--admin-sidebar-w);
            position: relative !important;
            top: auto !important;
            left: auto !important;
            align-self: stretch;
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
        }

        body.admin-body .admin-shell .admin-sidebar__nav,
        body.admin-body .admin-shell .admin-ref-nav {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
        }

        body.admin-body .admin-shell .admin-sidebar__user,
        body.admin-body .admin-shell .admin-ref-sidebar-user {
            flex: 0 0 auto;
            flex-shrink: 0;
            margin-top: auto;
        }

        body.admin-body .admin-shell .admin-main,
        body.admin-body .admin-shell .admin-ref-main {
            flex: 1 1 auto;
            min-width: 0;
            width: auto !important;
            max-width: none !important;
            height: 100vh;
            max-height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        body.admin-body .admin-shell .admin-content,
        body.admin-body .admin-shell .admin-ref-content {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
        }

        body.admin-body .admin-shell .admin-topbar,
        body.admin-body .admin-shell .admin-ref-topbar {
            flex: 0 0 auto;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        body.admin-body .admin-shell.is-sidebar-collapsed {
            grid-template-columns: none !important;
        }

        body.admin-body .admin-shell.is-sidebar-collapsed .admin-sidebar,
        body.admin-body .admin-shell.is-sidebar-collapsed .admin-ref-sidebar {
            flex-basis: 0;
            width: 0;
            max-width: 0;
            min-width: 0;
            padding-left: 0;
            padding-right: 0;
            opacity: 0;
            pointer-events: none;
            border-right: none;
            box-shadow: none;
            overflow: hidden;
        }

        @media (max-width: 900px) {
            body.admin-body .admin-shell {
                display: block !important;
                height: 100vh;
                overflow: hidden;
            }

            body.admin-body .admin-shell .admin-sidebar,
            body.admin-body .admin-shell .admin-ref-sidebar {
                position: fixed !important;
                top: 0;
                left: 0;
                flex: none;
                width: min(var(--admin-sidebar-w), 88vw);
                max-width: min(var(--admin-sidebar-w), 88vw);
                height: 100vh;
                max-height: 100vh;
                transform: translateX(-100%);
                opacity: 1;
                pointer-events: auto;
                z-index: 80;
            }

            body.admin-body .admin-shell .admin-sidebar.is-open,
            body.admin-body .admin-shell .admin-ref-sidebar.is-open {
                transform: translateX(0);
            }

            body.admin-body .admin-shell .admin-main,
            body.admin-body .admin-shell .admin-ref-main {
                width: 100% !important;
                height: 100vh;
                max-height: 100vh;
                overflow-y: auto;
            }
        }

        /* ADMIN HELL PHASE 2 — Topbar functionality hardening */
        body.admin-body .admin-topbar,
        body.admin-body .admin-ref-topbar {
            position: relative;
            z-index: 45;
        }

        body.admin-body .admin-topbar__actions,
        body.admin-body .admin-ref-topbar__right {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        body.admin-body .admin-topbar-search {
            position: relative;
            flex: 1 1 140px;
            max-width: 280px;
            min-width: 0;
        }

        body.admin-body .admin-topbar-search__input {
            width: 100%;
            min-width: 0;
            border: 0;
            background: transparent;
            color: #0f172a;
            font-size: 13px;
            outline: none;
        }

        body.admin-body .admin-topbar-search__input::placeholder {
            color: #94a3b8;
        }

        body.admin-body .admin-topbar-search__panel,
        body.admin-body .admin-topbar-command {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            min-width: 100%;
            width: max(100%, 268px);
            z-index: 120;
            max-height: 300px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
            padding: 6px;
        }

        body.admin-body .admin-topbar-search.is-active {
            box-shadow: inset 0 0 0 1px rgba(155, 27, 46, 0.22);
            border-radius: 10px;
            background: #fff;
        }

        body.admin-body .admin-topbar-action {
            position: relative;
            flex: 0 0 auto;
        }

        body.admin-body .admin-topbar-action__btn {
            cursor: pointer;
            border: 0;
            background: transparent;
            font: inherit;
            color: inherit;
            padding: 0;
            text-decoration: none;
        }

        body.admin-body .admin-topbar-action__btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        body.admin-body .admin-topbar-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            z-index: 120;
            min-width: 240px;
            max-width: min(320px, calc(100vw - 24px));
            padding: 8px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12), 0 2px 8px rgba(15, 23, 42, 0.06);
        }

        body.admin-body .admin-topbar-dropdown.is-open {
            display: block !important;
        }

        body.admin-body .admin-topbar-dropdown[hidden] {
            display: none !important;
        }

        body.admin-body .admin-topbar-dropdown__head {
            padding: 6px 8px 8px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 4px;
        }

        body.admin-body .admin-topbar-dropdown__head strong {
            font-size: 12px;
            color: #0f172a;
        }

        body.admin-body .admin-topbar-search__panel .admin-topbar-search__result,
        body.admin-body .admin-topbar-search__panel .admin-topbar-search__empty {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 8px 10px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #0f172a;
            font-size: 13px;
            font-weight: 600;
            text-align: left;
            text-decoration: none;
            cursor: pointer;
        }

        body.admin-body .admin-topbar-search__result-icon {
            flex: 0 0 auto;
            width: 26px;
            height: 26px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: #f1f5f9;
            color: #9b1b2e;
            font-size: 12px;
            font-weight: 700;
        }

        body.admin-body .admin-topbar-search__result-copy {
            flex: 1 1 auto;
            min-width: 0;
        }

        body.admin-body .admin-topbar-search__result-copy strong {
            display: block;
            font-size: 13px;
            color: #0f172a;
        }

        body.admin-body .admin-topbar-search__result-copy small {
            display: block;
            margin-top: 1px;
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
        }

        body.admin-body .admin-topbar-search__panel .admin-topbar-search__result:hover,
        body.admin-body .admin-topbar-search__panel .admin-topbar-search__result:focus-visible {
            background: #f8fafc;
            outline: none;
        }

        body.admin-body .admin-topbar-search__panel .admin-topbar-search__empty {
            color: #64748b;
            font-weight: 500;
            cursor: default;
            display: block;
        }

        body.admin-body .admin-notification-menu {
            min-width: 280px;
        }

        body.admin-body .admin-notification-menu__list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        body.admin-body .admin-notification-menu__item {
            display: flex;
            gap: 10px;
            padding: 8px;
            border-radius: 8px;
        }

        body.admin-body .admin-notification-menu__item strong {
            display: block;
            font-size: 12px;
            color: #0f172a;
        }

        body.admin-body .admin-notification-menu__item span,
        body.admin-body .admin-notification-menu__item a {
            display: block;
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        body.admin-body .admin-notification-menu__item a {
            color: #9b1b2e;
            text-decoration: none;
            font-weight: 600;
        }

        body.admin-body .admin-notification-menu__item a:hover {
            text-decoration: underline;
        }

        body.admin-body .admin-notification-menu__dot {
            flex: 0 0 auto;
            width: 8px;
            height: 8px;
            margin-top: 5px;
            border-radius: 50%;
        }

        body.admin-body .admin-notification-menu__dot--ok { background: #0d6b45; }
        body.admin-body .admin-notification-menu__dot--muted { background: #94a3b8; }
        body.admin-body .admin-notification-menu__dot--warn { background: #b97316; }
        body.admin-body .admin-notification-menu__dot--info { background: #0f5cc0; }

        body.admin-body .admin-language-menu__list,
        body.admin-body .admin-user-menu__list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        body.admin-body .admin-language-menu__item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            color: #0f172a;
            text-decoration: none;
            font-size: 12px;
        }

        body.admin-body .admin-language-menu__item:hover {
            background: #f8fafc;
        }

        body.admin-body .admin-language-menu__item.is-active {
            background: #fef2f2;
            box-shadow: inset 0 0 0 1px rgba(155, 27, 46, 0.2);
        }

        body.admin-body .admin-language-menu__item strong {
            font-size: 12px;
            font-weight: 600;
        }

        body.admin-body .admin-user-menu {
            min-width: 220px;
        }

        body.admin-body .admin-user-menu__head {
            padding: 8px 10px 10px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 4px;
        }

        body.admin-body .admin-user-menu__head strong {
            display: block;
            font-size: 13px;
            color: #0f172a;
        }

        body.admin-body .admin-user-menu__head span {
            display: block;
            margin-top: 2px;
            font-size: 11px;
            color: #64748b;
        }

        body.admin-body .admin-user-menu__list a,
        body.admin-body .admin-user-menu__logout button {
            display: block;
            width: 100%;
            padding: 8px 10px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #0f172a;
            font-size: 13px;
            font-weight: 600;
            text-align: left;
            text-decoration: none;
            cursor: pointer;
        }

        body.admin-body .admin-user-menu__list a:hover,
        body.admin-body .admin-user-menu__logout button:hover {
            background: #f8fafc;
        }

        body.admin-body .admin-user-menu__logout button {
            color: #9b1b2e;
        }

        body.admin-body .admin-topbar-action--user .admin-ref-topbar__logout {
            flex: 0 0 auto;
        }

        @media (max-width: 900px) {
            body.admin-body .admin-topbar-search {
                max-width: none;
            }

            body.admin-body .admin-ref-topbar__logout .btn {
                padding: 6px 10px;
                font-size: 12px;
            }
        }

        @media (max-width: 640px) {
            body.admin-body .admin-topbar-search {
                display: none;
            }
        }

        /* ADMIN HELL PHASE 2.1 — Real language and notifications fix */
        html.admin-root[dir="rtl"] .admin-topbar-dropdown,
        html.admin-root[dir="rtl"] .admin-language-menu__item,
        html.admin-root[dir="rtl"] .admin-notification-menu__item,
        html.admin-root[dir="rtl"] .admin-user-menu__list a {
            text-align: right;
        }

        body.admin-body .admin-notification-menu {
            min-width: min(360px, calc(100vw - 24px));
            max-width: 420px;
        }

        body.admin-body .admin-notification-menu__item--link {
            text-decoration: none;
            color: inherit;
            cursor: pointer;
        }

        body.admin-body .admin-notification-menu__item--link:hover {
            background: #f8fafc;
        }

        body.admin-body .admin-notification-menu__time {
            display: block;
            margin-top: 3px;
            font-size: 10px;
            color: #94a3b8;
        }

        body.admin-body .admin-ref-topbar__badge--subtle {
            background: #9b1b2e;
            color: #fff;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            line-height: 18px;
            text-align: center;
        }

        body.admin-body .admin-language-menu__item.is-active span:first-child {
            color: #9b1b2e;
            font-weight: 800;
        }

        /* ADMIN HELL PHASE 2.2 — No Ctrl+K topbar hardening */
        body.admin-body .admin-topbar-search__input:focus-visible {
            outline: 2px solid rgba(15, 92, 192, 0.35);
            outline-offset: 2px;
            border-radius: 6px;
        }

        body.admin-body .admin-topbar-action__btn:focus-visible {
            outline: 2px solid rgba(15, 92, 192, 0.35);
            outline-offset: 2px;
            border-radius: 8px;
        }

        body.admin-body .admin-notification-menu__head {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        body.admin-body .admin-notification-menu__summary {
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
        }

        body.admin-body .admin-notification-menu__item--audit .admin-notification-menu__dot {
            background: #0f5cc0;
        }

        /* ADMIN USERS HELL PHASE 1 — Staff/Public separation (tabs/filters base) */
        .admin-users-page {
            max-width: 100%;
            overflow-x: hidden;
        }

        .admin-users-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--line);
        }

        .admin-users-tabs__item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #f8fafc;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
        }

        .admin-users-tabs__item.is-active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .admin-users-tabs__badge {
            display: inline-flex;
            min-width: 22px;
            justify-content: center;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 11px;
            background: rgba(255, 255, 255, 0.2);
        }

        .admin-users-tabs__item:not(.is-active) .admin-users-tabs__badge {
            background: #e2e8f0;
            color: var(--ink);
        }

        .admin-users-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .admin-users-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            flex: 1 1 280px;
        }

        .status-badge--public {
            background: #e8f4fd;
            color: #0a458f;
        }

        .status-badge--muted {
            background: #eef2f6;
            color: var(--muted);
        }

        .status-badge--staff {
            background: #edf7f0;
            color: #1f7a45;
        }

        /* ADMIN USERS HELL PHASE 2.1 — Users table UI and actions fix */
        .admin-users-card {
            padding-bottom: 4px;
        }

        .admin-users-table-wrap {
            width: 100%;
            max-width: 100%;
            margin-right: 0;
            padding-right: 4px;
            padding-bottom: 4px;
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
            scrollbar-gutter: stable;
        }

        .admin-users-table {
            width: 100%;
            min-width: 980px;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
        }

        .admin-users-col-name { width: 20%; }
        .admin-users-col-email { width: 25%; }
        .admin-users-col-type { width: 10%; }
        .admin-users-col-status { width: 10%; }
        .admin-users-col-meta { width: 18%; }
        .admin-users-col-login { width: 12%; }
        .admin-users-col-actions { width: 170px; }

        /* ADMIN USERS HELL PHASE 2.2 — Sidebar-open table fit and sticky actions */
        .admin-users-page.admin-users-card.panel {
            overflow-x: hidden;
        }

        .admin-users-table-wrap--fit {
            padding-right: 8px;
            padding-left: 2px;
        }

        .admin-users-table--fit {
            width: 100%;
            min-width: 0;
            max-width: 100%;
        }

        .admin-users-table--fit .admin-users-col-name { width: 14%; }
        .admin-users-table--fit .admin-users-col-email { width: 21%; }
        .admin-users-table--fit .admin-users-col-type { width: 11%; }
        .admin-users-table--fit .admin-users-col-status { width: 8%; }
        .admin-users-table--fit .admin-users-col-meta { width: auto; }
        .admin-users-table--fit .admin-users-col-login { width: 128px; }
        .admin-users-table--fit .admin-users-col-actions { width: 158px; }

        .admin-users-table--fit thead th,
        .admin-users-table--fit tbody td {
            padding: 8px 10px;
        }

        .admin-users-table--fit .admin-users-table__type-cell .admin-users-badge {
            font-size: 10px;
            padding: 2px 6px;
        }

        .admin-users-sticky-col {
            position: sticky;
            background: #fff;
        }

        .admin-users-table--fit thead .admin-users-sticky-col {
            background: #f8fafc;
            z-index: 3;
        }

        .admin-users-sticky-col--login {
            right: 158px;
            z-index: 2;
            min-width: 128px;
            box-shadow: -4px 0 8px -6px rgba(19, 34, 56, 0.12);
        }

        .admin-users-sticky-col--actions {
            right: 0;
            z-index: 4;
            min-width: 158px;
            max-width: 158px;
            box-shadow: -8px 0 12px -8px rgba(19, 34, 56, 0.16);
        }

        .admin-users-table--fit tbody tr:hover .admin-users-sticky-col {
            background: #f8fafc;
        }

        .admin-users-table--fit .admin-users-actions {
            width: 158px;
            min-width: 158px;
            max-width: 158px;
            padding-right: 10px !important;
        }

        .admin-users-table--fit .admin-users-actions__group {
            flex-wrap: nowrap;
            gap: 3px;
        }

        .admin-users-table--fit .admin-users-action-btn {
            padding: 3px 6px;
            font-size: 10px;
        }

        .admin-users-table--fit .admin-users-cell-login {
            min-width: 128px;
            font-size: 11px;
        }

        .admin-users-toolbar {
            gap: 10px;
        }

        @media (min-width: 1200px) {
            .admin-users-table-wrap--fit {
                overflow-x: visible;
            }
        }

        @media (max-width: 1199px) {
            .admin-users-table--fit {
                min-width: 860px;
            }
        }

        .admin-users-table thead th {
            padding: 10px 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--muted);
            white-space: nowrap;
            vertical-align: bottom;
            text-align: left;
            background: #f8fafc;
            border-bottom: 1px solid var(--line);
        }

        .admin-users-col-login-head,
        .admin-users-col-actions-head {
            white-space: nowrap;
        }

        .admin-users-col-actions-head {
            text-align: right;
        }

        .admin-users-table tbody td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid var(--line);
            font-size: 13px;
            line-height: 1.35;
        }

        .admin-users-table tbody tr:hover td {
            background: #f8fafc;
        }

        .admin-users-cell-name,
        .admin-users-cell-email {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-users-cell-name strong {
            font-weight: 600;
            color: var(--ink);
        }

        .admin-users-cell-email .meta {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-users-cell-login,
        .admin-users-cell-date {
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
            color: var(--muted);
            font-size: 12px;
        }

        .admin-users-table__type-cell {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            align-items: center;
        }

        .admin-users-table__roles {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            align-items: center;
            max-height: 3.2em;
            overflow: hidden;
        }

        .admin-users-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.25;
            white-space: nowrap;
        }

        .admin-users-badge--public {
            background: #e8f4fd;
            color: #0a458f;
        }

        .admin-users-badge--muted {
            background: #eef2f6;
            color: var(--muted);
        }

        .admin-users-badge--staff {
            background: #edf7f0;
            color: #1f7a45;
        }

        .admin-users-badge--status {
            background: #f1f5f9;
            color: var(--ink);
            text-transform: capitalize;
        }

        .admin-users-badge--role {
            background: #e8f0fa;
            color: #0a458f;
        }

        .admin-users-actions {
            width: 170px;
            min-width: 170px;
            max-width: 170px;
            padding-right: 14px !important;
            text-align: right;
            vertical-align: middle;
            white-space: nowrap;
        }

        .admin-users-actions__group {
            display: inline-flex;
            flex-wrap: nowrap;
            justify-content: flex-end;
            align-items: center;
            gap: 4px;
            max-width: 100%;
        }

        .admin-users-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 8px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: #fff;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
            color: var(--ink);
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .admin-users-action-btn:hover,
        .admin-users-action-btn:focus-visible {
            border-color: var(--primary);
            color: var(--primary);
            background: #f8fafc;
            outline: none;
        }

        .admin-users-action-btn--danger {
            color: var(--danger);
            border-color: #f5d0cc;
        }

        .admin-users-action-btn--danger:hover,
        .admin-users-action-btn--danger:focus-visible {
            color: #fff;
            background: var(--danger);
            border-color: var(--danger);
        }

        .admin-users-action-form {
            display: inline-flex;
            margin: 0;
        }

        @media (max-width: 1100px) {
            .admin-users-actions__group {
                gap: 3px;
            }

            .admin-users-action-btn {
                padding: 4px 6px;
                font-size: 10px;
            }
        }

        /* ADMIN NEWS HELL PHASE 1 — News module UI and workflow hardening */
        .admin-news-page {
            max-width: 100%;
            overflow-x: hidden;
        }

        .admin-news-workflow {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }

        .admin-news-stat {
            display: inline-flex;
            flex-direction: column;
            gap: 2px;
            min-width: 96px;
            padding: 8px 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #f8fafc;
            text-decoration: none;
            color: inherit;
        }

        .admin-news-stat:hover {
            border-color: var(--primary);
            background: #fff;
        }

        .admin-news-stat--muted {
            cursor: default;
            opacity: 0.92;
        }

        .admin-news-stat--muted:hover {
            border-color: var(--line);
            background: #f8fafc;
        }

        .admin-news-stat__label {
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .admin-news-stat__count {
            font-size: 18px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.1;
        }

        .admin-news-filters {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
            gap: 10px 12px;
            margin-bottom: 14px;
        }

        .admin-news-filters__form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            flex: 1 1 520px;
        }

        .admin-news-filters__search {
            flex: 1 1 160px;
            min-width: 140px;
        }

        .admin-news-filters__select {
            min-width: 120px;
        }

        .admin-news-filters__create {
            flex: 0 0 auto;
            white-space: nowrap;
        }

        .admin-news-table-wrap {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: visible;
            scrollbar-gutter: stable;
        }

        .admin-news-table-wrap--fit:empty,
        .admin-news-table-wrap--fit {
            min-height: 0;
        }

        .admin-news-table-wrap--fit:not(:has(tbody tr td[colspan])) {
            overflow-x: visible;
        }

        @supports not selector(:has(*)) {
            .admin-news-table-wrap--fit {
                overflow-x: auto;
            }
        }

        .admin-news-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
        }

        .admin-news-table--fit {
            min-width: 0;
            max-width: 100%;
        }

        .admin-news-col-title { width: 24%; }
        .admin-news-col-category { width: 12%; }
        .admin-news-col-status { width: 12%; }
        .admin-news-col-author { width: 14%; }
        .admin-news-col-published { width: 14%; }
        .admin-news-col-actions { width: 168px; }

        .admin-news-table thead th {
            padding: 8px 10px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
            white-space: nowrap;
            background: #f8fafc;
            border-bottom: 1px solid var(--line);
            text-align: left;
        }

        .admin-news-col-actions-head {
            text-align: right;
        }

        .admin-news-table tbody td {
            padding: 10px;
            vertical-align: middle;
            border-bottom: 1px solid var(--line);
            font-size: 13px;
        }

        .admin-news-table tbody tr:hover td {
            background: #f8fafc;
        }

        .admin-news-cell-title {
            overflow: hidden;
        }

        .admin-news-cell-title strong {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-weight: 600;
        }

        .admin-news-cell-title .meta,
        .admin-news-cell-category,
        .admin-news-cell-author {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-news-cell-published {
            white-space: nowrap;
            font-size: 12px;
            font-variant-numeric: tabular-nums;
        }

        .admin-news-cell-published .meta {
            display: block;
            font-size: 11px;
        }

        .admin-news-status {
            display: inline-flex;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            text-transform: capitalize;
            background: #f1f5f9;
            color: var(--ink);
        }

        .admin-news-status--draft { background: #eef2f6; color: #475569; }
        .admin-news-status--pending_review { background: #fff4e5; color: #9a5b00; }
        .admin-news-status--approved { background: #e8f4fd; color: #0a458f; }
        .admin-news-status--published { background: #edf7f0; color: #1f7a45; }
        .admin-news-status--rejected { background: #fdecec; color: #b42318; }
        .admin-news-status--archived { background: #f1f5f9; color: #64748b; }
        .admin-news-status--scheduled { background: #f3e8ff; color: #6b21a8; }

        .admin-news-sticky-col {
            position: sticky;
            background: #fff;
            z-index: 1;
        }

        .admin-news-table thead .admin-news-sticky-col {
            background: #f8fafc;
            z-index: 2;
        }

        .admin-news-sticky-col--published {
            right: 168px;
            box-shadow: -4px 0 8px -6px rgba(19, 34, 56, 0.1);
        }

        .admin-news-sticky-col--actions {
            right: 0;
            z-index: 3;
            box-shadow: -8px 0 12px -8px rgba(19, 34, 56, 0.14);
        }

        .admin-news-table tbody tr:hover .admin-news-sticky-col {
            background: #f8fafc;
        }

        .admin-news-actions {
            width: 168px;
            min-width: 168px;
            max-width: 168px;
            padding-right: 10px !important;
            text-align: right;
            vertical-align: middle;
        }

        .admin-news-actions__group {
            display: inline-flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: center;
            gap: 3px;
            max-width: 100%;
        }

        .admin-news-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 3px 6px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: #fff;
            font-size: 10px;
            font-weight: 600;
            line-height: 1.2;
            color: var(--ink);
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .admin-news-action-btn:hover,
        .admin-news-action-btn:focus-visible {
            border-color: var(--primary);
            color: var(--primary);
            outline: none;
        }

        .admin-news-action-btn--primary {
            border-color: var(--primary);
            color: var(--primary);
        }

        .admin-news-action-btn--danger {
            color: var(--danger);
            border-color: #f5d0cc;
        }

        .admin-news-action-form {
            display: inline-flex;
            margin: 0;
        }

        .admin-news-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 28px 16px;
            text-align: center;
        }

        .admin-news-empty__icon {
            font-size: 28px;
            line-height: 1;
            opacity: 0.65;
        }

        .admin-news-table-wrap:has(.admin-news-empty) {
            overflow-x: hidden;
        }

        @media (min-width: 1200px) {
            .admin-news-table-wrap--fit {
                overflow-x: visible;
            }
        }

        @media (max-width: 1199px) {
            .admin-news-table--fit {
                min-width: 880px;
            }
        }

        /* ADMIN NEWS HELL PHASE 1.1 — Compact news index UI microfix */
        .admin-news-page.panel {
            padding: 14px 16px 16px;
        }

        .admin-news-card {
            display: flex;
            flex-direction: column;
            gap: 0;
            min-width: 0;
        }

        .admin-news-page .admin-news-workflow {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }

        .admin-news-page .admin-news-stat {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            min-width: 0;
            min-height: 64px;
            max-height: 72px;
            padding: 8px 10px;
        }

        .admin-news-page .admin-news-stat__label {
            font-size: 10px;
            line-height: 1.2;
        }

        .admin-news-page .admin-news-stat__count {
            font-size: 17px;
            flex-shrink: 0;
        }

        .admin-news-toolbar.toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px 12px;
            margin-bottom: 10px;
            padding-bottom: 0;
            border-bottom: none;
        }

        .admin-news-toolbar .admin-news-filters {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(108px, 0.9fr) minmax(108px, 0.9fr) auto auto;
            gap: 8px;
            align-items: center;
            min-width: 0;
            flex: none;
        }

        .admin-news-toolbar .admin-news-filters--has-author {
            grid-template-columns: minmax(0, 1.35fr) repeat(3, minmax(96px, 0.82fr)) auto auto;
        }

        .admin-news-toolbar .admin-news-filters input,
        .admin-news-toolbar .admin-news-filters select {
            width: 100%;
            min-width: 0;
            min-height: 38px;
            margin: 0;
            font-size: 13px;
        }

        .admin-news-toolbar .admin-news-filter--btn {
            width: auto;
            justify-self: start;
            min-width: 4.75rem;
            padding-inline: 12px;
        }

        .admin-news-toolbar .admin-news-create {
            justify-self: end;
            align-self: center;
            white-space: nowrap;
        }

        .admin-news-page .admin-news-table-wrap {
            margin-top: 0;
        }

        .admin-news-page .admin-news-empty {
            gap: 6px;
            padding: 18px 12px 20px;
            min-height: 0;
        }

        .admin-news-page .admin-news-empty__icon {
            font-size: 22px;
        }

        .admin-news-page .admin-news-empty .meta {
            margin: 0;
            font-size: 12px;
            line-height: 1.4;
        }

        .admin-news-page .admin-news-empty .btn {
            margin-top: 4px;
        }

        @media (max-width: 1100px) {
            .admin-news-page .admin-news-workflow {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        /* ADMIN NEWS MODULE STEP 1 — Journalist workflow UI */
        .admin-news-workspace .admin-news-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 10px;
            padding: 2px 0 10px;
            border-bottom: 1px solid var(--line);
        }

        .admin-news-workspace .admin-news-hero h1 {
            margin: 0;
            font-size: 24px;
            line-height: 1.15;
            letter-spacing: 0;
            color: var(--ink);
        }

        .admin-news-workspace .admin-news-hero p {
            margin: 4px 0 0;
            max-width: 660px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.4;
        }

        .admin-news-workspace .admin-news-workflow {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin: 0 0 10px;
            padding: 9px 10px;
            border: 1px solid rgba(15, 92, 192, 0.16);
            border-radius: 8px;
            background: #f6fbff;
            color: var(--ink);
            font-size: 12px;
        }

        .admin-news-workspace .admin-news-workflow strong {
            font-size: 12px;
        }

        .admin-news-workspace .admin-news-workflow span:not([aria-hidden]) {
            padding: 2px 8px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid rgba(15, 92, 192, 0.12);
            white-space: nowrap;
        }

        .admin-news-workspace .admin-news-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(128px, 1fr));
            gap: 8px;
            margin-bottom: 10px;
        }

        .admin-news-workspace .admin-news-stat-card {
            min-height: 54px;
            max-height: none;
            border-radius: 8px;
        }

        .admin-news-workspace .admin-news-filter-bar {
            grid-template-columns: minmax(0, 1fr) auto;
        }

        .admin-news-filter-summary {
            justify-self: end;
            color: var(--muted);
            font-size: 12px;
            white-space: nowrap;
        }

        .admin-news-form-card {
            margin-bottom: 14px;
        }

        .admin-news-form-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .admin-news-form-head .section-title {
            margin-bottom: 4px;
        }

        .admin-news-form-head .meta {
            margin: 0;
        }

        @media (max-width: 900px) {
            .admin-news-workspace .admin-news-hero,
            .admin-news-form-head {
                align-items: stretch;
                flex-direction: column;
            }

            .admin-news-filter-summary {
                justify-self: start;
            }
        }

        /* NEWS MEDIA UPLOAD PHASE 1 — Contextual news media */
        .admin-news-media-card {
            margin-top: 14px;
        }

        .admin-news-media-help {
            margin: 0 0 12px;
        }

        .admin-news-media-grid {
            display: grid;
            gap: 12px;
        }

        .admin-news-media-preview__image {
            display: block;
            max-width: 220px;
            max-height: 140px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--line);
        }

        .admin-news-media-preview__thumb {
            flex: 0 0 auto;
            width: 40px;
            height: 28px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid var(--line);
        }

        .admin-news-cell-title__row {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .admin-news-cell-title__copy {
            min-width: 0;
            overflow: hidden;
        }

        .admin-news-media-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .admin-news-media-gallery__thumb {
            width: 72px;
            height: 52px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--line);
        }

        .admin-news-media-remove {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .admin-news-media-empty {
            font-size: 12px;
            color: var(--muted);
        }

        /* ADMIN MEDIA HELL PHASE 1 — Media Review Center */
        .admin-media-review-page {
            max-width: 100%;
            overflow-x: hidden;
        }

        .admin-media-review-notice {
            margin-bottom: 12px;
            padding: 10px 12px;
            border: 1px solid rgba(10, 69, 143, 0.18);
            border-radius: 10px;
            background: #f0f7ff;
            font-size: 13px;
            line-height: 1.45;
            color: var(--ink);
        }

        .admin-media-review-summary {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }

        .admin-media-review-source {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-height: 64px;
            padding: 8px 10px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #f8fafc;
        }

        .admin-media-review-source__label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
        }

        .admin-media-review-source__count {
            font-size: 18px;
            font-weight: 700;
            line-height: 1.1;
            color: var(--ink);
        }

        .admin-media-review-meta {
            margin: 0 0 10px;
        }

        .admin-media-review-toolbar.toolbar {
            margin-bottom: 10px;
            padding-bottom: 0;
            border-bottom: none;
        }

        .admin-media-review-filters {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) repeat(3, minmax(108px, 0.85fr)) auto auto;
            gap: 8px;
            align-items: center;
            width: 100%;
        }

        .admin-media-review-filters input,
        .admin-media-review-filters select {
            width: 100%;
            min-width: 0;
            min-height: 38px;
        }

        .admin-media-review-table-wrap {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
        }

        .admin-media-review-table-wrap:has(.admin-media-review-empty) {
            overflow-x: hidden;
        }

        .admin-media-review-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
        }

        .admin-media-review-table th,
        .admin-media-review-table td {
            padding: 8px 10px;
            vertical-align: middle;
            border-bottom: 1px solid var(--line);
        }

        .admin-media-review-table th {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
            background: #f8fafc;
            white-space: nowrap;
        }

        .admin-media-review-preview {
            width: 64px;
            height: 44px;
            object-fit: cover;
            border-radius: 8px;
            display: block;
        }

        .admin-media-review-source-tag {
            display: inline-flex;
            padding: 2px 8px;
            border-radius: 999px;
            background: #eef2f6;
            font-size: 11px;
            font-weight: 600;
        }

        .admin-media-review-status {
            display: inline-flex;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            background: #f1f5f9;
        }

        .admin-media-review-status--archived {
            background: #f1f5f9;
            color: #64748b;
        }

        .admin-media-review-status--active {
            background: #edf7f0;
            color: #1f7a45;
        }

        .admin-media-review-actions {
            white-space: nowrap;
            text-align: right;
        }

        .admin-media-review-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 20px 12px 24px;
            text-align: center;
        }

        .admin-media-review-empty__icon {
            font-size: 22px;
            opacity: 0.7;
        }

        @media (max-width: 1100px) {
            .admin-media-review-summary {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .admin-media-review-filters {
                grid-template-columns: 1fr 1fr;
            }

            .admin-media-review-filter--search {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 900px) {
            .admin-news-toolbar.toolbar {
                grid-template-columns: 1fr;
            }

            .admin-news-toolbar .admin-news-filters,
            .admin-news-toolbar .admin-news-filters--has-author {
                grid-template-columns: 1fr;
            }

            .admin-news-toolbar .admin-news-create {
                justify-self: stretch;
            }

            .admin-news-toolbar .admin-news-create .btn {
                width: 100%;
            }

            .admin-news-page .admin-news-workflow {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

    </style>
</head>
<body class="admin-body">
    <div class="admin-shell admin-ref-shell">
        @include('admin.partials.sidebar')
        <button type="button" class="admin-sidebar-backdrop" id="admin-sidebar-backdrop" aria-label="{{ __('Close navigation') }}" hidden></button>

        <div class="admin-main admin-ref-main">
            @include('admin.partials.topbar')
            <div class="admin-ref-content admin-content">
                @include('admin.partials.flash')
                @yield('content')
            </div>
        </div>
    </div>
    <script>
        (() => {
            const shell = document.querySelector('.admin-shell');
            const menuButton = document.querySelector('.admin-sidebar-toggle');
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            const storageKey = 'morocco2030.admin.sidebarCollapsed';
            const mobileQuery = window.matchMedia('(max-width: 900px)');

            const isMobile = () => mobileQuery.matches;

            const setMobileOpen = (open) => {
                sidebar?.classList.toggle('is-open', open);
                shell?.classList.toggle('is-sidebar-mobile-open', open);
                if (backdrop) {
                    backdrop.hidden = !open;
                }
                menuButton?.setAttribute('aria-expanded', open ? 'true' : 'false');
            };

            const setDesktopCollapsed = (collapsed) => {
                shell?.classList.toggle('is-sidebar-collapsed', collapsed);
                localStorage.setItem(storageKey, collapsed ? '1' : '0');
                menuButton?.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            };

            const syncForViewport = () => {
                if (isMobile()) {
                    shell?.classList.remove('is-sidebar-collapsed');
                    setMobileOpen(false);
                    return;
                }

                setMobileOpen(false);
                const collapsed = localStorage.getItem(storageKey) === '1';
                setDesktopCollapsed(collapsed);
            };

            syncForViewport();
            mobileQuery.addEventListener('change', syncForViewport);

            menuButton?.addEventListener('click', () => {
                if (isMobile()) {
                    setMobileOpen(!sidebar?.classList.contains('is-open'));
                    return;
                }

                setDesktopCollapsed(!shell?.classList.contains('is-sidebar-collapsed'));
            });

            backdrop?.addEventListener('click', () => setMobileOpen(false));
        })();

        // ADMIN HELL PHASE 2.2 — No Ctrl+K topbar hardening
        (() => {
            const searchInput = document.getElementById('admin-topbar-search-input');
            const searchWrap = document.querySelector('.admin-topbar-search');
            const searchPanel = document.getElementById('admin-topbar-search-panel');
            const searchDataEl = document.getElementById('admin-topbar-search-data');
            const dropdownConfigs = [
                { toggle: document.getElementById('admin-notifications-toggle'), panel: document.getElementById('admin-notification-menu') },
                { toggle: document.getElementById('admin-language-toggle'), panel: document.getElementById('admin-language-menu') },
                { toggle: document.getElementById('admin-user-menu-toggle'), panel: document.getElementById('admin-user-menu') },
            ].filter((entry) => entry.toggle && entry.panel);

            let openPanel = null;
            let searchItems = [];

            try {
                searchItems = JSON.parse(searchDataEl?.textContent || '[]');
            } catch {
                searchItems = [];
            }

            const setExpanded = (toggle, expanded) => {
                toggle?.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            };

            const closePanel = (panel) => {
                if (!panel) {
                    return;
                }
                panel.hidden = true;
                panel.classList.remove('is-open');
            };

            const closeAll = () => {
                dropdownConfigs.forEach(({ toggle, panel }) => {
                    closePanel(panel);
                    setExpanded(toggle, false);
                });
                if (searchPanel) {
                    searchPanel.hidden = true;
                    searchPanel.classList.remove('is-open');
                    setExpanded(searchInput, false);
                }
                searchWrap?.classList.remove('is-active');
                openPanel = null;
            };

            const closeMenusOnly = () => {
                dropdownConfigs.forEach(({ toggle, panel }) => {
                    closePanel(panel);
                    setExpanded(toggle, false);
                });
                if (openPanel && openPanel !== searchPanel) {
                    openPanel = null;
                }
            };

            const openDropdown = (toggle, panel) => {
                if (!panel || panel === openPanel) {
                    closeAll();
                    return;
                }
                closeAll();
                panel.hidden = false;
                panel.classList.add('is-open');
                setExpanded(toggle, true);
                openPanel = panel;
            };

            const normalize = (value) => value.toLowerCase().trim();

            const filterSearchItems = (query) => {
                const needle = normalize(query);
                if (!needle) {
                    return searchItems.slice(0, 8);
                }
                return searchItems.filter((item) => {
                    const haystack = normalize(`${item.label} ${item.keywords || ''}`);
                    return haystack.includes(needle);
                }).slice(0, 8);
            };

            const renderSearchResults = (query) => {
                if (!searchPanel) {
                    return [];
                }
                closeMenusOnly();
                const matches = filterSearchItems(query);
                searchPanel.innerHTML = '';
                if (matches.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'admin-topbar-search__empty';
                    empty.textContent = searchPanel.dataset.emptyMessage || 'No admin section found';
                    searchPanel.appendChild(empty);
                } else {
                    const sectionHint = searchPanel.dataset.sectionLabel || 'Admin section';
                    matches.forEach((item) => {
                        const link = document.createElement('a');
                        link.className = 'admin-topbar-search__result';
                        link.href = item.url;
                        link.setAttribute('role', 'option');

                        const icon = document.createElement('span');
                        icon.className = 'admin-topbar-search__result-icon';
                        icon.setAttribute('aria-hidden', 'true');
                        icon.textContent = '→';

                        const copy = document.createElement('span');
                        copy.className = 'admin-topbar-search__result-copy';

                        const title = document.createElement('strong');
                        title.textContent = item.label;

                        const meta = document.createElement('small');
                        meta.textContent = sectionHint;

                        copy.append(title, meta);
                        link.append(icon, copy);
                        searchPanel.appendChild(link);
                    });
                }
                searchPanel.hidden = false;
                searchPanel.classList.add('is-open');
                searchWrap?.classList.add('is-active');
                setExpanded(searchInput, true);
                openPanel = searchPanel;
                return matches;
            };

            dropdownConfigs.forEach(({ toggle, panel }) => {
                toggle.addEventListener('click', (event) => {
                    event.stopPropagation();
                    if (panel.hidden || !panel.classList.contains('is-open')) {
                        openDropdown(toggle, panel);
                    } else {
                        closeAll();
                    }
                });
            });

            searchInput?.addEventListener('focus', () => {
                renderSearchResults(searchInput.value);
            });

            searchInput?.addEventListener('input', () => {
                renderSearchResults(searchInput.value);
            });

            searchInput?.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeAll();
                    searchInput.blur();
                    return;
                }
                if (event.key !== 'Enter') {
                    return;
                }
                event.preventDefault();
                const matches = renderSearchResults(searchInput.value);
                if (matches.length > 0 && matches[0].url) {
                    window.location.href = matches[0].url;
                }
            });

            document.addEventListener('click', (event) => {
                const target = event.target;
                if (!(target instanceof Element)) {
                    return;
                }
                if (
                    target.closest('.admin-topbar-search')
                    || target.closest('.admin-topbar-action')
                ) {
                    return;
                }
                closeAll();
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeAll();
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
