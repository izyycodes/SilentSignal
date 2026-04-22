// assets/js/settings.js

(function () {
    'use strict';

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const toggleMfa        = document.getElementById('toggleMfa');
    const mfaNote          = document.getElementById('mfaNote');
    const btnMinus         = document.getElementById('btnMinus');
    const btnPlus          = document.getElementById('btnPlus');
    const countdownValue   = document.getElementById('countdownValue');
    const toggleShake      = document.getElementById('toggleShake');
    const shakeNote        = document.getElementById('shakeNote');
    const toggleAutoInvite = document.getElementById('toggleAutoInvite');
    const inviteNote       = document.getElementById('inviteNote');
    const btnSave          = document.getElementById('btnSaveSettings');
    const saveStatus       = document.getElementById('saveStatus');

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
        note.classList.toggle('visible', show);
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

    // ── Toggle notes ──────────────────────────────────────────────────────────
    if (toggleMfa)        toggleMfa.addEventListener('change',        () => toggleNote(mfaNote,    toggleMfa.checked));
    if (toggleShake)      toggleShake.addEventListener('change',      () => toggleNote(shakeNote,  toggleShake.checked));
    if (toggleAutoInvite) toggleAutoInvite.addEventListener('change', () => toggleNote(inviteNote, toggleAutoInvite.checked));

    // ── Countdown stepper ─────────────────────────────────────────────────────
    updateCountdownDisplay();
    if (btnMinus) btnMinus.addEventListener('click', () => { if (countdown > MIN_COUNTDOWN) { countdown--; updateCountdownDisplay(); } });
    if (btnPlus)  btnPlus.addEventListener('click',  () => { if (countdown < MAX_COUNTDOWN) { countdown++; updateCountdownDisplay(); } });

    // ── Save settings ─────────────────────────────────────────────────────────
    if (btnSave) {
        btnSave.addEventListener('click', async () => {
            btnSave.classList.add('saving');
            btnSave.disabled = true;
            const origHtml = btnSave.innerHTML;
            btnSave.innerHTML = '<i class="ri-loader-4-line"></i> Saving…';

            const body = new URLSearchParams({
                mfa_enabled:   toggleMfa        && toggleMfa.checked        ? '1' : '0',
                sos_countdown: String(countdown),
                auto_shake:    toggleShake       && toggleShake.checked      ? '1' : '0',
                auto_invite:   toggleAutoInvite  && toggleAutoInvite.checked ? '1' : '0',
            });

            try {
                const res  = await fetch(BASE_URL + 'index.php?action=save-settings', {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body:    body.toString(),
                });
                const data = await res.json();
                data.success
                    ? showSaveStatus('success', 'Settings saved!')
                    : showSaveStatus('error', data.message || 'Save failed. Try again.');
            } catch {
                showSaveStatus('error', 'Network error. Try again.');
            } finally {
                btnSave.classList.remove('saving');
                btnSave.disabled  = false;
                btnSave.innerHTML = origHtml;
            }
        });
    }

    // ── Change Password ───────────────────────────────────────────────────────
    window.toggleChangePassword = function () {
        const form     = document.getElementById('changePasswordForm');
        const isHidden = form.style.display === 'none' || form.style.display === '';
        form.style.display = isHidden ? 'block' : 'none';
        if (isHidden) document.getElementById('cpwCurrent').focus();
        document.getElementById('cpwStatus').innerHTML = '';
    };

    window.toggleCpwEye = function (inputId, btn) {
        const input  = document.getElementById(inputId);
        const isPass = input.type === 'password';
        input.type   = isPass ? 'text' : 'password';
        btn.querySelector('i').className = isPass ? 'ri-eye-off-line' : 'ri-eye-line';
    };

    window.submitChangePassword = async function () {
        const current  = document.getElementById('cpwCurrent').value.trim();
        const newPw    = document.getElementById('cpwNew').value.trim();
        const confirm  = document.getElementById('cpwConfirm').value.trim();
        const statusEl = document.getElementById('cpwStatus');

        statusEl.innerHTML = '';

        if (!current || !newPw || !confirm) {
            statusEl.innerHTML = '<span class="cpw-error"><i class="ri-error-warning-line"></i> All fields are required.</span>';
            return;
        }
        if (newPw.length < 6) {
            statusEl.innerHTML = '<span class="cpw-error"><i class="ri-error-warning-line"></i> New password must be at least 6 characters.</span>';
            return;
        }
        if (newPw !== confirm) {
            statusEl.innerHTML = '<span class="cpw-error"><i class="ri-error-warning-line"></i> Passwords do not match.</span>';
            return;
        }

        const btn = document.getElementById('btnChangePassword');
        btn.disabled  = true;
        btn.innerHTML = '<i class="ri-loader-4-line"></i> Updating…';

        try {
            const res  = await fetch(BASE_URL + 'index.php?action=change-password', {
                method:  'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body:    new URLSearchParams({ current_password: current, new_password: newPw, confirm_password: confirm }).toString(),
            });
            const data = await res.json();

            if (data.success) {
                statusEl.innerHTML = '<span class="cpw-success"><i class="ri-checkbox-circle-fill"></i> Password updated successfully!</span>';
                document.getElementById('cpwCurrent').value = '';
                document.getElementById('cpwNew').value     = '';
                document.getElementById('cpwConfirm').value = '';
                setTimeout(() => window.toggleChangePassword(), 2000);
            } else {
                statusEl.innerHTML = '<span class="cpw-error"><i class="ri-error-warning-line"></i> ' + (data.message || 'Failed to update password.') + '</span>';
            }
        } catch {
            statusEl.innerHTML = '<span class="cpw-error"><i class="ri-error-warning-line"></i> Network error. Try again.</span>';
        } finally {
            btn.disabled  = false;
            btn.innerHTML = '<i class="ri-save-line"></i> Update Password';
        }
    };

})();