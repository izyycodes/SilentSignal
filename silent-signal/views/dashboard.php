<?php
// views/dashboard.php
// User Dashboard - Main landing page after login

$pageTitle = "Dashboard - Silent Signal";

// ================================
// DATA ARRAYS - Easy to modify
// ================================

// Current user status (would come from database)
$userStatus = [
    'status' => 'safe', // safe, danger, unknown
    'label' => "I'M SAFE",
    'lastUpdated' => '2 minutes ago',
];

// Module cards data
$moduleCards = [
    [
        'id' => 'emergency-alert',
        'action' => 'emergency-alert',
        'icon' => 'ri-alarm-warning-line',
        'iconBg' => '#fff3e0',
        'iconColor' => '#ef6c00',
        'title' => 'Emergency Alert',
        'description' => 'Single-tap SOS, shake-to-alert, panic detection with GPS location & medical data via SMS.',
        'features' => [
            ['icon' => 'ri-flashlight-line', 'text' => 'Instant SOS transmission'],
            ['icon' => 'ri-map-pin-line', 'text' => 'GPS location sharing'],
            ['icon' => 'ri-vibrate-line', 'text' => 'Multi-sensory confirmation'],
        ],
    ],
    [
        'id' => 'disaster-monitor',
        'action' => 'disaster-monitor',
        'icon' => 'ri-earth-line',
        'iconBg' => '#e8f5e9',
        'iconColor' => '#43a047',
        'title' => 'Disaster Monitoring',
        'description' => 'Typhoon/earthquake detection via PAGASA/PHIVOLCS with "Are You Safe?" visual prompts.',
        'alertBadge' => [
            'label' => 'RECENT ALERTS',
            'count' => '2 active alerts in your area',
        ],
    ],
    [
        'id' => 'family-checkin',
        'action' => 'family-checkin',
        'icon' => 'ri-team-line',
        'iconBg' => '#e3f2fd',
        'iconColor' => '#1976d2',
        'title' => 'Family Check-in',
        'description' => 'Real-time GPS tracking, photo/video updates, family dashboard with safety status.',
        'familyStatus' => [
            ['name' => 'M', 'color' => '#e53935', 'status' => 'safe'],
            ['name' => 'J', 'color' => '#ffc107', 'status' => 'safe'],
            ['name' => 'A', 'color' => '#43a047', 'status' => 'safe'],
        ],
        'safeCount' => '2/3 safe',
    ],
    [
        'id' => 'communication-hub',
        'action' => 'communication-hub',
        'icon' => 'ri-message-2-line',
        'iconBg' => '#fce4ec',
        'iconColor' => '#d81b60',
        'title' => 'Communication Hub',
        'description' => 'Icon-based messages, one-tap camera alerts, SMS communication with safety instructions.',
        'quickIcons' => [
            'ri-hospital-line',
            'ri-restaurant-line',
            'ri-alarm-warning-line',
            'ri-first-aid-kit-line',
        ],
    ],
];

// Recent activity (would come from database)
$recentActivity = [
    [
        'type' => 'typhoon',
        'icon' => 'ri-typhoon-line',
        'iconBg' => '#ffebee',
        'iconColor' => '#e53935',
        'title' => 'Typhoon Regis approaching',
        'time' => '10 min ago',
        'badge' => 'HIGH',
        'badgeClass' => 'high',
    ],
    [
        'type' => 'earthquake',
        'icon' => 'ri-earthquake-line',
        'iconBg' => '#fff3e0',
        'iconColor' => '#ef6c00',
        'title' => 'Magnitude 4.2 detected',
        'time' => '1 hour ago',
        'badge' => 'MEDIUM',
        'badgeClass' => 'medium',
    ],
];

require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/dashboard.css">

<div class="dashboard-container">
    
    <!-- Status Banner -->
    <div class="status-banner status-<?php echo $userStatus['status']; ?>">
        <div class="status-content">
            <span class="status-label">Your Current Status</span>
            <h2 class="status-title"><?php echo $userStatus['label']; ?></h2>
            <span class="status-time">Last Updated: <?php echo $userStatus['lastUpdated']; ?></span>
        </div>
        <div class="status-icon">
            <i class="ri-heart-pulse-line"></i>
        </div>
    </div>

    <!-- Module Cards Grid -->
    <div class="modules-grid">
        <?php foreach ($moduleCards as $module): ?>
            <a href="<?php echo BASE_URL; ?>index.php?action=<?php echo $module['action']; ?>" class="module-card">
                <div class="module-header">
                    <div class="module-icon" style="background: <?php echo $module['iconBg']; ?>; color: <?php echo $module['iconColor']; ?>;">
                        <i class="<?php echo $module['icon']; ?>"></i>
                    </div>
                    <h3><?php echo $module['title']; ?></h3>
                    <i class="ri-arrow-right-s-line module-arrow"></i>
                </div>
                
                <p class="module-description"><?php echo $module['description']; ?></p>
                
                <?php if (isset($module['features'])): ?>
                    <ul class="module-features">
                        <?php foreach ($module['features'] as $feature): ?>
                            <li>
                                <i class="<?php echo $feature['icon']; ?>"></i>
                                <span><?php echo $feature['text']; ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                
                <?php if (isset($module['alertBadge'])): ?>
                    <div class="module-alert-badge">
                        <span class="alert-label"><?php echo $module['alertBadge']['label']; ?></span>
                        <span class="alert-count"><?php echo $module['alertBadge']['count']; ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($module['familyStatus'])): ?>
                    <div class="module-family">
                        <div class="family-avatars">
                            <?php foreach ($module['familyStatus'] as $member): ?>
                                <div class="family-avatar" style="background: <?php echo $member['color']; ?>;">
                                    <?php echo $member['name']; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <span class="family-count"><?php echo $module['safeCount']; ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($module['quickIcons'])): ?>
                    <div class="module-quick-icons">
                        <?php foreach ($module['quickIcons'] as $icon): ?>
                            <div class="quick-icon">
                                <i class="<?php echo $icon; ?>"></i>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Recent Activity Section -->
    <div class="recent-activity">
        <div class="section-header">
            <div class="section-title">
                <i class="ri-time-line"></i>
                <h3>Recent Activity</h3>
            </div>
        </div>
        
        <div class="activity-list">
            <?php foreach ($recentActivity as $activity): ?>
                <div class="activity-item">
                    <div class="activity-icon" style="background: <?php echo $activity['iconBg']; ?>; color: <?php echo $activity['iconColor']; ?>;">
                        <i class="<?php echo $activity['icon']; ?>"></i>
                    </div>
                    <div class="activity-content">
                        <h4><?php echo $activity['title']; ?></h4>
                        <span class="activity-time"><?php echo $activity['time']; ?></span>
                    </div>
                    <span class="activity-badge <?php echo $activity['badgeClass']; ?>">
                        <?php echo $activity['badge']; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>
</body>
</html>