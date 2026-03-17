// Dashboard Shared JavaScript
// Handles header dropdown and mobile navigation

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================
    // MOBILE NAVIGATION
    // ================================
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileNav = document.getElementById('mobileNav');
    const mobileNavOverlay = document.getElementById('mobileNavOverlay');
    const mobileNavClose = document.getElementById('mobileNavClose');
    
    function openMobileNav() {
        if (mobileNav) mobileNav.classList.add('active');
        if (mobileNavOverlay) mobileNavOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeMobileNav() {
        if (mobileNav) mobileNav.classList.remove('active');
        if (mobileNavOverlay) mobileNavOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openMobileNav);
    if (mobileNavClose) mobileNavClose.addEventListener('click', closeMobileNav);
    if (mobileNavOverlay) mobileNavOverlay.addEventListener('click', closeMobileNav);
    
    // Close mobile nav on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMobileNav();
            closeUserDropdown();
        }
    });
    
    // ================================
    // USER DROPDOWN
    // ================================
    const userDropdown = document.getElementById('userDropdown');
    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    
    function toggleUserDropdown() {
        if (userDropdown) userDropdown.classList.toggle('active');
    }
    
    function closeUserDropdown() {
        if (userDropdown) {
            userDropdown.classList.remove('active');
        }
    }
    
    if (userDropdownBtn) {
        userDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleUserDropdown();
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (userDropdown && !userDropdown.contains(e.target)) {
            closeUserDropdown();
        }
    });
    
    // Close dropdown when clicking a link inside
    if (userDropdownMenu) {
        userDropdownMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeUserDropdown);
        });
    }
    
    // ================================
    // WINDOW RESIZE HANDLER
    // ================================
    window.addEventListener('resize', function() {
        // Close mobile nav if window is resized to desktop
        if (window.innerWidth > 1024) {
            closeMobileNav();
        }
    });
    
    // ================================
    // FLASH MESSAGES
    // ================================
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(message => {
        const autoDismiss = setTimeout(() => dismissMessage(message), 5000);
        const closeBtn = message.querySelector('.flash-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                clearTimeout(autoDismiss);
                dismissMessage(message);
            });
        }
    });
    function dismissMessage(message) {
        message.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => message.remove(), 300);
    }
});

// ================================
// DASHBOARD SOS
// ================================
let dashLat = null;
let dashLng = null;
let dashGpsReady = false;
let dashSosOverlay = null;
let dashToastTimer;

// Start GPS silently on page load
(function initDashGPS() {
    if (!navigator.geolocation) return;
    navigator.geolocation.getCurrentPosition(
        pos => { dashLat = pos.coords.latitude; dashLng = pos.coords.longitude; dashGpsReady = true; },
        null,
        { enableHighAccuracy: true, timeout: 10000 }
    );
    navigator.geolocation.watchPosition(
        pos => { dashLat = pos.coords.latitude; dashLng = pos.coords.longitude; dashGpsReady = true; },
        null,
        { enableHighAccuracy: true }
    );
})();

function dashboardSOS() {
    if (!dashSosOverlay) buildSOSOverlay();
    dashSosOverlay.classList.add('active');
    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
}

function buildSOSOverlay() {
    dashSosOverlay = document.createElement('div');
    dashSosOverlay.className = 'sos-confirm-overlay';
    dashSosOverlay.innerHTML = `
<div class="sos-confirm-box">
  <div class="sos-confirm-icon"><i class="ri-alarm-warning-fill"></i></div>
  <h3>Send SOS Alert?</h3>
  <p>This will send an emergency SMS with your GPS location to all your emergency contacts.</p>
  <div class="sos-confirm-btns">
    <button class="btn-cancel" onclick="cancelDashSOS()">Cancel</button>
    <button class="btn-send"   onclick="confirmDashSOS()"><i class="ri-send-plane-fill"></i> Send SOS</button>
  </div>
</div>`;
    document.body.appendChild(dashSosOverlay);
}

function cancelDashSOS() {
    if (dashSosOverlay) dashSosOverlay.classList.remove('active');
}

function confirmDashSOS() {
    if (dashSosOverlay) dashSosOverlay.classList.remove('active');

    const contacts = typeof DASH_CONTACTS !== 'undefined' ? DASH_CONTACTS : [];
    const user     = typeof DASH_USER     !== 'undefined' ? DASH_USER     : {};

    if (!contacts.length) {
        showDashToast('⚠️ No emergency contacts found. Add contacts in Medical Profile.', '#d84315');
        return;
    }

    const phones = contacts
        .map(c => (c.phone || '').replace(/\s/g, ''))
        .filter(Boolean)
        .join(',');

    if (!phones) {
        showDashToast('⚠️ No valid phone numbers found.', '#d84315');
        return;
    }

    // Build SMS body
    let smsBody = '🚨 EMERGENCY SOS 🚨\n';
    if (user.name) {
        smsBody += `From: ${user.name}`;
        if (user.pwdId) smsBody += ` (PWD ID: ${user.pwdId})`;
        smsBody += '\n';
    }

    const locationStr = dashGpsReady && dashLat
        ? `https://maps.google.com/?q=${dashLat},${dashLng}`
        : (user.address || 'Location unavailable');

    smsBody += `\nLocation: ${locationStr}`;

    if (user.bloodType)   smsBody += `\nBlood Type: ${user.bloodType}`;
    if (user.allergies)   smsBody += `\nAllergies: ${user.allergies}`;
    if (user.medications) smsBody += `\nMedications: ${user.medications}`;
    if (user.conditions)  smsBody += `\nConditions: ${user.conditions}`;

    smsBody += '\n\n⚠️ This person is DEAF/MUTE - Please respond via TEXT only.';

    // Visual feedback on button
    const btn = document.getElementById('sosTriggerBtn');
    if (btn) {
        btn.classList.add('sending');
        btn.innerHTML = `
<div class="sos-pulse-ring"></div>
<div class="sos-pulse-ring sos-pulse-ring-2"></div>
<i class="ri-loader-4-line"></i>
<span>Sending</span>`;
        setTimeout(() => {
            btn.classList.remove('sending');
            btn.classList.add('sent');
            btn.innerHTML = `<i class="ri-check-line"></i><span>Sent!</span>`;
            setTimeout(() => {
                btn.classList.remove('sent');
                btn.innerHTML = `
<div class="sos-pulse-ring"></div>
<div class="sos-pulse-ring sos-pulse-ring-2"></div>
<i class="ri-alarm-warning-fill"></i>
<span>SOS</span>`;
            }, 3000);
        }, 1200);
    }

    if (navigator.vibrate) navigator.vibrate([500, 200, 500]);
    showDashToast('📤 Opening SMS app...', '#2e7d32');

    // Log to server
    fetch((typeof DASH_BASE_URL !== 'undefined' ? DASH_BASE_URL : '') + 'index.php?action=send-hub-sms', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        keepalive: true,
        body: JSON.stringify({
            messages: [{ id: 'sos', title: 'SOS EMERGENCY', desc: 'Immediate emergency assistance needed' }],
            contacts: contacts.map(c => ({ name: c.name, phone: c.phone })),
            latitude: dashLat,
            longitude: dashLng,
            locationLabel: dashGpsReady && dashLat ? `Lat: ${dashLat.toFixed(6)}, Lng: ${dashLng.toFixed(6)}` : null
        })
    }).catch(() => {});

    setTimeout(() => {
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        const sep   = isIOS ? '&' : '?';
        window.location.href = `sms:${phones}${sep}body=${encodeURIComponent(smsBody)}`;
    }, 400);
}

function showDashToast(msg, bg) {
    let t = document.getElementById('dashToast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'dashToast';
        t.className = 'dash-toast';
        document.body.appendChild(t);
    }
    t.textContent      = msg;
    t.style.background = bg || '#333';
    t.classList.add('show');
    clearTimeout(dashToastTimer);
    dashToastTimer = setTimeout(() => t.classList.remove('show'), 2800);
}