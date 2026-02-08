<?php
// views/family-dashboard.php
// Family Member Dashboard - Shows PWD status and family member responses

require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/family-dashboard.css">

<div class="family-dashboard-container">

<!-- Welcome Header -->
<div class="welcome-header">
<div class="welcome-content">
<h1 class="welcome-title">Welcome, <?php echo $_SESSION['user_fname']; ?></h1>
<p class="welcome-subtitle">Family Member Dashboard</p>
</div>
<div class="header-actions">
<button class="quick-action-btn emergency-btn" onclick="handleEmergencyCall()">
<i class="ri-phone-line"></i>
<span>Emergency Call</span>
</button>
</div>
</div>

<!-- Quick Stats -->
<div class="quick-stats-grid">
<?php foreach ($quickStats as $stat): ?>
<div class="stat-card">
<div class="stat-icon" style="background: <?php echo $stat['color']; ?>15; color: <?php echo $stat['color']; ?>;">
<i class="<?php echo $stat['icon']; ?>"></i>
</div>
<div class="stat-content">
<div class="stat-value"><?php echo $stat['value']; ?></div>
<div class="stat-label"><?php echo $stat['label']; ?></div>
</div>
</div>
<?php endforeach; ?>
</div>

<!-- PWD Members Section -->
<div class="section-container">
<div class="section-header">
<div class="section-title">
<i class="ri-user-heart-line"></i>
<h2>PWD Members Under Your Care</h2>
</div>
</div>

<div class="pwd-members-grid">
<?php foreach ($pwdMembers as $pwd): ?>
<div class="pwd-member-card">
<!-- Status Banner -->
<div class="pwd-status-banner status-<?php echo $pwd['status']; ?>">
<div class="pwd-photo">
<?php echo $pwd['photo']; ?>
</div>
<div class="pwd-status-info">
<h3 class="pwd-name"><?php echo $pwd['name']; ?></h3>
<span class="pwd-relationship"><?php echo $pwd['relationship']; ?></span>
<div class="status-badge">
<i class="ri-checkbox-circle-line"></i>
<span><?php echo $pwd['statusLabel']; ?></span>
</div>
<span class="status-time">Updated: <?php echo $pwd['lastUpdated']; ?></span>
</div>
<div class="pwd-status-icon">
<i class="ri-shield-check-line"></i>
</div>
</div>

<!-- PWD Details -->
<div class="pwd-details-grid">
<div class="detail-item">
<i class="ri-map-pin-line"></i>
<div class="detail-content">
<span class="detail-label">Location</span>
<span class="detail-value"><?php echo $pwd['location']; ?></span>
</div>
</div>
<div class="detail-item">
<i class="ri-battery-2-charge-line"></i>
<div class="detail-content">
<span class="detail-label">Battery</span>
<span class="detail-value"><?php echo $pwd['battery']; ?>%</span>
</div>
</div>
<div class="detail-item">
<i class="ri-heart-pulse-line"></i>
<div class="detail-content">
<span class="detail-label">Disability</span>
<span class="detail-value"><?php echo $pwd['disability']; ?></span>
</div>
</div>
<div class="detail-item">
<i class="ri-calendar-line"></i>
<div class="detail-content">
<span class="detail-label">Age</span>
<span class="detail-value"><?php echo $pwd['age']; ?> years</span>
</div>
</div>
</div>

<!-- Quick Actions -->
<div class="pwd-quick-actions">
<button class="pwd-action-btn" onclick="viewLocation(<?php echo $pwd['latitude']; ?>, <?php echo $pwd['longitude']; ?>)">
<i class="ri-map-pin-2-line"></i>
<span>View Location</span>
</button>
<button class="pwd-action-btn" onclick="sendMessage(<?php echo $pwd['id']; ?>)">
<i class="ri-message-2-line"></i>
<span>Send Message</span>
</button>
<button class="pwd-action-btn" onclick="viewProfile(<?php echo $pwd['id']; ?>)">
<i class="ri-file-user-line"></i>
<span>View Profile</span>
</button>
</div>
</div>
<?php endforeach; ?>
</div>
</div>

<!-- Family Members Responsible Section -->
<div class="section-container">
<div class="section-header">
<div class="section-title">
<i class="ri-team-line"></i>
<h2>Family Members & Response Status</h2>
</div>
<span class="section-badge">3 Members</span>
</div>

<div class="family-members-table">
<table class="responsive-table">
<thead>
<tr>
<th>Family Member</th>
<th>Relationship</th>
<th>Contact</th>
<th>Response Status</th>
<th>Last Seen</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($otherFamilyMembers as $member): ?>
<tr class="member-row">
<td data-label="Family Member">
<div class="member-info">
<div class="member-avatar" style="background: <?php echo $member['color']; ?>;">
<?php echo $member['initials']; ?>
</div>
<div class="member-details">
<span class="member-name"><?php echo $member['name']; ?></span>
</div>
</div>
</td>
<td data-label="Relationship">
<span class="relationship-badge"><?php echo $member['relationship']; ?></span>
</td>
<td data-label="Contact">
<div class="contact-info">
<i class="ri-phone-line"></i>
<span><?php echo $member['phone']; ?></span>
</div>
</td>
<td data-label="Response Status">
<span class="response-status status-<?php echo $member['status']; ?>">
<?php if ($member['status'] === 'responded'): ?>
<i class="ri-checkbox-circle-fill"></i>
<span>Responded</span>
<?php else: ?>
<i class="ri-time-line"></i>
<span>Pending</span>
<?php endif; ?>
</span>
<span class="response-time"><?php echo $member['responseTime']; ?></span>
</td>
<td data-label="Last Seen">
<span class="last-seen"><?php echo $member['lastSeen']; ?></span>
</td>
<td data-label="Actions">
<div class="table-actions">
<button class="table-action-btn" onclick="callMember('<?php echo $member['phone']; ?>')" title="Call">
<i class="ri-phone-line"></i>
</button>
<button class="table-action-btn" onclick="messageMember(<?php echo $member['id']; ?>)" title="Message">
<i class="ri-message-2-line"></i>
</button>
</div>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>

<!-- Recent Emergency Alerts -->
<div class="section-container">
<div class="section-header">
<div class="section-title">
<i class="ri-history-line"></i>
<h2>Recent Emergency Alerts</h2>
</div>
</div>

<div class="alerts-timeline">
<?php foreach ($recentAlerts as $alert): ?>
<div class="alert-timeline-item">
<div class="alert-timeline-icon" style="background: <?php echo $alert['iconBg']; ?>; color: <?php echo $alert['iconColor']; ?>;">
<i class="<?php echo $alert['icon']; ?>"></i>
</div>
<div class="alert-timeline-content">
<div class="alert-timeline-header">
<h4><?php echo $alert['title']; ?></h4>
<span class="alert-status <?php echo $alert['statusClass']; ?>">
<?php echo $alert['statusLabel']; ?>
</span>
</div>
<p class="alert-description"><?php echo $alert['description']; ?></p>
<div class="alert-meta">
<span class="alert-location">
<i class="ri-map-pin-line"></i>
<?php echo $alert['location']; ?>
</span>
<span class="alert-time">
<i class="ri-time-line"></i>
<?php echo $alert['time']; ?>
</span>
<?php if (isset($alert['respondedBy'])): ?>
<span class="alert-responder">
<i class="ri-user-line"></i>
Responded by <?php echo $alert['respondedBy']; ?>
</span>
<?php endif; ?>
</div>
</div>
</div>
<?php endforeach; ?>
</div>
</div>

<!-- Emergency Quick Actions FAB -->
<div class="emergency-fab">
<button class="fab-button" onclick="triggerEmergencyActions()">
<i class="ri-alarm-warning-line"></i>
</button>
<div class="fab-menu">
<button class="fab-menu-item" onclick="callEmergencyServices()">
<i class="ri-phone-line"></i>
<span>Call 911</span>
</button>
<button class="fab-menu-item" onclick="alertAllFamily()">
<i class="ri-notification-3-line"></i>
<span>Alert All Family</span>
</button>
<button class="fab-menu-item" onclick="viewEmergencyContacts()">
<i class="ri-contacts-line"></i>
<span>Emergency Contacts</span>
</button>
</div>
</div>

</div>
<script src="<?php echo BASE_URL; ?>assets/js/family-dashboard.js"></script>


<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>
</body>
</html>