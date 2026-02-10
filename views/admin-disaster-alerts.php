<?php
require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin-disaster-alerts.css">

<div class="dashboard-container">

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1>Disaster Monitoring & Alerts</h1>
            <p>Real-time monitoring and management of natural disaster alerts</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="ri-stack-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Active Disasters</div>
                <div class="stat-value"><?php echo number_format($stats['active_disasters']); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="ri-windy-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Typhoons/ Storms</div>
                <div class="stat-value"><?php echo number_format($stats['typhoons']); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="ri-earthquake-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Earthquakes</div>
                <div class="stat-value"><?php echo number_format($stats['earthquakes']); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon blue-light">
                <i class="ri-flood-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Flood/ Warnings</div>
                <div class="stat-value"><?php echo number_format($stats['floods']); ?></div>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    <div class="section-header">
        <h2><i class="ri-alarm-warning-line"></i> Recent Disaster Alerts</h2>
        <div class="section-controls">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" id="searchInput" placeholder="Search by type or location...">
            </div>
            <select class="filter-select" id="severityFilter">
                <option value="">All Severity</option>
                <option value="extreme">Extreme</option>
                <option value="severe">Severe</option>
                <option value="moderate">Moderate</option>
                <option value="minor">Minor</option>
            </select>
            <select class="filter-select" id="typeFilter">
                <option value="">All Types</option>
                <option value="typhoon">Typhoon</option>
                <option value="earthquake">Earthquake</option>
                <option value="flood">Flood</option>
                <option value="fire">Fire</option>
            </select>
            <button class="btn-primary">
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
                    <th>DISASTER TYPE</th>
                    <th>SEVERITY</th>
                    <th>LOCATION/REGION</th>
                    <th>AFFECTED USERS</th>
                    <th>WIND SPEED/MAGNITUDE</th>
                    <th>ISSUED TIME</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alerts as $alert): ?>
                    <tr>
                        <td class="alert-id"><?php echo $alert['alert_id']; ?></td>
                        <td>
                            <span class="disaster-badge <?php echo strtolower($alert['disaster_type']); ?>">
                                <?php 
                                    $icon = match($alert['disaster_type']) {
                                        'Typhoon' => 'ri-windy-line',
                                        'Earthquake' => 'ri-earthquake-line',
                                        'Flood' => 'ri-flood-line',
                                        'Fire Alert' => 'ri-fire-line',
                                        'Tropical Storm' => 'ri-cloud-windy-line',
                                        default => 'ri-alert-line'
                                    };
                                ?>
                                <i class="<?php echo $icon; ?>"></i>
                                <?php echo $alert['disaster_type']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="severity-badge <?php echo $alert['severity']; ?>">
                                <?php echo strtoupper($alert['severity']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="location-info">
                                <i class="ri-map-pin-line"></i>
                                <?php echo $alert['location']; ?>
                            </div>
                        </td>
                        <td>
                            <div class="affected-users">
                                <i class="ri-group-line"></i>
                                <?php echo $alert['affected_users']; ?>
                            </div>
                        </td>
                        <td><?php echo $alert['magnitude']; ?></td>
                        <td>
                            <div class="time-info">
                                <i class="ri-time-line"></i>
                                <?php echo $alert['issued_time']; ?>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $alert['status']; ?>">
                                <?php echo strtoupper($alert['status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <div class="pagination-info">
            Showing 1-10 of 45 disaster alerts
        </div>
        <div class="pagination-controls">
            <button class="page-btn" disabled>Previous</button>
            <button class="page-number active">1</button>
            <button class="page-number">2</button>
            <button class="page-number">3</button>
            <span>...</span>
            <button class="page-number">5</button>
            <button class="page-btn">Next</button>
        </div>
    </div>
</div>

<?php
require_once VIEW_PATH . 'includes/dashboard-footer.php';
?>

<script src="<?php echo BASE_URL; ?>assets/js/admin-disaster-alerts.js"></script>