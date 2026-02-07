<?php
// controllers/UserController.php
// Handles all logged-in user pages

class UserController
{

    /**
     * Check if user is logged in
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Require login (redirect to auth if not logged in)
     */
    public function requireLogin()
    {
        if (!$this->isLoggedIn()) {
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


        // put data here

        require_once VIEW_PATH . 'family-checkin.php';
    }

    /**
     * Communication Hub Page (placeholder)
     */
    public function communicationHub()
    {
        $this->requireLogin();
        $pageTitle = "Communication Hub - Silent Signal";


        // put data here

        require_once VIEW_PATH . 'communication-hub.php';
    }

    /**
     * Medical Profile & Pre-Registration Page
     */
    public function medicalProfile() {
    $this->requireLogin();
    $pageTitle = "Medical Profile - Silent Signal";
    
    // Get user ID from session
    $userId = $_SESSION['user_id'];
    
    // Load medical profile model
    require_once MODEL_PATH . 'MedicalProfile.php';
    $medicalProfileModel = new MedicalProfile();
    
    // Get existing profile from database
    $existingProfile = $medicalProfileModel->getByUserId($userId);
    
    // Debug: Check what's being loaded
    error_log("Existing Profile: " . print_r($existingProfile, true));
    
    // Tab navigation
    $tabs = [
        ['id' => 'medical-profile', 'icon' => 'ri-heart-pulse-line', 'label' => 'Medical Profile'],
        ['id' => 'emergency-contacts', 'icon' => 'ri-contacts-line', 'label' => 'Emergency Contacts'],
        ['id' => 'medication-reminders', 'icon' => 'ri-alarm-line', 'label' => 'Medication Reminders'],
    ];
    
    // Use existing profile data OR auto-populate from session (registration data)
    $personalInfo = [
        'firstName' => ($existingProfile && isset($existingProfile['first_name'])) ? $existingProfile['first_name'] : ($_SESSION['user_fname'] ?? ''),
        'lastName' => ($existingProfile && isset($existingProfile['last_name'])) ? $existingProfile['last_name'] : ($_SESSION['user_lname'] ?? ''),
        'dateOfBirth' => ($existingProfile && isset($existingProfile['date_of_birth'])) ? $existingProfile['date_of_birth'] : '',
        'gender' => ($existingProfile && isset($existingProfile['gender'])) ? $existingProfile['gender'] : '',
        'pwdId' => ($existingProfile && isset($existingProfile['pwd_id'])) ? $existingProfile['pwd_id'] : '',
        'phone' => ($existingProfile && isset($existingProfile['phone'])) ? $existingProfile['phone'] : ($_SESSION['user_phone'] ?? ''),
        'email' => ($existingProfile && isset($existingProfile['email'])) ? $existingProfile['email'] : ($_SESSION['user_email'] ?? ''),
        'streetAddress' => ($existingProfile && isset($existingProfile['street_address'])) ? $existingProfile['street_address'] : '',
        'city' => ($existingProfile && isset($existingProfile['city'])) ? $existingProfile['city'] : '',
        'province' => ($existingProfile && isset($existingProfile['province'])) ? $existingProfile['province'] : '',
        'zipCode' => ($existingProfile && isset($existingProfile['zip_code'])) ? $existingProfile['zip_code'] : '',
    ];
    
    // Disability Status - auto-set based on user role
    $userRole = $_SESSION['user_role'] ?? '';
    $disabilityType = ($existingProfile && isset($existingProfile['disability_type'])) ? $existingProfile['disability_type'] : '';
    
    // If no disability type set yet and user is PWD, set default
    if (empty($disabilityType) && $userRole === 'pwd') {
        $disabilityType = 'Deaf/Mute'; // Default, user can change later
    }
    
    $disabilityStatus = [
        'primary' => $disabilityType,
        'verified' => !empty($disabilityType),
    ];
    
    // Medical data - properly check if existingProfile exists
    $allergies = ($existingProfile && isset($existingProfile['allergies'])) ? $existingProfile['allergies'] : ['Penicillin', 'Peanuts'];
    $medications = ($existingProfile && isset($existingProfile['medications'])) ? $existingProfile['medications'] : ['Lisinopril 10mg', 'Metformin 500mg'];
    $medicalConditions = ($existingProfile && isset($existingProfile['medical_conditions'])) ? $existingProfile['medical_conditions'] : ['Hypertension', 'Diabetes Type 2'];
    $bloodType = ($existingProfile && isset($existingProfile['blood_type'])) ? $existingProfile['blood_type'] : 'O+';
    
    // Emergency Contacts
    $emergencyContacts = ($existingProfile && isset($existingProfile['emergency_contacts'])) ? $existingProfile['emergency_contacts'] : 
    [
        [
            'name' => 'Maria Santos',
            'relation' => 'Mother',
            'phone' => '+639123456789',
            'initials' => 'MS',
            'color' => '#4caf50',
        ],
        [
            'name' => 'Jose Santos',
            'relation' => 'Father',
            'phone' => '+639234567890',
            'initials' => 'JS',
            'color' => '#ffc107',
        ],
        [
            'name' => 'Dr. Cruz',
            'relation' => 'Family Doctor',
            'phone' => '+639345678901',
            'initials' => 'DC',
            'color' => '#2196f3',
        ],
    ];
    
    // SMS Configuration
    $smsConfig = [
        'name' => trim(($personalInfo['firstName'] ?? '') . ' ' . ($personalInfo['lastName'] ?? '')),
        'pwdId' => $personalInfo['pwdId'] ?? '',
        'phone' => $personalInfo['phone'] ?? '',
        'address' => trim(($personalInfo['streetAddress'] ?? '') . ', ' . ($personalInfo['city'] ?? '')),
        'status' => 'Emergency SOS Activated',
        'bloodType' => $bloodType,
        'allergies' => !empty($allergies) ? implode(', ', $allergies) : 'None',
        'medications' => !empty($medications) ? implode(', ', $medications) : 'None',
    ];
    
    // Medication Reminders
    $medicationReminders = ($existingProfile && isset($existingProfile['medication_reminders'])) ? $existingProfile['medication_reminders'] : 
    [
        [
            'name' => 'Lisinopril 10mg',
            'frequency' => 'Daily reminder',
            'time' => '8:00 AM, 8:00 PM',
            'color' => '#4caf50',
        ],
        [
            'name' => 'Metformin 500mg',
            'frequency' => 'Daily reminder',
            'time' => '9:00 AM, 6:00 PM',
            'color' => '#2196f3',
        ],
    ];
    
    // Reminder Features
    $reminderFeatures = [
        'Full-screen visual alerts',
        'Strong vibration pattern',
        'LED flasher alert flash',
        'Customizable reminder times',
    ];
    
    // Check if this is first time visiting (no profile exists)
    $isFirstVisit = empty($existingProfile);
    
    require_once VIEW_PATH . 'medical-profile.php';
}

    /**
     * Save medical profile via AJAX
     */
    public function saveMedicalProfile()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        try {
            // Get JSON data from request
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (!$data) {
                throw new Exception('Invalid data received');
            }

            // Get user ID
            $userId = $_SESSION['user_id'];

            // Load model
            require_once MODEL_PATH . 'MedicalProfile.php';
            $medicalProfileModel = new MedicalProfile();

            // Prepare data for saving
            $profileData = [
                'first_name' => $data['personalInfo']['firstName'] ?? '',
                'last_name' => $data['personalInfo']['lastName'] ?? '',
                'date_of_birth' => $data['personalInfo']['dateOfBirth'] ?? '',
                'gender' => $data['personalInfo']['gender'] ?? '',
                'pwd_id' => $data['personalInfo']['pwdId'] ?? '',
                'phone' => $data['personalInfo']['phone'] ?? '',
                'email' => $data['personalInfo']['email'] ?? '',
                'street_address' => $data['personalInfo']['streetAddress'] ?? '',
                'city' => $data['personalInfo']['city'] ?? '',
                'province' => $data['personalInfo']['province'] ?? '',
                'zip_code' => $data['personalInfo']['zipCode'] ?? '',
                'disability_type' => $data['disabilityType'] ?? '',
                'blood_type' => $data['bloodType'] ?? '',
                'allergies' => $data['allergies'] ?? [],
                'medications' => $data['medications'] ?? [],
                'medical_conditions' => $data['conditions'] ?? [],
                'emergency_contacts' => $data['contacts'] ?? [],
                'sms_template' => $data['smsTemplate'] ?? '',
                'medication_reminders' => $data['medicationReminders'] ?? [],
            ];

            // Save to database
            $success = $medicalProfileModel->saveProfile($userId, $profileData);

            if ($success) {
                // Also save to session for quick access
                $_SESSION['medical_profile'] = $profileData;

                echo json_encode([
                    'success' => true,
                    'message' => 'Medical profile saved successfully'
                ]);
            } else {
                throw new Exception('Failed to save profile');
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
}
