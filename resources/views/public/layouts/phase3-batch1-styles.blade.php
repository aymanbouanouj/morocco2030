/* ==========================================================================
   PHASE 3 BATCH 1 - Public UI redesign implementation.
   Scope: public header, homepage hero, homepage preview sections, auth entry
   visibility. Backend, routing, admin, sports logic, and interaction hooks are
   intentionally untouched.
   ========================================================================== */

:root {
    --p3-red: #b10f2e;
    --p3-red-deep: #6f0d1b;
    --p3-gold: #c9a44c;
    --p3-green: #0d6b45;
    --p3-night-strong: #02060d;
    --p3-ink: #141c27;
    --p3-paper: #fffdf8;
    --p3-radius-lg: 28px;
    --p3-radius-md: 18px;
    --p3-shadow-shell: 0 18px 44px rgba(2, 6, 13, 0.42);
    --p3-shadow-card: 0 1px 0 rgba(255, 255, 255, 0.78) inset, 0 18px 38px rgba(6, 17, 31, 0.08);
}

body.page-home {
    background:
        radial-gradient(circle at 12% 0%, rgba(177, 15, 46, 0.08), transparent 28%),
        radial-gradient(circle at 88% 6%, rgba(201, 164, 76, 0.13), transparent 26%),
        linear-gradient(180deg, #fbf8f0 0%, #f2efe7 48%, #edf1f6 100%);
}

.page-container {
    width: min(calc(100% - 32px), var(--container, 1320px));
}

/* Public shell: compact black tournament bar with clear nav and auth. */

.site-header {
    background:
        radial-gradient(circle at 16% 0%, rgba(177, 15, 46, 0.36), transparent 30%),
        linear-gradient(105deg, var(--p3-night-strong) 0%, #071320 52%, #1c0610 100%);
    border-bottom: 1px solid rgba(201, 164, 76, 0.24);
    box-shadow: var(--p3-shadow-shell);
}

.site-header::before {
    height: 3px;
    background: linear-gradient(90deg, var(--p3-red-deep), var(--p3-red), var(--p3-gold), var(--p3-green));
    opacity: 0.96;
}

.site-header::after {
    background: linear-gradient(90deg, transparent, rgba(201, 164, 76, 0.46), transparent);
}

.site-header__inner {
    width: min(calc(100% - 32px), 1360px);
    display: grid;
    grid-template-columns: minmax(172px, auto) minmax(0, 1fr) auto;
    align-items: center;
    gap: clamp(0.75rem, 1.5vw, 1.45rem);
    padding-block: 0.58rem;
}

.brand {
    min-width: 0;
    gap: 0.75rem;
    padding: 0;
}

.brand__mark {
    width: 3.05rem;
    height: 3.05rem;
    border: 1px solid rgba(255, 255, 255, 0.22);
    background:
        radial-gradient(circle at 32% 24%, rgba(255, 230, 166, 0.5), transparent 34%),
        radial-gradient(circle at 72% 78%, rgba(13, 107, 69, 0.22), transparent 42%),
        linear-gradient(145deg, #d31f3c 0%, var(--p3-red) 48%, var(--p3-red-deep) 100%);
    box-shadow:
        inset 0 0 0 4px rgba(201, 164, 76, 0.62),
        inset 0 0 0 6px rgba(255, 255, 255, 0.08),
        0 12px 24px rgba(177, 15, 46, 0.34);
}

.brand__mark::before {
    content: "";
    position: absolute;
    inset: 0.46rem;
    border: 1px solid rgba(255, 255, 255, 0.36);
    transform: rotate(45deg);
    opacity: 0.52;
}

.brand__mark-text {
    position: relative;
    z-index: 1;
    font-size: 0.92rem;
    letter-spacing: 0.08em;
}

.brand__meta {
    color: #fff;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(1.05rem, 1.35vw, 1.28rem);
    font-weight: 700;
    letter-spacing: -0.02em;
}

.brand__meta small {
    color: rgba(201, 164, 76, 0.96);
    font-family: "Inter", "Segoe UI", system-ui, sans-serif;
    font-size: 0.58rem;
    font-weight: 800;
    letter-spacing: 0.2em;
}

.site-nav.site-nav--desktop {
    justify-self: center;
    display: inline-flex;
    align-items: center;
    max-width: 100%;
    padding: 0.22rem;
    gap: 0.08rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.045);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
    overflow-x: auto;
    scrollbar-width: none;
}

.site-nav.site-nav--desktop::-webkit-scrollbar {
    display: none;
}

.site-nav.site-nav--desktop a {
    min-height: 2rem;
    display: inline-flex;
    align-items: center;
    padding: 0.48rem 0.72rem;
    border-radius: 999px;
    color: rgba(255, 255, 255, 0.78);
    font-size: clamp(0.66rem, 0.72vw, 0.76rem);
    font-weight: 800;
    letter-spacing: 0.055em;
    line-height: 1;
    white-space: nowrap;
}

.site-nav.site-nav--desktop a:hover,
.site-nav.site-nav--desktop a:focus-visible {
    color: #fff;
    background: rgba(255, 255, 255, 0.08);
}

.site-nav.site-nav--desktop a.is-active {
    color: #fff;
    background: linear-gradient(135deg, rgba(177, 15, 46, 0.92), rgba(111, 13, 27, 0.92));
    box-shadow: inset 0 0 0 1px rgba(201, 164, 76, 0.34), 0 7px 16px rgba(177, 15, 46, 0.28);
}

.site-nav.site-nav--desktop a.is-active::after {
    content: none;
}

.site-header__utility-tools {
    justify-self: end;
    display: inline-flex;
    align-items: center;
    gap: 0.32rem;
    min-width: 0;
}

.shell-icon-button {
    width: 2.18rem;
    height: 2.18rem;
    flex: 0 0 auto;
    border-color: rgba(255, 255, 255, 0.14);
    background: rgba(255, 255, 255, 0.055);
    color: rgba(255, 255, 255, 0.9);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.shell-icon-button:hover,
.shell-icon-button:focus-visible,
.shell-icon-button[aria-expanded="true"],
.shell-icon-button.is-active {
    color: #fff;
    border-color: rgba(201, 164, 76, 0.58);
    background: rgba(201, 164, 76, 0.14);
}

.shell-icon-button--language {
    min-width: 2.7rem;
    padding-inline: 0.5rem;
}

.shell-current-language {
    color: rgba(255, 255, 255, 0.86);
    font-size: 0.62rem;
    font-weight: 900;
}

.site-header__divider {
    height: 1.45rem;
    margin-inline: 0.16rem;
    background: linear-gradient(180deg, transparent, rgba(201, 164, 76, 0.46), transparent);
}

.header-auth {
    gap: 0.38rem;
    margin-inline-start: 0.08rem;
}

.header-auth__link,
.header-auth__account {
    min-height: 2.18rem;
    height: 2.18rem;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 900;
    letter-spacing: 0.08em;
}

.header-auth__link {
    padding-inline: 0.8rem;
}

.header-auth__link--ghost {
    color: rgba(255, 255, 255, 0.92);
    border-color: rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.025);
}

.header-auth__link--ghost:hover,
.header-auth__link--ghost:focus-visible {
    border-color: rgba(201, 164, 76, 0.55);
    background: rgba(255, 255, 255, 0.08);
}

.header-auth__link--primary {
    border-color: rgba(201, 164, 76, 0.76);
    background: linear-gradient(135deg, #d11f3c 0%, var(--p3-red) 48%, var(--p3-red-deep) 100%);
    box-shadow: 0 10px 20px rgba(177, 15, 46, 0.34), inset 0 1px 0 rgba(255, 255, 255, 0.22);
}

.header-auth__account {
    padding-inline: 0.34rem 0.78rem;
    color: #fff;
    border-color: rgba(201, 164, 76, 0.34);
    background: rgba(255, 255, 255, 0.06);
}

.header-auth__account-mark {
    width: 1.62rem;
    height: 1.62rem;
    background: linear-gradient(135deg, var(--p3-gold), var(--p3-red));
}

.shell-popovers {
    width: min(calc(100% - 32px), 1360px);
    margin-inline: auto;
}

.shell-panel {
    border: 1px solid rgba(201, 164, 76, 0.26);
    border-radius: 18px;
    background: rgba(255, 253, 248, 0.98);
    box-shadow: 0 22px 50px rgba(2, 6, 13, 0.24);
}

.shell-panel--drawer {
    border-radius: 22px;
}

.shell-menu-link--primary,
.shell-drawer__actions a:last-child {
    background: linear-gradient(135deg, var(--p3-red), var(--p3-red-deep));
    color: #fff;
}

/* Homepage hero: official tournament entry point. */

.page-home .hero.hero--command {
    isolation: isolate;
    margin-top: clamp(1.05rem, 1.7vw, 1.45rem);
    display: grid;
    grid-template-columns: minmax(0, 1.05fr) minmax(340px, 0.76fr);
    align-items: stretch;
    gap: clamp(1rem, 2vw, 1.85rem);
    min-height: clamp(560px, 70vh, 760px);
    padding: clamp(1.65rem, 3.5vw, 4rem);
    border: 1px solid rgba(255, 255, 255, 0.11);
    border-radius: clamp(22px, 2.3vw, 34px);
    background:
        radial-gradient(circle at 76% 18%, rgba(201, 164, 76, 0.28), transparent 20%),
        radial-gradient(circle at 7% 88%, rgba(177, 15, 46, 0.36), transparent 32%),
        linear-gradient(118deg, rgba(2, 6, 13, 0.94) 0%, rgba(6, 17, 31, 0.96) 42%, rgba(76, 8, 22, 0.96) 100%);
    box-shadow:
        0 34px 90px rgba(6, 17, 31, 0.34),
        0 1px 0 rgba(255, 255, 255, 0.1) inset;
}

.page-home .hero.hero--command::before {
    inset: 10% -8% auto auto;
    width: min(36vw, 520px);
    background:
        radial-gradient(circle, rgba(201, 164, 76, 0.18), transparent 60%),
        repeating-conic-gradient(from 45deg, rgba(201, 164, 76, 0.14) 0 8deg, transparent 8deg 18deg);
    opacity: 0.76;
    filter: blur(0.2px);
}

.page-home .hero.hero--command::after {
    z-index: 0;
    background:
        linear-gradient(90deg, rgba(255, 255, 255, 0.055), transparent 30%),
        repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.026) 0 1px, transparent 1px 24px),
        linear-gradient(180deg, transparent 0%, rgba(2, 6, 13, 0.22) 100%);
}

.page-home .hero__main {
    max-width: 55rem;
    gap: clamp(0.85rem, 1.5vw, 1.2rem);
    justify-content: center;
}

.page-home .hero__eyebrow {
    width: fit-content;
    padding: 0.36rem 0.72rem;
    border: 1px solid rgba(201, 164, 76, 0.34);
    border-radius: 999px;
    color: #ffe4a1;
    background: rgba(255, 255, 255, 0.055);
    font-size: 0.7rem;
    letter-spacing: 0.2em;
}

.page-home .hero__eyebrow::before {
    width: 0.48rem;
    height: 0.48rem;
    border-radius: 999px;
    background: var(--p3-gold);
    box-shadow: 0 0 0 4px rgba(201, 164, 76, 0.16);
}

.page-home .hero__chip-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    margin-bottom: 0.08rem;
}

.page-home .hero-chip {
    display: inline-flex;
    align-items: center;
    min-height: 1.72rem;
    padding: 0.34rem 0.7rem;
    border: 1px solid rgba(255, 255, 255, 0.13);
    border-radius: 999px;
    color: rgba(255, 255, 255, 0.74);
    background: rgba(255, 255, 255, 0.045);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.page-home .hero h1 {
    max-width: 11ch;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(3.25rem, 7.3vw, 7rem);
    line-height: 0.88;
    letter-spacing: -0.07em;
    text-wrap: balance;
}

.page-home .hero h1 span {
    margin-top: 0.62rem;
    color: #f5d485;
    font-family: "Inter", "Segoe UI", system-ui, sans-serif;
    font-size: clamp(0.82rem, 1.35vw, 1.05rem);
    letter-spacing: 0.3em;
    line-height: 1.25;
}

.page-home .hero p {
    max-width: 43rem;
    color: rgba(255, 255, 255, 0.84);
    font-size: clamp(1rem, 1.15vw, 1.14rem);
    line-height: 1.68;
}

.page-home .hero__actions {
    gap: 0.62rem;
    margin-top: 0.32rem;
}

.page-home .hero__actions .button,
.page-home .hero__actions .button--subtle,
.page-home .hero__actions .button--ghost {
    min-height: 2.8rem;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 900;
    letter-spacing: 0.075em;
    text-transform: uppercase;
}

.page-home .hero__actions .button {
    border: 1px solid rgba(201, 164, 76, 0.75);
    background: linear-gradient(135deg, #d51f3f 0%, var(--p3-red) 52%, var(--p3-red-deep) 100%);
    color: #fff;
    box-shadow: 0 18px 34px rgba(177, 15, 46, 0.38), inset 0 1px 0 rgba(255, 255, 255, 0.24);
}

.page-home .hero__actions .button--subtle {
    border-color: rgba(255, 255, 255, 0.22);
    background: rgba(255, 255, 255, 0.085);
}

.page-home .hero__actions .button--ghost {
    color: #ffe0a0;
    border: 1px solid rgba(201, 164, 76, 0.24);
    padding-inline: 1.05rem;
    text-decoration: none;
}

.page-home .hero__quicklinks {
    width: fit-content;
    max-width: 100%;
    gap: 0.38rem;
    padding: 0.48rem;
    margin-top: 0.4rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.045);
}

.page-home .hero__quicklink {
    min-height: 1.9rem;
    padding: 0.38rem 0.76rem;
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.72rem;
    font-weight: 800;
}

.page-home .hero__quicklink--accent {
    color: #fff;
    background: rgba(177, 15, 46, 0.58);
}

.page-home .hero__board {
    align-self: center;
    gap: 0.82rem;
    padding: clamp(0.82rem, 1.35vw, 1.1rem);
    border: 1px solid rgba(201, 164, 76, 0.24);
    border-radius: 22px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.09), rgba(255, 255, 255, 0.025)),
        rgba(2, 6, 13, 0.48);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1), 0 18px 46px rgba(2, 6, 13, 0.3);
}

.page-home .hero-countdown {
    padding: 1rem;
    border-radius: 18px;
    border: 1px solid rgba(201, 164, 76, 0.36);
    background:
        linear-gradient(135deg, rgba(201, 164, 76, 0.2), rgba(177, 15, 46, 0.12) 52%, rgba(2, 6, 13, 0.42));
}

.page-home .hero-countdown__header strong {
    color: #fff;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(1.22rem, 2vw, 1.65rem);
    line-height: 1.05;
}

.page-home .hero-countdown__header p,
.page-home .hero-countdown__status {
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.72rem;
}

.page-home .hero-countdown__grid {
    gap: 0.45rem;
}

.page-home .hero-countdown__unit {
    min-height: 4rem;
    padding: 0.68rem 0.45rem;
    border-radius: 14px;
    background: rgba(2, 6, 13, 0.48);
}

.page-home .hero-countdown__unit strong {
    color: #fff;
    font-size: clamp(1.45rem, 2.4vw, 2.05rem);
}

.page-home .hero-countdown__unit span {
    color: rgba(255, 229, 168, 0.84);
    font-size: 0.58rem;
    letter-spacing: 0.11em;
}

.page-home .hero-feature {
    padding: 0.9rem;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.page-home .hero-feature--primary {
    border-color: rgba(201, 164, 76, 0.34);
    background:
        linear-gradient(135deg, rgba(177, 15, 46, 0.18), transparent 54%),
        rgba(255, 255, 255, 0.06);
}

.page-home .hero-feature__title {
    font-size: 1.04rem;
    line-height: 1.18;
}

.page-home .hero-feature__meta,
.page-home .hero-feature__row {
    font-size: 0.72rem;
}

.page-home .hero__board .key-list {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.48rem;
}

.page-home .hero__board .key-list > div {
    padding: 0.68rem;
    border-color: rgba(255, 255, 255, 0.09);
    border-radius: 13px;
}

.page-home .hero__board .key-list strong {
    color: #ffe0a0;
    font-size: 1.34rem;
}

/* Homepage section rhythm and preview cards. */

.page-home .page-section {
    margin-top: clamp(1.2rem, 2vw, 1.9rem);
}

.page-home .portal-access,
.page-home .section-shell,
.page-home .public-insights-strip,
.page-home .home-map-card {
    border-radius: var(--p3-radius-lg);
    box-shadow: var(--p3-shadow-card);
}

.page-home .portal-access {
    display: grid;
    grid-template-columns: minmax(200px, 0.28fr) minmax(0, 1fr);
    gap: clamp(1rem, 2vw, 1.6rem);
    padding: clamp(1.1rem, 2vw, 1.65rem);
    border-color: rgba(201, 164, 76, 0.3);
    background:
        radial-gradient(circle at 0% 0%, rgba(177, 15, 46, 0.08), transparent 28%),
        linear-gradient(135deg, var(--p3-paper), #f4eddf);
}

.page-home .portal-access__header {
    margin-bottom: 0;
    padding-inline-end: 1rem;
    border-inline-end: 1px solid rgba(20, 28, 39, 0.1);
}

.page-home .portal-access__header span,
.page-home .section-header__eyebrow,
.page-home .public-insights-strip__header span {
    color: var(--p3-red);
    letter-spacing: 0.18em;
}

.page-home .portal-access__header strong,
.page-home .section-header h2,
.page-home .section-header h3,
.page-home .quick-link-card strong,
.page-home .public-insights-strip__header strong,
.page-home .home-map-card__intro h2 {
    font-family: "Georgia", "Times New Roman", serif;
    letter-spacing: -0.03em;
}

.page-home .portal-access__links {
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.72rem;
}

.page-home .quick-link-card {
    min-height: 0;
    padding: 1rem 1rem 1.08rem;
    border-radius: 18px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 251, 244, 0.96));
}

.page-home .quick-link-card strong {
    font-size: 1.05rem;
    margin-block: 0.42rem 0.34rem;
}

.page-home .quick-link-card p {
    padding: 0;
    color: #59677a;
    font-size: 0.78rem;
    line-height: 1.42;
}

.page-home .section-shell {
    padding: clamp(1.1rem, 1.8vw, 1.65rem);
    background:
        linear-gradient(180deg, rgba(255, 253, 248, 0.98), rgba(247, 243, 235, 0.98));
    border-color: rgba(20, 28, 39, 0.09);
}

.page-home .section-shell::before,
.page-home .portal-access::before {
    opacity: 0.52;
}

.page-home .section-header {
    margin-bottom: clamp(0.85rem, 1.4vw, 1.2rem);
    gap: 0.9rem;
}

.page-home .section-header h2 {
    font-size: clamp(1.38rem, 2vw, 1.95rem);
}

.page-home .section-header p {
    color: #59677a;
    font-size: 0.88rem;
    line-height: 1.5;
}

.page-home .button--subtle,
.page-home .section-link {
    font-size: 0.7rem;
    letter-spacing: 0.11em;
}

.page-home .card-grid,
.page-home .listing-grid,
.page-home .team-grid,
.page-home .city-grid {
    gap: clamp(0.72rem, 1.25vw, 1rem);
}

.page-home .section-stack {
    gap: 0.72rem;
}

.page-home .match-card,
.page-home .news-card,
.page-home .panel,
.page-home .partner-card,
.page-home .list-card {
    border-radius: var(--p3-radius-md);
    border-color: rgba(20, 28, 39, 0.09);
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.72) inset, 0 12px 26px rgba(6, 17, 31, 0.06);
}

.page-home .match-card {
    padding: 0.92rem 1rem 0.85rem 1.12rem;
}

.page-home .match-card__team strong {
    font-size: clamp(1.12rem, 1.5vw, 1.32rem);
}

.page-home .score-box {
    min-width: 4.1rem;
    padding: 0.58rem 0.78rem;
    font-size: 1.32rem;
}

.page-home .match-card__meta {
    margin-top: 0.62rem;
    padding-top: 0.62rem;
}

.page-home .news-card__media {
    aspect-ratio: 17 / 9;
}

.page-home .news-card__body {
    padding: 0.92rem 1rem 1rem;
}

.page-home .news-card__title {
    font-size: 1.05rem;
    line-height: 1.22;
}

.page-home .news-card__summary {
    font-size: 0.82rem;
    line-height: 1.48;
}

.page-home .panel {
    padding: clamp(0.9rem, 1.4vw, 1.15rem);
    background: rgba(255, 255, 255, 0.72);
}

.page-home .list-card {
    padding: 0.82rem 0.92rem;
    background: #fff;
}

.page-home .public-insights-strip {
    padding: clamp(1.15rem, 2vw, 1.7rem);
    background:
        radial-gradient(circle at 92% 0%, rgba(201, 164, 76, 0.2), transparent 26%),
        linear-gradient(135deg, var(--p3-night-strong), #071a2c 60%, #260713);
}

.page-home .public-insights-strip__grid {
    gap: 0.72rem;
}

.page-home .public-insight {
    padding: 0.9rem 1rem;
    border-radius: 16px;
}

.page-home .public-insight strong {
    font-size: 1.72rem;
}

.page-home .home-map-card {
    padding: clamp(1.15rem, 2vw, 1.7rem);
    border-color: rgba(201, 164, 76, 0.24);
    background:
        linear-gradient(135deg, var(--p3-paper), #f3eddf);
}

/* Public auth screens: clearer access cards without changing auth logic. */

.auth-shell {
    margin-top: clamp(1.3rem, 2vw, 2rem);
}

.auth-card {
    border-color: rgba(201, 164, 76, 0.22);
    box-shadow: var(--p3-shadow-card);
}

.auth-card .button,
.auth-form .button {
    border-radius: 999px;
    background: linear-gradient(135deg, var(--p3-red), var(--p3-red-deep));
    box-shadow: 0 12px 26px rgba(177, 15, 46, 0.24);
}

/* Responsive behavior: keep header compact and homepage readable. */

@media (max-width: 1240px) {
    .site-header__inner {
        grid-template-columns: auto minmax(0, 1fr) auto;
        gap: 0.72rem;
    }

    .site-nav.site-nav--desktop a {
        padding-inline: 0.56rem;
        font-size: 0.64rem;
    }

    .header-auth__link {
        padding-inline: 0.64rem;
    }
}

@media (max-width: 1100px) {
    .site-nav.site-nav--desktop {
        justify-self: start;
    }

    .header-auth {
        display: none;
    }

    .site-header__divider {
        display: none;
    }

    .page-home .hero.hero--command {
        grid-template-columns: 1fr;
        min-height: 0;
    }

    .page-home .hero__board {
        align-self: stretch;
    }

    .page-home .portal-access,
    .page-home .portal-access__links {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-home .portal-access__header {
        grid-column: 1 / -1;
        border-inline-end: 0;
        border-bottom: 1px solid rgba(20, 28, 39, 0.1);
        padding: 0 0 0.9rem;
    }
}

@media (max-width: 900px) {
    .site-header__inner {
        grid-template-columns: auto auto;
        justify-content: space-between;
    }

    .site-nav.site-nav--desktop {
        display: none;
    }

    .page-home .hero.hero--command {
        padding: clamp(1.25rem, 5vw, 2rem);
    }

    .page-home .hero h1 {
        font-size: clamp(2.8rem, 14vw, 4.6rem);
    }
}

@media (max-width: 700px) {
    .page-container,
    .site-header__inner,
    .shell-popovers {
        width: min(calc(100% - 22px), 1360px);
    }

    .brand__mark {
        width: 2.55rem;
        height: 2.55rem;
    }

    .brand__meta {
        font-size: 1rem;
    }

    .brand__meta small {
        font-size: 0.5rem;
        letter-spacing: 0.15em;
    }

    .shell-icon-button--language {
        display: none;
    }

    .page-home .hero__chip-row,
    .page-home .hero__quicklinks {
        display: none;
    }

    .page-home .hero p {
        font-size: 0.96rem;
    }

    .page-home .hero__actions {
        display: grid;
        grid-template-columns: 1fr;
    }

    .page-home .hero__actions .button,
    .page-home .hero__actions .button--subtle,
    .page-home .hero__actions .button--ghost {
        justify-content: center;
        width: 100%;
    }

    .page-home .hero-countdown__grid,
    .page-home .hero__board .key-list,
    .page-home .portal-access,
    .page-home .portal-access__links {
        grid-template-columns: 1fr;
    }

    .page-home .section-header {
        align-items: flex-start;
        flex-direction: column;
    }
}

@media (max-width: 460px) {
    .site-header__utility-tools {
        gap: 0.22rem;
    }

    .shell-icon-button {
        width: 2.02rem;
        height: 2.02rem;
    }

    .header-auth__account {
        width: 2.02rem;
        height: 2.02rem;
        padding: 0;
    }

    .header-auth__account span:not(.header-auth__account-mark) {
        display: none;
    }

    .header-auth__account-mark {
        width: 100%;
        height: 100%;
    }

    .page-home .hero.hero--command,
    .page-home .portal-access,
    .page-home .section-shell,
    .page-home .public-insights-strip,
    .page-home .home-map-card {
        border-radius: 20px;
    }
}

[dir="rtl"] .page-home .portal-access__header {
    padding-inline-end: 0;
    padding-inline-start: 1rem;
}

[dir="rtl"] .page-home .hero__eyebrow::before {
    background: var(--p3-gold);
}

/* ==========================================================================
   PHASE 3 BATCH 1.1 - Visual refinement pass.
   Purpose: move the public shell/homepage closer to an official Morocco 2030
   tournament portal reference without changing markup hooks or backend logic.
   ========================================================================== */

:root {
    --p311-red: #b80f2d;
    --p311-burgundy: #4f0716;
    --p311-black: #05070d;
    --p311-gold: #d6b25c;
    --p311-cream: #fbf6ea;
    --p311-line-gold: rgba(214, 178, 92, 0.42);
    --p311-card-line: rgba(80, 55, 32, 0.12);
}

body.page-home {
    background:
        radial-gradient(circle at 50% -10%, rgba(184, 15, 45, 0.12), transparent 30%),
        radial-gradient(circle at 94% 10%, rgba(214, 178, 92, 0.16), transparent 24%),
        linear-gradient(180deg, #fff9ec 0%, #f6efe2 42%, #edf1f6 100%);
}

.site-header {
    background:
        linear-gradient(90deg, rgba(5, 7, 13, 0.98) 0%, rgba(18, 5, 13, 0.98) 48%, rgba(79, 7, 22, 0.98) 100%),
        radial-gradient(circle at 12% 0%, rgba(214, 178, 92, 0.14), transparent 28%);
    border-bottom-color: rgba(214, 178, 92, 0.34);
}

.site-header__inner {
    min-height: 4.15rem;
    padding-block: 0.5rem;
}

.brand {
    position: relative;
}

.brand::after {
    content: "";
    width: 1px;
    height: 2.45rem;
    margin-inline-start: 0.22rem;
    background: linear-gradient(180deg, transparent, rgba(214, 178, 92, 0.42), transparent);
}

.brand__mark {
    width: 3.22rem;
    height: 3.22rem;
    background:
        radial-gradient(circle at 34% 24%, rgba(255, 234, 174, 0.64), transparent 34%),
        conic-gradient(from 45deg, rgba(255, 255, 255, 0.12), transparent 12%, rgba(255, 255, 255, 0.1) 25%, transparent 36%),
        linear-gradient(145deg, #df203e 0%, var(--p311-red) 46%, var(--p311-burgundy) 100%);
    box-shadow:
        inset 0 0 0 3px rgba(255, 255, 255, 0.1),
        inset 0 0 0 6px rgba(214, 178, 92, 0.7),
        0 12px 28px rgba(184, 15, 45, 0.38);
}

.brand__mark::before {
    inset: 0.54rem;
    border-color: rgba(255, 239, 191, 0.52);
}

.brand__meta {
    font-size: clamp(1.1rem, 1.4vw, 1.34rem);
}

.brand__meta small {
    color: #f4d892;
}

.site-nav.site-nav--desktop {
    padding: 0.28rem;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.025)),
        rgba(5, 7, 13, 0.48);
    border-color: rgba(214, 178, 92, 0.18);
}

.site-nav.site-nav--desktop a {
    min-height: 2.14rem;
    padding-inline: clamp(0.58rem, 0.75vw, 0.86rem);
    color: rgba(255, 255, 255, 0.82);
}

.site-nav.site-nav--desktop a.is-active {
    position: relative;
    color: #fff;
    background:
        linear-gradient(135deg, rgba(184, 15, 45, 0.96), rgba(111, 13, 27, 0.96));
}

.site-nav.site-nav--desktop a.is-active::before {
    content: "";
    position: absolute;
    inset: auto 0.78rem -0.4rem;
    height: 2px;
    border-radius: 999px;
    background: linear-gradient(90deg, transparent, var(--p311-gold), transparent);
}

.site-header__utility-tools {
    padding: 0.18rem;
    border: 1px solid rgba(214, 178, 92, 0.12);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.035);
}

.shell-icon-button,
.header-auth__account {
    width: 2.24rem;
    height: 2.24rem;
}

.shell-icon-button {
    border-color: rgba(255, 255, 255, 0.16);
    background: rgba(255, 255, 255, 0.055);
}

.shell-icon-button:hover,
.shell-icon-button:focus-visible,
.shell-icon-button[aria-expanded="true"],
.shell-icon-button.is-active {
    background: rgba(214, 178, 92, 0.17);
    border-color: rgba(214, 178, 92, 0.66);
}

.header-auth__link {
    min-height: 2.24rem;
    height: 2.24rem;
    padding-inline: 0.86rem;
}

.header-auth__link--primary {
    background:
        linear-gradient(135deg, #e12442 0%, var(--p311-red) 48%, var(--p311-burgundy) 100%);
    border-color: rgba(214, 178, 92, 0.78);
    box-shadow: 0 10px 24px rgba(184, 15, 45, 0.42), inset 0 1px 0 rgba(255, 255, 255, 0.26);
}

.shell-panel {
    background:
        radial-gradient(circle at 100% 0%, rgba(214, 178, 92, 0.1), transparent 30%),
        rgba(255, 253, 248, 0.99);
}

.page-home .hero.hero--command {
    min-height: clamp(600px, 73vh, 780px);
    grid-template-columns: minmax(0, 1.12fr) minmax(340px, 0.72fr);
    background:
        linear-gradient(90deg, rgba(5, 7, 13, 0.2), rgba(5, 7, 13, 0.42)),
        radial-gradient(circle at 77% 16%, rgba(214, 178, 92, 0.3), transparent 18%),
        radial-gradient(circle at 15% 94%, rgba(184, 15, 45, 0.46), transparent 34%),
        linear-gradient(118deg, #05070d 0%, #0a1523 39%, #4f0716 100%);
}

.page-home .hero.hero--command::before {
    inset: 4% -6% auto auto;
    width: min(44vw, 620px);
    background:
        radial-gradient(circle, rgba(214, 178, 92, 0.18), transparent 58%),
        repeating-conic-gradient(from 45deg, rgba(214, 178, 92, 0.16) 0 7deg, transparent 7deg 18deg);
    opacity: 0.8;
}

.page-home .hero.hero--command::after {
    background:
        linear-gradient(90deg, rgba(255, 255, 255, 0.07), transparent 28%),
        linear-gradient(0deg, rgba(5, 7, 13, 0.28), transparent 44%),
        repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.028) 0 1px, transparent 1px 26px);
}

.page-home .hero__main {
    gap: clamp(0.78rem, 1.35vw, 1.08rem);
}

.page-home .hero__eyebrow {
    background: rgba(214, 178, 92, 0.1);
    border-color: rgba(214, 178, 92, 0.45);
    color: #ffe9ad;
}

.page-home .hero h1 {
    max-width: 10.5ch;
    font-size: clamp(3.35rem, 7.65vw, 7.4rem);
    text-shadow: 0 18px 42px rgba(0, 0, 0, 0.28);
}

.page-home .hero h1 span {
    color: #f7d884;
    letter-spacing: 0.32em;
}

.page-home .hero p {
    max-width: 45rem;
    color: rgba(255, 255, 255, 0.88);
}

.page-home .hero__actions .button {
    min-width: 11.5rem;
    background:
        linear-gradient(135deg, #e12442 0%, var(--p311-red) 52%, var(--p311-burgundy) 100%);
    border-color: rgba(214, 178, 92, 0.82);
}

.page-home .hero__actions .button--subtle {
    min-width: 9.4rem;
    border-color: rgba(214, 178, 92, 0.34);
}

.page-home .hero__actions .button--ghost {
    border-color: rgba(214, 178, 92, 0.36);
    background: rgba(214, 178, 92, 0.07);
}

.page-home .hero__quicklinks {
    background: rgba(5, 7, 13, 0.28);
    border-color: rgba(214, 178, 92, 0.18);
}

.page-home .hero__board {
    align-self: center;
    border-color: rgba(214, 178, 92, 0.34);
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.03)),
        rgba(5, 7, 13, 0.54);
}

.page-home .hero-countdown {
    background:
        linear-gradient(135deg, rgba(214, 178, 92, 0.22), rgba(184, 15, 45, 0.16) 52%, rgba(5, 7, 13, 0.5));
}

.page-home .hero-countdown__unit {
    border-color: rgba(214, 178, 92, 0.2);
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02)),
        rgba(5, 7, 13, 0.5);
}

.page-home .hero-countdown__unit strong {
    color: #fff8df;
}

.page-home .hero-feature {
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.075), rgba(255, 255, 255, 0.03));
}

.page-home .hero-feature--primary {
    background:
        linear-gradient(135deg, rgba(184, 15, 45, 0.2), rgba(214, 178, 92, 0.08) 58%),
        rgba(255, 255, 255, 0.055);
}

.page-home .hero__board .key-list > div {
    background: rgba(255, 255, 255, 0.055);
}

.page-home .portal-access,
.page-home .section-shell,
.page-home .home-map-card {
    background:
        radial-gradient(circle at 100% 0%, rgba(214, 178, 92, 0.1), transparent 28%),
        linear-gradient(180deg, #fffdf8, var(--p311-cream));
    border-color: var(--p311-card-line);
}

.page-home .portal-access {
    border-top: 3px solid rgba(214, 178, 92, 0.52);
}

.page-home .portal-access__header strong,
.page-home .section-header h2 {
    color: var(--p3-ink);
}

.page-home .section-header__eyebrow::before {
    background: linear-gradient(90deg, var(--p311-red), var(--p311-gold));
}

.page-home .quick-link-card,
.page-home .match-card,
.page-home .news-card,
.page-home .panel,
.page-home .partner-card,
.page-home .list-card {
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 251, 242, 0.98));
    border-color: rgba(20, 28, 39, 0.085);
}

.page-home .quick-link-card::before {
    background: linear-gradient(90deg, var(--p311-gold), var(--p311-red));
}

.page-home .match-card::before {
    background: linear-gradient(180deg, var(--p311-gold), var(--p311-red), var(--p311-burgundy));
}

.page-home .news-card__media {
    background:
        radial-gradient(circle at 80% 10%, rgba(214, 178, 92, 0.22), transparent 34%),
        linear-gradient(135deg, #05070d, #4f0716);
}

.page-home .public-insights-strip {
    border-color: rgba(214, 178, 92, 0.3);
    background:
        radial-gradient(circle at 92% 0%, rgba(214, 178, 92, 0.24), transparent 25%),
        linear-gradient(135deg, #05070d 0%, #071a2c 58%, #4f0716 100%);
}

@media (max-width: 1240px) {
    .brand::after {
        display: none;
    }

    .site-nav.site-nav--desktop a {
        padding-inline: 0.52rem;
    }
}

@media (max-width: 1100px) {
    .site-header__utility-tools {
        border-color: transparent;
        background: transparent;
        padding: 0;
    }
}

@media (max-width: 700px) {
    .site-header__inner {
        min-height: 3.7rem;
    }

    .page-home .hero.hero--command {
        min-height: 0;
        background:
            radial-gradient(circle at 80% 6%, rgba(214, 178, 92, 0.18), transparent 28%),
            linear-gradient(145deg, #05070d 0%, #111a28 46%, #4f0716 100%);
    }

    .page-home .hero h1 {
        line-height: 0.94;
    }
}

/* ==========================================================================
   PHASE 3 RESPONSIVE FIX PASS.
   Focus: layout correctness across desktop, laptop, tablet, and mobile.
   This section intentionally avoids new decoration and protects existing hooks.
   ========================================================================== */

/* Global overflow safeguards */

*,
*::before,
*::after {
    box-sizing: border-box;
}

html,
body {
    max-width: 100%;
    overflow-x: clip;
}

img,
svg,
video,
canvas,
iframe {
    max-width: 100%;
}

.page-container,
.site-header,
.site-header__inner,
.site-header__utility-tools,
.shell-popovers,
.page-header,
.page-header__content,
.section-shell,
.panel,
.auth-shell,
.auth-card,
.auth-grid,
.map-layout,
.map-shell,
.map-lists,
.knockout-shell,
.match-card,
.news-card,
.entity-card,
.partner-card,
.list-card {
    min-width: 0;
}

.page-header h1,
.section-header h2,
.section-header h3,
.hero h1,
.brand__meta,
.match-card__team strong,
.news-card__title,
.entity-card h2,
.entity-card h3,
.list-card a,
.map-location-card__title {
    overflow-wrap: break-word;
}

.button,
.button--subtle,
.button--ghost,
.match-card__cta,
.header-auth__link,
.shell-menu-link {
    max-width: 100%;
    white-space: normal;
    text-align: center;
}

/* Header */

@media (max-width: 1280px) {
    .site-header__inner {
        grid-template-columns: minmax(150px, auto) minmax(0, 1fr) auto;
        width: min(calc(100% - 24px), 1360px);
    }

    .site-nav.site-nav--desktop {
        justify-self: stretch;
        width: 100%;
    }

    .site-nav.site-nav--desktop a {
        padding-inline: clamp(0.42rem, 0.55vw, 0.62rem);
        letter-spacing: 0.035em;
    }
}

@media (max-width: 1080px) {
    .site-header__inner {
        grid-template-columns: minmax(0, auto) auto;
    }

    .site-nav.site-nav--desktop {
        display: none;
    }

    .shell-menu-button {
        display: inline-grid;
    }

    .site-header__utility-tools {
        justify-self: end;
    }
}

@media (max-width: 640px) {
    .site-header__inner,
    .shell-popovers,
    .page-container {
        width: min(calc(100% - 20px), 1360px);
    }

    .site-header__utility-tools {
        gap: 0.2rem;
    }

    .shell-icon-button,
    .header-auth__account {
        width: 2.08rem;
        height: 2.08rem;
    }

    .brand {
        gap: 0.55rem;
    }

    .brand__mark {
        width: 2.42rem;
        height: 2.42rem;
    }
}

@media (max-width: 480px) {
    .brand__meta {
        font-size: 0.96rem;
        max-width: 8.5rem;
    }

    .brand__meta small {
        display: none;
    }

    .shell-icon-button--language,
    .site-header__divider {
        display: none;
    }
}

@media (max-width: 390px) {
    .site-header__inner,
    .shell-popovers,
    .page-container {
        width: min(calc(100% - 16px), 1360px);
    }

    .brand__mark {
        width: 2.25rem;
        height: 2.25rem;
    }

    .brand__meta {
        max-width: 7.4rem;
        font-size: 0.88rem;
    }

    .shell-icon-button,
    .header-auth__account {
        width: 1.96rem;
        height: 1.96rem;
    }
}

/* Hero */

@media (max-width: 1024px) {
    .page-home .hero.hero--command {
        min-height: 0;
        grid-template-columns: 1fr;
        padding: clamp(1.35rem, 4vw, 2.4rem);
    }

    .page-home .hero__main {
        max-width: 100%;
    }

    .page-home .hero__board {
        align-self: stretch;
    }
}

@media (max-width: 768px) {
    .page-home .hero.hero--command {
        gap: 1rem;
        margin-top: 0.8rem;
    }

    .page-home .hero h1 {
        max-width: 100%;
        font-size: clamp(2.6rem, 12vw, 4.2rem);
        letter-spacing: -0.055em;
    }

    .page-home .hero h1 span {
        letter-spacing: 0.18em;
    }

    .page-home .hero p {
        font-size: 0.95rem;
        line-height: 1.58;
    }

    .page-home .hero__board {
        padding: 0.85rem;
    }
}

@media (max-width: 640px) {
    .page-home .hero.hero--command {
        padding: 1.1rem;
    }

    .page-home .hero__eyebrow {
        font-size: 0.62rem;
        letter-spacing: 0.14em;
    }

    .page-home .hero__actions {
        display: grid;
        grid-template-columns: 1fr;
        width: 100%;
    }

    .page-home .hero__actions .button,
    .page-home .hero__actions .button--subtle,
    .page-home .hero__actions .button--ghost {
        width: 100%;
        min-width: 0;
        justify-content: center;
    }

    .page-home .hero-countdown {
        padding: 0.85rem;
    }

    .page-home .hero-countdown__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-home .hero__board .key-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 480px) {
    .page-home .hero h1 {
        font-size: clamp(2.25rem, 13vw, 3.35rem);
    }

    .page-home .hero h1 span {
        font-size: 0.72rem;
        letter-spacing: 0.14em;
    }

    .page-home .hero-countdown__grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.3rem;
    }

    .page-home .hero-countdown__unit {
        min-height: 3.15rem;
        padding: 0.45rem 0.24rem;
    }

    .page-home .hero-countdown__unit strong {
        font-size: clamp(1.08rem, 7vw, 1.35rem);
    }

    .page-home .hero-countdown__unit span {
        font-size: 0.48rem;
        letter-spacing: 0.06em;
    }

    .page-home .hero__board .key-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-home .hero-feature__rows {
        display: grid;
        gap: 0.4rem;
    }
}

/* Cards and public sections */

@media (max-width: 1024px) {
    .card-grid,
    .listing-grid,
    .team-grid,
    .city-grid,
    .round-grid,
    .quick-links-grid,
    .search-groups {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .split-grid,
    .map-layout,
    .account-layout {
        grid-template-columns: 1fr;
    }

    .page-home .portal-access,
    .page-home .portal-access__links {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .page-section {
        margin-top: clamp(1rem, 4vw, 1.45rem);
    }

    .section-shell,
    .panel,
    .page-home .section-shell,
    .page-home .portal-access,
    .page-home .public-insights-strip,
    .page-home .home-map-card {
        padding: clamp(0.95rem, 4vw, 1.25rem);
        border-radius: 18px;
    }

    .section-header,
    .page-header__actions,
    .match-card__head,
    .match-card__meta,
    .badge-row {
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .section-header {
        flex-direction: column;
    }

    .section-header h2,
    .page-header h1 {
        font-size: clamp(1.45rem, 7vw, 2.15rem);
    }

    .match-card__teams {
        gap: 0.75rem;
    }
}

@media (max-width: 640px) {
    .card-grid,
    .listing-grid,
    .team-grid,
    .city-grid,
    .round-grid,
    .quick-links-grid,
    .search-groups,
    .page-home .portal-access,
    .page-home .portal-access__links,
    .page-home .card-grid,
    .page-home .listing-grid,
    .page-home .team-grid,
    .page-home .city-grid {
        grid-template-columns: 1fr;
    }

    .page-header {
        padding: clamp(1rem, 5vw, 1.35rem);
        border-radius: 18px;
    }

    .page-header p {
        font-size: 0.9rem;
    }

    .page-header__actions,
    .page-header__actions .button,
    .page-header__actions .button--subtle,
    .page-header__actions .button--ghost {
        width: 100%;
    }
}

/* Auth */

@media (max-width: 900px) {
    .auth-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .auth-shell {
        margin-top: 1rem;
    }

    .auth-card {
        padding: 1rem;
        border-radius: 18px;
    }

    .auth-form,
    .field-group,
    .form-actions {
        width: 100%;
    }

    .form-control,
    .auth-form input,
    .auth-form select,
    .auth-form textarea,
    .auth-form .button {
        width: 100%;
    }

    .form-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.65rem;
    }
}

/* Tables and standings */

.table-shell {
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
}

.table-shell table {
    min-width: 620px;
}

@media (max-width: 640px) {
    .table-shell {
        border-radius: 14px;
    }

    .table-shell table {
        min-width: 560px;
        font-size: 0.78rem;
    }

    .standings-table__team {
        min-width: 10rem;
    }
}

/* Map and knockout shell */

.map-layout,
.knockout-scroll,
.knockout-fit-viewport {
    max-width: 100%;
}

.knockout-scroll,
.knockout-fit-viewport {
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
}

.map-command-bar,
.map-filter,
.map-overview,
.map-location-card__header {
    min-width: 0;
}

@media (max-width: 1024px) {
    .map-layout {
        grid-template-columns: 1fr;
    }

    .map-command-bar {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    .map-filter {
        display: flex;
        max-width: 100%;
        overflow-x: auto;
        padding-bottom: 0.1rem;
        -webkit-overflow-scrolling: touch;
    }

    .map-filter__button {
        flex: 0 0 auto;
    }

    .map-overview {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .map-shell,
    .map-lists .panel,
    .knockout-shell {
        padding: clamp(0.85rem, 4vw, 1.1rem);
        border-radius: 18px;
    }

    .map-canvas {
        min-height: clamp(20rem, 62vw, 27rem);
    }

    .knockout-command {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    .knockout-command__stats,
    .knockout-legend {
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .map-overview {
        grid-template-columns: 1fr;
    }

    .map-canvas {
        min-height: 18rem;
    }

    .map-canvas__hud {
        max-width: calc(100% - 1rem);
    }

    .knockout-page-section {
        margin-top: 0.85rem;
    }
}

/* ==========================================================================
   PHASE 3 RESPONSIVE PATCH 1.1.
   Small targeted mobile fixes for search, homepage essentials, knockout,
   city placeholders, and visible mobile polish.
   ========================================================================== */

/* Search page */

.search-shell .search-form {
    align-items: end;
    min-width: 0;
}

.search-shell .search-form__field,
.search-shell .search-form__field input[type="search"] {
    min-width: 0;
}

.search-shell .search-form__field input[type="search"] {
    border-radius: 16px;
}

.search-shell .search-form > .button {
    border-radius: 16px;
    white-space: nowrap;
}

@media (max-width: 640px) {
    .search-shell {
        gap: 1rem;
    }

    .search-shell .search-form {
        grid-template-columns: 1fr;
        align-items: stretch;
        gap: 0.72rem;
        padding: 0.9rem;
        border-radius: 18px;
    }

    .search-shell .search-form__field {
        gap: 0.38rem;
    }

    .search-shell .search-form__field label {
        font-size: 0.72rem;
        color: var(--p3-ink);
    }

    .search-shell .search-form__field input[type="search"] {
        width: 100%;
        min-height: 2.9rem;
        padding: 0.72rem 0.86rem;
        border-radius: 12px;
        font-size: 0.95rem;
        background: #fff;
    }

    .search-shell .search-form > .button {
        width: 100%;
        min-height: 2.9rem;
        border-radius: 12px;
    }

    .search-summary {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.42rem;
    }
}

/* Homepage essentials contrast */

.page-home .portal-access {
    color: var(--p3-ink);
    background:
        radial-gradient(circle at 100% 0%, rgba(214, 178, 92, 0.12), transparent 28%),
        linear-gradient(180deg, #fffdf8 0%, #fbf1df 100%);
}

.page-home .portal-access__header span {
    color: #8f1027;
}

.page-home .portal-access__header strong {
    color: #151c27;
}

.page-home .portal-access .quick-link-card strong {
    color: #151c27;
}

.page-home .portal-access .quick-link-card p {
    color: #536173;
}

@media (max-width: 640px) {
    .page-home .portal-access__header {
        padding-bottom: 0.7rem;
    }
}

/* Knockout mobile scroll affordance */

.knockout-scroll,
.knockout-fit-viewport {
    scroll-padding-inline: 1rem;
}

@media (max-width: 768px) {
    .knockout-scroll,
    .knockout-fit-viewport {
        padding: 0.42rem 0.42rem 0.72rem;
        border-radius: 16px;
        background:
            linear-gradient(90deg, rgba(214, 178, 92, 0.14), transparent 1.2rem),
            linear-gradient(270deg, rgba(214, 178, 92, 0.14), transparent 1.2rem),
            rgba(255, 255, 255, 0.34);
    }

    .knockout-command h1 {
        font-size: clamp(1.45rem, 8vw, 2.1rem);
        line-height: 1;
    }

    .knockout-command__stats {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .knockout-command {
        margin-bottom: 0.6rem;
    }

    .knockout-legend {
        gap: 0.45rem;
        font-size: 0.72rem;
    }
}

/* City/archive cards: reduce empty placeholder height on mobile */

@media (max-width: 640px) {
    .page-inner .listing-grid .entity-card__media {
        aspect-ratio: 16 / 7.5;
        max-height: 8.75rem;
    }

    .page-inner .listing-grid .entity-card__media .placeholder-badge {
        width: 3rem;
        height: 3rem;
        font-size: 1rem;
        letter-spacing: 0.05em;
    }

    .page-inner .listing-grid .entity-card__body {
        padding: 0.9rem;
    }
}

@media (max-width: 480px) {
    .page-inner .listing-grid .entity-card__media {
        aspect-ratio: 16 / 6.8;
        max-height: 7.4rem;
    }
}

/* ==========================================================================
   PHASE 3 INNER PAGES VISUAL CONSISTENCY.
   Scope: public listing/detail surfaces only. Backend, hooks, map, knockout,
   auth, and sports logic are intentionally untouched.
   ========================================================================== */

body.page-inner {
    background:
        radial-gradient(circle at 8% 2%, rgba(177, 15, 46, 0.09), transparent 25rem),
        radial-gradient(circle at 92% 8%, rgba(201, 164, 76, 0.12), transparent 23rem),
        linear-gradient(180deg, #fbf7ee 0%, #f4efe6 38%, #eef2f6 100%);
}

.page-inner .page-header {
    position: relative;
    overflow: hidden;
    margin-top: clamp(1rem, 2.4vw, 1.55rem);
    padding: clamp(1.25rem, 3vw, 2rem);
    border: 1px solid rgba(201, 164, 76, 0.32);
    border-radius: 26px;
    background:
        linear-gradient(135deg, rgba(4, 9, 17, 0.92), rgba(80, 9, 23, 0.94)),
        radial-gradient(circle at 88% 0%, rgba(201, 164, 76, 0.22), transparent 34%);
    box-shadow: 0 20px 52px rgba(8, 15, 26, 0.18);
    color: #fff;
}

.page-inner .page-header::before {
    content: "";
    position: absolute;
    inset: -42% auto auto 58%;
    width: 24rem;
    aspect-ratio: 1;
    border: 1px solid rgba(201, 164, 76, 0.24);
    transform: rotate(45deg);
    opacity: 0.38;
    pointer-events: none;
}

.page-inner .page-header::after {
    content: "";
    position: absolute;
    inset: auto 1.35rem 0 1.35rem;
    height: 3px;
    border-radius: 999px 999px 0 0;
    background: linear-gradient(90deg, var(--p3-red-deep), var(--p3-red), var(--p3-gold), var(--p3-green));
    opacity: 0.95;
}

.page-inner .page-header__content {
    position: relative;
    z-index: 1;
    max-width: 58rem;
}

.page-inner .page-header__eyebrow,
.page-inner .section-header__eyebrow {
    color: #9f122c;
    font-size: 0.68rem;
    font-weight: 900;
    letter-spacing: 0.18em;
    text-transform: uppercase;
}

.page-inner .page-header__eyebrow {
    color: rgba(231, 196, 108, 0.98);
}

.page-inner .page-header h1 {
    margin-top: 0.32rem;
    color: #fff;
    font-size: clamp(2rem, 4vw, 3.4rem);
    letter-spacing: -0.055em;
    line-height: 0.98;
}

.page-inner .page-header p {
    max-width: 45rem;
    color: rgba(255, 255, 255, 0.78);
    font-size: clamp(0.95rem, 1.15vw, 1.08rem);
}

.page-inner .page-section {
    padding-block: clamp(1.2rem, 2.4vw, 2rem);
}

.page-inner .section-shell,
.page-inner .search-shell,
.page-inner .detail-shell,
.page-inner .panel,
.page-inner .map-shell,
.page-inner .map-lists .panel {
    border: 1px solid rgba(111, 13, 27, 0.1);
    background:
        linear-gradient(180deg, rgba(255, 253, 248, 0.96), rgba(255, 249, 238, 0.9)),
        radial-gradient(circle at 100% 0%, rgba(201, 164, 76, 0.1), transparent 24%);
    box-shadow: 0 16px 42px rgba(10, 18, 28, 0.07);
}

.page-inner .section-header h2,
.page-inner .section-header h3,
.page-inner .panel h2,
.page-inner .panel h3 {
    color: #111827;
    letter-spacing: -0.035em;
}

.page-inner .section-header p,
.page-inner .panel p,
.page-inner .detail-copy,
.page-inner .detail-copy p {
    color: #55606f;
}

.page-inner .card-grid,
.page-inner .listing-grid,
.page-inner .team-grid,
.page-inner .quick-links-grid {
    gap: clamp(0.9rem, 1.6vw, 1.2rem);
}

.page-inner .news-card,
.page-inner .match-card,
.page-inner .entity-card,
.page-inner .partner-card,
.page-inner .result-card,
.page-inner .quick-link-card,
.page-inner .list-card,
.page-inner .related-story {
    border: 1px solid rgba(111, 13, 27, 0.1);
    border-radius: 20px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 251, 244, 0.94));
    box-shadow: 0 13px 30px rgba(13, 22, 36, 0.065);
}

.page-inner .news-card:hover,
.page-inner .match-card:hover,
.page-inner .entity-card:hover,
.page-inner .partner-card:hover,
.page-inner .result-card:hover,
.page-inner .quick-link-card:hover,
.page-inner .map-location-card:hover {
    transform: translateY(-2px);
    border-color: rgba(177, 15, 46, 0.22);
    box-shadow: 0 18px 36px rgba(13, 22, 36, 0.1);
}

.page-inner .news-card__media,
.page-inner .entity-card__media {
    background:
        radial-gradient(circle at 76% 16%, rgba(201, 164, 76, 0.24), transparent 34%),
        linear-gradient(135deg, #7b1023, #111927 72%);
}

.page-inner .news-card__media {
    aspect-ratio: 16 / 9.2;
}

.page-inner .news-card__body,
.page-inner .entity-card__body,
.page-inner .partner-card__body {
    padding: clamp(0.92rem, 1.45vw, 1.18rem);
}

.page-inner .news-card__title {
    font-size: clamp(1rem, 1.15vw, 1.12rem);
    line-height: 1.25;
}

.page-inner .news-card__title a,
.page-inner .entity-card h3 a,
.page-inner .result-card strong a,
.page-inner .related-story a,
.page-inner .map-location-card__title {
    color: #111827;
    text-decoration-color: rgba(177, 15, 46, 0.24);
    text-underline-offset: 0.18em;
}

.page-inner .news-card__title a:hover,
.page-inner .entity-card h3 a:hover,
.page-inner .result-card strong a:hover,
.page-inner .related-story a:hover,
.page-inner .map-location-card__title:hover {
    color: #9f122c;
}

.page-inner .news-card__summary {
    color: #5e6876;
    font-size: 0.9rem;
    line-height: 1.55;
}

.page-inner .news-card__footer,
.page-inner .section-link,
.page-inner .map-focus-link {
    color: #8e1028;
    font-weight: 900;
}

.page-inner .match-feed {
    gap: 0.88rem;
}

.page-inner .match-card {
    padding: clamp(0.92rem, 1.35vw, 1.12rem);
}

.page-inner .match-card::before {
    background: linear-gradient(180deg, var(--p3-gold), var(--p3-red), var(--p3-red-deep));
}

.page-inner .match-card__head,
.page-inner .match-card__meta {
    gap: 0.62rem;
}

.page-inner .match-card__competition {
    gap: 0.38rem;
}

.page-inner .match-card__teams {
    align-items: center;
    gap: clamp(0.78rem, 1.7vw, 1.2rem);
}

.page-inner .match-card__team strong {
    color: #111827;
    font-size: clamp(1rem, 1.2vw, 1.18rem);
    line-height: 1.18;
}

.page-inner .match-card__label,
.page-inner .match-card__date {
    color: #6b7280;
    font-size: 0.72rem;
}

.page-inner .score-box {
    min-width: 4.8rem;
    border: 1px solid rgba(201, 164, 76, 0.36);
    background: linear-gradient(135deg, #111827, #621124);
    color: #fff;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.06);
}

.page-inner .match-card__meta {
    margin-top: 0.78rem;
    padding-top: 0.72rem;
    border-top-color: rgba(111, 13, 27, 0.1);
}

.page-inner .match-card__cta,
.page-inner .button--subtle,
.page-inner .button {
    border-radius: 999px;
}

.page-inner .match-card__cta {
    background: rgba(177, 15, 46, 0.08);
    color: #8e1028;
}

.page-inner .match-card__cta:hover,
.page-inner .button--subtle:hover {
    border-color: rgba(177, 15, 46, 0.22);
    background: rgba(177, 15, 46, 0.12);
}

.page-inner .meta-pill,
.page-inner .badge,
.page-inner .status-pill {
    border-color: rgba(111, 13, 27, 0.12);
    background: rgba(255, 255, 255, 0.72);
}

.page-inner .status-pill--completed {
    border-color: rgba(13, 107, 69, 0.22);
    color: #0b5d3a;
    background: rgba(13, 107, 69, 0.1);
}

.page-inner .status-pill--live {
    border-color: rgba(177, 15, 46, 0.3);
    color: #9f122c;
    background: rgba(177, 15, 46, 0.1);
}

.page-inner .entity-card__media,
.page-inner .partner-card .entity-card__media {
    aspect-ratio: 16 / 8.8;
}

.page-inner .placeholder-badge {
    border: 1px solid rgba(201, 164, 76, 0.42);
    background:
        radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.28), transparent 34%),
        linear-gradient(135deg, #b10f2e, #6f0d1b);
    color: #fff;
    box-shadow: 0 10px 24px rgba(111, 13, 27, 0.22);
}

.page-inner .detail-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(18rem, 24rem);
    gap: clamp(1rem, 2vw, 1.5rem);
    align-items: start;
}

.page-inner .detail-media {
    overflow: hidden;
    border-radius: 22px;
    background: linear-gradient(135deg, #7b1023, #111927);
}

.page-inner .detail-meta,
.page-inner .badge-row,
.page-inner .result-card__meta {
    gap: 0.42rem;
}

.page-inner .article-summary-box {
    border-inline-start: 4px solid var(--p3-red);
    background: rgba(177, 15, 46, 0.065);
    color: #242f3e;
}

.page-inner .detail-copy {
    font-size: clamp(0.98rem, 1.05vw, 1.06rem);
    line-height: 1.8;
}

.page-inner .search-form {
    border: 1px solid rgba(111, 13, 27, 0.11);
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 249, 238, 0.9));
}

.page-inner .result-list {
    gap: 0.7rem;
}

.page-inner .result-card {
    padding: 0.95rem 1rem;
}

.page-inner .result-card strong {
    display: block;
    margin-top: 0.25rem;
    font-size: 1.02rem;
    line-height: 1.25;
}

.page-inner .result-card p {
    margin-top: 0.38rem;
    color: #5f6977;
    line-height: 1.5;
}

.page-inner .empty-state {
    border: 1px dashed rgba(177, 15, 46, 0.22);
    border-radius: 22px;
    background:
        radial-gradient(circle at 100% 0%, rgba(201, 164, 76, 0.14), transparent 28%),
        rgba(255, 253, 248, 0.82);
}

.page-inner .empty-state__mark {
    border-color: rgba(201, 164, 76, 0.38);
    background: linear-gradient(135deg, #111827, #7b1023);
    color: #fff;
}

.page-inner .standings-table {
    border-radius: 18px;
    border-color: rgba(111, 13, 27, 0.1);
    background: #fffefb;
}

.page-inner .standings-table table {
    border-collapse: separate;
    border-spacing: 0;
}

.page-inner .standings-table th {
    background: #121926;
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.68rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.page-inner .standings-table td {
    border-bottom-color: rgba(111, 13, 27, 0.07);
}

.page-inner .standings-table tbody tr:hover td {
    background: rgba(177, 15, 46, 0.035);
}

.page-inner .standings-table__team a {
    color: #111827;
    font-weight: 900;
}

.page-inner .standings-table__points strong,
.page-inner .rank-pill {
    color: #8e1028;
}

.page-inner .map-command-bar,
.page-inner .map-overview__card,
.page-inner .map-detail-card,
.page-inner .map-section-panel,
.page-inner .map-location-card {
    border-color: rgba(111, 13, 27, 0.11);
}

.page-inner .map-overview__card strong {
    color: #8e1028;
}

.page-inner .map-detail-card__visual {
    box-shadow: inset 0 0 0 1px rgba(201, 164, 76, 0.42), 0 18px 38px rgba(111, 13, 27, 0.14);
}

.page-inner .map-filter__button.is-active {
    background: linear-gradient(135deg, var(--p3-red), var(--p3-red-deep));
    color: #fff;
}

.page-inner .pagination-shell {
    margin-top: clamp(1rem, 2vw, 1.35rem);
    justify-content: center;
}

.page-inner .pagination-shell a,
.page-inner .pagination-shell span {
    border-radius: 999px;
    border-color: rgba(111, 13, 27, 0.12);
    background: rgba(255, 255, 255, 0.8);
}

.page-inner .pagination-shell .is-active {
    border-color: rgba(177, 15, 46, 0.3);
    background: linear-gradient(135deg, var(--p3-red), var(--p3-red-deep));
    color: #fff;
}

@media (max-width: 1024px) {
    .page-inner .detail-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .page-inner .page-header {
        margin-top: 0.85rem;
        border-radius: 22px;
    }

    .page-inner .page-section {
        padding-block: 1rem;
    }

    .page-inner .match-card__teams {
        grid-template-columns: minmax(0, 1fr);
    }

    .page-inner .score-box {
        width: 100%;
        min-width: 0;
        justify-content: center;
    }

    .page-inner .match-feed .match-card__teams {
        grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
        text-align: center;
        gap: 0.48rem;
    }

    .page-inner .match-feed .score-box {
        width: auto;
        min-width: 3.7rem;
        padding-inline: 0.46rem;
    }

    .page-inner .match-feed .match-card__team strong {
        font-size: clamp(0.86rem, 3.4vw, 1rem);
    }

    .page-inner .match-feed .match-card__label {
        font-size: 0.62rem;
    }
}

@media (max-width: 640px) {
    .page-inner .card-grid,
    .page-inner .listing-grid,
    .page-inner .team-grid,
    .page-inner .quick-links-grid {
        gap: 0.82rem;
    }

    .page-inner .news-card__body,
    .page-inner .entity-card__body,
    .page-inner .partner-card__body,
    .page-inner .result-card,
    .page-inner .match-card {
        padding: 0.88rem;
    }

    .page-inner .page-header h1 {
        font-size: clamp(1.7rem, 10vw, 2.35rem);
    }

    .page-inner .page-header p,
    .page-inner .news-card__summary,
    .page-inner .result-card p {
        font-size: 0.9rem;
    }

    .page-inner .standings-table th,
    .page-inner .standings-table td {
        padding: 0.66rem 0.58rem;
    }
}

/* ==========================================================================
   PHASE 3 PUBLIC DETAIL PAGES FINE POLISH.
   Scope: public detail/account/match-centre surfaces only. Forms, hooks,
   auth, routes, Leaflet, sports calculations, and match data logic untouched.
   ========================================================================== */

.page-inner .detail-grid {
    margin-top: clamp(0.3rem, 0.8vw, 0.8rem);
}

.page-inner .detail-grid > .section-stack,
.page-inner .match-centre-grid > .section-stack,
.page-inner .match-centre-grid > aside,
.page-inner .account-layout > .section-stack {
    gap: clamp(0.82rem, 1.5vw, 1.15rem);
}

.page-inner .detail-media {
    position: relative;
    min-height: clamp(13rem, 31vw, 23rem);
    max-height: 25rem;
    border: 1px solid rgba(201, 164, 76, 0.28);
    box-shadow: 0 18px 44px rgba(10, 18, 28, 0.13);
}

.page-inner .detail-media::after {
    content: "";
    position: absolute;
    inset: 0;
    background:
        linear-gradient(180deg, transparent 58%, rgba(6, 11, 20, 0.26)),
        radial-gradient(circle at 100% 0%, rgba(201, 164, 76, 0.14), transparent 32%);
    pointer-events: none;
}

.page-inner .detail-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
}

.page-inner .detail-copy {
    color: #3e4755;
}

.page-inner .detail-copy a,
.page-inner .meta-list a,
.page-inner .account-inline-list a {
    color: #8e1028;
    font-weight: 800;
}

.page-inner .meta-list,
.page-inner .meta-list[style] {
    margin-top: 0;
    padding: 0.82rem 0;
    border-top: 1px solid rgba(111, 13, 27, 0.09);
}

.page-inner .meta-list:first-of-type {
    border-top: 0;
    padding-top: 0.35rem;
}

.page-inner .meta-list strong {
    color: #7d8795;
    font-size: 0.68rem;
    font-weight: 900;
    letter-spacing: 0.13em;
    text-transform: uppercase;
}

.page-inner .meta-list div,
.page-inner .meta-list a {
    color: #182130;
    font-size: 0.94rem;
    line-height: 1.35;
}

.page-inner .detail-grid .list-card {
    padding: 0.88rem 0.95rem;
}

.page-inner .detail-grid .list-card small,
.page-inner .account-inline-list .list-card small {
    color: #7d8795;
    font-size: 0.68rem;
    font-weight: 900;
    letter-spacing: 0.11em;
    text-transform: uppercase;
}

.page-inner .detail-grid .list-card p,
.page-inner .account-inline-list .list-card p {
    color: #5f6977;
    line-height: 1.48;
}

.page-inner .match-centre-hero {
    margin-top: clamp(1rem, 2vw, 1.4rem);
    padding: clamp(1rem, 2.4vw, 1.55rem);
    border: 1px solid rgba(201, 164, 76, 0.28);
    border-radius: 26px;
    background:
        radial-gradient(circle at 50% 0%, rgba(201, 164, 76, 0.15), transparent 31%),
        linear-gradient(135deg, #050b14 0%, #180714 48%, #741124 100%);
    box-shadow: 0 22px 55px rgba(5, 11, 20, 0.22);
}

.page-inner .match-centre-hero__topline {
    gap: 0.48rem;
    color: rgba(255, 255, 255, 0.74);
    font-size: 0.7rem;
    letter-spacing: 0.14em;
}

.page-inner .match-status-pill {
    border: 1px solid rgba(255, 255, 255, 0.18);
    box-shadow: none;
}

.page-inner .match-status-pill--live {
    background: #b10f2e;
}

.page-inner .match-status-pill--completed {
    background: rgba(13, 107, 69, 0.82);
}

.page-inner .match-status-pill--scheduled {
    background: rgba(201, 164, 76, 0.2);
    color: #ffe8ad;
}

.page-inner .match-scoreboard {
    gap: clamp(0.8rem, 2vw, 1.4rem);
    margin-top: clamp(0.9rem, 2vw, 1.35rem);
}

.page-inner .match-scoreboard__team {
    min-width: 0;
    padding: 0.78rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.055);
}

.page-inner .match-scoreboard__crest {
    width: clamp(2.55rem, 5vw, 3.25rem);
    height: clamp(2.55rem, 5vw, 3.25rem);
    border-color: rgba(201, 164, 76, 0.42);
    background:
        radial-gradient(circle at 35% 25%, rgba(255, 255, 255, 0.26), transparent 34%),
        linear-gradient(135deg, #b10f2e, #6f0d1b);
    color: #fff;
}

.page-inner .match-scoreboard__team strong {
    color: #fff;
    font-size: clamp(1rem, 2.1vw, 1.55rem);
    line-height: 1.06;
}

.page-inner .match-scoreboard__team span:not(.match-scoreboard__crest) {
    color: rgba(255, 255, 255, 0.62);
}

.page-inner .match-scoreboard__score {
    min-width: clamp(7.2rem, 13vw, 10rem);
    padding: 0.8rem 1rem;
    border: 1px solid rgba(201, 164, 76, 0.38);
    border-radius: 20px;
    background:
        radial-gradient(circle at 50% 0%, rgba(201, 164, 76, 0.2), transparent 48%),
        rgba(255, 255, 255, 0.08);
}

.page-inner .match-scoreboard__score strong {
    color: #fff;
    font-size: clamp(2rem, 4.4vw, 3.55rem);
    line-height: 0.92;
}

.page-inner .match-scoreboard__score span {
    color: rgba(255, 232, 173, 0.78);
    font-size: 0.7rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.page-inner .match-centre-meta {
    gap: 0.58rem;
}

.page-inner .match-centre-meta > div,
.page-inner .match-centre-notice {
    border-color: rgba(255, 255, 255, 0.11);
    background: rgba(255, 255, 255, 0.055);
}

.page-inner .match-centre-grid {
    gap: clamp(1rem, 2vw, 1.35rem);
    padding-top: clamp(0.9rem, 1.5vw, 1.2rem);
}

.page-inner .match-centre-panel {
    border-color: rgba(111, 13, 27, 0.1);
}

.page-inner .match-timeline {
    gap: 0.58rem;
}

.page-inner .match-timeline::before {
    background: linear-gradient(180deg, rgba(201, 164, 76, 0.6), rgba(177, 15, 46, 0.18));
}

.page-inner .match-timeline__item {
    padding: 0.72rem;
    border: 1px solid rgba(111, 13, 27, 0.08);
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.72);
}

.page-inner .match-timeline__minute strong {
    color: #8e1028;
}

.page-inner .match-timeline__icon {
    box-shadow: 0 7px 16px rgba(111, 13, 27, 0.16);
}

.page-inner .match-timeline__head strong {
    color: #111827;
}

.page-inner .match-stat-context,
.page-inner .match-lineup-card {
    border-color: rgba(111, 13, 27, 0.1);
    border-radius: 18px;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 249, 238, 0.74));
}

.page-inner .match-stat-context h3,
.page-inner .match-lineup-card h3 {
    color: #111827;
    letter-spacing: -0.025em;
}

.page-inner .match-stat-row__values strong {
    color: #111827;
}

.page-inner .match-stat-row__values span {
    color: #6c7481;
    font-weight: 800;
}

.page-inner .match-stat-row__bars span::after {
    background: linear-gradient(90deg, #b10f2e, #c9a44c);
}

.page-inner .match-stat-row__bars span:last-child::after {
    background: linear-gradient(270deg, #6f0d1b, #c9a44c);
}

.page-inner .match-lineup-list li {
    border-color: rgba(111, 13, 27, 0.08);
    background: rgba(255, 255, 255, 0.74);
}

.page-inner .match-lineup-list__number {
    border-color: rgba(201, 164, 76, 0.28);
    background: rgba(201, 164, 76, 0.12);
    color: #8e1028;
}

.page-inner .match-lineup-empty {
    border-color: rgba(111, 13, 27, 0.08);
    background: rgba(255, 255, 255, 0.62);
}

.page-inner .account-layout {
    gap: clamp(1rem, 2vw, 1.4rem);
    align-items: start;
}

.page-inner .account-sidebar {
    position: sticky;
    top: 5.25rem;
}

.page-inner .account-panel {
    border-color: rgba(111, 13, 27, 0.1);
    border-radius: 22px;
}

.page-inner .account-panel h2 {
    color: #111827;
    letter-spacing: -0.035em;
}

.page-inner .account-nav {
    gap: 0.42rem;
    margin-top: 1rem;
}

.page-inner .account-nav__link {
    border: 1px solid rgba(111, 13, 27, 0.1);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.72);
    color: #293344;
    font-weight: 850;
}

.page-inner .account-nav__link:hover {
    border-color: rgba(177, 15, 46, 0.24);
    color: #8e1028;
}

.page-inner .account-nav__link.is-active {
    border-color: rgba(201, 164, 76, 0.34);
    background: linear-gradient(135deg, #b10f2e, #6f0d1b);
    color: #fff;
}

.page-inner .account-stat {
    display: grid;
    align-content: center;
    min-height: 7.1rem;
    border-color: rgba(201, 164, 76, 0.22);
    background:
        radial-gradient(circle at 100% 0%, rgba(201, 164, 76, 0.16), transparent 30%),
        linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 248, 235, 0.9));
}

.page-inner .account-stat strong {
    color: #8e1028;
    font-size: clamp(1.75rem, 3vw, 2.45rem);
    line-height: 1;
}

.page-inner .account-stat span {
    margin-top: 0.4rem;
    color: #5f6977;
    font-size: 0.78rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.page-inner .account-form {
    margin-top: 1.15rem;
    gap: 0.92rem;
}

.page-inner .field-group label {
    color: #374151;
    font-size: 0.75rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.page-inner .form-control {
    border: 1px solid rgba(111, 13, 27, 0.14);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.92);
    color: #111827;
}

.page-inner .form-control:focus {
    border-color: rgba(177, 15, 46, 0.46);
    box-shadow: 0 0 0 4px rgba(177, 15, 46, 0.1);
    outline: none;
}

.page-inner .form-actions {
    margin-top: 0.3rem;
}

.page-inner .account-inline-list {
    gap: 0.7rem;
}

.page-inner .account-inline-list .list-card {
    padding: 0.86rem 0.95rem;
}

.page-inner .account-empty .empty-state {
    margin-top: 0.8rem;
}

@media (max-width: 1024px) {
    .page-inner .account-sidebar {
        position: static;
    }

    .page-inner .match-centre-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .page-inner .detail-media {
        min-height: 12rem;
        max-height: 18rem;
    }

    .page-inner .match-centre-hero {
        border-radius: 22px;
    }

    .page-inner .match-scoreboard {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .page-inner .match-scoreboard__team,
    .page-inner .match-scoreboard__team--away {
        justify-content: center;
        text-align: center;
    }

    .page-inner .match-scoreboard__team--away {
        flex-direction: row-reverse;
    }

    .page-inner .match-scoreboard__score {
        width: 100%;
    }

    .page-inner .match-centre-meta {
        display: grid;
    }

    .page-inner .match-stat-context__teams,
    .page-inner .match-stat-row__values {
        grid-template-columns: minmax(0, 1fr) minmax(5.8rem, auto) minmax(0, 1fr);
        gap: 0.42rem;
    }
}

@media (max-width: 640px) {
    .page-inner .detail-media {
        min-height: 10.5rem;
        border-radius: 18px;
    }

    .page-inner .detail-grid .listing-grid[style] {
        grid-template-columns: 1fr;
    }

    .page-inner .match-centre-hero {
        padding: 0.92rem;
    }

    .page-inner .match-scoreboard__team {
        padding: 0.68rem;
    }

    .page-inner .match-scoreboard__score strong {
        font-size: clamp(1.72rem, 12vw, 2.6rem);
    }

    .page-inner .match-timeline__item {
        padding: 0.62rem;
    }

    .page-inner .account-panel,
    .page-inner .match-centre-panel {
        padding: 0.92rem;
    }

    .page-inner .account-nav {
        display: flex;
        overflow-x: auto;
        padding-bottom: 0.15rem;
        -webkit-overflow-scrolling: touch;
    }

    .page-inner .account-nav__link {
        flex: 0 0 auto;
        white-space: nowrap;
    }

    .page-inner .form-grid-2,
    .page-inner .split-grid,
    .page-inner .summary-grid {
        grid-template-columns: 1fr;
    }
}

/* ==========================================================================
   DESIGN HELL PHASE 1 — Public shell brand foundation.
   Official logo binding, premium white header, red/gold footer, shared tokens.
   ========================================================================== */

:root {
    --m2030-red: #b10f2e;
    --m2030-red-deep: #6f0d1b;
    --m2030-red-soft: rgba(177, 15, 46, 0.12);
    --m2030-gold: #c9a44c;
    --m2030-gold-strong: #a8842e;
    --m2030-gold-soft: rgba(201, 164, 76, 0.18);
    --m2030-green: #0d6b45;
    --m2030-ink: #141c27;
    --m2030-ink-soft: #3a4657;
    --m2030-paper: #ffffff;
    --m2030-paper-warm: #fffdf8;
    --m2030-border: rgba(20, 28, 39, 0.1);
    --m2030-border-card: rgba(20, 28, 39, 0.08);
    --m2030-shadow-soft: 0 10px 28px rgba(6, 17, 31, 0.08);
    --m2030-shadow-header: 0 8px 24px rgba(6, 17, 31, 0.06);
    --m2030-radius-sm: 10px;
    --m2030-radius-md: 16px;
    --m2030-radius-lg: 22px;
    --m2030-container: 1320px;
    --m2030-header-h: 4.25rem;
    --m2030-shell-gap: clamp(1rem, 2vw, 1.5rem);
}

.page-container,
.site-header__inner,
.site-footer__inner,
.site-footer__bottom {
    width: min(calc(100% - 32px), var(--m2030-container));
}

/* Official header — clean white tournament bar */

.site-header {
    position: sticky;
    top: 0;
    z-index: 30;
    background:
        linear-gradient(180deg, var(--m2030-paper) 0%, rgba(255, 253, 248, 0.98) 100%);
    border-bottom: 1px solid var(--m2030-border);
    box-shadow: var(--m2030-shadow-header);
    backdrop-filter: saturate(130%) blur(8px);
    -webkit-backdrop-filter: saturate(130%) blur(8px);
}

.site-header::before {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 3px;
    background: linear-gradient(90deg, var(--m2030-red-deep), var(--m2030-red), var(--m2030-gold), var(--m2030-green), var(--m2030-red));
    opacity: 0.96;
    pointer-events: none;
}

.site-header::after {
    content: "";
    position: absolute;
    inset: auto 0 0 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(201, 164, 76, 0.42), transparent);
    pointer-events: none;
}

.site-header__inner {
    min-height: var(--m2030-header-h);
    padding-block: 0.55rem;
    grid-template-columns: minmax(0, auto) minmax(0, 1fr) auto;
    gap: var(--m2030-shell-gap);
}

.brand {
    flex-shrink: 0;
    min-width: 0;
    padding: 0;
    color: var(--m2030-ink);
    transition: opacity 0.18s ease;
}

.brand:hover,
.brand:focus-visible {
    opacity: 0.88;
    background: transparent;
}

.brand::after {
    display: none;
}

.brand__logo {
    display: block;
    width: auto;
    height: auto;
    max-height: 2.75rem;
    max-width: min(168px, 38vw);
    object-fit: contain;
    object-position: left center;
}

.brand__logo--stacked {
    display: none;
    max-height: 2.65rem;
    max-width: 3.1rem;
}

.site-nav.site-nav--desktop {
    justify-self: center;
    padding: 0;
    gap: 0.12rem;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    overflow-x: auto;
    scrollbar-width: none;
}

.site-nav.site-nav--desktop a {
    position: relative;
    min-height: 2.35rem;
    padding: 0.45rem 0.72rem;
    border-radius: 0;
    color: var(--m2030-ink-soft);
    font-size: clamp(0.68rem, 0.72vw, 0.78rem);
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    white-space: nowrap;
    transition: color 0.18s ease;
}

.site-nav.site-nav--desktop a:hover,
.site-nav.site-nav--desktop a:focus-visible {
    color: var(--m2030-red);
    background: transparent;
}

.site-nav.site-nav--desktop a.is-active {
    color: var(--m2030-red);
    background: transparent;
    box-shadow: none;
}

.site-nav.site-nav--desktop a.is-active::after {
    content: "";
    position: absolute;
    inset: auto 0.55rem -0.12rem;
    height: 2px;
    border-radius: 999px;
    background: linear-gradient(90deg, var(--m2030-red), var(--m2030-gold));
}

.site-nav.site-nav--desktop a.is-active::before {
    content: none;
}

.site-header__utility-tools {
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    gap: 0.28rem;
}

.shell-icon-button {
    width: 2.2rem;
    height: 2.2rem;
    border: 1px solid var(--m2030-border);
    border-radius: 999px;
    background: var(--m2030-paper-warm);
    color: var(--m2030-ink);
    box-shadow: none;
}

.shell-icon-button .shell-icon {
    fill: currentColor;
}

.shell-icon-button:hover,
.shell-icon-button:focus-visible,
.shell-icon-button[aria-expanded="true"],
.shell-icon-button.is-active {
    color: var(--m2030-red);
    border-color: rgba(201, 164, 76, 0.55);
    background: var(--m2030-gold-soft);
}

.shell-menu-button {
    color: var(--m2030-red);
    border-color: rgba(177, 15, 46, 0.22);
    background: var(--m2030-red-soft);
}

.shell-current-language {
    color: var(--m2030-ink-soft);
    font-size: 0.62rem;
    font-weight: 900;
}

.site-header__divider {
    height: 1.5rem;
    background: linear-gradient(180deg, transparent, rgba(201, 164, 76, 0.5), transparent);
}

.header-auth__link,
.header-auth__account {
    min-height: 2.2rem;
    height: 2.2rem;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 900;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.header-auth__link--ghost {
    color: var(--m2030-ink);
    border: 1px solid var(--m2030-border);
    background: var(--m2030-paper);
}

.header-auth__link--ghost:hover,
.header-auth__link--ghost:focus-visible {
    color: var(--m2030-red);
    border-color: rgba(201, 164, 76, 0.55);
    background: var(--m2030-gold-soft);
}

.header-auth__link--primary {
    color: #fff;
    border: 1px solid rgba(201, 164, 76, 0.65);
    background: linear-gradient(135deg, #d11f3c 0%, var(--m2030-red) 52%, var(--m2030-red-deep) 100%);
    box-shadow: 0 8px 18px rgba(177, 15, 46, 0.28);
}

.header-auth__account {
    color: var(--m2030-ink);
    border: 1px solid var(--m2030-border);
    background: var(--m2030-paper-warm);
}

.header-auth__account-mark {
    background: linear-gradient(135deg, var(--m2030-gold), var(--m2030-red));
}

.shell-popovers {
    width: min(calc(100% - 32px), var(--m2030-container));
}

.shell-panel {
    border: 1px solid rgba(201, 164, 76, 0.28);
    border-radius: var(--m2030-radius-md);
    background: var(--m2030-paper-warm);
    box-shadow: var(--m2030-shadow-soft);
}

/* Official footer — premium red identity */

.site-footer {
    margin-top: clamp(2.4rem, 4.5vw, 3.6rem);
    padding: 0;
    background:
        radial-gradient(circle at 12% 0%, rgba(201, 164, 76, 0.12), transparent 28%),
        radial-gradient(circle at 88% 100%, rgba(0, 0, 0, 0.18), transparent 34%),
        linear-gradient(165deg, var(--m2030-red) 0%, var(--m2030-red-deep) 58%, #3a0610 100%);
    color: #fff;
    position: relative;
    overflow: hidden;
}

.site-footer::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.028) 0 1px, transparent 1px 22px),
        repeating-linear-gradient(45deg, rgba(255, 255, 255, 0.018) 0 1px, transparent 1px 18px);
    pointer-events: none;
    opacity: 0.55;
}

.site-footer::after {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, var(--m2030-gold), transparent);
    opacity: 0.72;
    pointer-events: none;
}

.site-footer__inner {
    position: relative;
    z-index: 1;
    margin-inline: auto;
    padding: clamp(2rem, 3.5vw, 2.8rem) 0 clamp(1.4rem, 2.5vw, 2rem);
    display: grid;
    grid-template-columns: minmax(0, 1.35fr) repeat(2, minmax(150px, 0.75fr)) minmax(200px, 0.95fr);
    gap: clamp(1.2rem, 2.5vw, 2rem);
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}

.site-footer__inner::before {
    content: none;
}

.site-footer__brand-lockup {
    display: block;
}

.site-footer__logo {
    display: block;
    width: auto;
    max-width: min(148px, 42vw);
    max-height: 5.2rem;
    object-fit: contain;
    object-position: left center;
}

.site-footer__brand p,
.site-footer__note p {
    margin: 0.85rem 0 0;
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.9rem;
    line-height: 1.62;
    max-width: 26rem;
}

.site-footer__label {
    color: var(--m2030-gold);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.18em;
    text-transform: uppercase;
}

.site-footer__links a {
    color: rgba(255, 255, 255, 0.88);
    font-size: 0.9rem;
    font-weight: 600;
}

.site-footer__links a:hover,
.site-footer__links a:focus-visible {
    color: var(--m2030-gold);
}

.site-footer__note p a {
    color: rgba(255, 255, 255, 0.92);
    font-weight: 700;
    text-decoration: underline;
    text-decoration-color: rgba(201, 164, 76, 0.55);
    text-underline-offset: 0.14em;
}

.site-footer__note p a:hover {
    color: var(--m2030-gold);
}

.site-footer__bottom {
    position: relative;
    z-index: 1;
    margin-inline: auto;
    padding: 0.85rem 0 1.35rem;
    border-top: 1px solid rgba(255, 255, 255, 0.12);
}

.site-footer__bottom p {
    margin: 0;
    color: rgba(255, 255, 255, 0.62);
    font-size: 0.78rem;
    letter-spacing: 0.04em;
}

/* Homepage shell alignment after white header */

.page-home main {
    padding-top: 0.35rem;
}

.page-home .hero.hero--command {
    margin-top: clamp(0.85rem, 1.4vw, 1.25rem);
}

/* Phase 1 responsive polish */

@media (max-width: 1100px) {
    .site-footer__inner {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .site-footer__brand {
        grid-column: 1 / -1;
    }
}

@media (max-width: 900px) {
    .brand__logo--horizontal {
        display: none;
    }

    .brand__logo--stacked {
        display: block;
    }

    .site-header__inner {
        grid-template-columns: minmax(0, auto) auto;
        justify-content: space-between;
    }
}

@media (max-width: 700px) {
    .site-header__inner,
    .shell-popovers,
    .page-container,
    .site-footer__inner,
    .site-footer__bottom {
        width: min(calc(100% - 22px), var(--m2030-container));
    }

    .brand__logo--stacked {
        max-height: 2.45rem;
        max-width: 2.85rem;
    }

    .page-home .hero.hero--command {
        margin-top: 0.75rem;
        border-radius: 20px;
    }
}

@media (max-width: 520px) {
    .site-footer__inner {
        grid-template-columns: 1fr;
        gap: 1.35rem;
    }

    .site-footer__logo {
        max-width: 128px;
        max-height: 4.6rem;
    }
}

[dir="rtl"] .brand__logo,
[dir="rtl"] .site-footer__logo {
    object-position: right center;
}

[dir="rtl"] .site-nav.site-nav--desktop a.is-active::after {
    inset-inline: 0.55rem -0.12rem auto;
}

/* ==========================================================================
   DESIGN HELL PHASE 1.1 — Public shell bugfix at 100% zoom.
   Fix header flex layout, logo sizing, nav overflow, spacing, footer columns.
   ========================================================================== */

:root {
    --m2030-container: 1440px;
    --m2030-header-h: 5rem;
    --m2030-header-stripe: 3px;
    --m2030-header-total: calc(var(--m2030-header-h) + var(--m2030-header-stripe));
    --m2030-shell-gap: clamp(0.45rem, 1vw, 1rem);
}

html {
    scroll-padding-top: var(--m2030-header-total);
}

body.page-home main,
body.page-inner main {
    padding-top: clamp(0.65rem, 1.1vw, 1rem);
}

.site-header {
    position: sticky;
    top: 0;
    z-index: 40;
    overflow: visible;
    isolation: isolate;
}

.site-header__inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--m2030-shell-gap);
    width: min(calc(100% - 32px), var(--m2030-container));
    min-height: var(--m2030-header-h);
    max-width: min(calc(100% - 32px), var(--m2030-container));
    margin-inline: auto;
    padding-block: 0.625rem;
    padding-inline: 0;
    overflow: visible;
    grid-template-columns: none;
}

.brand {
    flex: 0 0 auto;
    min-width: 0;
    max-width: clamp(148px, 16vw, 190px);
    display: inline-flex;
    align-items: center;
}

.brand__logo {
    display: block;
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: clamp(2.875rem, 3.8vw, 3.875rem);
    object-fit: contain;
    object-position: left center;
}

.brand__logo--stacked {
    display: none;
    max-height: clamp(2.65rem, 7vw, 3.25rem);
    max-width: 3.5rem;
}

.site-nav.site-nav--desktop {
    flex: 1 1 auto;
    min-width: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: clamp(0.04rem, 0.28vw, 0.14rem);
    max-width: 100%;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    overflow: visible;
}

.site-nav.site-nav--desktop a {
    flex: 0 1 auto;
    min-width: 0;
    min-height: 2.25rem;
    padding: 0.32rem clamp(0.34rem, 0.5vw, 0.58rem);
    font-size: clamp(0.62rem, 0.62vw, 0.74rem);
    letter-spacing: clamp(0.028em, 0.035vw, 0.055em);
    line-height: 1.1;
    white-space: nowrap;
}

.site-nav.site-nav--desktop a.is-active::after {
    inset: auto clamp(0.28rem, 0.45vw, 0.5rem) 0;
    height: 2px;
}

.site-header__utility-tools {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    flex-shrink: 0;
    gap: clamp(0.14rem, 0.32vw, 0.26rem);
    min-width: 0;
    max-width: none;
    padding: 0;
    border: 0;
    background: transparent;
}

.shell-icon-button,
.header-auth__account {
    width: 2.125rem;
    height: 2.125rem;
    flex-shrink: 0;
}

.shell-icon-button--language {
    min-width: 2.5rem;
    width: auto;
    padding-inline: 0.42rem;
}

.header-auth {
    display: inline-flex;
    align-items: center;
    gap: 0.28rem;
    flex-shrink: 0;
}

.header-auth__link {
    min-height: 2.125rem;
    height: 2.125rem;
    padding-inline: clamp(0.55rem, 0.75vw, 0.82rem);
    font-size: clamp(0.58rem, 0.62vw, 0.68rem);
    white-space: nowrap;
}

.header-auth__link--primary {
    box-shadow: 0 6px 14px rgba(177, 15, 46, 0.22);
}

.site-header .shell-popovers {
    position: absolute;
    inset-inline: 0;
    top: 100%;
    left: 0;
    right: 0;
    transform: none;
    width: min(calc(100% - 32px), var(--m2030-container));
    margin-inline: auto;
    pointer-events: none;
    z-index: 45;
}

.site-header .shell-panel {
    pointer-events: auto;
    max-height: calc(100dvh - var(--m2030-header-total) - 1rem);
    overflow-y: auto;
}

.shell-menu-button {
    display: none;
}

.site-footer {
    overflow-x: clip;
}

.site-footer__inner {
    width: min(calc(100% - 32px), var(--m2030-container));
    grid-template-columns: minmax(0, 1.25fr) repeat(2, minmax(130px, 0.72fr)) minmax(180px, 0.9fr);
    gap: clamp(1rem, 2vw, 1.75rem);
}

.site-footer__logo {
    max-width: min(156px, 40vw);
    max-height: 5.5rem;
}

.site-footer__bottom {
    width: min(calc(100% - 32px), var(--m2030-container));
    margin-inline: auto;
}

.page-home .hero.hero--command {
    margin-top: clamp(0.65rem, 1vw, 0.95rem);
}

/* Neutralize legacy width/overflow rules from styles.blade.php */

@media (min-width: 1025px) {
    .site-nav.site-nav--desktop {
        display: flex !important;
    }

    .shell-menu-button {
        display: none !important;
    }

    .header-auth ~ .shell-icon-button[data-shell-toggle="account"] {
        display: none;
    }
}

@media (max-width: 1280px) and (min-width: 1025px) {
    .site-header__utility-tools > a.shell-icon-button {
        display: none;
    }

    .header-auth__link--ghost {
        display: none;
    }
}

@media (max-width: 1100px) and (min-width: 1025px) {
    .shell-icon-button--language {
        display: inline-grid;
    }
}

@media (max-width: 1024px) {
    .site-header__inner {
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-between;
    }

    .site-nav.site-nav--desktop {
        display: none !important;
    }

    .shell-menu-button {
        display: inline-grid !important;
    }

    .header-auth {
        display: none;
    }

    .site-header__divider {
        display: none;
    }
}

@media (max-width: 900px) {
    .brand__logo--horizontal {
        display: none;
    }

    .brand__logo--stacked {
        display: block;
    }
}

@media (max-width: 760px) {
    .site-header .shell-popovers {
        position: fixed;
        top: var(--m2030-header-total);
        width: min(calc(100% - 24px), var(--m2030-container));
    }

    .site-header__inner,
    .shell-popovers,
    .page-container,
    .site-footer__inner,
    .site-footer__bottom {
        width: min(calc(100% - 24px), var(--m2030-container));
    }
}

@media (max-width: 700px) {
    .site-header__inner {
        min-height: 4.35rem;
        padding-block: 0.5rem;
    }

    :root {
        --m2030-header-h: 4.35rem;
    }

    .shell-icon-button--language {
        display: inline-grid;
    }
}

@media (max-width: 520px) {
    .site-footer__inner {
        grid-template-columns: 1fr;
    }

    .site-footer__brand {
        grid-column: auto;
    }
}

[dir="rtl"] .site-nav.site-nav--desktop a.is-active::after {
    inset-inline: clamp(0.28rem, 0.45vw, 0.5rem);
}

/* ==========================================================================
   DESIGN HELL PHASE 1.2 — Footer reference match + auth menu fix.
   ========================================================================== */

.header-auth--guest {
    display: inline-flex;
    align-items: center;
    gap: 0.32rem;
    flex-shrink: 0;
}

.header-auth__login {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.42rem;
    min-height: 2.25rem;
    height: 2.25rem;
    padding-inline: 0.95rem 1.05rem;
    border: 1px solid rgba(201, 164, 76, 0.55);
    border-radius: 999px;
    background: linear-gradient(135deg, #d11f3c 0%, var(--m2030-red) 52%, var(--m2030-red-deep) 100%);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    white-space: nowrap;
    box-shadow: 0 8px 18px rgba(177, 15, 46, 0.28);
    transition: transform 0.18s ease, box-shadow 0.18s ease, opacity 0.18s ease;
}

.header-auth__login .shell-icon {
    width: 1rem;
    height: 1rem;
    fill: currentColor;
    flex-shrink: 0;
}

.header-auth__login:hover,
.header-auth__login:focus-visible {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 10px 22px rgba(177, 15, 46, 0.34);
}

.header-auth__login-menu {
    display: inline-grid;
    place-items: center;
    width: 2rem;
    height: 2.25rem;
    padding: 0;
    border: 1px solid var(--m2030-border);
    border-radius: 999px;
    background: var(--m2030-paper-warm);
    color: var(--m2030-ink-soft);
    cursor: pointer;
    transition: border-color 0.18s ease, background-color 0.18s ease, color 0.18s ease;
}

.header-auth__login-menu .shell-icon {
    width: 0.95rem;
    height: 0.95rem;
    fill: currentColor;
}

.header-auth__login-menu:hover,
.header-auth__login-menu:focus-visible,
.header-auth__login-menu[aria-expanded="true"] {
    color: var(--m2030-red);
    border-color: rgba(201, 164, 76, 0.55);
    background: var(--m2030-gold-soft);
}

.header-auth__register-secondary {
    color: var(--m2030-ink-soft);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-decoration: underline;
    text-decoration-color: rgba(201, 164, 76, 0.45);
    text-underline-offset: 0.16em;
    white-space: nowrap;
}

.header-auth__register-secondary:hover,
.header-auth__register-secondary:focus-visible {
    color: var(--m2030-red);
}

.shell-panel--account .shell-menu-link--primary {
    background: linear-gradient(135deg, var(--m2030-red), var(--m2030-red-deep));
    color: #fff;
}

.shell-panel--account .shell-menu-link:not(.shell-menu-link--primary) {
    color: var(--m2030-ink);
}

@media (min-width: 1025px) {
    .header-auth--guest ~ .shell-icon-button[data-shell-toggle="account"],
    .header-auth ~ .shell-icon-button[data-shell-toggle="account"] {
        display: none !important;
    }

    .header-auth--guest {
        display: inline-flex !important;
    }
}

@media (max-width: 1280px) and (min-width: 1025px) {
    .header-auth__register-secondary {
        display: none;
    }
}

@media (max-width: 1024px) {
    .header-auth--guest {
        display: inline-flex !important;
    }

    .header-auth__register-secondary {
        display: none;
    }

    .header-auth__login-text {
        display: none;
    }

    .header-auth__login {
        width: 2.25rem;
        height: 2.25rem;
        padding: 0;
        gap: 0;
    }

    .header-auth__login-menu {
        display: inline-grid;
    }
}

@media (max-width: 480px) {
    .header-auth__login-menu {
        display: none;
    }
}

/* Footer reference layout */

.site-footer__inner {
    grid-template-columns: minmax(0, 1.15fr) repeat(4, minmax(120px, 0.72fr)) minmax(210px, 0.95fr);
    gap: clamp(1rem, 2vw, 1.65rem);
    align-items: start;
}

.site-footer__brand {
    max-width: 18rem;
}

.site-footer__slogan {
    margin: 0.85rem 0 0;
    color: rgba(255, 255, 255, 0.82);
    font-size: clamp(0.72rem, 0.85vw, 0.82rem);
    font-weight: 800;
    letter-spacing: 0.14em;
    line-height: 1.55;
    text-transform: uppercase;
}

.site-footer__links {
    gap: 0.48rem;
}

.site-footer__links a {
    font-size: 0.86rem;
    line-height: 1.45;
}

.site-footer__aside {
    display: grid;
    gap: 1.15rem;
}

.site-footer__social-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    margin-top: 0.35rem;
}

.site-footer__social-link {
    display: inline-grid;
    place-items: center;
    width: 2.05rem;
    height: 2.05rem;
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.06);
    color: #fff;
    font-size: 0.68rem;
    font-weight: 900;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease;
}

.site-footer__social-link:hover,
.site-footer__social-link:focus-visible {
    color: var(--m2030-gold);
    border-color: rgba(201, 164, 76, 0.65);
    background: rgba(201, 164, 76, 0.12);
}

.site-footer__newsletter-note {
    margin: 0.35rem 0 0.65rem;
    color: rgba(255, 255, 255, 0.68);
    font-size: 0.82rem;
    line-height: 1.45;
}

.site-footer__newsletter-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 0.45rem;
    align-items: center;
}

.site-footer__newsletter-form input {
    width: 100%;
    min-height: 2.35rem;
    padding: 0.52rem 0.72rem;
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.22);
    color: #fff;
    font: inherit;
    font-size: 0.82rem;
}

.site-footer__newsletter-form input::placeholder {
    color: rgba(255, 255, 255, 0.48);
}

.site-footer__newsletter-form input:disabled {
    opacity: 0.72;
    cursor: not-allowed;
}

.site-footer__newsletter-btn {
    min-height: 2.35rem;
    padding: 0.52rem 0.95rem;
    border: 1px solid rgba(201, 164, 76, 0.72);
    border-radius: 999px;
    background: linear-gradient(135deg, var(--m2030-gold), var(--m2030-gold-strong));
    color: #2a1608;
    font: inherit;
    font-size: 0.68rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    white-space: nowrap;
    cursor: not-allowed;
    opacity: 0.88;
}

.site-footer__bottom {
    padding-top: 0.95rem;
    padding-bottom: 1.25rem;
}

@media (max-width: 1200px) {
    .site-footer__inner {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .site-footer__brand,
    .site-footer__aside {
        grid-column: 1 / -1;
    }

    .site-footer__aside {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
}

@media (max-width: 700px) {
    .site-footer__inner {
        grid-template-columns: 1fr;
        gap: 1.35rem;
    }

    .site-footer__aside {
        grid-template-columns: 1fr;
    }

    .site-footer__newsletter-form {
        grid-template-columns: 1fr;
    }

    .site-footer__newsletter-btn {
        width: 100%;
    }
}

/* DESIGN HELL PHASE 1.3 — Compact footer and login polish */

.header-auth--guest {
    gap: 0.22rem;
}

.header-auth__login {
    min-height: 2.125rem;
    height: 2.125rem;
    padding-inline: 0.82rem 0.92rem;
    gap: 0.38rem;
    font-size: 0.68rem;
    box-shadow: 0 6px 14px rgba(177, 15, 46, 0.24);
}

.header-auth__login .shell-icon {
    width: 0.95rem;
    height: 0.95rem;
}

.header-auth__login-menu {
    width: 1.85rem;
    height: 2.125rem;
    flex-shrink: 0;
}

.header-auth__register-secondary {
    display: none !important;
}

.site-header .shell-panel--account {
    position: absolute;
    top: 0.32rem;
    inset-inline-end: 0;
    inset-inline-start: auto;
    width: min(100%, 15rem);
    max-width: 15rem;
    padding: 0.42rem;
    border-radius: 12px;
}

.site-header .shell-panel--account .shell-panel__label {
    margin-bottom: 0.28rem;
    font-size: 0.58rem;
    letter-spacing: 0.12em;
}

.site-header .shell-panel--account .shell-menu-link {
    min-height: 1.95rem;
    padding: 0.42rem 0.62rem;
    font-size: 0.76rem;
    font-weight: 700;
    border-radius: 8px;
}

.site-header .shell-panel--account .shell-menu-link--primary {
    background: transparent;
    color: var(--m2030-ink);
    box-shadow: none;
}

.site-footer--compact {
    margin-top: clamp(1.35rem, 2.5vw, 2rem);
}

.site-footer--compact .site-footer__inner,
.site-footer__grid {
    grid-template-columns: minmax(108px, 1.25fr) repeat(4, minmax(72px, 0.82fr)) minmax(150px, 1.45fr);
    gap: clamp(0.55rem, 1vw, 0.85rem);
    padding: clamp(1.25rem, 1.6vw, 1.75rem) 0 clamp(0.85rem, 1.1vw, 1.15rem);
    align-items: start;
}

.site-footer--compact .footer-brand {
    max-width: 11.5rem;
}

.site-footer--compact .footer-logo,
.site-footer--compact .site-footer__logo {
    max-width: 108px;
    max-height: 4.5rem;
}

.site-footer--compact .site-footer__slogan {
    margin-top: 0.55rem;
    font-size: 0.62rem;
    letter-spacing: 0.12em;
    line-height: 1.45;
}

.site-footer--compact .footer-column {
    gap: 0.28rem;
}

.site-footer--compact .site-footer__label {
    margin-bottom: 0.18rem;
    font-size: 0.58rem;
    letter-spacing: 0.14em;
}

.site-footer--compact .site-footer__links a {
    font-size: 0.76rem;
    line-height: 1.35;
}

.site-footer--compact .footer-aside {
    display: grid;
    gap: 0.65rem;
}

.site-footer--compact .footer-social .site-footer__social-list {
    gap: 0.32rem;
    margin-top: 0.22rem;
}

.site-footer--compact .site-footer__social-link {
    width: 1.72rem;
    height: 1.72rem;
    font-size: 0.58rem;
}

.site-footer--compact .site-footer__newsletter-note {
    margin: 0.18rem 0 0.42rem;
    font-size: 0.72rem;
    line-height: 1.35;
}

.site-footer--compact .footer-newsletter__form {
    gap: 0.32rem;
}

.site-footer--compact .site-footer__newsletter-form input {
    min-height: 2rem;
    padding: 0.4rem 0.62rem;
    font-size: 0.74rem;
}

.site-footer--compact .site-footer__newsletter-btn {
    min-height: 2rem;
    padding: 0.4rem 0.72rem;
    font-size: 0.6rem;
}

.site-footer--compact .footer-bottom {
    padding-top: 0.55rem;
    padding-bottom: 0.85rem;
}

.site-footer--compact .footer-bottom p {
    font-size: 0.68rem;
}

@media (min-width: 901px) {
    .site-footer--compact .site-footer__brand,
    .site-footer--compact .site-footer__aside,
    .site-footer--compact .footer-brand,
    .site-footer--compact .footer-aside {
        grid-column: auto;
    }

    .site-footer--compact .site-footer__inner {
        grid-template-columns: minmax(108px, 1.25fr) repeat(4, minmax(72px, 0.82fr)) minmax(150px, 1.45fr);
    }
}

@media (max-width: 900px) {
    .site-footer--compact .site-footer__inner {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem 0.65rem;
        padding-block: 1.15rem 0.95rem;
    }

    .site-footer--compact .footer-brand {
        grid-column: 1 / -1;
        max-width: none;
    }

    .site-footer--compact .footer-aside {
        grid-column: 1 / -1;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }
}

@media (max-width: 1024px) {
    .header-auth__login-text {
        display: none;
    }

    .header-auth__login {
        width: 2.125rem;
        height: 2.125rem;
        padding: 0;
        justify-content: center;
    }
}

@media (max-width: 640px) {
    .site-footer--compact .site-footer__inner {
        grid-template-columns: 1fr;
    }

    .site-footer--compact .footer-aside {
        grid-template-columns: 1fr;
    }

    .site-footer--compact .footer-newsletter__form {
        grid-template-columns: 1fr;
    }

    .header-auth__login-menu {
        display: none;
    }
}

/* DESIGN HELL PHASE 1.4 — Final footer and auth dropdown fix */

.header-auth__login,
.header-auth__login-menu,
.header-auth__register-secondary,
.header-auth--guest {
    display: none !important;
}

.header-auth__trigger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    min-height: 2.125rem;
    height: 2.125rem;
    padding-inline: 0.88rem 0.96rem;
    border: 1px solid rgba(201, 164, 76, 0.55);
    border-radius: 999px;
    background: linear-gradient(135deg, #d11f3c 0%, var(--m2030-red) 52%, var(--m2030-red-deep) 100%);
    color: #fff;
    font: inherit;
    font-size: 0.68rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    white-space: nowrap;
    cursor: pointer;
    box-shadow: 0 6px 14px rgba(177, 15, 46, 0.24);
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.header-auth__trigger .shell-icon {
    width: 0.95rem;
    height: 0.95rem;
    fill: currentColor;
    flex-shrink: 0;
}

.header-auth__trigger:hover,
.header-auth__trigger:focus-visible,
.header-auth__trigger[aria-expanded="true"] {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(177, 15, 46, 0.32);
}

.site-header .shell-panel--account {
    position: absolute;
    top: 0.3rem;
    inset-inline-end: 0;
    inset-inline-start: auto;
    z-index: 50;
    width: min(100%, 16.25rem);
    max-width: 16.25rem;
    padding: 0.4rem;
    border-radius: 12px;
    background: var(--m2030-paper-warm);
    box-shadow: 0 14px 34px rgba(6, 17, 31, 0.16);
}

.site-header .shell-panel--account[hidden] {
    display: none !important;
}

.site-header .shell-panel--account .shell-panel__label {
    margin-bottom: 0.24rem;
    padding-inline: 0.35rem;
    color: var(--m2030-ink-soft);
    font-size: 0.56rem;
    letter-spacing: 0.14em;
}

.site-header .shell-panel--account .shell-menu-link {
    display: flex;
    align-items: center;
    min-height: 1.9rem;
    padding: 0.4rem 0.62rem;
    border-radius: 8px;
    font-size: 0.76rem;
    font-weight: 700;
    text-align: start;
}

.site-header .shell-panel--account .shell-menu-link--primary {
    color: #fff;
    background: linear-gradient(135deg, var(--m2030-red), var(--m2030-red-deep));
    box-shadow: none;
}

.site-header .shell-panel--account .shell-menu-link--secondary {
    color: var(--m2030-ink-soft);
    background: transparent;
    font-weight: 600;
}

.site-header .shell-panel--account .shell-menu-link--secondary:hover,
.site-header .shell-panel--account .shell-menu-link--secondary:focus-visible {
    color: var(--m2030-red);
    background: var(--m2030-gold-soft);
}

.site-footer--reference {
    margin-top: clamp(1.1rem, 2vw, 1.65rem);
}

.site-footer--reference .site-footer__inner {
    grid-template-columns: minmax(96px, 1.1fr) repeat(4, minmax(68px, 0.78fr)) minmax(148px, 1.35fr);
    gap: clamp(0.45rem, 0.85vw, 0.72rem);
    padding: clamp(1.5rem, 2vw, 2.25rem) 0 clamp(1rem, 1.35vw, 1.35rem);
    align-items: start;
}

.site-footer--reference .footer-logo,
.site-footer--reference .site-footer__logo {
    max-width: 102px;
    max-height: 4.25rem;
}

.site-footer--reference .site-footer__slogan {
    display: grid;
    gap: 0.12rem;
    margin-top: 0.48rem;
    font-size: 0.58rem;
    letter-spacing: 0.13em;
    line-height: 1.35;
}

.site-footer--reference .site-footer__slogan span {
    display: block;
}

.site-footer--reference .site-footer__label {
    margin-bottom: 0.14rem;
    font-size: 0.54rem;
    letter-spacing: 0.16em;
}

.site-footer--reference .footer-column {
    gap: 0.22rem;
}

.site-footer--reference .site-footer__links a {
    font-size: 0.72rem;
    line-height: 1.3;
}

.site-footer--reference .footer-aside {
    gap: 0.55rem;
}

.site-footer--reference .site-footer__social-list {
    gap: 0.28rem;
    margin-top: 0.16rem;
}

.site-footer--reference .site-footer__social-link {
    width: 1.62rem;
    height: 1.62rem;
    font-size: 0.54rem;
}

.site-footer--reference .site-footer__newsletter-note {
    margin: 0.12rem 0 0.34rem;
    font-size: 0.68rem;
}

.site-footer--reference .footer-newsletter__form {
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 0.28rem;
}

.site-footer--reference .site-footer__newsletter-form input {
    min-height: 1.85rem;
    padding: 0.34rem 0.55rem;
    font-size: 0.7rem;
}

.site-footer--reference .site-footer__newsletter-btn {
    min-height: 1.85rem;
    padding: 0.34rem 0.62rem;
    font-size: 0.56rem;
}

.site-footer--reference .footer-bottom {
    padding-top: 0.45rem;
    padding-bottom: 0.65rem;
}

.site-footer--reference .footer-bottom p {
    font-size: 0.64rem;
}

@media (min-width: 901px) {
    .site-footer--reference .site-footer__inner {
        grid-template-columns: minmax(96px, 1.1fr) repeat(4, minmax(68px, 0.78fr)) minmax(148px, 1.35fr);
    }

    .site-footer--reference .site-footer__brand,
    .site-footer--reference .footer-brand,
    .site-footer--reference .site-footer__aside,
    .site-footer--reference .footer-aside {
        grid-column: auto;
    }
}

@media (max-width: 1200px) {
    .site-footer--reference .site-footer__inner {
        grid-template-columns: minmax(96px, 1.1fr) repeat(4, minmax(62px, 0.72fr)) minmax(132px, 1.2fr);
    }
}

@media (max-width: 1024px) {
    .header-auth__trigger-text {
        display: none;
    }

    .header-auth__trigger {
        width: 2.125rem;
        height: 2.125rem;
        padding: 0;
    }
}

@media (max-width: 900px) {
    .site-footer--reference .site-footer__inner {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.72rem 0.55rem;
        padding-block: 1.15rem 0.9rem;
    }

    .site-footer--reference .footer-brand {
        grid-column: 1 / -1;
    }

    .site-footer--reference .footer-aside {
        grid-column: 1 / -1;
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 640px) {
    .site-footer--reference .site-footer__inner,
    .site-footer--reference .footer-aside {
        grid-template-columns: 1fr;
    }

    .site-footer--reference .footer-newsletter__form {
        grid-template-columns: 1fr;
    }
}

/* DESIGN HELL PHASE 2 — Public homepage premium redesign */

.page-home main {
    padding-top: 0.5rem;
}

.page-home .page-container {
    display: grid;
    gap: clamp(1rem, 1.8vw, 1.45rem);
}

.page-home .home-hero {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    border-radius: clamp(18px, 2vw, 28px);
    min-height: clamp(420px, 58vh, 580px);
    box-shadow: var(--m2030-shadow-soft, 0 18px 44px rgba(6, 17, 31, 0.12));
}

.page-home .home-hero__media {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(115deg, rgba(5, 7, 13, 0.82) 0%, rgba(79, 7, 22, 0.72) 42%, rgba(5, 7, 13, 0.55) 100%),
        radial-gradient(circle at 18% 20%, rgba(201, 164, 76, 0.22), transparent 34%),
        radial-gradient(circle at 82% 12%, rgba(13, 107, 69, 0.18), transparent 28%),
        url("{{ asset('assets/placeholders/stadium.svg') }}") center/cover no-repeat;
    filter: saturate(1.05);
}

.page-home .home-hero__media::after {
    content: "";
    position: absolute;
    inset: 0;
    background:
        repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.028) 0 1px, transparent 1px 24px),
        linear-gradient(180deg, transparent 55%, rgba(5, 7, 13, 0.42) 100%);
    pointer-events: none;
}

.page-home .home-hero__layout {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: minmax(220px, 0.95fr) minmax(0, 1.15fr) minmax(220px, 0.95fr);
    align-items: center;
    gap: clamp(0.85rem, 1.6vw, 1.35rem);
    min-height: inherit;
    padding: clamp(1.25rem, 2.4vw, 2rem);
}

.page-home .home-card {
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: var(--m2030-radius-md, 16px);
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.page-home .home-card__eyebrow,
.page-home .home-section-head__eyebrow {
    color: var(--m2030-gold, #c9a44c);
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.16em;
    text-transform: uppercase;
}

.page-home .home-card__empty {
    margin: 0.35rem 0 0;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.86rem;
    line-height: 1.5;
}

.page-home .home-next-match {
    padding: clamp(0.85rem, 1.4vw, 1.1rem);
    color: #fff;
}

.page-home .home-next-match__stage {
    margin: 0.35rem 0 0.55rem;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.page-home .home-next-match__teams {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 0.45rem;
    align-items: center;
    text-align: center;
}

.page-home .home-next-match__teams span {
    display: block;
    color: rgba(255, 255, 255, 0.62);
    font-size: 0.62rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.page-home .home-next-match__teams strong {
    display: block;
    margin-top: 0.18rem;
    font-size: 0.95rem;
}

.page-home .home-next-match__vs {
    color: var(--m2030-gold, #c9a44c);
    font-size: 0.72rem;
    font-weight: 900;
}

.page-home .home-next-match__meta {
    margin: 0.65rem 0 0.85rem;
    padding: 0;
    list-style: none;
    display: grid;
    gap: 0.22rem;
    color: rgba(255, 255, 255, 0.74);
    font-size: 0.74rem;
}

.page-home .home-hero__content {
    text-align: center;
    color: #fff;
}

.page-home .home-hero__logo {
    width: auto;
    max-width: min(120px, 28vw);
    max-height: 5rem;
    margin-inline: auto;
    object-fit: contain;
}

.page-home .home-hero__title {
    margin: 0.65rem 0 0;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(2rem, 4.8vw, 3.35rem);
    line-height: 0.95;
    letter-spacing: -0.04em;
}

.page-home .home-hero__slogan {
    display: grid;
    gap: 0.18rem;
    margin: 0.75rem 0 0;
    font-size: clamp(0.62rem, 0.9vw, 0.76rem);
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.88);
}

.page-home .home-hero__actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.55rem;
    margin-top: 1rem;
}

.page-home .home-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 2.45rem;
    padding: 0.55rem 1rem;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    text-decoration: none;
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.page-home .home-btn--primary {
    color: #fff;
    border: 1px solid rgba(201, 164, 76, 0.65);
    background: linear-gradient(135deg, #d11f3c 0%, var(--m2030-red, #b10f2e) 52%, var(--m2030-red-deep, #6f0d1b) 100%);
    box-shadow: 0 10px 22px rgba(177, 15, 46, 0.28);
}

.page-home .home-btn--gold {
    color: #2a1608;
    border: 1px solid rgba(201, 164, 76, 0.72);
    background: linear-gradient(135deg, var(--m2030-gold, #c9a44c), var(--m2030-gold-strong, #a8842e));
}

.page-home .home-btn--ghost,
.page-home .home-btn--subtle {
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.22);
    background: rgba(255, 255, 255, 0.08);
}

.page-home .home-btn:hover,
.page-home .home-btn:focus-visible {
    transform: translateY(-1px);
}

.page-home .home-hero__countdown {
    padding: clamp(0.85rem, 1.4vw, 1.1rem);
    color: #fff;
}

.page-home .home-hero__countdown-title {
    display: block;
    margin-top: 0.35rem;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(1.05rem, 1.6vw, 1.35rem);
}

.page-home .home-hero__countdown-date {
    margin: 0.35rem 0 0.65rem;
    color: rgba(255, 255, 255, 0.68);
    font-size: 0.72rem;
}

.page-home .home-hero__countdown-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.35rem;
}

.page-home .home-hero__countdown-unit {
    padding: 0.45rem 0.25rem;
    border: 1px solid rgba(201, 164, 76, 0.28);
    border-radius: 10px;
    background: rgba(5, 7, 13, 0.42);
    text-align: center;
}

.page-home .home-hero__countdown-unit strong {
    display: block;
    font-size: clamp(1.15rem, 2vw, 1.65rem);
    line-height: 1;
}

.page-home .home-hero__countdown-unit span {
    display: block;
    margin-top: 0.22rem;
    color: rgba(255, 229, 168, 0.84);
    font-size: 0.54rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.page-home .home-hero__countdown-status {
    margin: 0.55rem 0 0;
    color: rgba(255, 255, 255, 0.68);
    font-size: 0.68rem;
}

.page-home .home-quick-links__grid {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 0.65rem;
}

.page-home .home-quick-link {
    display: grid;
    gap: 0.28rem;
    padding: 0.85rem 0.8rem;
    border-color: rgba(20, 28, 39, 0.08);
    background: linear-gradient(180deg, #fff, #fffaf2);
    color: var(--m2030-ink, #141c27);
    text-decoration: none;
    box-shadow: var(--m2030-shadow-soft, 0 10px 28px rgba(6, 17, 31, 0.08));
    transition: transform 0.18s ease, border-color 0.18s ease;
}

.page-home .home-quick-link:hover,
.page-home .home-quick-link:focus-visible {
    transform: translateY(-2px);
    border-color: rgba(201, 164, 76, 0.45);
}

.page-home .home-quick-link strong {
    font-size: 0.86rem;
}

.page-home .home-quick-link span:last-child {
    color: #59677a;
    font-size: 0.72rem;
}

.page-home .home-quick-link__icon {
    display: inline-grid;
    place-items: center;
    width: 1.85rem;
    height: 1.85rem;
    border-radius: 999px;
    color: #fff;
    font-size: 0.72rem;
    font-weight: 900;
}

.page-home .home-quick-link__icon--red {
    background: var(--m2030-red, #b10f2e);
}

.page-home .home-quick-link__icon--green {
    background: var(--m2030-green, #0d6b45);
}

.page-home .home-quick-link__icon--gold {
    background: var(--m2030-gold-strong, #a8842e);
}

.page-home .home-section-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.85rem;
    margin-bottom: 0.85rem;
}

.page-home .home-section-head--inline {
    margin-bottom: 0.75rem;
}

.page-home .home-section-head--compact {
    margin-bottom: 0.55rem;
}

.page-home .home-section-head h2,
.page-home .home-section-head h3 {
    margin: 0.18rem 0 0;
    font-family: "Georgia", "Times New Roman", serif;
    letter-spacing: -0.03em;
}

.page-home .home-section-head p {
    margin: 0.35rem 0 0;
    color: #59677a;
    font-size: 0.84rem;
}

.page-home .home-section-head__links {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
}

.page-home .home-link {
    color: var(--m2030-red, #b10f2e);
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    text-decoration: none;
}

.page-home .home-link:hover,
.page-home .home-link:focus-visible {
    color: var(--m2030-gold-strong, #a8842e);
}

.page-home .home-metrics,
.page-home .home-dashboard-grid .home-card,
.page-home .home-partners {
    padding: clamp(0.95rem, 1.5vw, 1.25rem);
    border-color: rgba(20, 28, 39, 0.08);
    background: linear-gradient(180deg, #fff, #fffaf2);
    color: var(--m2030-ink, #141c27);
    box-shadow: var(--m2030-shadow-soft, 0 10px 28px rgba(6, 17, 31, 0.08));
}

.page-home .home-metrics__grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 0.55rem;
}

.page-home .home-metrics__item {
    padding: 0.65rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.72);
}

.page-home .home-metrics__item strong {
    display: block;
    color: var(--m2030-red, #b10f2e);
    font-size: 1.25rem;
}

.page-home .home-metrics__item span {
    display: block;
    margin-top: 0.12rem;
    font-size: 0.76rem;
    font-weight: 800;
}

.page-home .home-metrics__item small {
    display: block;
    margin-top: 0.18rem;
    color: #59677a;
    font-size: 0.68rem;
    line-height: 1.35;
}

.page-home .home-dashboard-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: clamp(0.85rem, 1.4vw, 1.1rem);
}

.page-home .home-fixtures__columns {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.85rem;
}

.page-home .home-fixtures__block h3 {
    margin: 0 0 0.55rem;
    font-size: 0.92rem;
}

.page-home .home-stack {
    display: grid;
    gap: 0.65rem;
}

.page-home .home-cities__map {
    position: relative;
    min-height: 220px;
    margin-bottom: 0.75rem;
    border: 1px solid rgba(20, 28, 39, 0.08);
    border-radius: 14px;
    background:
        radial-gradient(circle at 30% 35%, rgba(177, 15, 46, 0.12), transparent 34%),
        linear-gradient(180deg, #eef3f8, #dfe8f1);
    overflow: hidden;
}

.page-home .home-cities__map-label {
    position: absolute;
    top: 0.55rem;
    inset-inline-start: 0.65rem;
    color: #59677a;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.page-home .home-cities__pin {
    position: absolute;
    left: var(--x);
    top: var(--y);
    transform: translate(-50%, -50%);
    width: 0.72rem;
    height: 0.72rem;
    border-radius: 999px;
    background: var(--m2030-red, #b10f2e);
    box-shadow: 0 0 0 4px rgba(177, 15, 46, 0.18);
}

.page-home .home-cities__pin--stadium {
    background: var(--m2030-green, #0d6b45);
    box-shadow: 0 0 0 4px rgba(13, 107, 69, 0.18);
}

.page-home .home-cities__pin span {
    position: absolute;
    left: 50%;
    top: calc(100% + 0.28rem);
    transform: translateX(-50%);
    width: max-content;
    max-width: 8rem;
    color: var(--m2030-ink, #141c27);
    font-size: 0.62rem;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.page-home .home-cities__list {
    display: grid;
    gap: 0.45rem;
}

.page-home .home-cities__item {
    display: grid;
    gap: 0.08rem;
    padding: 0.55rem 0.65rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.72);
    text-decoration: none;
    color: inherit;
}

.page-home .home-cities__item span {
    color: var(--m2030-red, #b10f2e);
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.page-home .home-cities__item small {
    color: #59677a;
    font-size: 0.72rem;
}

.page-home .home-stadiums__grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.65rem;
}

.page-home .home-stadiums__item {
    display: grid;
    gap: 0.35rem;
    padding: 0.65rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.72);
    text-decoration: none;
    color: inherit;
}

.page-home .home-stadiums__item img {
    width: 100%;
    height: 72px;
    object-fit: cover;
    border-radius: 8px;
    opacity: 0.82;
}

.page-home .home-stadiums__item span {
    color: #59677a;
    font-size: 0.72rem;
}

.page-home .home-standings__grid {
    display: grid;
    gap: 0.75rem;
}

.page-home .home-standings__group {
    padding: 0.65rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.72);
}

.page-home .home-teams__list,
.page-home .home-teams__item {
    display: grid;
    gap: 0.45rem;
}

.page-home .home-teams__item {
    padding: 0.65rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.72);
    text-decoration: none;
    color: inherit;
}

.page-home .home-teams__item span {
    color: #59677a;
    font-size: 0.74rem;
}

.page-home .home-news__grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.75rem;
}

.page-home .home-partners__grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.65rem;
}

.page-home .home-partners__item {
    display: grid;
    gap: 0.45rem;
    justify-items: center;
    padding: 0.75rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.82);
    text-align: center;
}

.page-home .home-partners__logo {
    display: grid;
    place-items: center;
    width: 100%;
    min-height: 64px;
}

.page-home .home-partners__logo img {
    max-width: 100%;
    max-height: 52px;
    object-fit: contain;
}

.page-home .home-partners__logo span {
    display: inline-grid;
    place-items: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 999px;
    background: rgba(177, 15, 46, 0.1);
    color: var(--m2030-red, #b10f2e);
    font-weight: 900;
}

.page-home .home-fixtures,
.page-home .home-cities,
.page-home .home-stadiums,
.page-home .home-standings,
.page-home .home-bracket,
.page-home .home-teams {
    min-width: 0;
}

@media (max-width: 1100px) {
    .page-home .home-hero__layout {
        grid-template-columns: 1fr;
    }

    .page-home .home-quick-links__grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .page-home .home-metrics__grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .page-home .home-dashboard-grid {
        grid-template-columns: 1fr;
    }

    .page-home .home-news__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-home .home-partners__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 760px) {
    .page-home .home-hero {
        min-height: 0;
    }

    .page-home .home-quick-links__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-home .home-fixtures__columns,
    .page-home .home-stadiums__grid,
    .page-home .home-metrics__grid,
    .page-home .home-news__grid,
    .page-home .home-partners__grid {
        grid-template-columns: 1fr;
    }

    .page-home .home-hero__countdown-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

/* DESIGN HELL PHASE 2.1 — Exact homepage reference match */

.page-home {
    background: #f7f8fb;
    overflow-x: clip;
}

.page-home main {
    padding-top: 0.35rem;
}

.page-home .page-container {
    width: min(100%, 1180px);
    margin-inline: auto;
    display: grid;
    gap: clamp(0.85rem, 1.5vw, 1.15rem);
}

.page-home .home-ref-hero {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    border-radius: clamp(26px, 3vw, 34px);
    min-height: clamp(420px, 48vw, 520px);
    box-shadow: 0 22px 48px rgba(6, 17, 31, 0.14);
}

.page-home .home-ref-hero__bg {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(90deg, rgba(8, 12, 20, 0.55) 0%, rgba(8, 12, 20, 0.18) 38%, rgba(8, 12, 20, 0.42) 100%),
        linear-gradient(180deg, rgba(255, 160, 72, 0.28) 0%, rgba(177, 15, 46, 0.22) 38%, rgba(8, 12, 20, 0.62) 100%),
        url("{{ asset('assets/placeholders/city.svg') }}") left center/cover no-repeat,
        url("{{ asset('assets/placeholders/stadium.svg') }}") right center/cover no-repeat;
    filter: saturate(1.08) contrast(1.04);
}

.page-home .home-ref-hero__bg::after {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 50% 18%, rgba(201, 164, 76, 0.16), transparent 42%),
        linear-gradient(180deg, transparent 58%, rgba(5, 7, 13, 0.48) 100%);
    pointer-events: none;
}

.page-home .home-ref-hero__shell {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: minmax(220px, 260px) minmax(0, 1fr) minmax(220px, 260px);
    align-items: center;
    gap: clamp(0.75rem, 1.4vw, 1.1rem);
    min-height: inherit;
    padding: clamp(1.1rem, 2vw, 1.65rem);
}

.page-home .home-ref-next-match,
.page-home .home-ref-countdown {
    padding: clamp(0.85rem, 1.3vw, 1rem);
    border: 1px solid rgba(201, 164, 76, 0.28);
    border-radius: 18px;
    background: rgba(8, 12, 20, 0.78);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: #fff;
    box-shadow: 0 14px 32px rgba(0, 0, 0, 0.22);
}

.page-home .home-ref-next-match__eyebrow,
.page-home .home-ref-countdown__eyebrow {
    display: block;
    color: var(--m2030-gold, #c9a44c);
    font-size: 0.58rem;
    font-weight: 800;
    letter-spacing: 0.16em;
    text-transform: uppercase;
}

.page-home .home-ref-next-match__stage {
    margin: 0.35rem 0 0.55rem;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.page-home .home-ref-next-match__teams {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 0.35rem;
    align-items: center;
    text-align: center;
}

.page-home .home-ref-next-match__team strong {
    display: block;
    font-size: 0.88rem;
    line-height: 1.25;
}

.page-home .home-ref-next-match__vs {
    color: var(--m2030-gold, #c9a44c);
    font-size: 0.68rem;
    font-weight: 900;
}

.page-home .home-ref-next-match__meta {
    margin: 0.6rem 0 0.75rem;
    padding: 0;
    list-style: none;
    display: grid;
    gap: 0.18rem;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.7rem;
}

.page-home .home-ref-next-match__empty {
    margin: 0.35rem 0 0;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.78rem;
    line-height: 1.45;
}

.page-home .home-ref-hero__brand {
    text-align: center;
    color: #fff;
    padding-inline: 0.35rem;
}

.page-home .home-ref-hero__logo {
    width: auto;
    max-width: min(148px, 34vw);
    max-height: 5.5rem;
    margin-inline: auto;
    object-fit: contain;
    filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.28));
}

.page-home .home-ref-hero__title {
    margin: 0.55rem 0 0;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(2rem, 4.6vw, 3.2rem);
    font-weight: 900;
    line-height: 0.95;
    letter-spacing: -0.03em;
    text-transform: uppercase;
    background: linear-gradient(90deg, #fff 0%, #fff 42%, var(--m2030-gold, #c9a44c) 42%, var(--m2030-gold, #c9a44c) 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.page-home .home-ref-hero__slogan {
    display: grid;
    gap: 0.14rem;
    margin: 0.65rem 0 0;
    font-size: clamp(0.58rem, 0.85vw, 0.72rem);
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.9);
}

.page-home .home-ref-countdown__title {
    display: block;
    margin-top: 0.28rem;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.82);
}

.page-home .home-ref-countdown__date {
    margin: 0.25rem 0 0.55rem;
    color: rgba(255, 255, 255, 0.58);
    font-size: 0.66rem;
}

.page-home .home-ref-countdown__grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.32rem;
}

.page-home .home-ref-countdown__unit {
    padding: 0.42rem 0.2rem;
    border: 1px solid rgba(201, 164, 76, 0.22);
    border-radius: 10px;
    background: rgba(5, 7, 13, 0.45);
    text-align: center;
}

.page-home .home-ref-countdown__unit strong {
    display: block;
    font-size: clamp(1.05rem, 1.8vw, 1.45rem);
    line-height: 1;
}

.page-home .home-ref-countdown__unit span {
    display: block;
    margin-top: 0.18rem;
    color: rgba(255, 229, 168, 0.82);
    font-size: 0.5rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.page-home .home-ref-countdown__status {
    margin: 0.45rem 0 0.55rem;
    color: rgba(255, 255, 255, 0.58);
    font-size: 0.62rem;
    line-height: 1.35;
}

.page-home .home-ref-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 2.35rem;
    padding: 0.5rem 0.85rem;
    border-radius: 999px;
    font-size: 0.62rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    text-decoration: none;
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.page-home .home-ref-btn--primary {
    color: #fff;
    border: 1px solid rgba(201, 164, 76, 0.55);
    background: linear-gradient(135deg, #d11f3c 0%, var(--m2030-red, #b10f2e) 55%, var(--m2030-red-deep, #6f0d1b) 100%);
    box-shadow: 0 8px 18px rgba(177, 15, 46, 0.28);
}

.page-home .home-ref-btn--gold {
    color: #2a1608;
    border: 1px solid rgba(201, 164, 76, 0.72);
    background: linear-gradient(135deg, var(--m2030-gold, #c9a44c), var(--m2030-gold-strong, #a8842e));
}

.page-home .home-ref-btn--outline {
    width: auto;
    color: var(--m2030-ink, #141c27);
    border: 1px solid rgba(201, 164, 76, 0.55);
    background: #fff;
}

.page-home .home-ref-btn:hover,
.page-home .home-ref-btn:focus-visible {
    transform: translateY(-1px);
}

.page-home .home-ref-quick {
    position: relative;
    z-index: 2;
    margin-top: -1.35rem;
}

.page-home .home-ref-quick__heading {
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

.page-home .home-ref-quick__row {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 0.55rem;
}

.page-home .home-ref-quick-card {
    display: grid;
    gap: 0.22rem;
    padding: 0.75rem 0.7rem;
    border: 1px solid rgba(20, 28, 39, 0.07);
    border-radius: 16px;
    background: #fff;
    color: var(--m2030-ink, #141c27);
    text-decoration: none;
    box-shadow: 0 10px 24px rgba(6, 17, 31, 0.08);
    transition: transform 0.18s ease, border-color 0.18s ease;
}

.page-home .home-ref-quick-card:hover,
.page-home .home-ref-quick-card:focus-visible {
    transform: translateY(-2px);
    border-color: rgba(201, 164, 76, 0.42);
}

.page-home .home-ref-quick-card strong {
    font-size: 0.82rem;
}

.page-home .home-ref-quick-card span:last-child {
    color: #6b7788;
    font-size: 0.66rem;
}

.page-home .home-ref-quick-card__icon {
    display: block;
    width: 1.65rem;
    height: 1.65rem;
    border-radius: 999px;
}

.page-home .home-ref-quick-card__icon--red {
    background: var(--m2030-red, #b10f2e);
}

.page-home .home-ref-quick-card__icon--green {
    background: var(--m2030-green, #0d6b45);
}

.page-home .home-ref-quick-card__icon--gold {
    background: var(--m2030-gold-strong, #a8842e);
}

.page-home .home-ref-card {
    padding: clamp(0.9rem, 1.4vw, 1.15rem);
    border: 1px solid rgba(20, 28, 39, 0.07);
    border-radius: 20px;
    background: #fff;
    color: var(--m2030-ink, #141c27);
    box-shadow: 0 10px 26px rgba(6, 17, 31, 0.07);
    min-width: 0;
}

.page-home .home-ref-card__eyebrow {
    display: block;
    color: var(--m2030-red, #b10f2e);
    font-size: 0.58rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.page-home .home-ref-card__head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.page-home .home-ref-card__head--inline,
.page-home .home-ref-card__head--compact {
    margin-bottom: 0.55rem;
}

.page-home .home-ref-card__head h2,
.page-home .home-ref-card__head h3 {
    margin: 0.15rem 0 0;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(1rem, 1.4vw, 1.2rem);
    letter-spacing: -0.02em;
}

.page-home .home-ref-card__links {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
}

.page-home .home-ref-link {
    color: var(--m2030-red, #b10f2e);
    font-size: 0.66rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    text-decoration: none;
    white-space: nowrap;
}

.page-home .home-ref-link:hover,
.page-home .home-ref-link:focus-visible {
    color: var(--m2030-gold-strong, #a8842e);
}

.page-home .home-ref-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: clamp(0.75rem, 1.2vw, 0.95rem);
}

.page-home .home-ref-fixtures {
    grid-column: 1;
}

.page-home .home-ref-cities {
    grid-column: 2;
}

.page-home .home-ref-stadiums {
    grid-column: 1 / -1;
}

.page-home .home-ref-standings {
    grid-column: 1;
}

.page-home .home-ref-bracket {
    grid-column: 2;
}

.page-home .home-ref-fixtures__columns {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.75rem;
}

.page-home .home-ref-fixtures__block h3 {
    margin: 0 0 0.5rem;
    font-size: 0.82rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #6b7788;
}

.page-home .home-ref-stack {
    display: grid;
    gap: 0.55rem;
}

.page-home .home-ref-cities__map {
    position: relative;
    min-height: 200px;
    margin-bottom: 0.65rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 14px;
    background:
        radial-gradient(circle at 28% 38%, rgba(177, 15, 46, 0.1), transparent 32%),
        linear-gradient(180deg, #eef3f8, #dfe8f1);
    overflow: hidden;
}

.page-home .home-ref-cities__map-label {
    position: absolute;
    top: 0.5rem;
    inset-inline-start: 0.6rem;
    color: #6b7788;
    font-size: 0.58rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.page-home .home-ref-cities__pin {
    position: absolute;
    left: var(--x);
    top: var(--y);
    transform: translate(-50%, -50%);
    width: 0.65rem;
    height: 0.65rem;
    border-radius: 999px;
    background: var(--m2030-red, #b10f2e);
    box-shadow: 0 0 0 4px rgba(177, 15, 46, 0.16);
    text-decoration: none;
}

.page-home .home-ref-cities__pin--stadium {
    background: var(--m2030-green, #0d6b45);
    box-shadow: 0 0 0 4px rgba(13, 107, 69, 0.16);
}

.page-home .home-ref-cities__pin span {
    position: absolute;
    left: 50%;
    top: calc(100% + 0.22rem);
    transform: translateX(-50%);
    width: max-content;
    max-width: 7rem;
    color: var(--m2030-ink, #141c27);
    font-size: 0.58rem;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.page-home .home-ref-cities__list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.4rem;
}

.page-home .home-ref-cities__item {
    display: grid;
    gap: 0.06rem;
    padding: 0.48rem 0.55rem;
    border: 1px solid rgba(20, 28, 39, 0.05);
    border-radius: 10px;
    background: #fafbfd;
    text-decoration: none;
    color: inherit;
}

.page-home .home-ref-cities__item span {
    color: var(--m2030-red, #b10f2e);
    font-size: 0.56rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.page-home .home-ref-stadiums__layout {
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
    gap: 0.65rem;
}

.page-home .home-ref-stadiums__featured {
    display: grid;
    gap: 0.45rem;
    padding: 0.55rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 14px;
    background: #fafbfd;
    text-decoration: none;
    color: inherit;
}

.page-home .home-ref-stadiums__featured img {
    width: 100%;
    height: clamp(120px, 16vw, 160px);
    object-fit: cover;
    border-radius: 10px;
}

.page-home .home-ref-stadiums__featured strong {
    display: block;
    font-size: 0.92rem;
}

.page-home .home-ref-stadiums__featured span {
    color: #6b7788;
    font-size: 0.72rem;
}

.page-home .home-ref-stadiums__thumbs {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.45rem;
}

.page-home .home-ref-stadiums__thumb {
    display: grid;
    gap: 0.28rem;
    padding: 0.45rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 10px;
    background: #fafbfd;
    text-decoration: none;
    color: inherit;
}

.page-home .home-ref-stadiums__thumb img {
    width: 100%;
    height: 56px;
    object-fit: cover;
    border-radius: 8px;
}

.page-home .home-ref-stadiums__thumb span {
    font-size: 0.68rem;
    line-height: 1.25;
}

.page-home .home-ref-standings__groups {
    display: grid;
    gap: 0.65rem;
}

.page-home .home-ref-standings__group {
    padding: 0.55rem;
    border: 1px solid rgba(20, 28, 39, 0.05);
    border-radius: 12px;
    background: #fafbfd;
}

.page-home .home-ref-metrics__grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 0.5rem;
}

.page-home .home-ref-metrics__item {
    padding: 0.55rem;
    border: 1px solid rgba(20, 28, 39, 0.05);
    border-radius: 10px;
    background: #fafbfd;
}

.page-home .home-ref-metrics__item strong {
    display: block;
    color: var(--m2030-red, #b10f2e);
    font-size: 1.15rem;
}

.page-home .home-ref-metrics__item span {
    display: block;
    margin-top: 0.1rem;
    font-size: 0.72rem;
    font-weight: 800;
}

.page-home .home-ref-metrics__item small {
    display: block;
    margin-top: 0.12rem;
    color: #6b7788;
    font-size: 0.64rem;
    line-height: 1.35;
}

.page-home .home-ref-news__grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.75rem;
}

.page-home .home-ref-partners__row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.55rem;
}

.page-home .home-ref-partners__item {
    display: grid;
    gap: 0.35rem;
    justify-items: center;
    padding: 0.65rem;
    border: 1px solid rgba(20, 28, 39, 0.05);
    border-radius: 12px;
    background: #fafbfd;
    text-align: center;
}

.page-home .home-ref-partners__logo {
    display: grid;
    place-items: center;
    width: 100%;
    min-height: 56px;
}

.page-home .home-ref-partners__logo img {
    max-width: 100%;
    max-height: 48px;
    object-fit: contain;
}

.page-home .home-ref-partners__logo span {
    display: inline-grid;
    place-items: center;
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 999px;
    background: rgba(177, 15, 46, 0.1);
    color: var(--m2030-red, #b10f2e);
    font-weight: 900;
}

.page-home .home-ref-grid .match-card,
.page-home .home-ref-news .news-card {
    border-color: rgba(20, 28, 39, 0.06);
    box-shadow: none;
}

@media (max-width: 1080px) {
    .page-home .home-ref-hero__shell {
        grid-template-columns: 1fr;
    }

    .page-home .home-ref-hero__brand {
        order: 1;
    }

    .page-home .home-ref-countdown {
        order: 2;
    }

    .page-home .home-ref-next-match {
        order: 3;
    }

    .page-home .home-ref-quick__row {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .page-home .home-ref-grid {
        grid-template-columns: 1fr;
    }

    .page-home .home-ref-fixtures,
    .page-home .home-ref-cities,
    .page-home .home-ref-stadiums,
    .page-home .home-ref-standings,
    .page-home .home-ref-bracket {
        grid-column: auto;
    }

    .page-home .home-ref-metrics__grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .page-home .home-ref-news__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-home .home-ref-partners__row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 720px) {
    .page-home .home-ref-hero {
        min-height: 0;
        border-radius: 22px;
    }

    .page-home .home-ref-quick {
        margin-top: 0.65rem;
    }

    .page-home .home-ref-quick__row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-home .home-ref-fixtures__columns,
    .page-home .home-ref-stadiums__layout,
    .page-home .home-ref-stadiums__thumbs,
    .page-home .home-ref-cities__list,
    .page-home .home-ref-metrics__grid,
    .page-home .home-ref-news__grid,
    .page-home .home-ref-partners__row {
        grid-template-columns: 1fr;
    }

    .page-home .home-ref-countdown__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-home .home-ref-card__head {
        flex-direction: column;
        align-items: stretch;
    }
}

/* DESIGN HELL PHASE 2.2 — Designer assets homepage match */

.page-home {
    background: #fff;
    overflow-x: clip;
}

.page-home main {
    padding-top: 0.25rem;
}

.page-home .page-container {
    width: min(100%, 1200px);
    margin-inline: auto;
    display: grid;
    gap: 0.75rem;
}

.page-home .home-final-hero {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    border-radius: clamp(26px, 3vw, 34px);
    min-height: clamp(430px, 42vw, 520px);
    box-shadow: 0 18px 40px rgba(6, 17, 31, 0.12);
}

.page-home .home-final-hero__background {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(90deg, rgba(6, 10, 18, 0.52) 0%, rgba(6, 10, 18, 0.12) 34%, rgba(6, 10, 18, 0.12) 66%, rgba(6, 10, 18, 0.52) 100%),
        linear-gradient(180deg, rgba(6, 10, 18, 0.08) 0%, rgba(6, 10, 18, 0.28) 100%),
        var(--home-hero-bg);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.page-home .home-final-hero__layout {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: minmax(210px, 240px) minmax(0, 1fr) minmax(210px, 240px);
    align-items: center;
    gap: clamp(0.65rem, 1.2vw, 1rem);
    min-height: inherit;
    padding: clamp(1rem, 1.8vw, 1.45rem);
}

.page-home .home-final-next-match,
.page-home .home-final-countdown {
    align-self: center;
    max-width: 240px;
    padding: 0.75rem 0.8rem;
    border: 1px solid rgba(201, 164, 76, 0.32);
    border-radius: 16px;
    background: rgba(8, 12, 20, 0.82);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    color: #fff;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.24);
}

.page-home .home-final-next-match__eyebrow,
.page-home .home-final-countdown__eyebrow {
    display: block;
    color: var(--m2030-gold, #c9a44c);
    font-size: 0.56rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.page-home .home-final-next-match__stage {
    margin: 0.28rem 0 0.45rem;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.64rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.page-home .home-final-next-match__teams {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 0.25rem;
    align-items: center;
    text-align: center;
}

.page-home .home-final-next-match__teams strong {
    font-size: 0.82rem;
    line-height: 1.2;
}

.page-home .home-final-next-match__vs {
    color: var(--m2030-gold, #c9a44c);
    font-size: 0.62rem;
    font-weight: 900;
}

.page-home .home-final-next-match__meta {
    margin: 0.5rem 0 0.65rem;
    padding: 0;
    list-style: none;
    display: grid;
    gap: 0.14rem;
    color: rgba(255, 255, 255, 0.68);
    font-size: 0.64rem;
}

.page-home .home-final-next-match__empty {
    margin: 0.28rem 0 0;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.72rem;
    line-height: 1.4;
}

.page-home .home-final-next-match__venue {
    display: none;
}

.page-home .home-final-brand {
    text-align: center;
    color: #fff;
    padding-inline: 0.25rem;
}

.page-home .home-final-brand__logo {
    width: auto;
    max-width: min(156px, 36vw);
    max-height: 6rem;
    margin-inline: auto;
    object-fit: contain;
    filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.28));
}

.page-home .home-final-brand__title {
    margin: 0.45rem 0 0;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(2.1rem, 4.8vw, 3.35rem);
    font-weight: 900;
    line-height: 0.92;
    letter-spacing: -0.03em;
    text-transform: uppercase;
    text-shadow: 0 4px 18px rgba(0, 0, 0, 0.35);
    background: linear-gradient(90deg, #fff 0%, #fff 46%, var(--m2030-gold, #c9a44c) 46%, var(--m2030-gold, #c9a44c) 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.page-home .home-final-brand__slogan {
    display: grid;
    gap: 0.12rem;
    margin: 0.55rem 0 0;
    font-size: clamp(0.56rem, 0.82vw, 0.68rem);
    font-weight: 800;
    letter-spacing: 0.11em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.92);
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.35);
}

.page-home .home-final-countdown__title {
    display: block;
    margin-top: 0.22rem;
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.78);
}

.page-home .home-final-countdown__grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.28rem;
    margin-top: 0.45rem;
}

.page-home .home-final-countdown__unit {
    padding: 0.34rem 0.15rem;
    border: 1px solid rgba(201, 164, 76, 0.22);
    border-radius: 8px;
    background: rgba(5, 7, 13, 0.42);
    text-align: center;
}

.page-home .home-final-countdown__unit strong {
    display: block;
    font-size: clamp(0.95rem, 1.6vw, 1.25rem);
    line-height: 1;
}

.page-home .home-final-countdown__unit span {
    display: block;
    margin-top: 0.14rem;
    color: rgba(255, 229, 168, 0.82);
    font-size: 0.48rem;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.page-home .home-final-countdown__status {
    margin: 0.38rem 0 0.48rem;
    color: rgba(255, 255, 255, 0.55);
    font-size: 0.58rem;
    line-height: 1.3;
}

.page-home .home-final-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 2.15rem;
    padding: 0.42rem 0.75rem;
    border-radius: 999px;
    font-size: 0.58rem;
    font-weight: 900;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    text-decoration: none;
    transition: transform 0.16s ease;
}

.page-home .home-final-btn--primary {
    color: #fff;
    border: 1px solid rgba(201, 164, 76, 0.55);
    background: linear-gradient(135deg, #d11f3c 0%, var(--m2030-red, #b10f2e) 100%);
    box-shadow: 0 6px 14px rgba(177, 15, 46, 0.28);
}

.page-home .home-final-btn--gold {
    color: #2a1608;
    border: 1px solid rgba(201, 164, 76, 0.72);
    background: linear-gradient(135deg, var(--m2030-gold, #c9a44c), var(--m2030-gold-strong, #a8842e));
}

.page-home .home-final-btn--outline {
    width: auto;
    color: var(--m2030-ink, #141c27);
    border: 1px solid rgba(201, 164, 76, 0.55);
    background: #fff;
}

.page-home .home-final-btn:hover,
.page-home .home-final-btn:focus-visible {
    transform: translateY(-1px);
}

.page-home .home-final-quick {
    position: relative;
    z-index: 2;
    margin-top: -1.1rem;
}

.page-home .home-final-quick__heading {
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

.page-home .home-final-quick__row {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 0.45rem;
}

.page-home .home-final-quick-card {
    display: grid;
    gap: 0.16rem;
    padding: 0.62rem 0.58rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 14px;
    background: #fff;
    color: var(--m2030-ink, #141c27);
    text-decoration: none;
    box-shadow: 0 8px 20px rgba(6, 17, 31, 0.07);
    transition: transform 0.16s ease, border-color 0.16s ease;
}

.page-home .home-final-quick-card:hover,
.page-home .home-final-quick-card:focus-visible {
    transform: translateY(-2px);
    border-color: rgba(201, 164, 76, 0.4);
}

.page-home .home-final-quick-card strong {
    font-size: 0.76rem;
}

.page-home .home-final-quick-card span:last-child {
    color: #6b7788;
    font-size: 0.6rem;
}

.page-home .home-final-quick-card__icon {
    display: block;
    width: 1.45rem;
    height: 1.45rem;
    border-radius: 999px;
}

.page-home .home-final-quick-card__icon--red {
    background: var(--m2030-red, #b10f2e);
}

.page-home .home-final-quick-card__icon--green {
    background: var(--m2030-green, #0d6b45);
}

.page-home .home-final-quick-card__icon--gold {
    background: var(--m2030-gold-strong, #a8842e);
}

.page-home .home-final-card {
    padding: 0.75rem 0.85rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 18px;
    background: #fff;
    color: var(--m2030-ink, #141c27);
    box-shadow: 0 8px 22px rgba(6, 17, 31, 0.06);
    min-width: 0;
}

.page-home .home-final-card__eyebrow {
    display: block;
    color: var(--m2030-red, #b10f2e);
    font-size: 0.54rem;
    font-weight: 800;
    letter-spacing: 0.13em;
    text-transform: uppercase;
}

.page-home .home-final-card__head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.65rem;
    margin-bottom: 0.55rem;
}

.page-home .home-final-card__head--inline,
.page-home .home-final-card__head--compact {
    margin-bottom: 0.45rem;
}

.page-home .home-final-card__head h2,
.page-home .home-final-card__head h3 {
    margin: 0.12rem 0 0;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(0.95rem, 1.3vw, 1.1rem);
    letter-spacing: -0.02em;
}

.page-home .home-final-card__links {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
}

.page-home .home-final-link {
    color: var(--m2030-red, #b10f2e);
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    text-decoration: none;
    white-space: nowrap;
}

.page-home .home-final-link:hover,
.page-home .home-final-link:focus-visible {
    color: var(--m2030-gold-strong, #a8842e);
}

.page-home .home-final-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.65rem;
}

.page-home .home-final-fixtures {
    grid-column: 1;
}

.page-home .home-final-cities {
    grid-column: 2;
}

.page-home .home-final-stadiums {
    grid-column: 1 / -1;
}

.page-home .home-final-standings {
    grid-column: 1;
}

.page-home .home-final-bracket {
    grid-column: 2;
}

.page-home .home-final-fixtures__columns {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.55rem;
}

.page-home .home-final-fixtures__block h3 {
    margin: 0 0 0.4rem;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #6b7788;
}

.page-home .home-final-stack {
    display: grid;
    gap: 0.45rem;
}

.page-home .home-final-cities__map {
    margin-bottom: 0.5rem;
    border: 1px solid rgba(20, 28, 39, 0.05);
    border-radius: 12px;
    overflow: hidden;
    background: #f3f6fa;
}

.page-home .home-final-cities__map img {
    display: block;
    width: 100%;
    height: auto;
    max-height: 180px;
    object-fit: contain;
}

.page-home .home-final-cities__list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.35rem;
}

.page-home .home-final-cities__item {
    display: grid;
    gap: 0.04rem;
    padding: 0.4rem 0.48rem;
    border: 1px solid rgba(20, 28, 39, 0.05);
    border-radius: 8px;
    background: #fafbfd;
    text-decoration: none;
    color: inherit;
}

.page-home .home-final-cities__item span {
    color: var(--m2030-red, #b10f2e);
    font-size: 0.52rem;
    font-weight: 800;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.page-home .home-final-stadiums__layout {
    display: grid;
    grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
    gap: 0.55rem;
}

.page-home .home-final-stadiums__featured {
    display: grid;
    gap: 0.35rem;
    padding: 0.45rem;
    border: 1px solid rgba(20, 28, 39, 0.05);
    border-radius: 12px;
    background: #fafbfd;
    text-decoration: none;
    color: inherit;
}

.page-home .home-final-stadiums__featured img {
    width: 100%;
    height: clamp(100px, 14vw, 140px);
    object-fit: cover;
    border-radius: 8px;
}

.page-home .home-final-stadiums__featured strong {
    display: block;
    font-size: 0.86rem;
}

.page-home .home-final-stadiums__featured span {
    color: #6b7788;
    font-size: 0.68rem;
}

.page-home .home-final-stadiums__thumbs {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.35rem;
}

.page-home .home-final-stadiums__thumb {
    display: grid;
    gap: 0.22rem;
    padding: 0.38rem;
    border: 1px solid rgba(20, 28, 39, 0.05);
    border-radius: 8px;
    background: #fafbfd;
    text-decoration: none;
    color: inherit;
}

.page-home .home-final-stadiums__thumb img {
    width: 100%;
    height: 48px;
    object-fit: cover;
    border-radius: 6px;
}

.page-home .home-final-stadiums__thumb span {
    font-size: 0.62rem;
    line-height: 1.2;
}

.page-home .home-final-standings__groups {
    display: grid;
    gap: 0.5rem;
}

.page-home .home-final-standings__group {
    padding: 0.45rem;
    border: 1px solid rgba(20, 28, 39, 0.05);
    border-radius: 10px;
    background: #fafbfd;
}

.page-home .home-final-metrics__grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 0.4rem;
}

.page-home .home-final-metrics__item {
    padding: 0.45rem;
    border: 1px solid rgba(20, 28, 39, 0.05);
    border-radius: 8px;
    background: #fafbfd;
}

.page-home .home-final-metrics__item strong {
    display: block;
    color: var(--m2030-red, #b10f2e);
    font-size: 1.05rem;
}

.page-home .home-final-metrics__item span {
    display: block;
    margin-top: 0.08rem;
    font-size: 0.68rem;
    font-weight: 800;
}

.page-home .home-final-metrics__item small {
    display: block;
    margin-top: 0.1rem;
    color: #6b7788;
    font-size: 0.6rem;
    line-height: 1.3;
}

.page-home .home-final-news__grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.65rem;
}

.page-home .home-final-partners__row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.45rem;
}

.page-home .home-final-partners__item {
    display: grid;
    gap: 0.28rem;
    justify-items: center;
    padding: 0.55rem;
    border: 1px solid rgba(20, 28, 39, 0.05);
    border-radius: 10px;
    background: #fafbfd;
    text-align: center;
}

.page-home .home-final-partners__logo {
    display: grid;
    place-items: center;
    width: 100%;
    min-height: 48px;
}

.page-home .home-final-partners__logo img {
    max-width: 100%;
    max-height: 42px;
    object-fit: contain;
}

.page-home .home-final-partners__logo span {
    display: inline-grid;
    place-items: center;
    width: 2rem;
    height: 2rem;
    border-radius: 999px;
    background: rgba(177, 15, 46, 0.1);
    color: var(--m2030-red, #b10f2e);
    font-weight: 900;
}

.page-home .home-final-grid .match-card,
.page-home .home-final-news .news-card {
    border-color: rgba(20, 28, 39, 0.06);
    box-shadow: none;
    padding: 0.55rem 0.65rem;
}

.page-home .home-final-grid .match-card__teams,
.page-home .home-final-grid .news-card__body {
    gap: 0.35rem;
}

@media (max-width: 1024px) {
    .page-home .home-final-hero__layout {
        grid-template-columns: minmax(190px, 220px) minmax(0, 1fr) minmax(190px, 220px);
    }

    .page-home .home-final-next-match,
    .page-home .home-final-countdown {
        max-width: 220px;
    }

    .page-home .home-final-quick__row {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .page-home .home-final-grid {
        grid-template-columns: 1fr;
    }

    .page-home .home-final-fixtures,
    .page-home .home-final-cities,
    .page-home .home-final-stadiums,
    .page-home .home-final-standings,
    .page-home .home-final-bracket {
        grid-column: auto;
    }

    .page-home .home-final-news__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-home .home-final-partners__row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-home .home-final-metrics__grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 760px) {
    .page-home .home-final-hero {
        min-height: 0;
        border-radius: 22px;
    }

    .page-home .home-final-hero__layout {
        grid-template-columns: 1fr;
        padding: 0.85rem;
    }

    .page-home .home-final-brand {
        order: 1;
    }

    .page-home .home-final-countdown {
        order: 2;
        max-width: none;
    }

    .page-home .home-final-next-match {
        order: 3;
        max-width: none;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 88px;
        gap: 0.55rem;
        align-items: end;
    }

    .page-home .home-final-next-match__venue {
        display: block;
        width: 88px;
        height: 72px;
        object-fit: cover;
        border-radius: 10px;
        grid-row: 1 / span 6;
        grid-column: 2;
        align-self: center;
    }

    .page-home .home-final-next-match__eyebrow,
    .page-home .home-final-next-match__stage,
    .page-home .home-final-next-match__teams,
    .page-home .home-final-next-match__meta,
    .page-home .home-final-next-match__empty,
    .page-home .home-final-next-match .home-final-btn {
        grid-column: 1;
    }

    .page-home .home-final-quick {
        margin-top: 0.55rem;
    }

    .page-home .home-final-quick__row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-home .home-final-fixtures__columns,
    .page-home .home-final-stadiums__layout,
    .page-home .home-final-stadiums__thumbs,
    .page-home .home-final-cities__list,
    .page-home .home-final-metrics__grid,
    .page-home .home-final-news__grid,
    .page-home .home-final-partners__row {
        grid-template-columns: 1fr;
    }

    .page-home .home-final-countdown__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-home .home-final-card__head {
        flex-direction: column;
        align-items: stretch;
    }
}

/* DESIGN HELL PHASE 2.3 — Homepage visual bugfix */

.page-home .page-container {
    gap: 0.55rem;
}

.page-home .home-final-hero,
.page-home .home-final-quick,
.page-home .home-final-grid,
.page-home .home-final-card,
.page-home .home-final-news,
.page-home .home-final-partners {
    scroll-margin-top: var(--m2030-header-total, 5rem);
}

.page-home .home-final-quick {
    position: relative;
    z-index: 2;
    margin-top: -0.45rem;
    margin-bottom: 0.35rem;
    padding-top: 0.15rem;
}

.page-home .home-final-quick__row {
    gap: 0.4rem;
}

.page-home .home-final-quick-card {
    min-height: 0;
    max-height: 92px;
    padding: 0.52rem 0.5rem;
    align-content: start;
}

.page-home .home-final-quick-card strong {
    font-size: 0.72rem;
    line-height: 1.15;
}

.page-home .home-final-quick-card span:last-child {
    font-size: 0.56rem;
}

.page-home .home-final-quick-card__icon {
    width: 1.25rem;
    height: 1.25rem;
}

.page-home .home-final-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 0.55rem;
    align-items: start;
}

.page-home .home-final-grid > * {
    min-width: 0;
}

.page-home .home-final-card {
    padding: 0.65rem 0.75rem;
}

.page-home .home-final-card__head {
    margin-bottom: 0.45rem;
}

.page-home .home-final-metrics--secondary {
    padding: 0.55rem 0.65rem;
    border-style: dashed;
    background: #fafbfd;
    box-shadow: none;
}

.page-home .home-final-metrics--secondary .home-final-card__head h2 {
    font-size: 0.95rem;
}

.page-home .home-final-metrics--secondary .home-final-metrics__grid {
    grid-template-columns: repeat(auto-fit, minmax(108px, 1fr));
    gap: 0.35rem;
}

.page-home .home-final-metrics--secondary .home-final-metrics__item {
    padding: 0.38rem 0.42rem;
}

.page-home .home-final-metrics--secondary .home-final-metrics__item strong {
    font-size: 0.92rem;
}

.page-home .home-final-metrics--secondary .home-final-metrics__item span {
    font-size: 0.62rem;
}

.page-home .home-final-metrics--secondary .home-final-metrics__item small {
    display: none;
}

.page-home .home-final-fixtures__columns {
    gap: 0.45rem;
}

.page-home .home-final-fixtures__scroll {
    max-height: 280px;
    overflow-y: auto;
    overscroll-behavior: contain;
    padding-inline-end: 0.15rem;
}

.page-home .home-final-fixtures__scroll .match-card {
    padding: 0.42rem 0.5rem;
}

.page-home .home-final-fixtures__scroll .match-card__head {
    margin-bottom: 0.25rem;
}

.page-home .home-final-fixtures__scroll .match-card__teams {
    gap: 0.25rem;
}

.page-home .home-final-fixtures__scroll .match-card__team strong {
    font-size: 0.78rem;
}

.page-home .home-final-fixtures__scroll .score-box {
    font-size: 0.82rem;
    min-width: 2.75rem;
}

.page-home .home-final-fixtures__scroll .match-card__meta {
    font-size: 0.62rem;
    gap: 0.2rem;
}

.page-home .home-final-cities__map {
    display: grid;
    place-items: center;
    min-height: 0;
    max-height: 240px;
    padding: 0.35rem;
    margin-bottom: 0.45rem;
    background: #f3f6fa;
}

.page-home .home-final-cities__map img {
    width: 100%;
    height: auto;
    max-height: 220px;
    object-fit: contain;
}

.page-home .home-final-cities__list {
    gap: 0.3rem;
}

.page-home .home-final-cities__item {
    padding: 0.35rem 0.42rem;
}

.page-home .home-final-stadiums__layout {
    gap: 0.45rem;
    align-items: stretch;
}

.page-home .home-final-stadiums__featured img {
    height: 118px;
    max-height: 118px;
    object-fit: cover;
}

.page-home .home-final-stadiums__thumbs {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    align-content: start;
}

.page-home .home-final-stadiums__thumb img {
    height: 52px;
    max-height: 52px;
    object-fit: cover;
}

.page-home .home-final-standings__groups {
    gap: 0.4rem;
}

.page-home .home-final-standings__group {
    padding: 0.38rem;
}

.page-home .home-final-standings__group .table-shell {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    max-width: 100%;
}

.page-home .home-final-standings__group .table-shell table {
    min-width: 100%;
    font-size: 0.68rem;
}

.page-home .home-final-standings__group .table-shell th,
.page-home .home-final-standings__group .table-shell td {
    padding: 0.28rem 0.32rem;
    white-space: nowrap;
}

.page-home .home-final-bracket__scroll {
    max-height: 240px;
    overflow-y: auto;
    overscroll-behavior: contain;
}

.page-home .home-final-bracket__scroll .match-card {
    padding: 0.42rem 0.5rem;
}

.page-home .home-final-news {
    scroll-margin-top: var(--m2030-header-total, 5rem);
}

.page-home .home-final-news__grid {
    gap: 0.55rem;
}

.page-home .home-final-partners__row {
    gap: 0.4rem;
}

@media (min-width: 1025px) {
    .page-home .home-final-fixtures,
    .page-home .home-final-cities {
        grid-column: span 1;
    }

    .page-home .home-final-stadiums {
        grid-column: 1 / -1;
    }

    .page-home .home-final-standings,
    .page-home .home-final-bracket {
        grid-column: span 1;
    }
}

@media (max-width: 1024px) {
    .page-home .home-final-quick {
        margin-top: 0.35rem;
    }

    .page-home .home-final-quick__row {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .page-home .home-final-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 760px) {
    .page-home .home-final-quick {
        margin-top: 0.45rem;
    }

    .page-home .home-final-quick__row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-home .home-final-quick-card {
        max-height: none;
    }

    .page-home .home-final-fixtures__scroll,
    .page-home .home-final-bracket__scroll {
        max-height: none;
        overflow: visible;
    }

    .page-home .home-final-cities__map {
        max-height: 200px;
    }

    .page-home .home-final-cities__map img {
        max-height: 180px;
    }

    .page-home .home-final-metrics--secondary .home-final-metrics__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

/* DESIGN HELL PHASE 3 — Global public media binding */

.entity-card__media--bound,
.partner-logo-wrap {
    position: relative;
    overflow: hidden;
    background: linear-gradient(180deg, #f3f6fa, #e9eef4);
}

.entity-card__media--bound .media-bound-image,
.entity-card__media--bound .entity-card__image,
.news-card__media.entity-card__media--bound .media-bound-image {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.entity-card__media--flag,
.entity-detail-hero--flag {
    background: linear-gradient(180deg, #fff, #f7f8fb);
}

.entity-card__media--flag .team-flag,
.entity-detail-hero--flag .team-flag {
    object-fit: contain;
    padding: 18%;
}

.entity-card__media--avatar,
.entity-detail-hero--avatar {
    background: radial-gradient(circle at 50% 35%, #eef3f8, #dfe8f1);
}

.entity-card__media--avatar .player-avatar,
.entity-detail-hero--avatar .player-avatar {
    object-fit: contain;
    padding: 22%;
}

.entity-card__media--avatar .player-avatar--flag,
.entity-detail-hero--avatar .player-avatar--flag {
    padding: 28%;
}

.media-bound-image--placeholder {
    object-fit: contain !important;
    padding: 12%;
    opacity: 0.92;
}

.entity-detail-hero--bound {
    aspect-ratio: 16 / 9;
    max-height: 420px;
    border-radius: var(--m2030-radius-md, 16px);
    overflow: hidden;
    background: linear-gradient(180deg, #eef3f8, #dfe8f1);
}

.entity-detail-hero--bound .entity-detail-hero__media,
.entity-detail-hero--bound .media-bound-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.entity-detail-hero--flag .entity-detail-hero__media,
.entity-detail-hero--flag .media-bound-image {
    object-fit: contain;
    padding: 10%;
}

.entity-detail-hero--avatar .entity-detail-hero__media,
.entity-detail-hero--avatar .media-bound-image {
    object-fit: contain;
    padding: 12%;
}

.partner-logo-wrap {
    aspect-ratio: 16 / 9;
    display: grid;
    place-items: center;
}

.partner-logo-wrap .partner-logo,
.partner-logo-wrap .media-bound-image {
    max-width: 72%;
    max-height: 72%;
    width: auto;
    height: auto;
    object-fit: contain;
}

.partner-logo--text {
    display: inline-grid;
    place-items: center;
    width: 3rem;
    height: 3rem;
    border-radius: 999px;
    background: rgba(177, 15, 46, 0.1);
    color: var(--m2030-red, #b10f2e);
    font-weight: 900;
}

.news-card__media.entity-card__media--bound {
    aspect-ratio: 16 / 10;
}

.listing-grid .entity-card__media--bound {
    aspect-ratio: 16 / 10;
}

.card-grid .news-card__media.entity-card__media--bound {
    min-height: 180px;
}

.team-flag {
    background: transparent;
}

.player-avatar {
    border-radius: 999px;
}

@media (max-width: 760px) {
    .entity-detail-hero--bound {
        max-height: 260px;
    }

    .listing-grid .entity-card__media--bound {
        aspect-ratio: 16 / 11;
    }
}

/* DESIGN HELL PHASE 3.1 — Stadium and city media binding fix */

.entity-card__media--bound .media-bound-image:not(.media-bound-image--placeholder),
.entity-detail-hero--bound .media-bound-image:not(.media-bound-image--placeholder) {
    object-fit: cover !important;
    width: 100%;
    height: 100%;
}

.entity-card__media--bound .media-bound-image--placeholder {
    object-fit: contain !important;
    padding: 10%;
    background: linear-gradient(180deg, #f3f6fa, #e9eef4);
}

.listing-grid .entity-card__media--bound {
    min-height: 168px;
}

.entity-detail-hero--bound.entity-detail-hero--photo {
    background: #dfe8f1;
}

/* DESIGN HELL PHASE 3.2 — Public navigation and action fixes */

@media (max-width: 1280px) and (min-width: 1025px) {
    .site-header__utility-tools > a.shell-icon-button.header-map-action {
        display: inline-grid !important;
    }
}

.header-map-action {
    flex-shrink: 0;
}

.header-map-action.is-active {
    color: var(--m2030-red, #b10f2e);
    background: rgba(177, 15, 46, 0.08);
}

.page-home .home-final-quick-card--map:hover,
.page-home .home-final-quick-card--map:focus-visible {
    border-color: rgba(177, 15, 46, 0.28);
}

.page-home .home-final-quick-card__icon--map {
    background: linear-gradient(135deg, rgba(177, 15, 46, 0.16), rgba(0, 98, 51, 0.14));
    mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%23000' d='m9 18.7-6 2.1V5.2l6-2.1 6 2.2 6-2.1v15.6l-6 2.1-6-2.2Zm1-2 4 1.5V7.2l-4-1.5v11Zm-2 0V5.8L5 6.9V18l3-1.3Zm8 1.5 3-1.1V6l-3 1.1v11.1Z'/%3E%3C/svg%3E");
    mask-repeat: no-repeat;
    mask-position: center;
    mask-size: 58%;
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%23000' d='m9 18.7-6 2.1V5.2l6-2.1 6 2.2 6-2.1v15.6l-6 2.1-6-2.2Zm1-2 4 1.5V7.2l-4-1.5v11Zm-2 0V5.8L5 6.9V18l3-1.3Zm8 1.5 3-1.1V6l-3 1.1v11.1Z'/%3E%3C/svg%3E");
    -webkit-mask-repeat: no-repeat;
    -webkit-mask-position: center;
    -webkit-mask-size: 58%;
}

/* DESIGN HELL PHASE 4 — Stadiums and host cities inner page redesign */

.page-inner .entity-ref-page {
    margin: 0 calc(-1 * clamp(0.75rem, 2vw, 1.25rem));
    background: #f7f9fc;
}

.page-inner .entity-ref-hero {
    position: relative;
    overflow: hidden;
    padding: clamp(1.75rem, 3.5vw, 2.75rem) clamp(1rem, 3vw, 2rem);
    color: #fff;
    background:
        radial-gradient(circle at 88% 12%, rgba(212, 168, 67, 0.22), transparent 34%),
        linear-gradient(135deg, #1a0a0d 0%, #5c0818 42%, #b10f2e 100%);
    border-bottom: 3px solid rgba(212, 168, 67, 0.55);
}

.page-inner .entity-ref-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    opacity: 0.12;
    background-image: none;
    background-size: 280px auto;
    pointer-events: none;
}

.page-inner .entity-ref-hero__content {
    position: relative;
    z-index: 1;
    width: min(100%, 1120px);
    margin: 0 auto;
}

.page-inner .entity-ref-hero__eyebrow,
.page-inner .entity-ref-section__eyebrow {
    display: inline-block;
    margin: 0 0 0.55rem;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: rgba(255, 236, 196, 0.92);
}

.page-inner .entity-ref-hero h1 {
    margin: 0;
    font-size: clamp(1.85rem, 4vw, 2.75rem);
    line-height: 1.08;
    letter-spacing: -0.02em;
}

.page-inner .entity-ref-hero p {
    margin: 0.75rem 0 0;
    max-width: 42rem;
    color: rgba(255, 255, 255, 0.88);
    font-size: clamp(0.98rem, 1.6vw, 1.08rem);
    line-height: 1.55;
}

.page-inner .entity-ref-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem 1rem;
    align-items: center;
}

.page-inner .entity-ref-meta--hero {
    margin-top: 1.15rem;
}

.page-inner .entity-ref-meta--card {
    margin-top: 0.55rem;
    color: #64748b;
    font-size: 0.88rem;
}

.page-inner .entity-ref-meta--card span + span::before,
.page-inner .entity-ref-meta--hero span + span::before {
    content: "•";
    margin-inline-end: 1rem;
    color: rgba(255, 255, 255, 0.45);
}

.page-inner .entity-ref-meta--card span + span::before {
    color: #cbd5e1;
}

.page-inner .entity-ref-stat {
    display: grid;
    gap: 0.15rem;
}

.page-inner .entity-ref-meta--hero .entity-ref-stat {
    min-width: 6.5rem;
    padding: 0.65rem 0.85rem;
    border-radius: 0.85rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.14);
    backdrop-filter: blur(6px);
}

.page-inner .entity-ref-meta--hero .entity-ref-stat strong {
    font-size: 1.15rem;
    line-height: 1.1;
}

.page-inner .entity-ref-meta--hero .entity-ref-stat span {
    font-size: 0.76rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.78);
}

.page-inner .entity-ref-section {
    width: min(calc(100% - clamp(1rem, 3vw, 2rem)), 1120px);
    margin: 0 auto;
    padding: clamp(1.15rem, 2.5vw, 1.75rem) 0 clamp(1.5rem, 3vw, 2.25rem);
}

.page-inner .entity-ref-section__title {
    margin: 0 0 0.85rem;
    font-size: clamp(1.05rem, 2vw, 1.25rem);
    color: #0f172a;
}

.page-inner .entity-ref-section__head {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem 1rem;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 0.95rem;
}

.page-inner .entity-ref-section__head h2 {
    margin: 0.2rem 0 0;
    font-size: clamp(1.15rem, 2.2vw, 1.45rem);
    color: #0f172a;
}

.page-inner .entity-ref-section__link {
    color: var(--m2030-red, #b10f2e);
    font-weight: 700;
    font-size: 0.92rem;
    text-decoration: none;
}

.page-inner .entity-ref-section__link:hover,
.page-inner .entity-ref-section__link:focus-visible {
    text-decoration: underline;
}

.page-inner .entity-ref-section--panel {
    padding: 1rem 1.05rem;
    border-radius: 1rem;
    background: #fff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
}

.page-inner .entity-ref-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: clamp(0.85rem, 1.8vw, 1.15rem);
}

.page-inner .entity-ref-grid--compact {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.page-inner .entity-ref-card {
    display: flex;
    flex-direction: column;
    min-width: 0;
    overflow: hidden;
    border-radius: 1rem;
    background: #fff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
}

.page-inner .entity-ref-card:hover,
.page-inner .entity-ref-card:focus-within {
    transform: translateY(-2px);
    border-color: rgba(177, 15, 46, 0.18);
    box-shadow: 0 16px 34px rgba(15, 23, 42, 0.1);
}

.page-inner .entity-ref-card__media {
    display: block;
    aspect-ratio: 16 / 9;
    overflow: hidden;
    background: linear-gradient(180deg, #e8eef5, #d7e0ea);
}

.page-inner .entity-ref-card__media .media-bound-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.page-inner .entity-ref-card__media .media-bound-image--placeholder {
    object-fit: contain !important;
    padding: 12%;
    background: linear-gradient(180deg, #f3f6fa, #e9eef4);
}

.page-inner .entity-ref-card__body {
    display: flex;
    flex: 1;
    flex-direction: column;
    gap: 0.35rem;
    padding: 0.95rem 1rem 1rem;
}

.page-inner .entity-ref-card__body h3 {
    margin: 0;
    font-size: 1.02rem;
    line-height: 1.25;
}

.page-inner .entity-ref-card__body h3 a {
    color: #0f172a;
    text-decoration: none;
}

.page-inner .entity-ref-card__body h3 a:hover,
.page-inner .entity-ref-card__body h3 a:focus-visible {
    color: var(--m2030-red, #b10f2e);
}

.page-inner .entity-ref-card__location {
    margin: 0;
    color: #64748b;
    font-size: 0.9rem;
}

.page-inner .entity-ref-card__cta {
    margin-top: auto;
    padding-top: 0.55rem;
    color: var(--m2030-red, #b10f2e);
    font-size: 0.88rem;
    font-weight: 800;
    letter-spacing: 0.02em;
    text-decoration: none;
}

.page-inner .entity-ref-card__cta:hover,
.page-inner .entity-ref-card__cta:focus-visible {
    text-decoration: underline;
}

.page-inner .entity-ref-card--compact .entity-ref-card__body {
    padding: 0.8rem 0.85rem 0.9rem;
}

.page-inner .entity-ref-hero--detail {
    min-height: clamp(240px, 38vw, 420px);
    padding: 0;
    display: grid;
    align-items: end;
    background: #111;
}

.page-inner .entity-ref-hero--detail .entity-ref-hero__media {
    position: absolute;
    inset: 0;
}

.page-inner .entity-ref-hero--detail .entity-ref-hero__media .media-bound-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.page-inner .entity-ref-hero--detail .entity-ref-hero__media .media-bound-image--placeholder {
    object-fit: contain !important;
    padding: 8%;
    background: linear-gradient(180deg, #243041, #111827);
}

.page-inner .entity-ref-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(8, 10, 16, 0.18) 0%, rgba(8, 10, 16, 0.72) 58%, rgba(8, 10, 16, 0.92) 100%);
}

.page-inner .entity-ref-hero--detail .entity-ref-hero__content {
    position: relative;
    z-index: 2;
    padding: clamp(1.25rem, 3vw, 2rem);
}

.page-inner .entity-ref-back-link {
    display: inline-flex;
    margin-bottom: 0.65rem;
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.88rem;
    font-weight: 700;
    text-decoration: none;
}

.page-inner .entity-ref-back-link:hover,
.page-inner .entity-ref-back-link:focus-visible {
    color: #fff;
    text-decoration: underline;
}

.page-inner .entity-ref-hero__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem;
    margin-top: 1rem;
}

.page-inner .entity-ref-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 2.45rem;
    padding: 0.55rem 1rem;
    border-radius: 999px;
    font-size: 0.88rem;
    font-weight: 800;
    text-decoration: none;
    border: 1px solid transparent;
}

.page-inner .entity-ref-action--primary {
    color: #1a0a0d;
    background: linear-gradient(135deg, #f4d37b, #d4a843);
    border-color: rgba(212, 168, 67, 0.65);
}

.page-inner .entity-ref-action--ghost {
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.24);
}

.page-inner .entity-ref-action:hover,
.page-inner .entity-ref-action:focus-visible {
    filter: brightness(1.03);
}

.page-inner .entity-ref-detail {
    display: grid;
    gap: clamp(0.85rem, 2vw, 1.15rem);
    width: min(calc(100% - clamp(1rem, 3vw, 2rem)), 1120px);
    margin: 0 auto;
    padding-bottom: clamp(1.25rem, 3vw, 2rem);
}

.page-inner .entity-ref-detail-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.75rem;
}

.page-inner .entity-ref-stat--card {
    padding: 0.85rem 0.95rem;
    border-radius: 0.9rem;
    background: #fff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
}

.page-inner .entity-ref-stat--card span {
    display: block;
    margin-bottom: 0.25rem;
    color: #64748b;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.page-inner .entity-ref-stat--card strong {
    color: #0f172a;
    font-size: 1rem;
    line-height: 1.35;
    word-break: break-word;
}

.page-inner .entity-ref-stat--card a {
    color: var(--m2030-red, #b10f2e);
    text-decoration: none;
}

.page-inner .entity-ref-stat--card a:hover,
.page-inner .entity-ref-stat--card a:focus-visible {
    text-decoration: underline;
}

.page-inner .entity-ref-copy {
    color: #334155;
    line-height: 1.65;
}

.page-inner .entity-ref-match-feed {
    display: grid;
    gap: 0.65rem;
}

.page-inner .entity-ref-match-feed .match-card {
    margin: 0;
}

.page-inner .entity-ref-empty .empty-state {
    margin: 0;
}

.page-inner .entity-ref-pagination {
    margin-top: 1rem;
}

.page-inner .entity-ref-pagination .pagination-shell {
    justify-content: center;
}

@media (max-width: 1080px) {
    .page-inner .entity-ref-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-inner .entity-ref-grid--compact,
    .page-inner .entity-ref-detail-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .page-inner .entity-ref-page {
        margin: 0 calc(-1 * clamp(0.55rem, 2vw, 0.85rem));
    }

    .page-inner .entity-ref-grid,
    .page-inner .entity-ref-grid--compact,
    .page-inner .entity-ref-detail-grid {
        grid-template-columns: minmax(0, 1fr);
    }

    .page-inner .entity-ref-meta--hero .entity-ref-stat {
        min-width: calc(50% - 0.5rem);
        flex: 1 1 calc(50% - 0.5rem);
    }

    .page-inner .entity-ref-section--panel {
        padding: 0.85rem;
    }

    .page-inner .entity-ref-hero--detail {
        min-height: 280px;
    }
}

/* DESIGN HELL PHASE 5 — Teams players news partners redesign */

.page-inner .entity-ref-card__media--flag,
.page-inner .team-ref-flag {
    aspect-ratio: 16 / 10;
    display: grid;
    place-items: center;
    background: linear-gradient(180deg, #f8fafc, #edf2f7);
}

.page-inner .entity-ref-card__media--flag .media-bound-image,
.page-inner .team-ref-flag .media-bound-image {
    width: auto;
    height: auto;
    max-width: 58%;
    max-height: 72%;
    object-fit: contain;
}

.page-inner .entity-ref-card__media--avatar,
.page-inner .player-ref-avatar {
    aspect-ratio: 4 / 5;
    display: grid;
    place-items: center;
    background: linear-gradient(180deg, #f3f6fa, #e6edf5);
}

.page-inner .entity-ref-card__media--avatar .media-bound-image,
.page-inner .player-ref-avatar .media-bound-image {
    width: auto;
    height: auto;
    max-width: 72%;
    max-height: 82%;
    object-fit: contain;
}

.page-inner .entity-ref-card__media--avatar .player-avatar--flag,
.page-inner .player-ref-avatar .player-avatar--flag {
    border-radius: 999px;
}

.page-inner .entity-ref-card__media--logo,
.page-inner .partner-ref-logo {
    aspect-ratio: 16 / 10;
    display: grid;
    place-items: center;
    background: linear-gradient(180deg, #fff, #f3f6fa);
}

.page-inner .partner-ref-logo .partner-logo,
.page-inner .partner-ref-logo .media-bound-image {
    max-width: 72%;
    max-height: 72%;
    object-fit: contain;
}

.page-inner .news-ref-cover {
    aspect-ratio: 16 / 10;
    overflow: hidden;
    background: linear-gradient(180deg, #e8eef5, #d7e0ea);
}

.page-inner .news-ref-cover .media-bound-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.page-inner .news-ref-cover .media-bound-image--placeholder {
    object-fit: contain !important;
    padding: 12%;
}

.page-inner .entity-ref-grid--news {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.page-inner .entity-ref-grid--news .entity-ref-card--featured {
    grid-column: span 2;
    grid-row: span 2;
}

.page-inner .entity-ref-grid--news .entity-ref-card--featured .news-ref-cover,
.page-inner .entity-ref-grid--news .entity-ref-card--featured .entity-ref-card__media {
    aspect-ratio: 16 / 11;
}

.page-inner .entity-ref-grid--players {
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.page-inner .entity-ref-grid--partners {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.page-inner .entity-ref-card__excerpt {
    margin: 0;
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.55;
}

.page-inner .entity-ref-hero--team,
.page-inner .entity-ref-hero--player {
    background: linear-gradient(135deg, #1a0a0d 0%, #5c0818 42%, #b10f2e 100%);
}

.page-inner .entity-ref-hero--team .entity-ref-hero__media,
.page-inner .entity-ref-hero--player .entity-ref-hero__media {
    position: absolute;
    inset: 0;
    opacity: 0.28;
}

.page-inner .entity-ref-hero__media--flag,
.page-inner .team-ref-flag--hero {
    display: grid;
    place-items: center;
}

.page-inner .entity-ref-hero__media--flag .media-bound-image,
.page-inner .team-ref-flag--hero .media-bound-image {
    max-width: min(320px, 42vw);
    max-height: min(220px, 34vw);
    object-fit: contain;
    filter: drop-shadow(0 10px 24px rgba(0, 0, 0, 0.35));
}

.page-inner .entity-ref-hero__media--avatar,
.page-inner .player-ref-avatar--hero {
    display: grid;
    place-items: center;
}

.page-inner .entity-ref-hero__media--avatar .media-bound-image,
.page-inner .player-ref-avatar--hero .media-bound-image {
    max-width: min(240px, 34vw);
    max-height: min(280px, 40vw);
    border-radius: 999px;
    object-fit: cover;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
}

.page-inner .entity-ref-hero--news .entity-ref-hero__media {
    position: absolute;
    inset: 0;
}

.page-inner .entity-ref-hero--news .news-ref-cover .media-bound-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.page-inner .entity-ref-detail--article {
    grid-template-columns: minmax(0, 1fr) minmax(260px, 320px);
    align-items: start;
}

.page-inner .article-ref-content {
    max-width: 68ch;
}

.page-inner .entity-ref-lead {
    margin: 0 0 1rem;
    color: #334155;
    font-size: 1.08rem;
    line-height: 1.65;
    font-weight: 600;
}

.page-inner .entity-ref-related-list {
    display: grid;
    gap: 0.65rem;
}

.page-inner .entity-ref-related-item {
    display: grid;
    grid-template-columns: 88px minmax(0, 1fr);
    gap: 0.75rem;
    align-items: center;
    padding: 0.55rem;
    border-radius: 0.85rem;
    text-decoration: none;
    color: inherit;
    border: 1px solid rgba(15, 23, 42, 0.08);
    background: #fff;
}

.page-inner .entity-ref-related-item:hover,
.page-inner .entity-ref-related-item:focus-visible {
    border-color: rgba(177, 15, 46, 0.22);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
}

.page-inner .news-ref-cover--thumb {
    aspect-ratio: 16 / 10;
    border-radius: 0.55rem;
    overflow: hidden;
}

.page-inner .entity-ref-related-item__body {
    display: grid;
    gap: 0.25rem;
}

.page-inner .entity-ref-related-item__body strong {
    color: #0f172a;
    font-size: 0.95rem;
    line-height: 1.35;
}

.page-inner .entity-ref-related-item__body span {
    color: #64748b;
    font-size: 0.82rem;
}

.page-inner .entity-ref-card--partner h3 {
    margin: 0;
    font-size: 1.02rem;
    color: #0f172a;
}

@media (max-width: 1080px) {
    .page-inner .entity-ref-grid--news {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-inner .entity-ref-grid--news .entity-ref-card--featured {
        grid-column: span 2;
        grid-row: span 1;
    }

    .page-inner .entity-ref-grid--players {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-inner .entity-ref-grid--partners {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .page-inner .entity-ref-detail--article {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 640px) {
    .page-inner .entity-ref-grid--news,
    .page-inner .entity-ref-grid--players,
    .page-inner .entity-ref-grid--partners {
        grid-template-columns: minmax(0, 1fr);
    }

    .page-inner .entity-ref-grid--news .entity-ref-card--featured {
        grid-column: span 1;
    }
}

/* DESIGN HELL PHASE 6 — Standings knockout search map polish */

.page-inner .entity-ref-back-link--light {
    color: rgba(255, 255, 255, 0.88);
}

.page-inner .entity-ref-grid--standings {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.page-inner .standings-ref-group {
    min-width: 0;
}

.page-inner .standings-ref-table__scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    max-width: 100%;
}

.page-inner .standings-ref-table table {
    width: 100%;
    min-width: 640px;
    border-collapse: collapse;
}

.page-inner .standings-ref-table th,
.page-inner .standings-ref-table td {
    padding: 0.55rem 0.65rem;
    text-align: center;
    white-space: nowrap;
}

.page-inner .standings-ref-table th:nth-child(2),
.page-inner .standings-ref-table td:nth-child(2) {
    text-align: left;
    white-space: normal;
}

.page-inner .standings-ref-table th {
    font-size: 0.72rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #64748b;
    background: #f8fafc;
    border-bottom: 1px solid rgba(15, 23, 42, 0.08);
}

.page-inner .standings-ref-table td {
    border-bottom: 1px solid rgba(15, 23, 42, 0.06);
    color: #334155;
    font-size: 0.88rem;
}

.page-inner .standings-ref-table__row--qualified td {
    background: rgba(212, 168, 67, 0.08);
}

.page-inner .standings-ref-table__row--qualified .standings-table__points strong {
    color: var(--m2030-red, #b10f2e);
}

.page-inner .entity-ref-detail--standings {
    grid-template-columns: minmax(0, 1.4fr) minmax(260px, 0.8fr);
}

.page-inner .entity-ref-detail-grid--compact {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.page-inner .entity-ref-related-item--text {
    grid-template-columns: minmax(0, 1fr);
}

.page-inner .entity-ref-page--knockout .knockout-page-section {
    width: min(calc(100% - clamp(1rem, 3vw, 2rem)), 1360px);
    margin: 0 auto;
    padding: 0 0 clamp(1.25rem, 3vw, 2rem);
}

.page-inner .knockout-ref-bracket {
    border-radius: 1rem;
    background: #fff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    padding: clamp(0.85rem, 2vw, 1.25rem);
}

.page-inner .knockout-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    max-width: 100%;
}

.page-inner .search-ref-shell {
    width: min(calc(100% - clamp(1rem, 3vw, 2rem)), 1120px);
    margin: 0 auto;
    padding-bottom: clamp(1.25rem, 3vw, 2rem);
}

.page-inner .search-ref-form__inner {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 0.75rem;
    align-items: end;
}

.page-inner .search-ref-form .search-form__field label {
    display: block;
    margin-bottom: 0.35rem;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #64748b;
}

.page-inner .search-ref-form input[type="search"] {
    width: 100%;
    min-height: 2.75rem;
    padding: 0.65rem 0.85rem;
    border-radius: 0.75rem;
    border: 1px solid rgba(15, 23, 42, 0.12);
    font-size: 1rem;
}

.page-inner .search-ref-form input[type="search"]:focus {
    outline: 2px solid rgba(177, 15, 46, 0.25);
    border-color: rgba(177, 15, 46, 0.35);
}

.page-inner .search-ref-summary {
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem;
    margin: 0.85rem 0;
}

.page-inner .search-ref-results {
    display: grid;
    gap: 0.85rem;
}

.page-inner .search-ref-results__list {
    display: grid;
    gap: 0.65rem;
}

.page-inner .entity-ref-card--search-result,
.page-inner .entity-ref-card--search-scope {
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
}

.page-inner .entity-ref-card--search-result h3 {
    margin: 0.35rem 0 0;
    font-size: 1rem;
}

.page-inner .entity-ref-grid--search-scope {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.page-inner .map-ref-shell {
    width: min(calc(100% - clamp(1rem, 3vw, 2rem)), 1360px);
    margin: 0 auto;
    padding-bottom: clamp(1.25rem, 3vw, 2rem);
}

.page-inner .map-ref-container {
    border-radius: 1rem;
    background: #fff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    padding: clamp(0.85rem, 2vw, 1.15rem);
}

.page-inner .map-canvas.map-canvas--interactive {
    min-height: clamp(280px, 42vw, 460px);
    max-height: 520px;
    border-radius: 0.85rem;
    overflow: hidden;
}

.page-inner .map-ref-panel .panel,
.page-inner .map-ref-panel .map-section-panel,
.page-inner .map-ref-panel .map-detail-card {
    border-radius: 0.95rem;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
}

@media (max-width: 1080px) {
    .page-inner .entity-ref-grid--standings,
    .page-inner .entity-ref-grid--search-scope {
        grid-template-columns: minmax(0, 1fr);
    }

    .page-inner .entity-ref-detail--standings {
        grid-template-columns: minmax(0, 1fr);
    }

    .page-inner .map-layout.map-ref-shell {
        grid-template-columns: minmax(0, 1fr);
    }
}

@media (max-width: 640px) {
    .page-inner .search-ref-form__inner {
        grid-template-columns: minmax(0, 1fr);
    }

    .page-inner .search-ref-form .entity-ref-action {
        width: 100%;
    }

    .page-inner .map-canvas.map-canvas--interactive {
        min-height: 240px;
        max-height: 320px;
    }

    .page-inner .standings-ref-table table {
        min-width: 580px;
    }
}

/* PARALLEL TASK — FIFA Global Partners logos */

.fifa-partners-section {
    width: min(calc(100% - clamp(1rem, 3vw, 2rem)), 1320px);
    margin: clamp(1.1rem, 2.4vw, 2rem) auto;
    padding: clamp(1rem, 2vw, 1.45rem);
    border: 1px solid rgba(177, 15, 46, 0.12);
    border-radius: 18px;
    background:
        linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(255, 251, 244, 0.98)),
        radial-gradient(circle at 100% 0%, rgba(201, 164, 76, 0.12), transparent 32%);
    box-shadow: 0 16px 38px rgba(15, 23, 42, 0.08);
    overflow: hidden;
}

.fifa-partners-section--page {
    width: 100%;
    margin-block: 0 clamp(1rem, 2vw, 1.4rem);
}

.fifa-partners-section__head {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: clamp(0.9rem, 1.6vw, 1.15rem);
}

.fifa-partners-section__head h2 {
    margin: 0.18rem 0 0;
    color: #141c27;
    font-family: "Georgia", "Times New Roman", serif;
    font-size: clamp(1.35rem, 2vw, 1.95rem);
    line-height: 1.1;
}

.fifa-partners-section__head p {
    max-width: 24rem;
    margin: 0;
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.5;
    text-align: right;
}

.fifa-partners-section__eyebrow {
    color: var(--p3-red, #b10f2e);
    font-size: 0.72rem;
    font-weight: 900;
    letter-spacing: 0.16em;
    text-transform: uppercase;
}

.fifa-partners-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: clamp(0.7rem, 1.4vw, 1rem);
    max-width: 100%;
}

.fifa-partners-grid--page {
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
}

.fifa-partner-card {
    min-width: 0;
    min-height: 164px;
    display: grid;
    grid-template-rows: minmax(76px, auto) auto;
    align-content: center;
    gap: 0.55rem;
    padding: clamp(0.85rem, 1.6vw, 1.05rem);
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-top: 3px solid rgba(201, 164, 76, 0.78);
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.07);
}

.fifa-partner-card--page {
    min-height: 204px;
    grid-template-rows: minmax(92px, auto) auto auto;
}

.fifa-partner-logo {
    min-width: 0;
    min-height: 78px;
    display: grid;
    place-items: center;
    padding: 0.45rem;
}

.fifa-partner-card--page .fifa-partner-logo {
    min-height: 94px;
}

.fifa-partner-logo img {
    display: block;
    width: 100%;
    height: 76px;
    max-width: 138px;
    object-fit: contain;
}

.fifa-partner-card--page .fifa-partner-logo img {
    height: 88px;
    max-width: 160px;
}

.fifa-partner-text-logo {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 3.75rem;
    padding: 0.6rem 0.75rem;
    border: 1px solid rgba(177, 15, 46, 0.16);
    border-radius: 14px;
    background:
        linear-gradient(135deg, rgba(177, 15, 46, 0.08), rgba(201, 164, 76, 0.12)),
        #fffdf8;
    color: #5c0818;
    font-size: 0.9rem;
    font-weight: 900;
    line-height: 1.25;
    text-align: center;
    overflow-wrap: anywhere;
}

.fifa-partner-name {
    display: block;
    color: #141c27;
    font-size: 0.92rem;
    line-height: 1.25;
    text-align: center;
}

.fifa-partner-label {
    display: inline-flex;
    justify-self: center;
    align-items: center;
    min-height: 1.65rem;
    padding: 0.34rem 0.72rem;
    border-radius: 999px;
    background: rgba(177, 15, 46, 0.08);
    color: var(--p3-red, #b10f2e);
    font-size: 0.72rem;
    font-weight: 900;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    white-space: nowrap;
}

@media (max-width: 700px) {
    .fifa-partners-section__head {
        align-items: start;
        flex-direction: column;
    }

    .fifa-partners-section__head p {
        max-width: none;
        text-align: left;
    }

    .fifa-partners-grid,
    .fifa-partners-grid--page {
        grid-template-columns: repeat(auto-fit, minmax(132px, 1fr));
    }

    .fifa-partner-card,
    .fifa-partner-card--page {
        min-height: 158px;
        grid-template-rows: minmax(72px, auto) auto auto;
    }

    .fifa-partner-logo,
    .fifa-partner-card--page .fifa-partner-logo {
        min-height: 74px;
    }

    .fifa-partner-logo img,
    .fifa-partner-card--page .fifa-partner-logo img {
        height: 68px;
        max-width: 122px;
    }
}

/* DESIGN HELL PHASE 7 — Match centre fixtures results polish */

.page-inner .match-ref-page {
    margin: 0 calc(-1 * clamp(0.75rem, 2vw, 1.25rem));
    min-width: 0;
    overflow-x: clip;
    background: #f7f9fc;
}

.page-inner .match-ref-hero {
    margin-bottom: 0;
}

.page-inner .match-ref-section {
    width: min(calc(100% - clamp(1rem, 3vw, 2rem)), 1120px);
    margin: 0 auto;
    padding: clamp(0.85rem, 2vw, 1.35rem) 0 clamp(1.35rem, 3vw, 2rem);
    min-width: 0;
}

.page-inner .match-ref-filter {
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem;
    margin-bottom: 1rem;
}

.page-inner .match-ref-grid {
    display: grid;
    gap: clamp(0.85rem, 2vw, 1.15rem);
    min-width: 0;
}

.page-inner .match-ref-day {
    min-width: 0;
    padding: clamp(0.85rem, 1.8vw, 1.15rem);
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
}

.page-inner .match-ref-day__title {
    margin: 0 0 0.75rem;
    padding-bottom: 0.55rem;
    border-bottom: 1px solid rgba(177, 15, 46, 0.1);
    color: #0f172a;
    font-size: clamp(0.95rem, 1.8vw, 1.08rem);
    font-weight: 900;
    letter-spacing: 0.02em;
}

.page-inner .match-feed {
    display: grid;
    gap: 0.72rem;
    min-width: 0;
}

.page-inner .match-ref-card.match-card {
    min-width: 0;
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-left: 3px solid rgba(201, 164, 76, 0.72);
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
    overflow: hidden;
}

.page-inner .match-ref-card.match-card:hover {
    border-color: rgba(177, 15, 46, 0.18);
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
}

.page-inner .match-ref-card--results.match-card {
    border-left-color: rgba(177, 15, 46, 0.72);
}

.page-inner .match-ref-card .match-card__head,
.page-inner .match-ref-card .match-card__meta {
    padding-inline: clamp(0.75rem, 1.6vw, 0.95rem);
}

.page-inner .match-ref-card .match-card__teams {
    padding: 0.55rem clamp(0.75rem, 1.6vw, 0.95rem);
    gap: 0.55rem;
}

.page-inner .match-ref-scoreboard.match-card__teams {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
    align-items: center;
    gap: 0.55rem clamp(0.45rem, 1.2vw, 0.75rem);
}

.page-inner .match-ref-team.match-card__team {
    display: grid;
    gap: 0.2rem;
    min-width: 0;
    text-align: center;
}

.page-inner .match-ref-team.match-card__team strong {
    overflow-wrap: anywhere;
    font-size: clamp(0.88rem, 1.6vw, 1rem);
    line-height: 1.15;
}

.page-inner .match-ref-team--winner strong {
    color: var(--m2030-red, #b10f2e);
}

.page-inner .match-ref-team--draw strong {
    color: #475569;
}

.page-inner .match-ref-flag {
    display: grid;
    place-items: center;
    width: clamp(1.85rem, 4vw, 2.35rem);
    height: clamp(1.85rem, 4vw, 2.35rem);
    margin: 0 auto 0.15rem;
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 999px;
    background: #fff;
    overflow: hidden;
}

.page-inner .match-ref-flag img,
.page-inner .match-ref-flag .local-entity-media__img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.page-inner .match-ref-score.score-box {
    min-width: clamp(4.2rem, 10vw, 5.5rem);
    padding: 0.45rem 0.65rem;
    border-radius: 12px;
    border: 1px solid rgba(177, 15, 46, 0.14);
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 248, 235, 0.92));
    color: #0f172a;
    font-size: clamp(1rem, 2.2vw, 1.25rem);
    font-weight: 900;
    line-height: 1.1;
    text-align: center;
}

.page-inner .match-ref-card--results .match-ref-score.score-box {
    border-color: rgba(177, 15, 46, 0.24);
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 236, 236, 0.55));
    color: var(--m2030-red, #b10f2e);
    font-size: clamp(1.08rem, 2.4vw, 1.35rem);
}

.page-inner .match-ref-score .score-box__sub {
    margin-top: 0.2rem;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #64748b;
}

.page-inner .match-ref-meta.match-card__head,
.page-inner .match-ref-meta.match-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem 0.65rem;
    align-items: center;
    min-width: 0;
}

.page-inner .match-ref-status.status-pill,
.page-inner .match-ref-status.match-status-pill {
    flex-shrink: 0;
    font-size: 0.68rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.page-inner .match-ref-card .match-card__date {
    margin-inline-start: auto;
    color: #64748b;
    font-size: 0.78rem;
    font-weight: 700;
    white-space: nowrap;
}

.page-inner .match-ref-card .match-card__cta {
    margin-inline-start: auto;
    white-space: nowrap;
}

.page-inner .match-ref-empty.entity-ref-empty {
    padding: clamp(1.25rem, 3vw, 2rem);
    border: 1px dashed rgba(177, 15, 46, 0.18);
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.82);
}

.page-inner .match-ref-page--detail {
    padding-bottom: clamp(1rem, 2.5vw, 1.75rem);
}

.page-inner .match-ref-detail-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 0.55rem 1rem;
    justify-content: space-between;
    width: min(calc(100% - clamp(1rem, 3vw, 2rem)), 1120px);
    margin: 0 auto;
    padding: clamp(0.75rem, 1.8vw, 1rem) 0 0.35rem;
}

.page-inner .match-ref-scoreboard--detail.match-centre-hero {
    width: min(calc(100% - clamp(1rem, 3vw, 2rem)), 1120px);
    margin: 0 auto;
}

.page-inner .match-ref-flag--hero.match-scoreboard__crest {
    display: grid;
    place-items: center;
    padding: 0.2rem;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.12);
}

.page-inner .match-ref-flag--hero img,
.page-inner .match-ref-flag--hero .local-entity-media__img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.page-inner .match-ref-scoreboard--detail .match-ref-filter {
    margin-top: 0.85rem;
    margin-bottom: 0;
}

.page-inner .match-ref-detail.entity-ref-detail {
    width: min(calc(100% - clamp(1rem, 3vw, 2rem)), 1120px);
    margin: 0 auto;
    padding: clamp(0.75rem, 1.8vw, 1.1rem) 0 0;
}

.page-inner .match-ref-facts.entity-ref-detail-grid {
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 11rem), 1fr));
}

.page-inner .match-ref-page--detail .match-ref-section.match-centre-grid {
    width: min(calc(100% - clamp(1rem, 3vw, 2rem)), 1120px);
    margin: 0 auto;
    padding-top: clamp(0.65rem, 1.5vw, 0.95rem);
}

@media (max-width: 768px) {
    .page-inner .match-ref-scoreboard.match-card__teams {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .page-inner .match-ref-card .match-card__head {
        flex-direction: column;
        align-items: flex-start;
    }

    .page-inner .match-ref-card .match-card__date {
        margin-inline-start: 0;
    }

    .page-inner .match-ref-card .match-card__meta {
        flex-direction: column;
        align-items: stretch;
    }

    .page-inner .match-ref-card .match-card__cta {
        margin-inline-start: 0;
        text-align: center;
    }

    .page-inner .match-ref-detail-nav {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 640px) {
    .page-inner .match-ref-day {
        padding: 0.75rem;
        border-radius: 14px;
    }

    .page-inner .match-ref-card.match-card {
        border-radius: 14px;
    }
}

/* DESIGN HELL PHASE 7.1 — Global flags and partners final fix */

.page-inner .flag-ref-wrap {
    display: inline-grid;
    place-items: center;
    flex-shrink: 0;
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 10px;
    background: #fff;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(15, 23, 42, 0.05);
}

.page-inner .flag-ref-wrap--small {
    width: 1.65rem;
    height: 1.22rem;
    border-radius: 6px;
}

.page-inner .flag-ref-wrap--medium {
    width: 2.35rem;
    height: 1.72rem;
    border-radius: 8px;
}

.page-inner .flag-ref-wrap--large {
    width: clamp(3.25rem, 7vw, 4.75rem);
    height: clamp(2.35rem, 5vw, 3.45rem);
    border-radius: 12px;
}

.page-inner .flag-ref,
.page-inner .flag-ref-wrap img,
.page-inner .team-ref-flag img,
.page-inner .match-ref-flag img,
.page-inner .standings-ref-flag img,
.page-inner .knockout-ref-flag img,
.page-inner .player-ref-team-flag img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.page-inner .team-ref-flag.entity-ref-card__media--flag,
.page-inner .team-ref-flag.entity-ref-hero__media--flag {
    display: grid;
    place-items: center;
    padding: 0.45rem;
    background: #fff;
}

.page-inner .standings-ref-team {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    min-width: 0;
    max-width: 100%;
}

.page-inner .standings-ref-team a {
    overflow-wrap: anywhere;
}

.page-inner .standings-ref-flag {
    flex-shrink: 0;
}

.page-inner .knockout-team-row {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    align-items: center;
    gap: 0.42rem;
}

.page-inner .knockout-ref-flag {
    flex-shrink: 0;
}

.page-inner .entity-ref-stat__team {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    flex-wrap: wrap;
}

.page-inner .entity-ref-card__location {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    flex-wrap: wrap;
}

.page-inner .search-ref-result__media {
    flex-shrink: 0;
    margin-bottom: 0.35rem;
}

.page-inner .entity-ref-card--search-result {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    gap: 0.75rem;
    align-items: start;
}

.page-inner .search-ref-match-flags {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}

.page-home .home-final-next-match__teams {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 0.55rem 0.75rem;
}

.page-home .home-final-next-match__team {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    gap: 0.35rem;
    min-width: 0;
}

.page-home .home-final-next-match__team strong {
    text-align: center;
    overflow-wrap: anywhere;
}

.page-home .fifa-partners-section--home {
    margin-top: clamp(0.85rem, 1.8vw, 1.25rem);
    padding: clamp(0.85rem, 1.6vw, 1.15rem);
}

.page-home .fifa-partners-section--home .fifa-partners-section__head {
    margin-bottom: 0.75rem;
}

.page-home .fifa-partners-section--home .fifa-partners-section__head p {
    display: none;
}

.page-home .fifa-partners-grid {
    grid-template-columns: repeat(auto-fit, minmax(108px, 1fr));
    gap: 0.55rem;
}

.page-home .fifa-partner-card--home {
    min-height: 118px;
    grid-template-rows: auto minmax(56px, auto) auto;
    gap: 0.35rem;
    padding: 0.55rem 0.45rem 0.65rem;
}

.page-home .fifa-partner-card--home .fifa-partner-label {
    font-size: 0.58rem;
    letter-spacing: 0.06em;
    padding: 0.22rem 0.45rem;
}

.page-home .fifa-partner-card--home .fifa-partner-logo {
    min-height: 52px;
}

.page-home .fifa-partner-card--home .fifa-partner-logo img {
    height: 46px;
    max-width: 92px;
}

.page-home .fifa-partner-card--home .fifa-partner-name {
    font-size: 0.72rem;
    line-height: 1.15;
}

.page-home .fifa-partner-card--home .fifa-partner-text-logo {
    min-height: 2.75rem;
    font-size: 0.72rem;
    padding: 0.35rem 0.45rem;
}

.page-inner .fifa-partners-section--page .fifa-partner-card--page {
    min-height: 188px;
}

.page-inner .fifa-partner-text-logo {
    overflow-wrap: anywhere;
}

@media (max-width: 768px) {
    .page-inner .entity-ref-card--search-result {
        grid-template-columns: 1fr;
    }

    .page-home .fifa-partners-grid {
        grid-template-columns: repeat(auto-fit, minmax(96px, 1fr));
    }

    .page-inner .knockout-team-row {
        grid-template-columns: auto minmax(0, 1fr) auto;
    }
}

@media (max-width: 640px) {
    .page-home .fifa-partner-card--home {
        min-height: 108px;
    }

    .page-home .fifa-partner-card--home .fifa-partner-logo img {
        height: 40px;
        max-width: 82px;
    }
}

/* DESIGN HELL PHASE 7.2 — Flags sizing and partners grid microfix */

.page-inner .flag-ref-wrap,
.page-home .flag-ref-wrap {
    overflow: visible;
    border-radius: 6px;
    aspect-ratio: auto;
}

.page-inner .flag-ref-wrap--small,
.page-home .flag-ref-wrap--small {
    width: 34px;
    height: 24px;
    min-width: 34px;
    min-height: 24px;
    max-width: 34px;
    max-height: 24px;
    border-radius: 6px;
}

.page-inner .flag-ref-wrap--card,
.page-home .flag-ref-wrap--card {
    width: 48px;
    height: 34px;
    min-width: 48px;
    min-height: 34px;
    max-width: 48px;
    max-height: 34px;
    border-radius: 6px;
}

.page-inner .flag-ref-wrap--medium,
.page-home .flag-ref-wrap--medium {
    width: 44px;
    height: 32px;
    min-width: 44px;
    min-height: 32px;
    max-width: 44px;
    max-height: 32px;
    border-radius: 6px;
}

.page-inner .flag-ref-wrap img,
.page-home .flag-ref-wrap img,
.page-inner .flag-ref,
.page-home .flag-ref {
    display: block;
    width: 100%;
    height: 100%;
    max-width: 100%;
    object-fit: contain !important;
    border-radius: 0;
}

.page-inner .standings-ref-flag,
.page-inner .standings-ref-flag .flag-ref-wrap {
    width: 34px;
    height: 24px;
    min-width: 34px;
    min-height: 24px;
    max-width: 34px;
    max-height: 24px;
    aspect-ratio: auto;
    overflow: visible;
    border-radius: 6px;
}

.page-inner .standings-ref-flag .flag-ref-wrap--small {
    width: 34px;
    height: 24px;
}

.page-inner .standings-ref-team {
    align-items: center;
    gap: 0.4rem;
}

.page-inner .standings-ref-table td.standings-table__team {
    padding-top: 0.42rem;
    padding-bottom: 0.42rem;
    vertical-align: middle;
}

.page-inner .standings-ref-table tbody tr {
    height: auto;
}

.page-inner .match-ref-flag {
    display: inline-grid;
    place-items: center;
    width: auto;
    height: auto;
    margin: 0 auto 0.15rem;
    border: 0;
    border-radius: 0;
    background: transparent;
    overflow: visible;
}

.page-inner .match-ref-flag .flag-ref-wrap--card,
.page-home .match-ref-flag .flag-ref-wrap--card {
    width: 48px;
    height: 34px;
}

.page-inner .match-ref-flag--hero.match-scoreboard__crest {
    width: clamp(3.25rem, 7vw, 4.5rem);
    height: clamp(2.35rem, 5vw, 3.2rem);
    border-radius: 8px;
    overflow: visible;
    padding: 0.25rem;
}

.page-inner .match-ref-flag--hero .flag-ref-wrap,
.page-inner .match-ref-flag--hero .flag-ref-wrap--large {
    width: 100%;
    height: 100%;
    max-width: 100%;
    max-height: 100%;
    border-radius: 6px;
}

.page-inner .knockout-ref-flag,
.page-inner .knockout-ref-flag .flag-ref-wrap,
.page-inner .knockout-ref-flag .flag-ref-wrap--small {
    width: 30px;
    height: 21px;
    min-width: 30px;
    min-height: 21px;
    max-width: 30px;
    max-height: 21px;
    aspect-ratio: auto;
    overflow: visible;
    border-radius: 5px;
}

.page-inner .player-ref-team-flag,
.page-inner .player-ref-team-flag .flag-ref-wrap,
.page-home .player-ref-team-flag,
.page-home .player-ref-team-flag .flag-ref-wrap {
    width: 28px;
    height: 20px;
    min-width: 28px;
    min-height: 20px;
    max-width: 28px;
    max-height: 20px;
    aspect-ratio: auto;
    overflow: visible;
    border-radius: 5px;
}

.page-inner .player-ref-team-flag .player-avatar--flag,
.page-inner .entity-ref-card__location .player-avatar--flag {
    border-radius: 0 !important;
}

.page-inner .entity-ref-card__media--flag.team-ref-flag,
.page-inner a.entity-ref-card__media--flag.team-ref-flag {
    aspect-ratio: 4 / 3;
    max-height: 4.5rem;
    padding: 0.35rem;
}

.page-inner .entity-ref-hero__media--flag.team-ref-flag--hero,
.page-inner .team-ref-flag--hero {
    aspect-ratio: 4 / 3;
    max-width: clamp(4.5rem, 12vw, 6.5rem);
    max-height: clamp(3.25rem, 8vw, 4.75rem);
    padding: 0.35rem;
    border-radius: 10px;
    overflow: visible;
}

.page-inner .entity-ref-card__media--flag .media-bound-image,
.page-inner .team-ref-flag .media-bound-image,
.page-inner .entity-ref-hero__media--flag .media-bound-image {
    width: 100%;
    height: 100%;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain !important;
}

.page-home .home-final-next-match__team .flag-ref-wrap--medium {
    width: 44px;
    height: 32px;
}

.page-home .match-card .match-ref-flag .flag-ref-wrap--card {
    width: 42px;
    height: 30px;
    min-width: 42px;
    min-height: 30px;
    max-width: 42px;
    max-height: 30px;
}

.page-home .fifa-partners-section--home {
    margin-top: 0.65rem;
    margin-bottom: 0.35rem;
    padding: 0.65rem 0.75rem 0.75rem;
}

.page-home .fifa-partners-section--home .fifa-partners-section__head {
    margin-bottom: 0.55rem;
}

.page-home .fifa-partners-section--home .fifa-partners-section__head h2 {
    font-size: clamp(1.05rem, 1.8vw, 1.35rem);
}

.page-home .fifa-partners-grid--home {
    display: grid;
    grid-template-columns: repeat(9, minmax(0, 1fr));
    gap: 0.45rem;
}

.page-home .fifa-partner-card--home {
    min-height: 0;
    height: auto;
    max-height: 128px;
    grid-template-rows: auto minmax(44px, auto);
    gap: 0.25rem;
    padding: 0.4rem 0.35rem 0.45rem;
}

.page-home .fifa-partner-card--home .fifa-partner-label {
    font-size: 0.52rem;
    letter-spacing: 0.05em;
    padding: 0.16rem 0.35rem;
    min-height: 0;
}

.page-home .fifa-partner-card--home .fifa-partner-logo {
    min-height: 44px;
    max-height: 52px;
}

.page-home .fifa-partner-card--home .fifa-partner-logo img {
    height: 42px;
    max-width: 88px;
    width: auto;
    object-fit: contain;
}

.page-home .fifa-partner-card--home .fifa-partner-name {
    display: none;
}

.page-home .fifa-partner-card--home .fifa-partner-text-logo {
    min-height: 2.35rem;
    font-size: 0.62rem;
    padding: 0.25rem 0.35rem;
}

.page-inner .fifa-partners-section--page {
    padding-top: 0.5rem;
    padding-bottom: 0.75rem;
}

.page-inner .fifa-partners-grid--page {
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 0.65rem;
}

.page-inner .fifa-partner-card--page {
    min-height: 0;
    max-height: 176px;
    grid-template-rows: auto minmax(56px, auto) auto auto;
    gap: 0.35rem;
    padding: 0.65rem 0.55rem 0.7rem;
}

.page-inner .fifa-partner-card--page .fifa-partner-logo {
    min-height: 56px;
    max-height: 64px;
}

.page-inner .fifa-partner-card--page .fifa-partner-logo img {
    height: 52px;
    max-width: 120px;
}

@media (max-width: 1280px) {
    .page-home .fifa-partners-grid--home {
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }
}

@media (max-width: 900px) {
    .page-home .fifa-partners-grid--home {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .page-inner .flag-ref-wrap--card,
    .page-home .flag-ref-wrap--card,
    .page-inner .match-ref-flag .flag-ref-wrap--card,
    .page-home .match-card .match-ref-flag .flag-ref-wrap--card {
        width: 42px;
        height: 30px;
        min-width: 42px;
        min-height: 30px;
        max-width: 42px;
        max-height: 30px;
    }

    .page-inner .fifa-partners-grid--page {
        grid-template-columns: repeat(auto-fill, minmax(118px, 1fr));
    }
}

@media (max-width: 640px) {
    .page-home .fifa-partners-grid--home {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.35rem;
    }

    .page-home .fifa-partner-card--home {
        max-height: 112px;
        padding: 0.35rem 0.3rem 0.4rem;
    }

    .page-home .fifa-partner-card--home .fifa-partner-logo img {
        height: 36px;
        max-width: 76px;
    }

    .page-inner .standings-ref-flag,
    .page-inner .standings-ref-flag .flag-ref-wrap {
        width: 32px;
        height: 22px;
        min-width: 32px;
        min-height: 22px;
        max-width: 32px;
        max-height: 22px;
    }
}

/* DESIGN HELL PHASE 7.3 — Homepage standings flag hotfix */

.page-home .home-final-standings .standings-ref-table--compact-flags .standings-ref-team {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 0;
    max-width: 100%;
}

.page-home .home-final-standings .standings-ref-table--compact-flags td.standings-table__team {
    white-space: normal;
    vertical-align: middle;
    padding-top: 0.32rem;
    padding-bottom: 0.32rem;
    line-height: 1.25;
}

.page-home .home-final-standings .standings-ref-table--compact-flags tbody tr {
    height: auto;
}

.page-home .home-final-standings .standings-ref-table--compact-flags .standings-ref-team a {
    overflow-wrap: anywhere;
    min-width: 0;
}

.page-home .home-final-standings .standings-ref-flag,
.page-home .home-final-standings .standings-ref-flag .flag-ref-wrap,
.page-home .home-final-standings .standings-ref-flag .flag-ref-wrap--small,
.page-home .home-final-standings .standings-ref-table--compact-flags .team-ref-flag,
.page-home .home-final-standings .standings-ref-table--compact-flags img.team-flag,
.page-home .home-final-standings .standings-ref-table--compact-flags img.flag-ref,
.page-home .home-final-standings .standings-ref-table--compact-flags img.media-bound-image {
    display: block;
    flex: 0 0 auto;
    width: 34px !important;
    height: 24px !important;
    min-width: 34px !important;
    min-height: 24px !important;
    max-width: 34px !important;
    max-height: 24px !important;
    margin: 0;
    padding: 2px;
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 6px !important;
    background: #fff;
    overflow: visible;
    aspect-ratio: auto !important;
    object-fit: contain !important;
    object-position: center;
    box-sizing: border-box;
}

.page-home .home-final-standings .standings-ref-table--compact-flags img.team-flag--icon {
    padding: 3px;
    object-fit: contain !important;
}

.page-home .home-final-standings .standings-ref-table__scroll {
    overflow-x: auto;
    max-width: 100%;
}

.page-home .home-final-standings .standings-ref-table--compact-flags table {
    width: 100%;
    min-width: 0;
}

@media (max-width: 640px) {
    .page-home .home-final-standings .standings-ref-flag,
    .page-home .home-final-standings .standings-ref-flag .flag-ref-wrap,
    .page-home .home-final-standings .standings-ref-table--compact-flags img.team-flag,
    .page-home .home-final-standings .standings-ref-table--compact-flags img.media-bound-image {
        width: 32px !important;
        height: 22px !important;
        min-width: 32px !important;
        min-height: 22px !important;
        max-width: 32px !important;
        max-height: 22px !important;
    }
}

/* DESIGN HELL PHASE 7.4 — Homepage quick actions partners footer final polish */

.page-home .home-final-quick-track {
    overflow-x: auto;
    overflow-y: hidden;
    max-width: 100%;
    padding-bottom: 0.2rem;
    scroll-snap-type: x proximity;
    scroll-padding-inline: 0.35rem;
    scrollbar-width: thin;
    -webkit-overflow-scrolling: touch;
}

.page-home .home-final-quick-track .home-final-quick__row {
    display: flex;
    flex-wrap: nowrap;
    gap: 0.5rem;
    min-width: min(100%, max-content);
    width: max-content;
    padding-inline: 0.05rem;
}

.page-home .home-final-quick-card {
    flex: 0 0 auto;
    min-width: 7.35rem;
    scroll-snap-align: start;
}

.page-home .home-final-quick-icon {
    display: grid;
    place-items: center;
    width: 1.65rem;
    height: 1.65rem;
    border-radius: 999px;
    font-size: 0.72rem;
    line-height: 1;
    letter-spacing: -0.04em;
    color: #fff;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.18);
}

.page-home .home-final-quick-icon--red {
    background: linear-gradient(145deg, #c41a34, #8e1028);
}

.page-home .home-final-quick-icon--green {
    background: linear-gradient(145deg, #128052, #0a5a38);
}

.page-home .home-final-quick-icon--gold {
    background: linear-gradient(145deg, #c9a44c, #9a7424);
}

.page-home .home-final-quick-icon--map {
    background: linear-gradient(145deg, #334155, #1e293b);
}

.page-home .home-final-cities-scroll {
    display: flex;
    flex-wrap: nowrap;
    gap: 0.45rem;
    overflow-x: auto;
    overflow-y: hidden;
    max-width: 100%;
    padding-bottom: 0.15rem;
    scroll-snap-type: x proximity;
    scroll-padding-inline: 0.25rem;
    scrollbar-width: thin;
    -webkit-overflow-scrolling: touch;
}

.page-home .home-final-city-chip.home-final-cities__item {
    flex: 0 0 auto;
    min-width: 7.5rem;
    max-width: 9.5rem;
    scroll-snap-align: start;
    padding: 0.45rem 0.55rem;
    border: 1px solid rgba(20, 28, 39, 0.07);
    border-radius: 10px;
    background: #fafbfd;
    text-decoration: none;
    color: inherit;
}

.page-home .home-final-city-chip span {
    display: block;
    color: #6b7788;
    font-size: 0.58rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.page-home .home-final-city-chip strong {
    display: block;
    margin-top: 0.12rem;
    font-size: 0.78rem;
    line-height: 1.2;
    overflow-wrap: anywhere;
}

.page-home .fifa-partners-section--home {
    margin-top: 0.55rem;
    margin-bottom: 0.25rem;
    padding: 0.6rem 0.65rem 0.7rem;
}

.page-home .fifa-partners-section--home .fifa-partners-section__head {
    margin-bottom: 0.5rem;
}

.page-home .fifa-partners-section--home .fifa-partners-section__head p {
    display: none;
}

.page-home .fifa-partners-track {
    overflow-x: auto;
    overflow-y: hidden;
    max-width: 100%;
    padding-bottom: 0.15rem;
    scroll-snap-type: x proximity;
    scroll-padding-inline: 0.35rem;
    scrollbar-width: thin;
    -webkit-overflow-scrolling: touch;
}

.page-home .fifa-partners-grid--home {
    display: flex;
    flex-wrap: nowrap;
    gap: 0.55rem;
    width: max-content;
    min-width: 100%;
    padding-inline: 0.05rem;
}

.page-home .fifa-partner-card--home {
    flex: 0 0 auto;
    width: clamp(168px, 16vw, 196px);
    min-height: 0;
    max-height: 132px;
    scroll-snap-align: start;
    border-top: 3px solid rgba(201, 164, 76, 0.78);
    grid-template-rows: auto minmax(46px, auto) auto;
    gap: 0.28rem;
    padding: 0.45rem 0.4rem 0.5rem;
}

.page-home .fifa-partner-card--home .fifa-partner-label {
    font-size: 0.54rem;
    letter-spacing: 0.06em;
    padding: 0.14rem 0.38rem;
}

.page-home .fifa-partner-card--home .fifa-partner-logo {
    min-height: 46px;
    max-height: 54px;
}

.page-home .fifa-partner-card--home .fifa-partner-logo img {
    height: 44px;
    max-width: 92px;
}

.page-home .fifa-partner-card--home .fifa-partner-name {
    display: block;
    font-size: 0.68rem;
    line-height: 1.15;
    text-align: center;
    overflow-wrap: anywhere;
}

.page-home .fifa-partner-card--home .fifa-partner-text-logo {
    min-height: 2.25rem;
    font-size: 0.62rem;
    padding: 0.22rem 0.35rem;
}

.page-inner .fifa-partners-track--page {
    overflow-x: auto;
    max-width: 100%;
    scrollbar-width: thin;
}

.page-inner .fifa-partners-grid--page {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.65rem;
}

.page-inner .fifa-partner-card--page {
    border-top: 3px solid rgba(201, 164, 76, 0.72);
    min-height: 0;
    max-height: 168px;
    gap: 0.32rem;
    padding: 0.6rem 0.5rem 0.65rem;
}

.page-inner .fifa-partner-card--page .fifa-partner-logo {
    min-height: 54px;
    max-height: 60px;
}

.page-inner .fifa-partner-card--page .fifa-partner-logo img {
    height: 50px;
    max-width: 118px;
}

.site-footer.site-footer--zellige {
    position: relative;
    overflow: hidden;
    background:
        radial-gradient(circle at 12% 0%, rgba(201, 164, 76, 0.12), transparent 34%),
        linear-gradient(180deg, #4a0714 0%, #2b040c 48%, #1a0207 100%);
    border-top: 3px solid rgba(201, 164, 76, 0.55);
}

.site-footer.site-footer--zellige::before {
    content: "";
    position: absolute;
    inset: 0;
    opacity: 0.1;
    pointer-events: none;
    background-image: none;
    background-size: 240px auto;
    background-repeat: repeat;
}

.site-footer.site-footer--zellige .site-footer__inner,
.site-footer.site-footer--zellige .site-footer__bottom {
    position: relative;
    z-index: 1;
}

.site-footer.site-footer--zellige .site-footer__label {
    color: rgba(255, 236, 196, 0.92);
    letter-spacing: 0.14em;
}

.site-footer.site-footer--zellige .site-footer__links a,
.site-footer.site-footer--zellige .site-footer__newsletter-note {
    color: rgba(255, 255, 255, 0.86);
}

.site-footer.site-footer--zellige .site-footer__links a:hover,
.site-footer.site-footer--zellige .site-footer__links a:focus-visible {
    color: #ffe8ad;
}

.site-footer.site-footer--zellige .footer-social__link,
.site-footer.site-footer--zellige .site-footer__social-link {
    display: inline-grid;
    place-items: center;
    width: 1.85rem;
    height: 1.85rem;
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.06);
    color: #fff;
    font-size: 0.58rem;
    font-weight: 800;
    letter-spacing: 0.02em;
    text-decoration: none;
    transition: border-color 0.16s ease, background 0.16s ease, color 0.16s ease;
}

.site-footer.site-footer--zellige .footer-social__link:hover,
.site-footer.site-footer--zellige .footer-social__link:focus-visible,
.site-footer.site-footer--zellige .site-footer__social-link:hover,
.site-footer.site-footer--zellige .site-footer__social-link:focus-visible {
    border-color: rgba(201, 164, 76, 0.55);
    background: rgba(201, 164, 76, 0.14);
    color: #ffe8ad;
}

.site-footer.site-footer--zellige .footer-newsletter-form,
.site-footer.site-footer--zellige .site-footer__newsletter-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 0.35rem;
    align-items: center;
}

.site-footer.site-footer--zellige .footer-newsletter-form input,
.site-footer.site-footer--zellige .site-footer__newsletter-form input {
    min-height: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
}

.site-footer.site-footer--zellige .site-footer__newsletter-btn {
    min-height: 2rem;
    padding-inline: 0.85rem;
    border-radius: 999px;
    border: 1px solid rgba(201, 164, 76, 0.45);
    background: linear-gradient(135deg, #c9a44c, #9a7424);
    color: #1a0207;
    font-weight: 800;
    font-size: 0.68rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.site-footer.site-footer--zellige .site-footer__bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.72);
}

@media (min-width: 1100px) {
    .page-home .home-final-quick-track .home-final-quick__row {
        width: 100%;
        flex-wrap: wrap;
        justify-content: stretch;
    }

    .page-home .home-final-quick-card {
        flex: 1 1 calc(14.28% - 0.5rem);
        min-width: 6.75rem;
    }
}

@media (max-width: 900px) {
    .page-inner .fifa-partners-grid--page {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .page-home .home-final-quick-card {
        min-width: 6.65rem;
    }

    .page-home .home-final-city-chip.home-final-cities__item {
        min-width: 6.85rem;
    }

    .page-home .fifa-partner-card--home {
        width: 156px;
        max-height: 124px;
    }

    .page-inner .fifa-partners-grid--page {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .site-footer.site-footer--zellige .footer-newsletter-form,
    .site-footer.site-footer--zellige .site-footer__newsletter-form {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .page-inner .fifa-partners-grid--page {
        grid-template-columns: 1fr;
    }
}

/* DESIGN HELL PHASE 7.5 — FIFA partners reference strip */

.page-home .fifa-partners-section--strip {
    width: min(calc(100% - clamp(1rem, 3vw, 2rem)), 1320px);
    margin: 0.65rem auto 0.85rem;
    padding: clamp(0.85rem, 1.6vw, 1.15rem) clamp(0.75rem, 1.4vw, 1rem);
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
    overflow: visible;
}

.page-home .fifa-partners-section--strip .fifa-partners-section__head {
    margin-bottom: 0.55rem;
}

.page-home .fifa-partners-section--strip .fifa-partners-section__head h2 {
    margin-top: 0.12rem;
    font-size: clamp(0.98rem, 1.5vw, 1.2rem);
    line-height: 1.15;
}

.page-home .fifa-partners-section--strip .fifa-partners-section__eyebrow {
    font-size: 0.62rem;
    letter-spacing: 0.14em;
}

.page-home .fifa-partners-track--strip {
    overflow-x: auto;
    overflow-y: hidden;
    max-width: 100%;
    margin: 0;
    padding: 0;
    scroll-padding-inline: 0;
    scroll-snap-type: x proximity;
    scrollbar-width: thin;
    scrollbar-color: rgba(177, 15, 46, 0.25) transparent;
    -webkit-overflow-scrolling: touch;
}

.page-home .fifa-partners-track--strip::-webkit-scrollbar {
    height: 4px;
}

.page-home .fifa-partners-track--strip::-webkit-scrollbar-thumb {
    border-radius: 999px;
    background: rgba(177, 15, 46, 0.22);
}

.page-home .fifa-partners-grid--strip {
    display: grid;
    grid-template-columns: repeat(9, minmax(0, 1fr));
    gap: 0.42rem;
    width: 100%;
    min-width: 0;
    padding: 0;
}

.page-home .fifa-partner-card--strip {
    min-height: 0;
    height: auto;
    max-height: 96px;
    display: grid;
    grid-template-rows: auto minmax(38px, 1fr);
    align-content: start;
    gap: 0.22rem;
    padding: 0.38rem 0.32rem 0.42rem;
    border: 1px solid #eceff3;
    border-top: 2px solid rgba(201, 164, 76, 0.72);
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}

.page-home .fifa-partner-card--strip .fifa-partner-label {
    justify-self: center;
    min-height: 0;
    padding: 0.1rem 0.32rem;
    font-size: 0.48rem;
    letter-spacing: 0.05em;
    line-height: 1.2;
    white-space: nowrap;
}

.page-home .fifa-partner-card--strip .fifa-partner-logo {
    min-height: 0;
    max-height: 44px;
    padding: 0.12rem 0.2rem;
}

.page-home .fifa-partner-card--strip .fifa-partner-logo img {
    display: block;
    width: auto;
    height: auto;
    max-height: 38px;
    max-width: 92%;
    object-fit: contain;
}

.page-home .fifa-partner-card--strip .fifa-partner-text-logo {
    min-height: 2rem;
    max-height: 2.35rem;
    padding: 0.18rem 0.28rem;
    font-size: 0.58rem;
    line-height: 1.15;
    border-radius: 8px;
}

.page-inner .fifa-partners-section--page.fifa-partners-section--strip,
.page-inner .fifa-partners-section.fifa-partners-section--page {
    padding-top: 0.35rem;
    padding-bottom: 0.65rem;
}

.page-inner .fifa-partners-track--page {
    overflow: visible;
    max-width: 100%;
    padding: 0;
}

.page-inner .fifa-partners-grid--page.fifa-partners-grid--strip,
.page-inner .fifa-partners-grid--page {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.55rem;
    width: 100%;
    min-width: 0;
}

.page-inner .fifa-partner-card--page.fifa-partner-card--strip,
.page-inner .fifa-partner-card--page {
    min-height: 0;
    max-height: 132px;
    grid-template-rows: auto minmax(48px, auto) auto;
    gap: 0.28rem;
    padding: 0.5rem 0.45rem 0.55rem;
    border: 1px solid #eceff3;
    border-top: 2px solid rgba(201, 164, 76, 0.72);
    border-radius: 14px;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
}

.page-inner .fifa-partner-card--page .fifa-partner-label {
    justify-self: center;
    font-size: 0.58rem;
    padding: 0.14rem 0.38rem;
}

.page-inner .fifa-partner-card--page .fifa-partner-logo {
    min-height: 0;
    max-height: 52px;
    padding: 0.15rem;
}

.page-inner .fifa-partner-card--page .fifa-partner-logo img {
    max-height: 44px;
    max-width: 88%;
}

.page-inner .fifa-partner-card--page .fifa-partner-name {
    font-size: 0.72rem;
    line-height: 1.15;
    text-align: center;
}

.page-inner .fifa-partner-card--page .fifa-partner-text-logo {
    min-height: 2.1rem;
    font-size: 0.62rem;
    padding: 0.22rem 0.32rem;
}

@media (max-width: 1180px) {
    .page-home .fifa-partners-grid--strip {
        display: flex;
        flex-wrap: nowrap;
        gap: 0.45rem;
        width: 100%;
        min-width: 0;
    }

    .page-home .fifa-partner-card--strip {
        flex: 0 0 clamp(138px, 14vw, 168px);
        max-width: clamp(138px, 14vw, 168px);
        scroll-snap-align: start;
    }
}

@media (max-width: 900px) {
    .page-inner .fifa-partners-grid--page {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .page-home .fifa-partner-card--strip {
        flex: 0 0 142px;
        max-width: 142px;
        max-height: 92px;
    }

    .page-home .fifa-partner-card--strip .fifa-partner-logo img {
        max-height: 34px;
    }
}

@media (max-width: 480px) {
    .page-inner .fifa-partners-grid--page {
        grid-template-columns: 1fr;
    }

    .page-home .fifa-partner-card--strip {
        flex: 0 0 132px;
        max-width: 132px;
    }
}

/* DESIGN HELL PHASE 7.6 — Professional scroll controls */

.scroll-carousel {
    position: relative;
    display: flex;
    align-items: stretch;
    gap: 0.35rem;
    max-width: 100%;
    min-width: 0;
}

.scroll-carousel [data-scroll-track] {
    flex: 1 1 auto;
    min-width: 0;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    scroll-snap-type: x proximity;
    scrollbar-width: none;
    -ms-overflow-style: none;
    -webkit-overflow-scrolling: touch;
}

.scroll-carousel [data-scroll-track]::-webkit-scrollbar {
    display: none;
    width: 0;
    height: 0;
}

.scroll-nav {
    flex: 0 0 auto;
    align-self: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    margin: 0;
    padding: 0;
    border: 1px solid rgba(15, 23, 42, 0.12);
    border-radius: 999px;
    background: #fff;
    color: #334155;
    font-size: 1.15rem;
    line-height: 1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    cursor: pointer;
    transition: color 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
}

.scroll-nav:hover:not(:disabled) {
    border-color: rgba(177, 15, 46, 0.45);
    color: #b10f2e;
    box-shadow: 0 6px 16px rgba(177, 15, 46, 0.12);
}

.scroll-nav:focus-visible {
    outline: 2px solid rgba(201, 164, 76, 0.85);
    outline-offset: 2px;
}

.scroll-nav:disabled,
.scroll-nav[aria-disabled="true"] {
    opacity: 0.35;
    cursor: default;
    box-shadow: none;
}

.scroll-nav[hidden] {
    display: none;
}

.scroll-nav--prev {
    order: 0;
}

.scroll-nav--next {
    order: 2;
}

.scroll-carousel [data-scroll-track] {
    order: 1;
}

.fifa-partners-carousel {
    margin-top: 0.1rem;
    gap: 0.4rem;
}

.page-home .fifa-partners-section--home {
    overflow: hidden;
}

.page-home .fifa-partners-section--home .fifa-partners-track,
.page-home .fifa-partners-track--strip {
    overflow-x: auto;
    overflow-y: hidden;
    max-width: 100%;
    margin: 0;
    padding: 0;
    scroll-padding-inline: 0;
    scroll-behavior: smooth;
    scroll-snap-type: x proximity;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.page-home .fifa-partners-section--home .fifa-partners-track::-webkit-scrollbar,
.page-home .fifa-partners-track--strip::-webkit-scrollbar {
    display: none;
    width: 0;
    height: 0;
}

.page-home .fifa-partners-grid--strip {
    padding-inline: 0;
    margin-inline: 0;
}

.page-home .fifa-partner-card {
    scroll-snap-align: start;
}

.page-home .home-final-quick-carousel,
.page-home .home-final-cities-carousel {
    margin-top: 0.15rem;
}

.page-home .home-final-quick-track,
.page-home .home-final-cities-scroll {
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-width: none;
    -ms-overflow-style: none;
    scroll-behavior: smooth;
    scroll-snap-type: x proximity;
    padding-bottom: 0;
}

.page-home .home-final-quick-track::-webkit-scrollbar,
.page-home .home-final-cities-scroll::-webkit-scrollbar {
    display: none;
    width: 0;
    height: 0;
}

.page-home .home-final-quick-track .home-final-quick__row {
    padding-inline: 0;
    scroll-padding-inline: 0;
}

.page-home .home-final-cities-scroll {
    display: flex;
    flex-wrap: nowrap;
    padding-inline: 0;
    scroll-padding-inline: 0;
}

@media (max-width: 640px) {
    .scroll-nav {
        width: 1.65rem;
        height: 1.65rem;
        font-size: 1rem;
    }

    .scroll-carousel {
        gap: 0.25rem;
    }
}

@media (min-width: 1180px) {
    .page-home .fifa-partners-carousel .scroll-nav[hidden] {
        display: none;
    }
}

/* DESIGN HELL PHASE 7.7 — Homepage fixtures overflow microfix */

.page-home .home-final-card--fixtures {
    align-self: start;
}

.page-home .home-final-fixtures__columns {
    align-items: start;
}

.page-home .home-final-fixtures-list,
.page-home .home-final-fixtures__scroll {
    max-height: none;
    overflow: visible;
    overflow-x: hidden;
    overscroll-behavior: auto;
    padding-inline-end: 0;
}

.page-home .home-final-fixtures-list {
    display: grid;
    gap: 0.38rem;
}

.page-home .home-final-fixtures .match-card {
    padding: 0.38rem 0.45rem 0.42rem;
    border-radius: 10px;
}

.page-home .home-final-fixtures .match-card::before {
    display: none;
}

.page-home .home-final-fixtures .match-card__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.35rem;
    margin-bottom: 0.22rem;
}

.page-home .home-final-fixtures .match-card__competition {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.18rem;
    min-width: 0;
}

.page-home .home-final-fixtures .status-pill,
.page-home .home-final-fixtures .match-ref-status {
    padding: 0.12rem 0.34rem;
    font-size: 0.56rem;
    line-height: 1.15;
    letter-spacing: 0.04em;
}

.page-home .home-final-fixtures .badge {
    padding: 0.1rem 0.28rem;
    font-size: 0.54rem;
    line-height: 1.15;
}

.page-home .home-final-fixtures .match-card__date {
    flex: 0 0 auto;
    font-size: 0.62rem;
    line-height: 1.2;
    color: #6b7788;
    white-space: nowrap;
}

.page-home .home-final-fixtures .match-card__teams {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
    align-items: center;
    gap: 0.28rem;
}

.page-home .home-final-fixtures .match-card__team {
    display: grid;
    gap: 0.08rem;
    min-width: 0;
}

.page-home .home-final-fixtures .match-card__team--away {
    text-align: end;
}

.page-home .home-final-fixtures .match-card__label {
    display: none;
}

.page-home .home-final-fixtures .match-card__team strong {
    font-size: 0.74rem;
    line-height: 1.18;
    overflow-wrap: anywhere;
}

.page-home .home-final-fixtures .match-ref-flag,
.page-home .home-final-fixtures .match-ref-flag .flag-ref-wrap,
.page-home .home-final-fixtures .match-ref-flag img {
    width: 28px !important;
    height: 20px !important;
    min-width: 28px !important;
    min-height: 20px !important;
    max-width: 28px !important;
    max-height: 20px !important;
    border-radius: 3px;
    object-fit: cover;
}

.page-home .home-final-fixtures .score-box {
    min-width: 2.35rem;
    padding: 0.22rem 0.34rem;
    font-size: 0.76rem;
    line-height: 1.1;
}

.page-home .home-final-fixtures .score-box__sub {
    margin-top: 0.08rem;
    font-size: 0.52rem;
    line-height: 1.1;
}

.page-home .home-final-fixtures .match-card__meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.18rem;
    margin-top: 0.28rem;
    padding-top: 0.28rem;
    border-top: 1px solid rgba(20, 28, 39, 0.06);
    font-size: 0.58rem;
}

.page-home .home-final-fixtures .meta-pill {
    padding: 0.12rem 0.32rem;
    font-size: 0.56rem;
    line-height: 1.15;
}

.page-home .home-final-fixtures .match-card__cta {
    margin-inline-start: auto;
    padding: 0.14rem 0.42rem;
    font-size: 0.58rem;
    line-height: 1.15;
    white-space: nowrap;
}

.page-home .home-final-cities.home-final-card {
    align-self: start;
}

.page-home .home-final-cities__map {
    max-height: 168px;
    margin-bottom: 0.4rem;
    padding: 0.28rem;
}

.page-home .home-final-cities__map img {
    max-height: 150px;
}

.page-home .home-final-cities-carousel {
    margin-top: 0;
}

@media (max-width: 760px) {
    .page-home .home-final-fixtures__columns {
        grid-template-columns: 1fr;
        gap: 0.55rem;
    }

    .page-home .home-final-fixtures .match-card__teams {
        gap: 0.22rem;
    }

    .page-home .home-final-fixtures .match-card__team strong {
        font-size: 0.78rem;
    }

    .page-home .home-final-cities__map {
        max-height: 180px;
    }

    .page-home .home-final-cities__map img {
        max-height: 165px;
    }
}

/* DESIGN HELL PHASE 7.8 — Homepage final density and grid balance fix */

.page-home .page-container {
    gap: 0.4rem;
}

.page-home .home-final-grid {
    gap: 0.4rem;
    align-items: start;
}

.page-home .home-final-grid > .home-final-card {
    padding: 0.52rem 0.6rem;
}

.page-home .home-final-card__head {
    margin-bottom: 0.35rem;
    gap: 0.4rem;
}

.page-home .home-final-card__head--compact {
    margin-bottom: 0.28rem;
}

.page-home .home-final-card--fixtures,
.page-home .home-final-card--standings,
.page-home .home-final-card--bracket,
.page-home .home-final-card--stadiums,
.page-home .home-final-cities.home-final-card {
    align-self: start;
}

.page-home .home-final-standings__groups,
.page-home .home-final-standings__group {
    overflow: visible;
    max-height: none;
}

.page-home .home-final-standings__groups {
    gap: 0.3rem;
}

.page-home .home-final-standings__group {
    padding: 0.3rem;
}

.page-home .home-final-standings__group .table-shell,
.page-home .home-final-standings .standings-ref-table__scroll {
    overflow-y: visible;
    max-height: none;
}

.page-home .home-final-standings .standings-ref-table--compact-flags table {
    min-width: 0;
    width: 100%;
    font-size: 0.6rem;
}

.page-home .home-final-standings .standings-ref-table--compact-flags th,
.page-home .home-final-standings .standings-ref-table--compact-flags td {
    padding: 0.18rem 0.24rem;
    line-height: 1.12;
}

.page-home .home-final-standings .standings-ref-table--compact-flags th:nth-child(n+4):nth-child(-n+9),
.page-home .home-final-standings .standings-ref-table--compact-flags td:nth-child(n+4):nth-child(-n+9) {
    display: none;
}

.page-home .home-final-standings .standings-ref-table--compact-flags .rank-pill {
    min-width: 1.15rem;
    padding: 0.08rem 0.2rem;
    font-size: 0.56rem;
}

.page-home .home-final-standings .standings-ref-flag,
.page-home .home-final-standings .standings-ref-flag .flag-ref-wrap,
.page-home .home-final-standings .standings-ref-table--compact-flags img.team-flag,
.page-home .home-final-standings .standings-ref-table--compact-flags img.media-bound-image {
    width: 24px !important;
    height: 17px !important;
    min-width: 24px !important;
    min-height: 17px !important;
    max-width: 24px !important;
    max-height: 17px !important;
}

.page-home .home-final-standings .standings-ref-table--compact-flags .standings-ref-team a {
    font-size: 0.62rem;
    line-height: 1.15;
}

.page-home .home-final-bracket-list,
.page-home .home-final-bracket__scroll {
    display: grid;
    gap: 0.35rem;
    max-height: none;
    overflow: visible;
    overscroll-behavior: auto;
}

.page-home .home-final-bracket .match-card {
    padding: 0.36rem 0.42rem 0.4rem;
    border-radius: 10px;
}

.page-home .home-final-bracket .match-card::before {
    display: none;
}

.page-home .home-final-bracket .match-card__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.3rem;
    margin-bottom: 0.2rem;
}

.page-home .home-final-bracket .status-pill,
.page-home .home-final-bracket .badge {
    padding: 0.1rem 0.28rem;
    font-size: 0.54rem;
    line-height: 1.1;
}

.page-home .home-final-bracket .match-card__date {
    font-size: 0.6rem;
    white-space: nowrap;
}

.page-home .home-final-bracket .match-card__teams {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
    align-items: center;
    gap: 0.24rem;
}

.page-home .home-final-bracket .match-card__label {
    display: none;
}

.page-home .home-final-bracket .match-card__team strong {
    font-size: 0.72rem;
    line-height: 1.15;
}

.page-home .home-final-bracket .match-ref-flag,
.page-home .home-final-bracket .match-ref-flag img {
    width: 26px !important;
    height: 18px !important;
    min-width: 26px !important;
    max-width: 26px !important;
    max-height: 18px !important;
    border-radius: 3px;
    object-fit: cover;
}

.page-home .home-final-bracket .score-box {
    min-width: 2.2rem;
    padding: 0.18rem 0.3rem;
    font-size: 0.72rem;
}

.page-home .home-final-bracket .match-card__meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.16rem;
    margin-top: 0.24rem;
    padding-top: 0.24rem;
    border-top: 1px solid rgba(20, 28, 39, 0.06);
    font-size: 0.56rem;
}

.page-home .home-final-bracket .meta-pill {
    padding: 0.1rem 0.28rem;
    font-size: 0.54rem;
}

.page-home .home-final-bracket .match-card__cta {
    margin-inline-start: auto;
    padding: 0.12rem 0.38rem;
    font-size: 0.56rem;
}

.page-home .home-final-stadiums__layout {
    grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr);
    gap: 0.38rem;
    align-items: start;
}

.page-home .home-final-stadiums__featured {
    padding: 0.38rem;
    gap: 0.28rem;
}

.page-home .home-final-stadiums__featured img {
    height: 88px;
    max-height: 88px;
    object-fit: cover;
}

.page-home .home-final-stadiums__featured strong {
    font-size: 0.74rem;
    line-height: 1.15;
}

.page-home .home-final-stadiums__featured span {
    font-size: 0.6rem;
}

.page-home .home-final-stadiums__thumbs {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.3rem;
    align-content: start;
}

.page-home .home-final-stadiums__thumb {
    padding: 0.32rem;
    gap: 0.16rem;
}

.page-home .home-final-stadiums__thumb img {
    height: 42px;
    max-height: 42px;
}

.page-home .home-final-stadiums__thumb span {
    font-size: 0.58rem;
    line-height: 1.12;
    overflow-wrap: anywhere;
}

.page-home .home-final-metrics.home-final-card,
.page-home .home-final-partners.home-final-card {
    padding: 0.52rem 0.6rem;
}

.page-home .home-final-news {
    margin-top: 0;
}

.page-home .home-final-partners {
    margin-bottom: 0.15rem;
}

.page-home .fifa-partners-section--home.fifa-partners-section--strip {
    margin: 0.25rem auto 0.35rem;
    padding: clamp(0.55rem, 1vw, 0.75rem) clamp(0.6rem, 1.1vw, 0.8rem);
}

.page-home .fifa-partners-section--strip .fifa-partners-section__head {
    margin-bottom: 0.38rem;
}

.page-home .fifa-partners-carousel {
    margin-top: 0;
}

.page-home .fifa-partner-card--strip {
    max-height: 88px;
}

@media (max-width: 760px) {
    .page-home .home-final-grid > .home-final-card {
        padding: 0.55rem 0.62rem;
    }

    .page-home .home-final-stadiums__layout {
        grid-template-columns: 1fr;
    }

    .page-home .home-final-stadiums__featured img {
        height: 96px;
        max-height: 96px;
    }

    .page-home .fifa-partners-section--home.fifa-partners-section--strip {
        margin-bottom: 0.3rem;
    }
}

/* DESIGN HELL PHASE 7.9 — Homepage host map and quick access final fix */

.page-home .home-final-quick--official {
    position: relative;
    z-index: 2;
    margin-top: -0.5rem;
    margin-bottom: 0.28rem;
    padding-inline: 0.05rem;
}

.page-home .home-final-quick--official .home-final-quick__grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 0.36rem;
    width: 100%;
    min-width: 0;
    max-width: 100%;
}

.page-home .home-final-quick-card--official {
    display: grid;
    grid-template-rows: auto auto auto;
    justify-items: center;
    align-content: center;
    gap: 0.18rem;
    min-height: 86px;
    max-height: 102px;
    height: 100%;
    padding: 0.4rem 0.32rem 0.36rem;
    border: 1px solid rgba(20, 28, 39, 0.08);
    border-top: 2px solid rgba(201, 164, 76, 0.78);
    border-radius: 12px;
    background: #fff;
    color: var(--m2030-ink, #141c27);
    text-align: center;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
    transition: transform 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease;
}

.page-home .home-final-quick-card--official:hover,
.page-home .home-final-quick-card--official:focus-visible {
    transform: translateY(-1px);
    border-color: rgba(177, 15, 46, 0.22);
    border-top-color: rgba(177, 15, 46, 0.72);
    box-shadow: 0 6px 16px rgba(177, 15, 46, 0.08);
}

.page-home .home-final-quick-card--official strong {
    font-size: 0.66rem;
    line-height: 1.15;
    font-weight: 800;
}

.page-home .home-final-quick-card--official span:last-child {
    color: #6b7788;
    font-size: 0.52rem;
    line-height: 1.1;
}

.page-home .home-final-quick--official .home-final-quick-icon {
    display: grid;
    place-items: center;
    width: 1.48rem;
    height: 1.48rem;
    border-radius: 999px;
    font-size: 0.66rem;
    line-height: 1;
    color: #fff;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.2);
}

.page-home .home-final-quick--official .home-final-quick-icon--red {
    background: linear-gradient(145deg, #c41a34, #8e1028);
}

.page-home .home-final-quick--official .home-final-quick-icon--green {
    background: linear-gradient(145deg, #128052, #0a5a38);
}

.page-home .home-final-quick--official .home-final-quick-icon--gold {
    background: linear-gradient(145deg, #c9a44c, #9a7728);
}

.page-home .home-final-quick--official .home-final-quick-icon--map {
    background: linear-gradient(145deg, #334155, #141c27);
}

.page-home .home-final-quick-card--official.home-final-quick-card--map {
    border-top-color: rgba(20, 28, 39, 0.55);
}

.page-home .home-final-card--map {
    align-self: start;
}

.page-home .home-final-map-preview {
    width: 100%;
    min-width: 0;
}

.page-home .home-final-map-preview__frame,
.page-home .home-final-card--map .home-final-cities__map {
    display: grid;
    place-items: center;
    width: 100%;
    min-height: clamp(260px, 31vw, 340px);
    max-height: clamp(260px, 31vw, 340px);
    margin-bottom: 0.42rem;
    padding: 0.55rem 0.75rem;
    border: 1px solid rgba(20, 28, 39, 0.07);
    border-radius: 12px;
    background: linear-gradient(180deg, #f8fafc 0%, #edf2f7 100%);
    overflow: hidden;
}

.page-home .home-final-map-preview__image,
.page-home .home-final-card--map .home-final-cities__map img {
    display: block;
    width: 100%;
    height: 100%;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    object-position: center;
}

.page-home .home-final-card--map .home-final-cities-carousel {
    margin-top: 0;
}

.page-home .home-final-card--map .home-final-city-chip.home-final-cities__item {
    min-width: 6.75rem;
    max-width: 8.75rem;
    padding: 0.38rem 0.48rem;
}

@media (max-width: 1180px) {
    .page-home .home-final-quick--official .home-final-quick__grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .page-home .home-final-map-preview__frame,
    .page-home .home-final-card--map .home-final-cities__map {
        min-height: clamp(240px, 38vw, 300px);
        max-height: clamp(240px, 38vw, 300px);
    }
}

@media (max-width: 900px) {
    .page-home .home-final-quick--official .home-final-quick__grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .page-home .home-final-quick--official {
        margin-top: -0.35rem;
    }

    .page-home .home-final-quick--official .home-final-quick__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.32rem;
    }

    .page-home .home-final-quick-card--official {
        min-height: 84px;
        max-height: 96px;
        padding: 0.36rem 0.28rem 0.32rem;
    }

    .page-home .home-final-quick-card--official strong {
        font-size: 0.68rem;
    }

    .page-home .home-final-map-preview__frame,
    .page-home .home-final-card--map .home-final-cities__map {
        min-height: clamp(210px, 52vw, 260px);
        max-height: clamp(210px, 52vw, 260px);
        padding: 0.45rem 0.55rem;
    }
}

/* DESIGN HELL PHASE 7.10 — Homepage quick access and host map laptop fix */

.page-home .home-final-quick.home-final-quick--official {
    margin-top: -0.38rem;
    margin-bottom: 0.12rem;
    padding-inline: 0;
}

.page-home .home-final-quick.home-final-quick--official .home-final-quick__grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(120px, 1fr));
    gap: 0.28rem;
    width: 100%;
    min-width: 0;
}

.page-home .home-final-quick-card.home-final-quick-card--compact {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 0.28rem;
    min-height: 72px;
    max-height: 86px;
    height: auto;
    padding: 0.28rem 0.32rem;
    border: 1px solid rgba(20, 28, 39, 0.07);
    border-top: 2px solid rgba(201, 164, 76, 0.72);
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    text-align: left;
}

.page-home .home-final-quick-card--compact:hover,
.page-home .home-final-quick-card--compact:focus-visible {
    transform: none;
    border-color: rgba(177, 15, 46, 0.18);
    border-top-color: rgba(177, 15, 46, 0.62);
    box-shadow: 0 3px 10px rgba(177, 15, 46, 0.06);
}

.page-home .home-final-quick-card__body {
    display: flex;
    flex-direction: column;
    gap: 0.04rem;
    min-width: 0;
    flex: 1 1 auto;
}

.page-home .home-final-quick-card--compact strong {
    font-size: 0.62rem;
    line-height: 1.12;
    font-weight: 800;
    overflow-wrap: anywhere;
}

.page-home .home-final-quick-card__meta {
    color: #6b7788;
    font-size: 0.48rem;
    line-height: 1.08;
}

.page-home .home-final-quick-card--compact .home-final-quick-card__icon,
.page-home .home-final-quick-card--compact .home-final-quick-icon {
    flex: 0 0 auto;
    width: 1.75rem;
    height: 1.75rem;
    font-size: 0.62rem;
}

.page-home .home-final-grid {
    gap: 0.32rem;
    margin-top: 0;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    align-items: start;
}

.page-home .home-final-grid > .home-final-card {
    padding: 0.48rem 0.55rem;
}

.page-home .home-final-grid .home-final-card__head {
    margin-bottom: 0.3rem;
    gap: 0.35rem;
}

.page-home .home-final-card--fixtures,
.page-home .home-final-card--map.home-final-cities {
    min-height: 0;
}

.page-home .home-final-map.home-final-map-preview,
.page-home .home-final-card--map .home-final-map-preview {
    width: 100%;
    min-width: 0;
}

.page-home .home-final-map__frame,
.page-home .home-final-map-preview__frame.home-final-map__frame,
.page-home .home-final-card--map .home-final-cities__map.home-final-map__frame {
    display: grid;
    place-items: center;
    width: 100%;
    min-height: clamp(230px, 24vw, 280px);
    max-height: clamp(230px, 24vw, 280px);
    margin-bottom: 0.32rem;
    padding: 0.35rem 0.45rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 10px;
    background: linear-gradient(180deg, #f9fbfd 0%, #eef3f8 100%);
    overflow: hidden;
}

.page-home .home-final-map__image,
.page-home .home-final-map-preview__image.home-final-map__image,
.page-home .home-final-card--map .home-final-map__image {
    display: block;
    width: 100%;
    height: 100%;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    object-position: center;
}

.page-home .home-final-card--map .home-final-cities-carousel {
    gap: 0.22rem;
    margin-top: 0;
}

.page-home .home-final-card--map .scroll-nav {
    width: 1.55rem;
    height: 1.55rem;
    font-size: 0.95rem;
}

.page-home .home-final-card--map .home-final-city-chip.home-final-cities__item {
    min-width: 6.25rem;
    max-width: 8rem;
    padding: 0.32rem 0.4rem;
}

.page-home .home-final-card--map .home-final-city-chip span {
    font-size: 0.54rem;
}

.page-home .home-final-card--map .home-final-city-chip strong {
    font-size: 0.68rem;
    margin-top: 0.08rem;
}

@media (max-width: 1279px) and (min-width: 1024px) {
    .page-home .home-final-quick.home-final-quick--official .home-final-quick__grid {
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.22rem;
    }

    .page-home .home-final-quick-card--compact {
        padding: 0.24rem 0.26rem;
        min-height: 72px;
        max-height: 82px;
    }

    .page-home .home-final-quick-card--compact strong {
        font-size: 0.58rem;
    }

    .page-home .home-final-quick-card__meta {
        font-size: 0.46rem;
    }

    .page-home .home-final-quick-card--compact .home-final-quick-icon {
        width: 1.65rem;
        height: 1.65rem;
        font-size: 0.58rem;
    }
}

@media (max-width: 1023px) {
    .page-home .home-final-quick.home-final-quick--official .home-final-quick__grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.26rem;
    }

    .page-home .home-final-map__frame,
    .page-home .home-final-card--map .home-final-cities__map.home-final-map__frame {
        min-height: clamp(220px, 34vw, 260px);
        max-height: clamp(220px, 34vw, 260px);
    }
}

@media (max-width: 767px) {
    .page-home .home-final-quick.home-final-quick--official .home-final-quick__grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .page-home .home-final-quick.home-final-quick--official .home-final-quick__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.28rem;
    }

    .page-home .home-final-quick-card--compact {
        min-height: 74px;
        max-height: 86px;
    }

    .page-home .home-final-map__frame,
    .page-home .home-final-card--map .home-final-cities__map.home-final-map__frame {
        min-height: clamp(180px, 48vw, 220px);
        max-height: clamp(180px, 48vw, 220px);
        padding: 0.32rem 0.38rem;
        margin-bottom: 0.28rem;
    }
}

/* DESIGN HELL PHASE 7.11 — Homepage host map and fixtures final fix */

.page-home .home-final-fixtures__preview {
    min-width: 0;
}

.page-home .home-final-fixtures__subhead {
    margin: 0 0 0.32rem;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #6b7788;
}

.page-home .home-final-fixtures__sr-data {
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

.page-home .home-final-fixtures-list--upcoming {
    gap: 0.3rem;
}

.page-home .home-final-fixtures-list--upcoming .match-card:nth-child(n + 3) {
    display: none;
}

.page-home .home-final-fixtures .match-card {
    padding: 0.3rem 0.36rem 0.34rem;
    border-radius: 9px;
    box-shadow: none;
}

.page-home .home-final-fixtures .match-card::before {
    display: none;
}

.page-home .home-final-fixtures .match-card__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.25rem;
    margin-bottom: 0.16rem;
}

.page-home .home-final-fixtures .match-card__competition {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.14rem;
    min-width: 0;
}

.page-home .home-final-fixtures .status-pill,
.page-home .home-final-fixtures .match-ref-status,
.page-home .home-final-fixtures .badge {
    padding: 0.08rem 0.26rem;
    font-size: 0.5rem;
    line-height: 1.1;
}

.page-home .home-final-fixtures .match-card__date {
    font-size: 0.56rem;
    line-height: 1.1;
    white-space: nowrap;
}

.page-home .home-final-fixtures .match-card__teams {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
    align-items: center;
    gap: 0.2rem;
}

.page-home .home-final-fixtures .match-card__label {
    display: none;
}

.page-home .home-final-fixtures .match-card__team strong {
    font-size: 0.66rem;
    line-height: 1.12;
}

.page-home .home-final-fixtures .match-ref-flag,
.page-home .home-final-fixtures .match-ref-flag img,
.page-home .home-final-fixtures .match-ref-flag .flag-ref-wrap {
    width: 24px !important;
    height: 17px !important;
    min-width: 24px !important;
    max-width: 24px !important;
    max-height: 17px !important;
    border-radius: 2px;
    object-fit: cover;
}

.page-home .home-final-fixtures .score-box {
    min-width: 2rem;
    padding: 0.16rem 0.28rem;
    font-size: 0.68rem;
    line-height: 1.05;
}

.page-home .home-final-fixtures .match-card__meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.14rem;
    margin-top: 0.2rem;
    padding-top: 0.2rem;
    border-top: 1px solid rgba(20, 28, 39, 0.06);
}

.page-home .home-final-fixtures .meta-pill {
    padding: 0.08rem 0.24rem;
    font-size: 0.5rem;
    line-height: 1.1;
}

.page-home .home-final-fixtures .match-card__cta {
    margin-inline-start: auto;
    padding: 0.1rem 0.34rem;
    font-size: 0.52rem;
    line-height: 1.1;
}

.page-home .home-final-card--map.home-final-cities {
    position: relative;
}

.page-home .home-final-map-frame {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-width: 0;
    height: clamp(220px, 26vw, 320px);
    max-height: clamp(220px, 26vw, 320px);
    margin-bottom: 0.3rem;
    padding: 0.4rem 0.5rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 10px;
    background: linear-gradient(180deg, #f9fbfd 0%, #eef3f8 100%);
    overflow: hidden;
}

.page-home .home-final-map-frame img,
.page-home .home-final-map-frame__image {
    display: block;
    width: 100%;
    height: 100%;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    object-position: center;
}

.page-home .home-final-city-strip {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.28rem;
    width: 100%;
    min-width: 0;
}

.page-home .home-final-city-strip .home-final-city-chip {
    display: grid;
    gap: 0.04rem;
    min-width: 0;
    max-width: none;
    padding: 0.3rem 0.38rem;
    border: 1px solid rgba(20, 28, 39, 0.06);
    border-radius: 8px;
    background: #fafbfd;
    text-decoration: none;
    color: inherit;
}

.page-home .home-final-city-strip .home-final-city-chip span {
    color: var(--m2030-red, #b10f2e);
    font-size: 0.5rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    line-height: 1.1;
}

.page-home .home-final-city-strip .home-final-city-chip strong {
    font-size: 0.66rem;
    line-height: 1.12;
    overflow-wrap: anywhere;
}

.page-home .home-final-grid {
    align-items: start;
}

.page-home .home-final-card--fixtures,
.page-home .home-final-card--map.home-final-cities {
    align-self: start;
}

@media (min-width: 900px) {
    .page-home .home-final-city-strip {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

@media (max-width: 1023px) {
    .page-home .home-final-map-frame {
        height: clamp(230px, 34vw, 270px);
        max-height: clamp(230px, 34vw, 270px);
    }
}

@media (max-width: 640px) {
    .page-home .home-final-map-frame {
        height: clamp(180px, 48vw, 220px);
        max-height: clamp(180px, 48vw, 220px);
        padding: 0.32rem 0.38rem;
        margin-bottom: 0.26rem;
    }

    .page-home .home-final-city-strip {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.24rem;
    }
}

/* DESIGN HELL PHASE 7.12 — Homepage horizontal scrolls and footer final visual fix */

.page-home .home-final-quick-shell,
.page-home .home-final-city-strip-shell {
    display: flex;
    align-items: center;
    gap: 0.28rem;
    max-width: 100%;
    min-width: 0;
}

.page-home .home-final-quick-track,
.page-home .home-final-city-strip-shell .home-final-city-strip {
    flex: 1 1 auto;
    min-width: 0;
    display: flex;
    flex-wrap: nowrap;
    gap: 0.28rem;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    scroll-snap-type: x proximity;
    scrollbar-width: none;
    -ms-overflow-style: none;
    -webkit-overflow-scrolling: touch;
}

.page-home .home-final-quick-track::-webkit-scrollbar,
.page-home .home-final-city-strip::-webkit-scrollbar {
    display: none;
    width: 0;
    height: 0;
}

.page-home .home-final-quick-track .home-final-quick-card--compact {
    flex: 0 0 auto;
    min-width: 7.1rem;
    max-width: 9.25rem;
    scroll-snap-align: start;
}

.page-home .home-final-quick-icon svg {
    display: block;
    width: 0.92rem;
    height: 0.92rem;
}

.page-home .home-final-scroll-btn,
.page-home .home-final-city-scroll-btn {
    flex: 0 0 auto;
    display: inline-grid;
    place-items: center;
    width: 1.75rem;
    height: 1.75rem;
    padding: 0;
    border: 1px solid rgba(201, 164, 76, 0.45);
    border-radius: 999px;
    background: #fff;
    color: #8f0f1f;
    font-size: 1rem;
    line-height: 1;
    box-shadow: 0 3px 10px rgba(15, 23, 42, 0.08);
    cursor: pointer;
    transition: color 0.16s ease, border-color 0.16s ease, background 0.16s ease;
}

.page-home .home-final-scroll-btn:hover:not(:disabled),
.page-home .home-final-city-scroll-btn:hover:not(:disabled) {
    border-color: rgba(177, 15, 46, 0.55);
    background: #fff8f0;
    color: #b10f2e;
}

.page-home .home-final-scroll-btn:disabled,
.page-home .home-final-city-scroll-btn:disabled,
.page-home .home-final-scroll-btn[hidden],
.page-home .home-final-city-scroll-btn[hidden] {
    opacity: 0.35;
    cursor: default;
}

.page-home .home-final-scroll-btn[hidden],
.page-home .home-final-city-scroll-btn[hidden] {
    display: none;
}

.page-home .home-final-city-strip .home-final-city-chip {
    flex: 0 0 auto;
    display: grid;
    gap: 0.04rem;
    min-width: 6.35rem;
    max-width: 8.5rem;
    padding: 0.28rem 0.36rem;
    border: 1px solid rgba(20, 28, 39, 0.07);
    border-radius: 8px;
    background: #fafbfd;
    text-decoration: none;
    color: inherit;
    scroll-snap-align: start;
}

.page-home .home-final-city-strip .home-final-city-chip span {
    color: var(--m2030-red, #b10f2e);
    font-size: 0.48rem;
    font-weight: 800;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    line-height: 1.1;
}

.page-home .home-final-city-strip .home-final-city-chip strong {
    font-size: 0.64rem;
    line-height: 1.12;
    overflow-wrap: anywhere;
}

@media (min-width: 1360px) {
    .page-home .home-final-quick-track {
        flex-wrap: nowrap;
    }

    .page-home .home-final-quick-track .home-final-quick-card--compact {
        min-width: calc((100% - (0.28rem * 6)) / 7);
        max-width: none;
    }
}

.site-footer.site-footer--zellige {
    background:
        radial-gradient(circle at 14% 0%, rgba(255, 214, 138, 0.18), transparent 36%),
        radial-gradient(circle at 88% 12%, rgba(255, 255, 255, 0.08), transparent 28%),
        linear-gradient(180deg, #a30f24 0%, #8f0f1f 54%, #7a0c1a 100%);
    border-top: 3px solid rgba(201, 164, 76, 0.72);
}

.site-footer.site-footer--zellige::before {
    opacity: 0.14;
    background-image:
        repeating-linear-gradient(45deg, rgba(255, 255, 255, 0.06) 0 1px, transparent 1px 14px),
        repeating-linear-gradient(-45deg, rgba(255, 255, 255, 0.05) 0 1px, transparent 1px 14px),
        radial-gradient(circle at 20% 30%, rgba(201, 164, 76, 0.12), transparent 42%);
    background-size: auto, auto, 100% 100%;
}

.site-footer.site-footer--zellige::after {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    opacity: 0.08;
    background:
        radial-gradient(circle at 50% 120%, rgba(0, 0, 0, 0.28), transparent 58%),
        repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.04) 0 2px, transparent 2px 24px);
}

.site-footer.site-footer--zellige .site-footer__inner,
.site-footer.site-footer--zellige .site-footer__bottom,
.site-footer.site-footer--zellige .footer-grid {
    position: relative;
    z-index: 1;
}

.site-footer.site-footer--zellige .site-footer__slogan {
    color: rgba(255, 255, 255, 0.92);
}

.site-footer.site-footer--zellige .site-footer__label {
    color: #f3d792;
    font-weight: 800;
}

.site-footer.site-footer--zellige .site-footer__links a,
.site-footer.site-footer--zellige .site-footer__newsletter-note {
    color: rgba(255, 255, 255, 0.9);
}

.site-footer.site-footer--zellige .footer-social__link,
.site-footer.site-footer--zellige .site-footer__social-link {
    width: 2rem;
    height: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.28);
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    font-size: 0.6rem;
    font-weight: 800;
}

.site-footer.site-footer--zellige .footer-social__link span,
.site-footer.site-footer--zellige .site-footer__social-link span {
    letter-spacing: 0.02em;
}

.site-footer.site-footer--zellige .footer-newsletter-form input,
.site-footer.site-footer--zellige .site-footer__newsletter-form input {
    background: rgba(255, 255, 255, 0.14);
    border-color: rgba(255, 255, 255, 0.24);
    color: #fff;
}

.site-footer.site-footer--zellige .footer-newsletter-form input::placeholder,
.site-footer.site-footer--zellige .site-footer__newsletter-form input::placeholder {
    color: rgba(255, 255, 255, 0.62);
}

.site-footer.site-footer--zellige .site-footer__newsletter-btn {
    background: linear-gradient(135deg, #e0bb63, #c9a44c 45%, #9a7424);
    color: #2b040c;
    border-color: rgba(255, 236, 196, 0.55);
}

.site-footer.site-footer--zellige .site-footer__bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.16);
    color: rgba(255, 255, 255, 0.82);
}

@media (max-width: 640px) {
    .page-home .home-final-scroll-btn,
    .page-home .home-final-city-scroll-btn {
        width: 1.55rem;
        height: 1.55rem;
        font-size: 0.92rem;
    }

    .page-home .home-final-quick-track .home-final-quick-card--compact {
        min-width: 6.75rem;
    }
}

/* DESIGN HELL PHASE 7.2 — Flags size/crop and partners grid visual microfix */

.page-inner .flag-ref,
.page-home .flag-ref,
.page-inner .flag-ref-wrap img,
.page-home .flag-ref-wrap img,
.page-inner img.team-flag,
.page-home img.team-flag,
.page-inner .standings-ref-flag img,
.page-inner .match-ref-flag img,
.page-inner .knockout-ref-flag img {
    display: block;
    width: 100%;
    height: 100%;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain !important;
    object-position: center;
    border-radius: 0 !important;
}

.page-inner .flag-ref-wrap,
.page-home .flag-ref-wrap {
    display: inline-grid;
    place-items: center;
    flex: 0 0 auto;
    overflow: visible;
    border-radius: 6px;
    background: #fff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-sizing: border-box;
    aspect-ratio: auto !important;
}

.page-inner .flag-ref-wrap--small,
.page-home .flag-ref-wrap--small {
    width: 36px;
    height: 26px;
    min-width: 36px;
    min-height: 26px;
    max-width: 36px;
    max-height: 26px;
}

.page-inner .flag-ref-wrap--medium,
.page-home .flag-ref-wrap--medium {
    width: 48px;
    height: 34px;
    min-width: 48px;
    min-height: 34px;
    max-width: 48px;
    max-height: 34px;
}

.page-inner .flag-ref-wrap--card,
.page-home .flag-ref-wrap--card {
    width: 48px;
    height: 34px;
    min-width: 48px;
    min-height: 34px;
    max-width: 48px;
    max-height: 34px;
}

.page-inner .flag-ref-wrap--large,
.page-home .flag-ref-wrap--large {
    width: clamp(4rem, 10vw, 5.5rem);
    height: clamp(2.85rem, 7vw, 3.85rem);
    min-width: 0;
    min-height: 0;
    max-width: 100%;
    max-height: 100%;
}

.page-inner .standings-ref-flag,
.page-inner .standings-ref-flag .flag-ref-wrap,
.page-inner .standings-ref-flag .flag-ref-wrap--small {
    width: 36px;
    height: 26px;
    min-width: 36px;
    min-height: 26px;
    max-width: 36px;
    max-height: 26px;
    overflow: visible;
    border-radius: 6px;
}

.page-inner .standings-ref-table td.standings-table__team {
    padding-top: 0.4rem;
    padding-bottom: 0.4rem;
    vertical-align: middle;
}

.page-inner .match-ref-flag,
.page-home .match-ref-flag {
    display: inline-grid;
    place-items: center;
    width: auto;
    height: auto;
    margin: 0 auto 0.15rem;
    border: 0;
    border-radius: 0 !important;
    background: transparent;
    overflow: visible !important;
}

.page-inner .match-ref-flag .flag-ref-wrap--card,
.page-home .match-ref-flag .flag-ref-wrap--card {
    width: 48px;
    height: 34px;
    min-width: 48px;
    min-height: 34px;
    max-width: 48px;
    max-height: 34px;
}

.page-inner .knockout-ref-flag,
.page-inner .knockout-ref-flag .flag-ref-wrap,
.page-inner .knockout-ref-flag .flag-ref-wrap--small {
    width: 30px;
    height: 22px;
    min-width: 30px;
    min-height: 22px;
    max-width: 30px;
    max-height: 22px;
    overflow: visible;
    border-radius: 6px;
}

.page-inner .player-ref-team-flag,
.page-inner .player-ref-team-flag .flag-ref-wrap,
.page-home .player-ref-team-flag,
.page-home .player-ref-team-flag .flag-ref-wrap {
    width: 28px;
    height: 20px;
    min-width: 28px;
    min-height: 20px;
    max-width: 28px;
    max-height: 20px;
    overflow: visible;
    border-radius: 6px;
}

.page-inner .entity-ref-card__media--flag.team-ref-flag,
.page-inner a.entity-ref-card__media--flag.team-ref-flag,
.page-inner .entity-ref-hero__media--flag.team-ref-flag--hero,
.page-inner .team-ref-flag--hero {
    overflow: visible;
    border-radius: 10px;
}

.page-inner .entity-ref-card__media--flag.team-ref-flag,
.page-inner a.entity-ref-card__media--flag.team-ref-flag {
    aspect-ratio: 4 / 3;
    max-height: 4.5rem;
    padding: 0.35rem;
}

.page-inner .entity-ref-hero__media--flag.team-ref-flag--hero,
.page-inner .team-ref-flag--hero {
    aspect-ratio: 4 / 3;
    max-width: clamp(4.5rem, 12vw, 6.5rem);
    max-height: clamp(3.25rem, 8vw, 4.75rem);
    padding: 0.35rem;
}

.page-home .home-final-next-match__team > .flag-ref-wrap,
.page-home .home-final-next-match__team .flag-ref-wrap--medium {
    width: 48px !important;
    height: 34px !important;
    min-width: 48px !important;
    min-height: 34px !important;
    max-width: 48px !important;
    max-height: 34px !important;
    aspect-ratio: auto !important;
    overflow: visible !important;
}

.page-home .home-final-next-match__team .team-ref-flag {
    aspect-ratio: auto !important;
    max-height: none !important;
    padding: 0;
}

.page-home .home-final-standings .standings-ref-flag,
.page-home .home-final-standings .standings-ref-flag .flag-ref-wrap,
.page-home .home-final-standings .standings-ref-flag .flag-ref-wrap--small,
.page-home .home-final-standings .standings-ref-table--compact-flags img.team-flag,
.page-home .home-final-standings .standings-ref-table--compact-flags img.media-bound-image,
.page-home .home-final-standings .standings-ref-table--compact-flags img.flag-ref {
    width: 34px !important;
    height: 24px !important;
    min-width: 34px !important;
    min-height: 24px !important;
    max-width: 34px !important;
    max-height: 24px !important;
    object-fit: contain !important;
    border-radius: 6px !important;
    overflow: visible !important;
    aspect-ratio: auto !important;
}

.page-home .home-final-fixtures .match-ref-flag {
    width: auto !important;
    height: auto !important;
    min-width: 0 !important;
    min-height: 0 !important;
    max-width: none !important;
    max-height: none !important;
    overflow: visible !important;
    border-radius: 0 !important;
}

.page-home .home-final-fixtures .match-ref-flag .flag-ref-wrap,
.page-home .home-final-fixtures .match-ref-flag .flag-ref-wrap--card {
    width: 42px !important;
    height: 30px !important;
    min-width: 42px !important;
    min-height: 30px !important;
    max-width: 42px !important;
    max-height: 30px !important;
    overflow: visible !important;
    border-radius: 6px !important;
}

.page-home .home-final-fixtures .match-ref-flag img {
    width: 100% !important;
    height: 100% !important;
    min-width: 0 !important;
    min-height: 0 !important;
    max-width: 100% !important;
    max-height: 100% !important;
    object-fit: contain !important;
    border-radius: 0 !important;
}

.page-home .home-final-bracket .match-ref-flag {
    width: auto !important;
    height: auto !important;
    overflow: visible !important;
    border-radius: 0 !important;
}

.page-home .home-final-bracket .match-ref-flag .flag-ref-wrap,
.page-home .home-final-bracket .match-ref-flag .flag-ref-wrap--card {
    width: 28px !important;
    height: 20px !important;
    min-width: 28px !important;
    min-height: 20px !important;
    max-width: 28px !important;
    max-height: 20px !important;
    border-radius: 6px !important;
    overflow: visible !important;
}

.page-home .home-final-bracket .match-ref-flag img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    border-radius: 0 !important;
}

.page-home .fifa-partners-section--home.fifa-partners-section--strip {
    margin-top: 0.45rem;
    margin-bottom: 0.3rem;
    padding: 0.5rem 0.65rem 0.6rem;
}

.page-home .fifa-partners-section--home .fifa-partners-section__head {
    margin-bottom: 0.42rem;
}

.page-home .fifa-partners-grid--home.fifa-partners-grid--strip {
    display: grid !important;
    grid-template-columns: repeat(9, minmax(0, 1fr)) !important;
    flex-wrap: unset !important;
    gap: 0.32rem;
    width: 100% !important;
    min-width: 0;
    max-width: 100%;
}

.page-home .fifa-partner-card--home.fifa-partner-card--strip {
    flex: unset !important;
    width: auto !important;
    min-width: 0 !important;
    max-width: none !important;
    min-height: 0;
    max-height: 82px;
    grid-template-rows: auto minmax(30px, 1fr);
    gap: 0.16rem;
    padding: 0.28rem 0.22rem 0.32rem;
}

.page-home .fifa-partner-card--strip .fifa-partner-label {
    padding: 0.08rem 0.28rem;
    font-size: 0.46rem;
    line-height: 1.15;
}

.page-home .fifa-partner-card--strip .fifa-partner-logo {
    min-height: 0;
    max-height: 38px;
    padding: 0.08rem 0.16rem;
}

.page-home .fifa-partner-card--strip .fifa-partner-logo img {
    max-height: 32px;
    max-width: 92%;
    object-fit: contain;
}

.page-home .fifa-partner-card--strip .fifa-partner-text-logo {
    min-height: 1.65rem;
    max-height: 1.85rem;
    padding: 0.14rem 0.24rem;
    font-size: 0.54rem;
    line-height: 1.12;
}

.page-inner .fifa-partners-section--page.fifa-partners-section--strip,
.page-inner .fifa-partners-section.fifa-partners-section--page {
    margin-top: 0.25rem;
    padding: 0.35rem 0 0.55rem;
}

.page-inner .fifa-partners-section--page .fifa-partners-section__head {
    margin-bottom: 0.45rem;
}

.page-inner .fifa-partners-grid--page.fifa-partners-grid--strip,
.page-inner .fifa-partners-grid--page {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 0.48rem;
    width: 100%;
    min-width: 0;
}

.page-inner .fifa-partner-card--page.fifa-partner-card--strip,
.page-inner .fifa-partner-card--page {
    min-height: 0;
    max-height: 118px;
    grid-template-rows: auto minmax(44px, 1fr);
    gap: 0.22rem;
    padding: 0.42rem 0.36rem 0.46rem;
}

.page-inner .fifa-partner-card--page .fifa-partner-logo {
    min-height: 0;
    max-height: 52px;
    padding: 0.12rem 0.2rem;
}

.page-inner .fifa-partner-card--page .fifa-partner-logo img {
    max-height: 44px;
    max-width: 92%;
    object-fit: contain;
}

.page-inner .fifa-partner-card--page .fifa-partner-text-logo {
    min-height: 1.85rem;
    max-height: 2.05rem;
    padding: 0.16rem 0.28rem;
    font-size: 0.58rem;
    line-height: 1.12;
}

@media (min-width: 1025px) {
    .page-home .fifa-partners-grid--home.fifa-partners-grid--strip {
        display: grid !important;
        grid-template-columns: repeat(9, minmax(0, 1fr)) !important;
        width: 100% !important;
    }

    .page-home .fifa-partner-card--home.fifa-partner-card--strip {
        flex: unset !important;
        width: auto !important;
        max-width: none !important;
    }
}

@media (max-width: 1024px) {
    .page-home .fifa-partners-grid--home.fifa-partners-grid--strip {
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    }

    .page-inner .fifa-partners-grid--page.fifa-partners-grid--strip,
    .page-inner .fifa-partners-grid--page {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 768px) {
    .page-inner .flag-ref-wrap--card,
    .page-home .flag-ref-wrap--card,
    .page-inner .match-ref-flag .flag-ref-wrap--card,
    .page-home .match-ref-flag .flag-ref-wrap--card {
        width: 42px;
        height: 30px;
        min-width: 42px;
        min-height: 30px;
        max-width: 42px;
        max-height: 30px;
    }

    .page-home .fifa-partners-grid--home.fifa-partners-grid--strip {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .page-inner .fifa-partners-grid--page.fifa-partners-grid--strip,
    .page-inner .fifa-partners-grid--page {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .page-inner .standings-ref-flag,
    .page-inner .standings-ref-flag .flag-ref-wrap {
        width: 32px;
        height: 22px;
        min-width: 32px;
        min-height: 22px;
        max-width: 32px;
        max-height: 22px;
    }
}

/* DESIGN HELL PHASE 7.3 — Premium Moroccan zellij footer final redesign */

.site-footer.site-footer--premium {
    position: relative;
    overflow: hidden;
    margin-top: clamp(1.15rem, 2vw, 1.65rem);
    padding: 0;
    color: #fff;
    background:
        radial-gradient(ellipse 90% 42% at 50% -8%, rgba(214, 183, 90, 0.18), transparent 58%),
        radial-gradient(circle at 6% 88%, rgba(46, 120, 72, 0.11), transparent 34%),
        radial-gradient(circle at 94% 18%, rgba(255, 255, 255, 0.07), transparent 30%),
        linear-gradient(180deg, #b11226 0%, #a30d1f 36%, #961520 66%, #8f0718 100%);
    border-top: none;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.07),
        0 -8px 28px rgba(143, 7, 24, 0.12);
}

.site-footer.site-footer--premium::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    opacity: 0.17;
    background-image:
        none,
        repeating-linear-gradient(45deg, rgba(214, 183, 90, 0.05) 0 1px, transparent 1px 18px),
        repeating-linear-gradient(-45deg, rgba(255, 255, 255, 0.04) 0 1px, transparent 1px 18px);
    background-size: 196px auto, auto, auto;
    background-repeat: repeat, repeat, repeat;
    background-position: center top;
    mix-blend-mode: soft-light;
}

.site-footer.site-footer--premium::after {
    content: "";
    position: absolute;
    inset: 0 auto auto 0;
    width: 100%;
    height: 3px;
    z-index: 2;
    pointer-events: none;
    background: linear-gradient(90deg, transparent 0%, #a8862e 8%, #c9a646 24%, #d6b75a 50%, #c9a646 76%, #a8862e 92%, transparent 100%);
    box-shadow: 0 2px 14px rgba(201, 166, 70, 0.38);
}

.site-footer.site-footer--premium .site-footer__pattern.footer-zellij {
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    opacity: 0.14;
    background-image: none;
    background-size: 168px auto;
    background-repeat: repeat;
    background-position: center center;
}

.site-footer.site-footer--premium .site-footer__glow {
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    background:
        radial-gradient(circle at 14% 10%, rgba(214, 183, 90, 0.16), transparent 42%),
        radial-gradient(circle at 86% 92%, rgba(0, 0, 0, 0.24), transparent 52%),
        linear-gradient(180deg, rgba(255, 255, 255, 0.04) 0%, transparent 18%, transparent 82%, rgba(0, 0, 0, 0.12) 100%);
}

.site-footer.site-footer--premium .site-footer__inner,
.site-footer.site-footer--premium .site-footer__bottom,
.site-footer.site-footer--premium .footer-grid {
    position: relative;
    z-index: 1;
}

.site-footer.site-footer--premium .site-footer__inner {
    display: grid;
    grid-template-columns: minmax(100px, 1.15fr) repeat(4, minmax(72px, 0.82fr)) minmax(148px, 1.38fr);
    gap: clamp(0.55rem, 1.1vw, 0.9rem);
    align-items: start;
    width: min(calc(100% - 32px), var(--m2030-container));
    margin-inline: auto;
    padding: clamp(1.65rem, 2.6vw, 2.75rem) 0 clamp(1rem, 1.5vw, 1.75rem);
}

.site-footer.site-footer--premium .footer-brand-card {
    max-width: 11.75rem;
}

.site-footer.site-footer--premium .footer-brand,
.site-footer.site-footer--premium .site-footer__brand {
    display: grid;
    gap: 0.45rem;
}

.site-footer.site-footer--premium .footer-logo,
.site-footer.site-footer--premium .site-footer__logo {
    display: block;
    width: auto;
    max-width: 106px;
    max-height: 4.35rem;
    object-fit: contain;
    object-position: left center;
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.18));
}

.site-footer.site-footer--premium .footer-slogan,
.site-footer.site-footer--premium .site-footer__slogan {
    display: grid;
    gap: 0.1rem;
    margin: 0;
    color: rgba(255, 248, 236, 0.94);
    font-size: clamp(0.56rem, 0.72vw, 0.62rem);
    font-weight: 700;
    letter-spacing: 0.14em;
    line-height: 1.38;
    text-transform: uppercase;
}

.site-footer.site-footer--premium .footer-slogan span,
.site-footer.site-footer--premium .site-footer__slogan span {
    display: block;
}

.site-footer.site-footer--premium .footer-column {
    display: grid;
    gap: 0.24rem;
    align-content: start;
}

.site-footer.site-footer--premium .footer-column h3,
.site-footer.site-footer--premium .site-footer__label {
    margin: 0 0 0.2rem;
    color: #d6b75a;
    font-size: clamp(0.54rem, 0.68vw, 0.6rem);
    font-weight: 800;
    letter-spacing: 0.16em;
    line-height: 1.2;
    text-transform: uppercase;
}

.site-footer.site-footer--premium .footer-column a,
.site-footer.site-footer--premium .site-footer__links a {
    color: rgba(255, 250, 242, 0.9);
    font-size: clamp(0.7rem, 0.82vw, 0.76rem);
    font-weight: 600;
    line-height: 1.35;
    text-decoration: none;
    transition: color 0.16s ease;
}

.site-footer.site-footer--premium .footer-column a:hover,
.site-footer.site-footer--premium .footer-column a:focus-visible,
.site-footer.site-footer--premium .site-footer__links a:hover,
.site-footer.site-footer--premium .site-footer__links a:focus-visible {
    color: #f3d792;
    outline: none;
    text-decoration: underline;
    text-decoration-color: rgba(214, 183, 90, 0.55);
    text-underline-offset: 0.14em;
}

.site-footer.site-footer--premium .footer-aside {
    display: grid;
    gap: 0.62rem;
    align-content: start;
}

.site-footer.site-footer--premium .footer-social {
    display: grid;
    gap: 0.18rem;
}

.site-footer.site-footer--premium .footer-social__list,
.site-footer.site-footer--premium .site-footer__social-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.34rem;
    margin: 0;
    padding: 0;
}

.site-footer.site-footer--premium .footer-social a,
.site-footer.site-footer--premium .footer-social__link,
.site-footer.site-footer--premium .site-footer__social-link {
    display: inline-grid;
    place-items: center;
    width: 2rem;
    height: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.28);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
    color: #fff;
    font-size: 0.58rem;
    font-weight: 800;
    letter-spacing: 0.02em;
    text-decoration: none;
    transition:
        border-color 0.16s ease,
        background 0.16s ease,
        color 0.16s ease,
        box-shadow 0.16s ease,
        transform 0.16s ease;
}

.site-footer.site-footer--premium .footer-social a:hover,
.site-footer.site-footer--premium .footer-social a:focus-visible,
.site-footer.site-footer--premium .footer-social__link:hover,
.site-footer.site-footer--premium .footer-social__link:focus-visible,
.site-footer.site-footer--premium .site-footer__social-link:hover,
.site-footer.site-footer--premium .site-footer__social-link:focus-visible {
    border-color: rgba(214, 183, 90, 0.72);
    background: rgba(201, 166, 70, 0.2);
    color: #ffe8ad;
    box-shadow: 0 0 0 1px rgba(214, 183, 90, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.18);
    transform: translateY(-1px);
}

.site-footer.site-footer--premium .footer-newsletter,
.site-footer.site-footer--premium .footer-newsletter-card {
    display: grid;
    gap: 0.16rem;
    padding: 0.55rem 0.58rem 0.62rem;
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 12px;
    background: rgba(0, 0, 0, 0.12);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
}

.site-footer.site-footer--premium .footer-newsletter .site-footer__newsletter-note {
    margin: 0 0 0.38rem;
    color: rgba(255, 248, 236, 0.88);
    font-size: clamp(0.66rem, 0.78vw, 0.72rem);
    line-height: 1.35;
}

.site-footer.site-footer--premium .footer-newsletter-form,
.site-footer.site-footer--premium .site-footer__newsletter-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 0.32rem;
    align-items: center;
}

.site-footer.site-footer--premium .footer-newsletter input,
.site-footer.site-footer--premium .footer-newsletter-form input,
.site-footer.site-footer--premium .site-footer__newsletter-form input {
    min-height: 2rem;
    width: 100%;
    padding: 0.38rem 0.65rem;
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    font-size: 0.72rem;
    line-height: 1.2;
}

.site-footer.site-footer--premium .footer-newsletter input::placeholder,
.site-footer.site-footer--premium .footer-newsletter-form input::placeholder,
.site-footer.site-footer--premium .site-footer__newsletter-form input::placeholder {
    color: rgba(255, 255, 255, 0.58);
}

.site-footer.site-footer--premium .footer-newsletter input:disabled,
.site-footer.site-footer--premium .footer-newsletter-form input:disabled,
.site-footer.site-footer--premium .site-footer__newsletter-form input:disabled {
    opacity: 0.92;
    cursor: not-allowed;
}

.site-footer.site-footer--premium .footer-newsletter button,
.site-footer.site-footer--premium .site-footer__newsletter-btn {
    min-height: 2rem;
    padding: 0.38rem 0.82rem;
    border: 1px solid rgba(255, 236, 196, 0.55);
    border-radius: 999px;
    background: linear-gradient(135deg, #e0bb63 0%, #d6b75a 38%, #c9a646 72%, #a8862e 100%);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.16);
    color: #2b040c;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.07em;
    line-height: 1.1;
    text-transform: uppercase;
    white-space: nowrap;
}

.site-footer.site-footer--premium .footer-bottom,
.site-footer.site-footer--premium .site-footer__bottom {
    width: min(calc(100% - 32px), var(--m2030-container));
    margin-inline: auto;
    padding: 0.65rem 0 clamp(0.85rem, 1.2vw, 1.75rem);
    border-top: 1px solid rgba(255, 255, 255, 0.14);
}

.site-footer.site-footer--premium .footer-bottom p,
.site-footer.site-footer--premium .site-footer__bottom p {
    margin: 0;
    color: rgba(255, 248, 236, 0.78);
    font-size: clamp(0.62rem, 0.74vw, 0.68rem);
    line-height: 1.35;
    text-align: center;
}

@media (max-width: 900px) {
    .site-footer.site-footer--premium .site-footer__inner {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem 0.75rem;
        padding-top: clamp(1.5rem, 4vw, 2.25rem);
        padding-bottom: clamp(0.95rem, 2.5vw, 1.5rem);
    }

    .site-footer.site-footer--premium .footer-brand-card,
    .site-footer.site-footer--premium .site-footer__brand,
    .site-footer.site-footer--premium .footer-aside,
    .site-footer.site-footer--premium .site-footer__aside {
        grid-column: 1 / -1;
    }

    .site-footer.site-footer--premium .footer-logo,
    .site-footer.site-footer--premium .site-footer__logo {
        max-width: 98px;
    }

    .site-footer.site-footer--premium .footer-aside {
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr);
        gap: 0.75rem;
        align-items: start;
    }
}

@media (max-width: 640px) {
    .site-footer.site-footer--premium .site-footer__inner {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    .site-footer.site-footer--premium .footer-brand-card,
    .site-footer.site-footer--premium .site-footer__brand {
        justify-items: start;
    }

    .site-footer.site-footer--premium .footer-aside {
        grid-template-columns: 1fr;
    }

    .site-footer.site-footer--premium .footer-newsletter-form,
    .site-footer.site-footer--premium .site-footer__newsletter-form {
        grid-template-columns: 1fr;
    }

    .site-footer.site-footer--premium .footer-newsletter button,
    .site-footer.site-footer--premium .site-footer__newsletter-btn {
        width: 100%;
    }

    .site-footer.site-footer--premium::before {
        opacity: 0.15;
        background-size: 148px auto, auto, auto;
    }

    .site-footer.site-footer--premium .site-footer__pattern.footer-zellij {
        opacity: 0.12;
        background-size: 140px auto;
    }
}

/* DESIGN HELL PHASE 7.4 — Footer breathing space and premium structure */

.site-footer.site-footer--structured {
    margin-top: clamp(1.15rem, 2vw, 1.65rem);
    color: #fff8f0;
    background:
        radial-gradient(ellipse 88% 38% at 50% -6%, rgba(214, 183, 90, 0.12), transparent 56%),
        radial-gradient(circle at 8% 92%, rgba(46, 120, 72, 0.07), transparent 32%),
        linear-gradient(180deg, #b11226 0%, #971022 48%, #7f0b1a 100%);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.06),
        0 -6px 22px rgba(127, 11, 26, 0.1);
}

.site-footer.site-footer--structured::before {
    opacity: 0.11;
    background-image:
        none,
        repeating-linear-gradient(45deg, rgba(214, 183, 90, 0.035) 0 1px, transparent 1px 22px);
    background-size: 220px auto, auto;
    background-repeat: repeat, repeat;
    background-position: center top;
    mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.55) 0%, rgba(0, 0, 0, 0.22) 42%, rgba(0, 0, 0, 0.38) 100%);
    -webkit-mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.55) 0%, rgba(0, 0, 0, 0.22) 42%, rgba(0, 0, 0, 0.38) 100%);
}

.site-footer.site-footer--structured::after {
    height: 2px;
    background: linear-gradient(90deg, transparent 0%, #c9a646 18%, #d6b75a 50%, #c9a646 82%, transparent 100%);
    box-shadow: 0 1px 10px rgba(201, 166, 70, 0.28);
}

.site-footer.site-footer--structured .site-footer__pattern.footer-zellij {
    opacity: 0.08;
    background-size: 200px auto;
    mask-image: radial-gradient(ellipse 85% 70% at 50% 50%, transparent 18%, rgba(0, 0, 0, 0.85) 100%);
    -webkit-mask-image: radial-gradient(ellipse 85% 70% at 50% 50%, transparent 18%, rgba(0, 0, 0, 0.85) 100%);
}

.site-footer.site-footer--structured .site-footer__glow {
    background:
        radial-gradient(circle at 12% 8%, rgba(214, 183, 90, 0.1), transparent 40%),
        radial-gradient(circle at 88% 94%, rgba(0, 0, 0, 0.18), transparent 50%),
        linear-gradient(180deg, rgba(255, 255, 255, 0.03) 0%, transparent 24%, transparent 100%);
}

.site-footer.site-footer--structured .site-footer__inner {
    display: flex;
    flex-direction: column;
    gap: clamp(1.35rem, 2.4vw, 2rem);
    width: min(calc(100% - 32px), var(--m2030-container));
    margin-inline: auto;
    padding: clamp(1.75rem, 2.8vw, 2.75rem) 0 clamp(0.85rem, 1.4vw, 1.15rem);
}

.site-footer.site-footer--structured .site-footer__top {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(240px, 0.92fr);
    align-items: start;
    gap: clamp(1.75rem, 3vw, 2.75rem);
}

.site-footer.site-footer--structured .site-footer__brand-panel {
    display: grid;
    gap: 0.65rem;
    max-width: 15rem;
}

.site-footer.site-footer--structured .site-footer__brand-panel .footer-logo,
.site-footer.site-footer--structured .site-footer__brand-panel .site-footer__logo {
    max-width: 100px;
    max-height: 4rem;
}

.site-footer.site-footer--structured .site-footer__brand-panel .footer-slogan,
.site-footer.site-footer--structured .site-footer__brand-panel .site-footer__slogan {
    margin: 0;
    color: rgba(255, 244, 228, 0.92);
    font-size: clamp(0.58rem, 0.74vw, 0.64rem);
    letter-spacing: 0.13em;
    line-height: 1.45;
}

.site-footer.site-footer--structured .site-footer__newsletter-panel {
    display: grid;
    gap: clamp(0.85rem, 1.4vw, 1.15rem);
    justify-self: end;
    width: min(100%, 22rem);
}

.site-footer.site-footer--structured .footer-action-row {
    display: grid;
    gap: clamp(0.85rem, 1.4vw, 1.15rem);
}

.site-footer.site-footer--structured .footer-social {
    display: grid;
    gap: 0.45rem;
}

.site-footer.site-footer--structured .footer-social .site-footer__label {
    margin: 0;
}

.site-footer.site-footer--structured .footer-social__list,
.site-footer.site-footer--structured .site-footer__social-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.42rem;
    margin: 0;
    padding: 0;
}

.site-footer.site-footer--structured .footer-social a,
.site-footer.site-footer--structured .footer-social__link,
.site-footer.site-footer--structured .site-footer__social-link {
    width: 2.05rem;
    height: 2.05rem;
    border-color: rgba(255, 248, 236, 0.22);
    background: rgba(255, 255, 255, 0.08);
    color: #fff8f0;
}

.site-footer.site-footer--structured .footer-newsletter,
.site-footer.site-footer--structured .footer-newsletter-card {
    gap: 0.28rem;
    padding: 0.72rem 0.78rem 0.78rem;
    border-color: rgba(214, 183, 90, 0.22);
    background: rgba(0, 0, 0, 0.14);
}

.site-footer.site-footer--structured .footer-newsletter .site-footer__newsletter-note {
    margin: 0 0 0.55rem;
    color: rgba(255, 244, 228, 0.86);
    font-size: 0.74rem;
    line-height: 1.4;
}

.site-footer.site-footer--structured .site-footer__nav {
    padding-top: clamp(1rem, 1.8vw, 1.35rem);
    border-top: 1px solid rgba(255, 248, 236, 0.12);
}

.site-footer.site-footer--structured .footer-links-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: clamp(1.25rem, 2.2vw, 2rem);
}

.site-footer.site-footer--structured .footer-column {
    display: grid;
    gap: 0.42rem;
    align-content: start;
}

.site-footer.site-footer--structured .footer-column h3,
.site-footer.site-footer--structured .footer-column .site-footer__label {
    margin: 0 0 0.35rem;
    color: #d6b75a;
    font-size: 0.6rem;
    letter-spacing: 0.15em;
}

.site-footer.site-footer--structured .footer-column a,
.site-footer.site-footer--structured .site-footer__links a {
    color: rgba(255, 244, 228, 0.88);
    font-size: 0.78rem;
    font-weight: 600;
    line-height: 1.5;
}

.site-footer.site-footer--structured .footer-column a:hover,
.site-footer.site-footer--structured .footer-column a:focus-visible,
.site-footer.site-footer--structured .site-footer__links a:hover,
.site-footer.site-footer--structured .site-footer__links a:focus-visible {
    color: #f0d48a;
}

.site-footer.site-footer--structured .footer-newsletter input,
.site-footer.site-footer--structured .footer-newsletter-form input,
.site-footer.site-footer--structured .site-footer__newsletter-form input {
    min-height: 2.15rem;
    padding: 0.42rem 0.72rem;
    background: rgba(255, 255, 255, 0.1);
    color: #fff8f0;
}

.site-footer.site-footer--structured .footer-newsletter button,
.site-footer.site-footer--structured .site-footer__newsletter-btn {
    min-height: 2.15rem;
    padding: 0.42rem 0.9rem;
    background: linear-gradient(135deg, #e4c06a 0%, #d6b75a 42%, #c9a646 100%);
    color: #2b040c;
}

.site-footer.site-footer--structured .site-footer__bottom,
.site-footer.site-footer--structured .footer-bottom {
    width: min(calc(100% - 32px), var(--m2030-container));
    margin-inline: auto;
    padding: 0.75rem 0 clamp(0.9rem, 1.3vw, 1.35rem);
    border-top: 1px solid rgba(255, 248, 236, 0.1);
}

.site-footer.site-footer--structured .site-footer__bottom p,
.site-footer.site-footer--structured .footer-bottom p {
    color: rgba(255, 244, 228, 0.72);
    font-size: 0.66rem;
    line-height: 1.4;
}

@media (max-width: 900px) {
    .site-footer.site-footer--structured .site-footer__top {
        grid-template-columns: 1fr;
        gap: clamp(1.35rem, 3vw, 1.85rem);
    }

    .site-footer.site-footer--structured .site-footer__newsletter-panel {
        justify-self: stretch;
        width: 100%;
        max-width: none;
    }

    .site-footer.site-footer--structured .footer-links-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: clamp(1rem, 2.5vw, 1.5rem);
    }
}

@media (max-width: 640px) {
    .site-footer.site-footer--structured .site-footer__inner {
        gap: 1.15rem;
        padding-top: clamp(1.5rem, 4vw, 2rem);
    }

    .site-footer.site-footer--structured .footer-links-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .site-footer.site-footer--structured .footer-newsletter-form,
    .site-footer.site-footer--structured .site-footer__newsletter-form {
        grid-template-columns: 1fr;
    }

    .site-footer.site-footer--structured .footer-newsletter button,
    .site-footer.site-footer--structured .site-footer__newsletter-btn {
        width: 100%;
    }

    .site-footer.site-footer--structured::before {
        opacity: 0.09;
        background-size: 180px auto, auto;
    }

    .site-footer.site-footer--structured .site-footer__pattern.footer-zellij {
        opacity: 0.07;
    }
}

/* DESIGN HELL PHASE 7.5 — Footer exact premium reference match */

.site-footer.site-footer--reference {
    position: relative;
    overflow: hidden;
    margin-top: clamp(1.25rem, 2vw, 1.75rem);
    padding: 0;
    color: #fff8f0;
    background:
        radial-gradient(ellipse 120% 80% at 50% -20%, rgba(163, 13, 31, 0.55), transparent 55%),
        radial-gradient(circle at 0% 50%, rgba(0, 0, 0, 0.35), transparent 42%),
        radial-gradient(circle at 100% 50%, rgba(0, 0, 0, 0.35), transparent 42%),
        linear-gradient(180deg, #a30d1f 0%, #8f0718 42%, #5c0310 100%);
    border-top: none;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
}

.site-footer.site-footer--reference::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    opacity: 0.14;
    background-image: none;
    background-size: 210px auto;
    background-repeat: repeat;
    background-position: center top;
    mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.35) 45%, rgba(0, 0, 0, 0.55) 100%);
    -webkit-mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.35) 45%, rgba(0, 0, 0, 0.55) 100%);
}

.site-footer.site-footer--reference::after {
    content: "";
    position: absolute;
    inset: 0 auto auto 0;
    width: 100%;
    height: 2px;
    z-index: 2;
    pointer-events: none;
    background: linear-gradient(90deg, transparent 0%, #a8862e 12%, #d6b75a 50%, #a8862e 88%, transparent 100%);
    box-shadow: 0 1px 8px rgba(201, 166, 70, 0.25);
}

.site-footer.site-footer--reference .site-footer__ornament-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    background:
        radial-gradient(circle at 14% 18%, rgba(214, 183, 90, 0.08), transparent 38%),
        radial-gradient(circle at 86% 22%, rgba(214, 183, 90, 0.06), transparent 36%),
        linear-gradient(90deg, rgba(0, 0, 0, 0.28) 0%, transparent 14%, transparent 86%, rgba(0, 0, 0, 0.28) 100%);
}

.site-footer.site-footer--reference .site-footer__inner {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 0;
    width: min(calc(100% - 40px), 1440px);
    margin-inline: auto;
    padding: clamp(1.85rem, 3vw, 2.65rem) 0 clamp(0.85rem, 1.4vw, 1.15rem);
}

.site-footer.site-footer--reference .site-footer__top-ref {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(280px, 420px) minmax(0, 1fr);
    align-items: center;
    gap: clamp(1.25rem, 2.5vw, 2.25rem);
}

.site-footer.site-footer--reference .footer-ref-brand {
    display: grid;
    gap: 0.55rem;
    justify-items: start;
    max-width: 18rem;
}

.site-footer.site-footer--reference .footer-ref-logo {
    display: block;
    width: auto;
    max-width: min(220px, 100%);
    height: auto;
    max-height: 3.35rem;
    object-fit: contain;
    object-position: left center;
    filter: drop-shadow(0 2px 10px rgba(0, 0, 0, 0.22));
}

.site-footer.site-footer--reference .footer-ref-slogan {
    display: grid;
    gap: 0.08rem;
    margin: 0;
    color: rgba(255, 244, 228, 0.92);
    font-size: clamp(0.58rem, 0.72vw, 0.66rem);
    font-weight: 700;
    letter-spacing: 0.14em;
    line-height: 1.42;
    text-transform: uppercase;
}

.site-footer.site-footer--reference .footer-ref-slogan span {
    display: block;
}

.site-footer.site-footer--reference .footer-ref-brand__line {
    width: min(100%, 11rem);
    height: 1px;
    margin-top: 0.15rem;
    background: linear-gradient(90deg, #c9a646 0%, #d6b75a 45%, rgba(214, 183, 90, 0.2) 100%);
    position: relative;
}

.site-footer.site-footer--reference .footer-ref-brand__line::after {
    content: "";
    position: absolute;
    right: 0;
    top: 50%;
    width: 6px;
    height: 6px;
    border: 1px solid #d6b75a;
    transform: translateY(-50%) rotate(45deg);
    background: rgba(92, 3, 16, 0.85);
}

.site-footer.site-footer--reference .footer-ref-newsletter-card {
    justify-self: center;
    width: min(100%, 26rem);
    padding: clamp(0.85rem, 1.4vw, 1.05rem) clamp(0.95rem, 1.6vw, 1.2rem);
    border: 1px solid rgba(214, 183, 90, 0.55);
    border-radius: 14px;
    background: rgba(47, 4, 12, 0.42);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.06),
        0 8px 24px rgba(0, 0, 0, 0.18);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

.site-footer.site-footer--reference .footer-ref-newsletter-card__title {
    margin: 0 0 0.2rem;
    color: #d6b75a;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.16em;
    line-height: 1.2;
    text-align: center;
    text-transform: uppercase;
}

.site-footer.site-footer--reference .footer-ref-newsletter-card__note {
    margin: 0 0 0.65rem;
    color: rgba(255, 244, 228, 0.88);
    font-size: 0.76rem;
    line-height: 1.35;
    text-align: center;
}

.site-footer.site-footer--reference .footer-ref-newsletter-card__form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 0.45rem;
    align-items: center;
}

.site-footer.site-footer--reference .footer-ref-newsletter-card__field {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    min-width: 0;
    min-height: 2.35rem;
    padding: 0 0.75rem;
    border: 1px solid rgba(255, 248, 236, 0.14);
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.28);
}

.site-footer.site-footer--reference .footer-ref-newsletter-card__icon {
    display: inline-flex;
    flex-shrink: 0;
    color: rgba(255, 244, 228, 0.72);
}

.site-footer.site-footer--reference .footer-ref-newsletter-card input {
    width: 100%;
    min-width: 0;
    padding: 0;
    border: 0;
    background: transparent;
    color: #fff8f0;
    font-size: 0.76rem;
    line-height: 1.2;
    outline: none;
}

.site-footer.site-footer--reference .footer-ref-newsletter-card input::placeholder {
    color: rgba(255, 244, 228, 0.52);
}

.site-footer.site-footer--reference .footer-ref-newsletter-card input:disabled {
    cursor: not-allowed;
    opacity: 0.95;
}

.site-footer.site-footer--reference .footer-ref-newsletter-card__btn {
    min-height: 2.35rem;
    padding: 0.45rem 1rem;
    border: 1px solid rgba(255, 236, 196, 0.45);
    border-radius: 999px;
    background: linear-gradient(180deg, #e4c06a 0%, #d6b75a 48%, #c9a646 100%);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    color: #2b040c;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    line-height: 1.1;
    text-transform: uppercase;
    white-space: nowrap;
}

.site-footer.site-footer--reference .footer-ref-follow {
    display: grid;
    gap: 0.55rem;
    justify-items: end;
    justify-self: end;
    width: 100%;
    max-width: 16rem;
}

.site-footer.site-footer--reference .footer-ref-follow__title {
    margin: 0;
    color: #d6b75a;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.16em;
    line-height: 1.2;
    text-align: right;
    text-transform: uppercase;
}

.site-footer.site-footer--reference .footer-ref-follow__line {
    width: min(100%, 9rem);
    height: 1px;
    justify-self: end;
    background: linear-gradient(270deg, #c9a646 0%, #d6b75a 45%, rgba(214, 183, 90, 0.15) 100%);
    position: relative;
}

.site-footer.site-footer--reference .footer-ref-follow__line::before {
    content: "";
    position: absolute;
    left: 0;
    top: 50%;
    width: 6px;
    height: 6px;
    border: 1px solid #d6b75a;
    transform: translateY(-50%) rotate(45deg);
    background: rgba(92, 3, 16, 0.85);
}

.site-footer.site-footer--reference .footer-ref-socials {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 0.5rem;
    margin: 0;
    padding: 0;
}

.site-footer.site-footer--reference .footer-ref-socials a,
.site-footer.site-footer--reference .footer-ref-socials__link {
    display: inline-grid;
    place-items: center;
    width: 2.35rem;
    height: 2.35rem;
    border: 1px solid rgba(214, 183, 90, 0.55);
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.18);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
    color: #fff8f0;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.02em;
    text-decoration: none;
    transition:
        border-color 0.16s ease,
        background 0.16s ease,
        color 0.16s ease,
        transform 0.16s ease;
}

.site-footer.site-footer--reference .footer-ref-socials a:hover,
.site-footer.site-footer--reference .footer-ref-socials a:focus-visible,
.site-footer.site-footer--reference .footer-ref-socials__link:hover,
.site-footer.site-footer--reference .footer-ref-socials__link:focus-visible {
    border-color: #d6b75a;
    background: rgba(201, 166, 70, 0.18);
    color: #f0d48a;
    transform: translateY(-1px);
    outline: none;
}

.site-footer.site-footer--reference .footer-ref-divider {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: clamp(1.35rem, 2.2vw, 1.85rem) 0 clamp(1.15rem, 1.8vw, 1.55rem);
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, rgba(214, 183, 90, 0.25) 8%, rgba(214, 183, 90, 0.65) 50%, rgba(214, 183, 90, 0.25) 92%, transparent 100%);
}

.site-footer.site-footer--reference .footer-ref-divider__ornament {
    position: absolute;
    width: 10px;
    height: 10px;
    border: 1px solid #d6b75a;
    transform: rotate(45deg);
    background:
        linear-gradient(135deg, rgba(214, 183, 90, 0.35), rgba(92, 3, 16, 0.9));
    box-shadow: 0 0 0 3px rgba(92, 3, 16, 0.95);
}

.site-footer.site-footer--reference .footer-ref-nav {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0;
    padding: 0 clamp(0.25rem, 1vw, 0.75rem);
}

.site-footer.site-footer--reference .footer-ref-column {
    display: grid;
    gap: 0.48rem;
    justify-items: center;
    padding: 0 clamp(0.65rem, 1.2vw, 1.15rem);
    border-right: 1px solid rgba(214, 183, 90, 0.22);
    text-align: center;
}

.site-footer.site-footer--reference .footer-ref-column:last-child {
    border-right: 0;
}

.site-footer.site-footer--reference .footer-ref-column h3 {
    margin: 0 0 0.35rem;
    color: #d6b75a;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.15em;
    line-height: 1.2;
    text-transform: uppercase;
}

.site-footer.site-footer--reference .footer-ref-column a {
    color: rgba(255, 244, 228, 0.88);
    font-size: 0.78rem;
    font-weight: 600;
    line-height: 1.45;
    text-decoration: none;
    transition: color 0.16s ease;
}

.site-footer.site-footer--reference .footer-ref-column a:hover,
.site-footer.site-footer--reference .footer-ref-column a:focus-visible {
    color: #f0d48a;
    outline: none;
    text-decoration: underline;
    text-decoration-color: rgba(214, 183, 90, 0.5);
    text-underline-offset: 0.12em;
}

.site-footer.site-footer--reference .footer-ref-bottom {
    margin-top: clamp(1.15rem, 1.8vw, 1.45rem);
    padding-top: clamp(0.85rem, 1.3vw, 1.05rem);
    border-top: 1px solid rgba(214, 183, 90, 0.28);
    text-align: center;
}

.site-footer.site-footer--reference .footer-ref-bottom p {
    margin: 0;
    color: rgba(255, 244, 228, 0.72);
    font-size: 0.66rem;
    line-height: 1.35;
}

@media (max-width: 1024px) {
    .site-footer.site-footer--reference .site-footer__top-ref {
        grid-template-columns: 1fr;
        gap: clamp(1.35rem, 3vw, 1.85rem);
    }

    .site-footer.site-footer--reference .footer-ref-brand {
        justify-items: center;
        max-width: none;
        text-align: center;
    }

    .site-footer.site-footer--reference .footer-ref-logo {
        object-position: center;
    }

    .site-footer.site-footer--reference .footer-ref-brand__line {
        margin-inline: auto;
        background: linear-gradient(90deg, rgba(214, 183, 90, 0.2) 0%, #d6b75a 50%, rgba(214, 183, 90, 0.2) 100%);
    }

    .site-footer.site-footer--reference .footer-ref-brand__line::after {
        display: none;
    }

    .site-footer.site-footer--reference .footer-ref-follow {
        justify-items: center;
        justify-self: center;
        max-width: none;
    }

    .site-footer.site-footer--reference .footer-ref-follow__title {
        text-align: center;
    }

    .site-footer.site-footer--reference .footer-ref-follow__line {
        justify-self: center;
        background: linear-gradient(90deg, rgba(214, 183, 90, 0.2) 0%, #d6b75a 50%, rgba(214, 183, 90, 0.2) 100%);
    }

    .site-footer.site-footer--reference .footer-ref-follow__line::before {
        display: none;
    }

    .site-footer.site-footer--reference .footer-ref-socials {
        justify-content: center;
    }

    .site-footer.site-footer--reference .footer-ref-nav {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: clamp(0.85rem, 2vw, 1.25rem) 0;
    }

    .site-footer.site-footer--reference .footer-ref-column {
        border-right: 0;
        border-bottom: 0;
        padding-bottom: 0.5rem;
    }

    .site-footer.site-footer--reference .footer-ref-column:nth-child(odd) {
        border-right: 1px solid rgba(214, 183, 90, 0.18);
    }
}

@media (max-width: 640px) {
    .site-footer.site-footer--reference .site-footer__inner {
        width: min(calc(100% - 24px), 1440px);
        padding-top: clamp(1.5rem, 4vw, 2rem);
    }

    .site-footer.site-footer--reference .footer-ref-newsletter-card__form {
        grid-template-columns: 1fr;
    }

    .site-footer.site-footer--reference .footer-ref-newsletter-card__btn {
        width: 100%;
    }

    .site-footer.site-footer--reference .footer-ref-nav {
        grid-template-columns: 1fr;
    }

    .site-footer.site-footer--reference .footer-ref-column,
    .site-footer.site-footer--reference .footer-ref-column:nth-child(odd) {
        border-right: 0;
        padding-bottom: 0.75rem;
    }

    .site-footer.site-footer--reference::before {
        opacity: 0.11;
        background-size: 170px auto;
    }
}

/* DESIGN HELL PHASE 7.6 — Footer pixel-closer reference rebuild */

.site-footer.site-footer--exact-ref {
    position: relative;
    overflow: hidden;
    margin-top: 0;
    padding: 0;
    color: #fff8f0;
    background:
        radial-gradient(ellipse 120% 90% at 50% -30%, rgba(162, 11, 29, 0.45), transparent 58%),
        radial-gradient(circle at 0% 50%, rgba(0, 0, 0, 0.42), transparent 38%),
        radial-gradient(circle at 100% 50%, rgba(0, 0, 0, 0.42), transparent 38%),
        linear-gradient(180deg, #8b0617 0%, #a20b1d 38%, #8b0617 68%, #5a000b 100%);
    border-top: none;
    box-shadow: none;
}

.site-footer.site-footer--exact-ref::before,
.site-footer.site-footer--exact-ref::after {
    content: none;
}

.site-footer.site-footer--exact-ref .footer-exact-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    background-image: none;
    background-size: 220px auto;
    background-repeat: repeat;
    background-position: center top;
    opacity: 0.14;
    mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.25) 22%, rgba(0, 0, 0, 0.25) 78%, rgba(0, 0, 0, 0.85) 100%);
    -webkit-mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.25) 22%, rgba(0, 0, 0, 0.25) 78%, rgba(0, 0, 0, 0.85) 100%);
}

.site-footer.site-footer--exact-ref .footer-exact-bg::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 8% 20%, rgba(214, 183, 90, 0.1), transparent 34%),
        radial-gradient(circle at 92% 24%, rgba(214, 183, 90, 0.08), transparent 32%),
        linear-gradient(180deg, rgba(74, 0, 8, 0.15) 0%, transparent 28%, transparent 72%, rgba(30, 0, 8, 0.35) 100%);
}

.site-footer.site-footer--exact-ref .footer-exact-inner {
    position: relative;
    z-index: 1;
    width: min(100%, 1600px);
    margin: 0 auto;
    padding: clamp(2rem, 3.25vw, 3.25rem) clamp(1.25rem, 4.5vw, 4.5rem) clamp(1.35rem, 2.1vw, 2.125rem);
}

.site-footer.site-footer--exact-ref .footer-exact-top {
    display: grid;
    grid-template-columns: 1.1fr auto 1.75fr 1.25fr;
    align-items: center;
    gap: clamp(1.5rem, 3.5vw, 3.5rem);
    min-height: clamp(150px, 12vw, 190px);
}

.site-footer.site-footer--exact-ref .footer-exact-brand {
    display: grid;
    justify-items: center;
    gap: 0.65rem;
    text-align: center;
}

.site-footer.site-footer--exact-ref .footer-exact-logo {
    display: block;
    width: min(100%, 300px);
    max-width: 320px;
    height: auto;
    object-fit: contain;
    filter: drop-shadow(0 3px 12px rgba(0, 0, 0, 0.25));
}

.site-footer.site-footer--exact-ref .footer-exact-slogan {
    display: grid;
    gap: 0.12rem;
    margin: 0;
    color: #fff;
    font-size: clamp(0.62rem, 0.78vw, 0.72rem);
    font-weight: 700;
    letter-spacing: 0.25em;
    line-height: 1.45;
    text-transform: uppercase;
}

.site-footer.site-footer--exact-ref .footer-exact-slogan span {
    display: block;
}

.site-footer.site-footer--exact-ref .footer-exact-brand-line {
    width: min(100%, 13rem);
    height: 1px;
    margin-top: 0.2rem;
    background: linear-gradient(90deg, transparent, #d6b75a 18%, #c9a646 50%, #d6b75a 82%, transparent);
    position: relative;
}

.site-footer.site-footer--exact-ref .footer-exact-brand-line::after {
    content: "";
    position: absolute;
    left: 50%;
    top: 50%;
    width: 7px;
    height: 7px;
    border: 1px solid #d6b75a;
    transform: translate(-50%, -50%) rotate(45deg);
    background: rgba(90, 0, 11, 0.9);
}

.site-footer.site-footer--exact-ref .footer-exact-vertical-divider {
    position: relative;
    align-self: center;
    width: 1px;
    height: clamp(120px, 10vw, 165px);
    background: linear-gradient(180deg, transparent, rgba(214, 183, 90, 0.35) 12%, #d6b75a 50%, rgba(214, 183, 90, 0.35) 88%, transparent);
}

.site-footer.site-footer--exact-ref .footer-exact-vertical-divider::before {
    content: "";
    position: absolute;
    left: 50%;
    top: 50%;
    width: 8px;
    height: 8px;
    border: 1px solid #d6b75a;
    transform: translate(-50%, -50%) rotate(45deg);
    background: rgba(90, 0, 11, 0.92);
    box-shadow: 0 0 0 2px rgba(90, 0, 11, 0.95);
}

.site-footer.site-footer--exact-ref .footer-exact-newsletter {
    width: 100%;
    max-width: 620px;
    justify-self: center;
    padding: clamp(1.25rem, 1.75vw, 1.75rem) clamp(1.35rem, 2.1vw, 2.125rem);
    border: 1px solid rgba(214, 183, 90, 0.62);
    border-radius: 18px;
    background: rgba(80, 0, 15, 0.35);
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.06),
        0 0 24px rgba(214, 183, 90, 0.08);
    text-align: center;
}

.site-footer.site-footer--exact-ref .footer-exact-newsletter h2 {
    margin: 0 0 0.35rem;
    color: #d6b75a;
    font-size: clamp(0.68rem, 0.82vw, 0.78rem);
    font-weight: 800;
    letter-spacing: 0.2em;
    line-height: 1.2;
    text-transform: uppercase;
}

.site-footer.site-footer--exact-ref .footer-exact-newsletter > p {
    margin: 0 0 1rem;
    color: rgba(255, 255, 255, 0.92);
    font-size: clamp(0.82rem, 0.95vw, 0.95rem);
    line-height: 1.35;
}

.site-footer.site-footer--exact-ref .footer-exact-newsletter-form {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 0.875rem;
}

.site-footer.site-footer--exact-ref .footer-exact-mail-icon {
    display: inline-flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    width: 2.35rem;
    height: 2.35rem;
    border: 1px solid rgba(214, 183, 90, 0.45);
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.22);
    color: rgba(255, 248, 236, 0.85);
    font-size: 0.95rem;
    line-height: 1;
}

.site-footer.site-footer--exact-ref .footer-exact-newsletter-form input {
    flex: 1 1 180px;
    min-width: 0;
    min-height: 2.75rem;
    padding: 0 1.1rem;
    border: 1px solid rgba(214, 183, 90, 0.45);
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.28);
    color: #fff;
    font-size: 0.88rem;
    line-height: 1.2;
    outline: none;
}

.site-footer.site-footer--exact-ref .footer-exact-newsletter-form input::placeholder {
    color: rgba(255, 248, 236, 0.52);
}

.site-footer.site-footer--exact-ref .footer-exact-newsletter-form input:disabled {
    cursor: not-allowed;
    opacity: 0.95;
}

.site-footer.site-footer--exact-ref .footer-exact-newsletter-form button {
    flex-shrink: 0;
    min-height: 2.75rem;
    padding: 0.55rem 1.35rem;
    border: 1px solid rgba(255, 236, 196, 0.5);
    border-radius: 999px;
    background: linear-gradient(180deg, #e8c56e 0%, #d6b75a 45%, #c9a646 100%);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.22);
    color: #3a0208;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    line-height: 1.1;
    text-transform: uppercase;
    white-space: nowrap;
}

.site-footer.site-footer--exact-ref .footer-exact-follow {
    display: grid;
    gap: 1rem;
    justify-items: center;
    align-content: center;
}

.site-footer.site-footer--exact-ref .footer-exact-follow-title {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    gap: 0.75rem;
    width: min(100%, 16rem);
}

.site-footer.site-footer--exact-ref .footer-exact-follow-title span {
    display: block;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(214, 183, 90, 0.65));
}

.site-footer.site-footer--exact-ref .footer-exact-follow-title span:last-child {
    background: linear-gradient(270deg, transparent, rgba(214, 183, 90, 0.65));
}

.site-footer.site-footer--exact-ref .footer-exact-follow-title h2 {
    margin: 0;
    color: #d6b75a;
    font-size: clamp(0.68rem, 0.82vw, 0.78rem);
    font-weight: 800;
    letter-spacing: 0.38em;
    line-height: 1.2;
    text-transform: uppercase;
    white-space: nowrap;
}

.site-footer.site-footer--exact-ref .footer-exact-socials {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.65rem;
}

.site-footer.site-footer--exact-ref .footer-exact-socials a {
    display: inline-grid;
    place-items: center;
    width: clamp(2.75rem, 4vw, 4.125rem);
    height: clamp(2.75rem, 4vw, 4.125rem);
    border: 1px solid rgba(214, 183, 90, 0.62);
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.18);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
    color: #fff;
    font-size: clamp(0.95rem, 1.4vw, 1.35rem);
    font-weight: 700;
    line-height: 1;
    text-decoration: none;
    transition:
        border-color 0.16s ease,
        background 0.16s ease,
        box-shadow 0.16s ease,
        transform 0.16s ease;
}

.site-footer.site-footer--exact-ref .footer-exact-socials a:hover,
.site-footer.site-footer--exact-ref .footer-exact-socials a:focus-visible {
    border-color: #d6b75a;
    background: rgba(201, 166, 70, 0.22);
    box-shadow: 0 0 16px rgba(214, 183, 90, 0.25);
    transform: translateY(-2px);
    outline: none;
}

.site-footer.site-footer--exact-ref .footer-exact-main-divider {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: clamp(1.35rem, 1.9vw, 1.875rem) 0 clamp(1.1rem, 1.6vw, 1.625rem);
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, rgba(214, 183, 90, 0.2) 6%, rgba(214, 183, 90, 0.72) 50%, rgba(214, 183, 90, 0.2) 94%, transparent 100%);
}

.site-footer.site-footer--exact-ref .footer-exact-main-divider span {
    position: absolute;
    width: 11px;
    height: 11px;
    border: 1px solid #d6b75a;
    transform: rotate(45deg);
    background: linear-gradient(135deg, rgba(214, 183, 90, 0.45), rgba(90, 0, 11, 0.95));
    box-shadow: 0 0 0 3px rgba(90, 0, 11, 0.98);
}

.site-footer.site-footer--exact-ref .footer-exact-nav {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0;
    max-width: 1220px;
    margin: 0 auto;
    text-align: center;
}

.site-footer.site-footer--exact-ref .footer-exact-nav-col {
    display: grid;
    gap: 0.55rem;
    justify-items: center;
    padding: 0 clamp(0.75rem, 1.5vw, 1.35rem);
    border-right: 1px solid rgba(214, 183, 90, 0.28);
}

.site-footer.site-footer--exact-ref .footer-exact-nav-col:last-child {
    border-right: 0;
}

.site-footer.site-footer--exact-ref .footer-exact-nav-col h3 {
    position: relative;
    margin: 0 0 0.65rem;
    padding-bottom: 0.45rem;
    color: #d6b75a;
    font-size: 0.8125rem;
    font-weight: 800;
    letter-spacing: 0.31em;
    line-height: 1.2;
    text-transform: uppercase;
}

.site-footer.site-footer--exact-ref .footer-exact-nav-col h3::after {
    content: "";
    position: absolute;
    left: 50%;
    bottom: 0;
    width: 2.5rem;
    height: 1px;
    transform: translateX(-50%);
    background: linear-gradient(90deg, transparent, #d6b75a, transparent);
}

.site-footer.site-footer--exact-ref .footer-exact-nav-col a {
    color: rgba(255, 248, 236, 0.9);
    font-size: clamp(0.9rem, 1.05vw, 1.0625rem);
    font-weight: 600;
    line-height: 1.5;
    text-decoration: none;
    transition: color 0.16s ease;
}

.site-footer.site-footer--exact-ref .footer-exact-nav-col a:hover,
.site-footer.site-footer--exact-ref .footer-exact-nav-col a:focus-visible {
    color: #f0d48a;
    outline: none;
    text-decoration: underline;
    text-decoration-color: rgba(214, 183, 90, 0.45);
    text-underline-offset: 0.14em;
}

.site-footer.site-footer--exact-ref .footer-exact-bottom {
    position: relative;
    z-index: 1;
    padding: 1.125rem 1.5rem;
    border-top: 1px solid rgba(214, 183, 90, 0.32);
    background: rgba(30, 0, 8, 0.58);
    text-align: center;
}

.site-footer.site-footer--exact-ref .footer-exact-bottom p {
    margin: 0;
    color: rgba(255, 244, 228, 0.82);
    font-size: 0.875rem;
    letter-spacing: 0.09em;
    line-height: 1.35;
}

@media (max-width: 1100px) {
    .site-footer.site-footer--exact-ref .footer-exact-top {
        grid-template-columns: 1fr;
        gap: clamp(1.5rem, 3vw, 2rem);
        min-height: 0;
    }

    .site-footer.site-footer--exact-ref .footer-exact-vertical-divider {
        display: none;
    }

    .site-footer.site-footer--exact-ref .footer-exact-newsletter {
        max-width: none;
    }

    .site-footer.site-footer--exact-ref .footer-exact-nav {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: clamp(0.85rem, 2vw, 1.25rem) 0;
    }

    .site-footer.site-footer--exact-ref .footer-exact-nav-col {
        border-right: 0;
    }

    .site-footer.site-footer--exact-ref .footer-exact-nav-col:nth-child(odd) {
        border-right: 1px solid rgba(214, 183, 90, 0.22);
    }
}

@media (max-width: 640px) {
    .site-footer.site-footer--exact-ref .footer-exact-inner {
        padding: 1.75rem 1.125rem 1.25rem;
    }

    .site-footer.site-footer--exact-ref .footer-exact-logo {
        max-width: 210px;
    }

    .site-footer.site-footer--exact-ref .footer-exact-newsletter-form {
        flex-direction: column;
        align-items: stretch;
    }

    .site-footer.site-footer--exact-ref .footer-exact-mail-icon {
        display: none;
    }

    .site-footer.site-footer--exact-ref .footer-exact-newsletter-form input,
    .site-footer.site-footer--exact-ref .footer-exact-newsletter-form button {
        width: 100%;
    }

    .site-footer.site-footer--exact-ref .footer-exact-socials a {
        width: 2.65rem;
        height: 2.65rem;
        font-size: 0.95rem;
    }

    .site-footer.site-footer--exact-ref .footer-exact-nav {
        grid-template-columns: 1fr;
    }

    .site-footer.site-footer--exact-ref .footer-exact-nav-col,
    .site-footer.site-footer--exact-ref .footer-exact-nav-col:nth-child(odd) {
        border-right: 0;
        padding-bottom: 0.85rem;
    }

    .site-footer.site-footer--exact-ref .footer-exact-bg {
        opacity: 0.11;
        background-size: 170px auto;
    }
}

/* URGENT FIX — Compact premium footer and horizontal partners scroll restore */

.site-footer.site-footer--premium-compact {
    position: relative;
    overflow: hidden;
    margin-top: clamp(1rem, 1.8vw, 1.5rem);
    padding: 0;
    color: #fff8f0;
    background: linear-gradient(180deg, #a20b1d 0%, #8b0617 55%, #5a000b 100%);
    border-top: none;
}

.site-footer.site-footer--premium-compact::before,
.site-footer.site-footer--premium-compact::after {
    content: none;
}

.site-footer.site-footer--premium-compact .footer-premium-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    opacity: 0.12;
    background-image: none;
    background-size: 190px auto;
    background-repeat: repeat;
    mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.2) 30%, rgba(0, 0, 0, 0.2) 70%, rgba(0, 0, 0, 0.7) 100%);
    -webkit-mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.2) 30%, rgba(0, 0, 0, 0.2) 70%, rgba(0, 0, 0, 0.7) 100%);
}

.site-footer.site-footer--premium-compact .footer-premium-inner {
    position: relative;
    z-index: 1;
    width: min(100%, 1500px);
    margin: 0 auto;
    padding: 2.25rem 3rem 1.125rem;
}

.site-footer.site-footer--premium-compact .footer-premium-top {
    display: grid;
    grid-template-columns: minmax(220px, 0.9fr) minmax(360px, 1.2fr) minmax(260px, 0.9fr);
    align-items: center;
    gap: 2rem;
}

.site-footer.site-footer--premium-compact .footer-premium-brand {
    display: grid;
    gap: 0.45rem;
    justify-items: start;
    text-align: left;
}

.site-footer.site-footer--premium-compact .footer-premium-logo {
    display: block;
    width: auto;
    max-width: min(240px, 100%);
    height: auto;
    max-height: 3.25rem;
    object-fit: contain;
    object-position: left center;
}

.site-footer.site-footer--premium-compact .footer-premium-slogan {
    display: grid;
    gap: 0.08rem;
    margin: 0;
    color: rgba(255, 248, 236, 0.92);
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    line-height: 1.35;
    text-transform: uppercase;
}

.site-footer.site-footer--premium-compact .footer-premium-slogan span {
    display: block;
}

.site-footer.site-footer--premium-compact .footer-premium-newsletter {
    padding: 0.85rem 1rem;
    border: 1px solid rgba(214, 183, 90, 0.5);
    border-radius: 14px;
    background: rgba(80, 0, 15, 0.32);
    text-align: center;
}

.site-footer.site-footer--premium-compact .footer-premium-newsletter h2 {
    margin: 0 0 0.2rem;
    color: #d6b75a;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.16em;
    text-transform: uppercase;
}

.site-footer.site-footer--premium-compact .footer-premium-newsletter > p {
    margin: 0 0 0.65rem;
    color: rgba(255, 248, 236, 0.9);
    font-size: 0.76rem;
    line-height: 1.3;
}

.site-footer.site-footer--premium-compact .footer-premium-newsletter-form {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 0.5rem;
}

.site-footer.site-footer--premium-compact .footer-premium-newsletter-form input {
    flex: 1 1 auto;
    min-width: 0;
    min-height: 2.15rem;
    padding: 0.4rem 0.85rem;
    border: 1px solid rgba(214, 183, 90, 0.4);
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.25);
    color: #fff8f0;
    font-size: 0.76rem;
    outline: none;
}

.site-footer.site-footer--premium-compact .footer-premium-newsletter-form input::placeholder {
    color: rgba(255, 248, 236, 0.5);
}

.site-footer.site-footer--premium-compact .footer-premium-newsletter-form button {
    flex-shrink: 0;
    min-height: 2.15rem;
    padding: 0.4rem 0.85rem;
    border: 1px solid rgba(255, 236, 196, 0.45);
    border-radius: 999px;
    background: linear-gradient(180deg, #e4c06a, #c9a646);
    color: #2b040c;
    font-size: 0.58rem;
    font-weight: 800;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    white-space: nowrap;
}

.site-footer.site-footer--premium-compact .footer-premium-follow {
    display: grid;
    gap: 0.55rem;
    justify-items: end;
    text-align: right;
}

.site-footer.site-footer--premium-compact .footer-premium-follow h2 {
    margin: 0;
    color: #d6b75a;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.16em;
    text-transform: uppercase;
}

.site-footer.site-footer--premium-compact .footer-premium-socials {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 0.4rem;
}

.site-footer.site-footer--premium-compact .footer-premium-socials a {
    display: inline-grid;
    place-items: center;
    width: 2.15rem;
    height: 2.15rem;
    border: 1px solid rgba(214, 183, 90, 0.55);
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.15);
    color: #fff8f0;
    font-size: 0.58rem;
    font-weight: 800;
    text-decoration: none;
    transition: border-color 0.15s ease, background 0.15s ease;
}

.site-footer.site-footer--premium-compact .footer-premium-socials a:hover,
.site-footer.site-footer--premium-compact .footer-premium-socials a:focus-visible {
    border-color: #d6b75a;
    background: rgba(201, 166, 70, 0.2);
    outline: none;
}

.site-footer.site-footer--premium-compact .footer-premium-divider {
    position: relative;
    margin: 1.15rem 0 1rem;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(214, 183, 90, 0.55), transparent);
}

.site-footer.site-footer--premium-compact .footer-premium-divider span {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 8px;
    height: 8px;
    border: 1px solid #d6b75a;
    transform: translate(-50%, -50%) rotate(45deg);
    background: rgba(90, 0, 11, 0.95);
}

.site-footer.site-footer--premium-compact .footer-premium-nav {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0;
    max-width: 1100px;
    margin: 0 auto;
    text-align: center;
}

.site-footer.site-footer--premium-compact .footer-premium-nav-col {
    display: grid;
    gap: 0.32rem;
    justify-items: center;
    padding: 0 0.75rem;
    border-right: 1px solid rgba(214, 183, 90, 0.2);
}

.site-footer.site-footer--premium-compact .footer-premium-nav-col:last-child {
    border-right: 0;
}

.site-footer.site-footer--premium-compact .footer-premium-nav-col h3 {
    margin: 0 0 0.25rem;
    color: #d6b75a;
    font-size: 0.58rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.site-footer.site-footer--premium-compact .footer-premium-nav-col a {
    color: rgba(255, 248, 236, 0.88);
    font-size: 0.76rem;
    font-weight: 600;
    line-height: 1.4;
    text-decoration: none;
}

.site-footer.site-footer--premium-compact .footer-premium-nav-col a:hover,
.site-footer.site-footer--premium-compact .footer-premium-nav-col a:focus-visible {
    color: #f0d48a;
    outline: none;
    text-decoration: underline;
}

.site-footer.site-footer--premium-compact .footer-premium-bottom {
    position: relative;
    z-index: 1;
    padding: 0.85rem 1.5rem;
    border-top: 1px solid rgba(214, 183, 90, 0.25);
    background: rgba(30, 0, 8, 0.55);
    text-align: center;
}

.site-footer.site-footer--premium-compact .footer-premium-bottom p {
    margin: 0;
    color: rgba(255, 244, 228, 0.75);
    font-size: 0.68rem;
    letter-spacing: 0.06em;
}

.page-home .fifa-partners-section--home {
    margin-top: 0.5rem;
    margin-bottom: 0.35rem;
    padding: 0.65rem 0.75rem 0.55rem;
}

.page-home .fifa-partners-section--home .fifa-partners-section__head {
    margin-bottom: 0.45rem;
}

.page-home .fifa-partners-track--strip,
.page-home .fifa-partners-carousel [data-scroll-track] {
    overflow-x: auto !important;
    overflow-y: hidden !important;
    max-width: 100%;
    min-width: 0;
    scroll-snap-type: x proximity;
    scrollbar-width: thin;
    -webkit-overflow-scrolling: touch;
}

.page-home .fifa-partners-grid--home.fifa-partners-grid--strip,
.page-home .fifa-partners-section--home .fifa-partners-grid,
.page-home .fifa-partners-section--home .fifa-partners-grid--home {
    display: flex !important;
    flex-wrap: nowrap !important;
    align-items: stretch;
    gap: 0.75rem !important;
    width: max-content !important;
    min-width: 100%;
    max-width: none !important;
    grid-template-columns: none !important;
    overflow: visible;
    padding-bottom: 0.35rem;
    scroll-snap-type: x proximity;
}

.page-home .fifa-partner-card--home.fifa-partner-card--strip,
.page-home .fifa-partners-section--home .fifa-partner-card {
    flex: 0 0 180px !important;
    width: 180px !important;
    min-width: 180px !important;
    max-width: 210px !important;
    height: 84px !important;
    min-height: 84px !important;
    max-height: 92px !important;
    scroll-snap-align: start;
    grid-template-rows: auto 1fr;
    gap: 0.15rem;
    padding: 0.32rem 0.28rem 0.36rem;
}

.page-home .fifa-partner-card--strip .fifa-partner-logo,
.page-home .fifa-partners-section--home .fifa-partner-logo {
    min-height: 0;
    max-height: 38px;
    padding: 0.08rem 0.12rem;
}

.page-home .fifa-partner-card--strip .fifa-partner-logo img,
.page-home .fifa-partners-section--home .fifa-partner-logo img {
    max-height: 32px;
    max-width: 92%;
    object-fit: contain;
}

.page-home .fifa-partner-card--strip .fifa-partner-label {
    font-size: 0.46rem;
    padding: 0.06rem 0.24rem;
    line-height: 1.1;
}

@media (max-width: 1100px) {
    .site-footer.site-footer--premium-compact .footer-premium-inner {
        padding: 1.75rem 1.5rem 1rem;
    }

    .site-footer.site-footer--premium-compact .footer-premium-top {
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }

    .site-footer.site-footer--premium-compact .footer-premium-brand {
        grid-column: 1 / -1;
        justify-items: center;
        text-align: center;
    }

    .site-footer.site-footer--premium-compact .footer-premium-logo {
        object-position: center;
    }

    .site-footer.site-footer--premium-compact .footer-premium-follow {
        justify-items: center;
        text-align: center;
    }

    .site-footer.site-footer--premium-compact .footer-premium-socials {
        justify-content: center;
    }

    .site-footer.site-footer--premium-compact .footer-premium-nav {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem 0;
    }

    .site-footer.site-footer--premium-compact .footer-premium-nav-col {
        border-right: 0;
    }

    .site-footer.site-footer--premium-compact .footer-premium-nav-col:nth-child(odd) {
        border-right: 1px solid rgba(214, 183, 90, 0.18);
    }
}

@media (max-width: 640px) {
    .site-footer.site-footer--premium-compact .footer-premium-inner {
        padding: 1.5rem 1rem 0.85rem;
    }

    .site-footer.site-footer--premium-compact .footer-premium-top {
        grid-template-columns: 1fr;
    }

    .site-footer.site-footer--premium-compact .footer-premium-newsletter-form {
        flex-direction: column;
        align-items: stretch;
    }

    .site-footer.site-footer--premium-compact .footer-premium-newsletter-form button {
        width: 100%;
    }

    .site-footer.site-footer--premium-compact .footer-premium-nav {
        grid-template-columns: 1fr;
    }

    .site-footer.site-footer--premium-compact .footer-premium-nav-col,
    .site-footer.site-footer--premium-compact .footer-premium-nav-col:nth-child(odd) {
        border-right: 0;
    }

    .page-home .fifa-partner-card--home.fifa-partner-card--strip,
    .page-home .fifa-partners-section--home .fifa-partner-card {
        flex: 0 0 168px !important;
        width: 168px !important;
        min-width: 168px !important;
    }
}

/* FINAL FOOTER — Exact reference match */

.site-footer.site-footer--final-reference {
    position: relative;
    overflow: hidden;
    margin-top: clamp(1rem, 1.8vw, 1.5rem);
    padding: 0;
    color: #fff;
    background:
        radial-gradient(circle at 8% 10%, rgba(218, 177, 76, 0.16), transparent 28%),
        radial-gradient(circle at 90% 8%, rgba(255, 255, 255, 0.06), transparent 30%),
        linear-gradient(135deg, #9f071b 0%, #760015 46%, #3b000b 100%);
    border-top: 1px solid rgba(218, 177, 76, 0.55);
    box-shadow: none;
}

.site-footer.site-footer--final-reference::before,
.site-footer.site-footer--final-reference::after {
    content: none;
}

.site-footer.site-footer--final-reference .footer-final-pattern {
    position: absolute;
    inset: 0;
    opacity: 0.16;
    pointer-events: none;
    background-image:
        none,
        linear-gradient(30deg, rgba(255, 255, 255, 0.08) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.08) 87.5%, rgba(255, 255, 255, 0.08)),
        linear-gradient(150deg, rgba(255, 255, 255, 0.08) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.08) 87.5%, rgba(255, 255, 255, 0.08));
    background-size: 200px auto, 72px 124px, 72px 124px;
    background-repeat: repeat, repeat, repeat;
    mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.28) 35%, rgba(0, 0, 0, 0.28) 65%, rgba(0, 0, 0, 0.75) 100%);
    -webkit-mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.28) 35%, rgba(0, 0, 0, 0.28) 65%, rgba(0, 0, 0, 0.75) 100%);
}

.site-footer.site-footer--final-reference .footer-final-inner {
    position: relative;
    z-index: 1;
    max-width: 1500px;
    margin: 0 auto;
    padding: 46px 48px 26px;
}

.site-footer.site-footer--final-reference .footer-final-top {
    display: grid;
    grid-template-columns: minmax(230px, 0.85fr) minmax(420px, 1.25fr) minmax(280px, 0.9fr);
    align-items: center;
    gap: 56px;
}

.site-footer.site-footer--final-reference .footer-final-brand {
    text-align: center;
}

.site-footer.site-footer--final-reference .footer-final-logo {
    width: 230px;
    max-width: 100%;
    height: auto;
    object-fit: contain;
    display: block;
    margin: 0 auto 18px;
}

.site-footer.site-footer--final-reference .footer-final-slogan {
    display: grid;
    gap: 0.12rem;
    margin: 0;
    color: #fff;
    font-weight: 800;
    letter-spacing: 0.16em;
    line-height: 1.55;
    text-transform: uppercase;
    font-size: clamp(0.72rem, 0.95vw, 0.9375rem);
}

.site-footer.site-footer--final-reference .footer-final-slogan span {
    display: block;
}

.site-footer.site-footer--final-reference .footer-final-newsletter {
    border: 1px solid rgba(218, 177, 76, 0.7);
    border-radius: 16px;
    padding: 28px 34px;
    background: rgba(55, 0, 12, 0.28);
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.2);
}

.site-footer.site-footer--final-reference .footer-final-title {
    color: #d9b14c;
    font-weight: 900;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    font-size: 0.875rem;
    margin: 0 0 10px;
}

.site-footer.site-footer--final-reference .footer-final-newsletter > p {
    margin: 0 0 18px;
    color: rgba(255, 255, 255, 0.92);
    font-size: clamp(1rem, 1.25vw, 1.25rem);
    line-height: 1.3;
}

.site-footer.site-footer--final-reference .footer-final-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 180px;
    gap: 16px;
    align-items: center;
}

.site-footer.site-footer--final-reference .footer-final-input-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    border: 1px solid rgba(255, 255, 255, 0.38);
    border-radius: 999px;
    padding: 0 18px;
    height: 58px;
    background: rgba(40, 0, 10, 0.38);
}

.site-footer.site-footer--final-reference .footer-final-mail-icon {
    display: inline-flex;
    flex-shrink: 0;
    color: rgba(255, 255, 255, 0.75);
}

.site-footer.site-footer--final-reference .footer-final-input-wrap input {
    width: 100%;
    min-width: 0;
    border: 0;
    outline: 0;
    background: transparent;
    color: #fff;
    font-size: 1rem;
}

.site-footer.site-footer--final-reference .footer-final-input-wrap input::placeholder {
    color: rgba(255, 255, 255, 0.65);
}

.site-footer.site-footer--final-reference .footer-final-input-wrap input:disabled {
    cursor: not-allowed;
    opacity: 0.95;
}

.site-footer.site-footer--final-reference .footer-final-submit {
    height: 58px;
    border: 0;
    border-radius: 999px;
    background: linear-gradient(135deg, #f0d46d, #c99a2d);
    color: #3b000b;
    font-size: 0.72rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    cursor: not-allowed;
    white-space: nowrap;
}

.site-footer.site-footer--final-reference .footer-final-follow {
    text-align: center;
}

.site-footer.site-footer--final-reference .footer-final-follow-head {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    margin-bottom: 24px;
}

.site-footer.site-footer--final-reference .footer-final-follow-head::before,
.site-footer.site-footer--final-reference .footer-final-follow-head::after {
    content: "";
    flex: 1 1 0;
    max-width: 120px;
    height: 1px;
    background: rgba(218, 177, 76, 0.55);
}

.site-footer.site-footer--final-reference .footer-final-follow-head .footer-final-title {
    margin: 0;
    white-space: nowrap;
}

.site-footer.site-footer--final-reference .footer-final-social {
    display: flex;
    justify-content: center;
    gap: 22px;
    flex-wrap: nowrap;
}

.site-footer.site-footer--final-reference .footer-final-social a {
    width: 58px;
    height: 58px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(218, 177, 76, 0.75);
    color: #fff;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 900;
    background: rgba(70, 0, 15, 0.22);
    transition: border-color 0.15s ease, background 0.15s ease, transform 0.15s ease;
}

.site-footer.site-footer--final-reference .footer-final-social a:hover,
.site-footer.site-footer--final-reference .footer-final-social a:focus-visible {
    border-color: #f0d46d;
    background: rgba(201, 154, 45, 0.22);
    transform: translateY(-1px);
    outline: none;
}

.site-footer.site-footer--final-reference .footer-final-ornament-line {
    position: relative;
    height: 46px;
    margin: 20px 0 8px;
}

.site-footer.site-footer--final-reference .footer-final-ornament-line::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(218, 177, 76, 0.7), transparent);
}

.site-footer.site-footer--final-reference .footer-final-ornament-line span {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 14px;
    height: 14px;
    transform: translate(-50%, -50%) rotate(45deg);
    border: 1px solid rgba(218, 177, 76, 0.85);
    background: #650012;
}

.site-footer.site-footer--final-reference .footer-final-nav {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    max-width: 1180px;
    margin: 0 auto;
    text-align: center;
}

.site-footer.site-footer--final-reference .footer-final-col {
    position: relative;
    padding: 0 34px;
}

.site-footer.site-footer--final-reference .footer-final-col:not(:last-child)::after {
    content: "";
    position: absolute;
    right: 0;
    top: 20px;
    bottom: 18px;
    width: 1px;
    background: linear-gradient(180deg, transparent, rgba(218, 177, 76, 0.65), transparent);
}

.site-footer.site-footer--final-reference .footer-final-col h3 {
    color: #d9b14c;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.24em;
    font-size: 0.8125rem;
    margin: 0 0 24px;
}

.site-footer.site-footer--final-reference .footer-final-col h3::after {
    content: "";
    display: block;
    width: 42px;
    height: 1px;
    margin: 12px auto 0;
    background: rgba(218, 177, 76, 0.85);
}

.site-footer.site-footer--final-reference .footer-final-col a {
    display: block;
    color: rgba(255, 255, 255, 0.92);
    text-decoration: none;
    font-size: clamp(0.9rem, 1.05vw, 1.0625rem);
    line-height: 1.5;
    margin: 0 0 13px;
    transition: color 0.15s ease;
}

.site-footer.site-footer--final-reference .footer-final-col a:hover,
.site-footer.site-footer--final-reference .footer-final-col a:focus-visible {
    color: #f0d46d;
    outline: none;
    text-decoration: underline;
    text-decoration-color: rgba(218, 177, 76, 0.45);
}

.site-footer.site-footer--final-reference .footer-final-bottom {
    position: relative;
    z-index: 1;
    border-top: 1px solid rgba(218, 177, 76, 0.58);
    text-align: center;
    padding: 22px 20px 26px;
    background: rgba(28, 0, 8, 0.36);
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.9375rem;
}

.site-footer.site-footer--final-reference .footer-final-bottom p {
    margin: 0;
}

@media (max-width: 1180px) {
    .site-footer.site-footer--final-reference .footer-final-inner {
        padding: 38px 32px 22px;
    }

    .site-footer.site-footer--final-reference .footer-final-top {
        grid-template-columns: 1fr;
        gap: 32px;
    }

    .site-footer.site-footer--final-reference .footer-final-newsletter {
        max-width: 680px;
        margin: 0 auto;
        width: 100%;
    }

    .site-footer.site-footer--final-reference .footer-final-nav {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px 0;
        max-width: none;
    }

    .site-footer.site-footer--final-reference .footer-final-col::after {
        display: none;
    }

    .site-footer.site-footer--final-reference .footer-final-col:nth-child(odd) {
        border-right: 1px solid rgba(218, 177, 76, 0.22);
    }
}

@media (max-width: 700px) {
    .site-footer.site-footer--final-reference .footer-final-inner {
        padding: 34px 22px 18px;
    }

    .site-footer.site-footer--final-reference .footer-final-logo {
        width: 180px;
    }

    .site-footer.site-footer--final-reference .footer-final-form {
        grid-template-columns: 1fr;
    }

    .site-footer.site-footer--final-reference .footer-final-submit {
        width: 100%;
    }

    .site-footer.site-footer--final-reference .footer-final-social {
        gap: 14px;
        flex-wrap: wrap;
    }

    .site-footer.site-footer--final-reference .footer-final-social a {
        width: 46px;
        height: 46px;
        font-size: 0.78rem;
    }

    .site-footer.site-footer--final-reference .footer-final-nav {
        grid-template-columns: 1fr;
    }

    .site-footer.site-footer--final-reference .footer-final-col,
    .site-footer.site-footer--final-reference .footer-final-col:nth-child(odd) {
        border-right: 0;
        padding: 0 12px;
    }

    .site-footer.site-footer--final-reference .footer-final-col:not(:last-child)::after {
        display: none;
    }
}

/* EMERGENCY FINAL FOOTER — Compact one-screen reference match */

.m2030-footer-final {
    position: relative !important;
    overflow: hidden !important;
    color: #fff !important;
    background:
        radial-gradient(circle at 8% 8%, rgba(218, 177, 76, 0.12), transparent 30%),
        radial-gradient(circle at 92% 10%, rgba(255, 255, 255, 0.05), transparent 28%),
        linear-gradient(135deg, #a50b1f 0%, #7b0017 48%, #3a000b 100%) !important;
    border-top: 1px solid rgba(218, 177, 76, 0.55) !important;
    margin: 0 !important;
    padding: 0 !important;
    min-height: 0 !important;
    box-sizing: border-box;
}

.m2030-footer-final__pattern {
    position: absolute;
    inset: 0;
    pointer-events: none;
    opacity: 0.14;
    background-image:
        none,
        linear-gradient(30deg, rgba(255, 255, 255, 0.08) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.08) 87.5%, rgba(255, 255, 255, 0.08)),
        linear-gradient(150deg, rgba(255, 255, 255, 0.08) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.08) 87.5%, rgba(255, 255, 255, 0.08));
    background-size: 190px auto, 68px 118px, 68px 118px;
    background-repeat: repeat, repeat, repeat;
    mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0.72) 0%, rgba(0, 0, 0, 0.22) 30%, rgba(0, 0, 0, 0.22) 70%, rgba(0, 0, 0, 0.72) 100%);
    -webkit-mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0.72) 0%, rgba(0, 0, 0, 0.22) 30%, rgba(0, 0, 0, 0.22) 70%, rgba(0, 0, 0, 0.72) 100%);
}

.m2030-footer-final__inner {
    position: relative;
    z-index: 1;
    max-width: 1480px;
    margin: 0 auto;
    padding: 34px 42px 18px;
}

.m2030-footer-final__top {
    display: grid !important;
    grid-template-columns: minmax(210px, 0.8fr) minmax(420px, 1.15fr) minmax(300px, 0.9fr) !important;
    align-items: center !important;
    gap: 44px !important;
    margin: 0 !important;
}

.m2030-footer-final__brand {
    text-align: center;
}

.m2030-footer-final__logo {
    display: block;
    width: 220px;
    max-width: 100%;
    height: auto;
    object-fit: contain;
    margin: 0 auto 14px;
}

.m2030-footer-final__slogan {
    margin: 0;
    color: #fff;
    font-size: 14px;
    line-height: 1.55;
    letter-spacing: 0.16em;
    font-weight: 900;
    text-transform: uppercase;
}

.m2030-footer-final__newsletter {
    border: 1px solid rgba(218, 177, 76, 0.7);
    border-radius: 18px;
    padding: 24px 30px;
    background: rgba(52, 0, 13, 0.3);
    box-shadow: 0 18px 38px rgba(0, 0, 0, 0.2);
}

.m2030-footer-final__newsletter h2,
.m2030-footer-final__follow h2,
.m2030-footer-final__nav h3 {
    color: #d9b14c;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.24em;
}

.m2030-footer-final__newsletter h2 {
    margin: 0 0 8px;
    font-size: 14px;
}

.m2030-footer-final__newsletter p {
    margin: 0 0 16px;
    color: rgba(255, 255, 255, 0.92);
    font-size: 19px;
}

.m2030-footer-final__form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 170px;
    gap: 14px;
    align-items: center;
}

.m2030-footer-final__input {
    height: 54px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.36);
    background: rgba(35, 0, 10, 0.42);
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 0 18px;
    min-width: 0;
}

.m2030-footer-final__input span {
    color: #d9b14c;
    flex: 0 0 auto;
}

.m2030-footer-final__input input {
    width: 100%;
    min-width: 0;
    border: 0;
    outline: 0;
    background: transparent;
    color: #fff;
    font-size: 15px;
}

.m2030-footer-final__input input::placeholder {
    color: rgba(255, 255, 255, 0.68);
}

.m2030-footer-final__input input:disabled {
    cursor: not-allowed;
    opacity: 0.95;
}

.m2030-footer-final__form button {
    height: 54px;
    border: 0;
    border-radius: 999px;
    background: linear-gradient(135deg, #f1d46e, #c99a2d);
    color: #3a000b;
    font-size: 0.72rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    cursor: not-allowed;
    white-space: nowrap;
}

.m2030-footer-final__follow {
    text-align: center;
}

.m2030-footer-final__follow-title {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    margin-bottom: 20px;
}

.m2030-footer-final__follow-title span {
    width: 92px;
    height: 1px;
    background: rgba(218, 177, 76, 0.58);
}

.m2030-footer-final__follow h2 {
    margin: 0;
    font-size: 14px;
    white-space: nowrap;
}

.m2030-footer-final__social {
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: nowrap;
}

.m2030-footer-final__social a {
    width: 52px;
    height: 52px;
    border-radius: 999px;
    border: 1px solid rgba(218, 177, 76, 0.78);
    color: #fff;
    background: rgba(60, 0, 14, 0.22);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-weight: 900;
    font-size: 14px;
    transition: border-color 0.15s ease, background 0.15s ease;
}

.m2030-footer-final__social a:hover,
.m2030-footer-final__social a:focus-visible {
    border-color: #f1d46e;
    background: rgba(201, 154, 45, 0.22);
    outline: none;
}

.m2030-footer-final__divider {
    position: relative;
    height: 34px;
    margin: 14px 0 12px;
}

.m2030-footer-final__divider::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(218, 177, 76, 0.7), transparent);
}

.m2030-footer-final__divider span {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 13px;
    height: 13px;
    transform: translate(-50%, -50%) rotate(45deg);
    border: 1px solid rgba(218, 177, 76, 0.9);
    background: #710014;
}

.m2030-footer-final__nav {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    max-width: 1140px;
    margin: 0 auto !important;
    text-align: center;
}

.m2030-footer-final__nav section {
    position: relative;
    padding: 0 30px;
}

.m2030-footer-final__nav section:not(:last-child)::after {
    content: "";
    position: absolute;
    right: 0;
    top: 14px;
    bottom: 12px;
    width: 1px;
    background: linear-gradient(180deg, transparent, rgba(218, 177, 76, 0.56), transparent);
}

.m2030-footer-final__nav h3 {
    margin: 0 0 18px;
    font-size: 13px;
}

.m2030-footer-final__nav h3::after {
    content: "";
    display: block;
    width: 38px;
    height: 1px;
    margin: 10px auto 0;
    background: rgba(218, 177, 76, 0.85);
}

.m2030-footer-final__nav a {
    display: block;
    color: rgba(255, 255, 255, 0.92);
    text-decoration: none;
    font-size: 16px;
    line-height: 1.45;
    margin: 0 0 10px;
}

.m2030-footer-final__nav-muted {
    display: block;
    color: rgba(255, 255, 255, 0.58);
    font-size: 16px;
    font-weight: 600;
    line-height: 1.45;
    margin: 0 0 10px;
}

.m2030-footer-final__nav a:hover,
.m2030-footer-final__nav a:focus-visible {
    color: #f1d46e;
    outline: none;
    text-decoration: underline;
}

.m2030-footer-final__bottom {
    position: relative;
    z-index: 1;
    border-top: 1px solid rgba(218, 177, 76, 0.56);
    background: rgba(25, 0, 7, 0.38);
    text-align: center;
    padding: 16px 20px 18px;
    color: rgba(255, 255, 255, 0.82);
    font-size: 15px;
}

.m2030-footer-final__bottom p {
    margin: 0;
}

@media (max-width: 1180px) {
    .m2030-footer-final__inner {
        padding: 30px 26px 16px;
    }

    .m2030-footer-final__top {
        grid-template-columns: 200px minmax(360px, 1fr) 270px !important;
        gap: 28px !important;
    }

    .m2030-footer-final__logo {
        width: 180px;
    }

    .m2030-footer-final__newsletter {
        padding: 20px 22px;
    }

    .m2030-footer-final__form {
        grid-template-columns: minmax(0, 1fr) 145px;
    }

    .m2030-footer-final__input,
    .m2030-footer-final__form button {
        height: 50px;
    }

    .m2030-footer-final__social-link {
        width: 46px;
        height: 46px;
    }

    .m2030-footer-final__follow-title span {
        width: 62px;
    }

    .m2030-footer-final__nav section {
        padding: 0 18px;
    }

    .m2030-footer-final__nav a {
        font-size: 15px;
        margin-bottom: 8px;
    }

    .m2030-footer-final__nav-muted {
        font-size: 15px;
        margin-bottom: 8px;
    }
}

@media (max-width: 980px) {
    .m2030-footer-final__top {
        grid-template-columns: 1fr !important;
        gap: 24px !important;
    }

    .m2030-footer-final__newsletter {
        max-width: 680px;
        width: 100%;
        margin: 0 auto;
    }

    .m2030-footer-final__nav {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 28px 0;
    }

    .m2030-footer-final__nav section:nth-child(2)::after {
        display: none;
    }
}

@media (max-width: 640px) {
    .m2030-footer-final__inner {
        padding: 28px 18px 14px;
    }

    .m2030-footer-final__logo {
        width: 165px;
    }

    .m2030-footer-final__form {
        grid-template-columns: 1fr;
    }

    .m2030-footer-final__nav {
        grid-template-columns: 1fr !important;
    }

    .m2030-footer-final__nav section::after {
        display: none !important;
    }

    .m2030-footer-final__social {
        gap: 10px;
        flex-wrap: wrap;
    }

    .m2030-footer-final__social a,
    .m2030-footer-final__social-link {
        width: 42px;
        height: 42px;
    }
}

/* FOOTER FINAL FUNCTIONAL FIX — Social icons and newsletter form */

.m2030-footer-final__social-link {
    width: 52px;
    height: 52px;
    border-radius: 999px;
    border: 1px solid rgba(218, 177, 76, 0.78);
    color: #fff;
    background: rgba(60, 0, 14, 0.22);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: color 0.15s ease, background 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
}

.m2030-footer-final__social-link svg {
    width: 20px;
    height: 20px;
    display: block;
    fill: currentColor;
}

.m2030-footer-final__social-link:hover,
.m2030-footer-final__social-link:focus-visible {
    color: #3a000b;
    background: linear-gradient(135deg, #f1d46e, #c99a2d);
    border-color: rgba(241, 212, 110, 0.95);
    outline: none;
    transform: translateY(-1px);
}

.m2030-footer-final__input:focus-within {
    border-color: rgba(241, 212, 110, 0.95);
    box-shadow: 0 0 0 3px rgba(241, 212, 110, 0.18);
}

.m2030-footer-final__form button {
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    cursor: pointer;
}

.m2030-footer-final__form button:hover,
.m2030-footer-final__form button:focus-visible {
    transform: translateY(-1px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.22);
    outline: none;
}

.m2030-footer-final__notice {
    margin: 12px 0 0;
    font-size: 13px;
    line-height: 1.4;
    font-weight: 700;
}

.m2030-footer-final__notice--success {
    color: #d8ffe3;
}

.m2030-footer-final__notice--error {
    color: #ffd6d6;
}

/* DESIGN HELL PHASE 7.3 — Homepage quick access gap fix */

.page-home .home-final-hero + .home-final-quick.home-final-quick--official {
    position: relative;
    z-index: 5;
    margin-top: clamp(-52px, -4vw, -28px);
    margin-bottom: 22px;
    padding-top: 0;
    padding-inline: 0;
    min-height: 0;
}

.page-home .home-final-quick.home-final-quick--official .home-final-quick__grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 0.72rem;
    width: 100%;
    min-width: 0;
    min-height: 0;
    overflow: visible;
}

.page-home .home-final-quick.home-final-quick--official .home-final-quick-card.home-final-quick-card--compact {
    flex: unset;
    min-width: 0;
    max-width: none;
    width: auto;
    min-height: 86px;
    max-height: 96px;
    height: auto;
    scroll-snap-align: unset;
}

.page-home .home-final-quick.home-final-quick--official + .home-final-grid {
    margin-top: 0;
}

.page-home .home-final-grid {
    margin-top: 0;
}

@media (max-width: 1279px) and (min-width: 1024px) {
    .page-home .home-final-quick.home-final-quick--official .home-final-quick__grid {
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.62rem;
    }

    .page-home .home-final-quick.home-final-quick--official .home-final-quick-card.home-final-quick-card--compact {
        min-height: 82px;
        max-height: 92px;
    }
}

@media (max-width: 1023px) and (min-width: 768px) {
    .page-home .home-final-quick.home-final-quick--official .home-final-quick__grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.68rem;
    }
}

@media (max-width: 767px) and (min-width: 640px) {
    .page-home .home-final-quick.home-final-quick--official .home-final-quick__grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.65rem;
    }
}

@media (max-width: 639px) {
    .page-home .home-final-quick.home-final-quick--official {
        margin-top: clamp(-40px, -6vw, -24px);
        margin-bottom: 18px;
    }

    .page-home .home-final-quick.home-final-quick--official .home-final-quick__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.62rem;
    }

    .page-home .home-final-quick.home-final-quick--official .home-final-quick-card.home-final-quick-card--compact {
        min-height: 84px;
        max-height: 94px;
    }
}

/* DESIGN HELL PHASE 7.4 — Modern header search overlay */

.search-overlay {
    position: fixed;
    inset: 0;
    z-index: 120;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 110px 16px 24px;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.22s ease, visibility 0.22s ease;
}

.search-overlay.is-open {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.search-overlay[hidden] {
    display: none !important;
}

.search-overlay:not([hidden]) {
    display: flex;
}

.search-overlay__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(10, 10, 14, 0.55);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.search-overlay__panel {
    position: relative;
    z-index: 1;
    width: min(720px, calc(100% - 32px));
    padding: 1.15rem 1.2rem 1.05rem;
    border: 1px solid rgba(201, 164, 76, 0.42);
    border-radius: 26px;
    background:
        radial-gradient(circle at 100% 0%, rgba(201, 164, 76, 0.12), transparent 34%),
        linear-gradient(180deg, #fffdf8 0%, #fff 100%);
    box-shadow:
        0 28px 60px rgba(2, 6, 13, 0.28),
        0 0 0 1px rgba(177, 15, 46, 0.06);
    transform: translateY(-12px) scale(0.985);
    transition: transform 0.24s ease;
}

.search-overlay.is-open .search-overlay__panel {
    transform: translateY(0) scale(1);
}

.search-overlay__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.85rem;
}

.search-overlay__label {
    margin: 0;
    color: #8f0f1f;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.16em;
    text-transform: uppercase;
}

.search-overlay__close {
    display: inline-grid;
    place-items: center;
    width: 2rem;
    height: 2rem;
    padding: 0;
    border: 1px solid rgba(20, 28, 39, 0.1);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.92);
    color: #141c27;
    cursor: pointer;
    transition: border-color 0.16s ease, color 0.16s ease, background 0.16s ease;
}

.search-overlay__close svg {
    width: 1rem;
    height: 1rem;
}

.search-overlay__close:hover,
.search-overlay__close:focus-visible {
    border-color: rgba(177, 15, 46, 0.35);
    color: #b10f2e;
    background: #fff8f0;
    outline: none;
}

.search-overlay__form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 0.55rem;
    align-items: stretch;
}

.search-overlay__input {
    width: 100%;
    min-width: 0;
    padding: 0.82rem 0.95rem;
    border: 1px solid rgba(20, 28, 39, 0.12);
    border-radius: 14px;
    background: #fff;
    color: #141c27;
    font-size: 0.95rem;
    line-height: 1.3;
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
    transition: border-color 0.16s ease, box-shadow 0.16s ease;
}

.search-overlay__input::placeholder {
    color: #8a96a8;
}

.search-overlay__input:focus,
.search-overlay__input:focus-visible {
    border-color: rgba(201, 164, 76, 0.72);
    box-shadow: 0 0 0 3px rgba(201, 164, 76, 0.18);
    outline: none;
}

.search-overlay__submit {
    padding: 0.82rem 1.15rem;
    border: 1px solid rgba(201, 164, 76, 0.55);
    border-radius: 14px;
    background: linear-gradient(135deg, #e12442 0%, #b10f2e 52%, #8f0f1f 100%);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    cursor: pointer;
    white-space: nowrap;
    box-shadow: 0 10px 22px rgba(177, 15, 46, 0.24);
    transition: transform 0.16s ease, box-shadow 0.16s ease;
}

.search-overlay__submit:hover,
.search-overlay__submit:focus-visible {
    transform: translateY(-1px);
    box-shadow: 0 14px 26px rgba(177, 15, 46, 0.28);
    outline: none;
}

.search-overlay__quick {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    margin-top: 0.85rem;
    padding-top: 0.75rem;
    border-top: 1px solid rgba(20, 28, 39, 0.08);
}

.search-overlay__quick-link {
    display: inline-flex;
    align-items: center;
    padding: 0.34rem 0.62rem;
    border: 1px solid rgba(20, 28, 39, 0.08);
    border-radius: 999px;
    background: rgba(250, 251, 253, 0.96);
    color: #334155;
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-decoration: none;
    text-transform: uppercase;
    transition: border-color 0.16s ease, color 0.16s ease, background 0.16s ease;
}

.search-overlay__quick-link:hover,
.search-overlay__quick-link:focus-visible {
    border-color: rgba(177, 15, 46, 0.24);
    color: #b10f2e;
    background: #fff8f0;
    outline: none;
}

body.search-overlay-open {
    overflow: hidden;
}

@media (max-width: 639px) {
    .search-overlay {
        align-items: flex-end;
        padding: 0 10px calc(12px + env(safe-area-inset-bottom, 0px));
    }

    .search-overlay__panel {
        width: calc(100% - 20px);
        padding: 0.95rem 0.95rem 0.85rem;
        border-radius: 22px 22px 18px 18px;
    }

    .search-overlay__form {
        grid-template-columns: 1fr;
    }

    .search-overlay__submit {
        width: 100%;
    }

    .search-overlay__quick {
        gap: 0.38rem;
    }

    .search-overlay__quick-link {
        font-size: 0.58rem;
    }
}

/* Public account recovery polish */
body.page-account {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

body.page-account .site-header {
    position: relative;
    top: auto;
    z-index: 80;
    flex: 0 0 auto;
}

body.page-account main#main-content {
    position: relative;
    z-index: 0;
    flex: 1 0 auto;
    display: block;
    padding: clamp(0.75rem, 1.4vw, 1rem) 0 clamp(3rem, 5vw, 5rem);
    overflow: visible;
}

body.page-account .page-container {
    position: relative;
    z-index: 0;
    width: min(calc(100% - 42px), 1210px);
    margin-inline: auto;
}

body.page-account .flash-stack--account {
    position: relative;
    z-index: 1;
}

body.page-account .page-header {
    position: relative;
    z-index: 1;
    clear: both;
    margin-top: 0;
}

body.page-account .account-layout {
    position: relative;
    z-index: 1;
    clear: both;
    display: grid;
    grid-template-columns: minmax(16rem, 18.5rem) minmax(0, 1fr);
    align-items: start;
    gap: clamp(1rem, 2vw, 1.4rem);
    margin-top: 0;
    padding-block: clamp(1rem, 2vw, 1.6rem);
}

body.page-account .account-sidebar {
    position: sticky;
    top: 1rem;
    z-index: 2;
    align-self: start;
    min-width: 0;
    max-width: 100%;
}

body.page-account .account-panel,
body.page-account .account-overview-card {
    position: relative;
    z-index: 1;
    min-width: 0;
    max-width: 100%;
}

body.page-account .account-nav {
    min-width: 0;
    max-width: 100%;
}

.site-header .header-auth__account {
    width: auto;
    max-width: min(13rem, 24vw);
    min-width: 0;
    height: 2.125rem;
    padding: 0.22rem 0.72rem 0.22rem 0.28rem;
    justify-content: flex-start;
    gap: 0.45rem;
    overflow: hidden;
    white-space: nowrap;
    flex-shrink: 1;
}

.site-header .header-auth__account-name {
    display: block;
    min-width: 0;
    max-width: 9.4rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    line-height: 1;
    word-break: normal;
    overflow-wrap: normal;
}

.site-header .header-auth__account-mark {
    flex: 0 0 auto;
}

.flash-stack--account {
    gap: 0.45rem;
    margin: 0 0 0.85rem;
}

.flash-stack--account .flash-banner {
    min-height: 0;
    padding: 0.56rem 0.76rem;
    border-radius: 10px;
    font-size: 0.84rem;
    line-height: 1.35;
    box-shadow: none;
}

.page-inner .account-menu-card {
    padding: clamp(1rem, 1.8vw, 1.2rem);
}

.page-inner .account-kicker {
    display: inline-flex;
    align-items: center;
    width: fit-content;
    margin-bottom: 0.35rem;
    color: #8f0f1f;
    font-size: 0.64rem;
    font-weight: 900;
    letter-spacing: 0.11em;
    text-transform: uppercase;
}

.page-inner .account-overview-card {
    display: grid;
    grid-template-columns: minmax(0, 1.35fr) minmax(15rem, 0.95fr) auto;
    align-items: center;
    gap: clamp(0.85rem, 1.8vw, 1.25rem);
    padding: clamp(1rem, 2vw, 1.35rem);
    background:
        linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(255, 248, 240, 0.94)),
        radial-gradient(circle at 100% 0%, rgba(201, 164, 76, 0.16), transparent 34%);
}

.page-inner .account-identity {
    display: flex;
    align-items: center;
    min-width: 0;
    gap: 0.8rem;
}

.page-inner .account-avatar {
    display: inline-grid;
    place-items: center;
    width: 3.25rem;
    height: 3.25rem;
    flex: 0 0 auto;
    border: 1px solid rgba(201, 164, 76, 0.38);
    border-radius: 999px;
    background: linear-gradient(135deg, #c9a44c, #b10f2e);
    color: #fff;
    font-size: 1.15rem;
    font-weight: 900;
}

.page-inner .account-identity h2 {
    margin: 0;
    font-size: clamp(1.25rem, 2vw, 1.55rem);
    line-height: 1.1;
}

.page-inner .account-identity p {
    margin: 0.28rem 0 0;
    color: #64748b;
    font-size: 0.9rem;
    overflow-wrap: anywhere;
}

.page-inner .account-meta-list {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.55rem;
    margin: 0;
}

.page-inner .account-meta-list div {
    min-width: 0;
    padding: 0.66rem 0.7rem;
    border: 1px solid rgba(20, 28, 39, 0.08);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.78);
}

.page-inner .account-meta-list dt {
    margin: 0 0 0.26rem;
    color: #64748b;
    font-size: 0.6rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.page-inner .account-meta-list dd {
    margin: 0;
    color: #141c27;
    font-size: 0.84rem;
    font-weight: 800;
}

.page-inner .account-quick-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 0.45rem;
}

@media (max-width: 1100px) {
    .page-inner .account-overview-card {
        grid-template-columns: 1fr;
        align-items: stretch;
    }

    .page-inner .account-quick-actions {
        justify-content: flex-start;
    }
}

@media (max-width: 1024px) {
    body.page-account .account-layout {
        grid-template-columns: 1fr;
    }

    body.page-account .account-sidebar {
        position: static;
        z-index: auto;
    }

    body.page-account .account-menu-card {
        overflow: hidden;
    }

    body.page-account .account-nav {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
    }
}

@media (max-width: 760px) {
    body.page-account .page-container {
        width: min(calc(100% - 28px), 1210px);
    }

    .site-header .header-auth__account {
        width: 2.08rem;
        max-width: 2.08rem;
        height: 2.08rem;
        padding: 0;
        justify-content: center;
        flex-shrink: 0;
    }

    .site-header .header-auth__account-name {
        display: none;
    }

    .site-header .header-auth__account-mark {
        width: 100%;
        height: 100%;
    }

    .page-inner .account-meta-list {
        grid-template-columns: 1fr;
    }
}
