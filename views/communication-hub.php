<?php
// views/dashboard.php
// User Dashboard - Data is passed from UserController

require_once VIEW_PATH . 'includes/dashboard-header.php';
?>


<!-- Page-specific styles -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/communication-hub.css">

<!-- Page Container -->
<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-icon">
            <i class="ri-message-2-line"></i>
        </div>
        <div class="page-header-content">
            <h1>Communication Hub</h1>
            <p>Select pre-written messages to send to your emergency contacts via SMS with GPS location.</p>
        </div>
        <div class="page-header-meta">
            <div class="sel-label">Selected</div>
            <div class="sel-count" id="selCount">0</div>
        </div>
    </div>

    <!-- Categories Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon purple">
                <i class="ri-layout-grid-line"></i>
            </div>
            <h2>Filter by Category</h2>
        </div>
        <div class="cat-grid" id="catGrid"></div>
    </div>

    <!-- Messages Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon blue">
                <i class="ri-chat-3-line"></i>
            </div>
            <h2>Quick Messages</h2>
        </div>
        <p class="card-sub">Tap any message to select/deselect. Selected messages will be sent together.</p>
        <div class="msg-grid" id="msgGrid"></div>
    </div>

    <!-- SMS Preview Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon green">
                <i class="ri-message-3-line"></i>
            </div>
            <h2>SMS Preview</h2>
        </div>
        <div class="sms-preview-box empty" id="smsPreviewBox">
            <div class="sms-placeholder" id="smsPlaceholder">
                <i class="ri-chat-off-line"></i>
                <div>No messages selected yet</div>
            </div>
            <div class="sms-content" id="smsContent"></div>
        </div>
        <div class="action-row">
            <button class="action-btn primary" id="btnSend" disabled onclick="sendSMS()">
                <i class="ri-send-plane-fill"></i> Send SMS
            </button>
            <button class="action-btn secondary" id="btnClear" disabled onclick="clearAll()">
                <i class="ri-delete-bin-line"></i> Clear All
            </button>
        </div>
    </div>

    <!-- Camera Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon orange">
                <i class="ri-camera-line"></i>
            </div>
            <h2>Visual Evidence</h2>
        </div>
        <p class="card-sub">Capture photos or videos with GPS tagging for documentation.</p>
        <div class="cam-row">
            <button class="cam-btn photo" onclick="camAction('photo', this)">
                <i class="ri-camera-fill"></i>
                <span>Take Photo</span>
            </button>
            <button class="cam-btn video" onclick="camAction('video', this)">
                <i class="ri-video-fill"></i>
                <span>Record Video</span>
            </button>
        </div>
    </div>

    <!-- FSL Resources Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon purple">
                <i class="ri-file-text-line"></i>
            </div>
            <h2>FSL Emergency Resources</h2>
        </div>
        <p class="card-sub">Download Filipino Sign Language guides and instructions for offline use.</p>
        <div id="fslList"></div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast" id="toast"></div>

<!-- Page-specific JavaScript -->
<script src="<?php echo BASE_URL; ?>assets/js/communication-hub.js"></script>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>