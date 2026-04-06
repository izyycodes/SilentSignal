// ============================================================
// FAMILY CHECK-IN - Full Functional JS
// ============================================================

// ── State ──
let gpsWatchId = null;
let isSendingStatus = false;

// ── Leaflet map state ──
let familyMap = null;
let pwdMarker = null;

// Mock family member coordinates (offset slightly from PWD location)
// These will be placed relative to the PWD's actual GPS once acquired
const FAMILY_MOCK_OFFSETS = [
    { name: 'Maria (Mother)',   latOffset:  0.003, lngOffset:  0.005, color: '#e91e63' },
    { name: 'Jose (Father)',    latOffset: -0.004, lngOffset:  0.002, color: '#ff9800' },
    { name: 'Ana (Sister)',     latOffset:  0.001, lngOffset: -0.006, color: '#9c27b0' },
    { name: 'Carlos (Brother)', latOffset: -0.002, lngOffset: -0.003, color: '#4caf50' },
];
let familyMarkers = [];

// ── Init GPS on load ──
document.addEventListener('DOMContentLoaded', function () {
    startGPS();
    // Auto-refresh family status every 60 seconds
    setInterval(refreshFamilyStatus, 60000);
});

// ── GPS ──
function startGPS() {
    if (!navigator.geolocation) {
        document.getElementById('gpsAddressText').textContent = 'GPS not supported on this device.';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function (pos) {
            currentLat = pos.coords.latitude;
            currentLng = pos.coords.longitude;
            updateGPSDisplay(currentLat, currentLng);
            updateFamilyMap(currentLat, currentLng);
        },
        function (err) {
            document.getElementById('gpsAddressText').textContent = 'GPS unavailable (' + err.message + ')';
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );

    // Watch for updates
    gpsWatchId = navigator.geolocation.watchPosition(
        function (pos) {
            currentLat = pos.coords.latitude;
            currentLng = pos.coords.longitude;
            updateGPSDisplay(currentLat, currentLng);
            updateFamilyMap(currentLat, currentLng);
        },
        null,
        { enableHighAccuracy: true }
    );
}

function updateGPSDisplay(lat, lng) {
    const addrEl = document.getElementById('gpsAddressText');
    const timeEl = document.getElementById('gpsUpdateTime');
    if (addrEl) addrEl.textContent = 'Lat: ' + lat.toFixed(6) + ', Lng: ' + lng.toFixed(6);
    if (timeEl) timeEl.textContent = 'Updated just now';
}

// ── Leaflet Family Map ──
function updateFamilyMap(lat, lng) {
    const placeholder = document.getElementById('familyMapPlaceholder');
    const viewMapBtn  = document.getElementById('familyLocationLink');

    if (!familyMap) {
        // First time: initialise map
        familyMap = L.map('familyMiniMap', {
            center: [lat, lng],
            zoom: 15,
            zoomControl: true,
            attributionControl: false,
            scrollWheelZoom: false,
            doubleClickZoom: false,
            touchZoom: true,
            keyboard: false,
            boxZoom: false,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(familyMap);

        // PWD "You are here" icon
        const pwdIcon = L.divIcon({
            className: '',
            html: `<div style="
                background:#e53935;
                width:16px;height:16px;
                border-radius:50%;
                border:3px solid #fff;
                box-shadow:0 0 0 3px rgba(229,57,53,0.35),0 2px 6px rgba(0,0,0,0.4);
                position:relative;">
                <div style="
                    position:absolute;
                    bottom:20px;left:50%;transform:translateX(-50%);
                    white-space:nowrap;
                    background:rgba(26,77,127,0.92);
                    color:#fff;
                    font-size:10px;font-weight:600;
                    font-family:'Poppins',sans-serif;
                    padding:3px 7px;
                    border-radius:20px;
                    box-shadow:0 2px 6px rgba(0,0,0,0.25);">
                    📍 You are here
                </div>
            </div>`,
            iconSize: [16, 16],
            iconAnchor: [8, 8],
        });

        pwdMarker = L.marker([lat, lng], { icon: pwdIcon })
            .addTo(familyMap)
            .bindPopup('<b>You (PWD)</b><br>Your current location');

        // Add mock family member markers
        familyMarkers = [];
        FAMILY_MOCK_OFFSETS.forEach(function(member) {
            const mLat = lat + member.latOffset;
            const mLng = lng + member.lngOffset;

            const famIcon = L.divIcon({
                className: '',
                html: `<div style="
                    background:${member.color};
                    width:13px;height:13px;
                    border-radius:50%;
                    border:2px solid #fff;
                    box-shadow:0 0 0 2px ${member.color}55,0 2px 5px rgba(0,0,0,0.35);">
                </div>`,
                iconSize: [13, 13],
                iconAnchor: [6, 6],
            });

            const marker = L.marker([mLat, mLng], { icon: famIcon })
                .addTo(familyMap)
                .bindPopup(`<b>${member.name}</b><br><span style="color:#666;font-size:11px;">Family member</span>`);

            familyMarkers.push(marker);
        });

        // Fit map to show all markers
        const allLatLngs = [[lat, lng]].concat(
            FAMILY_MOCK_OFFSETS.map(m => [lat + m.latOffset, lng + m.lngOffset])
        );
        familyMap.fitBounds(L.latLngBounds(allLatLngs), { padding: [24, 24] });

        // Hide placeholder
        if (placeholder) placeholder.classList.add('hidden');

    } else {
        // Subsequent GPS updates: move PWD marker + re-fit
        pwdMarker.setLatLng([lat, lng]);
        familyMap.setView([lat, lng], familyMap.getZoom(), { animate: true });
        if (placeholder) placeholder.classList.add('hidden');
    }

    // Show & update "View Full Map" button
    if (viewMapBtn) {
        viewMapBtn.href = 'https://www.google.com/maps?q=' + lat + ',' + lng;
        viewMapBtn.style.display = 'inline-flex';
    }
}


// ── Set Status ──
function setStatus(type, el) {
    if (isSendingStatus) return;
    isSendingStatus = true;

    el.parentElement.querySelectorAll('.status-btn').forEach(b => b.classList.remove('chosen'));
    el.classList.add('chosen', 'loading');
    addRipple(el, type === 'safe' ? 'rgba(46,125,50,.25)' : 'rgba(198,40,40,.25)');

    if (navigator.vibrate) navigator.vibrate(50);

    fetch(BASE_URL + 'index.php?action=update-checkin-status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            status: type,
            latitude: currentLat,
            longitude: currentLng,
            message: type === 'safe' ? "I'm safe." : "I need help!"
        })
    })
    .then(r => r.json())
    .then(data => {
        el.classList.remove('loading');
        isSendingStatus = false;
        if (data.success) {
            const info = document.getElementById('statusCurrentInfo');
            if (info) info.innerHTML = '<span class="status-last-updated"><i class="ri-time-line"></i> Last updated: Just now</span>';
            showToast(
                type === 'safe' ? '✅ Status updated – You are Safe!' : '🚨 Help request sent to your family!',
                type === 'safe' ? '#2e7d32' : '#c62828'
            );
        } else {
            showToast('⚠️ Could not update status. Try again.', '#d84315');
        }
    })
    .catch(() => {
        el.classList.remove('loading');
        isSendingStatus = false;
        showToast('⚠️ Network error. Try again.', '#d84315');
    });
}

// ── GPS Pin Tap (open maps) ──
function gpsTap(e, btn) {
    addRipple(btn, 'rgba(255,255,255,.4)');
    if (navigator.vibrate) navigator.vibrate(30);

    if (currentLat && currentLng) {
        const url = 'https://www.google.com/maps?q=' + currentLat + ',' + currentLng;
        window.open(url, '_blank');
    } else {
        showToast('📍 Getting GPS location...', '#1A4D7F');
    }
}

// ── Media Capture ──
function mediaTap(btn, type) {
    btn.classList.add('flash');
    btn.addEventListener('animationend', () => btn.classList.remove('flash'), { once: true });
    if (navigator.vibrate) navigator.vibrate(40);

    const input = document.getElementById(type === 'photo' ? 'cameraInputPhoto' : 'cameraInputVideo');
    if (input) {
        input.click();
    } else {
        showToast(type === 'photo' ? '📸 Photo captured & GPS tagged!' : '🎥 Video recording started!', '#7b1fa2');
        logMedia(type);
    }
}

function handleMediaCapture(input, type) {
    if (input.files && input.files[0]) {
        showToast(
            type === 'photo' ? '📸 Photo captured & GPS tagged!' : '🎥 Video captured!',
            '#7b1fa2'
        );
        logMedia(type);
        input.value = '';
    }
}

function logMedia(type) {
    fetch(BASE_URL + 'index.php?action=log-checkin-media', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type, latitude: currentLat, longitude: currentLng })
    }).catch(() => {});
}

// ── Refresh Family Status ──
function refreshFamilyStatus() {
    fetch(BASE_URL + 'index.php?action=get-family-status')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.familyStatuses) {
                renderFamilyList(data.familyStatuses);
            }
        })
        .catch(() => {});
}

function renderFamilyList(members) {
    const list = document.getElementById('familyList');
    if (!list) return;

    if (members.length === 0) {
        list.innerHTML = '<div class="empty-family"><i class="ri-group-line"></i><p>No emergency contacts added yet.</p><a href="' + BASE_URL + 'index.php?action=medical-profile#emergency-contacts" class="link-btn">Add Emergency Contacts →</a></div>';
        return;
    }

    list.innerHTML = '';
    members.forEach(m => {
        const item = document.createElement('div');
        item.className = 'family-item';
        item.dataset.memberId = m.id;
        const phoneHtml = m.phone_number ? `&nbsp;•&nbsp;<i class="ri-phone-line"></i> ${escHtml(m.phone_number)}` : '';
        const unregTag  = !m.is_registered ? '<span class="unregistered-tag">Not registered</span>' : '';
        item.innerHTML = `
<div class="family-avatar-wrap" style="background-color:${escHtml(m.color)}">${escHtml(m.initials)}</div>
<div class="family-info">
  <div class="name">${escHtml(m.display_name)} ${unregTag}</div>
  <div class="loc"><i class="ri-account-circle-line"></i> ${escHtml(m.relationship_type || 'Emergency Contact')}${phoneHtml}</div>
</div>
<div class="family-status-right">
  <div class="family-status-badge ${escHtml(m.status_class)}">${escHtml(m.status_label)}</div>
  <div class="family-time"><i class="ri-time-line"></i> ${escHtml(m.time_ago)}</div>
</div>`;
        list.appendChild(item);
    });
}

// ── Breadcrumbs ──
function breadcrumbTap(e, btn) {
    addRipple(btn, 'rgba(255,255,255,.35)');
    if (navigator.vibrate) navigator.vibrate(30);

    const list = document.getElementById('breadcrumbList');
    const isVisible = list.style.display !== 'none';

    if (isVisible) {
        list.style.display = 'none';
        btn.innerHTML = '<i class="ri-route-line"></i> View Location Breadcrumbs';
        return;
    }

    btn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Loading...';

    fetch(BASE_URL + 'index.php?action=get-location-history')
        .then(r => r.json())
        .then(data => {
            btn.innerHTML = '<i class="ri-route-line"></i> Hide Breadcrumbs';
            list.style.display = 'block';
            if (data.success && data.history.length > 0) {
                list.innerHTML = data.history.map((h, i) => `
<div class="breadcrumb-item">
  <div class="breadcrumb-dot ${escHtml(h.status || 'unknown')}"></div>
  <div class="breadcrumb-info">
    <div class="breadcrumb-status">${escHtml(statusLabel(h.status))}</div>
    <div class="breadcrumb-coords">${h.latitude ? 'Lat: ' + parseFloat(h.latitude).toFixed(4) + ', Lng: ' + parseFloat(h.longitude).toFixed(4) : 'No coordinates'}</div>
    <div class="breadcrumb-time"><i class="ri-time-line"></i> ${escHtml(h.time_ago)}</div>
    ${h.message ? '<div class="breadcrumb-msg">' + escHtml(h.message) + '</div>' : ''}
  </div>
  ${h.latitude ? `<a href="https://www.google.com/maps?q=${h.latitude},${h.longitude}" target="_blank" class="breadcrumb-map-btn"><i class="ri-map-pin-line"></i></a>` : ''}
</div>`).join('');
            } else {
                list.innerHTML = '<div class="breadcrumb-empty">No location history found. Update your status to start tracking.</div>';
            }
        })
        .catch(() => {
            btn.innerHTML = '<i class="ri-route-line"></i> View Location Breadcrumbs';
            showToast('⚠️ Could not load history.', '#d84315');
        });
}

function statusLabel(s) {
    const map = { safe: 'Safe', danger: 'Danger', needs_assistance: 'Needs Help', unknown: 'Unknown' };
    return map[s] || 'Unknown';
}

// ── Ripple Helper ──
function addRipple(el, color) {
    const r = document.createElement('span');
    r.className = 'ripple';
    r.style.background = color;
    const rect = el.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    r.style.width = r.style.height = size + 'px';
    r.style.left = (rect.width / 2 - size / 2) + 'px';
    r.style.top  = (rect.height / 2 - size / 2) + 'px';
    el.appendChild(r);
    r.addEventListener('animationend', () => r.remove(), { once: true });
}

// ── Toast ──
let _toastTimer;
function showToast(message, bgColor) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.style.background = bgColor;
    toast.classList.add('show');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => toast.classList.remove('show'), 3000);
}

// ── HTML escape ──
function escHtml(str) {
    if (str == null) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
