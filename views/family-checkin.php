<?php
// views/family-checkin.php
// Family Check-in - Data is passed from UserController

$pageStyles = [BASE_URL . 'assets/css/family-checkin.css'];
require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<!-- Leaflet.js for mini-map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLEg=" crossorigin=""></script>

<!-- Full-screen map modal -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/fullmap-modal.css">
<script src="<?php echo BASE_URL; ?>assets/js/fullmap-modal.js" defer></script>

<!-- Page-specific styles -->
<style>
.mini-map-wrapper {
    margin-top: 12px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.mini-map-container {
    width: 100%;
    height: 200px;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #e0e7ef;
    position: relative;
    background: #e8eef5;
}
.mini-map-container .leaflet-control-container { display: none; }
.mini-map-placeholder {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #90a4b7;
    font-size: 13px;
    font-family: 'Poppins', sans-serif;
    background: #eef2f7;
    z-index: 10;
}
.mini-map-placeholder i { font-size: 32px; color: #b0c4d8; }
.mini-map-placeholder.hidden { display: none; }
.btn-view-map {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 600;
    background: linear-gradient(135deg, #1A4D7F, #2d6a9f);
    color: #fff;
    border: none;
    border-radius: 10px;
    text-decoration: none;
    width: fit-content;
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
}
.btn-view-map:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(26,77,127,0.35);
}
[data-theme="dark"] .mini-map-container { border-color: var(--border-light); }
[data-theme="dark"] .mini-map-placeholder { background: #1e2a38; color: var(--text-muted); }
[data-theme="dark"] .mini-map-placeholder i { color: #4a6a88; }
</style>

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
            <h2>Your Location</h2>
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

        <!-- Leaflet Mini-Map: PWD location + family markers -->
        <div class="mini-map-wrapper">
            <div id="familyMiniMap" class="mini-map-container">
                <div class="mini-map-placeholder" id="familyMapPlaceholder">
                    <i class="ri-map-pin-time-line"></i>
                    <span>Waiting for GPS signal...</span>
                </div>
            </div>
            <button id="familyLocationLink" class="btn-view-map" style="display:none;" onclick="openFamilyFullMap()">
                <i class="ri-map-2-line"></i> View Full Map
            </button>
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
                        <i class="ri-phone-line"></i> <?php echo htmlspecialchars($member['phone_number']); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="family-status-right">
                    <div class="family-status-badge <?php echo htmlspecialchars($member['status_class']); ?>">
                        <?php echo htmlspecialchars($member['status_label']); ?>
                    </div>
                    <div class="family-time"><i class="ri-time-line"></i> <?php echo htmlspecialchars($member['time_ago']); ?></div>
                    <?php if (!$member['is_registered'] && !empty($member['phone_number'])): ?>
                    <button class="invite-btn" onclick="toggleInvitePanel(this)"
                        data-name="<?php echo htmlspecialchars(addslashes($member['display_name'])); ?>"
                        data-phone="<?php echo htmlspecialchars($member['phone_number']); ?>">
                        <i class="ri-mail-send-line"></i> Invite
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!$member['is_registered'] && !empty($member['phone_number'])): ?>
            <div class="invite-panel" id="invitePanel_<?php echo (int)$member['id']; ?>" style="display:none;">
                <div class="invite-panel-inner">
                    <div class="invite-panel-header">
                        <i class="ri-mail-send-line"></i>
                        <span>Invite <?php echo htmlspecialchars($member['display_name']); ?> to Silent Signal</span>
                    </div>
                    <div class="invite-sms-preview">
                        <div class="invite-sms-label"><i class="ri-message-3-line"></i> SMS Preview</div>
                        <div class="invite-sms-body"><?php
                            $inviterName = $_SESSION['user_name'] ?? 'Your contact';
                            echo htmlspecialchars(
                                "Hi " . $member['display_name'] . "! " . $inviterName . " added you to Silent Signal (PWD emergency app). Register: " . BASE_URL . " Use number " . $member['phone_number'] . " to sign up."
                            );
                        ?></div>
                    </div>
                    <div class="invite-notice">
                        <i class="ri-information-line"></i>
                        Ensure the family member registers using the same phone number you linked so they are automatically connected to your account.
                    </div>
                    <div class="invite-panel-actions">
                        <button class="invite-send-btn" onclick="sendInviteSms('<?php echo htmlspecialchars(addslashes($member['display_name'])); ?>','<?php echo htmlspecialchars($member['phone_number']); ?>')">
                            <i class="ri-send-plane-fill"></i> Send Invite via SMS
                        </button>
                        <button class="invite-cancel-btn" onclick="closeInvitePanel(this)">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

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
const BASE_URL              = <?php echo json_encode(BASE_URL); ?>;
const currentUserId         = <?php echo json_encode($_SESSION['user_id']); ?>;
const initialMyStatus       = <?php echo json_encode($myStatus['status'] ?? null); ?>;
const initialFamilyStatuses = <?php echo json_encode($familyStatuses); ?>;
const initialStatusHistory  = <?php echo json_encode($statusHistory); ?>;
let currentLat = <?php echo json_encode($myStatus['latitude']  ?? null); ?>;
let currentLng = <?php echo json_encode($myStatus['longitude'] ?? null); ?>;
</script>

<!-- Page-specific JavaScript -->
<script src="<?php echo BASE_URL; ?>assets/js/family-checkin.js?v=2"></script>

<script>
function toggleInvitePanel(btn) {
    const item  = btn.closest('.family-item');
    const panel = item.nextElementSibling;
    if (!panel || !panel.classList.contains('invite-panel')) return;
    const isOpen = panel.style.display !== 'none';
    document.querySelectorAll('.invite-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.invite-btn').forEach(b => b.classList.remove('active'));
    if (!isOpen) {
        panel.style.display = 'block';
        btn.classList.add('active');
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function closeInvitePanel(btn) {
    const panel = btn.closest('.invite-panel');
    if (panel) panel.style.display = 'none';
    document.querySelectorAll('.invite-btn').forEach(b => b.classList.remove('active'));
}

async function sendInviteSms(name, phone) {
    const inviterName = <?php echo json_encode($_SESSION['user_name'] ?? 'Your contact'); ?>;
    const appUrl      = <?php echo json_encode(BASE_URL); ?>;
    const cleanPhone  = phone.replace(/\s|-/g, '');

    // Short message to stay within 1 SMS part (160 chars)
    const body = `Hi ${name}! ${inviterName} added you to Silent Signal (PWD emergency app). Register: ${appUrl} Use number ${phone} to sign up.`;

    // Show sending state
    const sendBtn = event?.target?.closest('.invite-send-btn') || document.querySelector('.invite-send-btn');
    if (sendBtn) {
        sendBtn.disabled  = true;
        sendBtn.innerHTML = '<i class="ri-loader-4-line"></i> Sending...';
    }

    try {
        const response = await fetch(BASE_URL + 'index.php?action=send-philsms', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                message:  body,
                phones:   cleanPhone,
                contacts: [{ name: name, phone: cleanPhone }]
            })
        });

        const result = await response.json();
        const sent   = result && (
            result.success === true ||
            (typeof result.sent === 'number' && result.sent > 0)
        );

        if (sent) {
            showToast('Invite sent to ' + name + '!', '#2e7d32');
            document.querySelectorAll('.invite-panel').forEach(p => p.style.display = 'none');
            document.querySelectorAll('.invite-btn').forEach(b => b.classList.remove('active'));
        } else {
            showToast('Opening SMS app as backup...', '#e65100');
            setTimeout(() => {
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
                const sep   = isIOS ? '&' : '?';
                window.location.href = `sms:${cleanPhone}${sep}body=${encodeURIComponent(body)}`;
            }, 500);
        }

    } catch (err) {
        console.error('PhilSMS error:', err);
        showToast('Opening SMS app as backup...', '#e65100');
        setTimeout(() => {
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            const sep   = isIOS ? '&' : '?';
            window.location.href = `sms:${cleanPhone}${sep}body=${encodeURIComponent(body)}`;
        }, 500);
    } finally {
        if (sendBtn) {
            sendBtn.disabled  = false;
            sendBtn.innerHTML = '<i class="ri-send-plane-fill"></i> Send Invite via SMS';
        }
    }
}
</script>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>
<script>
/**
 * Build member array from initialFamilyStatuses, then open the
 * full-screen map modal.  Called by the "View Full Map" button.
 */
function openFamilyFullMap() {
    var pwdLat = (typeof currentLat !== 'undefined') ? currentLat : null;
    var pwdLng = (typeof currentLng !== 'undefined') ? currentLng : null;

    if (!pwdLat || !pwdLng) {
        alert('GPS location not yet acquired. Please wait a moment and try again.');
        return;
    }

    /* Build member list from server-provided family statuses */
    var memberColors = ['#e91e63','#ff9800','#9c27b0','#4caf50','#2196f3','#f44336'];
    var members = [];

    /* Also include the mock offsets used by the mini-map as fallback */
    var offsets = [
        { dLat:  0.003, dLng:  0.005 },
        { dLat: -0.004, dLng:  0.002 },
        { dLat:  0.001, dLng: -0.006 },
        { dLat: -0.002, dLng: -0.003 },
        { dLat:  0.005, dLng: -0.001 },
        { dLat: -0.001, dLng:  0.004 },
    ];

    var statuses = (typeof initialFamilyStatuses !== 'undefined' && Array.isArray(initialFamilyStatuses))
        ? initialFamilyStatuses : [];

    statuses.forEach(function (m, idx) {
        var off  = offsets[idx % offsets.length];
        var mLat = (m.latitude  && parseFloat(m.latitude)  !== 0) ? parseFloat(m.latitude)  : pwdLat + off.dLat;
        var mLng = (m.longitude && parseFloat(m.longitude) !== 0) ? parseFloat(m.longitude) : pwdLng + off.dLng;
        members.push({
            name:  m.display_name || ('Member ' + (idx + 1)),
            lat:   mLat,
            lng:   mLng,
            color: m.color || memberColors[idx % memberColors.length]
        });
    });

    /* If no registered family yet, fall back to FAMILY_MOCK_OFFSETS from the mini-map */
    if (members.length === 0 && typeof FAMILY_MOCK_OFFSETS !== 'undefined') {
        FAMILY_MOCK_OFFSETS.forEach(function (m, idx) {
            members.push({
                name:  m.name,
                lat:   pwdLat + m.latOffset,
                lng:   pwdLng + m.lngOffset,
                color: m.color || memberColors[idx % memberColors.length]
            });
        });
    }

    FullMapModal.open(pwdLat, pwdLng, members);
}
</script>