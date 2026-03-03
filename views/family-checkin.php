<?php
// views/family-checkin.php
// Family Check-in - Data is passed from UserController

require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<!-- Page-specific styles -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/family-checkin.css">

<!-- Page Container -->
<div class="page-container">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="page-header-icon">
            <i class="ri-group-line"></i>
        </div>
        <div class="page-header-content">
            <h1>Family Check-in</h1>
            <p>Update your status and view family members' safety</p>
        </div>
        <div class="page-header-meta">
            <div class="family-label"><?php echo htmlspecialchars($familyGroupName); ?></div>
            <div class="family-sub"><?php echo $familyMemberCount; ?> member<?php echo $familyMemberCount !== 1 ? 's' : ''; ?></div>
        </div>
    </div>

    <!-- YOUR STATUS -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon green"><i class="ri-user-heart-line"></i></div>
            <h2>Your Status</h2>
        </div>
        <?php $myCurrentStatus = $myStatus['status'] ?? 'unknown'; ?>
        <div class="status-grid">
            <div class="status-btn safe<?php echo $myCurrentStatus === 'safe' ? ' chosen' : ''; ?>" onclick="setStatus('safe', this)">
                <div class="status-icon-wrap"><i class="ri-checkbox-circle-fill"></i></div>
                <div class="status-label">I'm Safe</div>
                <div class="status-desc">Let family know you're okay</div>
            </div>
            <div class="status-btn help<?php echo ($myCurrentStatus === 'needs_assistance' || $myCurrentStatus === 'danger') ? ' chosen' : ''; ?>" onclick="setStatus('needs_assistance', this)">
                <div class="status-icon-wrap"><i class="ri-alert-fill"></i></div>
                <div class="status-label">I Need Help</div>
                <div class="status-desc">Send alert to family</div>
            </div>
        </div>
        <div class="status-current-info" id="statusCurrentInfo">
            <?php if ($myStatus): ?>
            <span class="status-last-updated">
                <i class="ri-time-line"></i> Last updated:
                <?php
                $diff = time() - strtotime($myStatus['created_at']);
                if ($diff < 60) echo 'Just now';
                elseif ($diff < 3600) echo round($diff/60) . ' min ago';
                elseif ($diff < 86400) echo round($diff/3600) . ' hr ago';
                else echo round($diff/86400) . ' day ago';
                ?>
            </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- CURRENT LOCATION -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon blue"><i class="ri-map-pin-line"></i></div>
            <h2>Current Location</h2>
        </div>
        <div class="gps-location">
            <div class="gps-location-label">&#128205; CURRENT GPS LOCATION</div>
            <div class="gps-location-main">
                <div class="gps-location-info">
                    <div class="gps-location-address" id="gpsAddressText">
                        <?php if ($myStatus && $myStatus['latitude']): ?>
                            Lat: <?php echo number_format((float)$myStatus['latitude'], 6); ?>, Lng: <?php echo number_format((float)$myStatus['longitude'], 6); ?>
                        <?php else: ?>
                            Fetching GPS location...
                        <?php endif; ?>
                    </div>
                    <div class="gps-location-meta">
                        <i class="ri-time-line"></i>
                        <span id="gpsUpdateTime">
                        <?php if ($myStatus && $myStatus['latitude']): ?>
                            Last updated
                            <?php
                            $diff = time() - strtotime($myStatus['created_at']);
                            if ($diff < 60) echo 'just now';
                            elseif ($diff < 3600) echo round($diff/60) . ' min ago';
                            elseif ($diff < 86400) echo round($diff/3600) . ' hr ago';
                            else echo round($diff/86400) . ' day ago';
                            ?>
                        <?php else: ?>
                            Locating...
                        <?php endif; ?>
                        </span>
                    </div>
                </div>
                <button class="gps-pin-btn" onclick="gpsTap(event, this)" title="Open in Maps">
                    <i class="ri-map-pin-fill"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- PHOTO / VIDEO EVIDENCE -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon purple"><i class="ri-camera-line"></i></div>
            <h2>Photo/Video Evidence</h2>
        </div>
        <p class="card-sub">Capture and send GPS-tagged visual evidence to your family.</p>
        <div class="media-grid">
            <div class="media-btn" onclick="mediaTap(this, 'photo')">
                <div class="media-icon"><i class="ri-camera-line"></i></div>
                <div class="media-label">Take Photo</div>
            </div>
            <div class="media-btn" onclick="mediaTap(this, 'video')">
                <div class="media-icon"><i class="ri-vidicon-line"></i></div>
                <div class="media-label">Record Video</div>
            </div>
        </div>
        <input type="file" id="cameraInputPhoto" accept="image/*" capture="environment" style="display:none" onchange="handleMediaCapture(this, 'photo')">
        <input type="file" id="cameraInputVideo" accept="video/*" capture="environment" style="display:none" onchange="handleMediaCapture(this, 'video')">
    </div>

    <!-- FAMILY SAFETY STATUS -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon orange"><i class="ri-group-line"></i></div>
            <h2>Family Safety Status</h2>
            <button class="refresh-icon-btn" onclick="refreshFamilyStatus()" title="Refresh">
                <i class="ri-refresh-line"></i>
            </button>
        </div>

        <?php if (empty($familyStatuses)): ?>
        <div class="empty-family">
            <i class="ri-group-line"></i>
            <p>No emergency contacts added yet.</p>
            <small>Add contacts in your Medical Profile — they will automatically appear here with their live status if they're registered on Silent Signal.</small>
            <a href="<?php echo BASE_URL; ?>index.php?action=medical-profile#emergency-contacts" class="link-btn">
                Add Emergency Contacts &#8594;
            </a>
        </div>
        <?php else: ?>
        <div class="family-list" id="familyList">
            <?php foreach ($familyStatuses as $member): ?>
            <div class="family-item" data-member-id="<?php echo (int)$member['id']; ?>">
                <div class="family-avatar-wrap" style="background-color: <?php echo htmlspecialchars($member['color']); ?>">
                    <?php echo htmlspecialchars($member['initials']); ?>
                    <?php if (!$member['is_registered']): ?>
                    <span class="avatar-unregistered" title="Not on Silent Signal">?</span>
                    <?php endif; ?>
                </div>
                <div class="family-info">
                    <div class="name">
                        <?php echo htmlspecialchars($member['display_name']); ?>
                        <?php if (!$member['is_registered']): ?>
                        <span class="unregistered-tag">Not registered</span>
                        <?php endif; ?>
                    </div>
                    <div class="loc">
                        <i class="ri-account-circle-line"></i>
                        <?php echo htmlspecialchars($member['relationship_type'] ?? 'Emergency Contact'); ?>
                        <?php if (!empty($member['phone_number'])): ?>
                        &nbsp;&bull;&nbsp;<i class="ri-phone-line"></i> <?php echo htmlspecialchars($member['phone_number']); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="family-status-right">
                    <div class="family-status-badge <?php echo htmlspecialchars($member['status_class']); ?>">
                        <?php echo htmlspecialchars($member['status_label']); ?>
                    </div>
                    <div class="family-time"><i class="ri-time-line"></i> <?php echo htmlspecialchars($member['time_ago']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- LOCATION HISTORY -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon blue"><i class="ri-history-line"></i></div>
            <h2>Location History</h2>
        </div>
        <button class="breadcrumb-btn" onclick="breadcrumbTap(event, this)">
            <i class="ri-route-line"></i> View Location Breadcrumbs
        </button>
        <p class="breadcrumb-sub">Track your movement history (last 20 check-ins)</p>
        <div class="breadcrumb-list" id="breadcrumbList" style="display:none"></div>
    </div>

</div><!-- /page-container -->

<!-- Toast Notification -->
<div id="toast"></div>

<!-- Pass PHP data to JavaScript -->
<script>
const BASE_URL = <?php echo json_encode(BASE_URL); ?>;
const currentUserId = <?php echo json_encode($_SESSION['user_id']); ?>;
const initialMyStatus = <?php echo json_encode($myStatus['status'] ?? null); ?>;
const initialFamilyStatuses = <?php echo json_encode($familyStatuses); ?>;
const initialStatusHistory = <?php echo json_encode($statusHistory); ?>;
let currentLat = <?php echo json_encode($myStatus['latitude'] ?? null); ?>;
let currentLng = <?php echo json_encode($myStatus['longitude'] ?? null); ?>;
</script>

<!-- Page-specific JavaScript -->
<script src="<?php echo BASE_URL; ?>assets/js/family-checkin.js"></script>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>
