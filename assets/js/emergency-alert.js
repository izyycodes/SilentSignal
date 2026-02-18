// Emergency Alert System JavaScript
// Handles SOS, GPS location, shake detection, and SMS intent

// Get BASE_URL from meta tag
const BASE_URL = document.querySelector('meta[name="base-url"]')?.content || '';

// Global state
let userLocation = null;
let isAlertActive = false;
let shakeEnabled = false;
let panicClickCount = 0;
let panicClickTimer = null;

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================
    // INITIALIZE
    // ================================
    initializeLocation();
    initializeShakeDetection();
    initializePanicClickDetection();
    
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
    // SHAKE TOGGLE
    // ================================
    const shakeToggle = document.getElementById('shakeToggle');
    if (shakeToggle) {
        shakeToggle.addEventListener('change', function() {
            shakeEnabled = this.checked;
            showNotification(
                shakeEnabled ? 'Shake-to-alert enabled' : 'Shake-to-alert disabled',
                shakeEnabled ? 'success' : 'info'
            );
        });
    }
    
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
        // Get initial location
        updateLocation();
        
        // Update location every 30 seconds
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
                lat: position.coords.latitude,
                lng: position.coords.longitude,
                accuracy: position.coords.accuracy,
                timestamp: new Date().toISOString()
            };
            
            // Update display
            updateLocationDisplay(
                'Location acquired',
                userLocation.lat,
                userLocation.lng
            );
            
            // Reverse geocode to get address (optional)
            reverseGeocode(userLocation.lat, userLocation.lng);
        },
        (error) => {
            console.error('Geolocation error:', error.message);
            updateLocationDisplay('Unable to get location', null, null);
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
    
    if (locationCoords && lat && lng) {
        locationCoords.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    }
    
    if (locationLink && lat && lng) {
        locationLink.href = `https://maps.google.com/?q=${lat},${lng}`;
        locationLink.style.display = 'inline-block';
    }
}

function reverseGeocode(lat, lng) {
    // Using free Nominatim API for reverse geocoding
    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
        .then(response => response.json())
        .then(data => {
            if (data.display_name) {
                const addressDisplay = document.getElementById('locationAddress');
                if (addressDisplay) {
                    // Shorten the address
                    const parts = data.display_name.split(',');
                    const shortAddress = parts.slice(0, 3).join(',');
                    addressDisplay.textContent = shortAddress;
                }
                userLocation.address = data.display_name;
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
            
            const current = event.accelerationIncludingGravity;
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
    const panicArea = document.getElementById('panicDetectionArea') || document.body;
    
    panicArea.addEventListener('click', function(e) {
        // Ignore clicks on buttons and interactive elements
        if (e.target.closest('button, a, input, .no-panic')) return;
        
        panicClickCount++;
        
        if (panicClickTimer) clearTimeout(panicClickTimer);
        
        panicClickTimer = setTimeout(() => {
            panicClickCount = 0;
        }, 3000);
        
        // 5 rapid clicks in 3 seconds triggers SOS
        if (panicClickCount >= 5) {
            panicClickCount = 0;
            triggerSOS('panic');
        }
    });
}

// ================================
// TRIGGER SOS
// ================================
async function triggerSOS(triggerType) {
    if (isAlertActive) return;
    isAlertActive = true;
    
    console.log('SOS triggered by:', triggerType);
    
    // Visual feedback
    showAlertOverlay();
    vibrate([500, 200, 500, 200, 500]);
    
    // Get fresh location
    await refreshLocation();
    
    // Show confirmation dialog
    const confirmed = await showSOSConfirmation(triggerType);
    
    if (confirmed) {
        // Send the alert
        sendEmergencyAlert();
    } else {
        cancelAlert();
    }
}

function refreshLocation() {
    return new Promise((resolve) => {
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                        accuracy: position.coords.accuracy,
                        timestamp: new Date().toISOString()
                    };
                    resolve(userLocation);
                },
                (error) => {
                    console.error('Location error:', error);
                    resolve(userLocation); // Use last known location
                },
                { enableHighAccuracy: true, timeout: 5000 }
            );
        } else {
            resolve(null);
        }
    });
}

// ================================
// SOS CONFIRMATION
// ================================
function showSOSConfirmation(triggerType) {
    return new Promise((resolve) => {
        const modal = document.getElementById('sosConfirmModal');
        if (modal) {
            modal.classList.add('active');
            
            // Countdown timer (10 seconds)
            let countdown = 10;
            const countdownEl = document.getElementById('sosCountdown');
            
            const timer = setInterval(() => {
                countdown--;
                if (countdownEl) countdownEl.textContent = countdown;
                
                if (countdown <= 0) {
                    clearInterval(timer);
                    modal.classList.remove('active');
                    resolve(true); // Auto-confirm after countdown
                }
            }, 1000);
            
            // Confirm button
            const confirmBtn = document.getElementById('confirmSOSBtn');
            if (confirmBtn) {
                confirmBtn.onclick = () => {
                    clearInterval(timer);
                    modal.classList.remove('active');
                    resolve(true);
                };
            }
            
            // Cancel button
            const cancelBtn = document.getElementById('cancelSOSBtn');
            if (cancelBtn) {
                cancelBtn.onclick = () => {
                    clearInterval(timer);
                    modal.classList.remove('active');
                    resolve(false);
                };
            }
        } else {
            // No modal, just confirm
            resolve(confirm('Send Emergency SOS Alert?'));
        }
    });
}

// ================================
// SEND EMERGENCY ALERT
// ================================
async function sendEmergencyAlert() {
    // Get user's medical data and contacts from the page
    const userData = getUserData();
    const emergencyContacts = getEmergencyContacts();
    
    if (emergencyContacts.length === 0) {
        showNotification('No emergency contacts found. Please add contacts first.', 'error');
        cancelAlert();
        return;
    }
    
    // Build SMS message
    const message = buildEmergencyMessage(userData);
    
    // Get phone numbers
    const phoneNumbers = emergencyContacts.map(c => c.phone).join(',');
    
    // Open SMS app with pre-filled message
    openSMSIntent(phoneNumbers, message);
    
    // Log alert to database
    logAlertToDatabase('sos', message);
    
    // Show success feedback
    showNotification('Opening SMS app with emergency message...', 'success');
    
    // Visual confirmation
    flashScreen('green');
    vibrate([200, 100, 200]);
    
    // Reset after delay
    setTimeout(() => {
        isAlertActive = false;
        hideAlertOverlay();
    }, 2000);
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
    const timestamp = now.toLocaleString('en-PH', { 
        dateStyle: 'short', 
        timeStyle: 'short' 
    });
    
    let message = `🚨 EMERGENCY ALERT 🚨\n`;
    message += `⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\n`;
    message += `Name: ${userData.name || 'Unknown'}\n`;
    
    if (userData.pwdId) {
        message += `PWD ID: ${userData.pwdId}\n`;
    }
    
    message += `Phone: ${userData.phone || 'N/A'}\n`;
    message += `Status: NEEDS IMMEDIATE HELP\n`;
    message += `Time: ${timestamp}\n\n`;
    
    message += `📍 LOCATION:\n`;
    message += `${addressText}\n`;
    message += `Map: ${locationText}\n\n`;
    
    if (userData.bloodType || userData.allergies || userData.medications) {
        message += `🏥 MEDICAL INFO:\n`;
        if (userData.bloodType) message += `Blood Type: ${userData.bloodType}\n`;
        if (userData.allergies) message += `Allergies: ${userData.allergies}\n`;
        if (userData.medications) message += `Medications: ${userData.medications}\n`;
        if (userData.conditions) message += `Conditions: ${userData.conditions}\n`;
    }
    
    message += `\n⚠️ Please respond via TEXT MESSAGE only.`;
    
    return message;
}

// ================================
// SMS INTENT
// ================================
function openSMSIntent(phoneNumbers, message) {
    // Encode message for URL
    const encodedMessage = encodeURIComponent(message);
    
    // Detect platform
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
    const isAndroid = /Android/.test(navigator.userAgent);
    
    let smsUrl;
    
    if (isIOS) {
        // iOS uses & for body
        smsUrl = `sms:${phoneNumbers}&body=${encodedMessage}`;
    } else {
        // Android and others use ?body=
        smsUrl = `sms:${phoneNumbers}?body=${encodedMessage}`;
    }
    
    // Open SMS app
    window.location.href = smsUrl;
}

// ================================
// GET USER DATA
// ================================
function getUserData() {
    // Try to get from data attributes or hidden fields
    const userDataEl = document.getElementById('userData');
    
    if (userDataEl) {
        return {
            name: userDataEl.dataset.name || '',
            phone: userDataEl.dataset.phone || '',
            pwdId: userDataEl.dataset.pwdId || '',
            address: userDataEl.dataset.address || '',
            bloodType: userDataEl.dataset.bloodType || '',
            allergies: userDataEl.dataset.allergies || '',
            medications: userDataEl.dataset.medications || '',
            conditions: userDataEl.dataset.conditions || ''
        };
    }
    
    // Fallback to getting from display elements
    return {
        name: document.querySelector('.user-name')?.textContent || 'User',
        phone: document.querySelector('.user-phone')?.textContent || '',
        pwdId: '',
        address: '',
        bloodType: '',
        allergies: '',
        medications: '',
        conditions: ''
    };
}

// ================================
// GET EMERGENCY CONTACTS
// ================================
function getEmergencyContacts() {
    const contacts = [];
    
    // Get from data attribute
    const contactsDataEl = document.getElementById('emergencyContactsData');
    if (contactsDataEl && contactsDataEl.dataset.contacts) {
        try {
            return JSON.parse(contactsDataEl.dataset.contacts);
        } catch (e) {
            console.error('Error parsing contacts:', e);
        }
    }
    
    // Fallback: get from DOM
    document.querySelectorAll('.contact-item, .contact-card').forEach(card => {
        const name = card.querySelector('.contact-name, h4')?.textContent || '';
        const phone = card.querySelector('.contact-phone')?.textContent || 
                      card.dataset.phone || '';
        
        if (phone) {
            contacts.push({ name, phone: phone.replace(/\s/g, '') });
        }
    });
    
    return contacts;
}

// ================================
// LOG TO DATABASE
// ================================
function logAlertToDatabase(type, message) {
    fetch(BASE_URL + 'index.php?action=log-emergency-alert', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            type: type,
            message: message,
            location: userLocation,
            timestamp: new Date().toISOString()
        })
    }).catch(err => console.log('Failed to log alert:', err));
}

// ================================
// UI HELPERS
// ================================
function showAlertOverlay() {
    let overlay = document.getElementById('alertOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'alertOverlay';
        overlay.innerHTML = `
            <div class="alert-overlay-content">
                <div class="alert-spinner"></div>
                <h2>🚨 EMERGENCY ALERT 🚨</h2>
                <p>Preparing to send SOS...</p>
                <button id="cancelAlertBtn" class="btn btn-cancel">Cancel</button>
            </div>
        `;
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(220, 53, 69, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            color: white;
            text-align: center;
        `;
        document.body.appendChild(overlay);
        
        // Add cancel listener
        document.getElementById('cancelAlertBtn')?.addEventListener('click', cancelAlert);
    }
    overlay.style.display = 'flex';
}

function hideAlertOverlay() {
    const overlay = document.getElementById('alertOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

function cancelAlert() {
    isAlertActive = false;
    hideAlertOverlay();
    showNotification('Alert cancelled', 'info');
    vibrate([100]);
}

function flashScreen(color) {
    const flash = document.createElement('div');
    flash.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: ${color};
        z-index: 9999;
        animation: flashAnim 0.5s ease-out;
        pointer-events: none;
    `;
    document.body.appendChild(flash);
    
    setTimeout(() => flash.remove(), 500);
}

function vibrate(pattern) {
    if ('vibrate' in navigator) {
        navigator.vibrate(pattern);
    }
}

function showNotification(message, type = 'info') {
    const colors = {
        success: '#4caf50',
        error: '#f44336',
        info: '#2196f3',
        warning: '#ff9800'
    };
    
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        padding: 15px 25px;
        background: ${colors[type] || colors.info};
        color: white;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
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
    }, 3000);
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
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100px); }
    }
    @keyframes flashAnim {
        from { opacity: 0.8; }
        to { opacity: 0; }
    }
    .alert-spinner {
        width: 60px;
        height: 60px;
        border: 5px solid rgba(255,255,255,0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);