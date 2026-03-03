/**
 * Silent Signal — Dark / Light Mode Toggle
 * Persists user preference in localStorage.
 * Works across all pages (home + dashboard).
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'ss-theme';
    const DARK        = 'dark';
    const LIGHT       = 'light';

    /* ── Apply theme immediately (prevents flash) ── */
    function getPreferred() {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) return stored;
        // Respect OS preference as the initial default
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? DARK : LIGHT;
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(STORAGE_KEY, theme);
    }

    // Apply before the page renders (inline script will handle the first apply —
    // this covers any deferred / late loads)
    applyTheme(getPreferred());

    /* ── Wire up toggle button(s) after DOM ready ── */
    function initToggles() {
        var selectors = '.theme-toggle-btn, .theme-toggle-mobile';
        document.querySelectorAll(selectors).forEach(function (btn) {
            btn.addEventListener('click', function () {
                var current = document.documentElement.getAttribute('data-theme');
                applyTheme(current === DARK ? LIGHT : DARK);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initToggles);
    } else {
        initToggles();
    }

    /* ── React to OS-level changes (only if user hasn't chosen manually) ── */
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
        if (!localStorage.getItem(STORAGE_KEY)) {
            applyTheme(e.matches ? DARK : LIGHT);
        }
    });
})();