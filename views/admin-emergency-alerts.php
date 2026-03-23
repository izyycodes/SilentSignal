<?php
$pageStyles = [BASE_URL . 'assets/css/admin-emergency-alerts.css'];
require_once VIEW_PATH . 'includes/dashboard-header.php';
?>


<div class="dashboard-container">

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1>Emergency Alert Management</h1>
            <p>Monitor and respond to recent emergency alerts from users</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="ri-alarm-warning-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Alerts Today</div>
                <div class="stat-value"><?php echo number_format($stats['total_today']); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="ri-alert-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Critical Alerts</div>
                <div class="stat-value"><?php echo number_format($stats['critical']); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="ri-arrow-up-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Active Alerts</div>
                <div class="stat-value"><?php echo number_format($stats['active']); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="ri-checkbox-circle-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Resolved Today</div>
                <div class="stat-value"><?php echo number_format($stats['resolved_today']); ?></div>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    <div class="section-header">
        <h2><i class="ri-alarm-warning-line"></i> Recent Emergency Alerts</h2>
        <div class="section-controls">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" id="searchInput" placeholder="Search by user or location...">
            </div>
            <select class="filter-select" id="priorityFilter">
                <option value="">All Priorities</option>
                <option value="critical">Critical</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>
            <select class="filter-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="responded">Responded</option>
                <option value="resolved">Resolved</option>
            </select>
            <button class="btn-primary" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?action=admin-export-emergency-alerts'">
                <i class="ri-download-line"></i> Export Report
            </button>
        </div>
    </div>

    <!-- Alerts Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ALERT ID</th>
                    <th>USER</th>
                    <th>ALERT TYPE</th>
                    <th>PRIORITY</th>
                    <th>LOCATION</th>
                    <th>TIME</th>
                    <th>RESPONSE TIME</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alerts as $alert): ?>
                    <tr>
                        <td class="alert-id"><?php echo $alert['alert_id']; ?></td>
                        <td>
                            <div class="user-cell">
                                <div class="users-avatar <?php echo strtolower(substr($alert['user_name'], 0, 1)); ?>">
                                    <?php echo strtoupper(substr($alert['user_name'], 0, 2)); ?>
                                </div>
                                <div class="user-info">
                                    <div class="user-name"><?php echo $alert['user_name']; ?></div>
                                    <div class="user-id"><?php echo $alert['user_id']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="alert-type-badge <?php echo strtolower(str_replace(' ', '-', $alert['alert_type'])); ?>">
                                <?php 
                                    $icon = match($alert['alert_type']) {
                                        'Emergency SOS' => 'ri-alarm-warning-line',
                                        'Shake Alert' => 'ri-smartphone-line',
                                        'Medi-Alert' => 'ri-heart-pulse-line',
                                        'Panic Button' => 'ri-error-warning-line',
                                        default => 'ri-alert-line'
                                    };
                                ?>
                                <i class="<?php echo $icon; ?>"></i>
                                <?php echo $alert['alert_type']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="priority-badge <?php echo $alert['priority']; ?>">
                                <?php echo strtoupper($alert['priority']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="location-info">
                                <i class="ri-map-pin-line"></i>
                                <?php echo $alert['location']; ?>
                            </div>
                        </td>
                        <td>
                            <div class="time-info">
                                <i class="ri-time-line"></i>
                                <?php echo $alert['time']; ?>
                            </div>
                        </td>
                        <td><?php echo $alert['response_time'] ?? '-'; ?></td>
                        <td>
                            <span class="status-badge <?php echo $alert['status']; ?>">
                                <?php echo strtoupper($alert['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon view" onclick="viewAlert(<?php echo $alert['id']; ?>)" title="View">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button class="btn-icon location" onclick="viewLocation(<?php echo $alert['id']; ?>)" title="Location">
                                    <i class="ri-map-pin-line"></i>
                                </button>
                                <button class="btn-icon check" onclick="resolveAlert(<?php echo $alert['id']; ?>)" title="Resolve">
                                    <i class="ri-check-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <div class="pagination-info">
            Showing <strong><?php echo $rangeStart; ?>–<?php echo $rangeEnd; ?></strong> of <strong><?php echo number_format($totalCount); ?></strong> emergency alerts
        </div>
        <div class="pagination-controls">

            <?php if ($currentPage > 1): ?>
                <a href="<?php echo BASE_URL; ?>index.php?action=admin-emergency-alerts&page=<?php echo $currentPage - 1; ?>" class="page-btn">Previous</a>
            <?php else: ?>
                <button class="page-btn" disabled>Previous</button>
            <?php endif; ?>

            <?php
                $startPage = max(1, $currentPage - 2);
                $endPage   = min($totalPages, $startPage + 4);
                $startPage = max(1, $endPage - 4);

                if ($startPage > 1): ?>
                    <a href="<?php echo BASE_URL; ?>index.php?action=admin-emergency-alerts&page=1" class="page-number">1</a>
                    <?php if ($startPage > 2): ?><span class="page-ellipsis">...</span><?php endif; ?>
                <?php endif;

                for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <?php if ($i === $currentPage): ?>
                        <span class="page-number active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>index.php?action=admin-emergency-alerts&page=<?php echo $i; ?>" class="page-number"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor;

                if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?><span class="page-ellipsis">...</span><?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>index.php?action=admin-emergency-alerts&page=<?php echo $totalPages; ?>" class="page-number"><?php echo $totalPages; ?></a>
                <?php endif; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="<?php echo BASE_URL; ?>index.php?action=admin-emergency-alerts&page=<?php echo $currentPage + 1; ?>" class="page-btn">Next</a>
            <?php else: ?>
                <button class="page-btn" disabled>Next</button>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
require_once VIEW_PATH . 'includes/dashboard-footer.php';
?>

<script>window.BASE_URL = '<?php echo BASE_URL; ?>';</script>
<script src="<?php echo BASE_URL; ?>assets/js/admin-emergency-alerts.js"></script>