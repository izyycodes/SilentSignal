<?php
// views/dashboard.php
// User Dashboard - Data is passed from UserController

require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<meta name="base-url" content="<?php echo BASE_URL; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/dashboard.css">

<!-- Hidden User Data for SOS -->
<div id="userData" 
    data-name="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>"
    data-phone="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>"
    data-pwd-id="<?php echo htmlspecialchars($userData['pwdId'] ?? ''); ?>"
    data-address="<?php echo htmlspecialchars($userData['address'] ?? ''); ?>"
    data-blood-type="<?php echo htmlspecialchars($userData['bloodType'] ?? ''); ?>"
    data-allergies="<?php echo htmlspecialchars($userData['allergies'] ?? ''); ?>"
    data-medications="<?php echo htmlspecialchars($userData['medications'] ?? ''); ?>"
    data-conditions="<?php echo htmlspecialchars($userData['conditions'] ?? ''); ?>"
    style="display: none;">
</div>

<!-- Hidden Emergency Contacts Data -->
<div id="emergencyContactsData" 
    data-contacts='<?php echo json_encode($emergencyContacts ?? []); ?>'
    style="display: none;">
</div>

<div class="dashboard-container">
    
    <!-- Quick SOS Section -->
    <div class="quick-sos-section">
        <div class="sos-info">
            <h2>Emergency SOS</h2>
            <p>Tap the button or hold anywhere for 3 seconds to send an emergency alert</p>
        </div>
        <button class="quick-sos-btn" id="quickSOSBtn">
            <i class="ri-alarm-warning-fill"></i>
            <span>SOS</span>
        </button>
    </div>

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

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>
<script src="<?php echo BASE_URL; ?>assets/js/dashboard.js"></script>
</body>
</html>