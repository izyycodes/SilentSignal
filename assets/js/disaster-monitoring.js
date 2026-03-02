// Disaster Monitoring JavaScript
// Real weather data from OpenWeatherMap + Auto-SOS with SMS functionality

const BASE_URL = document.querySelector('meta[name="base-url"]')?.content || '';
const OPENWEATHER_API_KEY = '96c34b7b3b239de96e9bbada9c5a432a';

// Global state
let userLocation = null;
let weatherData = null;
let activeAlerts = [];
let autoSOSEnabled = true;
let countdownInterval = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize
    initializeLocation();
    initializeAutoSOS();
    initializeEventListeners();
});

// ================================
// LOCATION
// ================================
function initializeLocation() {
    updateLocationStatus('Acquiring GPS...', 'pending');
    
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                updateLocationStatus('Location acquired', 'success');
                
                // Fetch weather data
                fetchWeatherData();
                fetchWeatherAlerts();
                
                // Update every 5 minutes
                setInterval(fetchWeatherData, 300000);
                setInterval(fetchWeatherAlerts, 300000);
            },
            (error) => {
                console.error('Location error:', error);
                updateLocationStatus('Using default location', 'warning');
                
                // Default to Bacolod City, Philippines
                userLocation = { lat: 10.6765, lng: 122.9509 };
                fetchWeatherData();
                fetchWeatherAlerts();
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    } else {
        updateLocationStatus('GPS not available', 'error');
        userLocation = { lat: 10.6765, lng: 122.9509 };
        fetchWeatherData();
        fetchWeatherAlerts();
    }
}

function updateLocationStatus(text, status) {
    const statusEl = document.getElementById('locationStatus');
    if (statusEl) {
        statusEl.textContent = text;
        statusEl.className = `location-status ${status}`;
    }
}

// ================================
// FETCH WEATHER DATA
// ================================
async function fetchWeatherData() {
    if (!userLocation) return;
    
    try {
        const url = `https://api.openweathermap.org/data/2.5/weather?lat=${userLocation.lat}&lon=${userLocation.lng}&appid=${OPENWEATHER_API_KEY}&units=metric`;
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.cod === 200) {
            weatherData = data;
            updateWeatherDisplay(data);
        } else {
            console.error('Weather API error:', data.message);
        }
    } catch (error) {
        console.error('Failed to fetch weather:', error);
    }
}

// ================================
// FETCH WEATHER ALERTS
// ================================
async function fetchWeatherAlerts() {
    if (!userLocation) return;
    
    try {
        // One Call API for alerts
        const url = `https://api.openweathermap.org/data/2.5/onecall?lat=${userLocation.lat}&lon=${userLocation.lng}&exclude=minutely,hourly&appid=${OPENWEATHER_API_KEY}&units=metric`;
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.alerts && data.alerts.length > 0) {
            activeAlerts = data.alerts;
            displayAlerts(data.alerts);
            checkSevereAlerts(data.alerts);
        } else {
            checkWeatherConditions();
        }
    } catch (error) {
        console.error('Failed to fetch alerts:', error);
        checkWeatherConditions();
    }
}

// ================================
// UPDATE WEATHER DISPLAY
// ================================
function updateWeatherDisplay(data) {
    // Temperature
    const tempEl = document.getElementById('temperature');
    if (tempEl) tempEl.textContent = `${Math.round(data.main.temp)}°C`;
    
    // Humidity
    const humidityEl = document.getElementById('humidity');
    if (humidityEl) humidityEl.textContent = `${data.main.humidity}%`;
    
    // Wind Speed
    const windEl = document.getElementById('windSpeed');
    if (windEl) windEl.textContent = `${Math.round(data.wind.speed * 3.6)} km/h`;
    
    // Pressure
    const pressureEl = document.getElementById('pressure');
    if (pressureEl) pressureEl.textContent = `${data.main.pressure} hPa`;
    
    // Weather condition
    const conditionEl = document.getElementById('weatherCondition');
    if (conditionEl) conditionEl.textContent = data.weather[0].main;
    
    // Weather icon
    const iconEl = document.getElementById('weatherIcon');
    if (iconEl) {
        const iconCode = data.weather[0].icon;
        iconEl.src = `https://openweathermap.org/img/wn/${iconCode}@2x.png`;
        iconEl.alt = data.weather[0].description;
    }
    
    // Location name
    const locationEl = document.getElementById('locationName');
    if (locationEl) locationEl.textContent = data.name;
    
    // Rainfall
    const rainEl = document.getElementById('rainfall');
    if (rainEl) {
        const rain = data.rain ? data.rain['1h'] || data.rain['3h'] || 0 : 0;
        rainEl.textContent = rain > 0 ? `${rain} mm` : 'None';
    }
    
    // Feels like
    const feelsLikeEl = document.getElementById('feelsLike');
    if (feelsLikeEl) feelsLikeEl.textContent = `${Math.round(data.main.feels_like)}°C`;
    
    // Visibility
    const visibilityEl = document.getElementById('visibility');
    if (visibilityEl) visibilityEl.textContent = `${(data.visibility / 1000).toFixed(1)} km`;
    
    // Last updated
    const updatedEl = document.getElementById('lastUpdated');
    if (updatedEl) updatedEl.textContent = `Updated: ${new Date().toLocaleTimeString()}`;
}

// ================================
// CHECK WEATHER CONDITIONS FOR ALERTS
// ================================
function checkWeatherConditions() {
    if (!weatherData) return;
    
    const dominated = [];
    const wind = weatherData.wind.speed * 3.6;
    const temp = weatherData.main.temp;
    const weatherId = weatherData.weather[0].id;
    
    // Typhoon/Storm (wind > 62 km/h)
    if (wind >= 62) {
        dominated.push({
            type: 'typhoon',
            name: wind >= 118 ? 'Typhoon Warning' : 'Strong Wind Warning',
            severity: wind >= 118 ? 'HIGH' : 'MEDIUM',
            description: `Wind speed: ${Math.round(wind)} km/h. ${wind >= 118 ? 'Typhoon conditions detected!' : 'Storm conditions detected.'}`,
            source: 'OpenWeatherMap'
        });
    }
    
    // Thunderstorm (200-232)
    if (weatherId >= 200 && weatherId < 300) {
        dominated.push({
            type: 'storm',
            name: 'Thunderstorm Alert',
            severity: weatherId >= 210 ? 'HIGH' : 'MEDIUM',
            description: weatherData.weather[0].description,
            source: 'OpenWeatherMap'
        });
    }
    
    // Heavy rain (502-531)
    if (weatherId >= 502 && weatherId < 600) {
        dominated.push({
            type: 'flood',
            name: 'Heavy Rain Warning',
            severity: weatherId >= 503 ? 'HIGH' : 'MEDIUM',
            description: 'Heavy rainfall detected. Possible flooding in low-lying areas.',
            source: 'OpenWeatherMap'
        });
    }
    
    // Extreme heat (> 40°C)
    if (temp >= 40) {
        dominated.push({
            type: 'heat',
            name: 'Extreme Heat Warning',
            severity: 'HIGH',
            description: `Temperature: ${Math.round(temp)}°C. Stay hydrated and avoid sun exposure.`,
            source: 'OpenWeatherMap'
        });
    }
    
    if (dominated.length > 0) {
        displayGeneratedAlerts(dominated);
        checkSevereAlerts(dominated);
    } else {
        displayNoAlerts();
    }
}

// ================================
// DISPLAY ALERTS
// ================================
function displayAlerts(alerts) {
    const container = document.getElementById('alertsContainer');
    if (!container) return;
    
    container.innerHTML = '';
    
    alerts.forEach((alert, index) => {
        const severity = getSeverityFromAlert(alert);
        const alertCard = createAlertCard({
            type: getAlertType(alert.event),
            name: alert.event,
            severity: severity,
            description: alert.description,
            source: alert.sender_name,
            time: new Date(alert.start * 1000).toLocaleString()
        });
        container.appendChild(alertCard);
    });
}

function displayGeneratedAlerts(alerts) {
    const container = document.getElementById('alertsContainer');
    if (!container) return;
    
    container.innerHTML = '';
    
    alerts.forEach(alert => {
        const alertCard = createAlertCard(alert);
        container.appendChild(alertCard);
    });
}

function displayNoAlerts() {
    const container = document.getElementById('alertsContainer');
    if (!container) return;
    
    container.innerHTML = `
        <div class="no-alerts">
            <i class="ri-shield-check-line"></i>
            <h3>No Active Alerts</h3>
            <p>Your area is currently safe. We'll notify you if any disasters are detected.</p>
        </div>
    `;
}

function createAlertCard(alert) {
    const icons = {
        typhoon: 'ri-typhoon-line',
        earthquake: 'ri-earthquake-line',
        flood: 'ri-flood-line',
        storm: 'ri-thunderstorms-line',
        heat: 'ri-temp-hot-line',
        default: 'ri-alarm-warning-line'
    };
    
    const card = document.createElement('div');
    card.className = `alert-card severity-${alert.severity.toLowerCase()}`;
    card.innerHTML = `
        <div class="alert-header">
            <div class="alert-icon ${alert.severity.toLowerCase()}">
                <i class="${icons[alert.type] || icons.default}"></i>
            </div>
            <div class="alert-title">
                <h3>${alert.name}</h3>
                <span class="alert-source">${alert.source || 'Weather Service'}</span>
            </div>
            <span class="severity-badge ${alert.severity.toLowerCase()}">${alert.severity}</span>
        </div>
        <p class="alert-description">${alert.description}</p>
        <div class="alert-footer">
            <span class="alert-time"><i class="ri-time-line"></i> ${alert.time || 'Just now'}</span>
        </div>
    `;
    
    return card;
}

function getSeverityFromAlert(alert) {
    const tags = alert.tags || [];
    if (tags.includes('Extreme')) return 'HIGH';
    if (tags.includes('Severe')) return 'HIGH';
    if (tags.includes('Moderate')) return 'MEDIUM';
    return 'LOW';
}

function getAlertType(event) {
    const eventLower = event.toLowerCase();
    if (eventLower.includes('typhoon') || eventLower.includes('cyclone')) return 'typhoon';
    if (eventLower.includes('earthquake')) return 'earthquake';
    if (eventLower.includes('flood')) return 'flood';
    if (eventLower.includes('storm') || eventLower.includes('thunder')) return 'storm';
    if (eventLower.includes('heat')) return 'heat';
    return 'default';
}

// ================================
// AUTO-SOS SYSTEM
// ================================
function initializeAutoSOS() {
    const toggle = document.getElementById('autoSOSToggle');
    if (toggle) {
        toggle.checked = autoSOSEnabled;
        toggle.addEventListener('change', function() {
            autoSOSEnabled = this.checked;
            showNotification(
                autoSOSEnabled ? 'Auto-SOS enabled' : 'Auto-SOS disabled',
                autoSOSEnabled ? 'success' : 'info'
            );
        });
    }
}

function checkSevereAlerts(alerts) {
    if (!autoSOSEnabled) return;
    
    const severeAlert = alerts.find(a => 
        a.severity === 'HIGH' || 
        (a.tags && a.tags.includes('Extreme'))
    );
    
    if (severeAlert) {
        showAreYouSafePrompt(severeAlert);
    }
}

function showAreYouSafePrompt(alert) {
    // Don't show if already showing
    if (document.getElementById('safetyPromptModal')?.classList.contains('active')) return;
    
    // Create modal
    let modal = document.getElementById('safetyPromptModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'safetyPromptModal';
        modal.className = 'modal';
        document.body.appendChild(modal);
    }
    
    modal.innerHTML = `
        <div class="modal-content safety-prompt">
            <div class="alert-pulse"></div>
            <div class="modal-icon warning">
                <i class="ri-alarm-warning-fill"></i>
            </div>
            <h2>⚠️ ${alert.name || 'Disaster Alert'}</h2>
            <p class="alert-desc">${alert.description || 'A severe weather event has been detected in your area.'}</p>
            
            <div class="safety-question">
                <h3>Are You Safe?</h3>
                <p>Please respond to let your emergency contacts know your status.</p>
            </div>
            
            <div class="countdown-warning">
                <i class="ri-timer-line"></i>
                Auto-SOS in <span id="safetyCountdown">30</span> seconds if no response
            </div>
            
            <div class="modal-actions">
                <button class="btn btn-safe" id="btnImSafe">
                    <i class="ri-check-line"></i> I'm Safe
                </button>
                <button class="btn btn-danger" id="btnNeedHelp">
                    <i class="ri-alarm-warning-fill"></i> I Need Help
                </button>
            </div>
        </div>
    `;
    
    modal.classList.add('active');
    
    // Vibrate
    vibrate([500, 200, 500, 200, 500]);
    
    // Start countdown
    let countdown = 30;
    const countdownEl = document.getElementById('safetyCountdown');
    
    if (countdownInterval) clearInterval(countdownInterval);
    
    countdownInterval = setInterval(() => {
        countdown--;
        if (countdownEl) countdownEl.textContent = countdown;
        
        if (countdown === 20 || countdown === 10 || countdown === 5) {
            vibrate([200, 100, 200]);
        }
        
        if (countdown <= 0) {
            clearInterval(countdownInterval);
            modal.classList.remove('active');
            triggerAutoSOS(alert);
        }
    }, 1000);
    
    // I'm Safe button
    document.getElementById('btnImSafe').onclick = () => {
        clearInterval(countdownInterval);
        modal.classList.remove('active');
        markAsSafe(alert);
    };
    
    // Need Help button
    document.getElementById('btnNeedHelp').onclick = () => {
        clearInterval(countdownInterval);
        modal.classList.remove('active');
        triggerAutoSOS(alert);
    };
}

// ================================
// MARK AS SAFE - Send SMS
// ================================
function markAsSafe(alert) {
    const userData = getUserData();
    const contacts = getEmergencyContacts();
    
    // Build safe message
    let message = `✅ SAFETY UPDATE ✅\n\n`;
    message += `${userData.name} is SAFE.\n\n`;
    message += `Alert: ${alert.name || 'Weather Alert'}\n`;
    message += `Status: I am okay, no assistance needed.\n`;
    message += `Time: ${new Date().toLocaleString()}\n\n`;
    message += `⚠️ This person is DEAF/MUTE - Please respond via TEXT only.`;
    
    if (contacts.length > 0) {
        const phoneNumbers = contacts.map(c => c.phone.replace(/\s/g, '')).join(',');
        openSMSIntent(phoneNumbers, message);
    }
    
    // Update UI
    updateSafetyStatus('safe');
    showNotification('Status updated: You are SAFE', 'success');
    vibrate([100, 50, 100]);
    
    // Log to database
    logDisasterResponse('safe', alert.name);
}

// ================================
// TRIGGER AUTO-SOS - Send Emergency SMS
// ================================
function triggerAutoSOS(alert) {
    const userData = getUserData();
    const contacts = getEmergencyContacts();
    
    // Build emergency message
    let message = `🚨 EMERGENCY ALERT 🚨\n`;
    message += `⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\n`;
    message += `Name: ${userData.name}\n`;
    message += `Status: NEEDS IMMEDIATE HELP\n\n`;
    message += `⚠️ DISASTER DETECTED:\n`;
    message += `${alert.name || 'Severe Weather'}\n`;
    message += `${alert.description || ''}\n\n`;
    
    if (userLocation) {
        message += `📍 LOCATION:\n`;
        message += `https://maps.google.com/?q=${userLocation.lat},${userLocation.lng}\n\n`;
    }
    
    if (userData.bloodType || userData.allergies) {
        message += `🏥 MEDICAL INFO:\n`;
        if (userData.bloodType) message += `Blood Type: ${userData.bloodType}\n`;
        if (userData.allergies) message += `Allergies: ${userData.allergies}\n`;
        if (userData.medications) message += `Medications: ${userData.medications}\n`;
    }
    
    message += `\nTime: ${new Date().toLocaleString()}\n`;
    message += `\n⚠️ User did not respond to safety check. Please verify their safety immediately.`;
    
    if (contacts.length > 0) {
        const phoneNumbers = contacts.map(c => c.phone.replace(/\s/g, '')).join(',');
        openSMSIntent(phoneNumbers, message);
        showNotification('Opening SMS with emergency alert...', 'warning');
    } else {
        showNotification('No emergency contacts! Add contacts in Medical Profile.', 'error');
    }
    
    // Update UI
    updateSafetyStatus('danger');
    vibrate([500, 200, 500, 200, 500]);
    
    // Log to database
    logDisasterResponse('sos', alert.name);
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
// GET USER DATA
// ================================
function getUserData() {
    const userDataEl = document.getElementById('userData');
    
    if (userDataEl) {
        return {
            name: userDataEl.dataset.name || 'User',
            phone: userDataEl.dataset.phone || '',
            bloodType: userDataEl.dataset.bloodType || '',
            allergies: userDataEl.dataset.allergies || '',
            medications: userDataEl.dataset.medications || ''
        };
    }
    
    return { name: 'Silent Signal User' };
}

// ================================
// GET EMERGENCY CONTACTS
// ================================
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
// UPDATE SAFETY STATUS UI
// ================================
function updateSafetyStatus(status) {
    const badge = document.getElementById('userSafetyStatus');
    if (badge) {
        if (status === 'safe') {
            badge.className = 'status-badge safe';
            badge.innerHTML = '<i class="ri-check-line"></i> SAFE';
        } else if (status === 'danger') {
            badge.className = 'status-badge danger';
            badge.innerHTML = '<i class="ri-alarm-warning-line"></i> NEEDS HELP';
        } else {
            badge.className = 'status-badge unknown';
            badge.innerHTML = '<i class="ri-question-line"></i> UNKNOWN';
        }
    }
}

// ================================
// LOG TO DATABASE
// ================================
function logDisasterResponse(status, alertName) {
    fetch(`${BASE_URL}index.php?action=log-disaster-response`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            status: status,
            alert_name: alertName,
            location: userLocation,
            timestamp: new Date().toISOString()
        })
    }).catch(err => console.log('Failed to log response:', err));
}

// ================================
// EVENT LISTENERS
// ================================
function initializeEventListeners() {
    // Refresh button
    const refreshBtn = document.getElementById('refreshWeather');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', () => {
            refreshBtn.classList.add('spinning');
            fetchWeatherData();
            fetchWeatherAlerts();
            setTimeout(() => refreshBtn.classList.remove('spinning'), 1000);
            showNotification('Weather data refreshed', 'success');
        });
    }
    
    // Manual safety check button
    const checkSafetyBtn = document.getElementById('checkSafetyBtn');
    if (checkSafetyBtn) {
        checkSafetyBtn.addEventListener('click', () => {
            showAreYouSafePrompt({ 
                name: 'Safety Check', 
                description: 'Manual safety check requested.' 
            });
        });
    }
    
    // Test alert button
    const testAlertBtn = document.getElementById('testAlertBtn');
    if (testAlertBtn) {
        testAlertBtn.addEventListener('click', () => {
            showAreYouSafePrompt({
                name: 'TEST ALERT',
                description: 'This is a test alert. The system is working correctly.'
            });
        });
    }
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

// CSS animations
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
    .spinning {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);