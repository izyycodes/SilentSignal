// ============================================================
// FAMILY DASHBOARD — Full Functional JS
// ============================================================

let fabOpen      = false;
let dashRefreshTimer = null;
let currentLat   = null;
let currentLng   = null;

// ── Init ──
document.addEventListener('DOMContentLoaded', function () {
    startGPS();
    // Auto-refresh dashboard every 60 seconds
    dashRefreshTimer = setInterval(() => refreshDashboard(null, true), 60000);
});

// ── GPS ──
function startGPS() {
    if (!navigator.geolocation) return;
    navigator.geolocation.getCurrentPosition(
        pos => { currentLat = pos.coords.latitude; currentLng = pos.coords.longitude; },
        null,
        { enableHighAccuracy: true, timeout: 10000 }
    );
    navigator.geolocation.watchPosition(
        pos => { currentLat = pos.coords.latitude; currentLng = pos.coords.longitude; },
        null,
        { enableHighAccuracy: true }
    );
}

// ── Emergency Call ──
function handleEmergencyCall() {
    if (confirm('Call emergency services (911)?')) {
        window.location.href = 'tel:911';
    }
}

// ── View PWD Location (opens Google Maps) ──
function viewLocation(lat, lng, name) {
    if (!lat && !lng) {
        showToast('📍 No GPS coordinates available for ' + name, '#d84315');
        return;
    }
    window.open('https://www.google.com/maps?q=' + lat + ',' + lng, '_blank');
}

// ── Send Message (opens SMS app) ──
function sendMessage(pwdId, name) {
    // Get the PWD's phone number from pwdMembersData
    const pwd = pwdMembersData.find(p => p.id == pwdId);
    const phone = pwd?.phone || pwd?.phone_number || '';

    if (!phone) {
        showToast('⚠️ No phone number available for ' + name, '#d84315');
        return;
    }

    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
    const sep   = isIOS ? '&' : '?';
    const body  = encodeURIComponent('Hi ' + name + ', just checking in on you. Are you safe?');

    window.location.href = 'sms:' + phone + sep + 'body=' + body;
}

// ── View PWD Profile (AJAX modal) ──
function viewProfile(pwdId, name) {
    const modal = document.getElementById('profileModal');
    const body  = document.getElementById('profileModalBody');
    body.innerHTML = '<div class="modal-loading"><i class="ri-loader-4-line ri-spin"></i> Loading profile…</div>';
    modal.style.display = 'flex';

    fetch(BASE_URL + 'index.php?action=get-pwd-profile&pwd_id=' + pwdId)
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                body.innerHTML = '<p class="modal-error">Could not load profile.</p>';
                return;
            }
            const p = data.profile;
            const statusLabels = {safe:'Safe',danger:'Danger',needs_assistance:'Needs Help',unknown:'Unknown'};
            const fullName = escHtml((p.fname || '') + ' ' + (p.lname || ''));
            body.innerHTML = `
<div class="profile-modal-grid">
  <div class="profile-section">
    <h4><i class="ri-user-line"></i> Personal Info</h4>
    <div class="profile-row"><span>Name</span><strong>${fullName}</strong></div>
    <div class="profile-row"><span>Phone</span><strong>${escHtml(p.phone_number || '—')}</strong></div>
    <div class="profile-row"><span>Email</span><strong>${escHtml(p.email || '—')}</strong></div>
    <div class="profile-row"><span>Address</span><strong>${escHtml([p.street_address, p.city, p.province].filter(Boolean).join(', ') || '—')}</strong></div>
  </div>
  <div class="profile-section">
    <h4><i class="ri-heart-pulse-line"></i> Medical Info</h4>
    <div class="profile-row"><span>Disability</span><strong>${escHtml(p.disability_type || '—')}</strong></div>
    <div class="profile-row"><span>Blood Type</span><strong>${escHtml(p.blood_type || '—')}</strong></div>
    <div class="profile-row"><span>Allergies</span><strong>${escHtml(Array.isArray(p.allergies) && p.allergies.length ? p.allergies.join(', ') : '—')}</strong></div>
    <div class="profile-row"><span>Medications</span><strong>${escHtml(Array.isArray(p.medications) && p.medications.length ? p.medications.join(', ') : '—')}</strong></div>
    <div class="profile-row"><span>Conditions</span><strong>${escHtml(Array.isArray(p.medical_conditions) && p.medical_conditions.length ? p.medical_conditions.join(', ') : '—')}</strong></div>
  </div>
  <div class="profile-section">
    <h4><i class="ri-map-pin-line"></i> Current Status</h4>
    <div class="profile-row"><span>Status</span><strong>${escHtml(statusLabels[p.current_status] || 'Unknown')}</strong></div>
    <div class="profile-row"><span>Coordinates</span><strong>${p.latitude ? parseFloat(p.latitude).toFixed(4)+', '+parseFloat(p.longitude).toFixed(4) : '—'}</strong></div>
    <div class="profile-row"><span>Battery</span><strong>${p.battery_level != null ? p.battery_level+'%' : '—'}</strong></div>
  </div>
  ${Array.isArray(p.emergency_contacts) && p.emergency_contacts.length ? `
  <div class="profile-section">
    <h4><i class="ri-contacts-line"></i> Emergency Contacts</h4>
    ${p.emergency_contacts.map(c => `<div class="profile-row"><span>${escHtml(c.relationship||'Contact')}</span><strong>${escHtml(c.name)} — ${escHtml(c.phone)} ${c.is_registered ? '<span style="color:#43a047;font-size:10px">✓ Registered</span>' : '<span style="color:#e65100;font-size:10px">Not registered</span>'}</strong></div>`).join('')}
  </div>` : ''}
</div>`;
        })
        .catch(() => {
            body.innerHTML = '<p class="modal-error">Network error loading profile.</p>';
        });
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

// ── Call family member ──
function callMember(phone) {
    if (confirm('Call ' + phone + '?')) {
        window.location.href = 'tel:' + phone;
    }
}

// ── Message a co-family member (link to communication hub) ──
function messageMember(memberId, name) {
    showToast('💬 Opening communication for ' + name + '…', '#1976d2');
    setTimeout(() => {
        window.location.href = BASE_URL + 'index.php?action=communication-hub';
    }, 500);
}

// ── Respond to Alert ──
function respondToAlert(alertId, responseStatus) {
    const labelMap = {
        acknowledged: 'Acknowledged',
        on_the_way:   "On My Way",
        arrived:      'Arrived',
        resolved:     'Resolved'
    };

    fetch(BASE_URL + 'index.php?action=respond-to-alert', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            alert_id:        alertId,
            response_status: responseStatus,
            latitude:        currentLat,
            longitude:       currentLng
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('✅ Response recorded: ' + (labelMap[responseStatus] || responseStatus), '#2e7d32');
            // Hide the response buttons for this alert
            const alertEl = document.getElementById('alert-' + alertId);
            if (alertEl) {
                const actions = alertEl.querySelector('.alert-response-actions');
                if (actions) actions.style.display = 'none';
                const badge = alertEl.querySelector('.alert-status');
                if (badge) { badge.textContent = labelMap[responseStatus] || responseStatus; badge.className = 'alert-status responded'; }
            }
        } else {
            showToast('⚠️ ' + (data.message || 'Could not record response.'), '#d84315');
        }
    })
    .catch(() => showToast('⚠️ Network error. Try again.', '#d84315'));
}

// ── Refresh dashboard data ──
function refreshDashboard(btn, silent = false) {
    if (btn) { btn.querySelector('i').classList.add('ri-spin'); }

    fetch(BASE_URL + 'index.php?action=refresh-family-dashboard')
        .then(r => r.json())
        .then(data => {
            if (btn) btn.querySelector('i').classList.remove('ri-spin');
            if (!data.success) return;

            // Update each PWD card's status
            data.pwdStatuses.forEach(p => {
                const card = document.getElementById('pwd-card-' + p.id);
                if (!card) return;

                const statusLabels = {safe:'SAFE',danger:'DANGER',needs_assistance:'NEEDS HELP',unknown:'UNKNOWN'};
                const statusCSS    = {safe:'safe',danger:'danger',needs_assistance:'danger',unknown:'unknown'};

                const banner   = card.querySelector('.pwd-status-banner');
                const lblEl    = card.querySelector('.pwd-status-label');
                const updEl    = card.querySelector('.pwd-last-updated');
                const locEl    = card.querySelector('.pwd-location');

                if (banner) {
                    banner.className = 'pwd-status-banner status-' + (statusCSS[p.status] || 'unknown');
                }
                if (lblEl)  lblEl.textContent  = statusLabels[p.status] || 'UNKNOWN';
                if (updEl)  updEl.textContent  = p.time_ago;
                if (locEl && p.latitude) {
                    locEl.textContent = 'Lat: ' + parseFloat(p.latitude).toFixed(4) + ', Lng: ' + parseFloat(p.longitude).toFixed(4);
                }
            });

            if (!silent) showToast('✅ Dashboard refreshed', '#2e7d32');
        })
        .catch(() => {
            if (btn) btn.querySelector('i').classList.remove('ri-spin');
            if (!silent) showToast('⚠️ Could not refresh. Check connection.', '#d84315');
        });
}

// ── Emergency FAB ──
function triggerEmergencyActions() {
    const fabMenu = document.querySelector('.fab-menu');
    fabOpen = !fabOpen;
    if (fabOpen) {
        fabMenu.style.display = 'flex';
        setTimeout(() => fabMenu.classList.add('active'), 10);
    } else {
        fabMenu.classList.remove('active');
        setTimeout(() => fabMenu.style.display = 'none', 300);
    }
}

function callEmergencyServices() {
    if (confirm('Call emergency services (911)?')) {
        window.location.href = 'tel:911';
    }
}

function alertAllFamily() {
    if (!confirm('Send an emergency broadcast alert to all linked family members?')) return;

    fetch(BASE_URL + 'index.php?action=alert-all-family', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: 'Emergency alert from ' + (window.userName || 'a family member') + '. Please check in immediately.' })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('🚨 Alert broadcast sent to all family members!', '#c62828');
        } else {
            showToast('⚠️ ' + (data.message || 'Could not send alert.'), '#d84315');
        }
    })
    .catch(() => showToast('⚠️ Network error.', '#d84315'));

    triggerEmergencyActions();
}

function viewEmergencyContacts() {
    // Scroll to the PWD members section where emergency contacts are shown
    const section = document.getElementById('pwdMembers');
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
        showToast('📋 View PWD profiles for emergency contacts', '#1976d2');
    }
}

// ── Close FAB on outside click ──
document.addEventListener('click', function (e) {
    const fab = document.querySelector('.emergency-fab');
    if (fabOpen && fab && !fab.contains(e.target)) {
        triggerEmergencyActions();
    }
});

// ── Toast ──
let _toastTimer;
function showToast(msg, bg) {
    const t = document.getElementById('toast');
    if (!t) return;
    t.textContent    = msg;
    t.style.background = bg;
    t.classList.add('show');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => t.classList.remove('show'), 3000);
}

// ── HTML escape ──
function escHtml(str) {
    if (str == null) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
