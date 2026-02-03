<?php
// views/disaster-monitoring.php
// Disaster Monitoring & Auto-Alert - User Module

$pageTitle = "Disaster Monitoring - Silent Signal";

// ================================
// DATA ARRAYS - Easy to modify
// ================================

// Active disaster alerts (would come from API/database)
$disasterAlerts = [
    [
        'type' => 'typhoon',
        'name' => 'Typhoon Odette',
        'source' => 'PAGASA',
        'severity' => 'HIGH',
        'description' => 'Category 4 typhoon approaching. Expected landfall in 6 hours.',
        'stats' => [
            ['label' => 'Wind Speed', 'value' => '185 km/h', 'class' => 'danger'],
        ],
        'location' => 'Western Visayas',
        'time' => '10 minutes ago',
    ],
    [
        'type' => 'earthquake',
        'name' => 'Earthquake Detected',
        'source' => 'PHIVOLCS',
        'severity' => 'MEDIUM',
        'description' => 'Magnitude 4.2 earthquake recorded. No tsunami threat.',
        'stats' => [
            ['label' => 'Magnitude', 'value' => '4.2', 'class' => 'warning'],
            ['label' => 'Depth', 'value' => '10 km', 'class' => ''],
        ],
        'location' => 'Negros Occidental',
        'time' => '1 hour ago',
    ],
];

// Weather conditions data
$weatherConditions = [
    ['icon' => 'ri-temp-hot-line', 'label' => 'Temperature', 'value' => '28°C'],
    ['icon' => 'ri-drop-line', 'label' => 'Humidity', 'value' => '89%'],
    ['icon' => 'ri-windy-line', 'label' => 'Wind Speed', 'value' => '45 km/h'],
    ['icon' => 'ri-rainy-line', 'label' => 'Rainfall', 'value' => 'Heavy'],
    ['icon' => 'ri-dashboard-3-line', 'label' => 'Pressure', 'value' => '1005 hPa', 'fullWidth' => true],
];

// Auto-SOS checklist items
$autoSosSteps = [
    'Alert triggered by disaster detection',
    '"Are You Safe?" prompt displayed',
    '30 second countdown begins',
    'If no response: Auto SOS sent with GPS location',
];

// Alert history (would come from database)
$alertHistory = [
    ['type' => 'typhoon', 'name' => 'Typhoon Alert', 'time' => '2 hours ago', 'status' => 'dismissed'],
    ['type' => 'earthquake', 'name' => 'Earthquake Alert', 'time' => '5 hours ago', 'status' => 'responded'],
    ['type' => 'flood', 'name' => 'Flood Warning', 'time' => '1 day ago', 'status' => 'auto-sos'],
];

// Alert type icons mapping
$alertIcons = [
    'typhoon' => 'ri-typhoon-line',
    'earthquake' => 'ri-earthquake-line',
    'flood' => 'ri-flood-line',
];

// Severity badge classes
$severityClasses = [
    'HIGH' => 'high',
    'MEDIUM' => 'medium',
    'LOW' => 'low',
];

require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/disaster-monitoring.css">

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-icon" style="background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);">
            <i class="ri-earth-line"></i>
        </div>
        <div class="page-header-content">
            <h1>Disaster Monitoring & Auto-Alert</h1>
            <p>Real-time alerts from PAGASA & PHIVOLCS</p>
        </div>
        <div class="page-header-meta">
            <span class="api-badge">API Integration</span>
            <span class="status-indicator active"></span>
            <span class="status-text">Live Monitoring</span>
        </div>
    </div>

    <!-- Are You Safe Banner -->
    <div class="safety-prompt-card" id="safetyPrompt">
        <div class="safety-prompt-content">
            <h2>ARE YOU SAFE?</h2>
            <p>A typhoon alert has been issued in your area. Please confirm your safety status.</p>
            
            <div class="safety-buttons">
                <button class="safety-btn safe" id="btnSafe">
                    <i class="ri-checkbox-circle-line"></i>
                    <span>I'M SAFE</span>
                    <small>Everything is okay</small>
                </button>
                <button class="safety-btn help" id="btnHelp">
                    <i class="ri-close-circle-line"></i>
                    <span>SEND HELP</span>
                    <small>I need assistance</small>
                </button>
            </div>
        </div>
        
        <div class="auto-sos-warning">
            <i class="ri-timer-line"></i>
            <span>Auto-send SOS in <strong id="countdown">30</strong> seconds if no response</span>
        </div>
    </div>

    <!-- Active Disaster Alerts -->
    <?php foreach ($disasterAlerts as $alert): ?>
        <div class="alert-card <?php echo $alert['type']; ?>">
            <div class="alert-card-header">
                <div class="alert-icon <?php echo $alert['type']; ?>">
                    <i class="<?php echo $alertIcons[$alert['type']]; ?>"></i>
                </div>
                <div class="alert-info">
                    <h3><?php echo $alert['name']; ?></h3>
                    <span class="alert-source">via <?php echo $alert['source']; ?></span>
                </div>
                <span class="alert-badge <?php echo $severityClasses[$alert['severity']]; ?>">
                    <?php echo $alert['severity']; ?>
                </span>
            </div>
            
            <p class="alert-description"><?php echo $alert['description']; ?></p>
            
            <div class="alert-stats <?php echo count($alert['stats']) > 1 ? 'two-col' : ''; ?>">
                <?php foreach ($alert['stats'] as $stat): ?>
                    <div class="alert-stat">
                        <span class="stat-label"><?php echo $stat['label']; ?></span>
                        <span class="stat-value <?php echo $stat['class']; ?>"><?php echo $stat['value']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="alert-meta">
                <span><i class="ri-map-pin-line"></i> <?php echo $alert['location']; ?></span>
                <span><i class="ri-time-line"></i> <?php echo $alert['time']; ?></span>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Current Weather Conditions -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon blue"><i class="ri-cloud-line"></i></div>
            <h2>Current Weather Conditions</h2>
        </div>
        
        <div class="weather-grid">
            <?php foreach ($weatherConditions as $weather): ?>
                <div class="weather-item <?php echo isset($weather['fullWidth']) ? 'full-width' : ''; ?>">
                    <i class="<?php echo $weather['icon']; ?>"></i>
                    <div class="weather-info">
                        <span class="weather-label"><?php echo $weather['label']; ?></span>
                        <span class="weather-value"><?php echo $weather['value']; ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Auto-send SOS if No Response -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon green"><i class="ri-timer-flash-line"></i></div>
            <h2>Auto-send SOS if No Response</h2>
            <span class="feature-badge enabled">ENABLED</span>
        </div>
        <p class="card-description">If you don't respond to the "Are You Safe?" prompt within 30 seconds, an automatic SOS alert will be sent to your emergency contacts with your location.</p>
        
        <div class="auto-sos-info">
            <div class="auto-sos-timer">
                <i class="ri-time-line"></i>
                <span>Auto-send Timer: <strong>30 seconds</strong></span>
            </div>
            <ul class="auto-sos-list">
                <?php foreach ($autoSosSteps as $step): ?>
                    <li><i class="ri-check-line"></i> <?php echo $step; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Visual Flash Alert -->
    <div class="card visual-flash-card">
        <div class="card-header">
            <div class="card-icon orange"><i class="ri-flashlight-line"></i></div>
            <h2>Visual Flash Alert</h2>
        </div>
        <p class="card-description">Strong vibration pattern combined with full-screen color flash for visual confirmation.</p>
        
        <div class="flash-demo">
            <div class="flash-demo-screen"><span>ALERT ACTIVE</span></div>
        </div>
    </div>

    <!-- Alert History -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon purple"><i class="ri-history-line"></i></div>
            <h2>Alert History</h2>
        </div>
        
        <div class="history-list">
            <?php foreach ($alertHistory as $history): ?>
                <div class="history-item">
                    <div class="history-icon <?php echo $history['type']; ?>">
                        <i class="<?php echo $alertIcons[$history['type']]; ?>"></i>
                    </div>
                    <div class="history-content">
                        <h4><?php echo $history['name']; ?></h4>
                        <p><?php echo $history['time']; ?></p>
                    </div>
                    <span class="history-badge <?php echo $history['status']; ?>">
                        <?php echo strtoupper(str_replace('-', ' ', $history['status'])); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>
<script src="<?php echo BASE_URL; ?>assets/js/disaster-monitoring.js"></script>
</body>
</html>