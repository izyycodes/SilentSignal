<?php
// views/admin/dashboard.php
require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin-dashboard.css">

<div class="dashboard-container">
    <!-- Admin Dashboard Welcome Header -->
    <div class="admin-dashboard-header">
        <div class="admin-dashboard-header-content">
            <h1>Welcome, <?php echo $_SESSION['user_name']; ?></h1>
            <p>Administrator Dashboard</p>
        </div>
        <div class="header-actions">
            <button class="header-btn" onclick="window.location.reload()">
                <i class="ri-refresh-line"></i>
            </button>
            <button class="header-btn">
                <i class="ri-notification-3-line"></i>
                <span class="badge">3</span>
            </button>
            <a href="<?php echo BASE_URL; ?>index.php?action=dashboard" class="header-btn" title="User View">
                <i class="ri-eye-line"></i>
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">
                <i class="ri-user-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo number_format($stats['totalUsers']); ?></div>
                <div class="stat-label">Total Users</div>
                <div class="stat-change positive">
                    <i class="ri-arrow-up-line"></i> <?php echo $stats['userGrowth']; ?>% this month
                </div>
            </div>
        </div>

        <div class="stat-card red">
            <div class="stat-icon">
                <i class="ri-alarm-warning-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stats['activeAlerts']; ?></div>
                <div class="stat-label">Active Alerts</div>
                <div class="stat-change">
                    <?php echo $stats['resolvedToday']; ?> resolved today
                </div>
            </div>
        </div>

        <div class="stat-card orange">
            <div class="stat-icon">
                <i class="ri-earth-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stats['disasterAlerts']; ?></div>
                <div class="stat-label">Disaster Alerts</div>
                <div class="stat-change">
                    <?php echo $stats['activeDisasters']; ?> active
                </div>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon">
                <i class="ri-message-3-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $stats['messageInquiries']; ?></div>
                <div class="stat-label">Message Inquiries</div>
                <div class="stat-change">
                    <?php echo $stats['pendingMessages']; ?> pending
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="quick-actions-section">
        <h2><i class="ri-flashlight-line"></i> Quick Actions</h2>
        <div class="quick-actions-grid">
            <div class="action-card" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?action=admin-users'">
                <div class="action-icon blue">
                    <i class="ri-user-settings-line"></i>
                </div>
                <h3>Manage Users</h3>
                <p>View and manage user accounts</p>
                <span class="action-arrow"><i class="ri-arrow-right-line"></i></span>
            </div>

            <div class="action-card" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?action=admin-emergency-alerts'">
                <div class="action-icon red">
                    <i class="ri-alarm-warning-fill"></i>
                </div>
                <h3>Emergency Alerts</h3>
                <p>Monitor emergency situations</p>
                <span class="action-arrow"><i class="ri-arrow-right-line"></i></span>
            </div>

            <div class="action-card" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?action=admin-disaster-alerts'">
                <div class="action-icon orange">
                    <i class="ri-flood-line"></i>
                </div>
                <h3>Disaster Alerts</h3>
                <p>Manage disaster notifications</p>
                <span class="action-arrow"><i class="ri-arrow-right-line"></i></span>
            </div>

            <div class="action-card" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?action=admin-messages'">
                <div class="action-icon green">
                    <i class="ri-chat-3-fill"></i>
                </div>
                <h3>Message Inquiries</h3>
                <p>Respond to user messages</p>
                <span class="action-arrow"><i class="ri-arrow-right-line"></i></span>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2><i class="ri-time-line"></i> Recent Activity</h2>
            <a href="#" class="view-all-link">View All <i class="ri-arrow-right-line"></i></a>
        </div>
        <div class="activity-list">
            <?php foreach ($recentActivity as $activity): ?>
                <div class="activity-item">
                    <div class="activity-icon <?php echo $activity['type']; ?>">
                        <i class="<?php echo $activity['icon']; ?>"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title"><?php echo $activity['title']; ?></div>
                        <div class="activity-desc"><?php echo $activity['description']; ?></div>
                    </div>
                    <div class="activity-time"><?php echo $activity['time']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- System Health -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2><i class="ri-pulse-line"></i> System Health</h2>
        </div>
        <div class="health-grid">
            <div class="health-card">
                <div class="health-label">Server Status</div>
                <div class="health-value">
                    <span class="status-dot online"></span> Online
                </div>
            </div>
            <div class="health-card">
                <div class="health-label">Database</div>
                <div class="health-value">
                    <span class="status-dot online"></span> Connected
                </div>
            </div>
            <div class="health-card">
                <div class="health-label">SMS Service</div>
                <div class="health-value">
                    <span class="status-dot online"></span> Active
                </div>
            </div>
            <div class="health-card">
                <div class="health-label">API Status</div>
                <div class="health-value">
                    <span class="status-dot online"></span> Operational
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>