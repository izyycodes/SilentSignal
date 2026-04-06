// Emergency Alert System JavaScript
// Handles SOS, GPS location, shake detection, and PhilSMS sending

// Global state
let userLocation       = null;
let isAlertActive      = false;
let shakeEnabled       = false;
let sosCountdownSeconds = 10; // default; overridden by user settings on load
let miniMap            = null; // Leaflet mini-map instance
let miniMapMarker      = null; // Leaflet marker

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================
    // INITIALIZE
    // ================================
    initializeLocation();
    initializeShakeDetection();
    initializePanicClickDetection();
    loadUserSettings();
    
    // ================================
    // SOS BUTTON
    // ================================
    const sosButton = document.getElementById('sosButton');
    if (sosButton) {
        sosButton.addEventListener('click', function() {
            triggerSOS('button');
        });
        
        // Long press for SOS (3 seconds)
        let pressTimer = null;
        sosButton.addEventListener('mousedown', startLongPress);
        sosButton.addEventListener('touchstart', startLongPress);
        sosButton.addEventListener('mouseup', cancelLongPress);
        sosButton.addEventListener('mouseleave', cancelLongPress);
        sosButton.addEventListener('touchend', cancelLongPress);
        
        function startLongPress(e) {
            pressTimer = setTimeout(() => {
                triggerSOS('long_press');
            }, 3000);
        }
        
        function cancelLongPress() {
            clearTimeout(pressTimer);
        }
    }
    
    // ================================
    // CANCEL BUTTON
    // ================================
    const cancelBtn = document.getElementById('cancelAlertBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', cancelAlert);
    }
    
    // ================================
    // SHAKE TOGGLE — controlled from Settings page
    // The toggle UI was moved to Settings; state is loaded via loadUserSettings()
    // ================================
    
    // ================================
    // TEST ALERT BUTTON
    // ================================
    const testAlertBtn = document.getElementById('testAlertBtn');
    if (testAlertBtn) {
        testAlertBtn.addEventListener('click', function() {
            showTestAlert();
        });
    }
});

// ================================
// GPS LOCATION
// ================================
function initializeLocation() {
    if ("geolocation" in navigator) {
        updateLocation();
        setInterval(updateLocation, 30000);
    } else {
        console.warn('Geolocation not supported');
        updateLocationDisplay('GPS not available', null, null);
    }
}

function updateLocation() {
    const options = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 60000
    };
    
    navigator.geolocation.getCurrentPosition(
        (position) => {
            userLocation = {
                lat:       position.coords.latitude,
                lng:       position.coords.longitude,
                accuracy:  position.coords.accuracy,
                timestamp: new Date().toISOString()
            };
            updateLocationDisplay('Location acquired', userLocation.lat, userLocation.lng);
            reverseGeocode(userLocation.lat, userLocation.lng);
        },
        (error) => {
            console.error('Geolocation error:', error.message);
            if (error.code === error.PERMISSION_DENIED) {
                updateLocationDisplay('Location blocked', null, null);
                showAllowLocationBtn(true);
            } else {
                updateLocationDisplay('Unable to get location', null, null);
            }
        },
        options
    );
}

function updateLocationDisplay(status, lat, lng) {
    const locationStatus = document.getElementById('locationStatus');
    const locationCoords = document.getElementById('locationCoords');
    const locationLink = document.getElementById('locationLink');

    if (locationStatus) {
        locationStatus.textContent = status;
        locationStatus.className = lat ? 'status-success' : 'status-error';
    }

    // Hide allow-button once location is acquired
    if (lat) showAllowLocationBtn(false);
    if (locationCoords && lat && lng) {
        locationCoords.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    }
    if (locationLink && lat && lng) {
        locationLink.href = `https://maps.google.com/?q=${lat},${lng}`;
        locationLink.style.display = 'inline-flex';
    }

    // Update Leaflet mini-map
    if (lat && lng) {
        updateMiniMap(lat, lng);
    }
}

/* ── Leaflet Mini-Map ────────────────────────────────────── */
function updateMiniMap(lat, lng) {
    const placeholder = document.getElementById('miniMapPlaceholder');

    if (!miniMap) {
        // First time: initialise the map
        miniMap = L.map('miniMap', {
            center: [lat, lng],
            zoom: 16,
            zoomControl: false,
            attributionControl: false,
            dragging: false,
            scrollWheelZoom: false,
            doubleClickZoom: false,
            touchZoom: false,
            keyboard: false,
            boxZoom: false,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(miniMap);

        // Custom "You are here" marker
        const youAreHereIcon = L.divIcon({
            className: '',
            html: `<div style="
                background:#e53935;
                width:14px;height:14px;
                border-radius:50%;
                border:3px solid #fff;
                box-shadow:0 0 0 3px rgba(229,57,53,0.35),0 2px 6px rgba(0,0,0,0.35);
                position:relative;">
                <div style="
                    position:absolute;
                    bottom:18px;left:50%;transform:translateX(-50%);
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
            iconSize: [14, 14],
            iconAnchor: [7, 7],
        });

        miniMapMarker = L.marker([lat, lng], { icon: youAreHereIcon }).addTo(miniMap);

        // Hide placeholder
        if (placeholder) placeholder.classList.add('hidden');

    } else {
        // Subsequent updates: pan + move marker
        miniMap.setView([lat, lng], 16, { animate: true });
        miniMapMarker.setLatLng([lat, lng]);
        if (placeholder) placeholder.classList.add('hidden');
    }
}

function showAllowLocationBtn(show) {
    const btn = document.getElementById('allowLocationBtn');
    if (btn) btn.style.display = show ? 'flex' : 'none';
}

function requestLocationPermission() {
    const btn = document.getElementById('allowLocationBtn');

    // Check if the Permissions API can query location state
    if (navigator.permissions) {
        navigator.permissions.query({ name: 'geolocation' }).then(result => {
            if (result.state === 'denied') {
                // Browser has hard-blocked it — can't re-prompt, must guide to settings
                showLocationBlockedGuide();
            } else {
                // 'prompt' or 'granted' — try requesting again
                updateLocation();
                if (btn) {
                    btn.innerHTML = '<i class="ri-loader-4-line"></i> Requesting...';
                    btn.disabled = true;
                    setTimeout(() => {
                        btn.innerHTML = '<i class="ri-gps-line"></i> Allow Location';
                        btn.disabled = false;
                    }, 5000);
                }
            }
        });
    } else {
        // Fallback — just try again
        updateLocation();
    }
}

function showLocationBlockedGuide() {
    // Detect browser for specific instructions
    const isChrome  = /Chrome/.test(navigator.userAgent) && !/Edg/.test(navigator.userAgent);
    const isFirefox = /Firefox/.test(navigator.userAgent);
    const isSafari  = /Safari/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent);
    const isEdge    = /Edg/.test(navigator.userAgent);

    let steps = '';
    if (isChrome || isEdge) {
        steps = `1. Click the <strong>lock icon 🔒</strong> in the address bar<br>
                 2. Find <strong>Location</strong> and set it to <strong>Allow</strong><br>
                 3. Reload this page`;
    } else if (isFirefox) {
        steps = `1. Click the <strong>lock icon 🔒</strong> in the address bar<br>
                 2. Click <strong>Connection Secure → More Information</strong><br>
                 3. Go to <strong>Permissions → Access Your Location → Allow</strong><br>
                 4. Reload this page`;
    } else if (isSafari) {
        steps = `1. Go to <strong>Settings → Safari → Location</strong><br>
                 2. Set to <strong>Allow</strong><br>
                 3. Reload this page`;
    } else {
        steps = `1. Open your browser <strong>Site Settings</strong><br>
                 2. Find <strong>Location</strong> and set it to <strong>Allow</strong><br>
                 3. Reload this page`;
    }

    // Simple inline alert — reuses the existing notification style
    const notice = document.createElement('div');
    notice.id = 'locationBlockedGuide';
    notice.style.cssText = `
        position: fixed; top: 80px; left: 50%; transform: translateX(-50%);
        background: white; border: 2px solid #e53935; border-radius: 14px;
        padding: 20px 24px; max-width: 340px; width: 90%; z-index: 10000;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2); font-size: 13px; line-height: 1.7;
        font-family: 'Poppins', sans-serif;
    `;
    notice.innerHTML = `
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
            <i class="ri-map-pin-off-line" style="font-size:22px; color:#e53935;"></i>
            <strong style="font-size:14px; color:#333;">Location is Blocked</strong>
        </div>
        <p style="color:#555; margin:0 0 12px;">To re-enable location, follow these steps:</p>
        <p style="color:#333; margin:0 0 16px;">${steps}</p>
        <div style="display:flex; gap:8px;">
            <button onclick="window.location.reload()" style="flex:1; padding:10px; background:linear-gradient(135deg,#1A4D7F,#2d6a9f); color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; font-family:'Poppins',sans-serif;">
                <i class="ri-refresh-line"></i> Reload Page
            </button>
            <button onclick="document.getElementById('locationBlockedGuide')?.remove()" style="padding:10px 14px; background:#f5f5f5; color:#555; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; font-family:'Poppins',sans-serif;">
                Close
            </button>
        </div>
    `;
    document.body.appendChild(notice);

    // Auto-dismiss after 15 seconds
    setTimeout(() => notice.remove(), 15000);
}

function reverseGeocode(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
        .then(r => r.json())
        .then(data => {
            if (data.display_name) {
                const addressDisplay = document.getElementById('locationAddress');
                if (addressDisplay) {
                    const parts = data.display_name.split(',');
                    addressDisplay.textContent = parts.slice(0, 3).join(',');
                }
                if (userLocation) userLocation.address = data.display_name;
            }
        })
        .catch(err => console.log('Reverse geocode error:', err));
}

// ================================
// SHAKE DETECTION
// ================================
function initializeShakeDetection() {
    let lastX, lastY, lastZ;
    let lastTime = 0;
    const shakeThreshold = 15;
    
    if (window.DeviceMotionEvent) {
        window.addEventListener('devicemotion', function(event) {
            if (!shakeEnabled) return;
            
            const current     = event.accelerationIncludingGravity;
            const currentTime = Date.now();
            
            if ((currentTime - lastTime) > 100) {
                const diffTime = currentTime - lastTime;
                lastTime = currentTime;
                const speed = Math.abs(current.x + current.y + current.z - lastX - lastY - lastZ) / diffTime * 10000;
                if (speed > shakeThreshold) {
                    triggerSOS('shake');
                }
                lastX = current.x;
                lastY = current.y;
                lastZ = current.z;
            }
        });
    }
}

// ================================
// PANIC CLICK DETECTION
// ================================
function initializePanicClickDetection() {
    const sosButton = document.getElementById('sosButton');
    if (!sosButton) return;
    
    let clickTimes = [];
    
    sosButton.addEventListener('click', function(e) {
        const now = Date.now();
        clickTimes.push(now);
        clickTimes = clickTimes.filter(time => now - time < 3000);
        if (clickTimes.length >= 5) {
            clickTimes = [];
            triggerSOS('panic');
        }
    });
}

// ================================
// TRIGGER SOS
// ================================
// ================================
// LOAD USER SETTINGS FROM SERVER
// ================================
async function loadUserSettings() {
    try {
        const res  = await fetch(BASE_URL + 'index.php?action=get-settings-api');
        const data = await res.json();
        if (typeof data.sos_countdown_seconds === 'number') {
            sosCountdownSeconds = data.sos_countdown_seconds;
        }
        if (data.auto_shake_enabled) {
            shakeEnabled = true;
            showNotification('Shake-to-alert is active (via Settings)', 'info');
        }
        // Update countdown display on the SOS modal if it's already open
        const countdownEl = document.getElementById('sosCountdown');
        if (countdownEl) countdownEl.textContent = sosCountdownSeconds;
    } catch (e) {
        // Fail silently — use defaults
    }
}

async function triggerSOS(triggerType) {
    if (isAlertActive) return;
    isAlertActive = true;
    
    vibrate([500, 200, 500]);
    await refreshLocation();
    showSOSConfirmationModal(triggerType);
}

// ================================
// SOS CONFIRMATION MODAL
// ================================
function showSOSConfirmationModal(triggerType) {
    const modal = document.getElementById('sosConfirmModal');
    if (!modal) return;
    
    modal.classList.add('active');
    
    let countdown = sosCountdownSeconds;
    const countdownEl = document.getElementById('sosCountdown');
    if (countdownEl) countdownEl.textContent = countdown;
    
    if (window.sosCountdownTimer) clearInterval(window.sosCountdownTimer);
    
    window.sosCountdownTimer = setInterval(() => {
        countdown--;
        if (countdownEl) countdownEl.textContent = countdown;
        if (countdown <= 0) {
            clearInterval(window.sosCountdownTimer);
            modal.classList.remove('active');
            sendEmergencyAlert();
        }
    }, 1000);
    
    const confirmBtn = document.getElementById('confirmSOSBtn');
    if (confirmBtn) {
        confirmBtn.onclick = () => {
            clearInterval(window.sosCountdownTimer);
            modal.classList.remove('active');
            sendEmergencyAlert();
        };
    }
    
    const cancelBtn = document.getElementById('cancelSOSBtn');
    if (cancelBtn) {
        cancelBtn.onclick = () => {
            clearInterval(window.sosCountdownTimer);
            modal.classList.remove('active');
            cancelAlert();
        };
    }
    
    modal.onclick = (e) => {
        if (e.target === modal) {
            clearInterval(window.sosCountdownTimer);
            modal.classList.remove('active');
            cancelAlert();
        }
    };
}

// ================================
// REFRESH LOCATION
// ================================
function refreshLocation() {
    return new Promise((resolve) => {
        if (!("geolocation" in navigator)) return resolve(null);

        let settled = false;

        const fallback = setTimeout(() => {
            if (!settled) {
                settled = true;
                console.warn('GPS timeout — proceeding with last known location');
                resolve(userLocation);
            }
        }, 4000);

        navigator.geolocation.getCurrentPosition(
            (position) => {
                if (!settled) {
                    settled = true;
                    clearTimeout(fallback);
                    userLocation = {
                        lat:       position.coords.latitude,
                        lng:       position.coords.longitude,
                        accuracy:  position.coords.accuracy,
                        timestamp: new Date().toISOString()
                    };
                    resolve(userLocation);
                }
            },
            (error) => {
                if (!settled) {
                    settled = true;
                    clearTimeout(fallback);
                    console.warn('GPS error — proceeding with last known location');
                    resolve(userLocation);
                }
            },
            { enableHighAccuracy: true, timeout: 4000, maximumAge: 60000 }
        );
    });
}

// ================================
// SEND EMERGENCY ALERT
// ================================
async function sendEmergencyAlert() {
    const userData          = getUserData();
    const emergencyContacts = getEmergencyContacts();
    
    if (emergencyContacts.length === 0) {
        showNotification('No emergency contacts found. Please add contacts first.', 'error');
        cancelAlert();
        return;
    }
    
    const message = buildEmergencyMessage(userData);
    
    showNotification('Sending emergency SMS...', 'info');
    vibrate([200, 100, 200]);
    flashScreen('rgba(220, 53, 69, 0.3)');
    
    // Send via PhilSMS through PHP backend
    const result = await sendViaPhilSMS(message, emergencyContacts);
    console.log('PhilSMS result:', result);

    // Success if PhilSMS sent at least 1 message
    const philSmsSent = result && (
        result.success === true ||
        (typeof result.sent === 'number' && result.sent > 0)
    );

    if (philSmsSent) {
        showNotification('Emergency SMS sent to ' + result.sent + ' contact(s)!', 'success');
        flashScreen('rgba(76, 175, 80, 0.5)');
        vibrate([200, 100, 200, 100, 200]);
    } else {
        // PhilSMS failed — open native SMS as backup
        showNotification('Opening SMS app as backup...', 'warning');
        const phones = emergencyContacts.map(c => c.phone).join(',');
        setTimeout(() => openSMSFallback(phones, message), 1000);
    }
    
    // Always log to database
    logAlertToDatabase('sos', message);
    
    setTimeout(() => {
        isAlertActive = false;
        hideAlertOverlay();
    }, 2000);
}

// ================================
// SEND VIA PHILSMS (through PHP backend)
// ================================
async function sendViaPhilSMS(message, contacts) {
    try {
        const phones = contacts.map(c => c.phone.replace(/\s|-/g, '')).join(',');
        
        const response = await fetch(BASE_URL + 'index.php?action=send-philsms', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                message:  message,
                contacts: contacts,
                phones:   phones,
                location: userLocation
            }),
            keepalive: true
        });
        
        const data = await response.json();
        console.log('PhilSMS raw response:', data);
        return data;
        
    } catch (err) {
        console.error('PhilSMS send error:', err);
        return { success: false, sent: 0, message: err.message };
    }
}

// ================================
// BUILD EMERGENCY MESSAGE
// ================================
function buildEmergencyMessage(userData) {
    const locationText = userLocation
        ? `https://maps.google.com/?q=${userLocation.lat},${userLocation.lng}`
        : 'Location unavailable';

    const addressText = userLocation?.address || userData.address || 'Address not available';

    const now = new Date();
    const timestamp = now.toLocaleString('en-PH', { dateStyle: 'short', timeStyle: 'short' });

    let message = `EMERGENCY ALERT\n`;
    message += `DEAF/MUTE - TEXT ONLY - NO CALLS\n\n`;
    message += `Name: ${userData.name || 'Unknown'}\n`;
    if (userData.pwdId)  message += `PWD ID: ${userData.pwdId}\n`;
    message += `Phone: ${userData.phone || 'N/A'}\n`;
    message += `Status: NEEDS IMMEDIATE HELP\n`;
    message += `Time: ${timestamp}\n\n`;
    message += `LOCATION:\n`;
    message += `${addressText}\n`;
    message += `Map: ${locationText}\n\n`;

    if (userData.bloodType || userData.allergies || userData.medications) {
        message += `MEDICAL INFO:\n`;
        if (userData.bloodType)   message += `Blood Type: ${userData.bloodType}\n`;
        if (userData.allergies)   message += `Allergies: ${userData.allergies}\n`;
        if (userData.medications) message += `Medications: ${userData.medications}\n`;
        if (userData.conditions)  message += `Conditions: ${userData.conditions}\n`;
    }

    message += `\nThis person is DEAF/MUTE - Please respond via TEXT only.`;
    return message;
}

// ================================
// NATIVE SMS FALLBACK
// ================================
function openSMSFallback(phoneNumbers, message) {
    const encodedMessage = encodeURIComponent(message);
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
    const smsUrl = isIOS
        ? `sms:${phoneNumbers}&body=${encodedMessage}`
        : `sms:${phoneNumbers}?body=${encodedMessage}`;
    window.location.href = smsUrl;
}

// ================================
// GET USER DATA
// ================================
function getUserData() {
    if (typeof userInfoData !== 'undefined' && userInfoData) {
        return userInfoData;
    }
    return {
        name: '', phone: '', pwdId: '',
        address: '', bloodType: '',
        allergies: '', medications: '', conditions: ''
    };
}

// ================================
// GET EMERGENCY CONTACTS
// ================================
function getEmergencyContacts() {
    if (typeof emergencyContactsData !== 'undefined' && Array.isArray(emergencyContactsData) && emergencyContactsData.length > 0) {
        return emergencyContactsData;
    }
    return [];
}

// ================================
// LOG TO DATABASE
// ================================
function logAlertToDatabase(type, message) {
    fetch(BASE_URL + 'index.php?action=log-emergency-alert', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            type:      type,
            message:   message,
            location:  userLocation,
            timestamp: new Date().toISOString()
        }),
        keepalive: true
    }).catch(err => console.log('Failed to log alert:', err));
}

// ================================
// UI HELPERS
// ================================
function hideAlertOverlay() {
    const overlay = document.getElementById('alertOverlay');
    if (overlay) overlay.style.display = 'none';
}

function cancelAlert() {
    isAlertActive = false;
    if (window.sosCountdownTimer) clearInterval(window.sosCountdownTimer);
    const modal = document.getElementById('sosConfirmModal');
    if (modal) modal.classList.remove('active');
    hideAlertOverlay();
    showNotification('Alert cancelled', 'info');
    vibrate([100]);
}

function flashScreen(color) {
    const flash = document.createElement('div');
    flash.style.cssText = `
        position: fixed; top: 0; left: 0;
        width: 100%; height: 100%;
        background: ${color}; z-index: 9999;
        animation: flashAnim 0.5s ease-out;
        pointer-events: none;
    `;
    document.body.appendChild(flash);
    setTimeout(() => flash.remove(), 500);
}

function vibrate(pattern) {
    if ('vibrate' in navigator) navigator.vibrate(pattern);
}

function showNotification(message, type = 'info') {
    const colors = {
        success: '#4caf50',
        error:   '#f44336',
        info:    '#2196f3',
        warning: '#ff9800'
    };
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed; top: 80px; right: 20px;
        padding: 15px 25px;
        background: ${colors[type] || colors.info};
        color: white; border-radius: 10px;
        font-size: 14px; font-weight: 500;
        z-index: 10001;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease;
        max-width: 300px;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

function showTestAlert() {
    showNotification('Test alert! All systems working.', 'success');
    vibrate([200, 100, 200]);
    flashScreen('rgba(76, 175, 80, 0.5)');
}

// ================================
// CSS ANIMATIONS
// ================================
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(100px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideOut {
        from { opacity: 1; transform: translateX(0); }
        to   { opacity: 0; transform: translateX(100px); }
    }
    @keyframes flashAnim {
        from { opacity: 0.8; }
        to   { opacity: 0; }
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);