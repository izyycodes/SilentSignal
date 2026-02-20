// Dashboard JavaScript
// Includes Quick SOS functionality

const BASE_URL = document.querySelector('meta[name="base-url"]')?.content || '';

// Global state
let userLocation = null;
let isAlertActive = false;

document.addEventListener('DOMContentLoaded', function() {
    initializeLocation();
    initializeQuickSOS();
    initializeHoldDetection();
});

// ================================
// GPS LOCATION
// ================================
function initializeLocation() {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    timestamp: new Date().toISOString()
                };
                reverseGeocode(userLocation.lat, userLocation.lng);
            },
            (error) => {
                console.log('Location error:', error.message);
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }
}

function reverseGeocode(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
        .then(response => response.json())
        .then(data => {
            if (data.display_name) {
                userLocation.address = data.display_name;
            }
        })
        .catch(err => console.log('Reverse geocode error:', err));
}

// ================================
// QUICK SOS BUTTON
// ================================
function initializeQuickSOS() {
    const sosBtn = document.getElementById('quickSOSBtn');
    
    if (sosBtn) {
        sosBtn.addEventListener('click', function() {
            triggerSOS('button');
        });
    }
}

// ================================
// HOLD ANYWHERE DETECTION (3 seconds)
// ================================
function initializeHoldDetection() {
    let holdTimer = null;
    let holdIndicator = null;
    
    function createHoldIndicator() {
        if (holdIndicator) return;
        
        holdIndicator = document.createElement('div');
        holdIndicator.id = 'holdIndicator';
        holdIndicator.innerHTML = `
            <div class="hold-circle">
                <svg viewBox="0 0 100 100">
                    <circle class="hold-bg" cx="50" cy="50" r="45"></circle>
                    <circle class="hold-progress" cx="50" cy="50" r="45"></circle>
                </svg>
                <div class="hold-text">
                    <i class="ri-alarm-warning-fill"></i>
                    <span>Hold for SOS</span>
                </div>
            </div>
        `;
        document.body.appendChild(holdIndicator);
    }
    
    function showHoldIndicator(x, y) {
        createHoldIndicator();
        holdIndicator.style.cssText = `
            position: fixed;
            left: ${x}px;
            top: ${y}px;
            transform: translate(-50%, -50%);
            z-index: 9999;
            pointer-events: none;
            display: block;
        `;
        holdIndicator.classList.add('active');
    }
    
    function hideHoldIndicator() {
        if (holdIndicator) {
            holdIndicator.classList.remove('active');
        }
    }
    
    function startHold(e) {
        // Ignore clicks on buttons, links, inputs, cards, modals
        if (e.target.closest('button, a, input, select, textarea, .modal, .module-card, .quick-sos-section, .no-hold')) return;
        
        if (isAlertActive) return;
        
        const touch = e.touches ? e.touches[0] : e;
        const x = touch.clientX;
        const y = touch.clientY;
        
        // Show indicator after 500ms
        holdTimer = setTimeout(() => {
            showHoldIndicator(x, y);
            vibrate([50]);
        }, 500);
        
        // Trigger SOS after 3 seconds
        window.holdSOSTimer = setTimeout(() => {
            hideHoldIndicator();
            vibrate([200, 100, 200]);
            triggerSOS('hold');
        }, 3000);
    }
    
    function endHold() {
        if (holdTimer) {
            clearTimeout(holdTimer);
            holdTimer = null;
        }
        if (window.holdSOSTimer) {
            clearTimeout(window.holdSOSTimer);
            window.holdSOSTimer = null;
        }
        hideHoldIndicator();
    }
    
    // Mouse events
    document.addEventListener('mousedown', startHold);
    document.addEventListener('mouseup', endHold);
    document.addEventListener('mouseleave', endHold);
    
    // Touch events
    document.addEventListener('touchstart', startHold, { passive: true });
    document.addEventListener('touchend', endHold);
    document.addEventListener('touchcancel', endHold);
}

// ================================
// TRIGGER SOS
// ================================
async function triggerSOS(triggerType) {
    if (isAlertActive) return;
    isAlertActive = true;
    
    console.log('SOS triggered by:', triggerType);
    
    vibrate([500, 200, 500]);
    
    // Refresh location
    await refreshLocation();
    
    // Show confirmation modal
    showSOSConfirmationModal();
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
                    resolve(userLocation);
                },
                { enableHighAccuracy: true, timeout: 5000 }
            );
        } else {
            resolve(null);
        }
    });
}

// ================================
// SOS CONFIRMATION MODAL
// ================================
function showSOSConfirmationModal() {
    const modal = document.getElementById('sosConfirmModal');
    
    if (!modal) {
        // Create modal if doesn't exist
        const newModal = document.createElement('div');
        newModal.id = 'sosConfirmModal';
        newModal.className = 'modal active';
        newModal.innerHTML = `
            <div class="modal-content">
                <div class="modal-icon">
                    <i class="ri-alarm-warning-fill"></i>
                </div>
                <h2>Send Emergency Alert?</h2>
                <p>This will send an SOS message to all your emergency contacts with your location.</p>
                <div class="countdown">
                    Auto-sending in <span id="sosCountdown">10</span> seconds...
                </div>
                <div class="modal-actions">
                    <button class="btn btn-danger" id="confirmSOSBtn">
                        <i class="ri-send-plane-fill"></i> Send Now
                    </button>
                    <button class="btn btn-secondary" id="cancelSOSBtn">
                        <i class="ri-close-line"></i> Cancel
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(newModal);
        setupModalListeners(newModal);
    } else {
        modal.classList.add('active');
        setupModalListeners(modal);
    }
    
    // Start countdown
    startCountdown();
}

function setupModalListeners(modal) {
    const confirmBtn = document.getElementById('confirmSOSBtn');
    const cancelBtn = document.getElementById('cancelSOSBtn');
    
    if (confirmBtn) {
        confirmBtn.onclick = () => {
            clearInterval(window.sosCountdownTimer);
            modal.classList.remove('active');
            sendEmergencyAlert();
        };
    }
    
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

function startCountdown() {
    let countdown = 10;
    const countdownEl = document.getElementById('sosCountdown');
    
    if (window.sosCountdownTimer) {
        clearInterval(window.sosCountdownTimer);
    }
    
    if (countdownEl) countdownEl.textContent = countdown;
    
    window.sosCountdownTimer = setInterval(() => {
        countdown--;
        if (countdownEl) countdownEl.textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(window.sosCountdownTimer);
            document.getElementById('sosConfirmModal')?.classList.remove('active');
            sendEmergencyAlert();
        }
    }, 1000);
}

function cancelAlert() {
    isAlertActive = false;
    if (window.sosCountdownTimer) {
        clearInterval(window.sosCountdownTimer);
    }
    showNotification('Alert cancelled', 'info');
    vibrate([100]);
}

// ================================
// SEND EMERGENCY ALERT
// ================================
function sendEmergencyAlert() {
    const userData = getUserData();
    const emergencyContacts = getEmergencyContacts();
    
    if (emergencyContacts.length === 0) {
        showNotification('No emergency contacts found. Please add contacts first.', 'error');
        isAlertActive = false;
        return;
    }
    
    // Build SMS message
    const message = buildEmergencyMessage(userData);
    
    // Get phone numbers
    const phoneNumbers = emergencyContacts.map(c => c.phone.replace(/\s/g, '')).join(',');
    
    // Open SMS app
    openSMSIntent(phoneNumbers, message);
    
    // Log to database
    logAlertToDatabase('sos', message);
    
    // Show success
    showNotification('Opening SMS app with emergency message...', 'success');
    vibrate([200, 100, 200]);
    
    // Reset
    setTimeout(() => {
        isAlertActive = false;
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
    }
    
    message += `\n⚠️ Please respond via TEXT MESSAGE only.`;
    
    return message;
}

// ================================
// SMS INTENT
// ================================
function openSMSIntent(phoneNumbers, message) {
    const encodedMessage = encodeURIComponent(message);
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
    
    let smsUrl;
    if (isIOS) {
        smsUrl = `sms:${phoneNumbers}&body=${encodedMessage}`;
    } else {
        smsUrl = `sms:${phoneNumbers}?body=${encodedMessage}`;
    }
    
    window.location.href = smsUrl;
}

// ================================
// GET DATA FROM PAGE
// ================================
function getUserData() {
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
    
    return { name: 'User' };
}

function getEmergencyContacts() {
    const contactsEl = document.getElementById('emergencyContactsData');
    
    if (contactsEl && contactsEl.dataset.contacts) {
        try {
            return JSON.parse(contactsEl.dataset.contacts);
        } catch (e) {
            console.error('Error parsing contacts:', e);
        }
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
            type: type,
            message: message,
            location: userLocation,
            timestamp: new Date().toISOString()
        })
    }).catch(err => console.log('Failed to log alert:', err));
}

// ================================
// HELPERS
// ================================
function vibrate(pattern) {
    if ('vibrate' in navigator) {
        navigator.vibrate(pattern);
    }
}

function showNotification(message, type = 'info') {
    document.querySelectorAll('.notification').forEach(n => n.remove());
    
    const colors = {
        success: '#4caf50',
        error: '#f44336',
        info: '#2196f3',
        warning: '#ff9800'
    };
    
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        padding: 15px 25px;
        background: ${colors[type]};
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

// ================================
// CSS 
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
    
    /* Hold Indicator */
    #holdIndicator {
        display: none;
    }
    #holdIndicator.active {
        display: block;
    }
    .hold-circle {
        width: 100px;
        height: 100px;
        position: relative;
    }
    .hold-circle svg {
        transform: rotate(-90deg);
    }
    .hold-bg {
        fill: none;
        stroke: rgba(255,255,255,0.3);
        stroke-width: 8;
    }
    .hold-progress {
        fill: none;
        stroke: #ff5252;
        stroke-width: 8;
        stroke-dasharray: 283;
        stroke-dashoffset: 283;
        animation: holdProgress 3s linear forwards;
    }
    @keyframes holdProgress {
        to { stroke-dashoffset: 0; }
    }
    .hold-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        text-shadow: 0 2px 5px rgba(0,0,0,0.5);
    }
    .hold-text i {
        font-size: 24px;
        display: block;
        margin-bottom: 5px;
    }
    .hold-text span {
        font-size: 10px;
        font-weight: 600;
    }
    
    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        z-index: 10000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal.active {
        display: flex !important;
    }
    .modal-content {
        background: white;
        border-radius: 20px;
        padding: 30px;
        max-width: 400px;
        width: 100%;
        text-align: center;
        animation: modalSlide 0.3s ease;
    }
    @keyframes modalSlide {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    .modal-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(145deg, #ff5252, #d32f2f);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse-icon 1s ease infinite;
    }
    .modal-icon i {
        font-size: 40px;
        color: white;
    }
    @keyframes pulse-icon {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    .modal-content h2 {
        font-size: 22px;
        margin-bottom: 10px;
        color: #333;
    }
    .modal-content > p {
        font-size: 14px;
        color: #666;
        margin-bottom: 20px;
    }
    .countdown {
        font-size: 16px;
        color: #d32f2f;
        font-weight: 600;
        margin-bottom: 25px;
        padding: 15px;
        background: #ffebee;
        border-radius: 10px;
    }
    .countdown span {
        font-size: 28px;
        display: block;
        margin-top: 5px;
    }
    .modal-actions {
        display: flex;
        gap: 12px;
    }
    .modal-actions .btn {
        flex: 1;
        padding: 15px;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-danger {
        background: linear-gradient(145deg, #ff5252, #d32f2f);
        color: white;
    }
    .btn-secondary {
        background: #f5f5f5;
        color: #666;
    }
`;
document.head.appendChild(style);