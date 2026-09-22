:root {
    color-scheme: light;
    --bg: #eef3f9;
    --bg-strong: #061629;
    --bg-shell: #ffffff;
    --bg-soft: #e6edf6;
    --bg-accent: #0c2540;
    --paper: #fbfbf7;
    --paper-soft: #f4f5ef;
    --paper-strong: #ffffff;
    --ink: #07182c;
    --ink-soft: #1b2c43;
    --brand: #af1731;
    --brand-strong: #75101f;
    --brand-bright: #d22a44;
    --brand-soft: rgba(175, 23, 49, 0.12);
    --burgundy: #5c0f1e;
    --burgundy-soft: rgba(92, 15, 30, 0.16);
    --gold: #d4ad52;
    --gold-strong: #b88a2a;
    --gold-soft: rgba(212, 173, 82, 0.18);
    --morocco-green: #0d6b45;
    --green-soft: rgba(13, 107, 69, 0.13);
    --border: rgba(7, 24, 44, 0.1);
    --border-strong: rgba(7, 24, 44, 0.18);
    --border-soft: rgba(7, 24, 44, 0.06);
    --text: #07182c;
    --text-soft: #2c3e57;
    --text-muted: #5f7088;
    --success: #177654;
    --success-soft: rgba(23, 118, 84, 0.14);
    --warning: #8c5d10;
    --warning-soft: rgba(212, 173, 82, 0.22);
    --danger: #92253b;
    --danger-soft: rgba(146, 37, 59, 0.14);
    --shadow-xl: 0 38px 96px rgba(6, 22, 41, 0.18);
    --shadow-lg: 0 26px 70px rgba(6, 22, 41, 0.14);
    --shadow-md: 0 16px 40px rgba(6, 22, 41, 0.1);
    --shadow-sm: 0 10px 24px rgba(6, 22, 41, 0.07);
    --shadow-card: 0 1px 0 rgba(255, 255, 255, 0.65) inset, 0 14px 32px rgba(6, 22, 41, 0.08);
    --focus-ring: 0 0 0 3px rgba(212, 173, 82, 0.35), 0 0 0 5px rgba(175, 23, 49, 0.25);
    --focus-ring-light: 0 0 0 3px rgba(255, 255, 255, 0.55), 0 0 0 5px rgba(212, 173, 82, 0.6);
    --radius-xl: 26px;
    --radius-lg: 20px;
    --radius-md: 14px;
    --radius-sm: 10px;
    --container: 1240px;
    --section-gap: clamp(2.6rem, 3.6vw, 4.2rem);
    --header-h: 4.4rem;
}

*,
*::before,
*::after {
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    position: relative;
    margin: 0;
    min-height: 100vh;
    background:
        radial-gradient(ellipse at top, rgba(7, 24, 44, 0.06), transparent 56%),
        radial-gradient(circle at 92% 4%, rgba(175, 23, 49, 0.05), transparent 26%),
        linear-gradient(180deg, #f6f8fc 0%, #eef2f8 100%);
    background-attachment: fixed;
    color: var(--text);
    font-family: "Inter", "Segoe UI Variable", "Segoe UI", "Helvetica Neue", "Trebuchet MS", system-ui, -apple-system, sans-serif;
    font-feature-settings: "ss01", "cv02", "cv11";
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: optimizeLegibility;
}

body::before {
    content: "";
    position: fixed;
    inset: 0;
    background:
        radial-gradient(circle at 14% 8%, rgba(212, 173, 82, 0.08), transparent 22%),
        radial-gradient(circle at 88% 6%, rgba(7, 24, 44, 0.08), transparent 24%);
    pointer-events: none;
    z-index: -2;
}

a {
    color: inherit;
    text-decoration: none;
}

img {
    display: block;
    max-width: 100%;
}

main {
    position: relative;
    padding: 1rem 0 4.6rem;
}

:focus {
    outline: 0;
}

:focus-visible {
    outline: 0;
    box-shadow: var(--focus-ring);
    border-radius: 6px;
}

a:focus-visible {
    outline: 0;
    box-shadow: var(--focus-ring);
    border-radius: 6px;
}

button:focus-visible {
    outline: 0;
    box-shadow: var(--focus-ring);
}

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.skip-link {
    position: absolute;
    top: -42px;
    inset-inline-start: 1rem;
    z-index: 999;
    padding: 0.55rem 0.95rem;
    border-radius: 10px;
    background: var(--ink);
    color: #fff;
    font-weight: 700;
    box-shadow: var(--shadow-md);
    transition: top 0.18s ease;
}

.skip-link:focus,
.skip-link:focus-visible {
    top: 0.6rem;
    box-shadow: var(--focus-ring-light);
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.001ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.001ms !important;
        scroll-behavior: auto !important;
    }
}

.page-container,
.site-header__top-inner,
.site-header__inner,
.site-footer__inner {
    width: min(calc(100% - 48px), var(--container));
    margin-inline: auto;
}

.page-home .page-container {
    padding-top: 0;
}

.page-section {
    margin-top: var(--section-gap);
}

.page-section:first-child {
    margin-top: 0;
}

.flash-stack {
    display: grid;
    gap: 0.85rem;
    margin-bottom: 1.2rem;
}

.flash-banner {
    padding: 0.95rem 1.15rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.9);
    box-shadow: var(--shadow-sm);
    font-weight: 700;
}

.flash-banner--success {
    border-color: rgba(23, 118, 84, 0.2);
    background: rgba(23, 118, 84, 0.1);
    color: var(--success);
}

.flash-banner--status {
    border-color: rgba(10, 29, 51, 0.12);
    color: var(--text-soft);
}

.flash-banner--error {
    border-color: rgba(146, 37, 59, 0.22);
    background: rgba(146, 37, 59, 0.1);
    color: var(--danger);
}

.site-header {
    position: sticky;
    top: 0;
    z-index: 20;
    padding: 0;
    background:
        linear-gradient(180deg, rgba(4, 11, 22, 0.985), rgba(7, 20, 38, 0.985) 60%, rgba(38, 7, 15, 0.96)),
        #04090f;
    border-bottom: 1px solid rgba(212, 173, 82, 0.18);
    box-shadow: 0 18px 38px rgba(4, 11, 22, 0.24);
    backdrop-filter: saturate(140%) blur(6px);
}

.site-header::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: -1px;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, rgba(175, 23, 49, 0.5) 32%, rgba(212, 173, 82, 0.55) 50%, rgba(175, 23, 49, 0.5) 68%, transparent 100%);
    pointer-events: none;
}

.site-header__top {
    margin-bottom: 0;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.72rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.site-header__top-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.65rem;
    padding: 0.26rem 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    backdrop-filter: none;
}

.site-header__status-line {
    display: flex;
    align-items: center;
    gap: 0.46rem;
    min-width: 0;
}

.site-header__status-pill {
    display: inline-flex;
    align-items: center;
    min-height: 1.32rem;
    padding: 0.1rem 0.46rem;
    border-radius: 999px;
    background: rgba(212, 173, 82, 0.12);
    border: 1px solid rgba(212, 173, 82, 0.28);
    color: rgba(255, 218, 139, 0.95);
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.11em;
    text-transform: uppercase;
}

.site-header__status {
    max-width: 34rem;
    color: rgba(255, 255, 255, 0.62);
    line-height: 1.32;
}

.site-header__top-links {
    display: inline-flex;
    align-items: center;
    gap: 0.12rem;
    white-space: nowrap;
}

.site-header__top-links a {
    display: inline-flex;
    align-items: center;
    min-height: 1.55rem;
    padding: 0.16rem 0.45rem;
    border-radius: 999px;
    color: rgba(255, 255, 255, 0.68);
    font-size: 0.72rem;
    font-weight: 700;
}

.site-header__top-links button {
    display: inline-flex;
    align-items: center;
    min-height: 1.55rem;
    padding: 0.16rem 0.45rem;
    border: 0;
    border-radius: 999px;
    background: transparent;
    color: rgba(255, 255, 255, 0.68);
    font: inherit;
    font-size: 0.72rem;
    font-weight: 700;
    cursor: pointer;
}

.site-header__top-links a:hover {
    background: rgba(255, 255, 255, 0.09);
    color: #fff;
}

.site-header__top-links button:hover {
    background: rgba(255, 255, 255, 0.09);
    color: #fff;
}

.site-header__top-links a.is-active {
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
}

.site-header__admin-link {
    min-height: auto !important;
    padding: 0.12rem 0.24rem !important;
    border: 0 !important;
    background: transparent !important;
    color: rgba(255, 255, 255, 0.48) !important;
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    opacity: 0.78;
    text-transform: uppercase;
}

.site-header__admin-link:hover,
.site-header__admin-link.is-active {
    background: transparent !important;
    color: rgba(255, 255, 255, 0.82) !important;
    opacity: 1;
    text-decoration: underline;
}

.site-header__logout-form {
    margin: 0;
}

.language-switcher {
    display: inline-flex;
    align-items: center;
    gap: 0.12rem;
    margin-inline-start: 0.12rem;
    padding-inline-start: 0.12rem;
    border-inline-start: 1px solid rgba(255, 255, 255, 0.11);
}

.language-switcher__link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.58rem;
    min-height: 1.5rem;
    padding: 0.16rem 0.34rem;
    border-radius: 999px;
    color: rgba(255, 255, 255, 0.56);
    font-size: 0.64rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.language-switcher__link:hover,
.language-switcher__link.is-active {
    background: rgba(212, 173, 82, 0.16);
    color: #fff;
}

.site-header__inner {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    align-items: center;
    gap: 1.2rem;
    padding: 0.72rem 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}

.brand {
    display: inline-flex;
    align-items: center;
    gap: 0.72rem;
    padding: 0.18rem 0.32rem 0.18rem 0.18rem;
    border-radius: 12px;
    color: #fff;
    transition: background-color 0.18s ease;
}

.brand:hover {
    background: rgba(255, 255, 255, 0.04);
}

.brand__mark {
    width: 2.4rem;
    height: 2.4rem;
    border-radius: 10px;
    display: grid;
    place-items: center;
    background:
        linear-gradient(145deg, var(--brand-bright), var(--brand) 58%, var(--burgundy));
    color: #fff;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 0.88rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.28),
        inset 0 0 0 1px rgba(212, 173, 82, 0.18),
        0 8px 20px rgba(175, 23, 49, 0.32);
    position: relative;
}

.brand__mark::after {
    content: "";
    position: absolute;
    inset: auto 0 -3px 0;
    height: 2px;
    border-radius: 0 0 6px 6px;
    background: linear-gradient(90deg, transparent, var(--gold), transparent);
    opacity: 0.85;
}

.brand__meta {
    display: flex;
    flex-direction: column;
    line-height: 1.05;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.005em;
    color: #fff;
}

.brand__meta small {
    margin-top: 0.18rem;
    font-family: "Inter", "Segoe UI", sans-serif;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: rgba(212, 173, 82, 0.92);
}

.site-header__nav-shell {
    min-width: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.7rem;
}

.site-nav {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 0.1rem;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    overflow-x: auto;
    scrollbar-width: none;
    flex: 1 1 auto;
}

.site-nav::-webkit-scrollbar {
    display: none;
}

.site-nav a,
.header-link,
.button,
.button--subtle,
.button--ghost,
.match-card__cta {
    transition: transform 0.18s ease, background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
}

.site-nav a {
    position: relative;
    flex: 0 0 auto;
    padding: 0.5rem 0.78rem;
    border-radius: 8px;
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: 0.005em;
}

.site-nav a:hover {
    background: rgba(255, 255, 255, 0.07);
    color: #fff;
}

.site-nav a.is-active {
    background: rgba(255, 255, 255, 0.06);
    color: #fff;
}

.site-nav a.is-active::after {
    content: "";
    position: absolute;
    left: 0.78rem;
    right: 0.78rem;
    bottom: -0.32rem;
    height: 2px;
    border-radius: 2px;
    background: linear-gradient(90deg, var(--gold), var(--brand-bright));
}

.site-nav a:focus-visible {
    box-shadow: var(--focus-ring-light);
}

.header-link,
.button,
.button--subtle,
.button--ghost {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-height: 2.5rem;
    padding: 0.6rem 1.1rem;
    border-radius: 999px;
    border: 1px solid transparent;
    font-family: inherit;
    font-weight: 700;
    font-size: 0.86rem;
    letter-spacing: 0.005em;
    cursor: pointer;
    white-space: nowrap;
    text-align: center;
}

.header-link {
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.18);
}

.header-link--quiet {
    min-height: 2.2rem;
    padding-inline: 0.78rem;
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.14);
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.78rem;
}

.site-header__cta-primary {
    min-height: 2.2rem;
    padding-inline: 0.92rem;
    box-shadow: 0 8px 18px rgba(175, 23, 49, 0.28);
}

.site-header__audience-tools {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.4rem;
    flex: 0 0 auto;
}

.header-account {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 0;
    padding: 0.42rem 0.72rem;
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.07);
    color: #fff;
}

.header-account:hover,
.header-account.is-active {
    background: rgba(255, 255, 255, 0.14);
    border-color: rgba(255, 255, 255, 0.22);
}

.header-account__eyebrow {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    padding-inline-end: 0.5rem;
    border-inline-end: 1px solid rgba(255, 255, 255, 0.18);
    white-space: nowrap;
}

.header-account strong {
    font-size: 0.86rem;
    font-weight: 700;
    white-space: nowrap;
}

.header-link:hover,
.button:hover,
.button--subtle:hover,
.button--ghost:hover,
.match-card__cta:hover {
    transform: translateY(-1px);
}

.button:active,
.button--subtle:active,
.button--ghost:active,
.match-card__cta:active {
    transform: translateY(0);
}

.button {
    background: linear-gradient(140deg, var(--brand-bright), var(--brand) 55%, var(--burgundy));
    color: #fff;
    border-color: rgba(255, 255, 255, 0.12);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.24),
        0 12px 28px rgba(175, 23, 49, 0.32);
}

.button:hover {
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.32),
        0 16px 36px rgba(175, 23, 49, 0.4);
}

.button--subtle,
.match-card__cta {
    border-color: var(--border-strong);
    background: rgba(255, 255, 255, 0.92);
    color: var(--ink);
    box-shadow: var(--shadow-card);
}

.button--subtle:hover,
.match-card__cta:hover {
    border-color: var(--ink);
    background: #fff;
    color: var(--ink);
}

.button--ghost {
    background: transparent;
    color: var(--brand);
    border-color: transparent;
    padding-inline: 0.78rem;
    text-decoration-line: underline;
    text-decoration-thickness: 1px;
    text-underline-offset: 5px;
    text-decoration-color: rgba(175, 23, 49, 0.35);
}

.button--ghost:hover {
    color: var(--brand-strong);
    text-decoration-color: var(--brand);
}

.button:focus-visible,
.button--subtle:focus-visible,
.button--ghost:focus-visible {
    box-shadow: var(--focus-ring);
}

.button--lg {
    min-height: 3rem;
    padding: 0.78rem 1.5rem;
    font-size: 0.94rem;
}

.button--block {
    width: 100%;
}

.header-link--footer {
    align-self: flex-start;
    background: rgba(255, 255, 255, 0.08);
}

.hero,
.page-header {
    position: relative;
    overflow: hidden;
    border-radius: clamp(20px, 2.8vw, 28px);
    background:
        radial-gradient(circle at 88% 8%, rgba(212, 173, 82, 0.22), transparent 30%),
        radial-gradient(circle at 6% 88%, rgba(175, 23, 49, 0.28), transparent 36%),
        linear-gradient(135deg, #061629 0%, #0c2540 48%, #2c0a17 100%);
    color: #fff;
    box-shadow: var(--shadow-xl);
}

.hero::before,
.page-header::before {
    content: "";
    position: absolute;
    inset: auto -10% -38% auto;
    width: clamp(220px, 28vw, 420px);
    aspect-ratio: 1;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(212, 173, 82, 0.32), transparent 64%);
    pointer-events: none;
}

.hero::after,
.page-header::after {
    content: "";
    position: absolute;
    inset: 0;
    background:
        linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.06) 100%),
        repeating-linear-gradient(120deg, rgba(255, 255, 255, 0.025) 0 1px, transparent 1px 22px);
    pointer-events: none;
}

.hero {
    display: grid;
    grid-template-columns: minmax(0, 1.3fr) minmax(320px, 0.85fr);
    gap: clamp(1.4rem, 2.4vw, 2.4rem);
    padding: clamp(1.8rem, 3vw, 2.85rem);
    align-items: start;
    border: 1px solid rgba(255, 255, 255, 0.06);
}

.hero__content,
.hero__aside,
.hero__main,
.hero__board,
.page-header__content {
    position: relative;
    z-index: 1;
}

.hero__main,
.hero__content {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
}

.hero__body {
    max-width: 44rem;
}

.hero__eyebrow,
.page-header__eyebrow,
.section-header__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-size: 0.72rem;
    font-weight: 700;
}

.hero__eyebrow,
.page-header__eyebrow {
    color: var(--gold);
    text-shadow: 0 1px 0 rgba(0, 0, 0, 0.18);
}

.hero__eyebrow::before,
.page-header__eyebrow::before {
    content: "";
    width: 1.4rem;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--gold));
}

.hero__chip-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin: 0;
}

.hero-chip {
    display: inline-flex;
    align-items: center;
    min-height: 1.6rem;
    padding: 0.22rem 0.62rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.86);
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.hero h1,
.page-header h1,
.section-header h2,
.section-header h3,
.panel h2,
.panel h3,
.detail-shell h2,
.detail-shell h3 {
    margin: 0;
    font-family: "Georgia", "Times New Roman", serif;
    line-height: 1.08;
    letter-spacing: -0.025em;
}

.hero h1 {
    margin-top: 0.2rem;
    max-width: 13ch;
    font-size: clamp(2.6rem, 5.2vw, 4.8rem);
    letter-spacing: -0.04em;
    font-weight: 700;
}

.hero h1 span {
    display: block;
    margin-top: 0.4rem;
    color: rgba(255, 255, 255, 0.78);
    font-size: clamp(1rem, 1.5vw, 1.4rem);
    letter-spacing: 0.01em;
    font-weight: 600;
    font-family: "Inter", "Segoe UI", sans-serif;
    text-transform: uppercase;
}

.hero h1 span::before {
    content: "—";
    margin-inline-end: 0.5rem;
    color: var(--gold);
    opacity: 0.7;
}

.page-header__content {
    max-width: 60rem;
    padding: clamp(2rem, 3.2vw, 3rem);
}

.page-header h1 {
    margin-top: 0.5rem;
    font-size: clamp(2rem, 3.8vw, 3.2rem);
}

.hero p,
.page-header p,
.section-header p,
.detail-copy,
.site-footer p {
    color: inherit;
}

.hero p,
.page-header p {
    max-width: 46rem;
    margin: 0;
    font-size: clamp(0.96rem, 1.05vw, 1.1rem);
    line-height: 1.65;
    color: rgba(255, 255, 255, 0.82);
}

.hero__actions,
.page-header__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.7rem;
    align-items: center;
    margin-top: 0.4rem;
}

.hero__actions .button--ghost {
    color: var(--gold);
    text-decoration-color: rgba(212, 173, 82, 0.45);
}

.hero__actions .button--ghost:hover {
    color: #fff;
    text-decoration-color: var(--gold);
}

.hero__actions .button--subtle {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.18);
    color: #fff;
    backdrop-filter: blur(8px);
}

.hero__actions .button--subtle:hover {
    background: rgba(255, 255, 255, 0.16);
    border-color: rgba(255, 255, 255, 0.3);
    color: #fff;
}

.hero__quicklinks {
    display: flex;
    flex-wrap: wrap;
    gap: 0.32rem;
    margin-top: 0.4rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.hero__quicklink {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    min-height: 1.85rem;
    padding: 0.35rem 0.72rem;
    border-radius: 999px;
    background: transparent;
    color: rgba(255, 255, 255, 0.76);
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.01em;
}

.hero__quicklink::before {
    content: "›";
    color: var(--gold);
    font-weight: 700;
}

.hero__quicklink:hover {
    background: rgba(255, 255, 255, 0.07);
    color: #fff;
}

.hero__support-grid {
    display: grid;
    grid-template-columns: minmax(260px, 1.08fr) minmax(0, 0.92fr);
    gap: 0.85rem;
    align-items: stretch;
}

.hero__aside,
.hero__board {
    align-self: stretch;
    padding: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 18px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02)),
        rgba(3, 14, 26, 0.32);
    backdrop-filter: blur(12px) saturate(140%);
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.hero__aside h2,
.hero__board h2 {
    font-size: 1.45rem;
}

.hero__spotlight {
    display: flex;
    flex-direction: column;
    gap: 0.62rem;
}

.hero-feature-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.5rem;
}

.hero-feature {
    padding: 0.85rem 0.95rem;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(2, 8, 15, 0.4);
    transition: border-color 0.18s ease, transform 0.18s ease;
}

.hero-feature--primary {
    border-color: rgba(212, 173, 82, 0.32);
    background:
        linear-gradient(135deg, rgba(212, 173, 82, 0.08), transparent 60%),
        rgba(2, 8, 15, 0.46);
}

.hero-feature:hover {
    border-color: rgba(255, 255, 255, 0.18);
}

.hero-feature__label {
    display: inline-flex;
    margin-bottom: 0.5rem;
    color: var(--gold);
    text-transform: uppercase;
    letter-spacing: 0.14em;
    font-size: 0.66rem;
    font-weight: 800;
}

.hero-feature__title {
    display: block;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.04rem;
    line-height: 1.22;
    color: #fff;
}

.hero-feature__title:hover {
    color: var(--gold);
}

.hero-feature__meta,
.hero-feature__row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.hero-feature__meta {
    margin-top: 0.42rem;
    color: rgba(255, 255, 255, 0.74);
    font-size: 0.78rem;
}

.hero-feature__rows {
    display: flex;
    flex-direction: column;
    gap: 0.38rem;
    margin-top: 0.55rem;
}

.hero-feature__row {
    align-items: center;
    padding-top: 0.38rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.86);
}

.hero-feature__row strong {
    font-weight: 700;
}

.hero-feature__empty {
    margin-top: 0.35rem;
    color: rgba(255, 255, 255, 0.76);
    font-size: 0.86rem;
}

.hero-countdown {
    display: grid;
    gap: 0.72rem;
    padding: 0.86rem;
    border: 1px solid rgba(212, 173, 82, 0.26);
    border-radius: 18px;
    background:
        linear-gradient(135deg, rgba(212, 173, 82, 0.16), rgba(255, 255, 255, 0.06) 44%, rgba(5, 17, 31, 0.2)),
        rgba(2, 8, 15, 0.42);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.08),
        0 20px 42px rgba(0, 0, 0, 0.12);
}

.hero-countdown__header {
    display: grid;
    gap: 0.26rem;
}

.hero-countdown__eyebrow {
    color: rgba(255, 224, 153, 0.96);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.hero-countdown__header strong {
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(1.2rem, 1.9vw, 1.54rem);
    line-height: 1.1;
}

.hero-countdown__header p {
    margin: 0;
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.76rem;
}

.hero-countdown__grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.48rem;
}

.hero-countdown__unit {
    display: grid;
    gap: 0.18rem;
    padding: 0.58rem 0.44rem;
    border: 1px solid rgba(255, 255, 255, 0.11);
    border-radius: 13px;
    background: rgba(1, 6, 11, 0.36);
    text-align: center;
}

.hero-countdown__unit strong {
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(1.38rem, 2.55vw, 1.92rem);
    line-height: 1;
}

.hero-countdown__unit span {
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.hero-countdown__status {
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.76rem;
    font-weight: 700;
}

.key-list,
.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.72rem;
}

.key-list {
    margin-top: 0;
}

.key-list > div,
.stat-item,
.summary-grid .panel {
    padding: 0.64rem 0.7rem;
    border: 1px solid rgba(255, 255, 255, 0.11);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.075);
}

.key-list strong,
.stat-item strong {
    display: block;
    margin-bottom: 0.18rem;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1.2rem;
}

.key-list span,
.stat-item {
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.76rem;
}

.page-home .hero + .page-section {
    margin-top: clamp(1.25rem, 2vw, 1.75rem);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 1rem;
    margin-bottom: 1.4rem;
}

.section-header > div {
    display: flex;
    flex-direction: column;
    gap: 0.42rem;
    min-width: 0;
}

.section-header__eyebrow {
    color: var(--brand);
    letter-spacing: 0.18em;
    font-size: 0.7rem;
    text-transform: uppercase;
}

.section-header__eyebrow::before {
    content: "";
    width: 1.2rem;
    height: 2px;
    border-radius: 2px;
    background: linear-gradient(90deg, var(--brand), var(--gold));
}

.section-header h2 {
    font-size: clamp(1.5rem, 2.1vw, 2.2rem);
    color: var(--ink);
}

.section-header h3 {
    font-size: 1.2rem;
    color: var(--ink);
}

.section-header p {
    max-width: 46rem;
    margin: 0;
    color: var(--text-muted);
    font-size: 0.95rem;
    line-height: 1.55;
}

.section-stack {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.section-shell {
    position: relative;
    overflow: hidden;
    padding: clamp(1.2rem, 1.8vw, 1.7rem);
    border: 1px solid var(--border-soft);
    border-radius: 20px;
    background: var(--paper-strong);
    box-shadow: var(--shadow-card);
}

.section-shell::before {
    content: "";
    position: absolute;
    inset: 0 0 auto;
    height: 2px;
    background: linear-gradient(90deg, transparent 0%, rgba(175, 23, 49, 0.38) 20%, rgba(212, 173, 82, 0.42) 50%, rgba(175, 23, 49, 0.38) 80%, transparent 100%);
    opacity: 0.5;
}

.section-link {
    display: inline-flex;
    align-items: center;
    gap: 0.32rem;
    color: var(--brand);
    font-weight: 700;
    font-size: 0.88rem;
}

.section-link::after {
    content: "→";
    transition: transform 0.18s ease;
}

.section-link:hover {
    color: var(--brand-strong);
}

.section-link:hover::after {
    transform: translateX(2px);
}

.split-grid,
.detail-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.32fr) minmax(320px, 0.9fr);
    gap: 1.05rem;
}

.team-grid,
.city-grid,
.round-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.9rem;
}

.card-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.9rem;
}

.listing-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.9rem;
}

.quick-links-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.72rem;
}

.panel,
.detail-shell,
.table-shell,
.entity-card,
.news-card,
.partner-card,
.match-card,
.list-card,
.empty-state {
    border: 1px solid var(--border-soft);
    border-radius: 16px;
    background: var(--paper-strong);
    box-shadow: var(--shadow-card);
}

.panel,
.detail-shell,
.table-shell__body,
.partner-card__body {
    padding: 1.1rem;
}

.detail-shell {
    padding: clamp(1.4rem, 2.2vw, 1.9rem);
}

.list-card {
    padding: 0.85rem 0.95rem;
}

.detail-media,
.entity-card__media,
.news-card__media {
    position: relative;
    overflow: hidden;
    border-radius: calc(var(--radius-lg) - 6px);
    background: linear-gradient(135deg, rgba(7, 24, 44, 0.1), rgba(175, 23, 49, 0.14));
}

.detail-media {
    min-height: 320px;
}

.entity-card,
.news-card,
.partner-card {
    overflow: hidden;
    transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
}

.entity-card__media,
.news-card__media {
    aspect-ratio: 16 / 10;
}

.news-card__media::after,
.entity-card__media::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 50%, rgba(7, 24, 44, 0.2));
}

.entity-card__body,
.news-card__body {
    padding: 1.05rem 1.1rem 1.15rem;
}

.entity-card:hover,
.news-card:hover,
.partner-card:hover,
.match-card:hover,
.list-card:hover {
    transform: translateY(-3px);
    border-color: var(--border-strong);
    box-shadow: var(--shadow-md);
}

.news-card__media-link {
    display: block;
}

.news-card__title {
    margin: 0.7rem 0 0;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.18rem;
    line-height: 1.24;
    color: var(--ink);
    letter-spacing: -0.005em;
}

.news-card__title a {
    color: inherit;
    transition: color 0.18s ease;
}

.news-card__title a:hover {
    color: var(--brand);
}

.news-card__summary {
    display: -webkit-box;
    margin: 0.55rem 0 0;
    color: var(--text-muted);
    font-size: 0.92rem;
    line-height: 1.5;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-card__footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.95rem;
    padding-top: 0.75rem;
    border-top: 1px solid var(--border-soft);
    color: var(--brand);
    font-weight: 700;
    font-size: 0.86rem;
}

.news-card__footer::after {
    content: "→";
    color: var(--brand);
    transition: transform 0.18s ease;
}

.news-card:hover .news-card__footer::after {
    transform: translateX(3px);
}

.partner-card__body p,
.entity-card__body p {
    color: var(--text-muted);
    line-height: 1.55;
}

.placeholder-badge,
.rank-pill {
    display: inline-grid;
    place-items: center;
    width: 3.1rem;
    height: 3.1rem;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--bg-accent), #1d4d79);
    color: #fff;
    font-weight: 800;
    letter-spacing: 0.08em;
}

.quick-link-card {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
    min-height: 100%;
    padding: 1.1rem 1.15rem 1.2rem;
    position: relative;
    overflow: hidden;
    border: 1px solid var(--border-soft);
    border-radius: 16px;
    background: var(--paper-strong);
    box-shadow: var(--shadow-card);
    transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
    color: var(--ink);
}

.quick-link-card::before {
    content: "";
    position: absolute;
    inset: 0 0 auto;
    height: 2px;
    background: linear-gradient(90deg, var(--brand), var(--gold));
    opacity: 0;
    transition: opacity 0.18s ease;
}

.quick-link-card::after {
    content: "→";
    position: absolute;
    top: 1.1rem;
    inset-inline-end: 1.15rem;
    color: var(--brand);
    font-size: 1.1rem;
    font-weight: 700;
    opacity: 0.45;
    transition: transform 0.18s ease, opacity 0.18s ease;
}

.quick-link-card .section-header__eyebrow {
    margin-bottom: 0.1rem;
}

.quick-link-card strong {
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.18rem;
    line-height: 1.18;
    letter-spacing: -0.01em;
    color: var(--ink);
}

.quick-link-card p {
    margin: 0;
    color: var(--text-muted);
    font-size: 0.9rem;
    line-height: 1.5;
}

.quick-link-card:hover {
    transform: translateY(-3px);
    border-color: var(--border-strong);
    box-shadow: var(--shadow-md);
}

.quick-link-card:hover::before {
    opacity: 1;
}

.quick-link-card:hover::after {
    transform: translateX(3px);
    opacity: 1;
}

.quick-link-card:focus-visible {
    box-shadow: var(--focus-ring);
}

.home-map-card {
    position: relative;
    overflow: hidden;
    display: grid;
    grid-template-columns: minmax(230px, 0.74fr) minmax(340px, 1fr) minmax(220px, 0.7fr);
    gap: 0.85rem;
    align-items: stretch;
    padding: 1rem;
    border: 1px solid rgba(212, 173, 82, 0.18);
    border-radius: 22px;
    background:
        radial-gradient(circle at 16% 16%, rgba(175, 23, 49, 0.25), transparent 28%),
        radial-gradient(circle at 92% 6%, rgba(13, 107, 69, 0.18), transparent 24%),
        linear-gradient(135deg, rgba(3, 9, 16, 0.98), rgba(9, 27, 47, 0.94) 62%, rgba(92, 14, 33, 0.9));
    color: #fff;
    box-shadow: 0 20px 52px rgba(5, 12, 22, 0.18);
}

.home-map-card::before {
    content: "";
    position: absolute;
    inset: 0 0 auto;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(212, 173, 82, 0.54), rgba(13, 107, 69, 0.28), transparent);
}

.home-map-card__intro,
.home-map-card__visual,
.home-map-card__list {
    position: relative;
    z-index: 1;
}

.home-map-card__intro {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    gap: 0.62rem;
    padding: 0.45rem;
}

.home-map-card__intro .section-header__eyebrow {
    color: rgba(255, 218, 139, 0.86);
}

.home-map-card__intro h2 {
    margin: 0;
    max-width: 10ch;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(1.45rem, 2.2vw, 2.15rem);
    line-height: 1.06;
    letter-spacing: -0.03em;
}

.home-map-card__intro p {
    margin: 0;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.9rem;
}

.home-map-card__visual {
    min-height: 15rem;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 18px;
    background:
        radial-gradient(circle at 50% 48%, rgba(255, 255, 255, 0.12), transparent 22%),
        linear-gradient(145deg, rgba(255, 255, 255, 0.08), rgba(1, 6, 11, 0.22));
}

.home-map-card__visual::before {
    content: "";
    position: absolute;
    inset: 13% 19%;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 48% 42% 54% 38%;
    transform: rotate(-14deg);
    background:
        radial-gradient(circle at 62% 42%, rgba(212, 173, 82, 0.18), transparent 9%),
        radial-gradient(circle at 44% 60%, rgba(13, 107, 69, 0.14), transparent 12%),
        rgba(255, 255, 255, 0.035);
}

.home-map-card__map-label {
    position: absolute;
    top: 0.75rem;
    inset-inline-start: 0.75rem;
    z-index: 2;
    padding: 0.28rem 0.58rem;
    border-radius: 999px;
    background: rgba(1, 6, 11, 0.48);
    color: rgba(255, 255, 255, 0.74);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.home-map-card__pin {
    position: absolute;
    z-index: 3;
    top: var(--y);
    left: var(--x);
    width: 0.9rem;
    height: 0.9rem;
    border: 2px solid rgba(255, 255, 255, 0.82);
    border-radius: 999px;
    transform: translate(-50%, -50%);
    box-shadow: 0 0 0 5px rgba(255, 255, 255, 0.1), 0 10px 20px rgba(0, 0, 0, 0.2);
}

.home-map-card__pin--city {
    background: var(--brand);
}

.home-map-card__pin--stadium {
    background: var(--gold);
}

.home-map-card__pin span {
    position: absolute;
    top: calc(100% + 0.38rem);
    left: 50%;
    width: max-content;
    max-width: 9rem;
    padding: 0.22rem 0.46rem;
    border-radius: 999px;
    background: rgba(1, 6, 11, 0.72);
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.68rem;
    font-weight: 700;
    line-height: 1.2;
    opacity: 0;
    pointer-events: none;
    transform: translateX(-50%) translateY(-0.15rem);
    transition: opacity 0.18s ease, transform 0.18s ease;
}

.home-map-card__pin:hover span,
.home-map-card__pin:focus span {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

.home-map-card__list {
    display: grid;
    gap: 0.5rem;
}

.home-map-card__location {
    display: grid;
    gap: 0.12rem;
    padding: 0.64rem 0.7rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.07);
}

.home-map-card__location span {
    color: rgba(255, 218, 139, 0.78);
    font-size: 0.64rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.home-map-card__location strong {
    font-family: Georgia, "Times New Roman", serif;
    font-size: 0.98rem;
}

.home-map-card__location small {
    color: rgba(255, 255, 255, 0.66);
}

.home-map-card__location:hover {
    background: rgba(255, 255, 255, 0.12);
}

.auth-shell {
    width: min(100%, 980px);
    margin-inline: auto;
}

.auth-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.05fr) minmax(280px, 0.85fr);
    gap: 1.35rem;
    align-items: start;
}

.auth-card,
.account-panel {
    padding: clamp(1.35rem, 2vw, 1.85rem);
}

.auth-card h2,
.account-panel h2 {
    margin: 0 0 0.55rem;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1.55rem;
    line-height: 1.18;
}

.auth-card p,
.account-panel p {
    margin: 0;
    color: var(--text-muted);
}

.auth-form,
.account-form {
    display: grid;
    gap: 1rem;
    margin-top: 1.35rem;
}

.form-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.field-group {
    display: grid;
    gap: 0.42rem;
}

.field-group label {
    color: var(--text-soft);
    font-size: 0.88rem;
    font-weight: 700;
}

.form-control {
    width: 100%;
    min-height: 3rem;
    padding: 0.8rem 0.95rem;
    border: 1px solid rgba(10, 29, 51, 0.14);
    border-radius: var(--radius-sm);
    background: rgba(255, 255, 255, 0.96);
    color: var(--text);
    font: inherit;
    transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
}

textarea.form-control {
    min-height: 8.5rem;
    resize: vertical;
}

.form-control:focus {
    outline: none;
    border-color: rgba(175, 23, 49, 0.36);
    box-shadow: 0 0 0 4px rgba(175, 23, 49, 0.08);
    background: #fff;
}

.form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    align-items: center;
}

.form-helper {
    color: var(--text-muted);
    font-size: 0.88rem;
}

.form-error {
    color: var(--danger);
    font-size: 0.88rem;
    font-weight: 700;
}

.auth-note {
    display: grid;
    gap: 0.9rem;
}

.auth-note__list {
    display: grid;
    gap: 0.75rem;
    margin: 0;
    padding: 0;
    list-style: none;
}

.auth-note__list li {
    display: grid;
    gap: 0.2rem;
    padding: 0.9rem 1rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.82);
}

.auth-note__list strong {
    color: var(--text);
}

.account-layout {
    display: grid;
    grid-template-columns: minmax(230px, 0.34fr) minmax(0, 1fr);
    gap: 1.4rem;
    align-items: start;
}

.account-sidebar {
    position: sticky;
    top: 7.75rem;
}

.account-nav {
    display: grid;
    gap: 0.55rem;
}

.account-nav__link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.8rem;
    min-height: 3rem;
    padding: 0.78rem 0.92rem;
    border: 1px solid transparent;
    border-radius: var(--radius-md);
    color: var(--text-soft);
    font-weight: 700;
}

.account-nav__link:hover {
    border-color: rgba(10, 29, 51, 0.08);
    background: rgba(10, 29, 51, 0.04);
    color: var(--text);
}

.account-nav__link.is-active {
    border-color: rgba(175, 23, 49, 0.12);
    background: rgba(175, 23, 49, 0.08);
    color: var(--brand-strong);
}

.account-nav__meta {
    color: var(--text-muted);
    font-size: 0.8rem;
    font-weight: 700;
}

.account-stat {
    display: grid;
    gap: 0.25rem;
}

.account-stat strong {
    font-size: clamp(1.8rem, 3vw, 2.25rem);
    line-height: 1;
    color: var(--text);
}

.account-stat span {
    color: var(--text-muted);
    font-size: 0.92rem;
    font-weight: 700;
}

.account-inline-list {
    display: grid;
    gap: 0.9rem;
}

.account-inline-list .list-card {
    display: grid;
    gap: 0.35rem;
}

.account-inline-list .list-card strong {
    color: var(--text);
}

.account-inline-list .list-card p,
.account-inline-list .list-card small {
    color: var(--text-muted);
}

.account-empty {
    margin-top: 1rem;
}

.rank-pill {
    width: 2.1rem;
    height: 2.1rem;
    background: linear-gradient(135deg, rgba(175, 23, 49, 0.16), rgba(10, 29, 51, 0.15));
    color: var(--brand);
    letter-spacing: 0;
}

.badge-row,
.detail-meta,
.match-card__competition,
.match-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
}

.badge,
.meta-pill,
.status-pill {
    display: inline-flex;
    align-items: center;
    min-height: 1.62rem;
    padding: 0.18rem 0.62rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    line-height: 1.1;
}

.badge {
    background: var(--gold-soft);
    color: var(--warning);
    border: 1px solid rgba(212, 173, 82, 0.32);
}

.meta-pill {
    background: rgba(7, 24, 44, 0.06);
    color: var(--text-soft);
    border: 1px solid var(--border-soft);
}

.meta-pill--soft {
    background: var(--bg-soft);
}

.status-pill {
    color: #fff;
    background: #36506e;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-size: 0.66rem;
    padding: 0.22rem 0.7rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
}

.status-pill--completed {
    background: linear-gradient(135deg, var(--success), #0d5a3f);
}

.status-pill--live {
    background: linear-gradient(135deg, var(--brand-bright), var(--brand));
    position: relative;
    padding-inline-start: 1.2rem;
}

.status-pill--live::before {
    content: "";
    position: absolute;
    inset-inline-start: 0.55rem;
    top: 50%;
    width: 0.45rem;
    height: 0.45rem;
    margin-top: -0.225rem;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
    animation: pulseDot 1.6s infinite;
}

@keyframes pulseDot {
    0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
    70% { box-shadow: 0 0 0 6px rgba(255, 255, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
}

.status-pill--postponed,
.status-pill--scheduled {
    background: linear-gradient(135deg, #2c4666, #1b3251);
}

.status-pill--cancelled {
    background: linear-gradient(135deg, var(--danger), #6b1a2a);
}

.match-card {
    position: relative;
    overflow: hidden;
    padding: 1rem 1.1rem 1.05rem;
    background: var(--paper-strong);
    transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
}

.match-card::before {
    content: "";
    position: absolute;
    inset: 0 auto 0 0;
    width: 3px;
    background: linear-gradient(180deg, var(--brand) 0%, var(--gold) 60%, var(--brand) 100%);
}

.match-card__head {
    display: flex;
    justify-content: space-between;
    gap: 0.72rem;
    align-items: flex-start;
}

.match-card__date {
    text-align: end;
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 600;
}

.match-card__teams {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
    align-items: center;
    gap: 0.9rem;
    margin-top: 0.95rem;
}

.match-card__team {
    display: flex;
    flex-direction: column;
    gap: 0.22rem;
    min-width: 0;
}

.match-card__team strong {
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.08rem;
    line-height: 1.2;
    color: var(--ink);
    letter-spacing: -0.005em;
}

.match-card__team--away {
    text-align: end;
}

.match-card__label {
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: var(--text-muted);
    font-size: 0.62rem;
    font-weight: 700;
}

.score-box {
    min-width: 92px;
    padding: 0.7rem 0.85rem;
    border-radius: 12px;
    background:
        linear-gradient(135deg, rgba(7, 24, 44, 0.04), rgba(175, 23, 49, 0.04)),
        rgba(7, 24, 44, 0.03);
    border: 1px solid var(--border-soft);
    text-align: center;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.42rem;
    font-weight: 700;
    line-height: 1;
    color: var(--ink);
}

.score-box__sub {
    margin-top: 0.36rem;
    font-size: 0.66rem;
    color: var(--text-muted);
    font-family: "Inter", "Segoe UI", sans-serif;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.match-card__meta {
    justify-content: space-between;
    align-items: center;
    margin-top: 0.95rem;
    padding-top: 0.75rem;
    border-top: 1px solid var(--border-soft);
}

.match-card__cta {
    margin-inline-start: auto;
    min-height: 2.2rem;
    padding: 0.42rem 0.95rem;
    font-size: 0.78rem;
}

.match-shell .summary-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.match-shell {
    padding: 1.5rem;
    border: 1px solid var(--border);
    border-radius: calc(var(--radius-xl) + 2px);
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(248, 251, 255, 0.92));
    box-shadow: var(--shadow-md);
}

.match-shell .summary-grid .panel {
    border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.82);
}

.match-shell .match-card__teams {
    margin-top: 1.5rem;
    padding: 1.6rem;
    border: 1px solid var(--border);
    border-radius: calc(var(--radius-lg) - 4px);
    background:
        linear-gradient(135deg, rgba(15, 34, 56, 0.03), rgba(175, 23, 49, 0.05)),
        #fff;
}

.match-shell .score-box {
    min-width: 140px;
    padding: 1.15rem 1.2rem;
    font-size: 2.2rem;
}

.timeline {
    display: flex;
    flex-direction: column;
    gap: 0.95rem;
    position: relative;
}

.timeline::before {
    content: "";
    position: absolute;
    top: 0;
    bottom: 0;
    inset-inline-start: 1.65rem;
    width: 2px;
    background: linear-gradient(180deg, rgba(175, 23, 49, 0.18), rgba(10, 29, 51, 0.1));
}

.timeline-item {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    gap: 1rem;
    align-items: start;
    position: relative;
}

.timeline-minute {
    min-width: 3.4rem;
    padding: 0.65rem 0.7rem;
    border-radius: 999px;
    background: var(--brand-soft);
    color: var(--brand);
    text-align: center;
    font-weight: 800;
}

.lineup-columns {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.lineup-list {
    list-style: none;
    margin: 0.75rem 0 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.lineup-list li {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border);
}

.detail-copy {
    color: var(--text-soft);
    white-space: pre-line;
}

.article-summary-box {
    margin: 0 0 1.25rem;
    padding: 1rem 1.15rem;
    border-inline-start: 4px solid var(--brand);
    border-radius: 0 var(--radius-md) var(--radius-md) 0;
    background: linear-gradient(135deg, rgba(175, 23, 49, 0.06), rgba(10, 29, 51, 0.02));
    color: var(--text-soft);
}

.related-story {
    padding: 1rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.76);
}

.match-feed {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.round-panel {
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(245, 249, 253, 0.94));
}

.round-panel__count {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    background: var(--gold-soft);
    color: var(--warning);
    font-size: 0.78rem;
    font-weight: 700;
}

.detail-grid > aside {
    position: sticky;
    top: 7.6rem;
    align-self: start;
}

.meta-list strong {
    display: block;
    color: var(--text-muted);
    font-size: 0.8rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.meta-list div,
.meta-list a {
    margin-top: 0.3rem;
}

.table-shell {
    overflow: hidden;
}

.standings-table {
    padding: 0;
}

.standings-table table {
    width: 100%;
    border-collapse: collapse;
}

.standings-table th,
.standings-table td {
    padding: 0.95rem 0.9rem;
    border-bottom: 1px solid var(--border);
    text-align: start;
    font-size: 0.92rem;
}

.standings-table th {
    background: rgba(10, 29, 51, 0.04);
    color: var(--text-muted);
    font-size: 0.76rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.standings-table tbody tr:hover {
    background: rgba(10, 29, 51, 0.02);
}

.standings-table__team a {
    font-weight: 700;
}

.standings-table__points {
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1.02rem;
}

.empty-state {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    gap: 1.1rem;
    align-items: center;
    padding: 1.1rem 1.2rem;
    border: 1px dashed var(--border-strong);
    background:
        linear-gradient(135deg, rgba(7, 24, 44, 0.02), rgba(175, 23, 49, 0.025) 60%, transparent),
        var(--paper-soft);
    color: var(--text-soft);
    box-shadow: none;
}

.empty-state__mark {
    display: inline-grid;
    place-items: center;
    width: 2.6rem;
    height: 2.6rem;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(7, 24, 44, 0.06), rgba(175, 23, 49, 0.12));
    border: 1px solid var(--border-soft);
    color: var(--brand);
    font-size: 0.74rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    font-family: "Georgia", "Times New Roman", serif;
}

.empty-state strong {
    display: block;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.05rem;
    color: var(--ink);
    letter-spacing: -0.005em;
}

.empty-state p {
    margin: 0.3rem 0 0;
    color: var(--text-muted);
    line-height: 1.55;
}

.search-shell {
    display: flex;
    flex-direction: column;
    gap: 1.4rem;
}

.search-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 0.85rem;
    align-items: center;
    padding: 1rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    background: rgba(255, 255, 255, 0.88);
    box-shadow: var(--shadow-sm);
}

.search-form__field {
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
}

.search-form__field label {
    color: var(--text-muted);
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.search-form__field input {
    width: 100%;
    min-height: 3.2rem;
    padding: 0.8rem 1rem;
    border: 1px solid var(--border);
    border-radius: 16px;
    background: #fff;
    color: var(--text);
    font: inherit;
}

.search-form__field input:focus {
    outline: 2px solid rgba(175, 23, 49, 0.16);
    outline-offset: 2px;
    border-color: rgba(175, 23, 49, 0.22);
}

.search-summary {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
}

.search-summary__pill {
    display: inline-flex;
    align-items: center;
    min-height: 2rem;
    padding: 0.34rem 0.78rem;
    border-radius: 999px;
    background: rgba(10, 29, 51, 0.06);
    color: var(--text-soft);
    font-size: 0.82rem;
    font-weight: 700;
}

.search-groups {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1.2rem;
}

.search-group {
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
}

.result-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.result-card {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
    padding: 1rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.9);
}

.result-card strong {
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1.06rem;
    line-height: 1.2;
}

.result-card p {
    margin: 0;
    color: var(--text-muted);
}

.result-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
}

.map-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr);
    gap: 1.4rem;
}

.map-shell {
    position: relative;
    padding: 1.3rem;
    border: 1px solid var(--border);
    border-radius: calc(var(--radius-xl) + 2px);
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(245, 249, 253, 0.92));
    box-shadow: var(--shadow-md);
}

.map-shell__intro h2 {
    margin-top: 0.35rem;
    font-size: clamp(1.55rem, 2.2vw, 2.2rem);
}

.map-shell__intro p {
    margin: 0.55rem 0 0;
    color: var(--text-muted);
}

.map-overview {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.9rem;
    margin: 1.15rem 0 1rem;
}

.map-overview__card {
    padding: 0.9rem 0.95rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.84);
}

.map-overview__card strong {
    display: block;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1.3rem;
}

.map-overview__card span {
    display: block;
    margin-top: 0.2rem;
    color: var(--text-muted);
    font-size: 0.82rem;
}

.map-canvas {
    position: relative;
    min-height: 33rem;
    border-radius: 24px;
    overflow: hidden;
    background:
        radial-gradient(circle at 18% 24%, rgba(212, 173, 82, 0.18), transparent 26%),
        radial-gradient(circle at 82% 18%, rgba(175, 23, 49, 0.14), transparent 24%),
        linear-gradient(180deg, rgba(15, 41, 70, 0.08), rgba(15, 41, 70, 0.02)),
        #eef4fb;
    border: 1px solid rgba(10, 29, 51, 0.08);
}

.map-canvas::before {
    content: "";
    position: absolute;
    inset: 8% 10%;
    border-radius: 28px;
    background:
        linear-gradient(135deg, rgba(255, 255, 255, 0.78), rgba(255, 255, 255, 0.28)),
        radial-gradient(circle at center, rgba(10, 29, 51, 0.03), transparent 62%);
    box-shadow: inset 0 0 0 1px rgba(10, 29, 51, 0.06);
    pointer-events: none;
    z-index: 0;
}

.map-canvas__label {
    position: absolute;
    top: 1rem;
    inset-inline-start: 1rem;
    z-index: 1;
    padding: 0.42rem 0.8rem;
    border-radius: 999px;
    background: rgba(10, 29, 51, 0.82);
    color: #fff;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.map-canvas__fallback {
    position: absolute;
    inset: auto 1.15rem 1.15rem;
    z-index: 420;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    max-width: 22rem;
    padding: 0.85rem 0.95rem;
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 16px;
    background: rgba(10, 29, 51, 0.74);
    color: rgba(255, 255, 255, 0.92);
    pointer-events: none;
}

.map-canvas__fallback strong {
    font-size: 0.95rem;
}

.map-canvas__fallback span {
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.82rem;
    line-height: 1.5;
}

.map-canvas[data-ready="true"] .map-canvas__fallback {
    opacity: 0;
    visibility: hidden;
}

.leaflet-container {
    width: 100%;
    height: 100%;
    border-radius: 24px;
    background: transparent;
}

.leaflet-control-zoom {
    border: 0 !important;
    box-shadow: var(--shadow-sm) !important;
}

.leaflet-control-zoom a {
    color: var(--text) !important;
}

.leaflet-popup-content-wrapper,
.leaflet-popup-tip {
    background: rgba(255, 255, 255, 0.98);
    box-shadow: var(--shadow-sm);
}

.leaflet-popup-content {
    margin: 0.9rem 1rem;
}

.leaflet-host-marker-wrap {
    background: transparent;
    border: 0;
}

.leaflet-host-marker {
    position: relative;
    display: inline-grid;
    place-items: center;
    width: 22px;
    height: 22px;
}

.leaflet-host-marker__pulse,
.leaflet-host-marker__core {
    position: absolute;
    border-radius: 999px;
}

.leaflet-host-marker__pulse {
    inset: 0;
    opacity: 0.24;
}

.leaflet-host-marker__core {
    inset: 4px;
    border: 2px solid rgba(255, 255, 255, 0.9);
    box-shadow: 0 0 0 1px rgba(10, 29, 51, 0.08);
}

.leaflet-host-marker--city .leaflet-host-marker__pulse,
.leaflet-host-marker--city .leaflet-host-marker__core {
    background: var(--brand);
}

.leaflet-host-marker--stadium .leaflet-host-marker__pulse,
.leaflet-host-marker--stadium .leaflet-host-marker__core {
    background: var(--gold);
}

.map-popup {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.map-popup strong {
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1rem;
}

.map-popup span {
    color: var(--text-muted);
    font-size: 0.84rem;
}

.map-popup a {
    margin-top: 0.1rem;
    color: var(--brand);
    font-weight: 700;
}

.map-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 0.7rem;
    margin-top: 1rem;
}

.map-legend__item {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    color: var(--text-soft);
    font-size: 0.85rem;
    font-weight: 700;
}

.map-legend__dot {
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 999px;
}

.map-legend__dot--city {
    background: var(--brand);
}

.map-legend__dot--stadium {
    background: var(--gold);
}

.map-lists {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.map-shell__note {
    margin: 0.8rem 0 0;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.map-detail-card__eyebrow {
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem;
    align-items: center;
}

.map-detail-card__kind {
    margin-top: 0.95rem;
    color: var(--brand);
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-size: 0.78rem;
    font-weight: 800;
}

.map-detail-card h2 {
    margin-top: 0.5rem;
}

.map-detail-card__meta,
.map-detail-card__summary {
    margin: 0.45rem 0 0;
    color: var(--text-soft);
}

.map-detail-card__summary {
    color: var(--text-muted);
}

.map-detail-card__stats {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.75rem;
    margin-top: 1rem;
}

.map-detail-card__stat {
    padding: 0.85rem 0.9rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.72);
}

.map-detail-card__stat span {
    display: block;
    color: var(--text-muted);
    font-size: 0.78rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.map-detail-card__stat strong {
    display: block;
    margin-top: 0.3rem;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1rem;
}

.map-detail-card__actions {
    margin-top: 1rem;
}

.map-location-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.map-location-card.is-active {
    border-color: rgba(175, 23, 49, 0.28);
    box-shadow: var(--shadow-md);
}

.map-location-card__header {
    display: flex;
    justify-content: space-between;
    gap: 0.85rem;
    align-items: flex-start;
}

.map-location-card__title {
    display: inline-block;
    margin-top: 0.18rem;
    font-weight: 700;
}

.map-location-card__meta {
    margin: 0.5rem 0 0.75rem;
    color: var(--text-muted);
}

.map-focus-link {
    display: inline-flex;
    align-items: center;
    min-height: 2rem;
    padding: 0.38rem 0.75rem;
    border: 1px solid var(--border);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.92);
    color: var(--text);
    font-size: 0.82rem;
    font-weight: 700;
}

.map-focus-link:hover,
.map-focus-link.is-active {
    border-color: rgba(175, 23, 49, 0.28);
    background: rgba(175, 23, 49, 0.08);
    color: var(--brand);
}

.pagination-shell {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1.5rem;
    padding: 0.55rem;
    border: 1px solid var(--border);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.82);
    box-shadow: var(--shadow-sm);
}

.pagination-shell a,
.pagination-shell span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    min-height: 2.5rem;
    padding: 0.35rem 0.8rem;
    border-radius: 999px;
    font-weight: 700;
    color: var(--text-soft);
}

.pagination-shell .is-active {
    background: linear-gradient(135deg, var(--brand), #d53c58);
    color: #fff;
}

.site-footer {
    margin-top: clamp(2.8rem, 5vw, 4.2rem);
    padding: 0 0 2rem;
}

.site-footer__inner {
    position: relative;
    overflow: hidden;
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) repeat(2, minmax(170px, 0.75fr)) minmax(220px, 0.95fr);
    gap: 1.5rem;
    padding: clamp(1.6rem, 2.4vw, 2.4rem);
    border: 1px solid rgba(212, 173, 82, 0.16);
    border-radius: 22px;
    background:
        radial-gradient(circle at 92% 0%, rgba(175, 23, 49, 0.18), transparent 24%),
        linear-gradient(135deg, #04090f, #061629 60%, #2c0a17);
    color: #fff;
    box-shadow: var(--shadow-xl);
}

.site-footer__inner::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(212, 173, 82, 0.45), transparent);
    pointer-events: none;
}

.site-footer__brand-lockup {
    display: inline-flex;
    align-items: center;
    gap: 0.72rem;
}

.site-footer__mark {
    display: inline-grid;
    place-items: center;
    width: 2.4rem;
    height: 2.4rem;
    border-radius: 10px;
    background: linear-gradient(145deg, var(--brand-bright), var(--brand) 58%, var(--burgundy));
    color: #fff;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 0.88rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.24),
        0 8px 20px rgba(175, 23, 49, 0.28);
}

.site-footer__brand strong {
    display: block;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.32rem;
    letter-spacing: -0.005em;
}

.site-footer__brand p,
.site-footer__note p {
    margin: 0.75rem 0 0;
    color: rgba(255, 255, 255, 0.74);
    font-size: 0.92rem;
    line-height: 1.6;
    max-width: 24rem;
}

.site-footer__links,
.site-footer__note {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

.site-footer__label {
    color: var(--gold);
    text-transform: uppercase;
    letter-spacing: 0.16em;
    font-size: 0.68rem;
    font-weight: 800;
    margin-bottom: 0.2rem;
}

.site-footer__links a {
    color: rgba(255, 255, 255, 0.86);
    font-size: 0.92rem;
    font-weight: 600;
    transition: color 0.18s ease, transform 0.18s ease;
    display: inline-block;
}

.site-footer__links a:hover {
    color: var(--gold);
    transform: translateX(2px);
}

.site-footer__links a:focus-visible {
    box-shadow: var(--focus-ring-light);
    border-radius: 4px;
}

.footer-utility-link {
    align-self: flex-start;
    margin-top: 0.4rem;
    padding: 0.45rem 0.85rem;
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 999px;
    color: rgba(255, 255, 255, 0.84);
    font-weight: 700;
    font-size: 0.82rem;
}

.footer-utility-link:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(212, 173, 82, 0.4);
    color: #fff;
}

[dir="rtl"] body {
    font-family: "Segoe UI Variable", Tahoma, "Noto Naskh Arabic", sans-serif;
}

[dir="rtl"] .site-header__top-inner,
[dir="rtl"] .site-header__status-line,
[dir="rtl"] .site-header__top-links,
[dir="rtl"] .hero__actions,
[dir="rtl"] .hero__quicklinks,
[dir="rtl"] .site-header__audience-tools,
[dir="rtl"] .match-card__competition,
[dir="rtl"] .match-card__meta,
[dir="rtl"] .search-summary,
[dir="rtl"] .result-card__meta,
[dir="rtl"] .map-legend {
    flex-direction: row-reverse;
}

[dir="rtl"] .site-header__nav-shell,
[dir="rtl"] .site-nav {
    justify-content: flex-start;
}

[dir="rtl"] .match-card::before {
    inset: 0 0 0 auto;
}

[dir="rtl"] .article-summary-box {
    border-inline-start: 0;
    border-inline-end: 4px solid var(--brand);
    border-radius: var(--radius-md) 0 0 var(--radius-md);
}

[dir="rtl"] .timeline-item {
    grid-template-columns: minmax(0, 1fr) auto;
}

[dir="rtl"] .timeline::before {
    inset-inline-start: auto;
    inset-inline-end: 1.65rem;
}

[dir="rtl"] .timeline-minute {
    order: 2;
}

[dir="rtl"] .form-actions,
[dir="rtl"] .account-nav__link {
    flex-direction: row-reverse;
}

@media (max-width: 1220px) {
    .card-grid,
    .team-grid,
    .city-grid,
    .round-grid,
    .search-groups {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .listing-grid,
    .quick-links-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .key-list,
    .summary-grid,
    .match-shell .summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .site-footer__inner {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .map-overview {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .auth-grid {
        grid-template-columns: 1fr;
    }

    .hero__support-grid {
        grid-template-columns: 1fr;
    }

    .home-map-card {
        grid-template-columns: minmax(0, 0.82fr) minmax(0, 1fr);
    }

    .home-map-card__list {
        grid-column: 1 / -1;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

@media (max-width: 1080px) {
    .site-header__inner {
        grid-template-columns: 1fr;
    }

    .site-header__nav-shell {
        order: 2;
        justify-content: flex-start;
        align-items: flex-start;
        flex-direction: column;
    }

    .hero,
    .split-grid,
    .detail-grid,
    .map-layout,
    .account-layout {
        grid-template-columns: 1fr;
    }

    .detail-grid > aside {
        position: static;
    }

    .account-sidebar {
        position: static;
    }

    .listing-grid,
    .quick-links-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .site-footer__inner {
        grid-template-columns: 1fr 1fr;
    }

    .site-header__audience-tools {
        width: 100%;
        justify-content: flex-start;
        flex-wrap: wrap;
    }

    .header-account {
        min-width: 0;
    }

    .home-map-card {
        grid-template-columns: 1fr;
    }

    .home-map-card__list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 860px) {
    .page-container,
    .site-header__top-inner,
    .site-header__inner,
    .site-footer__inner {
        width: min(calc(100% - 32px), var(--container));
    }

    .card-grid,
    .team-grid,
    .city-grid,
    .round-grid,
    .listing-grid,
    .quick-links-grid,
    .match-feed,
    .lineup-columns,
    .search-groups,
    .form-grid-2 {
        grid-template-columns: 1fr;
    }

    .section-header,
    .match-card__head,
    .match-card__meta,
    .site-header__top-inner {
        align-items: flex-start;
        flex-direction: column;
    }

    .site-header__status-line {
        flex-wrap: wrap;
    }

    .site-header__top-links {
        white-space: normal;
        flex-wrap: wrap;
    }

    .language-switcher {
        margin-inline-start: 0;
        padding-inline-start: 0;
        border-inline-start: 0;
    }

    .match-card__teams,
    .match-shell .match-card__teams {
        grid-template-columns: 1fr;
        text-align: start;
    }

    .hero-countdown__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .match-card__team--away {
        text-align: start;
    }

    .score-box,
    .match-shell .score-box {
        width: 100%;
        min-width: 0;
    }

    .site-footer__inner {
        grid-template-columns: 1fr;
    }

    .map-detail-card__stats {
        grid-template-columns: 1fr;
    }

    .map-location-card__header {
        flex-direction: column;
        align-items: flex-start;
    }

    .home-map-card__visual {
        min-height: 13rem;
    }
}

@media (max-width: 640px) {
    main {
        padding-top: 0.65rem;
        padding-bottom: 3rem;
    }

    .hero,
    .page-header__content,
    .section-shell,
    .match-shell {
        padding: 1.05rem;
    }

    .search-form {
        grid-template-columns: 1fr;
    }

    .map-shell {
        min-height: 0;
    }

    .map-canvas {
        min-height: 28rem;
    }

    .hero h1 {
        max-width: none;
    }

    .hero__quicklinks {
        gap: 0.5rem;
    }

    .home-map-card__list {
        grid-template-columns: 1fr;
    }

    .empty-state {
        grid-template-columns: 1fr;
    }

    .key-list,
    .summary-grid,
    .match-shell .summary-grid {
        grid-template-columns: 1fr;
    }

    .standings-table {
        overflow-x: auto;
    }

    .standings-table th,
    .standings-table td {
        padding: 0.8rem 0.7rem;
        font-size: 0.86rem;
    }

    .page-container,
    .site-header__top-inner,
    .site-header__inner,
    .site-footer__inner {
        width: min(calc(100% - 24px), var(--container));
    }
}

/* Strict top/homepage recomposition: public shell, hero, first viewport, curated previews. */
.page-home main {
    padding-top: 0.58rem;
}

.site-header {
    background:
        linear-gradient(90deg, rgba(3, 5, 9, 0.99), rgba(8, 13, 22, 0.99) 58%, rgba(42, 6, 16, 0.98)),
        #050609;
    border-bottom-color: rgba(212, 173, 82, 0.18);
    box-shadow: 0 12px 34px rgba(0, 0, 0, 0.28);
}

.site-header__top {
    border-bottom-color: rgba(255, 255, 255, 0.07);
}

.site-header__top-inner {
    min-height: 1.9rem;
    padding-block: 0.18rem;
}

.site-header__status-line {
    gap: 0.42rem;
}

.site-header__status-pill {
    min-height: 1.22rem;
    padding: 0.07rem 0.42rem;
    border-color: rgba(212, 173, 82, 0.26);
    background: rgba(212, 173, 82, 0.1);
    font-size: 0.58rem;
}

.site-header__status {
    color: rgba(255, 255, 255, 0.58);
    font-size: 0.7rem;
}

.site-header__top-links {
    gap: 0.1rem;
}

.site-header__top-links a,
.site-header__top-links button,
.language-switcher__link {
    min-height: 1.42rem;
    padding: 0.1rem 0.38rem;
    border-radius: 7px;
    color: rgba(255, 255, 255, 0.62);
    font-size: 0.68rem;
}

.site-header__admin-link {
    color: rgba(255, 255, 255, 0.42) !important;
    font-size: 0.62rem;
}

.language-switcher {
    gap: 0.08rem;
    margin-inline-start: 0.08rem;
    padding-inline-start: 0.08rem;
}

.language-switcher__link {
    min-width: 1.44rem;
    font-size: 0.6rem;
}

.site-header__inner {
    display: grid;
    grid-template-columns: minmax(155px, 0.25fr) minmax(0, 1fr) auto;
    gap: clamp(0.5rem, 1vw, 0.9rem);
    min-height: 3.05rem;
    padding-block: 0.36rem;
}

.brand {
    gap: 0.5rem;
    min-width: 0;
}

.brand__mark {
    width: 2rem;
    height: 2rem;
    border-radius: 0.48rem;
    font-size: 0.78rem;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.2),
        0 8px 18px rgba(175, 23, 49, 0.24);
}

.brand__meta {
    font-size: 0.88rem;
    line-height: 1.05;
}

.brand__meta small {
    margin-top: 0.02rem;
    font-size: 0.52rem;
    letter-spacing: 0.11em;
}

.site-nav {
    justify-content: center;
    gap: 0.02rem;
    min-width: 0;
}

.site-nav a {
    position: relative;
    padding: 0.42rem 0.52rem;
    border-radius: 0;
    color: rgba(255, 255, 255, 0.68);
    font-size: 0.75rem;
    line-height: 1;
    white-space: nowrap;
}

.site-nav a::after {
    content: "";
    position: absolute;
    inset-inline: 0.52rem;
    bottom: 0.08rem;
    height: 2px;
    border-radius: 999px;
    background: transparent;
}

.site-nav a:hover,
.site-nav a.is-active {
    background: transparent;
    box-shadow: none;
    color: #fff;
}

.site-nav a.is-active::after {
    background: linear-gradient(90deg, var(--brand), var(--gold));
}

.site-header__audience-tools {
    gap: 0.34rem;
    justify-content: flex-end;
}

.site-header__audience-tools .header-link,
.site-header__audience-tools .button {
    min-height: 1.92rem;
    padding: 0.38rem 0.68rem;
    font-size: 0.74rem;
}

.site-header__audience-tools .header-link--quiet {
    background: transparent;
    border-color: rgba(255, 255, 255, 0.18);
}

.site-header__cta-primary {
    background: linear-gradient(135deg, #c51d39, #8f1329);
    box-shadow: 0 8px 18px rgba(175, 23, 49, 0.22);
}

.header-account {
    min-height: 1.92rem;
    padding: 0.28rem 0.58rem;
    border-radius: 8px;
}

.header-account__eyebrow {
    display: none;
}

.header-account strong {
    font-size: 0.74rem;
}

.hero--command {
    display: grid;
    grid-template-columns: minmax(0, 0.95fr) minmax(420px, 0.92fr);
    gap: clamp(0.9rem, 2vw, 1.35rem);
    min-height: clamp(28rem, 55vh, 36rem);
    padding: clamp(1.35rem, 2.3vw, 2.35rem);
    align-items: stretch;
    border-radius: 0;
    border: 1px solid rgba(255, 255, 255, 0.08);
    background:
        linear-gradient(90deg, rgba(2, 5, 10, 0.93), rgba(4, 12, 21, 0.9) 42%, rgba(98, 12, 31, 0.72)),
        radial-gradient(circle at 72% 14%, rgba(212, 173, 82, 0.28), transparent 21%),
        radial-gradient(circle at 18% 18%, rgba(175, 23, 49, 0.34), transparent 26%),
        #050914;
    box-shadow: 0 24px 70px rgba(0, 0, 0, 0.24);
}

.hero--command::before {
    inset: 8% auto auto 38%;
    width: clamp(200px, 26vw, 400px);
    opacity: 0.75;
}

.hero--command::after {
    background:
        linear-gradient(110deg, rgba(255, 255, 255, 0.07), transparent 32%),
        repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.022) 0 1px, transparent 1px 18px);
}

.hero__main,
.hero__board {
    position: relative;
    z-index: 1;
}

.hero__main {
    display: flex;
    flex-direction: column;
    justify-content: center;
    max-width: 44rem;
}

.hero__eyebrow {
    color: rgba(255, 224, 153, 0.9);
    font-size: 0.72rem;
}

.hero__chip-row {
    margin-top: 0.56rem;
}

.hero-chip {
    min-height: 1.34rem;
    padding: 0.1rem 0.48rem;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.075);
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.58rem;
}

.hero h1 {
    max-width: 9.8ch;
    margin-top: 0.42rem;
    font-size: clamp(3.25rem, 7.1vw, 7rem);
    line-height: 0.9;
    letter-spacing: -0.065em;
}

.hero h1 span {
    margin-top: 0.25rem;
    color: rgba(255, 255, 255, 0.76);
    font-family: "Segoe UI Variable", "Trebuchet MS", "Segoe UI", sans-serif;
    font-size: clamp(0.92rem, 1.35vw, 1.22rem);
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.hero p {
    max-width: 36rem;
    margin-top: 0.85rem;
    color: rgba(255, 255, 255, 0.76);
    font-size: clamp(0.94rem, 1.05vw, 1.06rem);
}

.hero__actions {
    gap: 0.48rem;
    margin-top: 1.05rem;
}

.hero__actions .button,
.hero__actions .button--subtle {
    min-height: 2.25rem;
    padding: 0.48rem 0.86rem;
}

.hero__quicklinks {
    gap: 0.34rem;
    margin-top: 0.8rem;
}

.hero__quicklink {
    min-height: 1.62rem;
    padding: 0.24rem 0.54rem;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.055);
    font-size: 0.7rem;
}

.hero__board {
    display: grid;
    grid-template-rows: auto minmax(0, 1fr) auto;
    gap: 0.62rem;
    align-self: stretch;
}

.hero-countdown {
    gap: 0.66rem;
    padding: 0.82rem;
    border-color: rgba(212, 173, 82, 0.34);
    border-radius: 10px;
    background:
        linear-gradient(135deg, rgba(212, 173, 82, 0.18), rgba(255, 255, 255, 0.055) 42%, rgba(2, 8, 15, 0.42)),
        rgba(0, 0, 0, 0.26);
}

.hero-countdown__header {
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: end;
    gap: 0.45rem;
}

.hero-countdown__eyebrow {
    grid-column: 1 / -1;
    font-size: 0.62rem;
}

.hero-countdown__header strong {
    font-size: clamp(1.2rem, 2vw, 1.65rem);
}

.hero-countdown__header p {
    text-align: end;
    font-size: 0.72rem;
}

.hero-countdown__grid {
    gap: 0.42rem;
}

.hero-countdown__unit {
    padding: 0.5rem 0.38rem;
    border-radius: 8px;
}

.hero-countdown__unit strong {
    font-size: clamp(1.45rem, 3vw, 2.15rem);
}

.hero-countdown__unit span {
    font-size: 0.58rem;
}

.hero-countdown__status {
    font-size: 0.72rem;
}

.hero__spotlight {
    display: grid;
    grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
    gap: 0.62rem;
}

.hero-feature {
    min-height: 100%;
    padding: 0.78rem;
    border-radius: 10px;
    background: rgba(0, 0, 0, 0.28);
}

.hero-feature--primary {
    background:
        linear-gradient(160deg, rgba(175, 23, 49, 0.28), rgba(0, 0, 0, 0.32)),
        rgba(0, 0, 0, 0.22);
}

.hero-feature__label {
    margin-bottom: 0.42rem;
    font-size: 0.62rem;
}

.hero-feature__title {
    font-size: clamp(1rem, 1.35vw, 1.28rem);
}

.hero-feature__meta,
.hero-feature__row,
.hero-feature__empty {
    font-size: 0.76rem;
}

.hero-feature__rows {
    gap: 0.32rem;
    margin-top: 0.48rem;
}

.hero-feature__row {
    padding-top: 0.32rem;
}

.hero__board .key-list {
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.42rem;
}

.hero__board .key-list > div {
    padding: 0.52rem;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.065);
}

.hero__board .key-list strong {
    font-size: 1.08rem;
}

.hero__board .key-list span {
    font-size: 0.66rem;
    line-height: 1.25;
}

.portal-access {
    display: grid;
    grid-template-columns: minmax(180px, 0.26fr) minmax(0, 1fr);
    gap: 0.7rem;
    align-items: stretch;
    margin-top: 0.7rem;
}

.portal-access__header {
    display: grid;
    align-content: center;
    gap: 0.18rem;
    min-height: 100%;
    padding: 0.8rem;
    border-radius: 0;
    background: #050914;
    color: #fff;
}

.portal-access__header span {
    color: rgba(255, 218, 139, 0.78);
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.portal-access__header strong {
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1.05rem;
    line-height: 1.08;
}

.portal-access__links {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.45rem;
}

.portal-access .quick-link-card {
    gap: 0.34rem;
    padding: 0.72rem;
    border-radius: 0;
    border-color: rgba(10, 29, 51, 0.08);
    box-shadow: none;
}

.portal-access .quick-link-card strong {
    font-size: 0.98rem;
}

.portal-access .quick-link-card p {
    font-size: 0.78rem;
    line-height: 1.35;
}

.public-insights-strip {
    display: grid;
    grid-template-columns: minmax(170px, 0.22fr) minmax(0, 1fr);
    gap: 0.65rem;
    align-items: stretch;
    margin: clamp(1rem, 2vw, 1.35rem) 0;
    padding: 0.72rem;
    border: 1px solid rgba(255, 255, 255, 0.10);
    background:
        linear-gradient(135deg, rgba(12, 12, 14, 0.96), rgba(82, 8, 22, 0.92)),
        var(--ink);
    color: #fff;
    box-shadow: 0 18px 42px rgba(28, 10, 16, 0.18);
}

.public-insights-strip__header {
    display: grid;
    align-content: center;
    gap: 0.18rem;
    padding: 0.68rem 0.72rem;
    border-inline-end: 1px solid rgba(255, 255, 255, 0.12);
}

.public-insights-strip__header span {
    color: rgba(255, 218, 139, 0.78);
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.public-insights-strip__header strong {
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1.05rem;
}

.public-insights-strip__grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 0.55rem;
}

.public-insight {
    display: grid;
    gap: 0.16rem;
    min-width: 0;
    padding: 0.68rem 0.72rem;
    border: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(255, 255, 255, 0.06);
}

.public-insight strong {
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(1.35rem, 2vw, 1.9rem);
    line-height: 1;
}

.public-insight span {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.74rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.public-insight small {
    color: rgba(255, 255, 255, 0.62);
    font-size: 0.72rem;
    line-height: 1.32;
}

.page-home .page-section {
    margin-top: clamp(1.4rem, 2.25vw, 2.3rem);
}

.page-home .section-shell {
    border-radius: 0;
    padding: clamp(0.9rem, 1.45vw, 1.2rem);
    box-shadow: 0 10px 26px rgba(10, 29, 51, 0.055);
}

.page-home .section-header {
    margin-bottom: 0.78rem;
}

.page-home .section-header h2 {
    font-size: clamp(1.25rem, 1.65vw, 1.75rem);
}

.page-home .card-grid,
.page-home .team-grid,
.page-home .city-grid,
.page-home .listing-grid {
    gap: 0.6rem;
}

.page-home .match-card,
.page-home .news-card,
.page-home .partner-card,
.page-home .list-card,
.page-home .panel {
    border-radius: 0;
}

.page-home .match-card {
    padding: 0.72rem 0.8rem;
}

.page-home .match-card__teams {
    margin-top: 0.62rem;
}

.page-home .score-box {
    min-width: 82px;
}

.page-home .news-card__body,
.page-home .partner-card__body,
.page-home .entity-card__body {
    padding: 0.78rem;
}

.page-home .news-card__title {
    font-size: 1.05rem;
}

.home-map-card {
    border-radius: 0;
    padding: 0.85rem;
}

.site-footer {
    margin-top: 3rem;
}

.site-footer__inner {
    border-radius: 0;
    padding: 1.05rem;
    box-shadow: 0 16px 44px rgba(0, 0, 0, 0.18);
}

@media (max-width: 1180px) {
    .site-header__inner {
        grid-template-columns: auto minmax(0, 1fr);
    }

    .site-nav {
        grid-column: 1 / -1;
        justify-content: flex-start;
        order: 3;
    }

    .site-header__audience-tools {
        justify-self: end;
    }

    .hero--command {
        grid-template-columns: 1fr;
        min-height: 0;
    }

    .hero__main {
        max-width: none;
    }

    .hero__spotlight {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .portal-access {
        grid-template-columns: 1fr;
    }

    .public-insights-strip {
        grid-template-columns: 1fr;
    }

    .public-insights-strip__header {
        border-inline-end: 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    .public-insights-strip__grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 860px) {
    .site-header__top-inner {
        align-items: flex-start;
    }

    .site-header__inner {
        grid-template-columns: 1fr;
    }

    .site-nav {
        order: 0;
    }

    .site-header__audience-tools {
        width: auto;
        justify-self: start;
    }

    .hero--command {
        padding: 1rem;
    }

    .hero h1 {
        font-size: clamp(2.65rem, 13vw, 4.3rem);
    }

    .hero-countdown__header {
        grid-template-columns: 1fr;
    }

    .hero-countdown__header p {
        text-align: start;
    }

    .hero__spotlight,
    .portal-access__links,
    .public-insights-strip__grid {
        grid-template-columns: 1fr;
    }

    .hero__board .key-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .site-header__top-links,
    .site-header__audience-tools {
        gap: 0.16rem;
    }

    .site-nav a {
        padding-inline: 0.48rem;
    }

    .hero__actions .button,
    .hero__actions .button--subtle,
    .site-header__audience-tools .header-link,
    .site-header__audience-tools .button {
        width: auto;
    }

    .hero-countdown__grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .hero-countdown__unit {
        padding-inline: 0.2rem;
    }

    .hero-countdown__unit strong {
        font-size: clamp(1.1rem, 9vw, 1.55rem);
    }

    .hero__board .key-list {
        grid-template-columns: 1fr;
    }
}

/* Final public shell interactions and compactness pass. */
:root {
    --brand: #b0162e;
    --brand-strong: #7f0f23;
    --gold: #d9b35a;
    --morocco-green: #0f704a;
    --compact-shadow: 0 12px 32px rgba(8, 17, 30, 0.08);
}

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.site-header {
    background:
        linear-gradient(90deg, #020306, #070b12 50%, #210511 100%),
        #020306;
    border-bottom: 1px solid rgba(217, 179, 90, 0.2);
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.34);
}

.site-header__top-inner {
    min-height: 1.55rem;
    padding-block: 0.08rem;
}

.site-header__status-pill {
    min-height: 1.06rem;
    padding: 0.04rem 0.36rem;
    border-radius: 4px;
    font-size: 0.53rem;
}

.site-header__status {
    font-size: 0.66rem;
}

.site-header__staff-access {
    gap: 0;
}

.site-header__admin-link {
    min-height: 1.2rem !important;
    padding: 0.04rem 0 !important;
    font-size: 0.56rem;
    letter-spacing: 0.14em;
}

.site-header__inner {
    grid-template-columns: minmax(145px, 0.22fr) minmax(0, 1fr) auto;
    min-height: 2.82rem;
    padding-block: 0.24rem;
}

.brand__mark {
    width: 1.82rem;
    height: 1.82rem;
    border-radius: 0.42rem;
    font-size: 0.72rem;
    background:
        linear-gradient(145deg, #d62a45, var(--brand) 58%, #640917);
}

.brand__meta {
    font-size: 0.82rem;
}

.brand__meta small {
    font-size: 0.48rem;
}

.site-nav--desktop {
    justify-content: center;
    gap: 0;
}

.site-nav--desktop a {
    padding: 0.38rem 0.46rem;
    font-size: 0.7rem;
    letter-spacing: 0.01em;
}

.site-nav--desktop a::after {
    inset-inline: 0.46rem;
    bottom: 0.02rem;
}

.site-header__utility-tools {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.18rem;
}

.shell-icon-button {
    display: inline-grid;
    grid-auto-flow: column;
    align-items: center;
    justify-content: center;
    gap: 0.22rem;
    min-width: 2rem;
    height: 2rem;
    padding: 0 0.46rem;
    border: 1px solid rgba(255, 255, 255, 0.13);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.045);
    color: rgba(255, 255, 255, 0.78);
    cursor: pointer;
    font: inherit;
    font-size: 0.68rem;
    font-weight: 800;
    line-height: 1;
    transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
}

.shell-icon-button:hover,
.shell-icon-button:focus-visible,
.shell-icon-button[aria-expanded="true"],
.shell-icon-button.is-active {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(217, 179, 90, 0.34);
    color: #fff;
    outline: 0;
}

.shell-icon-button:hover {
    transform: translateY(-1px);
}

.shell-icon {
    width: 1rem;
    height: 1rem;
    fill: currentColor;
}

.shell-current-language {
    font-size: 0.58rem;
    letter-spacing: 0.08em;
}

.shell-menu-button {
    display: none;
}

.shell-popovers {
    position: absolute;
    inset-inline: 0;
    top: 100%;
    width: min(calc(100% - 48px), var(--container));
    margin-inline: auto;
    pointer-events: none;
}

.shell-panel {
    position: absolute;
    inset-inline-end: 0;
    top: 0.42rem;
    z-index: 30;
    width: min(100%, 25rem);
    padding: 0.72rem;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    background:
        linear-gradient(145deg, rgba(7, 11, 18, 0.98), rgba(18, 7, 13, 0.98)),
        #070b12;
    color: #fff;
    box-shadow: 0 22px 54px rgba(0, 0, 0, 0.34);
    pointer-events: auto;
}

.shell-panel__label {
    display: block;
    margin-bottom: 0.46rem;
    color: rgba(255, 224, 153, 0.84);
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.shell-panel--language,
.shell-panel--account {
    width: min(100%, 17.5rem);
}

.shell-search-form {
    display: grid;
    gap: 0.48rem;
}

.shell-search-form label {
    color: rgba(255, 224, 153, 0.86);
    font-size: 0.64rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.shell-search-form__row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 0.42rem;
}

.shell-search-form input {
    width: 100%;
    min-height: 2.3rem;
    padding: 0.52rem 0.68rem;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
    font: inherit;
    font-size: 0.84rem;
}

.shell-search-form input::placeholder {
    color: rgba(255, 255, 255, 0.48);
}

.shell-search-form .button {
    min-height: 2.3rem;
    padding: 0.46rem 0.78rem;
    border-radius: 8px;
}

.shell-language-list,
.shell-drawer__nav,
.shell-drawer__actions {
    display: grid;
    gap: 0.3rem;
}

.shell-language-list__item,
.shell-menu-link,
.shell-drawer__nav a,
.shell-drawer__actions a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.65rem;
    min-height: 2.1rem;
    padding: 0.42rem 0.52rem;
    border: 1px solid rgba(255, 255, 255, 0.09);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.045);
    color: rgba(255, 255, 255, 0.84);
    font-size: 0.8rem;
    font-weight: 750;
}

.shell-language-list__item span {
    color: rgba(255, 224, 153, 0.84);
    font-size: 0.66rem;
    letter-spacing: 0.08em;
}

.shell-language-list__item strong {
    font-size: 0.8rem;
}

.shell-language-list__item:hover,
.shell-language-list__item.is-active,
.shell-menu-link:hover,
.shell-menu-link--primary,
.shell-drawer__nav a:hover,
.shell-drawer__nav a.is-active,
.shell-drawer__actions a:hover {
    border-color: rgba(217, 179, 90, 0.28);
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
}

.shell-menu-form {
    margin: 0;
}

.shell-menu-link--button {
    width: 100%;
    border: 1px solid rgba(255, 255, 255, 0.09);
    text-align: start;
    cursor: pointer;
    font: inherit;
}

.shell-menu-link--primary {
    background: linear-gradient(135deg, var(--brand), var(--brand-strong));
}

.shell-drawer__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 0.55rem;
    color: rgba(255, 224, 153, 0.86);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.shell-drawer__close {
    border: 0;
    background: transparent;
    color: rgba(255, 255, 255, 0.72);
    cursor: pointer;
    font: inherit;
    font-size: 0.64rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.shell-drawer__actions {
    margin-top: 0.52rem;
    padding-top: 0.52rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

main {
    padding: 0.68rem 0 3.2rem;
}

.page-container,
.site-header__top-inner,
.site-header__inner,
.site-footer__inner,
.shell-popovers {
    width: min(calc(100% - 42px), var(--container));
}

.page-section {
    margin-top: clamp(1.35rem, 2.2vw, 2.35rem);
}

.page-header {
    border-radius: 18px;
}

.page-header__content {
    padding: clamp(1.2rem, 2.25vw, 1.85rem);
}

.page-header h1 {
    margin-top: 0.42rem;
    font-size: clamp(1.85rem, 3.2vw, 2.85rem);
}

.page-header p {
    margin-top: 0.44rem;
    font-size: clamp(0.9rem, 1vw, 1rem);
}

.section-shell,
.panel,
.detail-shell,
.match-shell,
.search-shell,
.map-shell,
.table-shell {
    border-radius: 16px;
    box-shadow: var(--compact-shadow);
}

.section-shell,
.panel,
.detail-shell,
.match-shell,
.search-shell,
.map-shell {
    padding: clamp(0.9rem, 1.45vw, 1.25rem);
}

.section-header {
    margin-bottom: 0.85rem;
}

.section-header h2,
.section-header h3,
.panel h2,
.panel h3,
.detail-shell h2,
.detail-shell h3 {
    font-size: clamp(1.18rem, 1.65vw, 1.65rem);
}

.section-header p,
.panel p,
.detail-copy {
    font-size: 0.9rem;
}

.card-grid,
.team-grid,
.city-grid,
.listing-grid,
.quick-links-grid,
.search-groups,
.round-grid {
    gap: 0.72rem;
}

.news-card,
.match-card,
.partner-card,
.list-card,
.map-location-card,
.search-group,
.table-shell {
    border-radius: 14px;
}

.match-card {
    padding: 0.82rem 0.88rem;
}

.match-card__head,
.match-card__competition,
.match-card__meta {
    gap: 0.42rem;
}

.match-card__date,
.match-card__meta,
.news-card__footer,
.result-card__meta {
    font-size: 0.76rem;
}

.match-card__teams {
    gap: 0.62rem;
    margin-top: 0.68rem;
}

.match-card__team strong {
    font-size: 0.95rem;
}

.score-box {
    min-width: 82px;
    padding: 0.42rem 0.5rem;
    border-radius: 10px;
}

.score-box strong {
    font-size: 1.15rem;
}

.news-card__body,
.partner-card__body,
.entity-card__body,
.list-card,
.map-detail-card,
.search-group {
    padding: 0.86rem;
}

.news-card__media {
    min-height: 8.8rem;
}

.news-card__title {
    font-size: 1.06rem;
}

.news-card__summary {
    margin-top: 0.36rem;
    font-size: 0.86rem;
}

.standings-table th,
.standings-table td {
    padding: 0.58rem 0.55rem;
    font-size: 0.82rem;
}

.search-form {
    gap: 0.58rem;
}

.search-form__field input {
    min-height: 2.55rem;
    padding: 0.58rem 0.72rem;
}

.map-layout {
    gap: 0.85rem;
}

.map-canvas {
    min-height: 31rem;
    border-radius: 14px;
}

.map-overview {
    gap: 0.58rem;
}

.map-overview__card,
.map-detail-card__stat {
    padding: 0.68rem;
    border-radius: 12px;
}

.map-location-card {
    padding: 0.72rem;
}

.home-map-card {
    gap: 0.75rem;
    padding: 0.78rem;
    border-radius: 14px;
}

.home-map-card__visual {
    min-height: 12.5rem;
    border-radius: 12px;
}

.home-map-card__location {
    padding: 0.58rem;
    border-radius: 10px;
}

.site-footer {
    margin-top: 2.6rem;
}

.site-footer__inner {
    grid-template-columns: minmax(230px, 1.2fr) repeat(2, minmax(140px, 0.72fr)) minmax(210px, 1fr);
    gap: 1rem;
    padding: 0.95rem;
    border-radius: 16px;
}

.site-footer__mark {
    width: 2.05rem;
    height: 2.05rem;
    border-radius: 0.5rem;
    font-size: 0.72rem;
}

.site-footer__brand p,
.site-footer__note p {
    font-size: 0.82rem;
}

.site-footer__links {
    gap: 0.34rem;
}

.site-footer__links a,
.footer-utility-link {
    font-size: 0.8rem;
}

@media (max-width: 1180px) {
    .site-header__inner {
        grid-template-columns: minmax(140px, auto) minmax(0, 1fr) auto;
    }

    .site-nav--desktop {
        grid-column: auto;
        order: 0;
        justify-content: center;
    }
}

@media (max-width: 1040px) {
    .site-header__inner {
        grid-template-columns: minmax(140px, 1fr) auto;
    }

    .site-nav--desktop {
        display: none;
    }

    .shell-menu-button {
        display: inline-grid;
    }

    .site-header__utility-tools {
        justify-self: end;
    }
}

@media (max-width: 760px) {
    .site-header__top-inner {
        flex-direction: row;
        align-items: center;
    }

    .site-header__status {
        display: none;
    }

    .page-container,
    .site-header__top-inner,
    .site-header__inner,
    .site-footer__inner,
    .shell-popovers {
        width: min(calc(100% - 28px), var(--container));
    }

    .shell-popovers {
        position: fixed;
        top: 4.85rem;
        width: min(calc(100% - 28px), var(--container));
    }

    .shell-panel {
        width: 100%;
        max-height: calc(100vh - 6rem);
        overflow-y: auto;
    }

    .shell-search-form__row {
        grid-template-columns: 1fr;
    }

    .shell-search-form .button {
        width: 100%;
    }

    .shell-panel--drawer {
        inset-inline: 0;
    }

    .page-header__content,
    .section-shell,
    .panel,
    .detail-shell,
    .match-shell,
    .search-shell,
    .map-shell {
        padding: 0.9rem;
    }

    .map-canvas {
        min-height: 24rem;
    }

    .site-footer__inner {
        gap: 0.82rem;
    }
}

@media (max-width: 520px) {
    .brand__meta small {
        display: none;
    }

    .site-header__utility-tools {
        gap: 0.12rem;
    }

    .shell-icon-button {
        min-width: 1.88rem;
        height: 1.88rem;
        padding-inline: 0.38rem;
    }

    .shell-current-language {
        display: none;
    }

    main {
        padding-top: 0.5rem;
    }
}

/* Public shell layout bugfix: remove top-row footprint, stabilize 100% zoom width, and right-align menus. */
.site-header__top {
    display: none;
}

.site-header__inner,
.shell-popovers {
    width: min(calc(100% - 32px), 1360px);
}

.site-header__inner {
    grid-template-columns: auto minmax(0, 1fr) auto;
    min-height: 3rem;
    gap: clamp(0.5rem, 1.15vw, 1rem);
    overflow: visible;
}

.brand {
    min-width: max-content;
}

.site-nav--desktop {
    min-width: 0;
    overflow: hidden;
}

.site-nav--desktop a {
    padding-inline: clamp(0.34rem, 0.55vw, 0.48rem);
    font-size: clamp(0.66rem, 0.72vw, 0.72rem);
}

.site-header__utility-tools {
    flex: 0 0 auto;
    min-width: max-content;
}

.shell-icon-button--staff {
    opacity: 0.58;
}

.shell-icon-button--staff:hover,
.shell-icon-button--staff:focus-visible {
    opacity: 1;
}

.shell-popovers {
    inset-inline: auto;
    left: 50%;
    right: auto;
    transform: translateX(-50%);
    margin-inline: 0;
}

.shell-panel {
    inset-inline-start: auto;
    inset-inline-end: 0;
    max-height: calc(100vh - 4.25rem);
    overflow-y: auto;
}

.shell-panel--drawer {
    width: min(100%, 23rem);
}

[dir="rtl"] .shell-panel {
    inset-inline-start: 0;
    inset-inline-end: auto;
}

@media (max-width: 1240px) {
    .site-header__inner {
        grid-template-columns: minmax(130px, 1fr) auto;
    }

    .site-nav--desktop {
        display: none;
    }

    .shell-menu-button {
        display: inline-grid;
    }

    .site-header__utility-tools {
        justify-self: end;
    }
}

@media (max-width: 760px) {
    .shell-popovers {
        position: fixed;
        top: 3.45rem;
        width: min(calc(100% - 28px), var(--container));
    }

    .shell-panel {
        max-height: calc(100vh - 4.4rem);
    }

    .shell-panel--drawer {
        inset-inline: 0;
        width: 100%;
    }
}

/* Public inner pages compact polish: archives, details, sports surfaces, search, and map. */
.page-inner main {
    padding-top: 0.58rem;
    padding-bottom: 2.65rem;
}

.page-inner .page-container {
    width: min(calc(100% - 42px), 1210px);
}

.page-inner .page-header {
    border-radius: 16px;
    background:
        linear-gradient(110deg, rgba(3, 6, 11, 0.98), rgba(14, 27, 45, 0.96) 54%, rgba(119, 16, 36, 0.9)),
        #050914;
    box-shadow: 0 18px 48px rgba(5, 12, 22, 0.16);
}

.page-inner .page-header__content {
    display: grid;
    gap: 0.38rem;
    max-width: 58rem;
    padding: clamp(1rem, 1.85vw, 1.55rem);
}

.page-inner .page-header__eyebrow,
.page-inner .section-header__eyebrow {
    font-size: 0.62rem;
    letter-spacing: 0.14em;
}

.page-inner .page-header h1 {
    margin-top: 0;
    max-width: 18ch;
    font-size: clamp(1.72rem, 3vw, 2.65rem);
    line-height: 1;
}

.page-inner .page-header p {
    max-width: 48rem;
    margin-top: 0.05rem;
    font-size: 0.9rem;
    line-height: 1.45;
}

.page-inner .page-header__actions {
    gap: 0.38rem;
    margin-top: 0.45rem;
}

.page-inner .page-section {
    margin-top: clamp(1rem, 1.7vw, 1.75rem);
}

.page-inner .section-shell,
.page-inner .panel,
.page-inner .detail-shell,
.page-inner .match-shell,
.page-inner .search-shell,
.page-inner .map-shell {
    padding: clamp(0.72rem, 1.12vw, 1rem);
    border-radius: 14px;
    border-color: rgba(10, 29, 51, 0.08);
    box-shadow: 0 9px 24px rgba(10, 29, 51, 0.055);
}

.page-inner .section-shell::before {
    opacity: 0.45;
}

.page-inner .section-header,
.page-inner .section-header[style] {
    align-items: center;
    gap: 0.72rem;
    margin-bottom: 0.58rem !important;
}

.page-inner .section-header h2,
.page-inner .section-header h3,
.page-inner .panel h2,
.page-inner .panel h3,
.page-inner .detail-shell h2,
.page-inner .detail-shell h3 {
    font-size: clamp(1.05rem, 1.35vw, 1.42rem) !important;
    line-height: 1.12;
}

.page-inner .section-header p,
.page-inner .panel p,
.page-inner .detail-copy,
.page-inner .map-detail-card__summary,
.page-inner .result-card p {
    font-size: 0.84rem;
    line-height: 1.48;
}

.page-inner .section-stack {
    gap: 0.62rem;
}

.page-inner .card-grid,
.page-inner .listing-grid,
.page-inner .team-grid,
.page-inner .city-grid,
.page-inner .round-grid,
.page-inner .search-groups,
.page-inner .quick-links-grid {
    gap: 0.62rem;
}

.page-inner .listing-grid {
    grid-template-columns: repeat(auto-fit, minmax(215px, 1fr));
}

.page-inner .card-grid {
    grid-template-columns: repeat(auto-fit, minmax(245px, 1fr));
}

.page-inner .team-grid,
.page-inner .round-grid,
.page-inner .search-groups {
    grid-template-columns: repeat(auto-fit, minmax(285px, 1fr));
}

.page-inner .news-card,
.page-inner .match-card,
.page-inner .entity-card,
.page-inner .partner-card,
.page-inner .list-card,
.page-inner .result-card,
.page-inner .table-shell,
.page-inner .map-location-card,
.page-inner .map-overview__card,
.page-inner .map-detail-card__stat,
.page-inner .quick-link-card {
    border-radius: 12px;
    box-shadow: none;
}

.page-inner .news-card {
    min-height: 0;
}

.page-inner .news-card__media,
.page-inner .entity-card__media {
    min-height: 0;
    aspect-ratio: 16 / 10;
}

.page-inner .news-card__body,
.page-inner .entity-card__body,
.page-inner .partner-card__body {
    padding: 0.72rem;
}

.page-inner .news-card__title {
    font-size: 1rem;
    line-height: 1.18;
}

.page-inner .news-card__summary {
    margin-top: 0.3rem;
    font-size: 0.8rem;
    line-height: 1.42;
}

.page-inner .news-card__footer {
    margin-top: 0.58rem;
}

.page-inner .badge-row,
.page-inner .match-card__competition,
.page-inner .match-card__meta,
.page-inner .result-card__meta {
    gap: 0.28rem;
}

.page-inner .badge,
.page-inner .meta-pill,
.page-inner .status-pill,
.page-inner .section-link,
.page-inner .match-card__cta,
.page-inner .map-focus-link {
    min-height: 1.45rem;
    padding: 0.22rem 0.42rem;
    border-radius: 999px;
    font-size: 0.68rem;
}

.page-inner .match-feed {
    gap: 0.58rem;
}

.page-inner .match-card {
    padding: 0.66rem 0.72rem;
}

.page-inner .match-card__head {
    gap: 0.45rem;
}

.page-inner .match-card__date,
.page-inner .match-card__meta {
    font-size: 0.72rem;
}

.page-inner .match-card__teams {
    gap: 0.48rem;
    margin-top: 0.48rem;
}

.page-inner .match-card__team {
    gap: 0.12rem;
}

.page-inner .match-card__label {
    font-size: 0.58rem;
    letter-spacing: 0.1em;
}

.page-inner .match-card__team strong {
    font-size: 0.9rem;
    line-height: 1.12;
}

.page-inner .score-box {
    min-width: 74px;
    padding: 0.35rem 0.44rem;
    border-radius: 9px;
    font-size: 0.82rem;
}

.page-inner .score-box strong {
    font-size: 1.02rem;
}

.page-inner .summary-grid {
    gap: 0.52rem;
}

.page-inner .summary-grid .panel,
.page-inner .match-shell .summary-grid .panel {
    padding: 0.64rem 0.72rem;
}

.page-inner .match-shell .match-card__teams {
    margin-top: 0.82rem !important;
}

.page-inner .detail-grid {
    grid-template-columns: minmax(0, 1fr) minmax(250px, 0.34fr);
    gap: 0.82rem;
}

.page-inner .detail-grid > aside {
    top: 4.4rem;
}

.page-inner .detail-media {
    max-height: 360px;
    border-radius: 14px;
}

.page-inner .detail-meta,
.page-inner .detail-meta[style] {
    margin-bottom: 0.62rem !important;
}

.page-inner .article-summary-box {
    padding: 0.72rem 0.82rem;
    border-radius: 12px;
    font-size: 0.92rem;
    line-height: 1.48;
}

.page-inner .detail-copy {
    margin-top: 0.72rem !important;
}

.page-inner .detail-copy p {
    margin-block: 0.55rem;
}

.page-inner .meta-list,
.page-inner .meta-list[style] {
    display: grid;
    gap: 0.18rem;
    margin-top: 0.62rem !important;
    padding-top: 0.62rem;
    border-top: 1px solid var(--border);
}

.page-inner .meta-list:first-of-type {
    padding-top: 0;
    border-top: 0;
}

.page-inner .meta-list strong {
    font-size: 0.68rem;
    letter-spacing: 0.1em;
}

.page-inner .meta-list div,
.page-inner .meta-list a {
    font-size: 0.86rem;
}

.page-inner .timeline {
    gap: 0.52rem;
}

.page-inner .timeline::before {
    inset-inline-start: 1.32rem;
}

.page-inner .timeline-item {
    grid-template-columns: 2.7rem minmax(0, 1fr);
    gap: 0.58rem;
}

.page-inner .timeline-minute {
    width: 2.65rem;
    height: 2.65rem;
    font-size: 0.78rem;
}

.page-inner .lineup-columns {
    gap: 0.72rem;
}

.page-inner .lineup-list {
    gap: 0.35rem;
}

.page-inner .lineup-list li {
    padding: 0.5rem 0.55rem;
    border-radius: 10px;
    font-size: 0.84rem;
}

.page-inner .standings-table {
    overflow-x: auto;
}

.page-inner .standings-table th,
.page-inner .standings-table td {
    padding: 0.46rem 0.48rem;
    font-size: 0.76rem;
}

.page-inner .rank-pill {
    min-width: 1.55rem;
    min-height: 1.55rem;
    font-size: 0.7rem;
}

.page-inner .standings-table__team a {
    font-size: 0.82rem;
}

.page-inner .round-panel {
    padding: 0.72rem;
}

.page-inner .round-panel__count {
    padding: 0.22rem 0.46rem;
    font-size: 0.68rem;
}

.page-inner .entity-card__body h3,
.page-inner .partner-card__body h3 {
    font-size: 1rem;
    line-height: 1.15;
}

.page-inner .entity-card__body p,
.page-inner .partner-card__body p,
.page-inner .list-card {
    font-size: 0.82rem;
    line-height: 1.42;
}

.page-inner .list-card,
.page-inner .list-card[style] {
    padding: 0.68rem !important;
}

.page-inner .placeholder-badge {
    width: 3rem;
    height: 3rem;
    font-size: 0.9rem;
}

.page-inner .empty-state {
    grid-template-columns: auto minmax(0, 1fr);
    gap: 0.62rem;
    padding: 0.82rem;
    border-radius: 12px;
}

.page-inner .empty-state__mark {
    width: 2.45rem;
    height: 2.45rem;
    border-radius: 0.55rem;
    font-size: 0.7rem;
}

.page-inner .empty-state strong {
    font-size: 0.92rem;
}

.page-inner .empty-state p {
    margin-top: 0.2rem;
    font-size: 0.82rem;
}

.page-inner .pagination-shell {
    margin-top: 0.82rem;
    gap: 0.28rem;
}

.page-inner .pagination-shell a,
.page-inner .pagination-shell span {
    min-width: 2rem;
    min-height: 2rem;
    padding: 0.34rem 0.54rem;
    font-size: 0.78rem;
}

.page-inner .search-form {
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 0.48rem;
}

.page-inner .search-form__field label {
    font-size: 0.68rem;
}

.page-inner .search-form__field input {
    min-height: 2.35rem;
    padding: 0.5rem 0.65rem;
    font-size: 0.88rem;
}

.page-inner .search-summary {
    gap: 0.35rem;
    margin-top: 0.7rem;
}

.page-inner .result-list {
    gap: 0.45rem;
}

.page-inner .result-card {
    padding: 0.68rem;
}

.page-inner .result-card strong {
    font-size: 0.95rem;
}

.page-inner .result-card p {
    margin-top: 0.28rem;
}

.page-inner .map-layout {
    grid-template-columns: minmax(0, 1fr) minmax(300px, 0.36fr);
    gap: 0.72rem;
    align-items: start;
}

.page-inner .map-shell__intro {
    margin-bottom: 0.62rem;
}

.page-inner .map-shell__intro h2 {
    font-size: clamp(1.15rem, 1.55vw, 1.55rem);
}

.page-inner .map-shell__intro p,
.page-inner .map-shell__note {
    font-size: 0.82rem;
}

.page-inner .map-overview {
    gap: 0.45rem;
    margin-bottom: 0.62rem;
}

.page-inner .map-overview__card {
    padding: 0.58rem;
}

.page-inner .map-overview__card strong {
    font-size: 1.18rem;
}

.page-inner .map-overview__card span {
    font-size: 0.68rem;
}

.page-inner .map-canvas {
    min-height: clamp(24rem, 45vw, 33rem);
}

.page-inner .map-lists {
    gap: 0.62rem;
}

.page-inner .map-detail-card__eyebrow,
.page-inner .map-detail-card__stats,
.page-inner .map-detail-card__actions {
    margin-top: 0.56rem;
}

.page-inner .map-detail-card h2 {
    margin-top: 0.28rem;
    font-size: 1.25rem;
}

.page-inner .map-detail-card__stat {
    padding: 0.55rem;
}

.page-inner .map-location-card {
    padding: 0.6rem;
}

.page-inner .map-location-card__header {
    gap: 0.42rem;
}

.page-inner .map-location-card__title {
    font-size: 0.92rem;
}

.page-inner .map-location-card__meta {
    margin-top: 0.22rem;
    font-size: 0.78rem;
}

.page-inner .footer-utility-link {
    padding: 0.3rem 0.55rem;
}

@media (max-width: 1080px) {
    .page-inner .detail-grid,
    .page-inner .map-layout {
        grid-template-columns: 1fr;
    }

    .page-inner .detail-grid > aside {
        position: static;
    }
}

@media (max-width: 760px) {
    .page-inner .page-container {
        width: min(calc(100% - 28px), 1210px);
    }

    .page-inner .page-header__content {
        padding: 0.9rem;
    }

    .page-inner .page-header h1 {
        max-width: none;
    }

    .page-inner .section-header,
    .page-inner .match-card__head,
    .page-inner .match-card__meta {
        align-items: flex-start;
    }

    .page-inner .search-form {
        grid-template-columns: 1fr;
    }

    .page-inner .search-form .button {
        width: 100%;
    }

    .page-inner .card-grid,
    .page-inner .listing-grid,
    .page-inner .team-grid,
    .page-inner .round-grid,
    .page-inner .search-groups {
        grid-template-columns: 1fr;
    }

    .page-inner .match-card__teams,
    .page-inner .match-shell .match-card__teams {
        grid-template-columns: 1fr;
    }

    .page-inner .score-box {
        width: 100%;
    }

    .page-inner .empty-state {
        grid-template-columns: 1fr;
    }

    .page-inner .map-canvas {
        min-height: 23rem;
    }
}

.map-shell__intro--premium {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 0.7rem;
}

.map-command-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.9rem;
    margin: 0.78rem 0 0.7rem;
    padding: 0.62rem;
    border: 1px solid rgba(10, 29, 51, 0.08);
    border-radius: 16px;
    background:
        linear-gradient(135deg, rgba(10, 29, 51, 0.96), rgba(24, 45, 72, 0.92)),
        var(--text);
    color: #fff;
}

.map-command-bar__eyebrow {
    display: block;
    color: rgba(255, 218, 139, 0.84);
    font-size: 0.64rem;
    font-weight: 800;
    letter-spacing: 0.13em;
    text-transform: uppercase;
}

.map-command-bar strong {
    display: block;
    margin-top: 0.12rem;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(1rem, 1.35vw, 1.24rem);
}

.map-filter {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 0.32rem;
    padding: 0.25rem;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.08);
}

.map-filter__button {
    min-height: 2rem;
    padding: 0.38rem 0.72rem;
    border: 0;
    border-radius: 999px;
    background: transparent;
    color: rgba(255, 255, 255, 0.78);
    cursor: pointer;
    font: inherit;
    font-size: 0.75rem;
    font-weight: 800;
}

.map-filter__button:hover,
.map-filter__button.is-active {
    background: #fff;
    color: var(--brand);
}

.map-overview--premium .map-overview__card {
    position: relative;
    overflow: hidden;
}

.map-overview--premium .map-overview__card::after {
    content: "";
    position: absolute;
    inset: auto 0 0;
    height: 2px;
    background: linear-gradient(90deg, var(--brand), var(--gold));
    opacity: 0.75;
}

.map-canvas--interactive {
    isolation: isolate;
    background:
        radial-gradient(circle at 18% 22%, rgba(175, 23, 49, 0.12), transparent 24%),
        radial-gradient(circle at 82% 18%, rgba(212, 173, 82, 0.16), transparent 23%),
        linear-gradient(180deg, rgba(9, 23, 40, 0.08), rgba(9, 23, 40, 0.02)),
        #eef4f0;
}

.map-canvas__hud {
    position: absolute;
    z-index: 410;
    top: 0.75rem;
    inset-inline-start: 0.75rem;
    display: grid;
    gap: 0.08rem;
    padding: 0.5rem 0.62rem;
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 14px;
    background: rgba(10, 29, 51, 0.8);
    color: #fff;
    box-shadow: 0 12px 26px rgba(10, 29, 51, 0.16);
    pointer-events: none;
}

.map-canvas__hud span {
    color: rgba(255, 255, 255, 0.68);
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.map-canvas__hud strong {
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1.25rem;
    line-height: 1;
}

.leaflet-host-marker {
    width: 28px;
    height: 34px;
    transform: translateY(-2px);
}

.leaflet-host-marker__pulse {
    inset: 9px 3px 3px;
    filter: blur(0.5px);
}

.leaflet-host-marker__core {
    inset: 2px 5px 10px;
    border-radius: 999px 999px 999px 0;
    transform: rotate(-45deg);
    border-width: 2px;
}

.leaflet-host-marker__core::after {
    content: "";
    position: absolute;
    inset: 5px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.88);
}

.leaflet-host-marker--stadium .leaflet-host-marker__core {
    border-radius: 6px;
    transform: rotate(45deg);
}

.map-detail-card {
    position: relative;
    overflow: hidden;
}

.map-detail-card__visual {
    display: grid;
    place-items: center;
    width: 3.1rem;
    height: 3.1rem;
    border-radius: 18px;
    color: #fff;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1.45rem;
    font-weight: 800;
    box-shadow: 0 14px 32px rgba(10, 29, 51, 0.14);
}

.map-detail-card__visual--city {
    background: linear-gradient(135deg, var(--brand), #6f1020);
}

.map-detail-card__visual--stadium {
    background: linear-gradient(135deg, var(--gold), #8e6a22);
}

.map-section-panel {
    border-color: rgba(10, 29, 51, 0.08);
}

.map-location-card {
    position: relative;
}

.map-location-card::before {
    content: "";
    position: absolute;
    inset: 0 auto 0 0;
    width: 3px;
    border-radius: 999px;
    background: transparent;
}

.map-location-card[data-map-kind="city"]::before {
    background: rgba(175, 23, 49, 0.76);
}

.map-location-card[data-map-kind="stadium"]::before {
    background: rgba(212, 173, 82, 0.82);
}

.map-location-card__summary {
    margin: -0.35rem 0 0.65rem;
    color: var(--text-muted);
    font-size: 0.8rem;
    line-height: 1.45;
}

.map-location-card.is-active {
    transform: translateY(-1px);
}

.map-missing-card {
    display: grid;
    gap: 0.18rem;
    text-decoration: none;
}

.map-missing-card span {
    color: var(--text-muted);
    font-size: 0.8rem;
}

@media (max-width: 760px) {
    .map-command-bar {
        align-items: stretch;
        flex-direction: column;
    }

    .map-filter {
        width: 100%;
        justify-content: space-between;
        border-radius: 14px;
    }

    .map-filter__button {
        flex: 1 1 auto;
    }

    .map-canvas__hud {
        top: 0.55rem;
        inset-inline-start: 0.55rem;
    }
}

.match-centre-hero {
    position: relative;
    overflow: hidden;
    padding: clamp(1rem, 2vw, 1.45rem);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 22px;
    background:
        radial-gradient(circle at 18% 18%, rgba(175, 23, 49, 0.28), transparent 29%),
        radial-gradient(circle at 82% 20%, rgba(212, 173, 82, 0.22), transparent 26%),
        linear-gradient(135deg, #07111f, #10223b 58%, #08131f);
    color: #fff;
    box-shadow: 0 20px 55px rgba(10, 29, 51, 0.18);
}

.match-centre-hero::after {
    content: "";
    position: absolute;
    inset: 0;
    background:
        linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
        linear-gradient(180deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
    background-size: 44px 44px;
    mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.9), transparent 82%);
    pointer-events: none;
}

.match-centre-hero > * {
    position: relative;
    z-index: 1;
}

.match-centre-hero__topline,
.match-centre-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

.match-centre-hero__topline {
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.09em;
    text-transform: uppercase;
}

.match-status-pill {
    display: inline-flex;
    align-items: center;
    min-height: 1.8rem;
    padding: 0.28rem 0.68rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
}

.match-status-pill--live {
    background: var(--brand);
    box-shadow: 0 0 0 4px rgba(175, 23, 49, 0.18);
}

.match-status-pill--completed {
    background: rgba(255, 255, 255, 0.16);
    color: rgba(255, 255, 255, 0.86);
}

.match-status-pill--scheduled {
    background: rgba(212, 173, 82, 0.2);
    color: rgba(255, 235, 188, 0.96);
}

.match-scoreboard {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
    gap: clamp(0.8rem, 2vw, 1.5rem);
    align-items: center;
    margin-top: 1.1rem;
}

.match-scoreboard__team {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    min-width: 0;
}

.match-scoreboard__team--away {
    justify-content: flex-end;
    text-align: right;
}

.match-scoreboard__crest {
    display: grid;
    flex: 0 0 auto;
    place-items: center;
    width: clamp(3rem, 5vw, 4.2rem);
    height: clamp(3rem, 5vw, 4.2rem);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.92);
    font-weight: 900;
    letter-spacing: 0.06em;
}

.match-scoreboard__team strong {
    display: block;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(1.25rem, 2vw, 2.1rem);
    line-height: 1.05;
}

.match-scoreboard__team span:not(.match-scoreboard__crest) {
    display: block;
    margin-top: 0.32rem;
    color: rgba(255, 255, 255, 0.64);
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.match-scoreboard__score {
    display: grid;
    place-items: center;
    min-width: clamp(7rem, 13vw, 10rem);
    padding: 0.85rem 1rem;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.1);
    text-align: center;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.match-scoreboard__score strong {
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(2.05rem, 5vw, 4rem);
    line-height: 0.95;
}

.match-scoreboard__score span {
    margin-top: 0.42rem;
    color: rgba(255, 255, 255, 0.68);
    font-size: 0.76rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.match-centre-meta {
    margin-top: 1.1rem;
}

.match-centre-meta > div {
    flex: 1 1 13rem;
    padding: 0.7rem 0.82rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.08);
}

.match-centre-meta span {
    display: block;
    color: rgba(255, 255, 255, 0.58);
    font-size: 0.66rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.match-centre-meta strong {
    display: block;
    margin-top: 0.2rem;
    font-size: 0.95rem;
}

.match-centre-notice {
    display: grid;
    gap: 0.2rem;
    margin-top: 0.95rem;
    padding: 0.75rem 0.85rem;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.08);
}

.match-centre-notice span {
    color: rgba(255, 255, 255, 0.68);
}

.match-centre-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.58fr);
    gap: 1rem;
    align-items: start;
}

.match-centre-panel .section-header {
    margin-bottom: 0.85rem;
}

.match-timeline {
    position: relative;
    display: grid;
    gap: 0.58rem;
}

.match-timeline::before {
    content: "";
    position: absolute;
    top: 0.4rem;
    bottom: 0.4rem;
    left: 2.05rem;
    width: 2px;
    border-radius: 999px;
    background: linear-gradient(180deg, rgba(175, 23, 49, 0.28), rgba(212, 173, 82, 0.18));
}

.match-timeline__item {
    position: relative;
    display: grid;
    grid-template-columns: 3.2rem 2rem minmax(0, 1fr);
    gap: 0.62rem;
    align-items: start;
    padding: 0.68rem;
    border: 1px solid rgba(10, 29, 51, 0.08);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.82);
}

.match-timeline__minute {
    display: inline-flex;
    align-items: baseline;
    gap: 0.05rem;
    justify-content: center;
    min-height: 2rem;
    border-radius: 999px;
    background: rgba(10, 29, 51, 0.05);
    color: var(--text);
    font-weight: 800;
}

.match-timeline__minute span {
    color: var(--text-muted);
}

.match-timeline__icon {
    position: relative;
    z-index: 1;
    display: grid;
    place-items: center;
    width: 2rem;
    height: 2rem;
    border-radius: 999px;
    background: var(--text);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 900;
}

.match-timeline__item--goal .match-timeline__icon,
.match-timeline__item--penalty .match-timeline__icon {
    background: var(--brand);
}

.match-timeline__item--yellow_card .match-timeline__icon {
    background: #d4ad52;
    color: #1c1608;
}

.match-timeline__item--red_card .match-timeline__icon {
    background: #9f1239;
}

.match-timeline__item--substitution .match-timeline__icon {
    background: #0f766e;
}

.match-timeline__head {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem 0.55rem;
    justify-content: space-between;
}

.match-timeline__head strong {
    font-size: 0.95rem;
}

.match-timeline__head span,
.match-timeline__body p {
    color: var(--text-muted);
    font-size: 0.82rem;
}

.match-timeline__body p {
    margin: 0.22rem 0 0;
}

.match-timeline__body p span + span {
    margin-inline-start: 0.55rem;
}

.match-stat-comparison {
    display: grid;
    gap: 0.75rem;
}

.match-stat-context {
    padding: 0.72rem;
    border: 1px solid rgba(10, 29, 51, 0.08);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.76);
}

.match-stat-context h3 {
    margin: 0;
    font-size: 0.92rem;
}

.match-stat-context__teams,
.match-stat-row__values {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(8rem, auto) minmax(0, 1fr);
    gap: 0.65rem;
    align-items: center;
}

.match-stat-context__teams {
    margin-top: 0.42rem;
    color: var(--text-muted);
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
}

.match-stat-context__teams span:last-child,
.match-stat-row__values strong:last-child {
    text-align: right;
}

.match-stat-row {
    margin-top: 0.66rem;
}

.match-stat-row__values span {
    color: var(--text-muted);
    font-size: 0.82rem;
    font-weight: 700;
    text-align: center;
}

.match-stat-row__bars {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.25rem;
    margin-top: 0.3rem;
}

.match-stat-row__bars span {
    position: relative;
    height: 0.35rem;
    overflow: hidden;
    border-radius: 999px;
    background: rgba(10, 29, 51, 0.08);
}

.match-stat-row__bars span::after {
    content: "";
    position: absolute;
    inset: 0;
    width: var(--value);
    border-radius: inherit;
    background: var(--brand);
}

.match-stat-row__bars span:last-child::after {
    right: 0;
    left: auto;
    background: var(--gold);
}

.match-lineup-board {
    display: grid;
    gap: 0.72rem;
}

.match-lineup-card {
    padding: 0.72rem;
    border: 1px solid rgba(10, 29, 51, 0.08);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.78);
}

.match-lineup-card h3 {
    margin: 0;
    font-size: 1rem;
}

.match-lineup-group {
    margin-top: 0.72rem;
}

.match-lineup-group__head {
    display: flex;
    justify-content: space-between;
    gap: 0.7rem;
    color: var(--text-muted);
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.09em;
    text-transform: uppercase;
}

.match-lineup-list {
    display: grid;
    gap: 0.38rem;
    margin: 0.5rem 0 0;
    padding: 0;
    list-style: none;
}

.match-lineup-list li {
    display: grid;
    grid-template-columns: 2.45rem minmax(0, 1fr);
    gap: 0.5rem;
    align-items: center;
    padding: 0.48rem;
    border: 1px solid rgba(10, 29, 51, 0.06);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.78);
}

.match-lineup-list__number {
    display: grid;
    place-items: center;
    min-height: 2rem;
    border-radius: 10px;
    background: rgba(10, 29, 51, 0.06);
    font-size: 0.78rem;
    font-weight: 900;
}

.match-lineup-list__player strong,
.match-lineup-list__player small {
    display: block;
}

.match-lineup-list__player strong {
    font-size: 0.88rem;
}

.match-lineup-list__player small {
    margin-top: 0.12rem;
    color: var(--text-muted);
    font-size: 0.72rem;
}

.match-lineup-empty {
    margin-top: 0.45rem;
    padding: 0.52rem;
    border-radius: 12px;
    background: rgba(10, 29, 51, 0.04);
    color: var(--text-muted);
    font-size: 0.8rem;
}

@media (max-width: 1080px) {
    .match-centre-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 760px) {
    .match-scoreboard {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .match-scoreboard__team,
    .match-scoreboard__team--away {
        justify-content: center;
        text-align: center;
    }

    .match-scoreboard__team--away {
        flex-direction: row-reverse;
    }

    .match-centre-meta {
        display: grid;
    }

    .match-timeline::before {
        left: 1.55rem;
    }

    .match-timeline__item {
        grid-template-columns: 2.55rem minmax(0, 1fr);
    }

    .match-timeline__icon {
        display: none;
    }

    .match-stat-context__teams,
    .match-stat-row__values {
        grid-template-columns: minmax(0, 1fr) minmax(6.5rem, auto) minmax(0, 1fr);
    }
}

.knockout-shell {
    overflow: hidden;
    padding: clamp(0.85rem, 1.4vw, 1.15rem);
    background:
        radial-gradient(circle at 12% 0%, rgba(175, 23, 49, 0.11), transparent 24%),
        radial-gradient(circle at 94% 12%, rgba(212, 173, 82, 0.14), transparent 24%),
        linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(242, 246, 251, 0.96));
}

.knockout-command {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding: clamp(0.78rem, 1.35vw, 1.05rem);
    border: 1px solid rgba(212, 173, 82, 0.18);
    border-radius: 18px;
    background:
        linear-gradient(135deg, rgba(3, 7, 13, 0.98), rgba(11, 26, 45, 0.96) 58%, rgba(100, 15, 33, 0.92)),
        #050914;
    color: #fff;
    box-shadow: 0 18px 42px rgba(5, 12, 22, 0.16);
}

.knockout-command h2 {
    margin: 0.16rem 0 0;
    font-size: clamp(1.28rem, 2vw, 1.85rem);
}

.knockout-command p {
    max-width: 45rem;
    margin: 0.18rem 0 0;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.84rem;
    line-height: 1.45;
}

.knockout-command__stats {
    display: inline-grid;
    grid-template-columns: repeat(2, minmax(4.8rem, 1fr));
    gap: 0.45rem;
    flex: 0 0 auto;
}

.knockout-command__stats span {
    display: grid;
    gap: 0.1rem;
    padding: 0.58rem 0.68rem;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-align: center;
    text-transform: uppercase;
}

.knockout-command__stats strong {
    color: #fff;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1.45rem;
    line-height: 1;
}

.knockout-scroll {
    margin-top: 0.88rem;
    overflow-x: auto;
    overflow-y: hidden;
    padding: 0.2rem 0.05rem 0.9rem;
    scrollbar-color: rgba(175, 23, 49, 0.55) rgba(10, 29, 51, 0.08);
}

.knockout-bracket {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(230px, 1fr);
    gap: clamp(0.72rem, 1.35vw, 1rem);
    min-width: 1500px;
    align-items: stretch;
}

.knockout-round {
    position: relative;
    display: grid;
    grid-template-rows: auto minmax(0, 1fr);
    gap: 0.62rem;
    padding: 0.62rem;
    border: 1px solid rgba(10, 29, 51, 0.08);
    border-radius: 18px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.93), rgba(248, 250, 253, 0.86)),
        #fff;
    box-shadow: 0 10px 28px rgba(10, 29, 51, 0.06);
}

.knockout-round:not(.knockout-round--finals)::after {
    content: "";
    position: absolute;
    top: 5.4rem;
    bottom: 1.1rem;
    inset-inline-end: -0.52rem;
    width: 1px;
    background: linear-gradient(180deg, transparent, rgba(175, 23, 49, 0.24), rgba(212, 173, 82, 0.24), transparent);
}

.knockout-round--round-of-32 .knockout-match-card {
    padding: 0.48rem;
}

.knockout-round--round-of-32 .knockout-team-row {
    min-height: 1.72rem;
}

.knockout-round--round-of-32 .knockout-match-card__meta {
    display: none;
}

.knockout-round--semi-final {
    background:
        radial-gradient(circle at 50% 0%, rgba(212, 173, 82, 0.12), transparent 42%),
        linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(249, 246, 239, 0.9));
}

.knockout-round--finals {
    border-color: rgba(212, 173, 82, 0.24);
    background:
        radial-gradient(circle at 50% 0%, rgba(212, 173, 82, 0.18), transparent 34%),
        linear-gradient(180deg, rgba(7, 17, 31, 0.98), rgba(31, 18, 19, 0.96));
    color: #fff;
}

.knockout-round__header {
    display: grid;
    gap: 0.14rem;
    padding: 0.48rem 0.52rem 0.56rem;
    border-bottom: 1px solid rgba(10, 29, 51, 0.08);
}

.knockout-round--finals .knockout-round__header {
    border-bottom-color: rgba(255, 255, 255, 0.1);
}

.knockout-round__header span,
.knockout-round__header small {
    color: var(--text-muted);
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.knockout-round--finals .knockout-round__header span,
.knockout-round--finals .knockout-round__header small {
    color: rgba(255, 218, 139, 0.78);
}

.knockout-round__header h3 {
    margin: 0;
    font-size: 1.1rem;
    line-height: 1.04;
}

.knockout-round__matches,
.knockout-final-stack {
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    gap: 0.52rem;
}

.knockout-final-stack {
    justify-content: center;
    min-height: 100%;
}

.knockout-match-card {
    position: relative;
    display: grid;
    gap: 0.42rem;
    padding: 0.56rem;
    border: 1px solid rgba(10, 29, 51, 0.09);
    border-radius: 14px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(247, 250, 253, 0.92));
    color: var(--text);
    box-shadow: 0 8px 20px rgba(10, 29, 51, 0.05);
}

.knockout-match-card:not(.is-terminal)::after {
    content: "";
    position: absolute;
    top: 50%;
    inset-inline-end: -0.74rem;
    width: 0.74rem;
    height: 1px;
    background: rgba(175, 23, 49, 0.26);
}

.knockout-match-card--final {
    padding: 0.78rem;
    border-color: rgba(212, 173, 82, 0.42);
    background:
        radial-gradient(circle at top right, rgba(212, 173, 82, 0.18), transparent 38%),
        linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 249, 237, 0.94));
    box-shadow: 0 16px 34px rgba(0, 0, 0, 0.18);
}

.knockout-match-card--third {
    border-color: rgba(255, 255, 255, 0.14);
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
}

.knockout-match-card__top,
.knockout-match-card__meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.5rem;
}

.knockout-match-card__code {
    color: var(--text-muted);
    font-size: 0.62rem;
    font-weight: 900;
    letter-spacing: 0.11em;
    text-transform: uppercase;
}

.knockout-match-card--third .knockout-match-card__code {
    color: rgba(255, 255, 255, 0.66);
}

.knockout-match-card__status {
    display: inline-flex;
    align-items: center;
    min-height: 1.28rem;
    padding: 0.14rem 0.38rem;
    border-radius: 999px;
    background: rgba(10, 29, 51, 0.06);
    color: var(--text-muted);
    font-size: 0.58rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.knockout-match-card__status--live {
    background: var(--brand);
    color: #fff;
}

.knockout-match-card__status--completed {
    background: var(--success-soft);
    color: var(--success);
}

.knockout-match-card__status--scheduled {
    background: var(--gold-soft);
    color: var(--warning);
}

.knockout-match-card__body {
    display: grid;
    gap: 0.22rem;
}

.knockout-team-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 0.45rem;
    align-items: center;
    min-height: 2rem;
    padding: 0.36rem 0.42rem;
    border: 1px solid rgba(10, 29, 51, 0.065);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.72);
}

.knockout-team-row.is-pending {
    border-style: dashed;
    background:
        linear-gradient(135deg, rgba(10, 29, 51, 0.035), rgba(212, 173, 82, 0.06)),
        rgba(255, 255, 255, 0.55);
}

.knockout-team-row.is-pending .knockout-team-row__name {
    color: var(--text-muted);
    font-style: italic;
}

.knockout-match-card--third .knockout-team-row {
    border-color: rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.08);
}

.knockout-match-card--third .knockout-team-row.is-pending .knockout-team-row__name,
.knockout-match-card--third .knockout-team-row__name {
    color: rgba(255, 255, 255, 0.82);
}

.knockout-team-row__name {
    overflow: hidden;
    font-size: 0.78rem;
    font-weight: 800;
    line-height: 1.2;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.knockout-team-row__score {
    display: grid;
    place-items: center;
    min-width: 1.7rem;
    min-height: 1.7rem;
    border-radius: 8px;
    background: rgba(10, 29, 51, 0.06);
    font-family: Georgia, "Times New Roman", serif;
    font-size: 0.95rem;
    font-weight: 800;
}

.knockout-match-card__penalties {
    color: var(--text-muted);
    font-size: 0.68rem;
    font-weight: 800;
    text-align: center;
}

.knockout-match-card__meta {
    color: var(--text-muted);
    font-size: 0.68rem;
    font-weight: 700;
}

.knockout-match-card__meta a {
    color: var(--brand);
    font-weight: 900;
}

.knockout-match-card--third .knockout-match-card__meta,
.knockout-match-card--third .knockout-match-card__meta a {
    color: rgba(255, 255, 255, 0.72);
}

.knockout-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 0.42rem;
    margin-top: 0.65rem;
}

.knockout-legend span {
    display: inline-flex;
    align-items: center;
    gap: 0.34rem;
    min-height: 1.7rem;
    padding: 0.25rem 0.54rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.75);
    color: var(--text-muted);
    font-size: 0.7rem;
    font-weight: 800;
}

.knockout-legend__dot {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 999px;
}

.knockout-legend__dot--resolved {
    background: var(--success);
}

.knockout-legend__dot--pending {
    background: var(--gold);
}

.knockout-legend__dot--final {
    background: var(--brand);
}

@media (max-width: 1080px) {
    .knockout-command {
        align-items: stretch;
        flex-direction: column;
    }

    .knockout-command__stats {
        width: min(100%, 18rem);
    }
}

@media (max-width: 760px) {
    .knockout-shell {
        padding: 0.65rem;
    }

    .knockout-command {
        border-radius: 14px;
    }

    .knockout-bracket {
        grid-auto-columns: minmax(212px, 72vw);
        min-width: max-content;
    }

    .knockout-round {
        border-radius: 14px;
    }

    .knockout-round:not(.knockout-round--finals)::after,
    .knockout-match-card:not(.is-terminal)::after {
        display: none;
    }
}

/* Knockout bracket final visual polish: stronger bracket board, connectors, and terminal fixtures. */
.page-inner .knockout-shell {
    padding: clamp(0.82rem, 1.35vw, 1.08rem);
    border-color: rgba(212, 173, 82, 0.2);
    background:
        radial-gradient(circle at 18% 0%, rgba(175, 23, 49, 0.22), transparent 25%),
        radial-gradient(circle at 82% 5%, rgba(212, 173, 82, 0.22), transparent 24%),
        linear-gradient(135deg, rgba(4, 8, 15, 0.98), rgba(10, 25, 43, 0.96) 48%, rgba(72, 14, 28, 0.92)),
        #050914;
    box-shadow: 0 24px 60px rgba(5, 12, 22, 0.24);
}

.page-inner .knockout-command {
    border-color: rgba(255, 255, 255, 0.1);
    background:
        linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.04)),
        rgba(255, 255, 255, 0.04);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.page-inner .knockout-command h2 {
    color: #fff;
}

.page-inner .knockout-scroll {
    margin-top: 0.74rem;
    padding: 0.18rem 0.1rem 0.78rem;
    scrollbar-color: rgba(212, 173, 82, 0.75) rgba(255, 255, 255, 0.08);
}

.page-inner .knockout-bracket {
    grid-auto-columns: minmax(218px, 1fr);
    gap: clamp(0.54rem, 0.92vw, 0.76rem);
    min-width: 1420px;
}

.page-inner .knockout-round {
    overflow: visible;
    gap: 0.46rem;
    padding: 0.46rem;
    border-color: rgba(255, 255, 255, 0.12);
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(238, 244, 251, 0.88)),
        rgba(255, 255, 255, 0.9);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.4),
        0 14px 32px rgba(0, 0, 0, 0.16);
}

.page-inner .knockout-round:not(.knockout-round--finals)::before {
    content: "";
    position: absolute;
    top: 5rem;
    bottom: 0.9rem;
    inset-inline-end: -0.34rem;
    width: 3px;
    border-radius: 999px;
    background:
        linear-gradient(180deg, transparent 0%, rgba(212, 173, 82, 0.85) 18%, rgba(175, 23, 49, 0.82) 50%, rgba(212, 173, 82, 0.85) 82%, transparent 100%);
    box-shadow: 0 0 18px rgba(212, 173, 82, 0.3);
}

.page-inner .knockout-round:not(.knockout-round--finals)::after {
    top: 50%;
    bottom: auto;
    inset-inline-end: -0.76rem;
    width: 0.76rem;
    height: 3px;
    border-radius: 999px;
    background: linear-gradient(90deg, rgba(175, 23, 49, 0.78), rgba(212, 173, 82, 0.88));
    box-shadow: 0 0 16px rgba(212, 173, 82, 0.24);
}

.page-inner .knockout-round--semi-final {
    border-color: rgba(212, 173, 82, 0.22);
    background:
        radial-gradient(circle at 50% 0%, rgba(212, 173, 82, 0.2), transparent 34%),
        linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(252, 247, 235, 0.92));
}

.page-inner .knockout-round--finals {
    min-width: 275px;
    border: 1px solid rgba(212, 173, 82, 0.48);
    background:
        radial-gradient(circle at 50% 0%, rgba(212, 173, 82, 0.36), transparent 31%),
        radial-gradient(circle at 88% 76%, rgba(175, 23, 49, 0.34), transparent 32%),
        linear-gradient(180deg, rgba(8, 16, 29, 0.98), rgba(38, 16, 23, 0.98));
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.12),
        0 22px 50px rgba(0, 0, 0, 0.28);
}

.page-inner .knockout-round__header {
    padding: 0.4rem 0.44rem 0.48rem;
}

.page-inner .knockout-round__header h3 {
    font-size: 1.02rem !important;
}

.page-inner .knockout-round--finals .knockout-round__header h3 {
    color: #fff;
    font-size: 1.26rem !important;
}

.page-inner .knockout-round__matches,
.page-inner .knockout-final-stack {
    gap: 0.42rem;
}

.page-inner .knockout-match-card {
    isolation: isolate;
    gap: 0.34rem;
    padding: 0.48rem;
    border-color: rgba(10, 29, 51, 0.12);
    border-radius: 13px;
    background:
        linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(241, 246, 252, 0.94)),
        #fff;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.72),
        0 8px 20px rgba(10, 29, 51, 0.08);
}

.page-inner .knockout-match-card:not(.is-terminal)::after {
    inset-inline-end: -0.72rem;
    width: 0.72rem;
    height: 3px;
    border-radius: 999px;
    background: linear-gradient(90deg, rgba(175, 23, 49, 0.82), rgba(212, 173, 82, 0.9));
}

.knockout-match-card__node {
    position: absolute;
    z-index: 2;
    top: 50%;
    inset-inline-end: -0.2rem;
    width: 0.48rem;
    height: 0.48rem;
    border: 2px solid rgba(255, 255, 255, 0.86);
    border-radius: 999px;
    background: var(--brand);
    box-shadow: 0 0 0 4px rgba(175, 23, 49, 0.16), 0 0 16px rgba(175, 23, 49, 0.28);
    transform: translateY(-50%);
}

.knockout-match-card.is-terminal .knockout-match-card__node {
    display: none;
}

.page-inner .knockout-match-card__kicker {
    display: inline-flex;
    width: max-content;
    max-width: 100%;
    min-height: 1.34rem;
    align-items: center;
    padding: 0.14rem 0.46rem;
    border-radius: 999px;
    background: rgba(212, 173, 82, 0.18);
    color: #7a5612;
    font-size: 0.58rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.page-inner .knockout-match-card--final {
    gap: 0.5rem;
    padding: 0.86rem;
    border-width: 2px;
    border-color: rgba(212, 173, 82, 0.72);
    border-radius: 18px;
    background:
        radial-gradient(circle at 82% 0%, rgba(212, 173, 82, 0.25), transparent 36%),
        linear-gradient(180deg, rgba(255, 255, 255, 1), rgba(255, 249, 235, 0.97));
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.86),
        0 20px 42px rgba(0, 0, 0, 0.22),
        0 0 0 1px rgba(212, 173, 82, 0.22);
    transform: scale(1.04);
}

.page-inner .knockout-match-card--final::before {
    content: "";
    position: absolute;
    z-index: -1;
    inset: -0.5rem;
    border-radius: 22px;
    background: radial-gradient(circle at 50% 50%, rgba(212, 173, 82, 0.22), transparent 68%);
}

.page-inner .knockout-match-card--final .knockout-match-card__kicker {
    background: linear-gradient(135deg, var(--brand), #d64b62);
    color: #fff;
}

.page-inner .knockout-match-card--final .knockout-team-row {
    min-height: 2.34rem;
    border-color: rgba(212, 173, 82, 0.18);
    background: rgba(255, 255, 255, 0.88);
}

.page-inner .knockout-match-card--final .knockout-team-row__name {
    font-size: 0.92rem;
}

.page-inner .knockout-match-card--final .knockout-team-row__score {
    min-width: 2rem;
    min-height: 2rem;
    background: rgba(10, 29, 51, 0.08);
    font-size: 1.18rem;
}

.page-inner .knockout-match-card--third {
    margin-top: 0.9rem;
    border-color: rgba(255, 255, 255, 0.16);
    background:
        linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.045)),
        rgba(255, 255, 255, 0.06);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.page-inner .knockout-match-card--third .knockout-match-card__kicker {
    background: rgba(255, 255, 255, 0.11);
    color: rgba(255, 218, 139, 0.92);
}

.page-inner .knockout-match-card--third .knockout-match-card__status--scheduled {
    background: rgba(212, 173, 82, 0.18);
    color: rgba(255, 230, 169, 0.94);
}

.page-inner .knockout-team-row {
    min-height: 1.9rem;
    padding: 0.32rem 0.4rem;
    border-radius: 10px;
}

.page-inner .knockout-team-row.is-resolved {
    border-color: rgba(13, 107, 69, 0.16);
    background:
        linear-gradient(135deg, rgba(13, 107, 69, 0.06), rgba(255, 255, 255, 0.86)),
        #fff;
}

.page-inner .knockout-team-row.is-pending {
    border-color: rgba(212, 173, 82, 0.35);
    background:
        repeating-linear-gradient(135deg, rgba(212, 173, 82, 0.08) 0 5px, rgba(255, 255, 255, 0.3) 5px 10px),
        rgba(255, 255, 255, 0.7);
}

.page-inner .knockout-team-row__name {
    font-size: 0.78rem;
}

.page-inner .knockout-team-row__score {
    min-width: 1.62rem;
    min-height: 1.62rem;
}

.page-inner .knockout-match-card__meta {
    align-items: center;
    padding-top: 0.34rem;
    border-top: 1px solid rgba(10, 29, 51, 0.08);
}

.page-inner .knockout-match-card--third .knockout-match-card__meta {
    border-top-color: rgba(255, 255, 255, 0.1);
}

.page-inner .knockout-legend span {
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.74);
}

@media (max-width: 1080px) {
    .page-inner .knockout-bracket {
        min-width: 1360px;
    }

    .page-inner .knockout-match-card--final {
        transform: none;
    }
}

@media (max-width: 760px) {
    .page-inner .knockout-bracket {
        grid-auto-columns: minmax(220px, 76vw);
        min-width: max-content;
    }

    .page-inner .knockout-round:not(.knockout-round--finals)::before,
    .page-inner .knockout-round:not(.knockout-round--finals)::after,
    .page-inner .knockout-match-card:not(.is-terminal)::after,
    .knockout-match-card__node {
        display: none;
    }

    .page-inner .knockout-match-card--third {
        margin-top: 0.38rem;
    }
}

/* Symmetric compact bracket layout: mirrored wings feeding a central final axis. */
.page-inner .knockout-bracket--symmetric {
    display: grid;
    grid-auto-flow: initial;
    grid-template-columns: minmax(560px, 1fr) minmax(235px, 0.42fr) minmax(560px, 1fr);
    gap: clamp(0.42rem, 0.72vw, 0.62rem);
    min-width: 1320px;
    align-items: stretch;
    padding: 0.08rem;
}

.page-inner .knockout-wing {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: clamp(0.38rem, 0.58vw, 0.52rem);
    min-width: 0;
}

.page-inner .knockout-wing--right {
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.page-inner .knockout-bracket--symmetric .knockout-round {
    gap: 0.3rem;
    padding: 0.34rem;
    border-radius: 12px;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.45),
        0 10px 24px rgba(0, 0, 0, 0.13);
}

.page-inner .knockout-bracket--symmetric .knockout-round__header {
    gap: 0.08rem;
    padding: 0.28rem 0.32rem 0.34rem;
}

.page-inner .knockout-bracket--symmetric .knockout-round__header span,
.page-inner .knockout-bracket--symmetric .knockout-round__header small {
    font-size: 0.52rem;
    letter-spacing: 0.1em;
}

.page-inner .knockout-bracket--symmetric .knockout-round__header h3 {
    font-size: 0.86rem !important;
}

.page-inner .knockout-bracket--symmetric .knockout-round__matches,
.page-inner .knockout-bracket--symmetric .knockout-final-stack {
    gap: 0.28rem;
}

.page-inner .knockout-bracket--symmetric .knockout-round:not(.knockout-round--finals)::before {
    top: 3.35rem;
    bottom: 0.52rem;
    width: 2px;
    opacity: 0.95;
}

.page-inner .knockout-wing--left .knockout-round:not(.knockout-round--finals)::before {
    inset-inline-end: -0.26rem;
    inset-inline-start: auto;
}

.page-inner .knockout-wing--right .knockout-round:not(.knockout-round--finals)::before {
    inset-inline-start: -0.26rem;
    inset-inline-end: auto;
}

.page-inner .knockout-bracket--symmetric .knockout-round:not(.knockout-round--finals)::after {
    top: 50%;
    width: 0.56rem;
    height: 2px;
}

.page-inner .knockout-wing--left .knockout-round:not(.knockout-round--finals)::after {
    inset-inline-end: -0.58rem;
    inset-inline-start: auto;
}

.page-inner .knockout-wing--right .knockout-round:not(.knockout-round--finals)::after {
    inset-inline-start: -0.58rem;
    inset-inline-end: auto;
    background: linear-gradient(270deg, rgba(175, 23, 49, 0.78), rgba(212, 173, 82, 0.88));
}

.page-inner .knockout-bracket--symmetric .knockout-match-card {
    gap: 0.2rem;
    padding: 0.34rem;
    border-radius: 10px;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card:not(.is-terminal)::after {
    width: 0.54rem;
    height: 2px;
    inset-inline-end: -0.56rem;
}

.page-inner .knockout-wing--right .knockout-match-card:not(.is-terminal)::after {
    inset-inline-start: -0.56rem;
    inset-inline-end: auto;
    background: linear-gradient(270deg, rgba(175, 23, 49, 0.82), rgba(212, 173, 82, 0.9));
}

.page-inner .knockout-bracket--symmetric .knockout-match-card__node {
    width: 0.38rem;
    height: 0.38rem;
    inset-inline-end: -0.16rem;
    box-shadow: 0 0 0 3px rgba(175, 23, 49, 0.14), 0 0 12px rgba(175, 23, 49, 0.24);
}

.page-inner .knockout-wing--right .knockout-match-card__node {
    inset-inline-start: -0.16rem;
    inset-inline-end: auto;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card__top {
    gap: 0.28rem;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card__code {
    font-size: 0.52rem;
    letter-spacing: 0.09em;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card__status {
    min-height: 1.05rem;
    padding: 0.08rem 0.28rem;
    font-size: 0.48rem;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card__body {
    gap: 0.14rem;
}

.page-inner .knockout-bracket--symmetric .knockout-team-row {
    min-height: 1.5rem;
    gap: 0.28rem;
    padding: 0.2rem 0.3rem;
    border-radius: 8px;
}

.page-inner .knockout-bracket--symmetric .knockout-team-row__name {
    font-size: 0.66rem;
    line-height: 1.08;
}

.page-inner .knockout-bracket--symmetric .knockout-team-row__score {
    min-width: 1.28rem;
    min-height: 1.28rem;
    border-radius: 7px;
    font-size: 0.72rem;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card__meta {
    gap: 0.26rem;
    padding-top: 0.18rem;
    font-size: 0.54rem;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card__meta a {
    font-size: 0.55rem;
}

.page-inner .knockout-bracket--symmetric .knockout-round--round-of-32 .knockout-match-card,
.page-inner .knockout-bracket--symmetric .knockout-round--round-of-16 .knockout-match-card {
    padding: 0.3rem;
}

.page-inner .knockout-bracket--symmetric .knockout-round--round-of-32 .knockout-match-card__meta,
.page-inner .knockout-bracket--symmetric .knockout-round--round-of-16 .knockout-match-card__meta {
    display: none;
}

.page-inner .knockout-final-axis {
    position: relative;
    display: grid;
    grid-template-rows: auto minmax(0, 1fr);
    gap: 0.34rem;
    min-width: 0;
}

.page-inner .knockout-final-axis::before {
    content: "";
    position: absolute;
    z-index: 0;
    top: 50%;
    inset-inline: -0.62rem;
    height: 3px;
    border-radius: 999px;
    background: linear-gradient(90deg, rgba(212, 173, 82, 0.78), rgba(175, 23, 49, 0.92), rgba(212, 173, 82, 0.78));
    box-shadow: 0 0 20px rgba(212, 173, 82, 0.26);
}

.page-inner .knockout-final-axis__cap {
    position: relative;
    z-index: 1;
    display: grid;
    gap: 0.08rem;
    place-items: center;
    min-height: 2.65rem;
    border: 1px solid rgba(212, 173, 82, 0.28);
    border-radius: 14px;
    background:
        linear-gradient(135deg, rgba(212, 173, 82, 0.18), rgba(255, 255, 255, 0.08)),
        rgba(255, 255, 255, 0.06);
    color: #fff;
    text-align: center;
}

.page-inner .knockout-final-axis__cap span {
    color: rgba(255, 218, 139, 0.82);
    font-size: 0.54rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.page-inner .knockout-final-axis__cap strong {
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1rem;
    line-height: 1;
}

.page-inner .knockout-final-axis__line {
    position: absolute;
    z-index: 0;
    top: 3.15rem;
    bottom: 0.65rem;
    left: 50%;
    width: 3px;
    border-radius: 999px;
    background: linear-gradient(180deg, rgba(212, 173, 82, 0.2), rgba(212, 173, 82, 0.85), rgba(175, 23, 49, 0.78), rgba(212, 173, 82, 0.35));
    box-shadow: 0 0 22px rgba(212, 173, 82, 0.26);
    transform: translateX(-50%);
}

.page-inner .knockout-bracket--symmetric .knockout-round--finals {
    z-index: 1;
    gap: 0.34rem;
    min-width: 0;
    padding: 0.42rem;
    border-radius: 15px;
}

.page-inner .knockout-bracket--symmetric .knockout-round--finals .knockout-round__header {
    text-align: center;
}

.page-inner .knockout-bracket--symmetric .knockout-round--finals .knockout-round__header h3 {
    font-size: 1.08rem !important;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card--final {
    gap: 0.32rem;
    padding: 0.58rem;
    border-radius: 15px;
    transform: none;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card--final::before {
    inset: -0.34rem;
    border-radius: 18px;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card__kicker {
    min-height: 1.12rem;
    padding: 0.09rem 0.36rem;
    font-size: 0.47rem;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card--final .knockout-team-row {
    min-height: 1.86rem;
    padding: 0.26rem 0.34rem;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card--final .knockout-team-row__name {
    font-size: 0.78rem;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card--final .knockout-team-row__score {
    min-width: 1.52rem;
    min-height: 1.52rem;
    font-size: 0.92rem;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card--third {
    margin-top: 0.38rem;
    padding: 0.42rem;
    opacity: 0.94;
}

.page-inner .knockout-bracket--symmetric .knockout-match-card--third::before {
    content: "";
    position: absolute;
    top: -0.28rem;
    left: 50%;
    width: 42%;
    height: 1px;
    background: rgba(255, 255, 255, 0.18);
    transform: translateX(-50%);
}

.page-inner .knockout-bracket--symmetric .knockout-match-card--third .knockout-team-row {
    min-height: 1.46rem;
}

.page-inner .knockout-bracket--symmetric .knockout-legend span {
    min-height: 1.5rem;
}

@media (max-width: 1180px) {
    .page-inner .knockout-bracket--symmetric {
        min-width: 1260px;
        grid-template-columns: minmax(520px, 1fr) minmax(220px, 0.42fr) minmax(520px, 1fr);
    }
}

@media (max-width: 760px) {
    .page-inner .knockout-bracket--symmetric {
        grid-template-columns: none;
        grid-auto-flow: column;
        grid-auto-columns: minmax(250px, 78vw);
        min-width: max-content;
    }

    .page-inner .knockout-wing {
        display: contents;
    }

    .page-inner .knockout-final-axis {
        grid-template-rows: auto minmax(0, 1fr);
        min-width: min(280px, 82vw);
    }

    .page-inner .knockout-final-axis::before,
    .page-inner .knockout-final-axis__line,
    .page-inner .knockout-bracket--symmetric .knockout-round:not(.knockout-round--finals)::before,
    .page-inner .knockout-bracket--symmetric .knockout-round:not(.knockout-round--finals)::after,
    .page-inner .knockout-bracket--symmetric .knockout-match-card:not(.is-terminal)::after,
    .page-inner .knockout-bracket--symmetric .knockout-match-card__node {
        display: none;
    }
}

/* Knockout fit-to-viewport compact board: prioritize a single controlled tournament canvas on desktop. */
.page-inner .knockout-page-section {
    margin-top: 0 !important;
}

.page-inner .knockout-page-section + .page-section {
    margin-top: 0;
}

.page-inner .knockout-page-section .knockout-shell {
    display: flex;
    flex-direction: column;
    height: clamp(500px, calc(100svh - 4.75rem), 650px);
    min-height: 0;
    padding: 0.42rem;
    border-radius: 16px;
}

.page-inner .knockout-page-section .knockout-command {
    flex: 0 0 auto;
    gap: 0.58rem;
    min-height: 0;
    padding: 0.42rem 0.5rem;
    border-radius: 12px;
}

.page-inner .knockout-command h1 {
    margin: 0.06rem 0 0;
    color: #fff;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(1.05rem, 1.5vw, 1.38rem);
    line-height: 1;
    letter-spacing: -0.02em;
}

.page-inner .knockout-page-section .knockout-command p {
    max-width: 52rem;
    margin-top: 0.08rem;
    font-size: 0.68rem;
    line-height: 1.28;
}

.page-inner .knockout-page-section .knockout-command__stats {
    grid-template-columns: repeat(2, minmax(3.8rem, 1fr));
    gap: 0.28rem;
}

.page-inner .knockout-page-section .knockout-command__stats span {
    min-height: 2.05rem;
    padding: 0.28rem 0.38rem;
    border-radius: 10px;
    font-size: 0.52rem;
}

.page-inner .knockout-page-section .knockout-command__stats strong {
    font-size: 1.06rem;
}

.page-inner .knockout-page-section .knockout-scroll {
    flex: 1 1 auto;
    min-height: 0;
    margin-top: 0.34rem;
    padding: 0.06rem 0.03rem 0.36rem;
}

.page-inner .knockout-bracket--fit {
    height: 100%;
    min-width: 1180px;
    grid-template-columns: minmax(480px, 1fr) minmax(185px, 0.36fr) minmax(480px, 1fr);
    gap: 0.38rem;
}

.page-inner .knockout-bracket--fit .knockout-wing {
    gap: 0.28rem;
}

.page-inner .knockout-bracket--fit .knockout-round {
    min-height: 0;
    gap: 0.18rem;
    padding: 0.24rem;
    border-radius: 10px;
}

.page-inner .knockout-bracket--fit .knockout-round__header {
    gap: 0.03rem;
    padding: 0.18rem 0.22rem 0.2rem;
}

.page-inner .knockout-bracket--fit .knockout-round__header span,
.page-inner .knockout-bracket--fit .knockout-round__header small {
    font-size: 0.44rem;
    letter-spacing: 0.08em;
}

.page-inner .knockout-bracket--fit .knockout-round__header h3,
.page-inner .knockout-bracket--fit .knockout-round--finals .knockout-round__header h3 {
    font-size: 0.72rem !important;
    line-height: 0.98;
}

.page-inner .knockout-bracket--fit .knockout-round__matches,
.page-inner .knockout-bracket--fit .knockout-final-stack {
    gap: 0.16rem;
}

.page-inner .knockout-bracket--fit .knockout-round:not(.knockout-round--finals)::before {
    top: 2.55rem;
    bottom: 0.38rem;
    width: 2px;
}

.page-inner .knockout-bracket--fit .knockout-round:not(.knockout-round--finals)::after {
    width: 0.42rem;
    height: 2px;
}

.page-inner .knockout-bracket--fit .knockout-match-card {
    gap: 0.12rem;
    padding: 0.22rem;
    border-radius: 8px;
}

.page-inner .knockout-bracket--fit .knockout-match-card:not(.is-terminal)::after {
    width: 0.4rem;
    height: 2px;
    inset-inline-end: -0.42rem;
}

.page-inner .knockout-bracket--fit .knockout-wing--right .knockout-match-card:not(.is-terminal)::after {
    inset-inline-start: -0.42rem;
    inset-inline-end: auto;
}

.page-inner .knockout-bracket--fit .knockout-match-card__node {
    width: 0.32rem;
    height: 0.32rem;
    border-width: 1px;
}

.page-inner .knockout-bracket--fit .knockout-match-card__top {
    gap: 0.18rem;
}

.page-inner .knockout-bracket--fit .knockout-match-card__code {
    font-size: 0.44rem;
    letter-spacing: 0.07em;
}

.page-inner .knockout-bracket--fit .knockout-match-card__status {
    min-height: 0.86rem;
    padding: 0.04rem 0.2rem;
    font-size: 0.4rem;
    letter-spacing: 0.06em;
}

.page-inner .knockout-bracket--fit .knockout-match-card__body {
    gap: 0.08rem;
}

.page-inner .knockout-bracket--fit .knockout-team-row {
    min-height: 1.12rem;
    gap: 0.16rem;
    padding: 0.1rem 0.18rem;
    border-radius: 6px;
}

.page-inner .knockout-bracket--fit .knockout-team-row__name {
    font-size: 0.53rem;
    line-height: 1;
}

.page-inner .knockout-bracket--fit .knockout-team-row__score {
    min-width: 1rem;
    min-height: 1rem;
    border-radius: 5px;
    font-size: 0.58rem;
}

.page-inner .knockout-bracket--fit .knockout-match-card:not(.is-terminal) .knockout-match-card__meta {
    display: none;
}

.page-inner .knockout-bracket--fit .knockout-final-axis {
    gap: 0.2rem;
}

.page-inner .knockout-bracket--fit .knockout-final-axis::before {
    inset-inline: -0.4rem;
    height: 2px;
}

.page-inner .knockout-bracket--fit .knockout-final-axis__cap {
    min-height: 1.95rem;
    border-radius: 10px;
}

.page-inner .knockout-bracket--fit .knockout-final-axis__cap span {
    font-size: 0.43rem;
}

.page-inner .knockout-bracket--fit .knockout-final-axis__cap strong {
    font-size: 0.82rem;
}

.page-inner .knockout-bracket--fit .knockout-final-axis__line {
    top: 2.36rem;
    bottom: 0.36rem;
    width: 2px;
}

.page-inner .knockout-bracket--fit .knockout-round--finals {
    gap: 0.2rem;
    padding: 0.3rem;
    border-radius: 12px;
}

.page-inner .knockout-bracket--fit .knockout-match-card__kicker {
    min-height: 0.86rem;
    padding: 0.04rem 0.28rem;
    font-size: 0.38rem;
    letter-spacing: 0.08em;
}

.page-inner .knockout-bracket--fit .knockout-match-card--final {
    gap: 0.18rem;
    padding: 0.36rem;
    border-radius: 12px;
}

.page-inner .knockout-bracket--fit .knockout-match-card--final .knockout-team-row {
    min-height: 1.36rem;
    padding: 0.14rem 0.22rem;
}

.page-inner .knockout-bracket--fit .knockout-match-card--final .knockout-team-row__name {
    font-size: 0.62rem;
}

.page-inner .knockout-bracket--fit .knockout-match-card--final .knockout-team-row__score {
    min-width: 1.16rem;
    min-height: 1.16rem;
    font-size: 0.68rem;
}

.page-inner .knockout-bracket--fit .knockout-match-card--final .knockout-match-card__meta,
.page-inner .knockout-bracket--fit .knockout-match-card--third .knockout-match-card__meta {
    gap: 0.16rem;
    padding-top: 0.12rem;
    font-size: 0.48rem;
}

.page-inner .knockout-bracket--fit .knockout-match-card--third {
    margin-top: 0.2rem;
    padding: 0.28rem;
}

.page-inner .knockout-bracket--fit .knockout-match-card--third .knockout-team-row {
    min-height: 1.08rem;
}

.page-inner .knockout-page-section .knockout-legend {
    flex: 0 0 auto;
    gap: 0.26rem;
    margin-top: 0.22rem;
}

.page-inner .knockout-page-section .knockout-legend span {
    min-height: 1.16rem;
    padding: 0.12rem 0.34rem;
    font-size: 0.52rem;
}

.page-inner .knockout-page-section .knockout-legend__dot {
    width: 0.36rem;
    height: 0.36rem;
}

@media (max-height: 760px) and (min-width: 761px) {
    .page-inner .knockout-page-section .knockout-shell {
        height: calc(100svh - 4.35rem);
    }

    .page-inner .knockout-page-section .knockout-command p {
        display: none;
    }

    .page-inner .knockout-page-section .knockout-command {
        padding-block: 0.3rem;
    }

    .page-inner .knockout-bracket--fit .knockout-round__header small {
        display: none;
    }

    .page-inner .knockout-bracket--fit .knockout-team-row {
        min-height: 1rem;
    }
}

@media (max-width: 760px) {
    .page-inner .knockout-page-section .knockout-shell {
        height: auto;
        min-height: 0;
    }

    .page-inner .knockout-bracket--fit {
        height: auto;
        grid-auto-columns: minmax(220px, 76vw);
        min-width: max-content;
    }
}

/* Final knockout perfection: cleaner fit-to-screen tournament canvas with quieter, balanced nodes. */
.page-inner .knockout-shell--polished {
    height: clamp(445px, calc(100svh - 4.35rem), 560px);
    padding: 0.3rem;
    border: 1px solid rgba(212, 173, 82, 0.18);
    border-radius: 14px;
    background:
        radial-gradient(circle at 50% 0%, rgba(212, 173, 82, 0.16), transparent 23%),
        linear-gradient(135deg, #050912, #0a192b 52%, #3e0d1a);
    box-shadow: 0 18px 44px rgba(5, 12, 22, 0.22);
}

.page-inner .knockout-shell--polished .knockout-command {
    padding: 0.28rem 0.38rem;
    border-radius: 10px;
    border-color: rgba(255, 255, 255, 0.075);
    background: rgba(255, 255, 255, 0.045);
}

.page-inner .knockout-shell--polished .section-header__eyebrow {
    font-size: 0.5rem;
}

.page-inner .knockout-shell--polished .knockout-command h1 {
    margin-top: 0.02rem;
    font-size: clamp(0.95rem, 1.18vw, 1.18rem);
}

.page-inner .knockout-shell--polished .knockout-command__copy {
    margin-top: 0.02rem;
    color: rgba(255, 255, 255, 0.58);
    font-size: 0.58rem;
    line-height: 1.15;
}

.page-inner .knockout-shell--polished .knockout-command__stats {
    grid-template-columns: repeat(2, minmax(3.1rem, 1fr));
    gap: 0.18rem;
}

.page-inner .knockout-shell--polished .knockout-command__stats span {
    min-height: 1.58rem;
    padding: 0.16rem 0.24rem;
    border-radius: 8px;
    font-size: 0.42rem;
    letter-spacing: 0.07em;
}

.page-inner .knockout-shell--polished .knockout-command__stats strong {
    font-size: 0.82rem;
}

.page-inner .knockout-shell--polished .knockout-scroll {
    margin-top: 0.22rem;
    padding-bottom: 0.24rem;
}

.page-inner .knockout-shell--polished .knockout-bracket--fit {
    min-width: 1240px;
    grid-template-columns: minmax(515px, 1fr) minmax(170px, 0.32fr) minmax(515px, 1fr);
    gap: 0.24rem;
}

.page-inner .knockout-shell--polished .knockout-wing {
    gap: 0.18rem;
}

.page-inner .knockout-shell--polished .knockout-round {
    padding: 0.16rem;
    gap: 0.1rem;
    border-radius: 8px;
    border-color: rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.92);
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
}

.page-inner .knockout-shell--polished .knockout-round__header {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 0.2rem;
    padding: 0.1rem 0.14rem 0.14rem;
}

.page-inner .knockout-shell--polished .knockout-round__header span {
    display: none;
}

.page-inner .knockout-shell--polished .knockout-round__header small {
    font-size: 0.38rem;
    letter-spacing: 0.06em;
    white-space: nowrap;
}

.page-inner .knockout-shell--polished .knockout-round__header h3,
.page-inner .knockout-shell--polished .knockout-round--finals .knockout-round__header h3 {
    font-size: 0.62rem !important;
    letter-spacing: -0.01em;
    white-space: nowrap;
}

.page-inner .knockout-shell--polished .knockout-round__matches,
.page-inner .knockout-shell--polished .knockout-final-stack {
    gap: 0.1rem;
}

.page-inner .knockout-shell--polished .knockout-round:not(.knockout-round--finals)::before {
    top: 2rem;
    bottom: 0.24rem;
    width: 1px;
    opacity: 0.8;
    box-shadow: none;
}

.page-inner .knockout-shell--polished .knockout-round:not(.knockout-round--finals)::after,
.page-inner .knockout-shell--polished .knockout-match-card:not(.is-terminal)::after {
    height: 1px;
    box-shadow: none;
}

.page-inner .knockout-shell--polished .knockout-match-card {
    gap: 0.06rem;
    padding: 0.14rem;
    border-radius: 7px;
    border-color: rgba(10, 29, 51, 0.09);
    background: rgba(255, 255, 255, 0.96);
    box-shadow: none;
}

.page-inner .knockout-shell--polished .knockout-match-card__top {
    min-height: 0.72rem;
}

.page-inner .knockout-shell--polished .knockout-match-card__code {
    font-size: 0.37rem;
    letter-spacing: 0.055em;
}

.page-inner .knockout-shell--polished .knockout-match-card__status {
    min-height: 0.7rem;
    padding: 0.02rem 0.16rem;
    font-size: 0.34rem;
    letter-spacing: 0.045em;
}

.page-inner .knockout-shell--polished .knockout-match-card__body {
    gap: 0.06rem;
}

.page-inner .knockout-shell--polished .knockout-team-row {
    min-height: 0.92rem;
    padding: 0.07rem 0.14rem;
    border-radius: 5px;
    border-color: rgba(10, 29, 51, 0.055);
}

.page-inner .knockout-shell--polished .knockout-team-row__name {
    font-size: 0.47rem;
    font-weight: 800;
    letter-spacing: -0.01em;
}

.page-inner .knockout-shell--polished .knockout-team-row__score {
    min-width: 0.84rem;
    min-height: 0.84rem;
    border-radius: 4px;
    font-size: 0.48rem;
}

.page-inner .knockout-shell--polished .knockout-team-row.is-pending {
    border-color: rgba(212, 173, 82, 0.24);
    background:
        linear-gradient(135deg, rgba(212, 173, 82, 0.08), rgba(255, 255, 255, 0.72)),
        rgba(255, 255, 255, 0.88);
}

.page-inner .knockout-shell--polished .knockout-team-row.is-pending .knockout-team-row__name {
    color: #6b5f45;
}

.page-inner .knockout-shell--polished .knockout-match-card__node {
    width: 0.24rem;
    height: 0.24rem;
    border-width: 1px;
    box-shadow: 0 0 0 2px rgba(175, 23, 49, 0.1);
}

.page-inner .knockout-shell--polished .knockout-final-axis {
    gap: 0.12rem;
}

.page-inner .knockout-shell--polished .knockout-final-axis::before {
    height: 1px;
    opacity: 0.82;
}

.page-inner .knockout-shell--polished .knockout-final-axis__cap {
    min-height: 1.45rem;
    border-radius: 9px;
}

.page-inner .knockout-shell--polished .knockout-final-axis__cap span {
    font-size: 0.36rem;
}

.page-inner .knockout-shell--polished .knockout-final-axis__cap strong {
    font-size: 0.66rem;
}

.page-inner .knockout-shell--polished .knockout-final-axis__line {
    top: 1.76rem;
    bottom: 0.22rem;
    width: 1px;
}

.page-inner .knockout-shell--polished .knockout-round--finals {
    padding: 0.22rem;
    border-radius: 10px;
}

.page-inner .knockout-shell--polished .knockout-match-card__kicker {
    min-height: 0.68rem;
    padding: 0.01rem 0.2rem;
    font-size: 0.32rem;
}

.page-inner .knockout-shell--polished .knockout-match-card--final {
    padding: 0.28rem;
    border-radius: 10px;
    border-width: 1px;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.82),
        0 10px 24px rgba(0, 0, 0, 0.18);
}

.page-inner .knockout-shell--polished .knockout-match-card--final::before {
    inset: -0.18rem;
    border-radius: 12px;
    opacity: 0.7;
}

.page-inner .knockout-shell--polished .knockout-match-card--final .knockout-team-row {
    min-height: 1.12rem;
    padding: 0.1rem 0.16rem;
}

.page-inner .knockout-shell--polished .knockout-match-card--final .knockout-team-row__name {
    font-size: 0.52rem;
}

.page-inner .knockout-shell--polished .knockout-match-card--final .knockout-team-row__score {
    min-width: 0.94rem;
    min-height: 0.94rem;
    font-size: 0.54rem;
}

.page-inner .knockout-shell--polished .knockout-match-card--final .knockout-match-card__meta,
.page-inner .knockout-shell--polished .knockout-match-card--third .knockout-match-card__meta {
    padding-top: 0.08rem;
    font-size: 0.4rem;
}

.page-inner .knockout-shell--polished .knockout-match-card--third {
    margin-top: 0.14rem;
    padding: 0.18rem;
}

.page-inner .knockout-shell--polished .knockout-match-card--third::before {
    top: -0.12rem;
}

.page-inner .knockout-shell--polished .knockout-match-card--third .knockout-team-row {
    min-height: 0.88rem;
}

.page-inner .knockout-shell--polished .knockout-legend {
    justify-content: center;
}

.page-inner .knockout-shell--polished .knockout-legend span {
    min-height: 0.94rem;
    padding: 0.07rem 0.26rem;
    font-size: 0.44rem;
}

.page-inner .knockout-shell--polished .knockout-legend__dot {
    width: 0.28rem;
    height: 0.28rem;
}

@media (min-width: 761px) {
    .page-inner .knockout-shell--polished .knockout-scroll {
        overflow-y: hidden;
    }
}

@media (max-height: 720px) and (min-width: 761px) {
    .page-inner .knockout-shell--polished {
        height: calc(100svh - 4rem);
    }

    .page-inner .knockout-shell--polished .knockout-command__copy,
    .page-inner .knockout-shell--polished .knockout-command__stats span {
        display: none;
    }

    .page-inner .knockout-shell--polished .knockout-command {
        padding-block: 0.2rem;
    }
}

@media (max-width: 760px) {
    .page-inner .knockout-shell--polished {
        height: auto;
        padding: 0.5rem;
    }

    .page-inner .knockout-shell--polished .knockout-command {
        align-items: stretch;
        padding: 0.5rem;
    }

    .page-inner .knockout-shell--polished .knockout-command__copy {
        display: block;
        font-size: 0.66rem;
    }

    .page-inner .knockout-shell--polished .knockout-command__stats span {
        display: grid;
    }
}

/* Strict knockout readability redesign: flatter, cleaner, smaller board built around the centered final. */
.page-inner .knockout-shell--strict {
    height: clamp(390px, calc(100svh - 4.15rem), 535px);
    padding: 0.22rem;
    border-color: rgba(212, 173, 82, 0.16);
    background:
        radial-gradient(circle at 50% -8%, rgba(212, 173, 82, 0.18), transparent 24%),
        linear-gradient(135deg, #03070d 0%, #071827 52%, #2c0711 100%);
    box-shadow: 0 16px 38px rgba(3, 7, 13, 0.24);
}

.page-inner .knockout-shell--strict .knockout-command {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
    min-height: 1.9rem;
    padding: 0.18rem 0.28rem;
    border: 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}

.page-inner .knockout-shell--strict .section-header__eyebrow {
    display: none;
}

.page-inner .knockout-shell--strict .knockout-command h1 {
    margin: 0;
    font-size: clamp(0.9rem, 1.05vw, 1.08rem);
    letter-spacing: -0.015em;
}

.page-inner .knockout-shell--strict .knockout-command__stats {
    grid-template-columns: repeat(2, auto);
    gap: 0.18rem;
}

.page-inner .knockout-shell--strict .knockout-command__stats span {
    display: inline-flex;
    align-items: baseline;
    gap: 0.16rem;
    min-height: 1.24rem;
    padding: 0.08rem 0.28rem;
    border: 1px solid rgba(255, 255, 255, 0.09);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.045);
    color: rgba(255, 255, 255, 0.58);
    font-size: 0.42rem;
}

.page-inner .knockout-shell--strict .knockout-command__stats strong {
    font-family: "Segoe UI Variable", "Trebuchet MS", "Segoe UI", sans-serif;
    font-size: 0.7rem;
    line-height: 1;
}

.page-inner .knockout-shell--strict .knockout-scroll {
    margin-top: 0.18rem;
    padding: 0 0.02rem 0.18rem;
}

.page-inner .knockout-shell--strict .knockout-bracket--fit {
    min-width: 1160px;
    grid-template-columns: minmax(486px, 1fr) minmax(166px, 0.34fr) minmax(486px, 1fr);
    gap: 0.22rem;
    padding: 0;
}

.page-inner .knockout-shell--strict .knockout-wing {
    gap: 0.16rem;
}

.page-inner .knockout-shell--strict .knockout-round {
    padding: 0.1rem;
    gap: 0.06rem;
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 7px;
    background: rgba(255, 255, 255, 0.045);
    box-shadow: none;
}

.page-inner .knockout-shell--strict .knockout-round__header {
    justify-content: center;
    min-height: 0.9rem;
    padding: 0.04rem 0.06rem 0.08rem;
    border-bottom-color: rgba(255, 255, 255, 0.06);
    text-align: center;
}

.page-inner .knockout-shell--strict .knockout-round__header small {
    display: none;
}

.page-inner .knockout-shell--strict .knockout-round__header h3,
.page-inner .knockout-shell--strict .knockout-round--finals .knockout-round__header h3 {
    color: rgba(255, 255, 255, 0.82);
    font-family: "Segoe UI Variable", "Trebuchet MS", "Segoe UI", sans-serif;
    font-size: 0.5rem !important;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.page-inner .knockout-shell--strict .knockout-round__matches,
.page-inner .knockout-shell--strict .knockout-final-stack {
    justify-content: space-evenly;
    gap: 0.06rem;
}

.page-inner .knockout-shell--strict .knockout-round:not(.knockout-round--finals)::before {
    top: 1.3rem;
    bottom: 0.2rem;
    width: 1px;
    background: linear-gradient(180deg, transparent, rgba(212, 173, 82, 0.55), rgba(175, 23, 49, 0.48), rgba(212, 173, 82, 0.55), transparent);
}

.page-inner .knockout-shell--strict .knockout-round:not(.knockout-round--finals)::after,
.page-inner .knockout-shell--strict .knockout-match-card:not(.is-terminal)::after {
    height: 1px;
    background: rgba(212, 173, 82, 0.52);
}

.page-inner .knockout-shell--strict .knockout-match-card {
    gap: 0.04rem;
    padding: 0.1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.94);
    box-shadow: none;
}

.page-inner .knockout-shell--strict .knockout-match-card__top {
    min-height: 0.58rem;
}

.page-inner .knockout-shell--strict .knockout-match-card__code {
    color: rgba(10, 29, 51, 0.52);
    font-size: 0.32rem;
    letter-spacing: 0.05em;
}

.page-inner .knockout-shell--strict .knockout-match-card__status {
    min-height: 0.56rem;
    padding: 0.01rem 0.12rem;
    border-radius: 999px;
    font-size: 0.28rem;
    letter-spacing: 0.04em;
}

.page-inner .knockout-shell--strict .knockout-team-row {
    min-height: 0.78rem;
    padding: 0.04rem 0.1rem;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.76);
}

.page-inner .knockout-shell--strict .knockout-team-row__name {
    color: rgba(10, 29, 51, 0.86);
    font-size: 0.4rem;
    font-weight: 850;
}

.page-inner .knockout-shell--strict .knockout-team-row__score {
    min-width: 0.7rem;
    min-height: 0.7rem;
    border-radius: 3px;
    background: rgba(10, 29, 51, 0.055);
    font-size: 0.4rem;
}

.page-inner .knockout-shell--strict .knockout-team-row.is-pending {
    border-color: rgba(212, 173, 82, 0.18);
    background: rgba(255, 250, 236, 0.72);
}

.page-inner .knockout-shell--strict .knockout-team-row.is-pending .knockout-team-row__name {
    color: rgba(85, 72, 42, 0.86);
    font-style: normal;
}

.page-inner .knockout-shell--strict .knockout-match-card__node {
    width: 0.2rem;
    height: 0.2rem;
    background: var(--gold);
    box-shadow: none;
}

.page-inner .knockout-shell--strict .knockout-final-axis {
    gap: 0.08rem;
}

.page-inner .knockout-shell--strict .knockout-final-axis::before,
.page-inner .knockout-shell--strict .knockout-final-axis__line {
    background: rgba(212, 173, 82, 0.56);
    box-shadow: none;
}

.page-inner .knockout-shell--strict .knockout-final-axis__cap {
    min-height: 1.18rem;
    border-color: rgba(212, 173, 82, 0.18);
    border-radius: 8px;
    background: rgba(212, 173, 82, 0.1);
}

.page-inner .knockout-shell--strict .knockout-final-axis__cap span {
    display: none;
}

.page-inner .knockout-shell--strict .knockout-final-axis__cap strong {
    color: rgba(255, 225, 155, 0.94);
    font-size: 0.62rem;
}

.page-inner .knockout-shell--strict .knockout-final-axis__line {
    top: 1.42rem;
    bottom: 0.18rem;
    width: 1px;
}

.page-inner .knockout-shell--strict .knockout-round--finals {
    border-color: rgba(212, 173, 82, 0.22);
    background: rgba(255, 255, 255, 0.07);
}

.page-inner .knockout-shell--strict .knockout-match-card__kicker {
    min-height: 0.54rem;
    padding: 0 0.14rem;
    border-radius: 999px;
    font-size: 0.26rem;
    letter-spacing: 0.07em;
}

.page-inner .knockout-shell--strict .knockout-match-card--final {
    padding: 0.2rem;
    border-color: rgba(212, 173, 82, 0.5);
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 248, 229, 0.96));
}

.page-inner .knockout-shell--strict .knockout-match-card--final .knockout-match-card__kicker {
    background: var(--brand);
    color: #fff;
}

.page-inner .knockout-shell--strict .knockout-match-card--final .knockout-team-row {
    min-height: 0.92rem;
    border-color: rgba(212, 173, 82, 0.16);
}

.page-inner .knockout-shell--strict .knockout-match-card--final .knockout-team-row__name {
    font-size: 0.45rem;
}

.page-inner .knockout-shell--strict .knockout-match-card--final .knockout-team-row__score {
    min-width: 0.78rem;
    min-height: 0.78rem;
    font-size: 0.45rem;
}

.page-inner .knockout-shell--strict .knockout-match-card--final .knockout-match-card__meta,
.page-inner .knockout-shell--strict .knockout-match-card--third .knockout-match-card__meta {
    padding-top: 0.04rem;
    border-top-color: rgba(10, 29, 51, 0.06);
    font-size: 0.34rem;
}

.page-inner .knockout-shell--strict .knockout-match-card--third {
    margin-top: 0.1rem;
    padding: 0.14rem;
    border-color: rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.075);
}

.page-inner .knockout-shell--strict .knockout-match-card--third .knockout-team-row {
    min-height: 0.72rem;
}

.page-inner .knockout-shell--strict .knockout-match-card--third .knockout-team-row__name {
    color: rgba(255, 255, 255, 0.76);
}

.page-inner .knockout-shell--strict .knockout-legend {
    margin-top: 0.12rem;
}

.page-inner .knockout-shell--strict .knockout-legend span {
    min-height: 0.75rem;
    padding: 0.03rem 0.2rem;
    border: 0;
    background: transparent;
    color: rgba(255, 255, 255, 0.52);
    font-size: 0.36rem;
}

.page-inner .knockout-shell--strict .knockout-legend__dot {
    width: 0.22rem;
    height: 0.22rem;
}

@media (max-height: 690px) and (min-width: 761px) {
    .page-inner .knockout-shell--strict {
        height: calc(100svh - 3.85rem);
    }

    .page-inner .knockout-shell--strict .knockout-command__stats,
    .page-inner .knockout-shell--strict .knockout-legend {
        display: none;
    }

    .page-inner .knockout-shell--strict .knockout-command {
        min-height: 1.4rem;
    }
}

@media (max-width: 760px) {
    .page-inner .knockout-shell--strict {
        height: auto;
        padding: 0.5rem;
    }

    .page-inner .knockout-shell--strict .knockout-command {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.35rem;
        padding-bottom: 0.42rem;
    }

    .page-inner .knockout-shell--strict .knockout-command__stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-inner .knockout-shell--strict .knockout-bracket--fit {
        min-width: max-content;
    }
}

/* Final full-visibility fix: make desktop board fit the page instead of starting clipped. */
.page-inner .knockout-shell--strict {
    overflow: visible;
}

.page-inner .knockout-shell--strict .knockout-scroll {
    overflow-x: auto;
    overflow-y: hidden;
    margin-inline: -0.12rem;
    padding-inline: 0.34rem;
    scroll-padding-inline: 0.34rem;
    scrollbar-width: thin;
}

.page-inner .knockout-shell--strict .knockout-bracket--fit {
    width: 100%;
    min-width: 0;
    grid-template-columns: minmax(0, 1fr) minmax(136px, 0.32fr) minmax(0, 1fr);
    gap: clamp(0.16rem, 0.34vw, 0.28rem);
}

.page-inner .knockout-shell--strict .knockout-wing {
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: clamp(0.12rem, 0.24vw, 0.2rem);
}

.page-inner .knockout-shell--strict .knockout-round {
    min-width: 0;
}

.page-inner .knockout-shell--strict .knockout-match-card {
    min-width: 0;
}

.page-inner .knockout-shell--strict .knockout-team-row__name {
    font-size: clamp(0.42rem, 0.52vw, 0.52rem);
}

.page-inner .knockout-shell--strict .knockout-team-row__score {
    min-width: 0.78rem;
    min-height: 0.78rem;
}

.page-inner .knockout-shell--strict .knockout-round__header h3,
.page-inner .knockout-shell--strict .knockout-round--finals .knockout-round__header h3 {
    font-size: clamp(0.46rem, 0.58vw, 0.58rem) !important;
}

.page-inner .knockout-shell--strict .knockout-legend {
    display: none;
}

.page-inner .knockout-shell--strict .knockout-final-axis {
    min-width: 0;
}

.page-inner .knockout-shell--strict .knockout-round--finals {
    min-width: 0;
}

.page-inner .knockout-shell--strict .knockout-match-card--final {
    outline: 1px solid rgba(212, 173, 82, 0.28);
    outline-offset: 1px;
}

@media (max-width: 980px) {
    .page-inner .knockout-shell--strict .knockout-bracket--fit {
        width: max-content;
        min-width: 1040px;
    }

    .page-inner .knockout-shell--strict .knockout-scroll {
        padding-inline: 0.5rem;
        scroll-padding-inline: 0.5rem;
    }
}

@media (max-width: 760px) {
    .page-inner .knockout-shell--strict {
        overflow: hidden;
    }

    .page-inner .knockout-shell--strict .knockout-legend {
        display: flex;
    }
}

/* Auto-fit full bracket view: scale the complete board into desktop viewport when space is tight. */
.page-inner .knockout-shell--strict .knockout-fit-viewport {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    overflow-x: auto;
    overflow-y: hidden;
}

.page-inner .knockout-shell--strict .knockout-fit-canvas {
    --knockout-auto-scale: 1;
    flex: 0 0 auto;
    width: 1120px;
}

.page-inner .knockout-shell--strict .knockout-fit-canvas > .knockout-bracket--fit {
    width: 1120px;
    min-width: 1120px;
    grid-template-columns: 468px 168px 468px;
    transform-origin: top center;
}

.page-inner .knockout-shell--strict .knockout-fit-canvas.is-auto-scaled > .knockout-bracket--fit {
    will-change: transform;
}

.page-inner .knockout-shell--strict .knockout-fit-canvas .knockout-wing {
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.page-inner .knockout-shell--strict .knockout-fit-canvas .knockout-round {
    contain: layout;
}

.page-inner .knockout-shell--strict .knockout-fit-canvas .knockout-team-row__name {
    font-size: 0.46rem;
}

.page-inner .knockout-shell--strict .knockout-fit-canvas .knockout-match-card--final .knockout-team-row__name {
    font-size: 0.52rem;
}

@media (min-width: 981px) {
    .page-inner .knockout-shell--strict .knockout-fit-viewport {
        overflow-x: auto;
    }
}

@media (max-width: 980px) {
    .page-inner .knockout-shell--strict .knockout-fit-viewport {
        justify-content: flex-start;
        overflow-x: auto;
        padding-inline: 0.5rem;
        scroll-padding-inline: 0.5rem;
    }

    .page-inner .knockout-shell--strict .knockout-fit-canvas {
        width: auto !important;
        height: auto !important;
    }

    .page-inner .knockout-shell--strict .knockout-fit-canvas > .knockout-bracket--fit {
        width: max-content;
        min-width: 1040px;
        transform: none !important;
    }
}

/* World-class knockout board finalization: disciplined tournament canvas and premium compact nodes. */
.page-inner .knockout-shell--world-class {
    --knockout-board-width: 1088px;
    display: flex;
    flex-direction: column;
    height: clamp(420px, calc(100svh - 3.75rem), 560px);
    padding: 0.18rem;
    border: 1px solid rgba(212, 173, 82, 0.2);
    border-radius: 18px;
    background:
        radial-gradient(circle at 50% -12%, rgba(212, 173, 82, 0.16), transparent 25%),
        radial-gradient(circle at 0% 100%, rgba(175, 23, 49, 0.2), transparent 32%),
        linear-gradient(135deg, #02060c 0%, #081521 48%, #260712 100%);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.08),
        0 16px 38px rgba(3, 7, 13, 0.22);
}

.page-inner .knockout-shell--world-class .knockout-command {
    flex: 0 0 auto;
    min-height: 1.55rem;
    padding: 0.12rem 0.28rem 0.16rem;
    border-bottom: 1px solid rgba(212, 173, 82, 0.18);
}

.page-inner .knockout-shell--world-class .knockout-command h1 {
    color: rgba(255, 255, 255, 0.94);
    font-size: clamp(0.92rem, 1vw, 1.05rem);
    font-weight: 900;
    letter-spacing: -0.02em;
}

.page-inner .knockout-shell--world-class .knockout-command__stats {
    gap: 0.14rem;
}

.page-inner .knockout-shell--world-class .knockout-command__stats span {
    min-height: 1.05rem;
    padding: 0.04rem 0.24rem;
    border-color: rgba(212, 173, 82, 0.16);
    background: rgba(255, 255, 255, 0.045);
    color: rgba(255, 255, 255, 0.55);
    font-size: 0.34rem;
    letter-spacing: 0.075em;
}

.page-inner .knockout-shell--world-class .knockout-command__stats strong {
    color: rgba(255, 231, 176, 0.96);
    font-size: 0.68rem;
}

.page-inner .knockout-shell--world-class .knockout-fit-viewport {
    flex: 1 1 auto;
    min-height: 0;
    margin-top: 0.16rem;
    margin-inline: 0;
    padding: 0.2rem;
    border: 1px solid rgba(255, 255, 255, 0.075);
    border-radius: 14px;
    background:
        linear-gradient(90deg, rgba(255, 255, 255, 0.026), transparent 15%, transparent 85%, rgba(255, 255, 255, 0.026)),
        linear-gradient(180deg, rgba(255, 255, 255, 0.035), rgba(255, 255, 255, 0.012)),
        rgba(2, 7, 13, 0.2);
    scrollbar-color: rgba(212, 173, 82, 0.45) rgba(255, 255, 255, 0.06);
}

.page-inner .knockout-shell--world-class .knockout-fit-canvas {
    width: var(--knockout-board-width);
}

.page-inner .knockout-shell--world-class .knockout-fit-canvas > .knockout-bracket--world-class {
    width: var(--knockout-board-width);
    min-width: var(--knockout-board-width);
    grid-template-columns: 448px 176px 448px;
    gap: 0.16rem;
    align-items: stretch;
    transform-origin: top center;
}

.page-inner .knockout-shell--world-class .knockout-wing {
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.12rem;
    align-items: stretch;
}

.page-inner .knockout-shell--world-class .knockout-round {
    min-width: 0;
    overflow: visible;
    padding: 0.08rem;
    gap: 0.055rem;
    border-color: rgba(255, 255, 255, 0.075);
    border-radius: 9px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.065), rgba(255, 255, 255, 0.028)),
        rgba(255, 255, 255, 0.018);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.page-inner .knockout-shell--world-class .knockout-round--semi-final {
    border-color: rgba(212, 173, 82, 0.16);
    background:
        linear-gradient(180deg, rgba(212, 173, 82, 0.085), rgba(255, 255, 255, 0.026)),
        rgba(255, 255, 255, 0.018);
}

.page-inner .knockout-shell--world-class .knockout-round__header {
    min-height: 0.72rem;
    padding: 0.02rem 0.04rem 0.055rem;
    border-bottom-color: rgba(255, 255, 255, 0.07);
}

.page-inner .knockout-shell--world-class .knockout-round__header span,
.page-inner .knockout-shell--world-class .knockout-round__header small {
    display: none;
}

.page-inner .knockout-shell--world-class .knockout-round__header h3,
.page-inner .knockout-shell--world-class .knockout-round--finals .knockout-round__header h3 {
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.42rem !important;
    font-weight: 900;
    letter-spacing: 0.095em;
    line-height: 1;
    text-transform: uppercase;
}

.page-inner .knockout-shell--world-class .knockout-round__matches,
.page-inner .knockout-shell--world-class .knockout-final-stack {
    justify-content: space-between;
    gap: 0.045rem;
}

.page-inner .knockout-shell--world-class .knockout-round:not(.knockout-round--finals)::before {
    top: 1rem;
    bottom: 0.12rem;
    width: 1px;
    opacity: 0.72;
    background: linear-gradient(180deg, transparent, rgba(212, 173, 82, 0.58), rgba(175, 23, 49, 0.44), rgba(212, 173, 82, 0.58), transparent);
}

.page-inner .knockout-shell--world-class .knockout-wing--left .knockout-round:not(.knockout-round--finals)::before {
    inset-inline-end: -0.16rem;
}

.page-inner .knockout-shell--world-class .knockout-wing--right .knockout-round:not(.knockout-round--finals)::before {
    inset-inline-start: -0.16rem;
}

.page-inner .knockout-shell--world-class .knockout-round:not(.knockout-round--finals)::after {
    top: 50%;
    width: 0.22rem;
    height: 1px;
    background: rgba(212, 173, 82, 0.62);
}

.page-inner .knockout-shell--world-class .knockout-wing--left .knockout-round:not(.knockout-round--finals)::after {
    inset-inline-end: -0.24rem;
}

.page-inner .knockout-shell--world-class .knockout-wing--right .knockout-round:not(.knockout-round--finals)::after {
    inset-inline-start: -0.24rem;
    background: rgba(212, 173, 82, 0.62);
}

.page-inner .knockout-shell--world-class .knockout-match-card {
    min-width: 0;
    gap: 0.035rem;
    padding: 0.08rem;
    border: 1px solid rgba(10, 29, 51, 0.1);
    border-radius: 6px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(246, 248, 251, 0.95));
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.88),
        0 2px 5px rgba(2, 7, 13, 0.1);
}

.page-inner .knockout-shell--world-class .knockout-match-card.is-unresolved {
    border-color: rgba(212, 173, 82, 0.2);
}

.page-inner .knockout-shell--world-class .knockout-match-card__top {
    min-height: 0.45rem;
    gap: 0.08rem;
}

.page-inner .knockout-shell--world-class .knockout-match-card__code {
    color: rgba(175, 23, 49, 0.68);
    font-size: 0.29rem;
    font-weight: 950;
    letter-spacing: 0.06em;
}

.page-inner .knockout-shell--world-class .knockout-match-card__status {
    min-height: 0.45rem;
    padding: 0 0.1rem;
    border-radius: 999px;
    background: rgba(10, 29, 51, 0.055);
    color: rgba(10, 29, 51, 0.54);
    font-size: 0.245rem;
    letter-spacing: 0.05em;
}

.page-inner .knockout-shell--world-class .knockout-match-card__body {
    gap: 0.035rem;
}

.page-inner .knockout-shell--world-class .knockout-team-row {
    min-height: 0.64rem;
    gap: 0.08rem;
    padding: 0.035rem 0.075rem;
    border-radius: 4px;
    border-color: rgba(10, 29, 51, 0.055);
    background: rgba(255, 255, 255, 0.78);
}

.page-inner .knockout-shell--world-class .knockout-team-row.is-resolved {
    border-color: rgba(13, 107, 69, 0.14);
}

.page-inner .knockout-shell--world-class .knockout-team-row.is-pending {
    border-style: solid;
    border-color: rgba(212, 173, 82, 0.22);
    background:
        linear-gradient(90deg, rgba(212, 173, 82, 0.12), rgba(255, 255, 255, 0.78));
}

.page-inner .knockout-shell--world-class .knockout-team-row__name {
    color: rgba(10, 29, 51, 0.88);
    font-size: 0.43rem;
    font-weight: 900;
    letter-spacing: -0.012em;
    line-height: 1.04;
}

.page-inner .knockout-shell--world-class .knockout-team-row.is-pending .knockout-team-row__name {
    color: rgba(92, 74, 38, 0.9);
    font-weight: 850;
}

.page-inner .knockout-shell--world-class .knockout-team-row__score {
    min-width: 0.62rem;
    min-height: 0.62rem;
    border-radius: 4px;
    background: rgba(10, 29, 51, 0.07);
    color: rgba(10, 29, 51, 0.9);
    font-size: 0.38rem;
    line-height: 1;
}

.page-inner .knockout-shell--world-class .knockout-match-card__penalties {
    font-size: 0.28rem;
}

.page-inner .knockout-shell--world-class .knockout-match-card:not(.is-terminal) .knockout-match-card__meta {
    display: none;
}

.page-inner .knockout-shell--world-class .knockout-match-card:not(.is-terminal)::after {
    width: 0.2rem;
    height: 1px;
    background: rgba(212, 173, 82, 0.62);
}

.page-inner .knockout-shell--world-class .knockout-wing--left .knockout-match-card:not(.is-terminal)::after {
    inset-inline-end: -0.21rem;
}

.page-inner .knockout-shell--world-class .knockout-wing--right .knockout-match-card:not(.is-terminal)::after {
    inset-inline-start: -0.21rem;
    background: rgba(212, 173, 82, 0.62);
}

.page-inner .knockout-shell--world-class .knockout-match-card__node {
    width: 0.18rem;
    height: 0.18rem;
    border: 1px solid rgba(255, 255, 255, 0.85);
    background: var(--gold);
    box-shadow: 0 0 0 2px rgba(212, 173, 82, 0.12);
}

.page-inner .knockout-shell--world-class .knockout-wing--left .knockout-match-card__node {
    inset-inline-end: -0.095rem;
}

.page-inner .knockout-shell--world-class .knockout-wing--right .knockout-match-card__node {
    inset-inline-start: -0.095rem;
}

.page-inner .knockout-shell--world-class .knockout-final-axis {
    gap: 0.075rem;
    min-width: 0;
}

.page-inner .knockout-shell--world-class .knockout-final-axis::before {
    inset-inline: -0.2rem;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(212, 173, 82, 0.68), transparent);
}

.page-inner .knockout-shell--world-class .knockout-final-axis__cap {
    min-height: 0.94rem;
    border: 1px solid rgba(212, 173, 82, 0.24);
    border-radius: 8px;
    background:
        linear-gradient(135deg, rgba(212, 173, 82, 0.18), rgba(175, 23, 49, 0.16)),
        rgba(255, 255, 255, 0.045);
}

.page-inner .knockout-shell--world-class .knockout-final-axis__cap strong {
    color: rgba(255, 232, 177, 0.98);
    font-size: 0.54rem;
    font-weight: 950;
    letter-spacing: 0.035em;
}

.page-inner .knockout-shell--world-class .knockout-final-axis__line {
    top: 1.08rem;
    bottom: 0.1rem;
    width: 1px;
    background: linear-gradient(180deg, rgba(212, 173, 82, 0.1), rgba(212, 173, 82, 0.64), rgba(175, 23, 49, 0.46), rgba(212, 173, 82, 0.2));
}

.page-inner .knockout-shell--world-class .knockout-round--finals {
    min-width: 0;
    padding: 0.1rem;
    border-color: rgba(212, 173, 82, 0.3);
    background:
        radial-gradient(circle at 50% 0%, rgba(212, 173, 82, 0.18), transparent 38%),
        rgba(255, 255, 255, 0.06);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.08),
        0 0 0 1px rgba(175, 23, 49, 0.08);
}

.page-inner .knockout-shell--world-class .knockout-round--finals .knockout-round__header {
    min-height: 0.78rem;
    border-bottom-color: rgba(212, 173, 82, 0.18);
}

.page-inner .knockout-shell--world-class .knockout-final-stack {
    justify-content: center;
    gap: 0.12rem;
}

.page-inner .knockout-shell--world-class .knockout-match-card__kicker {
    min-height: 0.45rem;
    padding: 0 0.12rem;
    border-radius: 999px;
    font-size: 0.235rem;
    font-weight: 950;
    letter-spacing: 0.08em;
}

.page-inner .knockout-shell--world-class .knockout-match-card--final {
    padding: 0.14rem;
    border-color: rgba(212, 173, 82, 0.56);
    border-radius: 8px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 247, 226, 0.97));
    outline: 1px solid rgba(212, 173, 82, 0.36);
    outline-offset: 1px;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.96),
        0 7px 16px rgba(0, 0, 0, 0.18);
}

.page-inner .knockout-shell--world-class .knockout-match-card--final::before {
    inset: -0.08rem;
    border-radius: 10px;
    opacity: 0.42;
}

.page-inner .knockout-shell--world-class .knockout-match-card--final .knockout-match-card__kicker {
    background: linear-gradient(90deg, var(--brand), #8d1027);
    color: #fff;
}

.page-inner .knockout-shell--world-class .knockout-match-card--final .knockout-team-row {
    min-height: 0.78rem;
    padding: 0.055rem 0.09rem;
    border-color: rgba(212, 173, 82, 0.18);
}

.page-inner .knockout-shell--world-class .knockout-match-card--final .knockout-team-row__name {
    font-size: 0.47rem;
}

.page-inner .knockout-shell--world-class .knockout-match-card--final .knockout-team-row__score {
    min-width: 0.72rem;
    min-height: 0.72rem;
    background: rgba(175, 23, 49, 0.08);
    font-size: 0.43rem;
}

.page-inner .knockout-shell--world-class .knockout-match-card--final .knockout-match-card__meta,
.page-inner .knockout-shell--world-class .knockout-match-card--third .knockout-match-card__meta {
    gap: 0.08rem;
    padding-top: 0.045rem;
    border-top-color: rgba(10, 29, 51, 0.065);
    font-size: 0.31rem;
}

.page-inner .knockout-shell--world-class .knockout-match-card--final .knockout-match-card__meta a {
    color: rgba(175, 23, 49, 0.9);
}

.page-inner .knockout-shell--world-class .knockout-match-card--third {
    margin-top: 0;
    padding: 0.1rem;
    border-color: rgba(255, 255, 255, 0.13);
    border-radius: 7px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.085), rgba(255, 255, 255, 0.045));
    box-shadow: none;
}

.page-inner .knockout-shell--world-class .knockout-match-card--third .knockout-match-card__kicker {
    border-color: rgba(212, 173, 82, 0.22);
    background: rgba(212, 173, 82, 0.1);
    color: rgba(255, 232, 177, 0.86);
}

.page-inner .knockout-shell--world-class .knockout-match-card--third .knockout-team-row {
    min-height: 0.64rem;
    border-color: rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.06);
}

.page-inner .knockout-shell--world-class .knockout-match-card--third .knockout-team-row__name {
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.39rem;
}

.page-inner .knockout-shell--world-class .knockout-match-card--third .knockout-team-row__score {
    min-width: 0.58rem;
    min-height: 0.58rem;
    background: rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.34rem;
}

.page-inner .knockout-shell--world-class .knockout-match-card--third .knockout-match-card__meta,
.page-inner .knockout-shell--world-class .knockout-match-card--third .knockout-match-card__meta a {
    color: rgba(255, 255, 255, 0.64);
}

.page-inner .knockout-shell--world-class .knockout-legend {
    display: none;
}

@media (max-width: 1120px) and (min-width: 981px) {
    .page-inner .knockout-shell--world-class {
        --knockout-board-width: 1056px;
    }

    .page-inner .knockout-shell--world-class .knockout-fit-canvas > .knockout-bracket--world-class {
        grid-template-columns: 432px 176px 432px;
    }
}

@media (max-height: 700px) and (min-width: 981px) {
    .page-inner .knockout-shell--world-class {
        height: calc(100svh - 3.35rem);
    }

    .page-inner .knockout-shell--world-class .knockout-command {
        min-height: 1.28rem;
        padding-block: 0.08rem;
    }

    .page-inner .knockout-shell--world-class .knockout-command__stats {
        display: none;
    }

    .page-inner .knockout-shell--world-class .knockout-fit-viewport {
        margin-top: 0.1rem;
        padding: 0.14rem;
    }
}

@media (max-width: 980px) {
    .page-inner .knockout-shell--world-class {
        height: auto;
        padding: 0.42rem;
    }

    .page-inner .knockout-shell--world-class .knockout-fit-canvas {
        width: auto !important;
        height: auto !important;
    }

    .page-inner .knockout-shell--world-class .knockout-fit-canvas > .knockout-bracket--world-class {
        width: max-content;
        min-width: 1040px;
        grid-template-columns: 428px 172px 428px;
        transform: none !important;
    }

    .page-inner .knockout-shell--world-class .knockout-fit-viewport {
        justify-content: flex-start;
        padding: 0.45rem;
    }
}

/* Readable world-class bracket redesign: comfort and clarity over forced full-fit compression. */
.page-inner .knockout-shell--readable {
    --knockout-board-width: 1420px;
    height: auto;
    min-height: clamp(620px, calc(100svh - 4rem), 780px);
    overflow: visible;
    padding: clamp(0.46rem, 0.85vw, 0.7rem);
    border-color: rgba(212, 173, 82, 0.24);
    border-radius: 22px;
    background:
        radial-gradient(circle at 50% -8%, rgba(212, 173, 82, 0.15), transparent 24%),
        radial-gradient(circle at 100% 10%, rgba(175, 23, 49, 0.18), transparent 28%),
        linear-gradient(135deg, #03070d 0%, #071522 46%, #250711 100%);
}

.page-inner .knockout-shell--readable .knockout-command {
    min-height: 2rem;
    padding: 0.2rem 0.36rem 0.28rem;
    border-bottom-color: rgba(212, 173, 82, 0.22);
}

.page-inner .knockout-shell--readable .knockout-command h1 {
    font-size: clamp(1rem, 1.15vw, 1.22rem);
    letter-spacing: -0.025em;
}

.page-inner .knockout-shell--readable .knockout-command__stats {
    gap: 0.2rem;
}

.page-inner .knockout-shell--readable .knockout-command__stats span {
    min-height: 1.18rem;
    padding: 0.08rem 0.34rem;
    font-size: 0.38rem;
}

.page-inner .knockout-shell--readable .knockout-command__stats strong {
    font-size: 0.76rem;
}

.page-inner .knockout-shell--readable .knockout-fit-viewport {
    justify-content: flex-start;
    min-height: 0;
    margin-top: 0.52rem;
    padding: clamp(0.46rem, 0.8vw, 0.72rem);
    overflow-x: auto;
    overflow-y: visible;
    border-color: rgba(255, 255, 255, 0.09);
    border-radius: 20px;
    background:
        linear-gradient(90deg, rgba(255, 255, 255, 0.035), transparent 10%, transparent 90%, rgba(255, 255, 255, 0.035)),
        radial-gradient(circle at 50% 0%, rgba(212, 173, 82, 0.08), transparent 38%),
        rgba(2, 7, 13, 0.22);
    scroll-padding-inline: 0.72rem;
}

.page-inner .knockout-shell--readable .knockout-fit-canvas {
    width: var(--knockout-board-width);
    margin-inline: auto;
}

.page-inner .knockout-shell--readable .knockout-fit-canvas > .knockout-bracket--readable {
    width: var(--knockout-board-width);
    min-width: var(--knockout-board-width);
    min-height: clamp(560px, calc(100svh - 12rem), 690px);
    grid-template-columns: 596px 228px 596px;
    gap: 0.44rem;
    align-items: stretch;
    transform-origin: top center;
}

.page-inner .knockout-shell--readable .knockout-wing {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    grid-template-rows: auto minmax(0, 1fr);
    align-items: stretch;
    gap: 0.3rem 0.32rem;
}

.page-inner .knockout-shell--readable .knockout-wing__title {
    grid-column: 1 / -1;
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 0.75rem;
    min-height: 1.35rem;
    padding: 0 0.08rem 0.36rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.page-inner .knockout-shell--readable .knockout-wing__title span {
    color: rgba(255, 232, 177, 0.78);
    font-size: 0.5rem;
    font-weight: 950;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.page-inner .knockout-shell--readable .knockout-wing__title strong {
    color: rgba(255, 255, 255, 0.64);
    font-size: 0.55rem;
    font-weight: 850;
}

.page-inner .knockout-shell--readable .knockout-round {
    min-width: 0;
    overflow: visible;
    padding: 0.18rem 0.14rem;
    gap: 0.22rem;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.072), rgba(255, 255, 255, 0.03)),
        rgba(255, 255, 255, 0.016);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.055);
}

.page-inner .knockout-shell--readable .knockout-round__header {
    display: grid;
    gap: 0.12rem;
    min-height: 1.25rem;
    padding: 0.02rem 0.08rem 0.18rem;
    border-bottom-color: rgba(255, 255, 255, 0.08);
    text-align: start;
}

.page-inner .knockout-shell--readable .knockout-round__header span {
    display: none;
}

.page-inner .knockout-shell--readable .knockout-round__header h3,
.page-inner .knockout-shell--readable .knockout-round--finals .knockout-round__header h3 {
    color: rgba(255, 255, 255, 0.86);
    font-size: 0.61rem !important;
    font-weight: 950;
    letter-spacing: 0.045em;
    line-height: 1.05;
}

.page-inner .knockout-shell--readable .knockout-round__header small {
    display: block;
    color: rgba(255, 255, 255, 0.42);
    font-size: 0.36rem;
    font-weight: 850;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.page-inner .knockout-shell--readable .knockout-round__matches,
.page-inner .knockout-shell--readable .knockout-final-stack {
    justify-content: space-around;
    gap: 0.2rem;
}

.page-inner .knockout-shell--readable .knockout-round:not(.knockout-round--finals)::before {
    top: 1.72rem;
    bottom: 0.42rem;
    width: 1px;
    opacity: 0.55;
    background: linear-gradient(180deg, transparent, rgba(212, 173, 82, 0.5), rgba(175, 23, 49, 0.36), rgba(212, 173, 82, 0.5), transparent);
}

.page-inner .knockout-shell--readable .knockout-wing--left .knockout-round:not(.knockout-round--finals)::before {
    inset-inline-end: -0.2rem;
}

.page-inner .knockout-shell--readable .knockout-wing--right .knockout-round:not(.knockout-round--finals)::before {
    inset-inline-start: -0.2rem;
}

.page-inner .knockout-shell--readable .knockout-round:not(.knockout-round--finals)::after {
    top: 50%;
    width: 0.3rem;
    height: 1px;
    opacity: 0.7;
    background: rgba(212, 173, 82, 0.58);
}

.page-inner .knockout-shell--readable .knockout-wing--left .knockout-round:not(.knockout-round--finals)::after {
    inset-inline-end: -0.32rem;
}

.page-inner .knockout-shell--readable .knockout-wing--right .knockout-round:not(.knockout-round--finals)::after {
    inset-inline-start: -0.32rem;
}

.page-inner .knockout-shell--readable .knockout-match-card:not(.is-terminal)::after,
.page-inner .knockout-shell--readable .knockout-match-card__node {
    display: none;
}

.page-inner .knockout-shell--readable .knockout-match-card {
    gap: 0.12rem;
    padding: 0.17rem;
    border-color: rgba(10, 29, 51, 0.12);
    border-radius: 10px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(247, 249, 252, 0.96));
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.92),
        0 4px 10px rgba(2, 7, 13, 0.12);
}

.page-inner .knockout-shell--readable .knockout-match-card.is-unresolved {
    border-color: rgba(212, 173, 82, 0.26);
    background:
        linear-gradient(180deg, rgba(255, 254, 249, 0.99), rgba(250, 247, 238, 0.96));
}

.page-inner .knockout-shell--readable .knockout-match-card__top {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    gap: 0.22rem;
    min-height: 0.64rem;
}

.page-inner .knockout-shell--readable .knockout-match-card__code {
    color: rgba(175, 23, 49, 0.64);
    font-size: 0.36rem;
    letter-spacing: 0.075em;
}

.page-inner .knockout-shell--readable .knockout-match-card__date {
    overflow: hidden;
    color: rgba(10, 29, 51, 0.46);
    font-size: 0.35rem;
    font-weight: 850;
    line-height: 1;
    text-align: center;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.page-inner .knockout-shell--readable .knockout-match-card__status {
    min-height: 0.55rem;
    padding: 0.02rem 0.14rem;
    background: rgba(10, 29, 51, 0.06);
    color: rgba(10, 29, 51, 0.56);
    font-size: 0.29rem;
    letter-spacing: 0.055em;
}

.page-inner .knockout-shell--readable .knockout-match-card__body {
    gap: 0.08rem;
}

.page-inner .knockout-shell--readable .knockout-team-row {
    min-height: 1.1rem;
    gap: 0.12rem;
    padding: 0.09rem 0.12rem;
    border-radius: 7px;
    border-color: rgba(10, 29, 51, 0.07);
    background: rgba(255, 255, 255, 0.78);
}

.page-inner .knockout-shell--readable .knockout-team-row.is-pending {
    border-style: solid;
    border-color: rgba(212, 173, 82, 0.28);
    background:
        linear-gradient(90deg, rgba(212, 173, 82, 0.12), rgba(255, 255, 255, 0.78));
}

.page-inner .knockout-shell--readable .knockout-team-row__name {
    display: -webkit-box;
    overflow: hidden;
    color: rgba(10, 29, 51, 0.9);
    font-size: 0.56rem;
    font-weight: 900;
    letter-spacing: -0.012em;
    line-height: 1.08;
    text-overflow: initial;
    white-space: normal;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

.page-inner .knockout-shell--readable .knockout-team-row.is-pending .knockout-team-row__name {
    color: rgba(90, 72, 39, 0.9);
    font-style: normal;
}

.page-inner .knockout-shell--readable .knockout-team-row__score {
    min-width: 0.78rem;
    min-height: 0.78rem;
    border-radius: 5px;
    background: rgba(10, 29, 51, 0.075);
    color: rgba(10, 29, 51, 0.92);
    font-size: 0.46rem;
}

.page-inner .knockout-shell--readable .knockout-round--round-of-32 .knockout-team-row,
.page-inner .knockout-shell--readable .knockout-round--round-of-16 .knockout-team-row {
    min-height: 1.02rem;
}

.page-inner .knockout-shell--readable .knockout-round--round-of-32 .knockout-team-row__name,
.page-inner .knockout-shell--readable .knockout-round--round-of-16 .knockout-team-row__name {
    font-size: 0.5rem;
}

.page-inner .knockout-shell--readable .knockout-round--quarter-final .knockout-match-card:not(.is-terminal) .knockout-match-card__meta,
.page-inner .knockout-shell--readable .knockout-round--semi-final .knockout-match-card:not(.is-terminal) .knockout-match-card__meta {
    display: flex;
}

.page-inner .knockout-shell--readable .knockout-match-card:not(.is-terminal) .knockout-match-card__meta {
    gap: 0.15rem;
    padding-top: 0.04rem;
    border-top: 1px solid rgba(10, 29, 51, 0.06);
    color: rgba(10, 29, 51, 0.45);
    font-size: 0.35rem;
}

.page-inner .knockout-shell--readable .knockout-match-card:not(.is-terminal) .knockout-match-card__meta a {
    color: rgba(175, 23, 49, 0.74);
    font-size: 0.35rem;
}

.page-inner .knockout-shell--readable .knockout-final-axis {
    gap: 0.36rem;
}

.page-inner .knockout-shell--readable .knockout-final-axis::before {
    inset-inline: -0.34rem;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(212, 173, 82, 0.74), rgba(175, 23, 49, 0.42), rgba(212, 173, 82, 0.74), transparent);
}

.page-inner .knockout-shell--readable .knockout-final-axis__cap {
    min-height: 1.35rem;
    border-radius: 14px;
    border-color: rgba(212, 173, 82, 0.34);
    background:
        linear-gradient(135deg, rgba(212, 173, 82, 0.22), rgba(175, 23, 49, 0.18)),
        rgba(255, 255, 255, 0.06);
}

.page-inner .knockout-shell--readable .knockout-final-axis__cap strong {
    font-size: 0.7rem;
    letter-spacing: 0.04em;
}

.page-inner .knockout-shell--readable .knockout-final-axis__line {
    top: 1.65rem;
    bottom: 0.42rem;
    background: linear-gradient(180deg, rgba(212, 173, 82, 0.12), rgba(212, 173, 82, 0.62), rgba(175, 23, 49, 0.46), rgba(212, 173, 82, 0.24));
}

.page-inner .knockout-shell--readable .knockout-round--finals {
    padding: 0.22rem;
    border-color: rgba(212, 173, 82, 0.36);
    border-radius: 16px;
    background:
        radial-gradient(circle at 50% 0%, rgba(212, 173, 82, 0.2), transparent 42%),
        rgba(255, 255, 255, 0.065);
}

.page-inner .knockout-shell--readable .knockout-round--finals .knockout-round__header {
    min-height: 1.34rem;
    text-align: center;
}

.page-inner .knockout-shell--readable .knockout-final-stack {
    gap: 0.34rem;
}

.page-inner .knockout-shell--readable .knockout-match-card__kicker {
    min-height: 0.6rem;
    font-size: 0.31rem;
}

.page-inner .knockout-shell--readable .knockout-match-card--final {
    gap: 0.15rem;
    padding: 0.22rem;
    border-radius: 13px;
    border-color: rgba(212, 173, 82, 0.62);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.98),
        0 12px 24px rgba(0, 0, 0, 0.22);
}

.page-inner .knockout-shell--readable .knockout-match-card--final .knockout-team-row {
    min-height: 1.24rem;
    padding: 0.12rem 0.16rem;
}

.page-inner .knockout-shell--readable .knockout-match-card--final .knockout-team-row__name {
    font-size: 0.62rem;
}

.page-inner .knockout-shell--readable .knockout-match-card--final .knockout-team-row__score {
    min-width: 0.96rem;
    min-height: 0.96rem;
    font-size: 0.58rem;
}

.page-inner .knockout-shell--readable .knockout-match-card--final .knockout-match-card__meta,
.page-inner .knockout-shell--readable .knockout-match-card--third .knockout-match-card__meta {
    padding-top: 0.08rem;
    font-size: 0.4rem;
}

.page-inner .knockout-shell--readable .knockout-match-card--third {
    padding: 0.16rem;
    border-radius: 11px;
}

.page-inner .knockout-shell--readable .knockout-match-card--third .knockout-team-row {
    min-height: 0.98rem;
    padding: 0.08rem 0.12rem;
}

.page-inner .knockout-shell--readable .knockout-match-card--third .knockout-team-row__name {
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.5rem;
}

.page-inner .knockout-shell--readable .knockout-match-card--third .knockout-team-row__score {
    min-width: 0.72rem;
    min-height: 0.72rem;
    font-size: 0.42rem;
}

@media (max-width: 1280px) and (min-width: 981px) {
    .page-inner .knockout-shell--readable {
        --knockout-board-width: 1360px;
    }

    .page-inner .knockout-shell--readable .knockout-fit-canvas > .knockout-bracket--readable {
        grid-template-columns: 568px 224px 568px;
    }
}

@media (max-height: 760px) and (min-width: 981px) {
    .page-inner .knockout-shell--readable {
        min-height: 600px;
    }

    .page-inner .knockout-shell--readable .knockout-fit-viewport {
        margin-top: 0.34rem;
    }

    .page-inner .knockout-shell--readable .knockout-fit-canvas > .knockout-bracket--readable {
        min-height: 540px;
    }
}

@media (max-width: 980px) {
    .page-inner .knockout-shell--readable {
        --knockout-board-width: 1260px;
        min-height: 0;
        padding: 0.45rem;
    }

    .page-inner .knockout-shell--readable .knockout-fit-viewport {
        padding: 0.45rem;
    }

    .page-inner .knockout-shell--readable .knockout-fit-canvas > .knockout-bracket--readable {
        width: var(--knockout-board-width);
        min-width: var(--knockout-board-width);
        min-height: 540px;
        grid-template-columns: 520px 220px 520px;
    }
}

/* ==========================================================================
   PHASE 1A — Public UI/UX Premium Redesign (consolidated refinement layer)
   Visual direction: deep navy + dark burgundy + Moroccan red + warm gold,
   off-white cards, stronger typography, more whitespace, clearer hierarchy,
   improved accessibility and mobile responsiveness.
   This layer takes precedence over earlier override iterations so the
   public surface presents a consistent, premium tournament portal feel.
   ========================================================================== */

:root {
    --brand: #af1731;
    --brand-strong: #75101f;
    --brand-bright: #d22a44;
    --gold: #d4ad52;
    --gold-strong: #b88a2a;
    --burgundy: #5c0f1e;
    --ink: #07182c;
    --paper: #fbfbf7;
    --paper-strong: #ffffff;
    --section-gap-1a: clamp(2.4rem, 3.6vw, 3.8rem);
    --container-1a: 1240px;
}

html {
    scroll-behavior: smooth;
}

body {
    background:
        radial-gradient(ellipse at top, rgba(7, 24, 44, 0.05), transparent 56%),
        radial-gradient(circle at 92% 4%, rgba(175, 23, 49, 0.04), transparent 26%),
        linear-gradient(180deg, #f6f8fc 0%, #eef2f8 100%);
    background-attachment: fixed;
    font-family: "Inter", "Segoe UI Variable", "Segoe UI", "Helvetica Neue", system-ui, -apple-system, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: optimizeLegibility;
}

.page-container,
.site-header__top-inner,
.site-header__inner,
.site-footer__inner {
    width: min(calc(100% - 36px), var(--container-1a));
}

main {
    padding: 1rem 0 5rem;
}

/* --- Public header ---------------------------------------------------- */

.site-header {
    background:
        linear-gradient(180deg, #04090f 0%, #061629 58%, #1f0510 100%),
        #04090f;
    border-bottom: 1px solid rgba(212, 173, 82, 0.18);
    box-shadow: 0 16px 38px rgba(4, 9, 15, 0.28);
    backdrop-filter: saturate(140%) blur(6px);
    position: sticky;
    top: 0;
    z-index: 40;
}

.site-header::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: -1px;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, rgba(175, 23, 49, 0.42) 30%, rgba(212, 173, 82, 0.55) 50%, rgba(175, 23, 49, 0.42) 70%, transparent 100%);
    pointer-events: none;
}

.site-header__inner {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    align-items: center;
    gap: 1.25rem;
    min-height: 3.6rem;
    padding-block: 0.6rem;
}

.brand {
    display: inline-flex;
    align-items: center;
    gap: 0.78rem;
    padding: 0.2rem 0.34rem 0.2rem 0.2rem;
    border-radius: 12px;
    color: #fff;
    transition: background-color 0.18s ease;
}

.brand:hover {
    background: rgba(255, 255, 255, 0.05);
}

.brand:focus-visible {
    box-shadow: var(--focus-ring-light);
}

.brand__mark {
    position: relative;
    width: 2.45rem;
    height: 2.45rem;
    border-radius: 10px;
    display: grid;
    place-items: center;
    background:
        linear-gradient(145deg, var(--brand-bright), var(--brand) 56%, var(--burgundy));
    color: #fff;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 0.9rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.3),
        inset 0 0 0 1px rgba(212, 173, 82, 0.22),
        0 8px 22px rgba(175, 23, 49, 0.34);
}

.brand__mark::after {
    content: "";
    position: absolute;
    inset: auto 0 -3px 0;
    height: 2px;
    border-radius: 0 0 6px 6px;
    background: linear-gradient(90deg, transparent, var(--gold), transparent);
    opacity: 0.85;
}

.brand__meta {
    display: flex;
    flex-direction: column;
    line-height: 1.04;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.02rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.005em;
}

.brand__meta small {
    margin-top: 0.22rem;
    font-family: "Inter", "Segoe UI", sans-serif;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--gold);
}

.site-header__nav-shell {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.1rem;
    min-width: 0;
}

.site-nav {
    display: flex;
    align-items: center;
    gap: 0.15rem;
    padding: 0;
    overflow-x: auto;
    scrollbar-width: none;
    flex: 1 1 auto;
    min-width: 0;
}

.site-nav::-webkit-scrollbar { display: none; }

.site-nav a {
    position: relative;
    flex: 0 0 auto;
    padding: 0.55rem 0.85rem;
    border-radius: 8px;
    background: transparent;
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.84rem;
    font-weight: 600;
    letter-spacing: 0.005em;
    white-space: nowrap;
    line-height: 1;
    box-shadow: none;
    transition: color 0.18s ease, background-color 0.18s ease;
}

.site-nav a:hover {
    background: rgba(255, 255, 255, 0.07);
    color: #fff;
    box-shadow: none;
}

.site-nav a.is-active {
    background: rgba(255, 255, 255, 0.06);
    color: #fff;
    box-shadow: none;
}

.site-nav a::after {
    content: "";
    position: absolute;
    left: 0.85rem;
    right: 0.85rem;
    bottom: -0.36rem;
    height: 2px;
    border-radius: 2px;
    background: transparent;
    transition: background 0.18s ease;
}

.site-nav a.is-active::after {
    background: linear-gradient(90deg, var(--gold), var(--brand-bright));
}

.site-nav a:focus-visible {
    box-shadow: var(--focus-ring-light);
    outline: 0;
}

.site-header__utility-tools,
.site-header__audience-tools {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.36rem;
    flex: 0 0 auto;
}

.shell-icon-button {
    width: 2.2rem;
    height: 2.2rem;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.04);
    color: rgba(255, 255, 255, 0.9);
    transition: background-color 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
}

.shell-icon-button:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(212, 173, 82, 0.36);
    color: #fff;
    transform: translateY(-1px);
}

.shell-icon-button[aria-expanded="true"],
.shell-icon-button.is-active {
    background: rgba(212, 173, 82, 0.16);
    border-color: rgba(212, 173, 82, 0.5);
    color: #fff;
}

.shell-icon-button:focus-visible {
    box-shadow: var(--focus-ring-light);
    outline: 0;
}

.shell-icon {
    width: 1.1rem;
    height: 1.1rem;
    fill: currentColor;
}

.shell-current-language {
    font-size: 0.66rem;
    font-weight: 800;
    letter-spacing: 0.06em;
}

/* --- Buttons (clear primary/secondary/tertiary hierarchy) ------------- */

.button,
.button--subtle,
.button--ghost,
.match-card__cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.55rem;
    min-height: 2.55rem;
    padding: 0.65rem 1.15rem;
    border-radius: 999px;
    border: 1px solid transparent;
    font-family: inherit;
    font-weight: 700;
    font-size: 0.88rem;
    letter-spacing: 0.005em;
    cursor: pointer;
    white-space: nowrap;
    line-height: 1;
    transition: transform 0.18s ease, background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
}

.button {
    background: linear-gradient(140deg, var(--brand-bright), var(--brand) 55%, var(--burgundy));
    color: #fff;
    border-color: rgba(255, 255, 255, 0.1);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.26),
        0 12px 26px rgba(175, 23, 49, 0.32);
}

.button:hover {
    transform: translateY(-1px);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.34),
        0 18px 34px rgba(175, 23, 49, 0.4);
}

.button:active {
    transform: translateY(0);
}

.button--subtle,
.match-card__cta {
    background: var(--paper-strong);
    color: var(--ink);
    border-color: rgba(7, 24, 44, 0.16);
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.65) inset, 0 8px 18px rgba(7, 24, 44, 0.08);
}

.button--subtle:hover,
.match-card__cta:hover {
    transform: translateY(-1px);
    background: #fff;
    border-color: var(--ink);
    color: var(--ink);
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.75) inset, 0 14px 26px rgba(7, 24, 44, 0.14);
}

.button--ghost {
    background: transparent;
    color: var(--brand);
    border-color: transparent;
    padding-inline: 0.85rem;
    box-shadow: none;
    text-decoration-line: underline;
    text-decoration-thickness: 1px;
    text-underline-offset: 6px;
    text-decoration-color: rgba(175, 23, 49, 0.4);
}

.button--ghost:hover {
    transform: translateY(-1px);
    color: var(--brand-strong);
    text-decoration-color: var(--brand);
    background: rgba(175, 23, 49, 0.06);
}

.button:focus-visible,
.button--subtle:focus-visible,
.button--ghost:focus-visible,
.match-card__cta:focus-visible {
    box-shadow: var(--focus-ring);
    outline: 0;
}

/* --- Hero (less cluttered, stronger hierarchy) ------------------------ */

.hero,
.hero--command {
    position: relative;
    overflow: hidden;
    display: grid;
    grid-template-columns: minmax(0, 1.18fr) minmax(330px, 0.85fr);
    gap: clamp(1.6rem, 2.8vw, 2.8rem);
    align-items: stretch;
    min-height: 0;
    padding: clamp(1.8rem, 3vw, 2.85rem);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 22px;
    color: #fff;
    background:
        radial-gradient(circle at 88% 8%, rgba(212, 173, 82, 0.22), transparent 30%),
        radial-gradient(circle at 6% 90%, rgba(175, 23, 49, 0.26), transparent 36%),
        linear-gradient(135deg, #061629 0%, #0c2540 48%, #2c0a17 100%);
    box-shadow: var(--shadow-xl);
}

.hero::before,
.hero--command::before {
    content: "";
    position: absolute;
    inset: auto -10% -38% auto;
    width: clamp(220px, 28vw, 420px);
    aspect-ratio: 1;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(212, 173, 82, 0.32), transparent 64%);
    pointer-events: none;
}

.hero::after,
.hero--command::after {
    content: "";
    position: absolute;
    inset: 0;
    background:
        linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.08) 100%),
        repeating-linear-gradient(120deg, rgba(255, 255, 255, 0.024) 0 1px, transparent 1px 22px);
    pointer-events: none;
}

.hero__main {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 1rem;
    max-width: 46rem;
}

.hero__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    color: var(--gold);
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-size: 0.74rem;
    font-weight: 700;
    margin: 0;
}

.hero__eyebrow::before {
    content: "";
    width: 1.4rem;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--gold));
}

.hero__chip-row {
    display: none;
}

.hero h1 {
    margin: 0;
    max-width: 14ch;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(2.6rem, 5.4vw, 4.8rem);
    line-height: 1.02;
    letter-spacing: -0.045em;
    font-weight: 700;
    color: #fff;
}

.hero h1 span {
    display: block;
    margin-top: 0.55rem;
    color: var(--gold);
    font-family: "Inter", "Segoe UI", sans-serif;
    font-size: clamp(0.86rem, 1.3vw, 1.05rem);
    letter-spacing: 0.22em;
    text-transform: uppercase;
    font-weight: 700;
}

.hero h1 span::before {
    display: none;
}

.hero p {
    max-width: 44rem;
    margin: 0;
    color: rgba(255, 255, 255, 0.82);
    font-size: clamp(0.98rem, 1.1vw, 1.1rem);
    line-height: 1.65;
}

.hero__actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.65rem;
    margin-top: 0.5rem;
}

.hero__actions .button {
    min-height: 2.85rem;
    padding: 0.78rem 1.45rem;
    font-size: 0.96rem;
}

.hero__actions .button--subtle {
    min-height: 2.85rem;
    padding: 0.78rem 1.35rem;
    font-size: 0.92rem;
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.2);
    color: #fff;
    backdrop-filter: blur(8px);
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.1) inset;
}

.hero__actions .button--subtle:hover {
    background: rgba(255, 255, 255, 0.14);
    border-color: rgba(255, 255, 255, 0.34);
    color: #fff;
}

.hero__actions .button--ghost {
    color: var(--gold);
    padding-inline: 1rem;
    text-decoration-color: rgba(212, 173, 82, 0.45);
    background: transparent;
}

.hero__actions .button--ghost:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.05);
    text-decoration-color: var(--gold);
}

.hero__quicklinks {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    margin-top: 0.45rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.hero__quicklink {
    display: inline-flex;
    align-items: center;
    gap: 0.42rem;
    min-height: 1.92rem;
    padding: 0.4rem 0.8rem;
    border-radius: 999px;
    background: transparent;
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.01em;
}

.hero__quicklink::before {
    content: "›";
    color: var(--gold);
    font-weight: 700;
    font-size: 0.95rem;
    line-height: 1;
}

.hero__quicklink:hover {
    background: rgba(255, 255, 255, 0.07);
    color: #fff;
}

.hero__quicklink:focus-visible {
    box-shadow: var(--focus-ring-light);
    outline: 0;
}

/* Hero aside / board */

.hero__board,
.hero__aside {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
    padding: 1rem;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.015)),
        rgba(3, 14, 26, 0.36);
    backdrop-filter: blur(14px) saturate(140%);
}

.hero-countdown {
    border-color: rgba(212, 173, 82, 0.32);
    border-radius: 14px;
    background:
        linear-gradient(135deg, rgba(212, 173, 82, 0.18), rgba(255, 255, 255, 0.04) 44%, rgba(2, 8, 15, 0.46));
}

.hero-countdown__eyebrow {
    color: var(--gold);
}

.hero-countdown__unit {
    border-radius: 12px;
    border-color: rgba(255, 255, 255, 0.1);
    background: rgba(1, 6, 11, 0.42);
}

.hero-countdown__unit strong {
    font-family: "Georgia", "Times New Roman", serif;
    font-variant-numeric: tabular-nums;
    font-feature-settings: "tnum";
}

.hero-feature {
    border-radius: 13px;
    padding: 0.95rem 1.05rem;
}

.hero-feature--primary {
    border-color: rgba(212, 173, 82, 0.28);
    background:
        linear-gradient(135deg, rgba(212, 173, 82, 0.1), transparent 60%),
        rgba(2, 8, 15, 0.46);
}

.hero-feature__label {
    color: var(--gold);
    letter-spacing: 0.16em;
    font-size: 0.66rem;
    font-weight: 800;
}

.hero-feature__title {
    color: #fff;
    font-size: 1.06rem;
    font-family: "Georgia", "Times New Roman", serif;
}

.hero-feature__title:hover {
    color: var(--gold);
}

.hero__spotlight {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.65rem;
}

.hero__board .key-list {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.55rem;
    margin-top: 0.2rem;
}

.hero__board .key-list > div {
    padding: 0.75rem 0.7rem;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(255, 255, 255, 0.04);
    text-align: start;
}

.hero__board .key-list strong {
    display: block;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.4rem;
    line-height: 1;
    margin-bottom: 0.25rem;
    color: #fff;
    font-variant-numeric: tabular-nums;
}

.hero__board .key-list span {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.68rem;
    font-weight: 600;
    line-height: 1.35;
}

/* --- Section spacing & content rhythm --------------------------------- */

.page-home .page-section,
.page-section {
    margin-top: var(--section-gap-1a);
}

.page-home .hero + .page-section,
.page-home .hero--command + .page-section {
    margin-top: clamp(1.6rem, 2.6vw, 2.4rem);
}

.section-shell {
    border: 1px solid var(--border-soft);
    border-radius: 18px;
    background: var(--paper-strong);
    padding: clamp(1.3rem, 2vw, 1.8rem);
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.6) inset, 0 14px 32px rgba(7, 24, 44, 0.07);
    overflow: hidden;
    position: relative;
}

.section-shell::before {
    content: "";
    position: absolute;
    inset: 0 0 auto;
    height: 2px;
    background: linear-gradient(90deg, transparent 0%, rgba(175, 23, 49, 0.38) 25%, rgba(212, 173, 82, 0.45) 50%, rgba(175, 23, 49, 0.38) 75%, transparent 100%);
    opacity: 0.45;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 1rem;
    margin-bottom: 1.3rem;
}

.section-header > div {
    display: flex;
    flex-direction: column;
    gap: 0.42rem;
    min-width: 0;
}

.section-header__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--brand);
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-size: 0.7rem;
    font-weight: 700;
}

.section-header__eyebrow::before {
    content: "";
    width: 1.2rem;
    height: 2px;
    border-radius: 2px;
    background: linear-gradient(90deg, var(--brand), var(--gold));
}

.section-header h2 {
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(1.55rem, 2.2vw, 2.2rem);
    line-height: 1.1;
    letter-spacing: -0.025em;
    color: var(--ink);
    margin: 0;
}

.section-header h3 {
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.18rem;
    color: var(--ink);
    margin: 0;
    line-height: 1.18;
    letter-spacing: -0.01em;
}

.section-header p {
    max-width: 46rem;
    margin: 0;
    color: var(--text-muted);
    font-size: 0.95rem;
    line-height: 1.55;
}

.section-link {
    display: inline-flex;
    align-items: center;
    gap: 0.34rem;
    color: var(--brand);
    font-weight: 700;
    font-size: 0.88rem;
    border-radius: 4px;
    padding: 0.2rem 0.32rem;
}

.section-link::after {
    content: "→";
    transition: transform 0.18s ease;
    color: var(--brand);
}

.section-link:hover {
    color: var(--brand-strong);
}

.section-link:hover::after {
    transform: translateX(3px);
}

.section-link:focus-visible {
    box-shadow: var(--focus-ring);
    outline: 0;
}

.split-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.32fr) minmax(320px, 0.9fr);
    gap: 1.25rem;
}

.card-grid,
.team-grid,
.city-grid,
.round-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
}

.page-home .card-grid,
.page-home .team-grid,
.page-home .city-grid,
.page-home .listing-grid {
    gap: 0.95rem;
}

.listing-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
}

/* --- Cards (off-white, refined, premium) ------------------------------ */

.panel,
.detail-shell,
.table-shell,
.entity-card,
.news-card,
.partner-card,
.match-card,
.list-card {
    border: 1px solid var(--border-soft);
    border-radius: 14px;
    background: var(--paper-strong);
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.65) inset, 0 12px 26px rgba(7, 24, 44, 0.06);
    transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
}

.page-home .match-card,
.page-home .news-card,
.page-home .partner-card,
.page-home .list-card,
.page-home .panel {
    border-radius: 14px;
}

.entity-card:hover,
.news-card:hover,
.partner-card:hover,
.match-card:hover,
.list-card:hover {
    transform: translateY(-3px);
    border-color: rgba(7, 24, 44, 0.18);
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.7) inset, 0 20px 38px rgba(7, 24, 44, 0.12);
}

/* Match card refinement */

.match-card {
    position: relative;
    overflow: hidden;
    padding: 1.05rem 1.15rem 1.1rem;
}

.page-home .match-card {
    padding: 1rem 1.1rem 1.05rem;
}

.match-card::before {
    content: "";
    position: absolute;
    inset: 0 auto 0 0;
    width: 3px;
    background: linear-gradient(180deg, var(--brand) 0%, var(--gold) 60%, var(--brand) 100%);
}

.match-card__head {
    display: flex;
    justify-content: space-between;
    gap: 0.85rem;
    align-items: flex-start;
}

.match-card__competition {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
}

.match-card__date {
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 600;
    text-align: end;
}

.match-card__teams {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
    align-items: center;
    gap: 0.95rem;
    margin-top: 0.95rem;
}

.match-card__team strong {
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.08rem;
    line-height: 1.2;
    color: var(--ink);
    letter-spacing: -0.005em;
}

.match-card__label {
    text-transform: uppercase;
    letter-spacing: 0.16em;
    color: var(--text-muted);
    font-size: 0.6rem;
    font-weight: 700;
}

.score-box {
    min-width: 96px;
    padding: 0.75rem 0.9rem;
    border-radius: 12px;
    border: 1px solid var(--border-soft);
    background:
        linear-gradient(135deg, rgba(7, 24, 44, 0.04), rgba(175, 23, 49, 0.05)),
        rgba(7, 24, 44, 0.03);
    text-align: center;
    color: var(--ink);
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    font-variant-numeric: tabular-nums;
}

.page-home .score-box {
    min-width: 86px;
    padding: 0.62rem 0.78rem;
    font-size: 1.32rem;
}

.score-box__sub {
    margin-top: 0.4rem;
    font-family: "Inter", "Segoe UI", sans-serif;
    font-size: 0.62rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-muted);
    font-weight: 700;
}

.match-card__meta {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.95rem;
    padding-top: 0.78rem;
    border-top: 1px solid var(--border-soft);
}

.match-card__cta {
    margin-inline-start: auto;
    min-height: 2.15rem;
    padding: 0.42rem 0.95rem;
    font-size: 0.78rem;
    font-weight: 700;
}

/* News card refinement */

.news-card__title {
    margin: 0.75rem 0 0;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.2rem;
    line-height: 1.22;
    color: var(--ink);
    letter-spacing: -0.005em;
}

.page-home .news-card__title { font-size: 1.1rem; }

.news-card__title a { color: inherit; transition: color 0.18s ease; }
.news-card__title a:hover { color: var(--brand); }

.news-card__summary {
    margin: 0.55rem 0 0;
    color: var(--text-muted);
    font-size: 0.92rem;
    line-height: 1.55;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-card__footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.95rem;
    padding-top: 0.78rem;
    border-top: 1px solid var(--border-soft);
    color: var(--brand);
    font-weight: 700;
    font-size: 0.86rem;
}

.news-card__footer::after {
    content: "→";
    color: var(--brand);
    transition: transform 0.18s ease;
}

.news-card:hover .news-card__footer::after { transform: translateX(3px); }

.news-card__body,
.entity-card__body,
.partner-card__body {
    padding: 1.05rem 1.15rem 1.2rem;
}

.page-home .news-card__body,
.page-home .partner-card__body,
.page-home .entity-card__body {
    padding: 1rem 1.05rem 1.1rem;
}

/* --- Badges & pills --------------------------------------------------- */

.badge,
.meta-pill,
.status-pill {
    display: inline-flex;
    align-items: center;
    min-height: 1.7rem;
    padding: 0.22rem 0.7rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    line-height: 1.1;
}

.badge {
    background: rgba(212, 173, 82, 0.16);
    color: var(--warning);
    border: 1px solid rgba(212, 173, 82, 0.32);
}

.meta-pill {
    background: rgba(7, 24, 44, 0.05);
    color: var(--text-soft);
    border: 1px solid var(--border-soft);
}

.meta-pill--soft {
    background: rgba(7, 24, 44, 0.04);
}

.status-pill {
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-size: 0.66rem;
    padding: 0.26rem 0.74rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
    background: linear-gradient(135deg, #2c4666, #1b3251);
}

.status-pill--completed {
    background: linear-gradient(135deg, var(--success), #0d5a3f);
}

.status-pill--live {
    background: linear-gradient(135deg, var(--brand-bright), var(--brand));
    padding-inline-start: 1.34rem;
    position: relative;
}

.status-pill--live::before {
    content: "";
    position: absolute;
    inset-inline-start: 0.6rem;
    top: 50%;
    width: 0.46rem;
    height: 0.46rem;
    margin-top: -0.23rem;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
    animation: pulseDot 1.6s infinite;
}

@keyframes pulseDot {
    0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
    70% { box-shadow: 0 0 0 6px rgba(255, 255, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
}

.status-pill--cancelled {
    background: linear-gradient(135deg, var(--danger), #6b1a2a);
}

/* --- Portal access (Start With The Essentials) ------------------------ */

.portal-access {
    display: grid;
    grid-template-columns: minmax(220px, 0.3fr) minmax(0, 1fr);
    gap: 1rem;
    align-items: stretch;
    margin-top: 0;
}

.portal-access__header {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 0.3rem;
    padding: 1.4rem 1.4rem;
    border-radius: 16px;
    background:
        radial-gradient(circle at 12% 90%, rgba(212, 173, 82, 0.16), transparent 50%),
        linear-gradient(135deg, #061629, #1f0510);
    color: #fff;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}

.portal-access__header::before {
    content: "";
    position: absolute;
    inset: 0 0 auto;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--gold), transparent);
    opacity: 0.7;
}

.portal-access__header span {
    color: var(--gold);
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-size: 0.68rem;
    font-weight: 800;
}

.portal-access__header strong {
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(1.2rem, 1.7vw, 1.45rem);
    line-height: 1.1;
    letter-spacing: -0.02em;
}

.portal-access__links {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.85rem;
}

.portal-access .quick-link-card,
.quick-link-card {
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 1.15rem 1.2rem 1.25rem;
    border-radius: 14px;
    border: 1px solid var(--border-soft);
    background: var(--paper-strong);
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.65) inset, 0 12px 26px rgba(7, 24, 44, 0.06);
    color: var(--ink);
    transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
}

.portal-access .quick-link-card::after,
.quick-link-card::after {
    content: "→";
    position: absolute;
    top: 1.15rem;
    inset-inline-end: 1.2rem;
    color: var(--brand);
    font-size: 1.05rem;
    font-weight: 700;
    opacity: 0.45;
    transition: transform 0.18s ease, opacity 0.18s ease;
}

.portal-access .quick-link-card::before,
.quick-link-card::before {
    content: "";
    position: absolute;
    inset: 0 0 auto;
    height: 2px;
    background: linear-gradient(90deg, var(--brand), var(--gold));
    opacity: 0;
    transition: opacity 0.18s ease;
}

.portal-access .quick-link-card strong,
.quick-link-card strong {
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.14rem;
    line-height: 1.2;
    letter-spacing: -0.005em;
    color: var(--ink);
}

.portal-access .quick-link-card p,
.quick-link-card p {
    margin: 0;
    color: var(--text-muted);
    font-size: 0.88rem;
    line-height: 1.5;
}

.portal-access .quick-link-card:hover,
.quick-link-card:hover {
    transform: translateY(-3px);
    border-color: rgba(7, 24, 44, 0.18);
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.7) inset, 0 22px 40px rgba(7, 24, 44, 0.12);
}

.portal-access .quick-link-card:hover::before,
.quick-link-card:hover::before {
    opacity: 1;
}

.portal-access .quick-link-card:hover::after,
.quick-link-card:hover::after {
    transform: translateX(3px);
    opacity: 1;
}

.portal-access .quick-link-card:focus-visible,
.quick-link-card:focus-visible {
    box-shadow: var(--focus-ring);
    outline: 0;
}

/* --- Public insights strip ------------------------------------------- */

.public-insights-strip {
    display: grid;
    grid-template-columns: minmax(200px, 0.24fr) minmax(0, 1fr);
    gap: 0;
    overflow: hidden;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.06);
    margin: 0;
    padding: 0;
    background:
        radial-gradient(circle at 8% 80%, rgba(212, 173, 82, 0.16), transparent 40%),
        linear-gradient(135deg, #04090f 0%, #061629 60%, #1f0510 100%);
    color: #fff;
    box-shadow: var(--shadow-xl);
}

.public-insights-strip__header {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 0.3rem;
    padding: 1.3rem 1.4rem;
    border-inline-end: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
}

.public-insights-strip__header::before {
    content: "";
    position: absolute;
    inset: 0 0 auto;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--gold), transparent);
    opacity: 0.6;
}

.public-insights-strip__header span {
    color: var(--gold);
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-size: 0.66rem;
    font-weight: 800;
}

.public-insights-strip__header strong {
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(1.15rem, 1.6vw, 1.4rem);
    line-height: 1.1;
    letter-spacing: -0.02em;
}

.public-insights-strip__grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 0;
}

.public-insight {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 0.2rem;
    padding: 1.25rem 1.2rem;
    border: 0;
    border-inline-end: 1px solid rgba(255, 255, 255, 0.06);
    background: transparent;
}

.public-insight:last-child {
    border-inline-end: 0;
}

.public-insight strong {
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(1.6rem, 2.4vw, 2.2rem);
    line-height: 1;
    color: #fff;
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.015em;
}

.public-insight span {
    color: var(--gold);
    text-transform: uppercase;
    letter-spacing: 0.14em;
    font-size: 0.66rem;
    font-weight: 800;
    margin-top: 0.05rem;
}

.public-insight small {
    color: rgba(255, 255, 255, 0.66);
    font-size: 0.78rem;
    line-height: 1.4;
}

/* --- Home map card ---------------------------------------------------- */

.home-map-card {
    border-radius: 18px;
    padding: clamp(1rem, 1.6vw, 1.4rem);
}

.home-map-card__intro h2 {
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(1.5rem, 2.2vw, 2rem);
    line-height: 1.08;
    letter-spacing: -0.025em;
}

.home-map-card__intro p {
    color: rgba(255, 255, 255, 0.76);
}

/* --- Empty state (refined) ------------------------------------------- */

.empty-state {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    gap: 1.1rem;
    align-items: center;
    padding: 1.15rem 1.25rem;
    border: 1px dashed rgba(7, 24, 44, 0.18);
    border-radius: 14px;
    background:
        linear-gradient(135deg, rgba(7, 24, 44, 0.02), rgba(175, 23, 49, 0.025) 60%, transparent),
        var(--paper-soft);
    color: var(--text-soft);
    box-shadow: none;
}

.empty-state__mark {
    display: inline-grid;
    place-items: center;
    width: 2.65rem;
    height: 2.65rem;
    border-radius: 12px;
    border: 1px solid var(--border-soft);
    background: linear-gradient(135deg, rgba(7, 24, 44, 0.06), rgba(175, 23, 49, 0.14));
    color: var(--brand);
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 0.76rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.empty-state strong {
    display: block;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.08rem;
    color: var(--ink);
    letter-spacing: -0.005em;
}

.empty-state p {
    margin: 0.32rem 0 0;
    color: var(--text-muted);
    line-height: 1.55;
}

/* --- Page header (inner pages) --------------------------------------- */

.page-header {
    position: relative;
    overflow: hidden;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.06);
    background:
        radial-gradient(circle at 88% 8%, rgba(212, 173, 82, 0.2), transparent 28%),
        radial-gradient(circle at 6% 90%, rgba(175, 23, 49, 0.24), transparent 32%),
        linear-gradient(135deg, #061629 0%, #0c2540 50%, #2c0a17 100%);
    color: #fff;
    box-shadow: var(--shadow-xl);
}

.page-header__content {
    position: relative;
    z-index: 1;
    max-width: 62rem;
    padding: clamp(2rem, 3.4vw, 3rem);
}

.page-header__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    color: var(--gold);
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-size: 0.72rem;
    font-weight: 700;
    margin: 0;
}

.page-header__eyebrow::before {
    content: "";
    width: 1.4rem;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--gold));
}

.page-header h1 {
    margin: 0.55rem 0 0;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(2.1rem, 3.8vw, 3.3rem);
    line-height: 1.06;
    letter-spacing: -0.035em;
    color: #fff;
}

.page-header p {
    max-width: 46rem;
    margin: 0.85rem 0 0;
    color: rgba(255, 255, 255, 0.84);
    font-size: clamp(0.96rem, 1.1vw, 1.08rem);
    line-height: 1.6;
}

.page-header__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.65rem;
    margin-top: 1.15rem;
}

.page-header .page-header__actions .button--subtle {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
    color: #fff;
    backdrop-filter: blur(8px);
}

.page-header .page-header__actions .button--subtle:hover {
    background: rgba(255, 255, 255, 0.16);
    border-color: rgba(255, 255, 255, 0.34);
    color: #fff;
}

/* --- Footer ---------------------------------------------------------- */

.site-footer {
    margin-top: clamp(2.8rem, 5vw, 4.4rem);
    padding: 0 0 2rem;
}

.site-footer__inner {
    position: relative;
    overflow: hidden;
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) repeat(2, minmax(170px, 0.75fr)) minmax(220px, 0.95fr);
    gap: 1.6rem;
    padding: clamp(1.75rem, 2.6vw, 2.4rem);
    border-radius: 20px;
    border: 1px solid rgba(212, 173, 82, 0.16);
    background:
        radial-gradient(circle at 92% 0%, rgba(175, 23, 49, 0.18), transparent 24%),
        linear-gradient(135deg, #04090f, #061629 60%, #2c0a17);
    color: #fff;
    box-shadow: var(--shadow-xl);
}

.site-footer__inner::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(212, 173, 82, 0.5), transparent);
    pointer-events: none;
}

.site-footer__brand-lockup {
    display: inline-flex;
    align-items: center;
    gap: 0.78rem;
}

.site-footer__mark {
    width: 2.45rem;
    height: 2.45rem;
    border-radius: 10px;
    background: linear-gradient(145deg, var(--brand-bright), var(--brand) 58%, var(--burgundy));
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.28),
        0 8px 22px rgba(175, 23, 49, 0.3);
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 0.88rem;
    font-weight: 800;
    letter-spacing: 0.05em;
}

.site-footer__brand strong {
    font-family: "Georgia", "Times New Roman", serif;
    font-size: 1.34rem;
    letter-spacing: -0.005em;
}

.site-footer__brand p,
.site-footer__note p {
    margin: 0.78rem 0 0;
    color: rgba(255, 255, 255, 0.75);
    font-size: 0.92rem;
    line-height: 1.6;
}

.site-footer__label {
    color: var(--gold);
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-size: 0.68rem;
    font-weight: 800;
    margin-bottom: 0.25rem;
}

.site-footer__links a {
    color: rgba(255, 255, 255, 0.86);
    font-size: 0.92rem;
    font-weight: 600;
    display: inline-block;
    padding: 0.18rem 0;
    transition: color 0.18s ease, transform 0.18s ease;
}

.site-footer__links a:hover {
    color: var(--gold);
    transform: translateX(2px);
}

.site-footer__links a:focus-visible {
    box-shadow: var(--focus-ring-light);
    border-radius: 4px;
    outline: 0;
}

/* --- Shell popovers (search/language/account/menu) -------------------- */

.shell-panel {
    border: 1px solid var(--border-soft);
    border-radius: 14px;
    background: var(--paper-strong);
    box-shadow: var(--shadow-lg);
    padding: 1rem;
}

.shell-panel__label {
    color: var(--brand);
    text-transform: uppercase;
    letter-spacing: 0.16em;
    font-size: 0.68rem;
    font-weight: 800;
}

.shell-menu-link,
.shell-language-list__item,
.shell-drawer__nav a,
.shell-drawer__actions a {
    border-radius: 10px;
    transition: background-color 0.18s ease, color 0.18s ease;
}

.shell-menu-link:hover,
.shell-language-list__item:hover,
.shell-drawer__nav a:hover,
.shell-drawer__actions a:hover {
    background: rgba(175, 23, 49, 0.08);
    color: var(--brand);
}

.shell-menu-link--primary {
    background: linear-gradient(140deg, var(--brand-bright), var(--brand) 55%, var(--burgundy)) !important;
    color: #fff !important;
    box-shadow: 0 8px 18px rgba(175, 23, 49, 0.28);
}

.shell-menu-link--primary:hover {
    color: #fff !important;
    transform: translateY(-1px);
}

/* --- Responsive: tablet & mobile refinements ------------------------- */

@media (max-width: 1180px) {
    .hero,
    .hero--command {
        grid-template-columns: 1fr;
        gap: 1.6rem;
    }

    .hero__main {
        max-width: none;
    }

    .public-insights-strip {
        grid-template-columns: 1fr;
    }

    .public-insights-strip__header {
        border-inline-end: 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .public-insights-strip__grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .public-insight:nth-child(3) {
        border-inline-end: 0;
    }

    .public-insight {
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .portal-access {
        grid-template-columns: 1fr;
    }

    .portal-access__links {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .card-grid,
    .team-grid,
    .city-grid,
    .round-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .listing-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .site-footer__inner {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 860px) {
    main {
        padding: 0.85rem 0 3.6rem;
    }

    .page-container,
    .site-header__inner,
    .site-footer__inner {
        width: min(calc(100% - 24px), var(--container-1a));
    }

    .site-header__inner {
        grid-template-columns: auto auto;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .site-nav {
        display: none;
    }

    .site-header__audience-tools,
    .site-header__utility-tools {
        gap: 0.32rem;
    }

    .hero h1 {
        font-size: clamp(2.4rem, 11vw, 3.6rem);
        max-width: none;
    }

    .hero p {
        font-size: 1rem;
    }

    .hero__actions {
        gap: 0.55rem;
    }

    .hero__actions .button,
    .hero__actions .button--subtle {
        min-height: 2.65rem;
        padding-inline: 1.1rem;
    }

    .hero__board .key-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .card-grid,
    .team-grid,
    .city-grid,
    .round-grid,
    .listing-grid,
    .portal-access__links {
        grid-template-columns: 1fr;
    }

    .public-insights-strip__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .public-insight {
        border-inline-end: 0;
    }

    .public-insight:nth-child(odd) {
        border-inline-end: 1px solid rgba(255, 255, 255, 0.06);
    }

    .site-footer__inner {
        grid-template-columns: 1fr;
        text-align: start;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .match-card__teams {
        gap: 0.7rem;
    }

    .empty-state {
        grid-template-columns: 1fr;
        gap: 0.7rem;
        text-align: start;
    }
}

@media (max-width: 540px) {
    .hero,
    .hero--command {
        padding: 1.4rem 1.15rem;
        border-radius: 16px;
    }

    .page-header__content {
        padding: 1.6rem 1.2rem;
    }

    .hero__actions .button,
    .hero__actions .button--subtle,
    .hero__actions .button--ghost {
        width: 100%;
        justify-content: center;
    }

    .hero-countdown__grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.36rem;
    }

    .hero__board .key-list {
        grid-template-columns: 1fr;
    }

    .match-card__teams {
        grid-template-columns: 1fr;
        text-align: start;
    }

    .match-card__team--away {
        text-align: start;
    }

    .score-box {
        justify-self: start;
    }
}

/* --- Reduced motion respect ------------------------------------------ */

@media (prefers-reduced-motion: reduce) {
    .status-pill--live::before {
        animation: none;
    }
}

/* --- Print -------------------------------------------------------- */

@media print {
    .site-header,
    .site-footer,
    .hero__quicklinks,
    .shell-popovers {
        display: none !important;
    }

    body {
        background: #fff !important;
        color: #000 !important;
    }
}

[dir="rtl"] body,
[dir="rtl"] .site-header,
[dir="rtl"] .site-footer,
[dir="rtl"] .page-container,
[dir="rtl"] .shell-panel,
[dir="rtl"] .page-header,
[dir="rtl"] .panel,
[dir="rtl"] .section-shell,
[dir="rtl"] .auth-card,
[dir="rtl"] .account-shell,
[dir="rtl"] .search-shell,
[dir="rtl"] .map-shell {
    text-align: right;
}

[dir="rtl"] .site-header__inner,
[dir="rtl"] .site-footer__inner,
[dir="rtl"] .section-header,
[dir="rtl"] .hero__actions,
[dir="rtl"] .hero__quicklinks,
[dir="rtl"] .portal-access__links,
[dir="rtl"] .form-actions,
[dir="rtl"] .match-card__head,
[dir="rtl"] .match-card__meta,
[dir="rtl"] .map-location-card__header,
[dir="rtl"] .shell-search-form__row,
[dir="rtl"] .shell-drawer__head {
    direction: rtl;
}

[dir="rtl"] .site-nav,
[dir="rtl"] .site-header__utility-tools,
[dir="rtl"] .shell-language-list,
[dir="rtl"] .shell-drawer__nav,
[dir="rtl"] .shell-drawer__actions {
    direction: rtl;
}

[dir="rtl"] .brand,
[dir="rtl"] .site-footer__brand-lockup,
[dir="rtl"] .hero-countdown__grid,
[dir="rtl"] .key-list,
[dir="rtl"] .stats-grid,
[dir="rtl"] .map-overview,
[dir="rtl"] .account-nav,
[dir="rtl"] .auth-note__list li {
    direction: rtl;
}

[dir="rtl"] .score-box,
[dir="rtl"] .match-card__score,
[dir="rtl"] .standings-table table,
[dir="rtl"] .hero-countdown__unit strong,
[dir="rtl"] [data-countdown-unit] {
    direction: ltr;
}

[dir="rtl"] input,
[dir="rtl"] textarea,
[dir="rtl"] select {
    text-align: right;
}

[dir="rtl"] input[type="email"],
[dir="rtl"] input[type="url"],
[dir="rtl"] input[type="password"],
[dir="rtl"] input[type="number"] {
    direction: ltr;
    text-align: left;
}

[dir="rtl"] .shell-panel--drawer {
    right: auto;
    left: 0;
}

[dir="rtl"] .map-canvas,
[dir="rtl"] .home-map-card__visual {
    direction: ltr;
}

/* --- PHASE 1A RTL refinements --------------------------------------- */

[dir="rtl"] .hero__eyebrow::before,
[dir="rtl"] .page-header__eyebrow::before,
[dir="rtl"] .section-header__eyebrow::before {
    background: linear-gradient(270deg, transparent, var(--gold));
}

[dir="rtl"] .section-link::after {
    content: "←";
}

[dir="rtl"] .section-link:hover::after {
    transform: translateX(-3px);
}

[dir="rtl"] .news-card__footer::after {
    content: "←";
}

[dir="rtl"] .news-card:hover .news-card__footer::after {
    transform: translateX(-3px);
}

[dir="rtl"] .quick-link-card::after,
[dir="rtl"] .portal-access .quick-link-card::after {
    content: "←";
}

[dir="rtl"] .quick-link-card:hover::after,
[dir="rtl"] .portal-access .quick-link-card:hover::after {
    transform: translateX(-3px);
}

[dir="rtl"] .hero__quicklink::before {
    content: "‹";
}

[dir="rtl"] .site-nav a::after {
    background-image: linear-gradient(270deg, var(--gold), var(--brand-bright));
}

/* ==========================================================================
   PHASE 1B - Moroccan Premium Public Identity layer.
   Premium official tournament feel with restrained Moroccan cues.
   Stronger CTA visibility for sign-in and registration entry points.
   Circular-logo-ready brand mark (text fallback now, image later).
   Subtle zellige and ornamental accents, CSS-only, a11y safe.
   Tighter typography hierarchy, premium spacing, clearer focus states.
   Cleaner header structure with visible auth controls on desktop.
   Better hero composition with cultural emblem feel.
   Auth pages polished with restrained cultural signature.
   ========================================================================== */

/* --- Extended design tokens (Moroccan identity overlay) --------------- */

:root {
    --m-navy: #061629;
    --m-navy-soft: #0b2545;
    --m-navy-strong: #03101e;
    --m-burgundy: #5c0f1e;
    --m-red: #af1731;
    --m-red-bright: #d22a44;
    --m-green: #0d6b45;
    --m-gold: #d4ad52;
    --m-gold-strong: #b88a2a;
    --m-gold-pale: rgba(212, 173, 82, 0.18);
    --m-paper: #fbfbf7;
    --m-paper-warm: #f5efe1;
    --m-ink: #07182c;
    --m-shadow-hero: 0 30px 80px rgba(6, 22, 41, 0.34), 0 8px 22px rgba(92, 15, 30, 0.22);
    --m-shadow-card: 0 1px 0 rgba(255, 255, 255, 0.55) inset, 0 18px 38px rgba(6, 22, 41, 0.08);
    --m-shadow-cta: 0 14px 28px rgba(175, 23, 49, 0.35), 0 4px 10px rgba(92, 15, 30, 0.3);
}

/* --- Zellige-inspired subtle pattern utility -------------------------- */

.m-zellige-bg {
    background-image:
        radial-gradient(circle at 25% 25%, rgba(212, 173, 82, 0.045) 0 1.5px, transparent 1.6px),
        radial-gradient(circle at 75% 75%, rgba(212, 173, 82, 0.04) 0 1.5px, transparent 1.6px),
        radial-gradient(circle at 50% 50%, rgba(212, 173, 82, 0.025) 0 1px, transparent 1.1px);
    background-size: 32px 32px, 32px 32px, 16px 16px;
    background-position: 0 0, 16px 16px, 0 0;
}

.m-ornamental-divider {
    position: relative;
    height: 1.4rem;
    text-align: center;
    margin: 1.4rem 0;
    overflow: hidden;
}

.m-ornamental-divider::before,
.m-ornamental-divider::after {
    content: "";
    position: absolute;
    top: 50%;
    width: calc(50% - 1.6rem);
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(212, 173, 82, 0.45), transparent);
}

.m-ornamental-divider::before { left: 0; }
.m-ornamental-divider::after { right: 0; }

.m-ornamental-divider__mark {
    position: relative;
    display: inline-block;
    width: 0.6rem;
    height: 0.6rem;
    margin-top: 0.4rem;
    transform: rotate(45deg);
    background: linear-gradient(135deg, var(--m-gold), var(--m-gold-strong));
    box-shadow: 0 0 0 4px rgba(212, 173, 82, 0.12);
}

/* --- Premium header: cleaner structure, sticky depth ------------------ */

.site-header {
    background:
        linear-gradient(180deg, rgba(3, 16, 30, 0.96) 0%, rgba(6, 22, 41, 0.94) 60%, rgba(11, 37, 69, 0.92) 100%);
    border-bottom: 1px solid rgba(212, 173, 82, 0.16);
    box-shadow: 0 14px 38px rgba(3, 16, 30, 0.45);
    backdrop-filter: saturate(140%) blur(10px);
    -webkit-backdrop-filter: saturate(140%) blur(10px);
}

.site-header::before {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 2px;
    background: linear-gradient(90deg, var(--m-red) 0%, var(--m-gold) 35%, var(--m-green) 70%, var(--m-red) 100%);
    background-size: 200% 100%;
    opacity: 0.85;
    pointer-events: none;
}

.site-header::after {
    content: "";
    position: absolute;
    inset: auto 0 0 0;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, rgba(212, 173, 82, 0.55) 50%, transparent 100%);
    pointer-events: none;
}

.site-header__inner {
    gap: 1.2rem;
    padding-top: 0.55rem;
    padding-bottom: 0.55rem;
}

/* --- Brand mark prepared for circular logo image ---------------------- */

.brand {
    gap: 0.85rem;
    padding: 0.15rem 0.25rem;
}

.brand__mark {
    position: relative;
    width: 2.8rem;
    height: 2.8rem;
    border-radius: 999px;
    aspect-ratio: 1;
    overflow: hidden;
    display: grid;
    place-items: center;
    background:
        radial-gradient(circle at 30% 30%, rgba(212, 173, 82, 0.55), transparent 55%),
        linear-gradient(140deg, var(--m-red-bright) 0%, var(--m-red) 55%, var(--m-burgundy) 100%);
    color: #fff;
    font-family: "Cormorant Garamond", "Playfair Display", "Georgia", "Times New Roman", serif;
    font-weight: 700;
    font-size: 1rem;
    letter-spacing: 0.04em;
    box-shadow:
        inset 0 0 0 2px rgba(255, 255, 255, 0.12),
        inset 0 0 0 4px rgba(212, 173, 82, 0.55),
        inset 0 0 0 5px rgba(255, 255, 255, 0.08),
        0 8px 22px rgba(175, 23, 49, 0.4),
        0 2px 4px rgba(92, 15, 30, 0.4);
    transition: transform 0.22s ease, box-shadow 0.22s ease;
}

.brand__mark::after {
    content: none;
}

.brand__mark img,
.brand__mark svg.brand-logo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 999px;
    display: block;
}

.brand:hover .brand__mark,
.brand:focus-visible .brand__mark {
    transform: translateY(-1px);
    box-shadow:
        inset 0 0 0 2px rgba(255, 255, 255, 0.16),
        inset 0 0 0 4px rgba(212, 173, 82, 0.7),
        inset 0 0 0 5px rgba(255, 255, 255, 0.1),
        0 12px 30px rgba(175, 23, 49, 0.5),
        0 4px 8px rgba(92, 15, 30, 0.45);
}

.brand__meta {
    display: inline-flex;
    flex-direction: column;
    gap: 0.05rem;
    font-family: "Cormorant Garamond", "Playfair Display", "Georgia", serif;
    font-size: 1.18rem;
    font-weight: 600;
    color: #fff;
    letter-spacing: 0.01em;
    line-height: 1.05;
}

.brand__meta small {
    font-family: "Inter", system-ui, sans-serif;
    font-weight: 600;
    font-size: 0.62rem;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(212, 173, 82, 0.92);
    margin-top: 0.12rem;
}

/* --- Site nav: stronger readability ----------------------------------- */

.site-nav.site-nav--desktop {
    gap: 0.05rem;
    flex-wrap: nowrap;
}

.site-nav a {
    color: rgba(255, 255, 255, 0.78);
    padding: 0.5rem 0.85rem;
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: 0.045em;
    text-transform: uppercase;
    border-radius: 8px;
}

.site-nav a:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.06);
}

.site-nav a.is-active {
    color: #fff;
    background: rgba(212, 173, 82, 0.1);
}

.site-nav a.is-active::after {
    bottom: -0.42rem;
    height: 2px;
    background: linear-gradient(90deg, var(--m-gold), var(--m-red-bright));
}

/* --- Header utility tools cluster (icons + visible auth buttons) ------ */

.site-header__utility-tools {
    gap: 0.35rem;
    align-items: center;
}

.site-header__divider {
    width: 1px;
    height: 1.6rem;
    background: linear-gradient(180deg, transparent, rgba(255, 255, 255, 0.16), transparent);
    margin: 0 0.2rem;
}

.shell-icon-button {
    width: 2.3rem;
    height: 2.3rem;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.14);
    background: rgba(255, 255, 255, 0.04);
    color: rgba(255, 255, 255, 0.92);
}

.shell-icon-button:hover {
    background: rgba(212, 173, 82, 0.18);
    border-color: rgba(212, 173, 82, 0.5);
    color: #fff;
}

.shell-icon-button[aria-expanded="true"],
.shell-icon-button.is-active {
    background: rgba(212, 173, 82, 0.22);
    border-color: rgba(212, 173, 82, 0.65);
}

.shell-icon-button--language {
    width: auto;
    min-width: 2.6rem;
    padding: 0 0.6rem;
    gap: 0.35rem;
}

.shell-current-language {
    font-size: 0.66rem;
    font-weight: 800;
    letter-spacing: 0.08em;
}

/* Visible auth buttons in header */

.header-auth {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-inline-start: 0.15rem;
}

.header-auth__link {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    height: 2.3rem;
    padding: 0 0.95rem;
    border-radius: 999px;
    font-family: inherit;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    border: 1px solid transparent;
    transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
    white-space: nowrap;
}

.header-auth__link--ghost {
    color: rgba(255, 255, 255, 0.92);
    background: transparent;
    border-color: rgba(255, 255, 255, 0.2);
}

.header-auth__link--ghost:hover,
.header-auth__link--ghost:focus-visible {
    color: #fff;
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(212, 173, 82, 0.5);
    transform: translateY(-1px);
}

.header-auth__link--primary {
    color: #fff;
    background: linear-gradient(135deg, var(--m-red-bright) 0%, var(--m-red) 55%, var(--m-burgundy) 100%);
    border-color: rgba(212, 173, 82, 0.6);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.18),
        0 10px 22px rgba(175, 23, 49, 0.35),
        0 2px 4px rgba(92, 15, 30, 0.4);
}

.header-auth__link--primary:hover,
.header-auth__link--primary:focus-visible {
    transform: translateY(-1px);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.22),
        0 14px 28px rgba(175, 23, 49, 0.45),
        0 4px 8px rgba(92, 15, 30, 0.45);
}

.header-auth__link:focus-visible {
    outline: 0;
    box-shadow: var(--focus-ring-light);
}

.header-auth__account {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    height: 2.3rem;
    padding: 0 0.85rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.16);
    color: #fff;
    font-size: 0.78rem;
    font-weight: 600;
}

.header-auth__account-mark {
    width: 1.7rem;
    height: 1.7rem;
    border-radius: 999px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, var(--m-gold) 0%, var(--m-red) 100%);
    color: #fff;
    font-size: 0.7rem;
    font-weight: 800;
}

/* --- Mobile responsiveness for new header items ----------------------- */

@media (max-width: 1180px) {
    .site-nav.site-nav--desktop a {
        padding: 0.45rem 0.6rem;
        font-size: 0.72rem;
    }
}

@media (max-width: 1080px) {
    .header-auth__link {
        padding: 0 0.75rem;
        font-size: 0.72rem;
    }
}

@media (max-width: 960px) {
    .header-auth {
        display: none;
    }
    .site-header__divider {
        display: none;
    }
}

@media (max-width: 880px) {
    .site-nav.site-nav--desktop {
        display: none;
    }
    .shell-icon-button--language {
        display: none;
    }
    .header-auth__account span:not(.header-auth__account-mark) {
        display: none;
    }
    .header-auth__account {
        padding: 0;
        width: 2.3rem;
        height: 2.3rem;
        justify-content: center;
    }
    .header-auth__account-mark {
        width: 100%;
        height: 100%;
        border-radius: 999px;
    }
}

@media (max-width: 640px) {
    .brand__mark {
        width: 2.4rem;
        height: 2.4rem;
        font-size: 0.85rem;
    }
    .brand__meta {
        font-size: 1rem;
    }
    .brand__meta small {
        font-size: 0.55rem;
        letter-spacing: 0.18em;
    }
}

/* --- Hero: official, Moroccan, cinematic ----------------------------- */

.hero {
    position: relative;
    overflow: hidden;
    background:
        radial-gradient(ellipse 70% 60% at 80% 0%, rgba(212, 173, 82, 0.18), transparent 60%),
        radial-gradient(ellipse 60% 50% at 0% 100%, rgba(175, 23, 49, 0.35), transparent 60%),
        linear-gradient(135deg, var(--m-navy-strong) 0%, var(--m-navy) 50%, var(--m-navy-soft) 100%);
    border: 1px solid rgba(212, 173, 82, 0.2);
    box-shadow: var(--m-shadow-hero);
    border-radius: 28px;
    padding: clamp(2rem, 4vw, 3.4rem);
}

.hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 25% 25%, rgba(212, 173, 82, 0.08) 0 1.6px, transparent 1.8px),
        radial-gradient(circle at 75% 75%, rgba(212, 173, 82, 0.07) 0 1.6px, transparent 1.8px);
    background-size: 36px 36px, 36px 36px;
    background-position: 0 0, 18px 18px;
    opacity: 0.55;
    pointer-events: none;
}

.hero::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 60%, rgba(3, 16, 30, 0.35) 100%);
    pointer-events: none;
}

.hero__main {
    position: relative;
    z-index: 1;
}

.hero__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.7rem;
    padding: 0.32rem 0.85rem 0.32rem 0.55rem;
    border-radius: 999px;
    background: rgba(212, 173, 82, 0.12);
    border: 1px solid rgba(212, 173, 82, 0.4);
    color: var(--m-gold);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
}

.hero__eyebrow::before {
    content: "";
    width: 0.45rem;
    height: 0.45rem;
    transform: rotate(45deg);
    background: linear-gradient(135deg, var(--m-gold), var(--m-gold-strong));
    box-shadow: 0 0 0 3px rgba(212, 173, 82, 0.18);
    margin: 0;
}

.hero h1,
.hero__main h1,
#hero-title {
    font-family: "Cormorant Garamond", "Playfair Display", "Georgia", "Times New Roman", serif;
    font-weight: 700;
    line-height: 1.02;
    letter-spacing: -0.005em;
    color: #fff;
    font-size: clamp(2.6rem, 5.5vw, 4.4rem);
    margin: 1rem 0 0.7rem;
}

.hero h1 span,
#hero-title span {
    display: block;
    background: linear-gradient(135deg, var(--m-gold) 0%, #f0d27a 50%, var(--m-gold-strong) 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    font-style: italic;
    font-size: 0.55em;
    font-weight: 500;
    letter-spacing: 0.02em;
    margin-top: 0.3rem;
}

.hero__main p {
    max-width: 56ch;
    font-size: 1.06rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.82);
    margin: 0 0 1.6rem;
}

.hero__actions {
    margin-top: 1.7rem;
    gap: 0.7rem;
    flex-wrap: wrap;
}

.hero__actions .button {
    background: linear-gradient(135deg, var(--m-red-bright) 0%, var(--m-red) 55%, var(--m-burgundy) 100%);
    border: 1px solid rgba(212, 173, 82, 0.65);
    color: #fff;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-size: 0.78rem;
    padding: 0.95rem 1.6rem;
    min-height: 3rem;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.18),
        0 14px 28px rgba(175, 23, 49, 0.45),
        0 4px 10px rgba(92, 15, 30, 0.4);
}

.hero__actions .button:hover,
.hero__actions .button:focus-visible {
    transform: translateY(-2px);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.22),
        0 18px 38px rgba(175, 23, 49, 0.55),
        0 6px 14px rgba(92, 15, 30, 0.5);
}

.hero__actions .button--subtle {
    color: #fff;
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.24);
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-size: 0.78rem;
    padding: 0.95rem 1.6rem;
    min-height: 3rem;
    backdrop-filter: blur(8px);
}

.hero__actions .button--subtle:hover,
.hero__actions .button--subtle:focus-visible {
    background: rgba(255, 255, 255, 0.14);
    border-color: rgba(212, 173, 82, 0.45);
    color: #fff;
    transform: translateY(-2px);
}

.hero__actions .button--ghost {
    color: rgba(255, 255, 255, 0.92);
    background: transparent;
    border: 1px solid transparent;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 0.95rem 1.1rem;
    min-height: 3rem;
}

.hero__actions .button--ghost::after {
    content: "→";
    font-size: 1rem;
    color: var(--m-gold);
    transition: transform 0.18s ease;
}

.hero__actions .button--ghost:hover,
.hero__actions .button--ghost:focus-visible {
    color: #fff;
    background: rgba(255, 255, 255, 0.04);
}

.hero__actions .button--ghost:hover::after,
.hero__actions .button--ghost:focus-visible::after {
    transform: translateX(3px);
}

.hero__quicklinks {
    margin-top: 1.6rem;
    gap: 0.55rem;
    flex-wrap: wrap;
    padding-top: 1.2rem;
    border-top: 1px solid rgba(212, 173, 82, 0.18);
}

.hero__quicklink {
    color: rgba(255, 255, 255, 0.82);
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.14);
    padding: 0.45rem 0.85rem;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.hero__quicklink:hover,
.hero__quicklink:focus-visible {
    color: #fff;
    background: rgba(212, 173, 82, 0.15);
    border-color: rgba(212, 173, 82, 0.4);
}

.hero__quicklink--accent {
    background: linear-gradient(135deg, rgba(212, 173, 82, 0.22), rgba(175, 23, 49, 0.2));
    border-color: rgba(212, 173, 82, 0.5);
    color: #fff;
}

.hero__quicklink--accent:hover,
.hero__quicklink--accent:focus-visible {
    background: linear-gradient(135deg, rgba(212, 173, 82, 0.35), rgba(175, 23, 49, 0.32));
    border-color: rgba(212, 173, 82, 0.7);
}

/* Hero aside / board polish */

.hero__board,
.hero__aside {
    position: relative;
    z-index: 1;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(212, 173, 82, 0.22);
    border-radius: 22px;
    padding: 1.4rem;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.06),
        0 18px 38px rgba(3, 16, 30, 0.34);
}

.hero-countdown {
    background: rgba(3, 16, 30, 0.4);
    border: 1px solid rgba(212, 173, 82, 0.3);
    border-radius: 16px;
    padding: 1.1rem 1.15rem 1.25rem;
}

.hero-countdown__eyebrow {
    color: var(--m-gold);
    font-size: 0.66rem;
    font-weight: 800;
    letter-spacing: 0.2em;
    text-transform: uppercase;
}

.hero-countdown__header strong {
    color: #fff;
    font-family: "Cormorant Garamond", "Playfair Display", "Georgia", serif;
    font-size: 1.3rem;
    font-weight: 700;
    line-height: 1.05;
    margin: 0.3rem 0 0.3rem;
    display: block;
}

.hero-countdown__header p {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.78rem;
    margin: 0;
}

.hero-countdown__grid {
    margin-top: 1.05rem;
    gap: 0.5rem;
}

.hero-countdown__unit {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(212, 173, 82, 0.28);
    border-radius: 12px;
    padding: 0.7rem 0.4rem 0.55rem;
    text-align: center;
}

.hero-countdown__unit strong {
    color: var(--m-gold);
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: 1.55rem;
    font-weight: 700;
    line-height: 1;
    display: block;
}

.hero-countdown__unit span {
    color: rgba(255, 255, 255, 0.62);
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    margin-top: 0.4rem;
    display: block;
}

.hero-countdown__status {
    margin-top: 0.95rem;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.78rem;
    text-align: center;
    padding-top: 0.7rem;
    border-top: 1px solid rgba(212, 173, 82, 0.18);
}

.hero-feature {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 14px;
    padding: 1rem;
}

.hero-feature--primary {
    border-color: rgba(212, 173, 82, 0.4);
    background: linear-gradient(135deg, rgba(212, 173, 82, 0.08), rgba(175, 23, 49, 0.08));
}

.hero-feature__label {
    color: var(--m-gold);
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.18em;
    text-transform: uppercase;
}

.hero-feature__title {
    color: #fff;
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: 1.18rem;
    font-weight: 700;
    line-height: 1.1;
    display: block;
    margin: 0.4rem 0 0.5rem;
    text-decoration: none;
    transition: color 0.18s ease;
}

.hero-feature__title:hover {
    color: var(--m-gold);
}

.hero-feature__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem 0.9rem;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.76rem;
}

.key-list {
    margin-top: 0.95rem;
    gap: 0.5rem;
}

.key-list > div {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 0.75rem 0.85rem;
}

.key-list > div strong {
    color: #fff;
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: 1.45rem;
    font-weight: 700;
    line-height: 1;
}

.key-list > div span {
    color: rgba(255, 255, 255, 0.62);
    font-size: 0.72rem;
    margin-top: 0.3rem;
    display: block;
    line-height: 1.35;
}

/* --- Section shells & cards (off-white "paper" identity) ------------- */

.section-shell {
    background: var(--m-paper);
    border: 1px solid rgba(7, 24, 44, 0.08);
    border-radius: 24px;
    padding: clamp(1.6rem, 2.4vw, 2.3rem);
    box-shadow: var(--m-shadow-card);
    position: relative;
}

.section-shell::before {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 3px;
    border-radius: 24px 24px 0 0;
    background: linear-gradient(90deg, transparent 0%, rgba(212, 173, 82, 0.45) 50%, transparent 100%);
    pointer-events: none;
}

.section-header__eyebrow {
    color: var(--m-red);
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.2em;
    text-transform: uppercase;
}

.section-header h2 {
    font-family: "Cormorant Garamond", "Playfair Display", "Georgia", serif;
    font-size: clamp(1.5rem, 2.6vw, 2rem);
    font-weight: 700;
    line-height: 1.1;
    color: var(--m-ink);
    margin: 0.5rem 0 0.5rem;
}

.section-header h3 {
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-weight: 700;
    color: var(--m-ink);
}

.section-header p {
    color: #4a5b75;
    font-size: 0.95rem;
    max-width: 60ch;
}

.match-card,
.news-card,
.quick-link-card,
.panel,
.partner-card {
    background: #fff;
    border: 1px solid rgba(7, 24, 44, 0.07);
    border-radius: 18px;
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.65) inset, 0 12px 30px rgba(6, 22, 41, 0.06);
    transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
}

.match-card:hover,
.news-card:hover,
.quick-link-card:hover,
.partner-card:hover {
    transform: translateY(-3px);
    border-color: rgba(212, 173, 82, 0.4);
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.65) inset, 0 22px 44px rgba(6, 22, 41, 0.1);
}

.match-card {
    position: relative;
    padding: 1.15rem 1.2rem 1rem 1.4rem;
    overflow: hidden;
}

.match-card::before {
    content: "";
    position: absolute;
    inset: 0 auto 0 0;
    width: 4px;
    background: linear-gradient(180deg, var(--m-gold) 0%, var(--m-red) 100%);
}

[dir="rtl"] .match-card {
    padding: 1.15rem 1.4rem 1rem 1.2rem;
}

[dir="rtl"] .match-card::before {
    inset: 0 0 0 auto;
}

.match-card__competition {
    gap: 0.5rem;
    flex-wrap: wrap;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.32rem 0.65rem;
    border-radius: 999px;
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.status-pill--scheduled {
    background: var(--m-navy);
    color: #fff;
    border: 1px solid rgba(212, 173, 82, 0.45);
}

.status-pill--live {
    background: linear-gradient(135deg, var(--m-red-bright), var(--m-red));
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 6px 14px rgba(175, 23, 49, 0.4);
}

.status-pill--live::before {
    content: "";
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 999px;
    background: #fff;
    box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.6);
    animation: m-live-pulse 1.6s ease-out infinite;
}

@keyframes m-live-pulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
    70% { box-shadow: 0 0 0 8px rgba(255, 255, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
}

.status-pill--completed {
    background: rgba(23, 118, 84, 0.14);
    color: var(--m-green);
    border: 1px solid rgba(23, 118, 84, 0.34);
}

.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.32rem 0.7rem;
    border-radius: 999px;
    background: rgba(212, 173, 82, 0.13);
    border: 1px solid rgba(212, 173, 82, 0.35);
    color: var(--m-gold-strong);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.08em;
}

.meta-pill,
.meta-pill--soft {
    display: inline-flex;
    align-items: center;
    padding: 0.3rem 0.7rem;
    border-radius: 999px;
    background: rgba(7, 24, 44, 0.05);
    border: 1px solid rgba(7, 24, 44, 0.1);
    color: var(--m-ink);
    font-size: 0.74rem;
    font-weight: 600;
}

.match-card__teams {
    margin-top: 0.85rem;
    align-items: center;
}

.match-card__label {
    color: #6b7a93;
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
}

.match-card__team strong {
    font-family: "Cormorant Garamond", "Playfair Display", "Georgia", serif;
    font-size: 1.45rem;
    font-weight: 700;
    color: var(--m-ink);
    line-height: 1.05;
    display: block;
    margin-top: 0.2rem;
}

.score-box {
    background: linear-gradient(135deg, rgba(212, 173, 82, 0.1), rgba(7, 24, 44, 0.04));
    border: 1px solid rgba(212, 173, 82, 0.32);
    border-radius: 12px;
    padding: 0.7rem 0.95rem;
    color: var(--m-ink);
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: 1.55rem;
    font-weight: 700;
    text-align: center;
    min-width: 4.6rem;
    line-height: 1;
}

.score-box__sub {
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: var(--m-red);
    margin-top: 0.25rem;
    font-family: "Inter", sans-serif;
}

.match-card__cta {
    background: #fff;
    border: 1.5px solid var(--m-ink);
    color: var(--m-ink);
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 0.55rem 1rem;
    min-height: 2.4rem;
    border-radius: 999px;
    transition: background 0.18s ease, color 0.18s ease, transform 0.18s ease;
}

.match-card__cta:hover,
.match-card__cta:focus-visible {
    background: var(--m-ink);
    color: #fff;
    transform: translateY(-1px);
}

.match-card__meta {
    margin-top: 0.85rem;
    padding-top: 0.85rem;
    border-top: 1px dashed rgba(7, 24, 44, 0.1);
    gap: 0.5rem;
    flex-wrap: wrap;
    align-items: center;
}

/* News card */

.news-card {
    overflow: hidden;
    padding: 0;
}

.news-card__media {
    aspect-ratio: 16 / 9;
    background: linear-gradient(135deg, var(--m-navy), var(--m-burgundy));
    position: relative;
    overflow: hidden;
}

.news-card__media .placeholder-badge {
    color: rgba(212, 173, 82, 0.85);
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: 0.06em;
}

.news-card__body {
    padding: 1.1rem 1.15rem 1.15rem;
}

.news-card__title {
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: 1.15rem;
    font-weight: 700;
    line-height: 1.2;
    color: var(--m-ink);
    margin: 0.55rem 0 0.6rem;
}

.news-card__title a {
    color: inherit;
    transition: color 0.18s ease;
}

.news-card__title a:hover {
    color: var(--m-red);
}

.news-card__summary {
    color: #4a5b75;
    font-size: 0.88rem;
    line-height: 1.55;
    margin: 0 0 0.85rem;
}

.news-card__footer {
    color: var(--m-red);
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding-top: 0.6rem;
    border-top: 1px dashed rgba(7, 24, 44, 0.1);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.news-card__footer::after {
    content: "→";
    color: var(--m-gold-strong);
    font-size: 1rem;
    transition: transform 0.18s ease;
}

.news-card:hover .news-card__footer::after {
    transform: translateX(3px);
}

/* Quick link cards in Portal Access */

.quick-link-card {
    position: relative;
    padding: 1.4rem 1.4rem 1.4rem 1.4rem;
    overflow: hidden;
}

.quick-link-card::before {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 3px;
    background: linear-gradient(90deg, var(--m-gold), var(--m-red));
    transform-origin: left center;
    transform: scaleX(0.25);
    transition: transform 0.32s ease;
}

.quick-link-card:hover::before {
    transform: scaleX(1);
}

.quick-link-card::after {
    content: "→";
    position: absolute;
    right: 1.2rem;
    bottom: 1rem;
    color: var(--m-gold-strong);
    font-size: 1.1rem;
    transition: transform 0.22s ease;
}

.quick-link-card:hover::after {
    transform: translateX(4px);
}

[dir="rtl"] .quick-link-card::after {
    right: auto;
    left: 1.2rem;
    content: "←";
}

[dir="rtl"] .quick-link-card:hover::after {
    transform: translateX(-4px);
}

.quick-link-card strong {
    display: block;
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--m-ink);
    margin: 0.55rem 0 0.5rem;
    line-height: 1.05;
}

.quick-link-card p {
    color: #4a5b75;
    font-size: 0.88rem;
    line-height: 1.55;
    margin: 0;
    padding-right: 1.4rem;
}

[dir="rtl"] .quick-link-card p {
    padding-right: 0;
    padding-left: 1.4rem;
}

/* Portal Access strip */

.portal-access {
    background: linear-gradient(135deg, var(--m-paper) 0%, var(--m-paper-warm) 100%);
    border: 1px solid rgba(212, 173, 82, 0.22);
    border-radius: 24px;
    padding: clamp(1.6rem, 2.4vw, 2.2rem);
    box-shadow: var(--m-shadow-card);
    position: relative;
}

.portal-access::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 24px;
    background-image:
        radial-gradient(circle at 18% 22%, rgba(212, 173, 82, 0.05) 0 1.4px, transparent 1.5px),
        radial-gradient(circle at 82% 78%, rgba(175, 23, 49, 0.04) 0 1.4px, transparent 1.5px);
    background-size: 36px 36px, 36px 36px;
    background-position: 0 0, 18px 18px;
    pointer-events: none;
    opacity: 0.55;
}

.portal-access__header {
    position: relative;
    margin-bottom: 1.2rem;
}

.portal-access__header span {
    color: var(--m-red);
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    display: block;
    margin-bottom: 0.4rem;
}

.portal-access__header strong {
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: clamp(1.5rem, 2.4vw, 2rem);
    font-weight: 700;
    line-height: 1.1;
    color: var(--m-ink);
}

.portal-access__links {
    position: relative;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 1rem;
}

/* --- Public insights strip ------------------------------------------- */

.public-insights-strip {
    background: linear-gradient(135deg, var(--m-navy-strong) 0%, var(--m-navy) 100%);
    border: 1px solid rgba(212, 173, 82, 0.22);
    border-radius: 24px;
    padding: clamp(1.6rem, 2.4vw, 2.2rem);
    box-shadow: var(--m-shadow-card);
    color: #fff;
    position: relative;
    overflow: hidden;
}

.public-insights-strip::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 25% 25%, rgba(212, 173, 82, 0.045) 0 1.5px, transparent 1.6px),
        radial-gradient(circle at 75% 75%, rgba(212, 173, 82, 0.04) 0 1.5px, transparent 1.6px);
    background-size: 36px 36px, 36px 36px;
    background-position: 0 0, 18px 18px;
    pointer-events: none;
    opacity: 0.5;
}

.public-insights-strip__header {
    position: relative;
    margin-bottom: 1.2rem;
}

.public-insights-strip__header span {
    color: var(--m-gold);
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    display: block;
    margin-bottom: 0.4rem;
}

.public-insights-strip__header strong {
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: clamp(1.5rem, 2.4vw, 2rem);
    font-weight: 700;
    line-height: 1.1;
    color: #fff;
}

.public-insights-strip__grid {
    position: relative;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.95rem;
}

.public-insight {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 1.1rem 1.2rem;
    transition: border-color 0.18s ease, background 0.18s ease;
}

.public-insight:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(212, 173, 82, 0.4);
}

.public-insight strong {
    color: var(--m-gold);
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    display: block;
}

.public-insight span {
    color: #fff;
    font-size: 0.85rem;
    font-weight: 600;
    margin-top: 0.55rem;
    display: block;
}

.public-insight small {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.72rem;
    margin-top: 0.35rem;
    display: block;
    line-height: 1.35;
}

/* --- Page header (inner pages) -------------------------------------- */

.page-header {
    background:
        radial-gradient(ellipse 70% 60% at 80% 0%, rgba(212, 173, 82, 0.16), transparent 60%),
        radial-gradient(ellipse 60% 50% at 0% 100%, rgba(175, 23, 49, 0.3), transparent 60%),
        linear-gradient(135deg, var(--m-navy-strong) 0%, var(--m-navy) 50%, var(--m-navy-soft) 100%);
    border: 1px solid rgba(212, 173, 82, 0.2);
    box-shadow: var(--m-shadow-hero);
    border-radius: 24px;
    padding: clamp(2rem, 4vw, 3rem);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 25% 25%, rgba(212, 173, 82, 0.07) 0 1.5px, transparent 1.6px),
        radial-gradient(circle at 75% 75%, rgba(212, 173, 82, 0.06) 0 1.5px, transparent 1.6px);
    background-size: 36px 36px, 36px 36px;
    background-position: 0 0, 18px 18px;
    opacity: 0.5;
    pointer-events: none;
}

.page-header__content {
    position: relative;
}

.page-header__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.32rem 0.85rem 0.32rem 0.55rem;
    border-radius: 999px;
    background: rgba(212, 173, 82, 0.12);
    border: 1px solid rgba(212, 173, 82, 0.4);
    color: var(--m-gold);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.2em;
    text-transform: uppercase;
}

.page-header__eyebrow::before {
    content: "";
    width: 0.4rem;
    height: 0.4rem;
    transform: rotate(45deg);
    background: linear-gradient(135deg, var(--m-gold), var(--m-gold-strong));
}

.page-header h1 {
    font-family: "Cormorant Garamond", "Playfair Display", "Georgia", serif;
    font-size: clamp(2rem, 4.2vw, 3.2rem);
    font-weight: 700;
    line-height: 1.05;
    color: #fff;
    margin: 0.9rem 0 0.7rem;
}

.page-header p {
    color: rgba(255, 255, 255, 0.78);
    font-size: 1rem;
    line-height: 1.55;
    max-width: 60ch;
    margin: 0;
}

.page-header .page-header__actions .button--subtle {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.22);
    color: #fff;
    backdrop-filter: blur(8px);
}

.page-header .page-header__actions .button--subtle:hover,
.page-header .page-header__actions .button--subtle:focus-visible {
    background: rgba(255, 255, 255, 0.14);
    border-color: rgba(212, 173, 82, 0.45);
    color: #fff;
}

/* --- Buttons (clearer hierarchy on cards) ---------------------------- */

.button {
    background: linear-gradient(135deg, var(--m-red-bright) 0%, var(--m-red) 55%, var(--m-burgundy) 100%);
    border: 1px solid rgba(212, 173, 82, 0.5);
    color: #fff;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 0.78rem 1.3rem;
    min-height: 2.7rem;
    border-radius: 999px;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.16),
        0 12px 24px rgba(175, 23, 49, 0.32),
        0 2px 4px rgba(92, 15, 30, 0.32);
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.button:hover,
.button:focus-visible {
    transform: translateY(-1px);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.2),
        0 16px 32px rgba(175, 23, 49, 0.42),
        0 4px 8px rgba(92, 15, 30, 0.4);
}

.button--subtle {
    background: #fff;
    border: 1.5px solid var(--m-ink);
    color: var(--m-ink);
    font-size: 0.74rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 0.65rem 1.15rem;
    min-height: 2.55rem;
    border-radius: 999px;
    box-shadow: 0 6px 14px rgba(6, 22, 41, 0.06);
}

.button--subtle:hover,
.button--subtle:focus-visible {
    background: var(--m-ink);
    color: #fff;
    transform: translateY(-1px);
}

.button--ghost {
    background: transparent;
    border: 1px solid transparent;
    color: var(--m-red);
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 0.65rem 0.9rem;
    min-height: 2.55rem;
    border-radius: 999px;
}

.button--ghost::after {
    content: "→";
    color: var(--m-gold-strong);
    margin-left: 0.35rem;
    transition: transform 0.18s ease;
}

.button--ghost:hover::after,
.button--ghost:focus-visible::after {
    transform: translateX(3px);
}

[dir="rtl"] .button--ghost::after {
    content: "←";
    margin-left: 0;
    margin-right: 0.35rem;
}

[dir="rtl"] .button--ghost:hover::after,
[dir="rtl"] .button--ghost:focus-visible::after {
    transform: translateX(-3px);
}

/* --- Footer (Moroccan flag ribbon top accent) ----------------------- */

.site-footer {
    background:
        linear-gradient(180deg, var(--m-navy-strong) 0%, var(--m-navy) 100%);
    border-top: 1px solid rgba(212, 173, 82, 0.22);
    margin-top: 3.5rem;
    padding: 2.8rem 0 1.6rem;
    color: rgba(255, 255, 255, 0.78);
    position: relative;
}

.site-footer::before {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 3px;
    background: linear-gradient(90deg, var(--m-red) 0%, var(--m-gold) 33%, var(--m-green) 66%, var(--m-red) 100%);
    background-size: 200% 100%;
}

.site-footer__label {
    color: var(--m-gold);
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    margin-bottom: 0.6rem;
    display: block;
}

.site-footer__mark {
    width: 2.6rem;
    height: 2.6rem;
    border-radius: 999px;
    aspect-ratio: 1;
    overflow: hidden;
    display: grid;
    place-items: center;
    background:
        radial-gradient(circle at 30% 30%, rgba(212, 173, 82, 0.55), transparent 55%),
        linear-gradient(140deg, var(--m-red-bright) 0%, var(--m-red) 55%, var(--m-burgundy) 100%);
    color: #fff;
    font-family: "Cormorant Garamond", serif;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    box-shadow:
        inset 0 0 0 2px rgba(255, 255, 255, 0.1),
        inset 0 0 0 4px rgba(212, 173, 82, 0.5);
}

.site-footer__brand strong {
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: 1.3rem;
    font-weight: 700;
    color: #fff;
    margin-left: 0.65rem;
}

[dir="rtl"] .site-footer__brand strong {
    margin-left: 0;
    margin-right: 0.65rem;
}

.site-footer__links a {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.88rem;
    transition: color 0.18s ease;
}

.site-footer__links a:hover,
.site-footer__links a:focus-visible {
    color: var(--m-gold);
}

/* --- Auth pages: Moroccan signature on the auth shell -------------- */

.auth-shell {
    position: relative;
}

.auth-grid {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 1.6rem;
    align-items: start;
}

@media (max-width: 880px) {
    .auth-grid {
        grid-template-columns: 1fr;
    }
}

.auth-card {
    background: #fff;
    border: 1px solid rgba(7, 24, 44, 0.08);
    border-radius: 22px;
    padding: clamp(1.6rem, 3vw, 2.4rem);
    box-shadow: var(--m-shadow-card);
    position: relative;
    overflow: hidden;
}

.auth-card::before {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 4px;
    background: linear-gradient(90deg, var(--m-red) 0%, var(--m-gold) 50%, var(--m-green) 100%);
    pointer-events: none;
}

.auth-card h2 {
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: clamp(1.4rem, 2.2vw, 1.85rem);
    font-weight: 700;
    color: var(--m-ink);
    margin: 0.6rem 0 0.5rem;
    line-height: 1.1;
}

.auth-card > p {
    color: #4a5b75;
    font-size: 0.95rem;
    line-height: 1.55;
    margin: 0 0 1.4rem;
}

.auth-form {
    display: grid;
    gap: 1.05rem;
}

.field-group {
    display: grid;
    gap: 0.4rem;
}

.field-group label {
    color: var(--m-ink);
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.form-control {
    width: 100%;
    height: 3rem;
    padding: 0 0.95rem;
    background: var(--m-paper);
    border: 1.5px solid rgba(7, 24, 44, 0.14);
    border-radius: 12px;
    font-family: inherit;
    font-size: 0.95rem;
    color: var(--m-ink);
    transition: border-color 0.18s ease, background 0.18s ease, box-shadow 0.18s ease;
}

.form-control:hover {
    border-color: rgba(7, 24, 44, 0.24);
}

.form-control:focus,
.form-control:focus-visible {
    outline: 0;
    border-color: var(--m-red);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(212, 173, 82, 0.28);
}

select.form-control {
    appearance: none;
    background-image:
        linear-gradient(45deg, transparent 50%, var(--m-ink) 50%),
        linear-gradient(135deg, var(--m-ink) 50%, transparent 50%);
    background-position: calc(100% - 1.05rem) 50%, calc(100% - 0.75rem) 50%;
    background-size: 6px 6px, 6px 6px;
    background-repeat: no-repeat;
    padding-right: 2rem;
}

[dir="rtl"] select.form-control {
    background-position: 1.05rem 50%, 0.75rem 50%;
    padding-right: 0.95rem;
    padding-left: 2rem;
}

.form-error {
    color: var(--m-red);
    font-size: 0.8rem;
    font-weight: 600;
    margin-top: 0.2rem;
}

.form-helper {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    color: #4a5b75;
    font-size: 0.88rem;
}

.form-helper input[type="checkbox"] {
    width: 1.05rem;
    height: 1.05rem;
    accent-color: var(--m-red);
}

.form-helper a.section-link {
    color: var(--m-red);
    font-weight: 700;
    border-bottom: 1px solid rgba(175, 23, 49, 0.32);
    padding-bottom: 1px;
}

.form-helper a.section-link:hover {
    color: var(--m-burgundy);
    border-bottom-color: var(--m-red);
}

.form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.7rem;
    align-items: center;
    margin-top: 0.4rem;
}

.form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 640px) {
    .form-grid-2 {
        grid-template-columns: 1fr;
    }
}

.auth-note {
    background: linear-gradient(180deg, var(--m-paper) 0%, var(--m-paper-warm) 100%);
}

.auth-note::before {
    background: linear-gradient(90deg, var(--m-gold) 0%, var(--m-red) 100%);
}

.auth-note__list {
    margin: 1.2rem 0 0;
    padding: 0;
    list-style: none;
    display: grid;
    gap: 0.85rem;
}

.auth-note__list li {
    background: rgba(255, 255, 255, 0.6);
    border: 1px solid rgba(212, 173, 82, 0.2);
    border-radius: 14px;
    padding: 0.8rem 0.95rem;
    display: grid;
    gap: 0.2rem;
}

.auth-note__list li strong {
    color: var(--m-ink);
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: 1.05rem;
    font-weight: 700;
}

.auth-note__list li span {
    color: #4a5b75;
    font-size: 0.85rem;
    line-height: 1.45;
}

/* --- Flash banners (success / status / error) ----------------------- */

.flash-stack {
    display: grid;
    gap: 0.6rem;
    margin: 0 0 1.4rem;
}

.flash-banner {
    border-radius: 14px;
    padding: 0.85rem 1.05rem;
    font-size: 0.92rem;
    font-weight: 600;
    border: 1.5px solid transparent;
}

.flash-banner--success {
    background: rgba(23, 118, 84, 0.1);
    border-color: rgba(23, 118, 84, 0.32);
    color: var(--m-green);
}

.flash-banner--status {
    background: rgba(212, 173, 82, 0.13);
    border-color: rgba(212, 173, 82, 0.42);
    color: #74540b;
}

.flash-banner--error {
    background: rgba(175, 23, 49, 0.1);
    border-color: rgba(175, 23, 49, 0.4);
    color: var(--m-red);
}

/* --- Empty state polish --------------------------------------------- */

.empty-state {
    background: var(--m-paper);
    border: 1.5px dashed rgba(7, 24, 44, 0.18);
    border-radius: 20px;
    padding: 2rem 1.6rem;
    display: flex;
    align-items: center;
    gap: 1.2rem;
}

.empty-state__mark {
    width: 3.2rem;
    height: 3.2rem;
    border-radius: 999px;
    aspect-ratio: 1;
    display: grid;
    place-items: center;
    background:
        radial-gradient(circle at 30% 30%, rgba(212, 173, 82, 0.55), transparent 55%),
        linear-gradient(140deg, var(--m-red-bright) 0%, var(--m-red) 55%, var(--m-burgundy) 100%);
    color: #fff;
    font-family: "Cormorant Garamond", serif;
    font-weight: 700;
    font-size: 1rem;
    letter-spacing: 0.04em;
    box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.12), inset 0 0 0 4px rgba(212, 173, 82, 0.5);
    flex-shrink: 0;
}

.empty-state strong {
    display: block;
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: 1.18rem;
    font-weight: 700;
    color: var(--m-ink);
    margin-bottom: 0.3rem;
}

.empty-state p {
    color: #4a5b75;
    font-size: 0.92rem;
    line-height: 1.55;
    margin: 0;
}

/* --- Shell popovers (search/language/account/menu) ------------------ */

.shell-panel {
    background: #fff;
    border: 1px solid rgba(7, 24, 44, 0.12);
    border-radius: 18px;
    box-shadow: 0 24px 60px rgba(6, 22, 41, 0.2);
    padding: 1.1rem;
    min-width: 18rem;
}

.shell-panel__label {
    color: var(--m-red);
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    margin-bottom: 0.65rem;
    display: block;
}

.shell-menu-link {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.65rem 0.8rem;
    border-radius: 10px;
    color: var(--m-ink);
    font-size: 0.92rem;
    font-weight: 600;
    transition: background 0.16s ease, color 0.16s ease;
}

.shell-menu-link:hover,
.shell-menu-link:focus-visible {
    background: var(--m-paper);
    color: var(--m-red);
}

.shell-menu-link--primary {
    background: linear-gradient(135deg, var(--m-red-bright), var(--m-red));
    color: #fff;
    margin-top: 0.4rem;
}

.shell-menu-link--primary:hover,
.shell-menu-link--primary:focus-visible {
    background: var(--m-burgundy);
    color: #fff;
}

.shell-menu-form {
    margin: 0;
}

.shell-menu-link--button {
    border: 0;
    width: 100%;
    text-align: start;
    cursor: pointer;
    background: transparent;
    font-family: inherit;
}

.shell-search-form label {
    color: var(--m-red);
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    margin-bottom: 0.55rem;
    display: block;
}

.shell-search-form__row {
    display: flex;
    gap: 0.55rem;
}

.shell-search-form__row input[type="search"] {
    flex: 1;
    height: 2.8rem;
    padding: 0 0.95rem;
    background: var(--m-paper);
    border: 1.5px solid rgba(7, 24, 44, 0.14);
    border-radius: 999px;
    font-family: inherit;
    font-size: 0.92rem;
    color: var(--m-ink);
}

.shell-search-form__row input[type="search"]:focus,
.shell-search-form__row input[type="search"]:focus-visible {
    outline: 0;
    border-color: var(--m-red);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(212, 173, 82, 0.28);
}

.shell-language-list {
    display: grid;
    gap: 0.4rem;
}

.shell-language-list__item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 0.6rem 0.75rem;
    border-radius: 12px;
    color: var(--m-ink);
    background: var(--m-paper);
    border: 1px solid transparent;
    transition: background 0.16s ease, border-color 0.16s ease, color 0.16s ease;
}

.shell-language-list__item span {
    width: 2.1rem;
    text-align: center;
    font-size: 0.7rem;
    font-weight: 800;
    color: var(--m-red);
    letter-spacing: 0.08em;
}

.shell-language-list__item strong {
    font-weight: 600;
    font-size: 0.9rem;
}

.shell-language-list__item:hover,
.shell-language-list__item:focus-visible {
    background: rgba(212, 173, 82, 0.1);
    border-color: rgba(212, 173, 82, 0.32);
}

.shell-language-list__item.is-active {
    background: rgba(175, 23, 49, 0.08);
    border-color: rgba(175, 23, 49, 0.32);
    color: var(--m-red);
}

.shell-drawer__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 0.8rem;
    margin-bottom: 0.8rem;
    border-bottom: 1px solid rgba(7, 24, 44, 0.08);
}

.shell-drawer__head span {
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--m-ink);
}

.shell-drawer__close {
    background: transparent;
    border: 1px solid rgba(7, 24, 44, 0.14);
    color: var(--m-ink);
    border-radius: 999px;
    padding: 0.32rem 0.75rem;
    font-size: 0.78rem;
    font-weight: 700;
    cursor: pointer;
}

.shell-drawer__close:hover,
.shell-drawer__close:focus-visible {
    background: var(--m-ink);
    color: #fff;
}

.shell-drawer__nav {
    display: grid;
    gap: 0.25rem;
}

.shell-drawer__nav a {
    padding: 0.6rem 0.8rem;
    border-radius: 10px;
    color: var(--m-ink);
    font-weight: 600;
}

.shell-drawer__nav a:hover,
.shell-drawer__nav a:focus-visible {
    background: var(--m-paper);
    color: var(--m-red);
}

.shell-drawer__nav a.is-active {
    background: rgba(175, 23, 49, 0.08);
    color: var(--m-red);
}

.shell-drawer__actions {
    margin-top: 1rem;
    padding-top: 0.95rem;
    border-top: 1px solid rgba(7, 24, 44, 0.08);
    display: grid;
    gap: 0.4rem;
}

.shell-drawer__actions a {
    padding: 0.6rem 0.8rem;
    border-radius: 10px;
    background: var(--m-paper);
    color: var(--m-ink);
    font-weight: 700;
    font-size: 0.85rem;
    text-align: center;
}

.shell-drawer__actions a:hover,
.shell-drawer__actions a:focus-visible {
    background: var(--m-ink);
    color: #fff;
}

/* --- Home map card -------------------------------------------------- */

.home-map-card {
    background: var(--m-paper);
    border: 1px solid rgba(7, 24, 44, 0.08);
    border-radius: 24px;
    padding: clamp(1.6rem, 2.4vw, 2.2rem);
    box-shadow: var(--m-shadow-card);
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 1.6rem;
    position: relative;
}

@media (max-width: 880px) {
    .home-map-card {
        grid-template-columns: 1fr;
    }
}

.home-map-card__intro h2 {
    font-family: "Cormorant Garamond", "Playfair Display", serif;
    font-size: clamp(1.4rem, 2.4vw, 2rem);
    font-weight: 700;
    color: var(--m-ink);
    margin: 0.5rem 0 0.55rem;
    line-height: 1.1;
}

.home-map-card__intro p {
    color: #4a5b75;
    font-size: 0.96rem;
    line-height: 1.55;
    margin: 0 0 1.1rem;
}

/* --- Section link with arrow accent -------------------------------- */

.section-link {
    color: var(--m-red);
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    border-bottom: 1px solid transparent;
    padding-bottom: 1px;
}

.section-link::after {
    content: "→";
    color: var(--m-gold-strong);
    font-size: 1rem;
    transition: transform 0.18s ease;
}

.section-link:hover,
.section-link:focus-visible {
    color: var(--m-burgundy);
    border-bottom-color: rgba(175, 23, 49, 0.4);
}

.section-link:hover::after,
.section-link:focus-visible::after {
    transform: translateX(3px);
}

[dir="rtl"] .section-link::after {
    content: "←";
}

[dir="rtl"] .section-link:hover::after,
[dir="rtl"] .section-link:focus-visible::after {
    transform: translateX(-3px);
}

/* --- Body / global tweaks -------------------------------------------- */

body {
    font-family: "Inter", "Segoe UI Variable", "Segoe UI", "Helvetica Neue", system-ui, sans-serif;
}

body.page-home {
    background:
        radial-gradient(ellipse at top, rgba(7, 24, 44, 0.06), transparent 56%),
        radial-gradient(circle at 92% 4%, rgba(175, 23, 49, 0.05), transparent 26%),
        linear-gradient(180deg, #f6f8fc 0%, #eef2f8 100%);
}

body.page-inner {
    background:
        radial-gradient(ellipse at top, rgba(7, 24, 44, 0.04), transparent 50%),
        linear-gradient(180deg, #f6f8fc 0%, #eef2f8 100%);
}

/* --- Skip link visible on focus ------------------------------------- */

.skip-link {
    position: absolute;
    top: -100px;
    left: 0;
    background: var(--m-ink);
    color: #fff;
    padding: 0.6rem 1rem;
    border-radius: 0 0 12px 0;
    font-weight: 700;
    z-index: 1000;
    transition: top 0.2s ease;
}

.skip-link:focus,
.skip-link:focus-visible {
    top: 0;
    outline: 2px solid var(--m-gold);
    outline-offset: 2px;
}

/* --- High-contrast focus for primary controls ----------------------- */

a:focus-visible,
button:focus-visible,
input:focus-visible,
select:focus-visible,
textarea:focus-visible {
    outline: 0;
    box-shadow: 0 0 0 3px rgba(212, 173, 82, 0.45), 0 0 0 5px rgba(175, 23, 49, 0.35);
    border-radius: 8px;
}

/* --- Reduced motion ------------------------------------------------- */

@media (prefers-reduced-motion: reduce) {
    .status-pill--live::before {
        animation: none;
    }
    *,
    *::before,
    *::after {
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
    }
}

/* --- PHASE 1B RTL refinements --------------------------------------- */

[dir="rtl"] .hero__eyebrow::before,
[dir="rtl"] .page-header__eyebrow::before,
[dir="rtl"] .section-header__eyebrow::before {
    background: linear-gradient(270deg, transparent, var(--m-gold));
}

[dir="rtl"] .news-card__footer::after {
    content: "←";
}

[dir="rtl"] .site-nav a.is-active::after {
    background-image: linear-gradient(270deg, var(--m-gold), var(--m-red-bright));
}

[dir="rtl"] .button--ghost::after {
    margin-left: 0;
    margin-right: 0.35rem;
}

[dir="rtl"] .hero__actions .button--ghost::after {
    content: "←";
}
