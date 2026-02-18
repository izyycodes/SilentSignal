<?php
// controllers/UserController.php
// Handles all logged-in user pages

require_once __DIR__ . '/../models/MedicalProfile.php';
require_once __DIR__ . '/../models/CommunicationHub.php';
require_once __DIR__ . '/../models/FamilyCheckin.php';

class UserController {
    
    // Shared data for header and footer
    protected $navItems;
    protected $userMenuItems;
    protected $footerLinks;
    protected $footerSupport;
    protected $footerSocial;
    
    /**
     * Constructor - Initialize shared data
     */
    public function __construct() {
        $this->initSharedData();
    }
    
    /**
     * Initialize shared header/footer data
     */
    private function initSharedData() {
        // Header navigation items
        $this->navItems = [
            ['action' => 'dashboard', 'icon' => 'ri-home-line', 'label' => 'Home'],
            ['action' => 'emergency-alert', 'icon' => 'ri-alarm-warning-line', 'label' => 'Emergency Alert'],
            ['action' => 'disaster-monitor', 'icon' => 'ri-earth-line', 'label' => 'Disaster Monitor'],
            ['action' => 'family-checkin', 'icon' => 'ri-team-line', 'label' => 'Family Check-in'],
            ['action' => 'communication-hub', 'icon' => 'ri-message-2-line', 'label' => 'Communication Hub'],
        ];
        
        // User dropdown menu items
        $this->userMenuItems = [
            ['action' => 'medical-profile', 'icon' => 'ri-heart-pulse-line', 'label' => 'Medical Profile'],
        ];
        
        // Footer quick links
        $this->footerLinks = [
            ['label' => 'Home', 'action' => 'dashboard'],
            ['label' => 'Emergency Alert', 'action' => 'emergency-alert'],
            ['label' => 'Disaster Monitor', 'action' => 'disaster-monitor'],
            ['label' => 'Family Check-in', 'action' => 'family-checkin'],
            ['label' => 'Communication Hub', 'action' => 'communication-hub'],
        ];
        
        // Footer support links
        $this->footerSupport = [
            ['label' => 'Help Center', 'href' => '#'],
            ['label' => 'Safety Guide', 'href' => '#'],
            ['label' => 'FSL Resources', 'href' => '#'],
            ['label' => 'Contact Us', 'action' => 'home', 'anchor' => '#contact'],
        ];
        
        // Footer social links
        $this->footerSocial = [
            ['icon' => 'fa-brands fa-facebook-f', 'href' => '#'],
            ['icon' => 'fa-brands fa-instagram', 'href' => '#'],
            ['icon' => 'fa-brands fa-tiktok', 'href' => '#'],
            ['icon' => 'fa-brands fa-x-twitter', 'href' => '#'],
        ];
    }
    
    /**
     * Get shared data for views
     */
    private function getSharedData() {
        return [
            'navItems' => $this->navItems,
            'userMenuItems' => $this->userMenuItems,
            'footerLinks' => $this->footerLinks,
            'footerSupport' => $this->footerSupport,
            'footerSocial' => $this->footerSocial,
            'currentAction' => $_GET['action'] ?? 'dashboard',
        ];
    }
    
    /**
     * Check if user is logged in
     */
    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login to access this page.";
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
        // Re-check account status on every page load
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        if (!$userModel->isUserActive($_SESSION['user_id'])) {
            session_destroy();
            session_start();
            $_SESSION['error'] = "Your account has been deactivated. Please contact the administrator.";
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
    }
    
    /**
     * User Dashboard
     */
    public function dashboard() {
        $this->requireLogin();
        $pageTitle = "Dashboard - Silent Signal";
        
        // Shared header/footer data
        extract($this->getSharedData());
        
        // User status data
        $userStatus = [
            'status' => 'safe',
            'label' => "I'M SAFE",
            'lastUpdated' => '2 minutes ago',
        ];
        
        // Module cards data
        $moduleCards = [
            [
                'id' => 'emergency-alert',
                'action' => 'emergency-alert',
                'icon' => 'ri-alarm-warning-line',
                'iconBg' => '#fff3e0',
                'iconColor' => '#ef6c00',
                'title' => 'Emergency Alert',
                'description' => 'Single-tap SOS, shake-to-alert, panic detection with GPS location & medical data via SMS.',
                'features' => [
                    ['icon' => 'ri-flashlight-line', 'text' => 'Instant SOS transmission'],
                    ['icon' => 'ri-map-pin-line', 'text' => 'GPS location sharing'],
                    ['icon' => 'ri-vibrate-line', 'text' => 'Multi-sensory confirmation'],
                ],
            ],
            [
                'id' => 'disaster-monitor',
                'action' => 'disaster-monitor',
                'icon' => 'ri-earth-line',
                'iconBg' => '#e8f5e9',
                'iconColor' => '#43a047',
                'title' => 'Disaster Monitoring',
                'description' => 'Typhoon/earthquake detection via PAGASA/PHIVOLCS with "Are You Safe?" visual prompts.',
                'alertBadge' => [
                    'label' => 'RECENT ALERTS',
                    'count' => '2 active alerts in your area',
                ],
            ],
            [
                'id' => 'family-checkin',
                'action' => 'family-checkin',
                'icon' => 'ri-team-line',
                'iconBg' => '#e3f2fd',
                'iconColor' => '#1976d2',
                'title' => 'Family Check-in',
                'description' => 'Real-time GPS tracking, photo/video updates, family dashboard with safety status.',
                'familyStatus' => [
                    ['name' => 'M', 'color' => '#e53935', 'status' => 'safe'],
                    ['name' => 'J', 'color' => '#ffc107', 'status' => 'safe'],
                    ['name' => 'A', 'color' => '#43a047', 'status' => 'safe'],
                ],
                'safeCount' => '2/3 safe',
            ],
            [
                'id' => 'communication-hub',
                'action' => 'communication-hub',
                'icon' => 'ri-message-2-line',
                'iconBg' => '#fce4ec',
                'iconColor' => '#d81b60',
                'title' => 'Communication Hub',
                'description' => 'Icon-based messages, one-tap camera alerts, SMS communication with safety instructions.',
                'quickIcons' => [
                    'ri-hospital-line',
                    'ri-restaurant-line',
                    'ri-alarm-warning-line',
                    'ri-first-aid-kit-line',
                ],
            ],
        ];
        
        // Recent activity data
        $recentActivity = [
            [
                'type' => 'typhoon',
                'icon' => 'ri-typhoon-line',
                'iconBg' => '#ffebee',
                'iconColor' => '#e53935',
                'title' => 'Typhoon Regis approaching',
                'time' => '10 min ago',
                'badge' => 'HIGH',
                'badgeClass' => 'high',
            ],
            [
                'type' => 'earthquake',
                'icon' => 'ri-earthquake-line',
                'iconBg' => '#fff3e0',
                'iconColor' => '#ef6c00',
                'title' => 'Magnitude 4.2 detected',
                'time' => '1 hour ago',
                'badge' => 'MEDIUM',
                'badgeClass' => 'medium',
            ],
        ];
        
        require_once VIEW_PATH . 'dashboard.php';
    }
    
    /**
     * Emergency Alert System Page
     */
    public function emergencyAlert() {
        $this->requireLogin();
        $pageTitle = "Emergency Alert - Silent Signal";
        
        // Shared header/footer data
        extract($this->getSharedData());
        
        // Load medical profile for user data
        $medicalProfileModel = new MedicalProfile();
        $profile = $medicalProfileModel->getByUserId($_SESSION['user_id']);
        
        // Build user data for SMS
        $userData = [
            'name' => ($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''),
            'phone' => $profile['phone'] ?? '',
            'pwdId' => $profile['pwd_id'] ?? '',
            'address' => ($profile['street_address'] ?? '') . ', ' . ($profile['city'] ?? '') . ', ' . ($profile['province'] ?? ''),
            'bloodType' => $profile['blood_type'] ?? '',
            'allergies' => is_array($profile['allergies'] ?? null) ? implode(', ', $profile['allergies']) : '',
            'medications' => is_array($profile['medications'] ?? null) ? implode(', ', $profile['medications']) : '',
            'conditions' => is_array($profile['medical_conditions'] ?? null) ? implode(', ', $profile['medical_conditions']) : '',
        ];
        
        // Get emergency contacts from profile
        $emergencyContacts = $profile['emergency_contacts'] ?? [];
        
        // Add colors and initials to contacts
        $colors = ['#4caf50', '#ffc107', '#2196f3', '#e53935', '#9c27b0'];
        foreach ($emergencyContacts as $i => &$contact) {
            if (!isset($contact['color'])) {
                $contact['color'] = $colors[$i % count($colors)];
            }
            if (!isset($contact['initials'])) {
                $nameParts = explode(' ', $contact['name'] ?? '');
                $contact['initials'] = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
            }
        }
        
        // Quick info cards data
        $infoCards = [
            ['icon' => 'ri-map-pin-line', 'label' => 'GPS Location'],
            ['icon' => 'ri-heart-pulse-line', 'label' => 'Medical Data'],
            ['icon' => 'ri-message-2-line', 'label' => 'SMS Alert'],
        ];
        
        // Feature cards data
        $featureCards = [
            [
                'id' => 'sos-transmission',
                'icon' => 'ri-hand-heart-line',
                'color' => 'blue',
                'title' => 'Single-Tap SOS Transmission',
                'description' => 'Press the button below to send an emergency alert with your GPS location and medical data via SMS.',
            ],
            [
                'id' => 'shake-alert',
                'icon' => 'ri-shake-hands-line',
                'color' => 'yellow',
                'title' => 'Shake-to-Alert Triggering',
                'description' => 'Enable shake detection to trigger emergency alerts by shaking your device.',
            ],
            [
                'id' => 'panic-click',
                'icon' => 'ri-cursor-line',
                'color' => 'purple',
                'title' => 'Panic-Click Detection',
                'description' => 'Rapid taps (5 times in 3 seconds) will automatically trigger an emergency alert.',
            ],
        ];
        
        // Confirmation options
        $confirmationOptions = [
            ['icon' => 'ri-vibrate-line', 'title' => 'Vibration Pattern', 'desc' => 'Strong pulse feedback'],
            ['icon' => 'ri-flashlight-line', 'title' => 'Color Flash', 'desc' => 'Full screen visual alert'],
        ];
        
        require_once VIEW_PATH . 'emergency-alert.php';
    }
    
    /**
     * Disaster Monitoring Page
     */
    public function disasterMonitor() {
        $this->requireLogin();
        $pageTitle = "Disaster Monitoring - Silent Signal";
        
        // Shared header/footer data
        extract($this->getSharedData());
        
        // Active disaster alerts (would come from API/database)
        $disasterAlerts = [
            [
                'type' => 'typhoon',
                'name' => 'Typhoon Odette',
                'source' => 'PAGASA',
                'severity' => 'HIGH',
                'description' => 'Category 4 typhoon approaching. Expected landfall in 6 hours.',
                'stats' => [
                    ['label' => 'Wind Speed', 'value' => '185 km/h', 'class' => 'danger'],
                ],
                'location' => 'Western Visayas',
                'time' => '10 minutes ago',
            ],
            [
                'type' => 'earthquake',
                'name' => 'Earthquake Detected',
                'source' => 'PHIVOLCS',
                'severity' => 'MEDIUM',
                'description' => 'Magnitude 4.2 earthquake recorded. No tsunami threat.',
                'stats' => [
                    ['label' => 'Magnitude', 'value' => '4.2', 'class' => 'warning'],
                    ['label' => 'Depth', 'value' => '10 km', 'class' => ''],
                ],
                'location' => 'Negros Occidental',
                'time' => '1 hour ago',
            ],
        ];
        
        // Weather conditions data
        $weatherConditions = [
            ['icon' => 'ri-temp-hot-line', 'label' => 'Temperature', 'value' => '28°C'],
            ['icon' => 'ri-drop-line', 'label' => 'Humidity', 'value' => '89%'],
            ['icon' => 'ri-windy-line', 'label' => 'Wind Speed', 'value' => '45 km/h'],
            ['icon' => 'ri-rainy-line', 'label' => 'Rainfall', 'value' => 'Heavy'],
            ['icon' => 'ri-dashboard-3-line', 'label' => 'Pressure', 'value' => '1005 hPa', 'fullWidth' => true],
        ];
        
        // Auto-SOS checklist items
        $autoSosSteps = [
            'Alert triggered by disaster detection',
            '"Are You Safe?" prompt displayed',
            '30 second countdown begins',
            'If no response: Auto SOS sent with GPS location',
        ];
        
        // Alert history (would come from database)
        $alertHistory = [
            ['type' => 'typhoon', 'name' => 'Typhoon Alert', 'time' => '2 hours ago', 'status' => 'dismissed'],
            ['type' => 'earthquake', 'name' => 'Earthquake Alert', 'time' => '5 hours ago', 'status' => 'responded'],
            ['type' => 'flood', 'name' => 'Flood Warning', 'time' => '1 day ago', 'status' => 'auto-sos'],
        ];
        
        // Alert type icons mapping
        $alertIcons = [
            'typhoon' => 'ri-typhoon-line',
            'earthquake' => 'ri-earthquake-line',
            'flood' => 'ri-flood-line',
        ];
        
        // Severity badge classes
        $severityClasses = [
            'HIGH' => 'high',
            'MEDIUM' => 'medium',
            'LOW' => 'low',
        ];
        
        require_once VIEW_PATH . 'disaster-monitoring.php';
    }
    
    /**
     * Family Check-in Page
     */
    public function familyCheckin() {
        $this->requireLogin();
        $pageTitle = "Family Check-in - Silent Signal";

        // Shared header/footer data
        extract($this->getSharedData());

        $userId = $_SESSION['user_id'];
        $familyCheckinModel = new FamilyCheckin();

        // Sync emergency contacts from medical profile → pwd_emergency_contacts + family_pwd_relationships
        require_once __DIR__ . '/../models/MedicalProfile.php';
        $medModel   = new MedicalProfile();
        $profile    = $medModel->getByUserId($userId);
        $rawContacts = $profile['emergency_contacts'] ?? [];
        if (!empty($rawContacts)) {
            $familyCheckinModel->syncEmergencyContacts($userId, $rawContacts);
        }

        // Get the family group name and member count (now from pwd_emergency_contacts)
        $familyGroupName   = $familyCheckinModel->getFamilyGroupName($userId);
        $familyMemberCount = $familyCheckinModel->getFamilyMemberCount($userId);

        // Get my own latest status
        $myStatus = $familyCheckinModel->getLatestStatus($userId);

        // Get ALL emergency contacts with their status (registered users have live status)
        $familyStatuses = $familyCheckinModel->getFamilyStatusesForPwd($userId);

        // Get status history for breadcrumbs
        $statusHistory = $familyCheckinModel->getStatusHistory($userId, 20);

        // Enrich each contact row for the view
        $avatarColors = ['#e53935', '#ffc107', '#43a047', '#1976d2', '#9c27b0', '#ef6c00'];
        foreach ($familyStatuses as $i => &$member) {
            // Name: use fname/lname if registered user, else use contact_name
            if (!empty($member['fname'])) {
                $member['display_name'] = $member['fname'] . ' ' . $member['lname'];
                $member['initials']     = strtoupper(substr($member['fname'],0,1) . substr($member['lname'],0,1));
                $member['is_registered'] = true;
            } else {
                $member['display_name'] = $member['fname_full'];
                $nameParts = explode(' ', trim($member['fname_full']));
                $member['initials']     = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
                $member['is_registered'] = false;
            }

            $member['color'] = $avatarColors[$i % count($avatarColors)];

            $statusLabels  = ['safe' => 'SAFE', 'danger' => 'DANGER', 'needs_assistance' => 'NEEDS HELP', 'unknown' => 'UNKNOWN'];
            $statusClasses = ['safe' => 'safe', 'danger' => 'needs-help', 'needs_assistance' => 'needs-help', 'unknown' => 'unknown'];
            $cs = $member['current_status'] ?? 'unknown';
            $member['status_label'] = $statusLabels[$cs] ?? 'UNKNOWN';
            $member['status_class'] = $statusClasses[$cs] ?? 'unknown';

            // Unregistered contacts cannot have a live status
            if (!$member['is_registered']) {
                $member['status_label'] = 'NOT REGISTERED';
                $member['status_class'] = 'not-registered';
            }

            if (!empty($member['last_updated'])) {
                $diff = time() - strtotime($member['last_updated']);
                if ($diff < 60)        $member['time_ago'] = 'Just now';
                elseif ($diff < 3600)  $member['time_ago'] = round($diff/60) . ' min ago';
                elseif ($diff < 86400) $member['time_ago'] = round($diff/3600) . ' hr ago';
                else                   $member['time_ago'] = round($diff/86400) . ' day ago';
            } else {
                $member['time_ago'] = $member['is_registered'] ? 'No update yet' : 'Not on Silent Signal';
            }
        }
        unset($member);

        require_once VIEW_PATH . 'family-checkin.php';
    }

    /**
     * AJAX: Update status (family check-in)
     */
    public function updateCheckinStatus() {
        $this->requireLogin();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['status'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data.']);
            exit();
        }

        $allowed = ['safe', 'danger', 'needs_assistance', 'unknown'];
        $status = in_array($input['status'], $allowed) ? $input['status'] : 'unknown';
        $latitude = isset($input['latitude']) ? (float)$input['latitude'] : null;
        $longitude = isset($input['longitude']) ? (float)$input['longitude'] : null;
        $message = $input['message'] ?? null;

        try {
            $model = new FamilyCheckin();
            $ok = $model->saveStatusUpdate(
                $_SESSION['user_id'],
                $status,
                $latitude,
                $longitude,
                $message
            );
            echo json_encode(['success' => $ok, 'message' => $ok ? 'Status updated.' : 'Failed to save.']);
        } catch (Exception $e) {
            error_log("UpdateCheckinStatus Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error.']);
        }
        exit();
    }

    /**
     * AJAX: Get family status (family check-in live refresh)
     */
    public function getFamilyStatus() {
        $this->requireLogin();
        header('Content-Type: application/json');

        try {
            $model  = new FamilyCheckin();
            $userId = $_SESSION['user_id'];

            $familyStatuses = $model->getFamilyStatusesForPwd($userId);
            $myStatus       = $model->getLatestStatus($userId);

            $avatarColors  = ['#e53935', '#ffc107', '#43a047', '#1976d2', '#9c27b0', '#ef6c00'];
            $statusLabels  = ['safe'=>'SAFE','danger'=>'DANGER','needs_assistance'=>'NEEDS HELP','unknown'=>'UNKNOWN'];
            $statusClasses = ['safe'=>'safe','danger'=>'needs-help','needs_assistance'=>'needs-help','unknown'=>'unknown'];

            foreach ($familyStatuses as $i => &$member) {
                if (!empty($member['fname'])) {
                    $member['display_name'] = $member['fname'] . ' ' . $member['lname'];
                    $member['initials']     = strtoupper(substr($member['fname'],0,1) . substr($member['lname'],0,1));
                    $member['is_registered'] = true;
                } else {
                    $member['display_name'] = $member['fname_full'];
                    $np = explode(' ', trim($member['fname_full']));
                    $member['initials']     = strtoupper(substr($np[0]??'',0,1).substr($np[1]??'',0,1));
                    $member['is_registered'] = false;
                }
                $member['color'] = $avatarColors[$i % count($avatarColors)];
                $cs = $member['current_status'] ?? 'unknown';
                if (!$member['is_registered']) {
                    $member['status_label'] = 'NOT REGISTERED';
                    $member['status_class'] = 'not-registered';
                } else {
                    $member['status_label'] = $statusLabels[$cs] ?? 'UNKNOWN';
                    $member['status_class'] = $statusClasses[$cs] ?? 'unknown';
                }
                if (!empty($member['last_updated'])) {
                    $diff = time() - strtotime($member['last_updated']);
                    if ($diff < 60)        $member['time_ago'] = 'Just now';
                    elseif ($diff < 3600)  $member['time_ago'] = round($diff/60) . ' min ago';
                    elseif ($diff < 86400) $member['time_ago'] = round($diff/3600) . ' hr ago';
                    else                   $member['time_ago'] = round($diff/86400) . ' day ago';
                } else {
                    $member['time_ago'] = $member['is_registered'] ? 'No update yet' : 'Not on Silent Signal';
                }
            }
            unset($member);

            echo json_encode([
                'success'        => true,
                'myStatus'       => $myStatus,
                'familyStatuses' => $familyStatuses
            ]);
        } catch (Exception $e) {
            error_log("GetFamilyStatus Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error.']);
        }
        exit();
    }

    public function getLocationHistory() {
        $this->requireLogin();
        header('Content-Type: application/json');

        try {
            $model = new FamilyCheckin();
            $history = $model->getStatusHistory($_SESSION['user_id'], 20);
            foreach ($history as &$item) {
                $diff = time() - strtotime($item['created_at']);
                if ($diff < 60) $item['time_ago'] = 'Just now';
                elseif ($diff < 3600) $item['time_ago'] = round($diff/60) . ' min ago';
                elseif ($diff < 86400) $item['time_ago'] = round($diff/3600) . ' hr ago';
                else $item['time_ago'] = round($diff/86400) . ' day ago';
            }
            unset($item);
            echo json_encode(['success' => true, 'history' => $history]);
        } catch (Exception $e) {
            error_log("GetLocationHistory Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error.']);
        }
        exit();
    }

    /**
     * AJAX: Log media capture (family check-in)
     */
    public function logCheckinMedia() {
        $this->requireLogin();
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        try {
            $model = new FamilyCheckin();
            $ok = $model->logMediaCapture(
                $_SESSION['user_id'],
                $input['type'] ?? 'photo',
                $input['latitude'] ?? null,
                $input['longitude'] ?? null
            );
            echo json_encode(['success' => $ok]);
        } catch (Exception $e) {
            error_log("LogCheckinMedia Error: " . $e->getMessage());
            echo json_encode(['success' => false]);
        }
        exit();
    }
    
    /**
     * Communication Hub Page
     */
    public function communicationHub() {
        $this->requireLogin();
        $pageTitle = "Communication Hub - Silent Signal";

        // Shared header/footer data
        extract($this->getSharedData());

        // Load user's medical profile for emergency contacts
        $medicalProfileModel = new MedicalProfile();
        $profile = $medicalProfileModel->getByUserId($_SESSION['user_id']);

        // Build user info for SMS
        $userInfo = [
            'name' => trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')) ?: ($_SESSION['user_name'] ?? 'User'),
            'phone' => $profile['phone'] ?? '',
            'address' => trim(implode(', ', array_filter([
                $profile['street_address'] ?? '',
                $profile['city'] ?? '',
                $profile['province'] ?? ''
            ]))),
            'bloodType' => $profile['blood_type'] ?? '',
            'pwdId' => $profile['pwd_id'] ?? '',
        ];

        // Emergency contacts from profile
        $emergencyContacts = $profile['emergency_contacts'] ?? [];
        $colors = ['#4caf50', '#ffc107', '#2196f3', '#e53935', '#9c27b0'];
        foreach ($emergencyContacts as $i => &$contact) {
            if (!isset($contact['color'])) $contact['color'] = $colors[$i % count($colors)];
            if (!isset($contact['initials'])) {
                $np = explode(' ', $contact['name'] ?? '');
                $contact['initials'] = strtoupper(substr($np[0] ?? '', 0, 1) . substr($np[1] ?? '', 0, 1));
            }
        }
        unset($contact);

        // Categories
        $categories = [
            ['id' => 'all',       'icon' => 'ri-grid-line',          'label' => 'All'],
            ['id' => 'medical',   'icon' => 'ri-hospital-line',       'label' => 'Medical'],
            ['id' => 'food',      'icon' => 'ri-restaurant-line',     'label' => 'Food'],
            ['id' => 'water',     'icon' => 'ri-drop-line',           'label' => 'Water'],
            ['id' => 'shelter',   'icon' => 'ri-home-line',           'label' => 'Shelter'],
            ['id' => 'emergency', 'icon' => 'ri-alarm-warning-line',  'label' => 'Emergency'],
        ];

        // Pre-defined icon-based messages
        $messages = [
            ['id' => 'medical_help',   'cat' => 'medical',   'icon' => 'ri-hospital-line',        'title' => 'Medical Help',    'desc' => 'I need medical assistance'],
            ['id' => 'medication',     'cat' => 'medical',   'icon' => 'ri-medicine-bottle-line',  'title' => 'Medication',      'desc' => 'I need medication'],
            ['id' => 'sick',           'cat' => 'medical',   'icon' => 'ri-emotion-sad-line',      'title' => 'Sick',            'desc' => 'I am feeling sick'],
            ['id' => 'first_aid',      'cat' => 'medical',   'icon' => 'ri-first-aid-kit-line',    'title' => 'First Aid',       'desc' => 'I need first aid'],
            ['id' => 'food',           'cat' => 'food',      'icon' => 'ri-restaurant-2-line',     'title' => 'Food',            'desc' => 'I need food'],
            ['id' => 'drinks',         'cat' => 'food',      'icon' => 'ri-cup-line',              'title' => 'Drinks',          'desc' => 'I need something to drink'],
            ['id' => 'hungry',         'cat' => 'food',      'icon' => 'ri-cake-line',             'title' => 'Hungry',          'desc' => 'I am hungry'],
            ['id' => 'water',          'cat' => 'water',     'icon' => 'ri-drop-line',             'title' => 'Water',           'desc' => 'I need clean water'],
            ['id' => 'drinking_water', 'cat' => 'water',     'icon' => 'ri-goblet-line',           'title' => 'Drinking Water',  'desc' => 'I need drinking water'],
            ['id' => 'shelter',        'cat' => 'shelter',   'icon' => 'ri-home-heart-line',       'title' => 'Shelter',         'desc' => 'I need shelter'],
            ['id' => 'rest_area',      'cat' => 'shelter',   'icon' => 'ri-hotel-bed-line',        'title' => 'Rest Area',       'desc' => 'Looking for rest area'],
            ['id' => 'emergency',      'cat' => 'emergency', 'icon' => 'ri-alarm-warning-line',    'title' => 'Emergency',       'desc' => 'This is an emergency'],
            ['id' => 'injured',        'cat' => 'emergency', 'icon' => 'ri-health-book-line',      'title' => 'Injured',         'desc' => 'I am injured'],
            ['id' => 'danger',         'cat' => 'emergency', 'icon' => 'ri-error-warning-line',    'title' => 'Danger',          'desc' => 'I am in danger'],
            ['id' => 'flood',          'cat' => 'emergency', 'icon' => 'ri-flood-line',            'title' => 'Flood',           'desc' => 'Flooding in area'],
            ['id' => 'fire',           'cat' => 'emergency', 'icon' => 'ri-fire-line',             'title' => 'Fire',            'desc' => 'There is a fire'],
            ['id' => 'lost',           'cat' => 'emergency', 'icon' => 'ri-map-pin-user-line',     'title' => 'Lost',            'desc' => 'I am lost'],
        ];

        // Filipino Sign Language (FSL) downloadable resources
        $fslItems = [
            [
                'title' => 'Emergency Preparedness Guide',
                'desc'  => 'FSL illustrated guide for disaster preparation',
                'file'  => 'fsl-emergency-preparedness.pdf',
            ],
            [
                'title' => 'Evacuation Instructions',
                'desc'  => 'Step-by-step FSL evacuation procedures',
                'file'  => 'fsl-evacuation-instructions.pdf',
            ],
            [
                'title' => 'First Aid in FSL',
                'desc'  => 'Basic first aid instructions in FSL',
                'file'  => 'fsl-first-aid.pdf',
            ],
            [
                'title' => 'Disaster Communication Signs',
                'desc'  => 'Common disaster-related FSL signs and phrases',
                'file'  => 'fsl-disaster-communication.pdf',
            ],
        ];

        require_once VIEW_PATH . 'communication-hub.php';
    }

    /**
     * AJAX: Send SMS via Communication Hub
     */
    public function sendHubSms() {
        $this->requireLogin();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['messages'])) {
            echo json_encode(['success' => false, 'message' => 'No messages selected.']);
            exit();
        }

        try {
            $model = new CommunicationHub();
            $ok = $model->logSmsEvent(
                $_SESSION['user_id'],
                $input['messages'],
                $input['contacts'] ?? [],
                $input['latitude'] ?? null,
                $input['longitude'] ?? null,
                $input['locationLabel'] ?? null
            );
            echo json_encode([
                'success' => $ok,
                'message' => $ok ? 'SMS sent to your emergency contacts!' : 'Failed to log SMS.'
            ]);
        } catch (Exception $e) {
            error_log("SendHubSms Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error.']);
        }
        exit();
    }

    /**
     * AJAX: Log media capture in Communication Hub
     */
    public function logHubMedia() {
        $this->requireLogin();
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        try {
            $model = new CommunicationHub();
            $ok = $model->logMediaCapture(
                $_SESSION['user_id'],
                $input['type'] ?? 'photo',
                $input['latitude'] ?? null,
                $input['longitude'] ?? null
            );
            echo json_encode(['success' => $ok]);
        } catch (Exception $e) {
            error_log("LogHubMedia Error: " . $e->getMessage());
            echo json_encode(['success' => false]);
        }
        exit();
    }

    /**
     * AJAX: Get emergency contacts for current user (for Communication Hub)
     */
    public function getEmergencyContacts() {
        $this->requireLogin();
        header('Content-Type: application/json');
        try {
            $model = new MedicalProfile();
            $profile = $model->getByUserId($_SESSION['user_id']);
            $contacts = $profile['emergency_contacts'] ?? [];
            $colors = ['#4caf50', '#ffc107', '#2196f3', '#e53935', '#9c27b0'];
            foreach ($contacts as $i => &$c) {
                if (!isset($c['color'])) $c['color'] = $colors[$i % count($colors)];
                if (!isset($c['initials'])) {
                    $np = explode(' ', $c['name'] ?? '');
                    $c['initials'] = strtoupper(substr($np[0] ?? '', 0, 1) . substr($np[1] ?? '', 0, 1));
                }
            }
            unset($c);
            echo json_encode(['success' => true, 'contacts' => $contacts]);
        } catch (Exception $e) {
            error_log("GetEmergencyContacts Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'contacts' => []]);
        }
        exit();
    }
    
    /**
     * Medical Profile & Pre-Registration Page
     */
    public function medicalProfile() {
        $this->requireLogin();
        $pageTitle = "Medical Profile - Silent Signal";
        
        // Shared header/footer data
        extract($this->getSharedData());
        
        // Load medical profile from database
        $medicalProfileModel = new MedicalProfile();
        $profile = $medicalProfileModel->getByUserId($_SESSION['user_id']);
        
        // Tab navigation
        $tabs = [
            ['id' => 'medical-profile', 'icon' => 'ri-heart-pulse-line', 'label' => 'Medical Profile'],
            ['id' => 'emergency-contacts', 'icon' => 'ri-contacts-line', 'label' => 'Emergency Contacts'],
            ['id' => 'medication-reminders', 'icon' => 'ri-alarm-line', 'label' => 'Medication Reminders'],
        ];
        
        // Personal Information from database or empty defaults
        $personalInfo = [
            'firstName' => $profile['first_name'] ?? '',
            'lastName' => $profile['last_name'] ?? '',
            'dateOfBirth' => $profile['date_of_birth'] ?? '',
            'gender' => $profile['gender'] ?? '',
            'pwdId' => $profile['pwd_id'] ?? '',
            'phone' => !empty($profile['phone']) ? $profile['phone'] : ($_SESSION['user_phone'] ?? ''),
            'email' => $profile['email'] ?? $_SESSION['user_email'] ?? '',
            'streetAddress' => $profile['street_address'] ?? '',
            'city' => $profile['city'] ?? '',
            'province' => $profile['province'] ?? '',
            'zipCode' => $profile['zip_code'] ?? '',
        ];
        
        // Disability Status - get is_verified from users table
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        $userVerified = $userModel->getUserVerifiedStatus($_SESSION['user_id']);
        $disabilityStatus = [
            'primary' => !empty($profile['disability_type']) ? $profile['disability_type'] : 'Not specified',
            'is_verified' => $userVerified,
        ];
        
        // Allergies (from JSON - already decoded by model)
        $allergies = $profile['allergies'] ?? [];
        
        // Current Medications (from JSON - already decoded by model)
        $medications = $profile['medications'] ?? [];
        
        // Medical Conditions (from JSON - already decoded by model)
        $medicalConditions = $profile['medical_conditions'] ?? [];
        
        // Blood Type
        $bloodType = $profile['blood_type'] ?? 'Not set';
        
        // Emergency Contacts (from JSON - already decoded by model)
        $emergencyContacts = $profile['emergency_contacts'] ?? [];
        
        // Add colors to contacts if not present
        $colors = ['#4caf50', '#ffc107', '#2196f3', '#e53935', '#9c27b0'];
        foreach ($emergencyContacts as $i => &$contact) {
            if (!isset($contact['color'])) {
                $contact['color'] = $colors[$i % count($colors)];
            }
            if (!isset($contact['initials'])) {
                $nameParts = explode(' ', $contact['name'] ?? '');
                $contact['initials'] = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
            }
        }
        
        // SMS Configuration (build from profile data)
        $smsConfig = [
            'name' => $personalInfo['firstName'] . ' ' . $personalInfo['lastName'],
            'pwdId' => $personalInfo['pwdId'],
            'phone' => $personalInfo['phone'],
            'address' => $personalInfo['streetAddress'] . ', ' . $personalInfo['city'],
            'status' => 'Emergency SOS Activated',
            'bloodType' => $bloodType,
            'allergies' => is_array($allergies) ? implode(', ', $allergies) : '',
            'medications' => is_array($medications) ? implode(', ', $medications) : '',
        ];
        
        // Medication Reminders (from JSON - already decoded by model)
        $medicationReminders = $profile['medication_reminders'] ?? [];
        
        // Add colors to reminders if not present
        foreach ($medicationReminders as $i => &$reminder) {
            if (!isset($reminder['color'])) {
                $reminder['color'] = $colors[$i % count($colors)];
            }
        }
        
        // Reminder Features (static)
        $reminderFeatures = [
            'Full-screen visual alerts',
            'Strong vibration pattern',
            'LED flasher alert flash',
            'Customizable reminder times',
        ];
        
        require_once VIEW_PATH . 'medical-profile.php';
    }
    
    /**
     * Save Medical Profile (AJAX endpoint)
     */
    public function saveMedicalProfile() {
        $this->requireLogin();
        
        // Set JSON header
        header('Content-Type: application/json');
        
        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Invalid data received.']);
            exit();
        }
        
        try {
            $medicalProfile = new MedicalProfile();
            
            // Prepare data for saving
            $profileData = [
                'first_name' => $input['firstName'] ?? '',
                'last_name' => $input['lastName'] ?? '',
                'date_of_birth' => !empty($input['dateOfBirth']) ? $input['dateOfBirth'] : null,
                'gender' => $input['gender'] ?? '',
                'pwd_id' => $input['pwdId'] ?? '',
                'phone' => $input['phone'] ?? '',
                'email' => $input['email'] ?? '',
                'street_address' => $input['streetAddress'] ?? '',
                'city' => $input['city'] ?? '',
                'province' => $input['province'] ?? '',
                'zip_code' => $input['zipCode'] ?? '',
                'disability_type' => $input['disabilityType'] ?? '',
                'blood_type' => $input['bloodType'] ?? '',
                'allergies' => $input['allergies'] ?? [],
                'medications' => $input['medications'] ?? [],
                'medical_conditions' => $input['medicalConditions'] ?? [],
                'emergency_contacts' => $input['emergencyContacts'] ?? [],
                'sms_template' => $input['smsTemplate'] ?? '',
                'medication_reminders' => $input['medicationReminders'] ?? []
            ];
            
            if ($medicalProfile->saveProfile($_SESSION['user_id'], $profileData)) {
                // Sync emergency contacts → pwd_emergency_contacts + family_pwd_relationships
                require_once __DIR__ . '/../models/FamilyCheckin.php';
                $fcModel = new FamilyCheckin();
                $fcModel->syncEmergencyContacts($_SESSION['user_id'], $profileData['emergency_contacts'] ?? []);
                echo json_encode(['success' => true, 'message' => 'Profile saved successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save profile.']);
            }
        } catch (Exception $e) {
            error_log("Save Medical Profile Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
        }
        
        exit();
    }
    
    /**
     * Log Emergency Alert (AJAX endpoint)
     */
    public function logEmergencyAlert() {
        $this->requireLogin();
        
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Invalid data.']);
            exit();
        }
        
        try {
            require_once __DIR__ . '/../config/Database.php';
            $database = new Database();
            $db = $database->getConnection();
            
            $stmt = $db->prepare("
                INSERT INTO emergency_alerts (user_id, alert_type, message, latitude, longitude, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $_SESSION['user_id'],
                $input['type'] ?? 'sos',
                $input['message'] ?? '',
                $input['location']['lat'] ?? null,
                $input['location']['lng'] ?? null
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Alert logged.']);
        } catch (Exception $e) {
            error_log("Log Emergency Alert Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to log alert.']);
        }
        
        exit();
    }
}