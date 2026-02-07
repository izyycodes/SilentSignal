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
            <div class="family-label"><?php echo $familyInfo['name']; ?></div>
            <div class="family-sub"><?php echo $familyInfo['memberCount']; ?> members</div>
        </div>
    </div>

    <!-- YOUR STATUS -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon green"><i class="ri-user-heart-line"></i></div>
            <h2>Your Status</h2>
        </div>
        <div class="status-grid">
            <div class="status-btn safe" onclick="setStatus('safe', this)">
                <div class="status-icon-wrap">
                    <i class="ri-checkbox-circle-fill"></i>
                </div>
                <div class="status-label">I'm Safe</div>
                <div class="status-desc">Let family know you're okay</div>
            </div>
            <div class="status-btn help" onclick="setStatus('help', this)">
                <div class="status-icon-wrap">
                    <i class="ri-alert-fill"></i>
                </div>
                <div class="status-label">I Need Help</div>
                <div class="status-desc">Send alert to family</div>
            </div>
        </div>
    </div>

    <!-- CURRENT LOCATION -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon blue"><i class="ri-map-pin-line"></i></div>
            <h2>Current Location</h2>
        </div>
        <div class="gps-location">
            <div class="gps-location-label">📍 CURRENT GPS LOCATION</div>
            <div class="gps-location-main">
                <div class="gps-location-info">
                    <div class="gps-location-address"><?php echo $currentLocation['address']; ?></div>
                    <div class="gps-location-meta">
                        <i class="ri-time-line"></i>
                        <span>Last updated <?php echo $currentLocation['lastUpdated']; ?></span>
                    </div>
                </div>
                <button class="gps-pin-btn" onclick="gpsTap(event, this)">
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
        <div class="media-grid">
            <div class="media-btn" onclick="mediaTap(this, '📸 Photo captured & tagged with GPS!')">
                <div class="media-icon">
                    <i class="ri-camera-line"></i>
                </div>
                <div class="media-label">Take Photo</div>
            </div>
            <div class="media-btn" onclick="mediaTap(this, '🎥 Video recording started!')">
                <div class="media-icon">
                    <i class="ri-vidicon-line"></i>
                </div>
                <div class="media-label">Record Video</div>
            </div>
        </div>
    </div>

    <!-- FAMILY SAFETY STATUS -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon orange"><i class="ri-group-line"></i></div>
            <h2>Family Safety Status</h2>
        </div>
        <div class="family-list">
            <?php foreach ($familyMembers as $member): ?>
                <div class="family-item">
                    <div class="family-avatar-wrap <?php echo $member['avatarClass']; ?>"><?php echo $member['initials']; ?></div>
                    <div class="family-info">
                        <div class="name"><?php echo $member['name']; ?></div>
                        <div class="loc"><i class="ri-map-pin-line"></i> <?php echo $member['location']; ?></div>
                    </div>
                    <div class="family-status-right">
                        <div class="family-status-badge <?php echo $member['status']; ?>"><?php echo $member['statusLabel']; ?></div>
                        <div class="family-time"><i class="ri-time-line"></i> <?php echo $member['lastUpdated']; ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- LOCATION HISTORY -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon blue"><i class="ri-history-line"></i></div>
            <h2>Location History</h2>
        </div>
        <button class="breadcrumb-btn" onclick="breadcrumbTap(event,this)">View Location Breadcrumbs</button>
        <p class="breadcrumb-sub">Track family members' movement history</p>
    </div>

</div><!-- /page-container -->

<!-- Toast Notification -->
<div id="toast"></div>

<!-- Page-specific JavaScript -->
<script src="<?php echo BASE_URL; ?>assets/js/family-checkin.js"></script>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>