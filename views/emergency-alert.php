<?php
// views/emergency-alert.php
// Emergency Alert System - Data is passed from UserController

$pageStyles = [BASE_URL . 'assets/css/emergency-alert.css'];
require_once VIEW_PATH . 'includes/dashboard-header.php';
?>


<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-icon" style="background: linear-gradient(135deg, #ef6c00 0%, #e65100 100%);">
            <i class="ri-alarm-warning-fill"></i>
        </div>
        <div class="page-header-content">
            <h1>Emergency Alert System</h1>
            <p>One-tap SOS with GPS location and medical data</p>
        </div>
    </div>

    <!-- Main SOS Button -->
    <div class="sos-section">
        <button class="sos-button" id="sosButton">
            <div class="sos-button-inner">
                <i class="ri-alarm-warning-fill"></i>
                <span>SOS</span>
            </div>
            <div class="sos-pulse"></div>
            <div class="sos-pulse delay"></div>
        </button>
        <p class="sos-hint">Tap to send emergency alert</p>
        <p class="sos-hint-small">Long press (3 sec) or tap 5 times rapidly</p>
    </div>
    
    <!-- GPS Location Status -->
    <div class="card location-card">
        <div class="card-header">
            <div class="card-icon blue"><i class="ri-map-pin-line"></i></div>
            <h2>Your Location</h2>
            <div class="location-status-group">
                <span id="locationStatus" class="status-pending">Acquiring...</span>
                <button id="allowLocationBtn" class="allow-location-btn" onclick="requestLocationPermission()" style="display:none;">
                    <i class="ri-gps-line"></i> Allow Location
                </button>
            </div>
        </div>
        <div class="location-info">
            <div class="location-coords">
                <i class="ri-crosshair-2-line"></i>
                <span id="locationCoords">Waiting for GPS...</span>
            </div>
            <div class="location-address">
                <i class="ri-map-2-line"></i>
                <span id="locationAddress">Getting address...</span>
            </div>
            <a href="#" id="locationLink" class="btn btn-small" style="display: none;" target="_blank">
                <i class="ri-external-link-line"></i> View on Map
            </a>
        </div>
    </div>
    
    <!-- Quick Info Cards -->
    <div class="info-cards">
        <?php foreach ($infoCards as $card): ?>
            <div class="info-card">
                <i class="<?php echo $card['icon']; ?>"></i>
                <span><?php echo $card['label']; ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Feature Cards -->
    <div class="feature-cards">
        <?php foreach ($featureCards as $feature): ?>
            <div class="card feature-card" id="<?php echo $feature['id']; ?>">
                <div class="card-header">
                    <div class="card-icon <?php echo $feature['color']; ?>">
                        <i class="<?php echo $feature['icon']; ?>"></i>
                    </div>
                    <h2><?php echo $feature['title']; ?></h2>
                    <?php if ($feature['id'] === 'shake-alert'): ?>
                        <a href="<?php echo BASE_URL; ?>index.php?action=settings" class="shake-settings-link" title="Configure in Settings">
                            <i class="ri-settings-3-line"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <?php if ($feature['id'] === 'shake-alert'): ?>
                    <p class="card-description"><?php echo $feature['description']; ?></p>
                    <p class="shake-status-indicator" id="shakeStatusIndicator">
                        <i class="ri-information-line"></i> Status controlled from
                        <a href="<?php echo BASE_URL; ?>index.php?action=settings">Settings</a>
                    </p>
                <?php else: ?>
                    <p class="card-description"><?php echo $feature['description']; ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- SMS Preview -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon orange"><i class="ri-message-3-line"></i></div>
            <h2>SMS Preview</h2>
        </div>
        <div class="sms-preview">
            <div class="sms-bubble">
                <p class="sms-header">🚨 <strong>EMERGENCY ALERT</strong> 🚨</p>
                <p class="sms-warning">⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️</p>
                <br>
                <p>Name: <?php echo htmlspecialchars($userData['name'] ?? 'Not set'); ?></p>
                <?php if (!empty($userData['pwdId'])): ?>
                    <p>PWD ID: <?php echo htmlspecialchars($userData['pwdId']); ?></p>
                <?php endif; ?>
                <p>Phone: <?php echo htmlspecialchars($userData['phone'] ?? 'Not set'); ?></p>
                <p>Status: NEEDS IMMEDIATE HELP</p>
                <p>Time: [Current timestamp]</p>
                <br>
                <p>📍 Location: [GPS coordinates + Map link]</p>
                <br>
                <?php if (!empty($userData['bloodType']) || !empty($userData['allergies'])): ?>
                    <p><strong>🏥 Medical Info:</strong></p>
                    <?php if (!empty($userData['bloodType'])): ?>
                        <p>• Blood Type: <?php echo htmlspecialchars($userData['bloodType']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($userData['allergies'])): ?>
                        <p>• Allergies: <?php echo htmlspecialchars($userData['allergies']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($userData['medications'])): ?>
                        <p>• Medications: <?php echo htmlspecialchars($userData['medications']); ?></p>
                    <?php endif; ?>
                <?php endif; ?>
                <br>
                <p class="sms-footer">⚠️ Please respond via TEXT MESSAGE only.</p>
            </div>
        </div>
    </div>

    <!-- Multi-sensory Confirmation -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon purple"><i class="ri-notification-3-line"></i></div>
            <h2>Alert Confirmation</h2>
        </div>
        <div class="confirmation-options">
            <?php foreach ($confirmationOptions as $option): ?>
                <div class="confirmation-item">
                    <i class="<?php echo $option['icon']; ?>"></i>
                    <div>
                        <h4><?php echo $option['title']; ?></h4>
                        <p><?php echo $option['desc']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="btn btn-test" id="testAlertBtn">
            <i class="ri-play-line"></i> Test Alert Feedback
        </button>
    </div>
</div>

<!-- SOS Confirmation Modal -->
<div class="modal" id="sosConfirmModal">
    <div class="modal-content">
        <div class="modal-icon">
            <i class="ri-alarm-warning-fill"></i>
        </div>
        <h2>Send Emergency Alert?</h2>
        <p>This will send an SOS message to all your emergency contacts with your location.</p>
        <div class="countdown">
            Auto-sending in <span id="sosCountdown">10</span> seconds...
        </div>
        <p class="countdown-settings-hint"><i class="ri-settings-3-line"></i> Countdown duration is configurable in <a href="<?php echo BASE_URL; ?>index.php?action=settings">Settings</a></p>
        <div class="modal-actions">
            <button class="btn btn-danger" id="confirmSOSBtn">
                <i class="ri-send-plane-fill"></i> Send Now
            </button>
            <button class="btn btn-secondary" id="cancelSOSBtn">
                <i class="ri-close-line"></i> Cancel
            </button>
        </div>
    </div>
</div>

<!-- Pass PHP data to JavaScript -->
<script>
const BASE_URL              = <?php echo json_encode(BASE_URL); ?>;
const emergencyContactsData = <?php echo json_encode($emergencyContacts ?? []); ?>;
const userInfoData          = {
    name:        <?php echo json_encode(trim($userData['name']        ?? '')); ?>,
    phone:       <?php echo json_encode($userData['phone']       ?? ''); ?>,
    pwdId:       <?php echo json_encode($userData['pwdId']       ?? ''); ?>,
    address:     <?php echo json_encode($userData['address']     ?? ''); ?>,
    bloodType:   <?php echo json_encode($userData['bloodType']   ?? ''); ?>,
    allergies:   <?php echo json_encode($userData['allergies']   ?? ''); ?>,
    medications: <?php echo json_encode($userData['medications'] ?? ''); ?>,
    conditions:  <?php echo json_encode($userData['conditions']  ?? ''); ?>
};
</script>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>
<script src="<?php echo BASE_URL; ?>assets/js/emergency-alert.js"></script>
</body>
</html>