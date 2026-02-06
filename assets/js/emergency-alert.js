// Emergency Alert System JavaScript

document.addEventListener('DOMContentLoaded', function() {
    
    // Elements
    const sosButton = document.getElementById('sosButton');
    const sosSuccessBanner = document.getElementById('sosSuccessBanner');
    const flashOverlay = document.getElementById('flashOverlay');
    const shakeToggle = document.getElementById('shakeToggle');
    const testShakeBtn = document.getElementById('testShakeBtn');
    const tapCountDisplay = document.getElementById('tapCount');
    
    // Variables
    let tapCount = 0;
    let tapTimer = null;
    let isSOSActive = false;
    
    // ================================
    // SOS BUTTON - SINGLE TAP
    // ================================
    if (sosButton) {
        sosButton.addEventListener('click', function() {
            if (!isSOSActive) {
                triggerSOS();
            }
        });
    }
    
    // ================================
    // PANIC CLICK DETECTION
    // ================================
    document.addEventListener('click', function(e) {
        // Don't count clicks on interactive elements
        if (e.target.closest('button, a, input, select, textarea, .toggle-switch')) {
            return;
        }
        
        tapCount++;
        updateTapDisplay();
        
        // Reset timer
        clearTimeout(tapTimer);
        tapTimer = setTimeout(function() {
            tapCount = 0;
            updateTapDisplay();
        }, 3000);
        
        // Check for panic threshold (5 taps)
        if (tapCount >= 5) {
            tapCount = 0;
            updateTapDisplay();
            triggerSOS();
        }
    });
    
    function updateTapDisplay() {
        if (tapCountDisplay) {
            tapCountDisplay.textContent = tapCount;
            
            // Visual feedback
            if (tapCount >= 3) {
                tapCountDisplay.style.color = '#f44336';
            } else {
                tapCountDisplay.style.color = '#7b1fa2';
            }
        }
    }
    
    // ================================
    // SHAKE DETECTION
    // ================================
    let lastX, lastY, lastZ;
    let shakeThreshold = 15;
    let shakeCount = 0;
    let shakeTimer = null;
    
    if (window.DeviceMotionEvent && shakeToggle && shakeToggle.checked) {
        window.addEventListener('devicemotion', handleShake);
    }
    
    if (shakeToggle) {
        shakeToggle.addEventListener('change', function() {
            if (this.checked) {
                window.addEventListener('devicemotion', handleShake);
            } else {
                window.removeEventListener('devicemotion', handleShake);
            }
        });
    }
    
    function handleShake(event) {
        let acceleration = event.accelerationIncludingGravity;
        
        if (!acceleration) return;
        
        let x = acceleration.x;
        let y = acceleration.y;
        let z = acceleration.z;
        
        if (lastX !== undefined) {
            let deltaX = Math.abs(x - lastX);
            let deltaY = Math.abs(y - lastY);
            let deltaZ = Math.abs(z - lastZ);
            
            if (deltaX > shakeThreshold || deltaY > shakeThreshold || deltaZ > shakeThreshold) {
                shakeCount++;
                
                clearTimeout(shakeTimer);
                shakeTimer = setTimeout(function() {
                    shakeCount = 0;
                }, 2000);
                
                // 3 shakes trigger SOS
                if (shakeCount >= 3) {
                    shakeCount = 0;
                    triggerSOS();
                }
            }
        }
        
        lastX = x;
        lastY = y;
        lastZ = z;
    }
    
    // Test Shake Button (for devices without accelerometer)
    if (testShakeBtn) {
        testShakeBtn.addEventListener('click', function() {
            // Simulate shake detection
            showNotification('Shake detected! SOS would be triggered.', 'warning');
            
            // Visual feedback
            this.textContent = 'Shake Detected!';
            this.style.background = '#4caf50';
            
            setTimeout(() => {
                this.textContent = 'Test Shake Detection';
                this.style.background = '';
            }, 2000);
        });
    }
    
    // ================================
    // TRIGGER SOS
    // ================================
    function triggerSOS() {
        if (isSOSActive) return;
        isSOSActive = true;
        
        // Visual flash - Red then Green
        flashScreen('red');
        
        setTimeout(function() {
            flashScreen('green');
        }, 300);
        
        setTimeout(function() {
            flashOverlay.className = 'flash-overlay';
        }, 600);
        
        // Vibration pattern (if supported)
        if (navigator.vibrate) {
            navigator.vibrate([200, 100, 200, 100, 400]);
        }
        
        // Show success banner
        setTimeout(function() {
            if (sosSuccessBanner) {
                sosSuccessBanner.style.display = 'flex';
                sosSuccessBanner.scrollIntoView({ behavior: 'smooth' });
            }
            
            // Update SOS button
            if (sosButton) {
                sosButton.innerHTML = `
                    <i class="ri-checkbox-circle-fill"></i>
                    <span class="sos-text">SOS SENT</span>
                    <span class="sos-subtext">Help is on the way</span>
                `;
                sosButton.style.background = 'linear-gradient(135deg, #4caf50 0%, #388e3c 100%)';
            }
            
            // Send actual SOS (in real app, this would call API)
            sendSOSAlert();
            
        }, 700);
        
        // Reset after 10 seconds
        setTimeout(function() {
            resetSOS();
        }, 10000);
    }
    
    function flashScreen(color) {
        if (flashOverlay) {
            flashOverlay.className = 'flash-overlay flash-' + color;
        }
    }
    
    function resetSOS() {
        isSOSActive = false;
        
        if (sosSuccessBanner) {
            sosSuccessBanner.style.display = 'none';
        }
        
        if (sosButton) {
            sosButton.innerHTML = `
                <i class="ri-alarm-warning-fill"></i>
                <span class="sos-text">EMERGENCY SOS</span>
                <span class="sos-subtext">Single-tap only</span>
            `;
            sosButton.style.background = 'linear-gradient(135deg, #ff5252 0%, #d32f2f 100%)';
        }
    }
    
    // ================================
    // SEND SOS ALERT (API Call)
    // ================================
    function sendSOSAlert() {
        // Get GPS location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const data = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        timestamp: new Date().toISOString(),
                        type: 'sos'
                    };
                    
                    console.log('SOS Alert Data:', data);
                    
                    // In real app: send to server
                    // fetch('/api/sos', {
                    //     method: 'POST',
                    //     body: JSON.stringify(data)
                    // });
                },
                function(error) {
                    console.error('GPS Error:', error);
                    // Send SOS without precise location
                }
            );
        }
    }
    
    // ================================
    // NOTIFICATION HELPER
    // ================================
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = 'notification notification-' + type;
        notification.innerHTML = `
            <i class="ri-${type === 'success' ? 'checkbox-circle' : 'error-warning'}-fill"></i>
            <span>${message}</span>
        `;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'success' ? '#4caf50' : type === 'warning' ? '#ff9800' : '#f44336'};
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(function() {
            notification.remove();
        }, 3000);
    }
    
});