<?php
// views/communication-hub.php
// Communication Hub - Data is passed from UserController

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

    <!-- Emergency Contacts Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-icon red">
                <i class="ri-contacts-line"></i>
            </div>
            <h2>Emergency Contacts</h2>
            <?php if (empty($emergencyContacts)): ?>
            <a href="<?php echo BASE_URL; ?>index.php?action=medical-profile" class="contacts-add-btn">
                <i class="ri-add-line"></i> Add Contacts
            </a>
            <?php endif; ?>
        </div>
        <?php if (empty($emergencyContacts)): ?>
        <div class="no-contacts-notice">
            <i class="ri-alert-line"></i>
            No emergency contacts added yet. 
            <a href="<?php echo BASE_URL; ?>index.php?action=medical-profile">Add contacts in your Medical Profile</a> so your SOS messages reach the right people.
        </div>
        <?php else: ?>
        <div class="contacts-list" id="contactsList">
            <?php foreach ($emergencyContacts as $contact): ?>
            <div class="contact-chip">
                <div class="contact-avatar" style="background-color: <?php echo htmlspecialchars($contact['color']); ?>">
                    <?php echo htmlspecialchars($contact['initials']); ?>
                </div>
                <div class="contact-info">
                    <div class="contact-name"><?php echo htmlspecialchars($contact['name'] ?? ''); ?></div>
                    <div class="contact-phone"><?php echo htmlspecialchars($contact['phone'] ?? ''); ?></div>
                </div>
                <div class="contact-rel"><?php echo htmlspecialchars($contact['relationship'] ?? ''); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
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
        <div class="sms-user-info" id="smsUserInfo">
            <i class="ri-user-line"></i>
            <span>From: <strong><?php echo htmlspecialchars($userInfo['name']); ?></strong>
            <?php if ($userInfo['pwdId']): ?> (PWD ID: <?php echo htmlspecialchars($userInfo['pwdId']); ?>)<?php endif; ?>
            </span>
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
        <?php if (empty($emergencyContacts)): ?>
        <p class="sms-warning"><i class="ri-alert-line"></i> No emergency contacts set. <a href="<?php echo BASE_URL; ?>index.php?action=medical-profile">Add contacts</a> first.</p>
        <?php endif; ?>
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
        <input type="file" id="hubCameraPhoto" accept="image/*" capture="environment" style="display:none" onchange="handleHubCapture(this, 'photo')">
        <input type="file" id="hubCameraVideo" accept="video/*" capture="environment" style="display:none" onchange="handleHubCapture(this, 'video')">
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

<!-- Pass PHP data to JavaScript -->
<script>
const BASE_URL = <?php echo json_encode(BASE_URL); ?>;
const categoriesData = <?php echo json_encode($categories); ?>;
const messagesData = <?php echo json_encode($messages); ?>;
const fslItemsData = <?php echo json_encode($fslItems); ?>;
const emergencyContactsData = <?php echo json_encode($emergencyContacts); ?>;
const userInfoData = <?php echo json_encode($userInfo); ?>;
</script>

<!-- Page-specific JavaScript -->
<script src="<?php echo BASE_URL; ?>assets/js/communication-hub.js"></script>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>
