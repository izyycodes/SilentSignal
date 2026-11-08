// ============================================================
// FAMILY DASHBOARD — Full Functional JS
// ============================================================

let fabOpen      = false;
let dashRefreshTimer = null;
let currentLat   = null;
let currentLng   = null;

// Leaflet map instance (kept alive between opens)
let _leafletMap     = null;
let _leafletMarkers = [];
let _nearbyPanel    = null;   // Geoapify nearby places + routes (assets/js/geoapify.js)

// ── Init ──
document.addEventListener('DOMContentLoaded', function () {
    startGPS();
    fillAddresses(document);   // turn PWD + alert coordinates into street addresses
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

// ── View PWD Location — Leaflet map showing ALL connected PWDs ──
function viewLocation(focusLat, focusLng, focusName) {
    const modal = document.getElementById('leafletMapModal');
    if (!modal) return;
    modal.style.display = 'flex';
    document.body.classList.add('map-open');

    // Give the DOM a tick to show before initialising Leaflet
    requestAnimationFrame(() => {
        _initOrRefreshLeafletMap(focusLat, focusLng, focusName);
    });
}

function _initOrRefreshLeafletMap(focusLat, focusLng, focusName) {
    const container = document.getElementById('leafletMapContainer');
    if (!container) return;

    // Build the list of all PWD members with valid coordinates
    const validPwds = (pwdMembersData || []).filter(p =>
        p.latitude && p.longitude &&
        parseFloat(p.latitude) !== 0 && parseFloat(p.longitude) !== 0
    );

    // --- First-time initialisation ---
    if (!_leafletMap) {
        _leafletMap = L.map('leafletMapContainer', {
            zoomControl: true,
            attributionControl: true,
        });

        // Map tiles from Geoapify (falls back to OpenStreetMap if the key is missing)
        SSGeo.addTiles(_leafletMap);

        _nearbyPanel = new SSGeo.NearbyPanel({
            map:      _leafletMap,
            chipsEl:  document.getElementById('familyChips'),
            listEl:   document.getElementById('familyList'),
            routeEl:  document.getElementById('familyRoute'),
            showCall: true,
        });
    } else {
        // Clear existing markers
        _leafletMarkers.forEach(m => _leafletMap.removeLayer(m));
        _leafletMarkers = [];
        _nearbyPanel.clearRoute();
    }

    // Notify Leaflet that its container may have changed size
    _leafletMap.invalidateSize();

    if (validPwds.length === 0) {
        // No GPS data — show a world view and a notice
        _leafletMap.setView([14.0, 121.0], 6); // Default: Philippines
        _showMapNotice('No GPS coordinates available yet for any PWD member.');
        _updateMapLegend([]);
        return;
    }

    _hideMapNotice();

    // Status colour mapping
    const statusColors = {
        safe:             '#4caf50',
        danger:           '#f44336',
        needs_assistance: '#ff9800',
        unknown:          '#9e9e9e',
    };

    const bounds = [];

    validPwds.forEach(pwd => {
        const lat    = parseFloat(pwd.latitude);
        const lng    = parseFloat(pwd.longitude);
        const status = pwd.status || 'unknown';
        const color  = statusColors[status] || '#9e9e9e';
        const isFocus = (pwd.name === focusName);

        // Custom SVG pin marker
        const svgIcon = L.divIcon({
            className: '',
            html: `
                <div class="pwd-map-pin ${isFocus ? 'pwd-map-pin--focus' : ''}" style="--pin-color:${color};">
                    <div class="pwd-map-pin__bubble">
                        <span class="pwd-map-pin__initials">${_initials(pwd.name)}</span>
                    </div>
                    <div class="pwd-map-pin__tail"></div>
                    ${isFocus ? '<div class="pwd-map-pin__pulse"></div>' : ''}
                </div>`,
            iconSize:   [44, 56],
            iconAnchor: [22, 56],
            popupAnchor:[0, -58],
        });

        const statusLabel = {
            safe:             'Safe',
            danger:           'Danger',
            needs_assistance: 'Needs Help',
            unknown:          'Unknown',
        }[status] || 'Unknown';

        const batteryText = pwd.battery != null && pwd.battery !== '' && pwd.battery !== '—'
            ? `${pwd.battery}%` : '—';

        // Popup is built each time it opens so it always shows the latest address
        const popupHtml = () => `
            <div class="pwd-map-popup">
                <div class="pwd-map-popup__header" style="background:${color};">
                    <span class="pwd-map-popup__name">${escHtml(pwd.name)}</span>
                    <span class="pwd-map-popup__status">${statusLabel}</span>
                </div>
                <div class="pwd-map-popup__body">
                    <div class="pwd-map-popup__row">
                        <i class="ri-map-pin-line"></i>
                        <span id="pwd-addr-${pwd.id}" title="${lat.toFixed(5)}, ${lng.toFixed(5)}">${escHtml(_pwdAddr[pwd.id] || (lat.toFixed(5) + ', ' + lng.toFixed(5)))}</span>
                    </div>
                    <div class="pwd-map-popup__row">
                        <i class="ri-battery-2-charge-line"></i>
                        <span>Battery: ${batteryText}</span>
                    </div>
                    <div class="pwd-map-popup__actions">
                        <button class="pwd-map-popup__btn" onclick="routeToPwd(${pwd.id})">
                            <i class="ri-route-line"></i> Route
                        </button>
                        <button class="pwd-map-popup__btn" onclick="showNearbyFor(${pwd.id})">
                            <i class="ri-hospital-line"></i> Nearby
                        </button>
                        <button class="pwd-map-popup__btn" onclick="sendMessage(${pwd.id}, '${escHtml(pwd.name).replace(/'/g,"\\'")}')">
                            <i class="ri-message-2-line"></i> Message
                        </button>
                    </div>
                </div>
            </div>`;

        const marker = L.marker([lat, lng], { icon: svgIcon, zIndexOffset: 1000 })
            .addTo(_leafletMap)
            .bindPopup(popupHtml, { maxWidth: 260, className: 'pwd-leaflet-popup' });

        // Fill in the street address (Geoapify reverse geocoding) when the popup opens
        marker.on('popupopen', () => {
            SSGeo.reverse(lat, lng).then(a => {
                if (!a) return;
                _pwdAddr[pwd.id] = a.short;
                const el = document.getElementById('pwd-addr-' + pwd.id);
                if (el) el.textContent = a.short;
            });
        });

        // Open popup on the focused PWD immediately
        if (isFocus) {
            marker.openPopup();
        }

        _leafletMarkers.push(marker);
        bounds.push([lat, lng]);
    });

    // Fit all markers into view, or zoom to the focused one
    if (focusLat && focusLng && parseFloat(focusLat) !== 0) {
        _leafletMap.setView([focusLat, focusLng], 15);
    } else if (bounds.length === 1) {
        _leafletMap.setView(bounds[0], 15);
    } else if (bounds.length > 1) {
        _leafletMap.fitBounds(bounds, { padding: [40, 40] });
    }

    // Nearby hospitals, clinics, police and fire stations around the focused PWD
    // (or the first PWD when showing everyone)
    const nearbyPwd = validPwds.find(p => p.name === focusName) || validPwds[0];
    _loadNearbyFor(nearbyPwd);

    // Add viewer's own location marker if available
    if (currentLat && currentLng) {
        const youIcon = L.divIcon({
            className: '',
            html: `<div class="you-map-pin"><i class="ri-map-pin-user-fill"></i></div>`,
            iconSize:   [36, 36],
            iconAnchor: [18, 36],
        });
        const youMarker = L.marker([currentLat, currentLng], { icon: youIcon })
            .addTo(_leafletMap)
            .bindPopup('<div style="font-weight:600;font-size:13px;">📍 Your Location</div>');
        _leafletMarkers.push(youMarker);
    }

    _updateMapLegend(validPwds);
}

// ── Nearby help + routes (Geoapify) ──
const _pwdAddr = {};

function _firstName(name) {
    return String(name || '').trim().split(/\s+/)[0] || 'them';
}

function _loadNearbyFor(pwd) {
    if (!pwd || !_nearbyPanel) return;
    const title = document.getElementById('familyNearbyTitle');
    if (title) title.textContent = 'Nearby help · ' + _firstName(pwd.name);
    _nearbyPanel.clearRoute();
    _nearbyPanel.load(pwd.latitude, pwd.longitude, _firstName(pwd.name));
}

// "Nearby" button in a PWD popup
function showNearbyFor(pwdId) {
    const pwd = (pwdMembersData || []).find(p => p.id == pwdId);
    if (!pwd || !_leafletMap) return;
    _leafletMap.closePopup();
    _leafletMap.setView([parseFloat(pwd.latitude), parseFloat(pwd.longitude)], 14);
    _loadNearbyFor(pwd);
}

// "Route" button in a PWD popup: road route from you to the PWD
function routeToPwd(pwdId) {
    const pwd = (pwdMembersData || []).find(p => p.id == pwdId);
    if (!pwd || !_nearbyPanel) return;
    _leafletMap.closePopup();
    if (currentLat == null || currentLng == null) {
        showToast('📍 Turn on your location to see the route to ' + _firstName(pwd.name), '#d84315');
        return;
    }
    _nearbyPanel.routeBetween(
        { lat: currentLat, lng: currentLng },
        { lat: parseFloat(pwd.latitude), lng: parseFloat(pwd.longitude) },
        _firstName(pwd.name)
    );
}

// Replace "Lat/Lng" text with a street address on any element that has
// data-geo-lat / data-geo-lng (PWD cards and alerts)
function fillAddresses(root) {
    if (!window.SSGeo) return;
    (root || document).querySelectorAll('[data-geo-lat]').forEach(el => {
        const lat = parseFloat(el.dataset.geoLat), lng = parseFloat(el.dataset.geoLng);
        if (!lat || !lng) return;
        const key = lat.toFixed(5) + ',' + lng.toFixed(5);
        if (el.dataset.geoDone === key) return;
        SSGeo.reverse(lat, lng).then(a => {
            if (!a) return;
            // Skip if the coordinates changed while we were waiting
            if (parseFloat(el.dataset.geoLat).toFixed(5) + ',' + parseFloat(el.dataset.geoLng).toFixed(5) !== key) return;
            const target = el.querySelector('.geo-addr-text') || el;
            target.textContent = a.short;
            el.title = a.full + ' (' + key.replace(',', ', ') + ')';
            el.dataset.geoDone = key;
        });
    });
}

// ── Map legend ──
function _updateMapLegend(pwds) {
    const legend = document.getElementById('mapLegend');
    if (!legend) return;

    const statusColors = { safe:'#4caf50', danger:'#f44336', needs_assistance:'#ff9800', unknown:'#9e9e9e' };
    const statusLabels = { safe:'Safe', danger:'Danger', needs_assistance:'Needs Help', unknown:'Unknown' };

    if (pwds.length === 0) {
        legend.innerHTML = '<span class="map-legend__item"><span class="map-legend__dot" style="background:#ccc;"></span>No data</span>';
        return;
    }

    // Count statuses
    const counts = {};
    pwds.forEach(p => { const s = p.status || 'unknown'; counts[s] = (counts[s] || 0) + 1; });

    legend.innerHTML = Object.entries(counts).map(([s, n]) =>
        `<span class="map-legend__item">
            <span class="map-legend__dot" style="background:${statusColors[s]||'#9e9e9e'};"></span>
            ${statusLabels[s]||s} (${n})
        </span>`
    ).join('') +
    (currentLat ? `<span class="map-legend__item"><span class="map-legend__dot you-dot"></span> You</span>` : '');
}

function _showMapNotice(msg) {
    const el = document.getElementById('mapNotice');
    if (el) { el.textContent = msg; el.style.display = 'block'; }
}
function _hideMapNotice() {
    const el = document.getElementById('mapNotice');
    if (el) el.style.display = 'none';
}

// ── Close Leaflet Map Modal ──
function closeLeafletMap() {
    const modal = document.getElementById('leafletMapModal');
    if (modal) modal.style.display = 'none';
    document.body.classList.remove('map-open');
}

// ── Send Message (opens SMS app) ──
function sendMessage(pwdId, name) {
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
    <div class="profile-row"><span>Location</span><strong id="profileAddr">${p.latitude ? '…' : '—'}</strong></div>
    <div class="profile-row"><span>Coordinates</span><strong>${p.latitude ? parseFloat(p.latitude).toFixed(4)+', '+parseFloat(p.longitude).toFixed(4) : '—'}</strong></div>
    <div class="profile-row"><span>Battery</span><strong>${p.battery_level != null ? p.battery_level+'%' : '—'}</strong></div>
  </div>
  ${Array.isArray(p.emergency_contacts) && p.emergency_contacts.length ? `
  <div class="profile-section">
    <h4><i class="ri-contacts-line"></i> Emergency Contacts</h4>
    ${p.emergency_contacts.map(c => `<div class="profile-row"><span>${escHtml(c.relationship||'Contact')}</span><strong>${escHtml(c.name)} — ${escHtml(c.phone)} ${c.is_registered ? '<span style="color:#43a047;font-size:10px">✓ Registered</span>' : '<span style="color:#e65100;font-size:10px">Not registered</span>'}</strong></div>`).join('')}
  </div>` : ''}
</div>`;
            if (p.latitude && window.SSGeo) {
                SSGeo.reverse(p.latitude, p.longitude).then(a => {
                    const el = document.getElementById('profileAddr');
                    if (el) el.textContent = a ? a.short : parseFloat(p.latitude).toFixed(4) + ', ' + parseFloat(p.longitude).toFixed(4);
                });
            }
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

// ── Message a co-family member ──
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

            data.pwdStatuses.forEach(p => {
                const card = document.getElementById('pwd-card-' + p.id);
                if (!card) return;

                const statusLabels = {safe:'SAFE',danger:'DANGER',needs_assistance:'NEEDS HELP',unknown:'UNKNOWN'};
                const statusCSS    = {safe:'safe',danger:'danger',needs_assistance:'danger',unknown:'unknown'};

                const banner = card.querySelector('.pwd-status-banner');
                const lblEl  = card.querySelector('.pwd-status-label');
                const updEl  = card.querySelector('.pwd-last-updated');
                const locEl  = card.querySelector('.pwd-location');

                if (banner) banner.className = 'pwd-status-banner status-' + (statusCSS[p.status] || 'unknown');
                if (lblEl)  lblEl.textContent  = statusLabels[p.status] || 'UNKNOWN';
                if (updEl)  updEl.textContent  = p.time_ago;
                if (locEl && p.latitude) {
                    const key = parseFloat(p.latitude).toFixed(5) + ',' + parseFloat(p.longitude).toFixed(5);
                    if (locEl.dataset.geoDone !== key) {
                        // Moved: show coordinates for a moment, then swap in the new address
                        locEl.dataset.geoLat = p.latitude;
                        locEl.dataset.geoLng = p.longitude;
                        if (locEl.dataset.geoDone) {
                            locEl.textContent = 'Lat: ' + parseFloat(p.latitude).toFixed(4) + ', Lng: ' + parseFloat(p.longitude).toFixed(4);
                        }
                        fillAddresses(card);
                    }
                }

                // Sync pwdMembersData for the map
                const idx = pwdMembersData.findIndex(x => x.id == p.id);
                if (idx !== -1) {
                    pwdMembersData[idx].status    = p.status;
                    pwdMembersData[idx].latitude  = p.latitude;
                    pwdMembersData[idx].longitude = p.longitude;
                    pwdMembersData[idx].battery   = p.battery;
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
    t.textContent      = msg;
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

// ── Helper: initials from full name ──
function _initials(name) {
    if (!name) return '?';
    const parts = name.trim().split(/\s+/);
    return (parts[0][0] + (parts[1] ? parts[1][0] : '')).toUpperCase();
}