<?php
// controllers/FamilyController.php
// Handles all Family Member pages and AJAX endpoints

require_once __DIR__ . '/../models/FamilyDashboard.php';
require_once __DIR__ . '/../models/FamilyCheckin.php';

class FamilyController {
    protected $navItems;
    protected $userMenuItems;
    protected $footerLinks;
    protected $footerSupport;
    protected $footerSocial;

    public function __construct() {
        $this->initSharedData();
    }

    private function initSharedData() {
        $this->navItems = [
            ['action' => 'family-dashboard',                          'icon' => 'ri-home-line',          'label' => 'Home'],
            ['action' => 'family-dashboard#pwdMembers',               'icon' => 'ri-team-line',           'label' => 'PWD Members'],
            ['action' => 'family-dashboard#responseStatus',           'icon' => 'ri-alarm-warning-line',  'label' => 'Response Status'],
            ['action' => 'family-dashboard#recentEmergencyAlerts',    'icon' => 'ri-alert-line',          'label' => 'Recent Alerts'],
        ];

        $this->userMenuItems = [
            ['action' => 'medical-profile', 'icon' => 'ri-heart-pulse-line', 'label' => 'Medical Profile'],
        ];

        $this->footerLinks = [
            ['label' => 'Home',             'action' => 'family-dashboard'],
            ['label' => 'PWD Members',      'action' => 'family-dashboard#pwdMembers'],
            ['label' => 'Response Status',  'action' => 'family-dashboard#responseStatus'],
            ['label' => 'Recent Alerts',    'action' => 'family-dashboard#recentEmergencyAlerts'],
        ];

        $this->footerSupport = [
            ['label' => 'Help Center',  'href' => '#'],
            ['label' => 'Safety Guide', 'href' => '#'],
            ['label' => 'FSL Resources','href' => '#'],
            ['label' => 'Contact Us',   'action' => 'home', 'anchor' => '#contact'],
        ];

        $this->footerSocial = [
            ['icon' => 'fa-brands fa-facebook-f',  'href' => '#'],
            ['icon' => 'fa-brands fa-instagram',   'href' => '#'],
            ['icon' => 'fa-brands fa-tiktok',      'href' => '#'],
            ['icon' => 'fa-brands fa-x-twitter',   'href' => '#'],
        ];
    }

    private function getSharedData() {
        return [
            'navItems'      => $this->navItems,
            'userMenuItems' => $this->userMenuItems,
            'footerLinks'   => $this->footerLinks,
            'footerSupport' => $this->footerSupport,
            'footerSocial'  => $this->footerSocial,
            'currentAction' => $_GET['action'] ?? 'family-dashboard',
        ];
    }

    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login to access this page.';
            header('Location: ' . BASE_URL . 'index.php?action=auth');
            exit();
        }
    }

    private function requireFamilyRole() {
        $this->requireLogin();
        if ($_SESSION['user_role'] !== 'family') {
            $_SESSION['error'] = 'Access denied. This page is only for family members.';
            header('Location: ' . BASE_URL . 'index.php?action=dashboard');
            exit();
        }
    }

    // =========================================================================
    // PAGE: Family Dashboard
    // =========================================================================
    public function familyDashboard() {
        $this->requireFamilyRole();

        $pageTitle     = 'Family Dashboard - Silent Signal';
        extract($this->getSharedData());

        $familyMemberId = $_SESSION['user_id'];
        $model          = new FamilyDashboard();

        // ── PWD Members ──────────────────────────────────────────────────────
        $rawPwd = $model->getPwdMembers($familyMemberId);
        $avatarColors = ['#e53935','#1976d2','#43a047','#9c27b0','#ef6c00','#fbc02d'];

        $pwdMembers = [];
        foreach ($rawPwd as $i => $p) {
            $status   = $p['current_status'] ?? 'unknown';
            $statusLabels  = ['safe'=>'SAFE','danger'=>'DANGER','needs_assistance'=>'NEEDS HELP','unknown'=>'UNKNOWN'];
            $statusCSSMap  = ['safe'=>'safe','danger'=>'danger','needs_assistance'=>'danger','unknown'=>'unknown'];

            // Calculate age from DOB
            $age = '—';
            if (!empty($p['date_of_birth'])) {
                $dob = new DateTime($p['date_of_birth']);
                $age = (new DateTime())->diff($dob)->y;
            }

            // Time ago
            $timeAgo = 'No update';
            if (!empty($p['last_updated'])) {
                $diff = time() - strtotime($p['last_updated']);
                if ($diff < 60)        $timeAgo = 'Just now';
                elseif ($diff < 3600)  $timeAgo = round($diff/60) . ' min ago';
                elseif ($diff < 86400) $timeAgo = round($diff/3600) . ' hr ago';
                else                   $timeAgo = round($diff/86400) . ' day ago';
            }

            $initials = strtoupper(substr($p['fname'],0,1) . substr($p['lname'],0,1));

            $pwdMembers[] = [
                'id'           => $p['id'],
                'name'         => $p['fname'] . ' ' . $p['lname'],
                'photo'        => $initials,
                'color'        => $avatarColors[$i % count($avatarColors)],
                'relationship' => $p['relationship_type'],
                'status'       => $statusCSSMap[$status] ?? 'unknown',
                'statusLabel'  => $statusLabels[$status]  ?? 'UNKNOWN',
                'lastUpdated'  => $timeAgo,
                'location'     => !empty($p['latitude'])
                    ? 'Lat: ' . number_format((float)$p['latitude'],4) . ', Lng: ' . number_format((float)$p['longitude'],4)
                    : ($p['city'] ?? '—'),
                'battery'      => $p['battery_level'] ?? '—',
                'latitude'     => (float)($p['latitude']  ?? 0),
                'longitude'    => (float)($p['longitude'] ?? 0),
                'disability'   => $p['disability_type'] ?? '—',
                'bloodType'    => $p['blood_type']      ?? '—',
                'age'          => $age,
                'phone'        => $p['phone_number']    ?? '',
            ];
        }

        // ── Co-Family Members ─────────────────────────────────────────────────
        $rawFam         = $model->getCoFamilyMembers($familyMemberId);
        $coFamColors    = ['#4caf50','#2196f3','#ffc107','#e53935','#9c27b0','#ef6c00'];
        $otherFamilyMembers = [];

        foreach ($rawFam as $j => $f) {
            $respStatus = $f['response_status'] ?? null;
            $responded  = in_array($respStatus, ['acknowledged','on_the_way','arrived','resolved']);

            $lastSeen = 'Unknown';
            if (!empty($f['last_seen'])) {
                $diff = time() - strtotime($f['last_seen']);
                if ($diff < 60)        $lastSeen = 'Just now';
                elseif ($diff < 3600)  $lastSeen = round($diff/60) . ' min ago';
                elseif ($diff < 86400) $lastSeen = round($diff/3600) . ' hr ago';
                else                   $lastSeen = round($diff/86400) . ' day ago';
            }

            $respTime = '—';
            if (!empty($f['response_time'])) {
                $diff = time() - strtotime($f['response_time']);
                if ($diff < 60)        $respTime = 'Just now';
                elseif ($diff < 3600)  $respTime = round($diff/60) . ' min ago';
                elseif ($diff < 86400) $respTime = round($diff/3600) . ' hr ago';
                else                   $respTime = round($diff/86400) . ' day ago';
            } elseif (!$responded) {
                $respTime = 'Not responded';
            }

            $otherFamilyMembers[] = [
                'id'           => $f['id'],
                'name'         => $f['fname'] . ' ' . $f['lname'],
                'initials'     => strtoupper(substr($f['fname'],0,1) . substr($f['lname'],0,1)),
                'relationship' => $f['relationship_type'],
                'phone'        => $f['phone_number'] ?? '—',
                'status'       => $responded ? 'responded' : 'pending',
                'responseLabel'=> ['acknowledged'=>'Acknowledged','on_the_way'=>'On the way','arrived'=>'Arrived','resolved'=>'Resolved'][$respStatus] ?? ($responded ? 'Responded' : 'Pending'),
                'responseTime' => $respTime,
                'lastSeen'     => $lastSeen,
                'color'        => $coFamColors[$j % count($coFamColors)],
                'pwdName'      => trim(($f['pwd_fname'] ?? '') . ' ' . ($f['pwd_lname'] ?? '')),
            ];
        }

        // ── Recent Alerts ─────────────────────────────────────────────────────
        $rawAlerts   = $model->getRecentAlerts($familyMemberId, 10);
        $alertIconMap = [
            'sos'           => ['icon'=>'ri-alarm-warning-line',  'bg'=>'#ffebee','color'=>'#e53935'],
            'shake'         => ['icon'=>'ri-shake-hands-line',    'bg'=>'#fff3e0','color'=>'#ef6c00'],
            'panic_click'   => ['icon'=>'ri-cursor-line',         'bg'=>'#f3e5f5','color'=>'#9c27b0'],
            'medical'       => ['icon'=>'ri-heart-pulse-line',    'bg'=>'#e8f5e9','color'=>'#2e7d32'],
            'assistance'    => ['icon'=>'ri-hand-heart-line',     'bg'=>'#e3f2fd','color'=>'#1565c0'],
            'fall_detection'=> ['icon'=>'ri-run-line',            'bg'=>'#fff8e1','color'=>'#f57f17'],
        ];
        $alertStatusMap = [
            'active'      => ['label'=>'Active',      'class'=>'active'],
            'acknowledged'=> ['label'=>'Acknowledged','class'=>'responded'],
            'responded'   => ['label'=>'Responded',   'class'=>'responded'],
            'resolved'    => ['label'=>'Resolved',    'class'=>'resolved'],
            'cancelled'   => ['label'=>'Cancelled',   'class'=>'cancelled'],
        ];

        $recentAlerts = [];
        foreach ($rawAlerts as $a) {
            $iconSet    = $alertIconMap[$a['alert_type']] ?? $alertIconMap['sos'];
            $statusSet  = $alertStatusMap[$a['status'] ?? 'active'] ?? $alertStatusMap['active'];

            $timeAgo = '—';
            if (!empty($a['created_at'])) {
                $diff = time() - strtotime($a['created_at']);
                if ($diff < 60)        $timeAgo = 'Just now';
                elseif ($diff < 3600)  $timeAgo = round($diff/60) . ' min ago';
                elseif ($diff < 86400) $timeAgo = round($diff/3600) . ' hr ago';
                else                   $timeAgo = round($diff/86400) . ' day ago';
            }

            $responderName = null;
            if (!empty($a['responder_fname'])) {
                $responderName = $a['responder_fname'] . ' ' . $a['responder_lname'];
            }

            $recentAlerts[] = [
                'id'          => $a['id'],
                'type'        => $a['alert_type'],
                'icon'        => $iconSet['icon'],
                'iconBg'      => $iconSet['bg'],
                'iconColor'   => $iconSet['color'],
                'title'       => ucfirst(str_replace('_',' ',$a['alert_type'])) . ' Alert',
                'description' => !empty($a['message']) ? $a['message'] : ($a['fname'] . ' triggered an alert'),
                'location'    => $a['latitude'] ? 'Lat: '.number_format((float)$a['latitude'],4).', Lng: '.number_format((float)$a['longitude'],4) : '—',
                'time'        => $timeAgo,
                'status'      => $a['status'] ?? 'active',
                'statusLabel' => $statusSet['label'],
                'statusClass' => $statusSet['class'],
                'respondedBy' => $responderName,
                'pwdName'     => $a['fname'] . ' ' . $a['lname'],
            ];
        }

        // ── Quick Stats ───────────────────────────────────────────────────────
        $stats = $model->getQuickStats($familyMemberId);
        $quickStats = [
            ['label'=>'PWD Members',     'value'=>(string)$stats['pwdCount'],    'icon'=>'ri-user-heart-line',    'color'=>'#1976d2'],
            ['label'=>'Family Contacts', 'value'=>(string)$stats['familyCount'], 'icon'=>'ri-team-line',          'color'=>'#43a047'],
            ['label'=>'Total Alerts',    'value'=>(string)$stats['alertCount'],  'icon'=>'ri-error-warning-line', 'color'=>'#ef6c00'],
            ['label'=>'PWD Safe Rate',   'value'=>$stats['safePercent'],         'icon'=>'ri-shield-check-line',  'color'=>'#4caf50'],
        ];

        // Pass PWD list to JS for "send message" / "view profile" buttons
        $pwdMembersJson = json_encode(array_map(fn($p) => ['id'=>$p['id'],'name'=>$p['name']], $pwdMembers));

        require_once VIEW_PATH . 'family-dashboard.php';
    }

    // =========================================================================
    // AJAX: Get live status of a PWD
    // =========================================================================
    public function getPwdLiveStatus() {
        $this->requireFamilyRole();
        header('Content-Type: application/json');

        $pwdId = (int)($_GET['pwd_id'] ?? 0);
        if (!$pwdId) {
            echo json_encode(['success' => false, 'message' => 'Missing pwd_id.']);
            exit();
        }

        try {
            $model  = new FamilyDashboard();
            $status = $model->getPwdLiveStatus($pwdId, $_SESSION['user_id']);

            if (!$status) {
                echo json_encode(['success' => false, 'message' => 'No access or no data.']);
                exit();
            }

            // Time ago
            $diff = time() - strtotime($status['created_at']);
            if ($diff < 60)        $timeAgo = 'Just now';
            elseif ($diff < 3600)  $timeAgo = round($diff/60) . ' min ago';
            elseif ($diff < 86400) $timeAgo = round($diff/3600) . ' hr ago';
            else                   $timeAgo = round($diff/86400) . ' day ago';

            $status['time_ago'] = $timeAgo;
            echo json_encode(['success' => true, 'status' => $status]);
        } catch (Exception $e) {
            error_log('GetPwdLiveStatus Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error.']);
        }
        exit();
    }

    // =========================================================================
    // AJAX: Get PWD profile for modal
    // =========================================================================
    public function getPwdProfile() {
        $this->requireFamilyRole();
        header('Content-Type: application/json');

        $pwdId = (int)($_GET['pwd_id'] ?? 0);
        if (!$pwdId) {
            echo json_encode(['success' => false, 'message' => 'Missing pwd_id.']);
            exit();
        }

        try {
            $model   = new FamilyDashboard();
            $profile = $model->getPwdProfile($pwdId, $_SESSION['user_id']);

            if (!$profile) {
                echo json_encode(['success' => false, 'message' => 'Profile not found or no access.']);
                exit();
            }

            echo json_encode(['success' => true, 'profile' => $profile]);
        } catch (Exception $e) {
            error_log('GetPwdProfile Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error.']);
        }
        exit();
    }

    // =========================================================================
    // AJAX: Respond to an emergency alert
    // =========================================================================
    public function respondToAlert() {
        $this->requireFamilyRole();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['alert_id']) || empty($input['response_status'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data.']);
            exit();
        }

        $allowed = ['acknowledged', 'on_the_way', 'arrived', 'resolved'];
        $responseStatus = in_array($input['response_status'], $allowed) ? $input['response_status'] : 'acknowledged';

        try {
            $model = new FamilyDashboard();
            $ok = $model->respondToAlert(
                (int)$input['alert_id'],
                $_SESSION['user_id'],
                $responseStatus,
                $input['latitude']  ?? null,
                $input['longitude'] ?? null,
                $input['notes']     ?? null
            );
            echo json_encode(['success' => $ok, 'message' => $ok ? 'Response recorded.' : 'Failed to record response.']);
        } catch (Exception $e) {
            error_log('RespondToAlert Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error.']);
        }
        exit();
    }

    // =========================================================================
    // AJAX: Alert all family (broadcast)
    // =========================================================================
    public function alertAllFamily() {
        $this->requireFamilyRole();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        try {
            $model = new FamilyDashboard();
            // Get all PWDs for this family member and log the broadcast
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();

            $stmt = $db->prepare("
                SELECT pwd_user_id FROM family_pwd_relationships WHERE family_member_id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $pwdIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $message = $input['message'] ?? 'Emergency alert from family member.';

            foreach ($pwdIds as $pwdId) {
                $model->logBroadcast($_SESSION['user_id'], $pwdId, $message);
            }

            echo json_encode(['success' => true, 'message' => 'Alert broadcast to all family members.', 'pwd_count' => count($pwdIds)]);
        } catch (Exception $e) {
            error_log('AlertAllFamily Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error.']);
        }
        exit();
    }

    // =========================================================================
    // AJAX: Get refreshed dashboard data
    // =========================================================================
    public function refreshDashboard() {
        $this->requireFamilyRole();
        header('Content-Type: application/json');

        try {
            $model          = new FamilyDashboard();
            $familyMemberId = $_SESSION['user_id'];
            $rawPwd         = $model->getPwdMembers($familyMemberId);
            $stats          = $model->getQuickStats($familyMemberId);

            $pwdStatuses = [];
            foreach ($rawPwd as $p) {
                $status  = $p['current_status'] ?? 'unknown';
                $diff    = !empty($p['last_updated']) ? time() - strtotime($p['last_updated']) : null;
                $timeAgo = 'No update';
                if ($diff !== null) {
                    if ($diff < 60)        $timeAgo = 'Just now';
                    elseif ($diff < 3600)  $timeAgo = round($diff/60) . ' min ago';
                    elseif ($diff < 86400) $timeAgo = round($diff/3600) . ' hr ago';
                    else                   $timeAgo = round($diff/86400) . ' day ago';
                }
                $pwdStatuses[] = [
                    'id'          => $p['id'],
                    'name'        => $p['fname'] . ' ' . $p['lname'],
                    'status'      => $status,
                    'latitude'    => $p['latitude'],
                    'longitude'   => $p['longitude'],
                    'battery'     => $p['battery_level'],
                    'time_ago'    => $timeAgo,
                ];
            }

            echo json_encode([
                'success'     => true,
                'pwdStatuses' => $pwdStatuses,
                'stats'       => $stats,
            ]);
        } catch (Exception $e) {
            error_log('RefreshDashboard Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error.']);
        }
        exit();
    }
}
