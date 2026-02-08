<?php
// controllers/UserController.php
// Handles all logged-in user pages

require_once MODEL_PATH . 'MedicalProfile.php';

class UserController
{

    // Shared data for header and footer
    protected $navItems;
    protected $userMenuItems;
    protected $footerLinks;
    protected $footerSupport;
    protected $footerSocial;

    /**
     * Constructor - Initialize shared data
     */
    public function __construct()
    {
        $this->initSharedData();
    }

    /**
     * Initialize shared header/footer data
     */
    private function initSharedData()
    {
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
    private function getSharedData()
    {
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
    private function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login to access this page.";
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
    }

    /**
     * User Dashboard
     */
    public function dashboard()
    {
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
    public function emergencyAlert()
    {
        $this->requireLogin();
        $pageTitle = "Emergency Alert - Silent Signal";

        // Shared header/footer data
        extract($this->getSharedData());

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
                'description' => 'Shake your device to trigger an emergency alert. This feature detects rapid taps in 3 seconds with auto-escalation to SOS.',
            ],
            [
                'id' => 'panic-click',
                'icon' => 'ri-cursor-line',
                'color' => 'purple',
                'title' => 'Panic-Click Detection',
                'description' => 'Rapid taps in 3 seconds will trigger an emergency alert with automatic escalation to SOS.',
            ],
            [
                'id' => 'auto-message',
                'icon' => 'ri-message-3-line',
                'color' => 'green',
                'title' => 'Auto-send Prevention Message',
                'description' => 'Default message sent to emergency contacts: "DEAF/MUTE - TEXT ONLY - NO CALLS" with GPS link.',
            ],
            [
                'id' => 'multi-sensory',
                'icon' => 'ri-notification-3-line',
                'color' => 'orange',
                'title' => 'Multi-sensory Confirmation',
                'description' => 'Alert confirmation through vibration pattern and full-screen color flash.',
            ],
        ];

        // Emergency contacts (would come from database)
        $emergencyContacts = [
            ['name' => 'Maria Santos (Mother)', 'phone' => '+639123456789', 'isEmergency' => false],
            ['name' => 'Jose Santos (Father)', 'phone' => '+639234567890', 'isEmergency' => false],
            ['name' => 'Emergency Services', 'phone' => '911', 'isEmergency' => true],
        ];

        // Confirmation options
        $confirmationOptions = [
            ['icon' => 'ri-vibrate-line', 'title' => 'Vibration Pattern', 'desc' => 'Strong pulse feedback'],
            ['icon' => 'ri-flashlight-line', 'title' => 'Color Flash', 'desc' => 'Full screen visual alert'],
        ];

        // SMS Preview data
        $smsPreview = [
            'badge' => '⚠️ EMERGENCY ALERT ⚠️',
            'lines' => [
                'DEAF/MUTE - TEXT ONLY - NO CALLS',
                'Name: ' . ($_SESSION['user_name'] ?? 'User Name'),
                'Status: Needs Assistance',
                'Location: 123 Main St, Bacolod City',
            ],
            'link' => 'https://maps.google.com/?q=10.6776,122.9509',
        ];

        require_once VIEW_PATH . 'emergency-alert.php';
    }

    /**
     * Disaster Monitoring Page
     */
    public function disasterMonitor()
    {
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
     * Family Check-in Page (placeholder)
     */
    public function familyCheckin()
    {
        $this->requireLogin();
        $pageTitle = "Family Check-in - Silent Signal";

        // Shared header/footer data
        extract($this->getSharedData());

        require_once VIEW_PATH . 'family-checkin.php';
    }

    /**
     * Communication Hub Page (placeholder)
     */
    public function communicationHub()
    {
        $this->requireLogin();
        $pageTitle = "Communication Hub - Silent Signal";

        // Shared header/footer data
        extract($this->getSharedData());


        $categories = [
            ['id' => 'all', 'icon' => 'ri-grid-line', 'label' => 'All'],
            ['id' => 'medical', 'icon' => 'ri-hospital-line', 'label' => 'Medical'],
            ['id' => 'food', 'icon' => 'ri-restaurant-line', 'label' => 'Food'],
            ['id' => 'water', 'icon' => 'ri-drop-line', 'label' => 'Water'],
            ['id' => 'shelter', 'icon' => 'ri-home-line', 'label' => 'Shelter'],
            ['id' => 'emergency', 'icon' => 'ri-alarm-warning-line', 'label' => 'Emergency'],
        ];

        // Pre-defined icon-based messages
        $messages = [
            // Medical
            ['id' => 'medical_help', 'cat' => 'medical', 'icon' => 'ri-hospital-line', 'title' => 'Medical Help', 'desc' => 'I need medical assistance'],
            ['id' => 'medication', 'cat' => 'medical', 'icon' => 'ri-medicine-bottle-line', 'title' => 'Medication', 'desc' => 'I need medication'],
            ['id' => 'sick', 'cat' => 'medical', 'icon' => 'ri-emotion-sad-line', 'title' => 'Sick', 'desc' => 'I am feeling sick'],
            // Food
            ['id' => 'food', 'cat' => 'food', 'icon' => 'ri-restaurant-2-line', 'title' => 'Food', 'desc' => 'I need food'],
            ['id' => 'drinks', 'cat' => 'food', 'icon' => 'ri-cup-line', 'title' => 'Drinks', 'desc' => 'I need something to drink'],
            ['id' => 'hungry', 'cat' => 'food', 'icon' => 'ri-cake-line', 'title' => 'Hungry', 'desc' => 'I am hungry'],
            // Water
            ['id' => 'water', 'cat' => 'water', 'icon' => 'ri-drop-line', 'title' => 'Water', 'desc' => 'I need clean water'],
            ['id' => 'drinking_water', 'cat' => 'water', 'icon' => 'ri-goblet-line', 'title' => 'Drinking Water', 'desc' => 'I need drinking water'],
            // Shelter
            ['id' => 'shelter', 'cat' => 'shelter', 'icon' => 'ri-home-heart-line', 'title' => 'Shelter', 'desc' => 'I need shelter'],
            ['id' => 'rest_area', 'cat' => 'shelter', 'icon' => 'ri-hotel-bed-line', 'title' => 'Rest Area', 'desc' => 'Looking for rest area'],
            // Emergency
            ['id' => 'emergency', 'cat' => 'emergency', 'icon' => 'ri-alarm-warning-line', 'title' => 'Emergency', 'desc' => 'This is an emergency'],
            ['id' => 'injured', 'cat' => 'emergency', 'icon' => 'ri-health-book-line', 'title' => 'Injured', 'desc' => 'I am injured'],
            ['id' => 'danger', 'cat' => 'emergency', 'icon' => 'ri-error-warning-line', 'title' => 'Danger', 'desc' => 'I am in danger'],
            ['id' => 'flood', 'cat' => 'emergency', 'icon' => 'ri-flood-line', 'title' => 'Flood', 'desc' => 'Flooding in area'],
            ['id' => 'fire', 'cat' => 'emergency', 'icon' => 'ri-fire-line', 'title' => 'Fire', 'desc' => 'There is a fire'],
            ['id' => 'lost', 'cat' => 'emergency', 'icon' => 'ri-map-pin-user-line', 'title' => 'Lost', 'desc' => 'I am lost'],
        ];

        // Filipino Sign Language (FSL) downloadable resources
        $fslItems = [
            ['title' => 'Emergency Preparedness Guide', 'desc' => 'FSL illustrated guide for disaster preparation'],
            ['title' => 'Evacuation Instructions', 'desc' => 'Step-by-step FSL evacuation procedures'],
            ['title' => 'First Aid in FSL', 'desc' => 'Basic first aid instructions in FSL'],
        ];

        require_once VIEW_PATH . 'communication-hub.php';
    }

    /**
     * Medical Profile & Pre-Registration Page
     */
    public function medicalProfile()
    {
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
            'phone' => $profile['phone'] ?? $_SESSION['user_phone'] ?? '',
            'email' => $profile['email'] ?? $_SESSION['user_email'] ?? '',
            'streetAddress' => $profile['street_address'] ?? '',
            'city' => $profile['city'] ?? '',
            'province' => $profile['province'] ?? '',
            'zipCode' => $profile['zip_code'] ?? '',
        ];

        // Disability Status - auto-set based on user role
        $userRole = $_SESSION['user_role'] ?? '';
        $disabilityType = ($profile && isset($profile['disability_type'])) ? $profile['disability_type'] : '';

        // If no disability type set yet and user is PWD, set default
        if (empty($disabilityType) && $userRole === 'pwd') {
            $disabilityType = 'Deaf/Mute'; // Default, user can change later
        }

        $disabilityStatus = [
            'primary' => $disabilityType,
            'verified' => !empty($disabilityType),
        ];

        // Allergies (from JSON - already decoded by model)
        $allergies = $profile['allergies'] ?? [];

        // Current Medications (from JSON - already decoded by model)
        $medications = $profile['medications'] ?? [];

        // Medical Conditions (from JSON - already decoded by model)
        $medicalConditions = $profile['medical_conditions'] ?? [];

        // Blood Type
        $bloodType = $profile['blood_type'] ?? '';

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
            'name' => $personalInfo['firstName'] . ' ' . $personalInfo['lastName'],
            'pwdId' => $personalInfo['pwdId'],
            'phone' => $personalInfo['phone'],
            'address' => $personalInfo['streetAddress'] . ', ' . $personalInfo['city'],
            'status' => 'Emergency SOS Activated',
            'bloodType' => $bloodType,
            'allergies' => is_array($allergies) ? implode(', ', $allergies) : '',
            'medications' => is_array($medications) ? implode(', ', $medications) : '',
            'bloodType' => $bloodType,
            'allergies' => is_array($allergies) ? implode(', ', $allergies) : '',
            'medications' => is_array($medications) ? implode(', ', $medications) : '',
        ];

        // Medication Reminders (from JSON - already decoded by model)
        $medicationReminders = ($profile && isset($profile['medication_reminders'])) ? $profile['medication_reminders'] : [];

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
    public function saveMedicalProfile()
    {
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
}
