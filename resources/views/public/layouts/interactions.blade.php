<script>
(() => {
    const shell = document.querySelector('[data-public-shell]');

    if (!shell) {
        return;
    }

    const toggles = Array.from(shell.querySelectorAll('[data-shell-toggle]'));
    const panels = Array.from(shell.querySelectorAll('[data-shell-panel]'));
    const closeButtons = Array.from(shell.querySelectorAll('[data-shell-close]'));

    const closePanels = () => {
        panels.forEach((panel) => {
            panel.hidden = true;
            panel.classList.remove('is-open');
        });

        toggles.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', 'false');
        });
    };

    const openPanel = (name, trigger) => {
        const target = shell.querySelector(`[data-shell-panel="${name}"]`);

        if (!target) {
            return;
        }

        const isOpen = !target.hidden;
        closePanels();

        if (isOpen) {
            return;
        }

        target.hidden = false;
        target.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
    };

    toggles.forEach((toggle) => {
        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            openPanel(toggle.dataset.shellToggle, toggle);
        });
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', closePanels);
    });

    panels.forEach((panel) => {
        panel.addEventListener('click', (event) => {
            event.stopPropagation();
        });
    });

    document.addEventListener('click', closePanels);
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closePanels();
        }
    });
})();

(() => {
    const overlay = document.querySelector('[data-search-overlay]');

    if (!overlay) {
        return;
    }

    const shell = document.querySelector('[data-public-shell]');
    const openButtons = Array.from(document.querySelectorAll('[data-search-open]'));
    const closeTriggers = Array.from(overlay.querySelectorAll('[data-search-close]'));
    const panel = overlay.querySelector('[data-search-panel]');
    const input = overlay.querySelector('[data-search-input]');
    let previouslyFocused = null;
    let closeTimer = null;

    const closeShellPanels = () => {
        if (!shell) {
            return;
        }

        shell.querySelectorAll('[data-shell-panel]').forEach((shellPanel) => {
            shellPanel.hidden = true;
            shellPanel.classList.remove('is-open');
        });

        shell.querySelectorAll('[data-shell-toggle]').forEach((toggle) => {
            toggle.setAttribute('aria-expanded', 'false');
        });
    };

    const setOpenButtonsExpanded = (expanded) => {
        openButtons.forEach((button) => {
            button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        });
    };

    const openSearchOverlay = () => {
        if (overlay.classList.contains('is-open')) {
            return;
        }

        closeShellPanels();
        previouslyFocused = document.activeElement;
        window.clearTimeout(closeTimer);
        overlay.hidden = false;
        overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('search-overlay-open');
        setOpenButtonsExpanded(true);

        window.requestAnimationFrame(() => {
            overlay.classList.add('is-open');
            window.setTimeout(() => input?.focus(), 60);
        });
    };

    const closeSearchOverlay = () => {
        if (!overlay.classList.contains('is-open') && overlay.hidden) {
            return;
        }

        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('search-overlay-open');
        setOpenButtonsExpanded(false);

        closeTimer = window.setTimeout(() => {
            if (!overlay.classList.contains('is-open')) {
                overlay.hidden = true;
            }
        }, 220);

        if (previouslyFocused && typeof previouslyFocused.focus === 'function') {
            previouslyFocused.focus();
        }
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();

            if (overlay.classList.contains('is-open')) {
                closeSearchOverlay();
                return;
            }

            openSearchOverlay();
        });
    });

    closeTriggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            closeSearchOverlay();
        });
    });

    panel?.addEventListener('click', (event) => {
        event.stopPropagation();
    });

    overlay.addEventListener('click', (event) => {
        if (event.target === overlay || event.target.classList.contains('search-overlay__backdrop')) {
            closeSearchOverlay();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
            event.stopPropagation();
            closeSearchOverlay();
        }
    }, true);
})();

(() => {
    const carousels = document.querySelectorAll('[data-scroll-carousel]');

    if (!carousels.length) {
        return;
    }

    const updateScrollNav = (track, prevButton, nextButton) => {
        const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
        const canScroll = maxScroll > 1;
        const atStart = track.scrollLeft <= 1;
        const atEnd = track.scrollLeft >= maxScroll - 1;

        [prevButton, nextButton].forEach((button) => {
            if (!button) {
                return;
            }

            button.hidden = !canScroll;
        });

        if (prevButton) {
            prevButton.disabled = !canScroll || atStart;
            prevButton.setAttribute('aria-disabled', !canScroll || atStart ? 'true' : 'false');
        }

        if (nextButton) {
            nextButton.disabled = !canScroll || atEnd;
            nextButton.setAttribute('aria-disabled', !canScroll || atEnd ? 'true' : 'false');
        }
    };

    carousels.forEach((carousel) => {
        const track = carousel.querySelector('[data-scroll-track]');
        const prevButton = carousel.querySelector('[data-scroll-prev]');
        const nextButton = carousel.querySelector('[data-scroll-next]');

        if (!track) {
            return;
        }

        const scrollStep = () => Math.max(Math.round(track.clientWidth * 0.7), 120);

        prevButton?.addEventListener('click', () => {
            track.scrollBy({ left: -scrollStep(), behavior: 'smooth' });
        });

        nextButton?.addEventListener('click', () => {
            track.scrollBy({ left: scrollStep(), behavior: 'smooth' });
        });

        track.addEventListener('scroll', () => {
            updateScrollNav(track, prevButton, nextButton);
        }, { passive: true });

        window.addEventListener('resize', () => {
            updateScrollNav(track, prevButton, nextButton);
        });

        window.setTimeout(() => {
            updateScrollNav(track, prevButton, nextButton);
        }, 60);
    });
})();
</script>
