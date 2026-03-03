// ============================================================
// DISASTER MONITORING - Full Functional JS
// ============================================================

// ── State ──
let countdownTimer = null;
let countdownValue = 30;
let isPromptActive = true;

// ── Elements ──
const safetyPrompt  = document.getElementById('safetyPrompt');
const btnSafe       = document.getElementById('btnSafe');
const btnHelp       = document.getElementById('btnHelp');
const countdownDisplay = document.getElementById('countdown');

// ── Init ──
document.addEventListener('DOMContentLoaded', function () {

    if (btnSafe) btnSafe.addEventListener('click', respondSafe);
    if (btnHelp) btnHelp.addEventListener('click', respondHelp);

    // Start countdown only if the safety prompt is visible
    if (safetyPrompt && safetyPrompt.style.display !== 'none') {
        startCountdown();
    }
});

// ============================================================
// COUNTDOWN
// ============================================================
function startCountdown() {
    if (!isPromptActive) return;

    countdownTimer = setInterval(function () {
        countdownValue--;

        if (countdownDisplay) {
            countdownDisplay.textContent = countdownValue;
            if (countdownValue <= 10) {
                countdownDisplay.style.color = '#f44336';
                countdownDisplay.style.fontWeight = '700';
            }
        }

        if (countdownValue % 5 === 0 && navigator.vibrate) {
            navigator.vibrate(200);
        }

        if (countdownValue <= 0) {
            clearInterval(countdownTimer);
            triggerAutoSOS();
        }
    }, 1000);
}

// ============================================================
// RESPOND SAFE
// ============================================================
function respondSafe() {
    isPromptActive = false;
    clearInterval(countdownTimer);

    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);

    if (safetyPrompt) {
        safetyPrompt.innerHTML = `
            <div class="safety-response-success">
                <div class="success-icon"><i class="ri-checkbox-circle-fill"></i></div>
                <h2>Thank you!</h2>
                <p>Your response has been recorded. Your family has been notified that you're safe.</p>
            </div>
        `;
        safetyPrompt.style.background = 'linear-gradient(135deg, #4caf50 0%, #388e3c 100%)';
    }

    sendSafetyResponse('safe');

    setTimeout(function () {
        if (safetyPrompt) safetyPrompt.style.display = 'none';
    }, 5000);
}

// ============================================================
// RESPOND HELP
// ============================================================
function respondHelp() {
    isPromptActive = false;
    clearInterval(countdownTimer);

    if (navigator.vibrate) navigator.vibrate([200, 100, 200, 100, 400]);

    const contactCount = typeof emergencyContactsData !== 'undefined' ? emergencyContactsData.length : 0;

    if (safetyPrompt) {
        safetyPrompt.innerHTML = `
            <div class="safety-response-help">
                <div class="help-icon"><i class="ri-alarm-warning-fill"></i></div>
                <h2>SOS SENT!</h2>
                <p>Emergency alert has been sent to your contacts with your GPS location. Help is on the way.</p>
                <div class="sos-details">
                    <span><i class="ri-map-pin-line"></i> Location shared</span>
                    <span><i class="ri-user-heart-line"></i> ${contactCount} contact${contactCount !== 1 ? 's' : ''} notified</span>
                </div>
            </div>
        `;
        safetyPrompt.style.background = 'linear-gradient(135deg, #f44336 0%, #c62828 100%)';
    }

    sendSafetyResponse('help');
}

// ============================================================
// AUTO-SOS
// ============================================================
function triggerAutoSOS() {
    isPromptActive = false;

    if (navigator.vibrate) navigator.vibrate([300, 100, 300, 100, 300, 100, 500]);

    flashScreen();

    const contactCount = typeof emergencyContactsData !== 'undefined' ? emergencyContactsData.length : 0;

    if (safetyPrompt) {
        safetyPrompt.innerHTML = `
            <div class="safety-response-auto">
                <div class="auto-icon"><i class="ri-timer-flash-fill"></i></div>
                <h2>AUTO-SOS ACTIVATED</h2>
                <p>No response detected. Emergency SOS has been automatically sent to your contacts.</p>
                <div class="sos-details">
                    <span><i class="ri-map-pin-line"></i> GPS location shared</span>
                    <span><i class="ri-user-heart-line"></i> ${contactCount} contact${contactCount !== 1 ? 's' : ''} notified</span>
                </div>
            </div>
        `;
        safetyPrompt.style.background = 'linear-gradient(135deg, #ff9800 0%, #f57c00 100%)';
    }

    sendSafetyResponse('auto-sos');
}

// ============================================================
// SEND SAFETY RESPONSE
// ============================================================
function sendSafetyResponse(status) {
    const contacts   = typeof emergencyContactsData !== 'undefined' ? emergencyContactsData : [];
    const phones     = contacts.map(c => (c.phone || '').replace(/\s/g, '')).filter(Boolean).join(',');
    const userName   = typeof userInfoData !== 'undefined' ? (userInfoData.name   || 'Silent Signal User') : 'Silent Signal User';
    const pwdId      = typeof userInfoData !== 'undefined' ? (userInfoData.pwdId  || '') : '';
    const bloodType  = typeof userInfoData !== 'undefined' ? (userInfoData.bloodType || '') : '';

    function buildAndSend(lat, lng) {
        // Log to server — keepalive survives navigation
        fetch(BASE_URL + 'index.php?action=log-disaster-response', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            keepalive: true,
            body: JSON.stringify({
                status:     status,
                latitude:   lat,
                longitude:  lng,
                timestamp:  new Date().toISOString(),
                alert_type: 'disaster_response'
            })
        }).catch(() => {});

        // Only open SMS for help / auto-sos
        if (status === 'safe') return;
        if (!phones)           return;

        const label = status === 'auto-sos' ? 'AUTO-SOS' : 'SOS';
        let smsBody  = `🚨 ${label} - DISASTER EMERGENCY 🚨\n`;
        smsBody     += `From: ${userName}`;
        if (pwdId)      smsBody += ` (PWD ID: ${pwdId})`;
        smsBody     += `\n`;
        if (status === 'auto-sos') smsBody += `⚠️ No response detected — auto alert triggered.\n`;
        smsBody     += lat && lng
            ? `\nLocation: https://maps.google.com/?q=${lat},${lng}`
            : `\nLocation: unavailable`;
        if (bloodType)  smsBody += `\nBlood Type: ${bloodType}`;
        smsBody     += `\n\nThis person is DEAF/MUTE - Please respond via TEXT only.`;

        setTimeout(() => {
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            const sep   = isIOS ? '&' : '?';
            window.location.href = `sms:${phones}${sep}body=${encodeURIComponent(smsBody)}`;
        }, 300);
    }

    // Try GPS but fall back after 3 seconds so SMS always fires
    if (navigator.geolocation) {
        let done = false;

        const fallback = setTimeout(() => {
            if (!done) { done = true; buildAndSend(null, null); }
        }, 3000);

        navigator.geolocation.getCurrentPosition(
            pos => {
                if (!done) {
                    done = true;
                    clearTimeout(fallback);
                    buildAndSend(pos.coords.latitude, pos.coords.longitude);
                }
            },
            () => {
                if (!done) {
                    done = true;
                    clearTimeout(fallback);
                    buildAndSend(null, null);
                }
            },
            { enableHighAccuracy: true, timeout: 3000 }
        );
    } else {
        buildAndSend(null, null);
    }
}

// ============================================================
// FLASH SCREEN
// ============================================================
function flashScreen() {
    const flash = document.createElement('div');
    flash.style.cssText = `
        position: fixed; top: 0; left: 0;
        width: 100%; height: 100%;
        background: #f44336; z-index: 9999;
        animation: flashAnim 0.5s ease;
        pointer-events: none;
    `;
    const style = document.createElement('style');
    style.textContent = `@keyframes flashAnim { 0%,100%{opacity:0} 50%{opacity:1} }`;
    document.head.appendChild(style);
    document.body.appendChild(flash);
    setTimeout(() => { flash.remove(); style.remove(); }, 500);
}

// ============================================================
// ADDITIONAL STYLES
// ============================================================
const additionalStyles = document.createElement('style');
additionalStyles.textContent = `
    .safety-response-success,
    .safety-response-help,
    .safety-response-auto {
        text-align: center;
        padding: 20px;
    }
    .success-icon, .help-icon, .auto-icon {
        width: 80px; height: 80px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px;
        font-size: 40px;
    }
    .safety-response-success h2,
    .safety-response-help h2,
    .safety-response-auto h2 { font-size: 28px; margin: 0 0 10px 0; }
    .safety-response-success p,
    .safety-response-help p,
    .safety-response-auto p {
        font-size: 14px; opacity: 0.9;
        max-width: 350px; margin: 0 auto 20px;
    }
    .sos-details {
        display: flex; justify-content: center;
        gap: 20px; flex-wrap: wrap;
    }
    .sos-details span {
        display: flex; align-items: center; gap: 6px;
        font-size: 13px;
        background: rgba(255,255,255,0.2);
        padding: 8px 15px; border-radius: 20px;
    }
`;
document.head.appendChild(additionalStyles);