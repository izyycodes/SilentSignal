<?php
// views/family-dashboard.php
// Family Member Dashboard – all data from FamilyController

require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/family-dashboard.css">

<div class="family-dashboard-container">

    <!-- Welcome Header -->
    <div class="welcome-header">
        <div class="welcome-content">
            <h1 class="welcome-title">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
            <p class="welcome-subtitle">Family Member Dashboard</p>
        </div>
        <div class="header-actions">
            <button class="quick-action-btn refresh-btn" onclick="refreshDashboard(this)" title="Refresh data">
                <i class="ri-refresh-line"></i>
                <span>Refresh</span>
            </button>
            <button class="quick-action-btn emergency-btn" onclick="handleEmergencyCall()">
                <i class="ri-phone-line"></i>
                <span>Emergency Call</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats-grid">
        <?php foreach ($quickStats as $stat): ?>
        <div class="stat-card" id="stat-<?php echo htmlspecialchars(strtolower(str_replace(' ','-',$stat['label']))); ?>">
            <div class="stat-icon" style="background:<?php echo $stat['color']; ?>15; color:<?php echo $stat['color']; ?>;">
                <i class="<?php echo $stat['icon']; ?>"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo htmlspecialchars($stat['value']); ?></div>
                <div class="stat-label"><?php echo htmlspecialchars($stat['label']); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- PWD Members Section -->
    <div class="section-container" id="pwdMembers">
        <div class="section-header">
            <div class="section-title">
                <i class="ri-user-heart-line"></i>
                <h2>PWD Members Under Your Care</h2>
            </div>
            <span class="section-badge"><?php echo count($pwdMembers); ?> Member<?php echo count($pwdMembers) !== 1 ? 's' : ''; ?></span>
        </div>

        <?php if (empty($pwdMembers)): ?>
        <div class="empty-state">
            <i class="ri-user-heart-line"></i>
            <p>No PWD members linked to your account yet.</p>
            <small>Ask an administrator to link you to a PWD member.</small>
        </div>
        <?php else: ?>
        <div class="pwd-members-grid">
            <?php foreach ($pwdMembers as $pwd): ?>
            <div class="pwd-member-card" id="pwd-card-<?php echo $pwd['id']; ?>">
                <!-- Status Banner -->
                <div class="pwd-status-banner status-<?php echo htmlspecialchars($pwd['status']); ?>">
                    <div class="pwd-photo" style="background:<?php echo htmlspecialchars($pwd['color']); ?>;">
                        <?php echo htmlspecialchars($pwd['photo']); ?>
                    </div>
                    <div class="pwd-status-info">
                        <h3 class="pwd-name"><?php echo htmlspecialchars($pwd['name']); ?></h3>
                        <span class="pwd-relationship"><?php echo htmlspecialchars($pwd['relationship']); ?></span>
                        <div class="status-badge">
                            <i class="ri-checkbox-circle-line"></i>
                            <span class="pwd-status-label"><?php echo htmlspecialchars($pwd['statusLabel']); ?></span>
                        </div>
                        <span class="status-time">Updated: <span class="pwd-last-updated"><?php echo htmlspecialchars($pwd['lastUpdated']); ?></span></span>
                    </div>
                    <div class="pwd-status-icon">
                        <i class="<?php echo $pwd['status'] === 'safe' ? 'ri-shield-check-line' : 'ri-alarm-warning-line'; ?>"></i>
                    </div>
                </div>

                <!-- PWD Details -->
                <div class="pwd-details-grid">
                    <div class="detail-item">
                        <i class="ri-map-pin-line"></i>
                        <div class="detail-content">
                            <span class="detail-label">Location</span>
                            <span class="detail-value pwd-location"><?php echo htmlspecialchars($pwd['location']); ?></span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="ri-battery-2-charge-line"></i>
                        <div class="detail-content">
                            <span class="detail-label">Battery</span>
                            <span class="detail-value"><?php echo htmlspecialchars((string)$pwd['battery']); ?>%</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="ri-heart-pulse-line"></i>
                        <div class="detail-content">
                            <span class="detail-label">Disability</span>
                            <span class="detail-value"><?php echo htmlspecialchars($pwd['disability']); ?></span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="ri-calendar-line"></i>
                        <div class="detail-content">
                            <span class="detail-label">Age</span>
                            <span class="detail-value"><?php echo htmlspecialchars((string)$pwd['age']); ?> <?php echo is_numeric($pwd['age']) ? 'years' : ''; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="pwd-quick-actions">
                    <button class="pwd-action-btn" onclick="viewLocation(<?php echo (float)$pwd['latitude']; ?>, <?php echo (float)$pwd['longitude']; ?>, '<?php echo htmlspecialchars(addslashes($pwd['name'])); ?>')">
                        <i class="ri-map-pin-2-line"></i>
                        <span>View Location</span>
                    </button>
                    <button class="pwd-action-btn" onclick="sendMessage(<?php echo (int)$pwd['id']; ?>, '<?php echo htmlspecialchars(addslashes($pwd['name'])); ?>')">
                        <i class="ri-message-2-line"></i>
                        <span>Send Message</span>
                    </button>
                    <button class="pwd-action-btn" onclick="viewProfile(<?php echo (int)$pwd['id']; ?>, '<?php echo htmlspecialchars(addslashes($pwd['name'])); ?>')">
                        <i class="ri-file-user-line"></i>
                        <span>View Profile</span>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Family Members & Response Status -->
    <div class="section-container" id="responseStatus">
        <div class="section-header">
            <div class="section-title">
                <i class="ri-team-line"></i>
                <h2>Family Members &amp; Response Status</h2>
            </div>
            <span class="section-badge"><?php echo count($otherFamilyMembers); ?> Member<?php echo count($otherFamilyMembers) !== 1 ? 's' : ''; ?></span>
        </div>

        <?php if (empty($otherFamilyMembers)): ?>
        <div class="empty-state">
            <i class="ri-team-line"></i>
            <p>No other family members found for the same PWD members.</p>
        </div>
        <?php else: ?>
        <div class="family-members-table">
            <table class="responsive-table">
                <thead>
                    <tr>
                        <th>Family Member</th>
                        <th>Relationship to PWD</th>
                        <th>Contact</th>
                        <th>Response Status</th>
                        <th>Last Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($otherFamilyMembers as $member): ?>
                    <tr class="member-row">
                        <td data-label="Family Member">
                            <div class="member-info">
                                <div class="member-avatar" style="background:<?php echo htmlspecialchars($member['color']); ?>;">
                                    <?php echo htmlspecialchars($member['initials']); ?>
                                </div>
                                <div class="member-details">
                                    <span class="member-name"><?php echo htmlspecialchars($member['name']); ?></span>
                                    <?php if (!empty($member['pwdName'])): ?>
                                    <span class="member-pwd-tag" style="font-size:11px;color:#888;">re: <?php echo htmlspecialchars($member['pwdName']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td data-label="Relationship to PWD">
                            <span class="relationship-badge"><?php echo htmlspecialchars($member['relationship']); ?></span>
                        </td>
                        <td data-label="Contact">
                            <div class="contact-info">
                                <i class="ri-phone-line"></i>
                                <span><?php echo htmlspecialchars($member['phone']); ?></span>
                            </div>
                        </td>
                        <td data-label="Response Status">
                            <div class="response">
                                <span class="response-status status-<?php echo htmlspecialchars($member['status']); ?>">
                                    <?php if ($member['status'] === 'responded'): ?>
                                        <i class="ri-checkbox-circle-fill"></i><span><?php echo htmlspecialchars($member['responseLabel'] ?? 'Responded'); ?></span>
                                    <?php else: ?>
                                        <i class="ri-time-line"></i><span>Pending</span>
                                    <?php endif; ?>
                                </span>
                                <span class="response-time"><?php echo htmlspecialchars($member['responseTime']); ?></span>
                            </div>
                        </td>
                        <td data-label="Last Active">
                            <span class="last-seen"><?php echo htmlspecialchars($member['lastSeen']); ?></span>
                        </td>
                        <td data-label="Actions">
                            <div class="table-actions">
                                <?php if ($member['phone'] !== '—'): ?>
                                <button class="table-action-btn" onclick="callMember('<?php echo htmlspecialchars(addslashes($member['phone'])); ?>')" title="Call">
                                    <i class="ri-phone-line"></i>
                                </button>
                                <?php endif; ?>
                                <button class="table-action-btn" onclick="messageMember(<?php echo (int)$member['id']; ?>, '<?php echo htmlspecialchars(addslashes($member['name'])); ?>')" title="Message">
                                    <i class="ri-message-2-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent Emergency Alerts -->
    <div class="section-container" id="recentEmergencyAlerts">
        <div class="section-header">
            <div class="section-title">
                <i class="ri-history-line"></i>
                <h2>Recent Emergency Alerts</h2>
            </div>
        </div>

        <?php if (empty($recentAlerts)): ?>
        <div class="empty-state">
            <i class="ri-shield-check-line"></i>
            <p>No emergency alerts recorded yet.</p>
        </div>
        <?php else: ?>
        <div class="alerts-timeline">
            <?php foreach ($recentAlerts as $alert): ?>
            <div class="alert-timeline-item" id="alert-<?php echo $alert['id']; ?>">
                <div class="alert-timeline-icon" style="background:<?php echo $alert['iconBg']; ?>; color:<?php echo $alert['iconColor']; ?>;">
                    <i class="<?php echo $alert['icon']; ?>"></i>
                </div>
                <div class="alert-timeline-content">
                    <div class="alert-timeline-header">
                        <h4><?php echo htmlspecialchars($alert['title']); ?> — <?php echo htmlspecialchars($alert['pwdName']); ?></h4>
                        <span class="alert-status <?php echo htmlspecialchars($alert['statusClass']); ?>">
                            <?php echo htmlspecialchars($alert['statusLabel']); ?>
                        </span>
                    </div>
                    <p class="alert-description"><?php echo htmlspecialchars($alert['description']); ?></p>
                    <div class="alert-meta">
                        <span class="alert-location"><i class="ri-map-pin-line"></i> <?php echo htmlspecialchars($alert['location']); ?></span>
                        <span class="alert-time"><i class="ri-time-line"></i> <?php echo htmlspecialchars($alert['time']); ?></span>
                        <?php if ($alert['respondedBy']): ?>
                        <span class="alert-responder"><i class="ri-user-line"></i> Responded by <?php echo htmlspecialchars($alert['respondedBy']); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($alert['status'] === 'active'): ?>
                    <div class="alert-response-actions">
                        <button class="respond-btn on-way" onclick="respondToAlert(<?php echo $alert['id']; ?>, 'on_the_way')">
                            <i class="ri-run-line"></i> I'm On My Way
                        </button>
                        <button class="respond-btn arrived" onclick="respondToAlert(<?php echo $alert['id']; ?>, 'arrived')">
                            <i class="ri-checkbox-circle-line"></i> I've Arrived
                        </button>
                        <button class="respond-btn resolve" onclick="respondToAlert(<?php echo $alert['id']; ?>, 'resolved')">
                            <i class="ri-check-double-line"></i> Mark Resolved
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Emergency FAB -->
    <div class="emergency-fab">
        <button class="fab-button" onclick="triggerEmergencyActions()">
            <i class="ri-alarm-warning-line"></i>
        </button>
        <div class="fab-menu" style="display:none;">
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

</div><!-- /family-dashboard-container -->

<!-- PWD Profile Modal -->
<div class="modal-overlay" id="profileModal" style="display:none;" onclick="closeModal('profileModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h3><i class="ri-file-user-line"></i> PWD Profile</h3>
            <button onclick="closeModal('profileModal')" class="modal-close"><i class="ri-close-line"></i></button>
        </div>
        <div class="modal-body" id="profileModalBody">Loading...</div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<!-- Pass data to JS -->
<script>
const BASE_URL = <?php echo json_encode(BASE_URL); ?>;
const pwdMembersData = <?php echo $pwdMembersJson; ?>;
</script>

<script src="<?php echo BASE_URL; ?>assets/js/family-dashboard.js"></script>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>
