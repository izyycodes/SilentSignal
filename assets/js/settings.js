// assets/js/settings.js
// Settings page interactivity: toggles, countdown stepper, save via AJAX

(function () {
    'use strict';

    // ── DOM refs ─────────────────────────────────────────────────────────────
    const toggleMfa       = document.getElementById('toggleMfa');
    const mfaNote         = document.getElementById('mfaNote');

    const btnMinus        = document.getElementById('btnMinus');
    const btnPlus         = document.getElementById('btnPlus');
    const countdownValue  = document.getElementById('countdownValue');

    const toggleShake     = document.getElementById('toggleShake');
    const shakeNote       = document.getElementById('shakeNote');

    const toggleAutoInvite = document.getElementById('toggleAutoInvite');
    const inviteNote       = document.getElementById('inviteNote');

    const btnSave         = document.getElementById('btnSaveSettings');
    const saveStatus      = document.getElementById('saveStatus');

    // Current countdown value (initialCountdown is set inline in the view)
    let countdown = typeof initialCountdown !== 'undefined' ? initialCountdown : 10;
    const MIN_COUNTDOWN = 5;
    const MAX_COUNTDOWN = 60;

    // ── Helpers ───────────────────────────────────────────────────────────────
    function updateCountdownDisplay() {
        if (countdownValue) countdownValue.textContent = countdown;
        if (btnMinus) btnMinus.disabled = countdown <= MIN_COUNTDOWN;
        if (btnPlus)  btnPlus.disabled  = countdown >= MAX_COUNTDOWN;
    }

    function toggleNote(note, show) {
        if (!note) return;
        if (show) {
            note.classList.add('visible');
        } else {
            note.classList.remove('visible');
        }
    }

    function showSaveStatus(type, message) {
        if (!saveStatus) return;
        saveStatus.className = 'settings-save-status ' + type;
        saveStatus.innerHTML = (type === 'success')
            ? '<i class="ri-checkbox-circle-fill"></i> ' + message
            : '<i class="ri-error-warning-fill"></i> ' + message;
        setTimeout(() => {
            saveStatus.className = 'settings-save-status';
            saveStatus.innerHTML = '';
        }, 3500);
    }

    // ── MFA Toggle ───────────────────────────────────────────────────────────
    if (toggleMfa) {
        toggleMfa.addEventListener('change', () => {
            toggleNote(mfaNote, toggleMfa.checked);
        });
    }

    // ── Shake Toggle ─────────────────────────────────────────────────────────
    if (toggleShake) {
        toggleShake.addEventListener('change', () => {
            toggleNote(shakeNote, toggleShake.checked);
        });
    }

    // ── Auto-Invite Toggle ───────────────────────────────────────────────────
    if (toggleAutoInvite) {
        toggleAutoInvite.addEventListener('change', () => {
            toggleNote(inviteNote, toggleAutoInvite.checked);
        });
    }

    // ── Countdown stepper ────────────────────────────────────────────────────
    updateCountdownDisplay();

    if (btnMinus) {
        btnMinus.addEventListener('click', () => {
            if (countdown > MIN_COUNTDOWN) {
                countdown--;
                updateCountdownDisplay();
            }
        });
    }

    if (btnPlus) {
        btnPlus.addEventListener('click', () => {
            if (countdown < MAX_COUNTDOWN) {
                countdown++;
                updateCountdownDisplay();
            }
        });
    }

    // ── Save ─────────────────────────────────────────────────────────────────
    if (btnSave) {
        btnSave.addEventListener('click', async () => {
            btnSave.classList.add('saving');
            btnSave.disabled = true;
            const origHtml = btnSave.innerHTML;
            btnSave.innerHTML = '<i class="ri-loader-4-line"></i> Saving…';

            const body = new URLSearchParams({
                mfa_enabled:   toggleMfa       && toggleMfa.checked       ? '1' : '0',
                sos_countdown: String(countdown),
                auto_shake:    toggleShake     && toggleShake.checked     ? '1' : '0',
                auto_invite:   toggleAutoInvite && toggleAutoInvite.checked ? '1' : '0',
            });

            try {
                const res = await fetch(BASE_URL + 'index.php?action=save-settings', {
                    method:  'POST',
                    headers: {
                        'Content-Type':     'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: body.toString(),
                });
                const data = await res.json();

                if (data.success) {
                    showSaveStatus('success', 'Settings saved!');
                } else {
                    showSaveStatus('error', 'Save failed. Try again.');
                }
            } catch (err) {
                console.error('Settings save error:', err);
                showSaveStatus('error', 'Network error. Try again.');
            } finally {
                btnSave.classList.remove('saving');
                btnSave.disabled = false;
                btnSave.innerHTML = origHtml;
            }
        });
    }
})();
