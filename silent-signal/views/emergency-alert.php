<?php
// views/emergency-alert.php
// Emergency Alert System - Data is passed from UserController

require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/emergency-alert.css">

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-icon" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);">
            <i class="ri-alarm-warning-fill"></i>
        </div>
        <div class="page-header-content">
            <h1>Emergency Alert System</h1>
            <p>One-tap emergency response</p>
        </div>
        <div class="page-header-status">
            <span class="status-badge status-active">SOS ACTIVE</span>
        </div>
    </div>

    <!-- SOS Alert Sent Banner (Hidden by default) -->
    <div class="alert-banner alert-success" id="sosSuccessBanner" style="display: none;">
        <div class="alert-banner-icon"><i class="ri-checkbox-circle-fill"></i></div>
        <div class="alert-banner-content">
            <h3>SOS ALERT SENT!</h3>
            <p>Emergency contacts notified. Help is on the way.</p>
        </div>
        <span class="alert-banner-meta">GPS Location Shared ✓</span>
    </div>

    <!-- Single-Tap SOS Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon <?php echo $featureCards[0]['color']; ?>">
                <i class="<?php echo $featureCards[0]['icon']; ?>"></i>
            </div>
            <h2><?php echo $featureCards[0]['title']; ?></h2>
        </div>
        <p class="card-description"><?php echo $featureCards[0]['description']; ?></p>
        
        <div class="sos-container">
            <button class="sos-button" id="sosButton">
                <i class="ri-alarm-warning-fill"></i>
                <span class="sos-text">EMERGENCY SOS</span>
                <span class="sos-subtext">Single-tap only</span>
            </button>
        </div>

        <div class="info-cards">
            <?php foreach ($infoCards as $card): ?>
                <div class="info-card">
                    <div class="info-card-icon"><i class="<?php echo $card['icon']; ?>"></i></div>
                    <span><?php echo $card['label']; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Shake-to-Alert Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon <?php echo $featureCards[1]['color']; ?>">
                <i class="<?php echo $featureCards[1]['icon']; ?>"></i>
            </div>
            <h2><?php echo $featureCards[1]['title']; ?></h2>
        </div>
        <p class="card-description"><?php echo $featureCards[1]['description']; ?></p>
        
        <div class="toggle-row">
            <span class="toggle-label">Shake Detection Active</span>
            <label class="toggle-switch">
                <input type="checkbox" id="shakeToggle" checked>
                <span class="toggle-slider"></span>
            </label>
        </div>
        <button class="btn btn-warning btn-block" id="testShakeBtn">Test Shake Detection</button>
    </div>

    <!-- Panic-Click Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon <?php echo $featureCards[2]['color']; ?>">
                <i class="<?php echo $featureCards[2]['icon']; ?>"></i>
            </div>
            <h2><?php echo $featureCards[2]['title']; ?></h2>
        </div>
        <p class="card-description"><?php echo $featureCards[2]['description']; ?></p>
        
        <div class="panic-stats">
            <div class="panic-stat-card">
                <div class="panic-stat-value" id="tapCount">0</div>
                <div class="panic-stat-label">Rapid Taps</div>
                <div class="panic-stat-meta">in 3 seconds</div>
            </div>
            <div class="panic-stat-card">
                <div class="panic-stat-icon"><i class="ri-arrow-right-up-line"></i></div>
                <div class="panic-stat-label">Auto Escalation</div>
                <div class="panic-stat-meta">to SOS mode</div>
            </div>
        </div>
    </div>

    <!-- Auto-send Message Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon <?php echo $featureCards[3]['color']; ?>">
                <i class="<?php echo $featureCards[3]['icon']; ?>"></i>
            </div>
            <h2><?php echo $featureCards[3]['title']; ?></h2>
        </div>
        <p class="card-description"><?php echo $featureCards[3]['description']; ?></p>
        
        <div class="sms-preview">
            <div class="sms-preview-header">
                <i class="ri-smartphone-line"></i>
                <span>SMS Preview</span>
            </div>
            <div class="sms-preview-content">
                <div class="sms-badge"><?php echo $smsPreview['badge']; ?></div>
                <?php foreach ($smsPreview['lines'] as $line): ?>
                    <p class="sms-text"><?php echo $line; ?></p>
                <?php endforeach; ?>
                <a href="#" class="sms-link">📍 <?php echo $smsPreview['link']; ?></a>
            </div>
        </div>

        <div class="contacts-section">
            <h4>Emergency Contacts</h4>
            <div class="contacts-list">
                <?php foreach ($emergencyContacts as $contact): ?>
                    <div class="contact-item">
                        <span class="contact-name"><?php echo $contact['name']; ?></span>
                        <span class="contact-phone <?php echo $contact['isEmergency'] ? 'emergency' : ''; ?>">
                            <?php echo $contact['phone']; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Multi-sensory Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon <?php echo $featureCards[4]['color']; ?>">
                <i class="<?php echo $featureCards[4]['icon']; ?>"></i>
            </div>
            <h2><?php echo $featureCards[4]['title']; ?></h2>
        </div>
        <p class="card-description"><?php echo $featureCards[4]['description']; ?></p>
        
        <div class="confirmation-options">
            <?php foreach ($confirmationOptions as $option): ?>
                <div class="confirmation-card">
                    <div class="confirmation-icon"><i class="<?php echo $option['icon']; ?>"></i></div>
                    <h4><?php echo $option['title']; ?></h4>
                    <p><?php echo $option['desc']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="flash-overlay" id="flashOverlay"></div>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>
<script src="<?php echo BASE_URL; ?>assets/js/emergency-alert.js"></script>
</body>
</html>