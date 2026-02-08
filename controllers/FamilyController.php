<?php 

class FamilyController {
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
            ['action' => 'family-dashboard', 'icon' => 'ri-home-line', 'label' => 'Home'],
            ['action' => 'family-dashboard#pwdMembers', 'icon' => 'ri-team-line', 'label' => 'PWD Members'],
            ['action' => 'family-dashboard#responseStatus', 'icon' => 'ri-alarm-warning-line', 'label' => 'Response Status'],
            ['action' => 'family-dashboard#recentEmergencyAlerts', 'icon' => 'ri-alert-line', 'label' => 'Recent Emergency Alerts'],
        ];

        // User dropdown menu items
        $this->userMenuItems = [
            ['action' => 'medical-profile', 'icon' => 'ri-heart-pulse-line', 'label' => 'Medical Profile'],
        ];

        // Footer quick links
        $this->footerLinks = [
            ['label' => 'Home', 'action' => 'family-dashboard'],
            ['label' => 'PWD Members', 'action' => 'family-dashboard#pwdMembers'],
            ['label' => 'Response Status', 'action' => 'family-dashboard#responseStatus'],
            ['label' => 'Recent Emergency Alerts', 'action' => 'family-dashboard#recentEmergencyAlerts'],
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
     * Family Member Dashboard
     */
    public function familyDashboard()
    {
        $this->requireLogin();

        // Check if user is a family member
        if ($_SESSION['user_role'] !== 'family') {
            $_SESSION['error'] = "Access denied. This page is only for family members.";
            header("Location: " . BASE_URL . "index.php?action=dashboard");
            exit();
        }

        $pageTitle = "Family Dashboard - Silent Signal";

        // Shared header/footer data
        extract($this->getSharedData());

        $familyMemberId = $_SESSION['user_id'];

        // Get PWD members this family member is responsible for
        // In a real scenario, this would come from database
        $pwdMembers = [
            [
                'id' => 3,
                'name' => 'Juan Santos',
                'photo' => 'JS',
                'relationship' => 'Son',
                'status' => 'safe',
                'statusLabel' => 'SAFE',
                'lastUpdated' => '5 minutes ago',
                'location' => 'Bacolod City Public Plaza',
                'battery' => 85,
                'latitude' => 10.6780,
                'longitude' => 122.9506,
                'disability' => 'Deaf/Mute',
                'age' => 28,
                'bloodType' => 'O+',
                'emergencyContacts' => 3,
            ],
        ];

        // Get other family members responsible for the same PWD
        $otherFamilyMembers = [
            [
                'id' => 4,
                'name' => 'Maria Santos',
                'initials' => 'MS',
                'relationship' => 'Mother',
                'phone' => '+639123456789',
                'status' => 'responded',
                'responseTime' => '2 min ago',
                'lastSeen' => '10 minutes ago',
                'color' => '#4caf50',
            ],
            [
                'id' => 5,
                'name' => 'Jose Santos',
                'initials' => 'JS',
                'relationship' => 'Father',
                'phone' => '+639234567890',
                'status' => 'responded',
                'responseTime' => '5 min ago',
                'lastSeen' => '15 minutes ago',
                'color' => '#2196f3',
            ],
            [
                'id' => 6,
                'name' => 'Ana Santos',
                'initials' => 'AS',
                'relationship' => 'Sister',
                'phone' => '+639345678901',
                'status' => 'pending',
                'responseTime' => 'Not responded',
                'lastSeen' => '1 hour ago',
                'color' => '#ffc107',
            ],
        ];

        // Recent emergency alerts
        $recentAlerts = [
            [
                'id' => 1,
                'type' => 'sos',
                'icon' => 'ri-alarm-warning-line',
                'iconBg' => '#ffebee',
                'iconColor' => '#e53935',
                'title' => 'SOS Alert Triggered',
                'description' => 'Juan activated emergency SOS',
                'location' => 'SM City Bacolod',
                'time' => '2 hours ago',
                'status' => 'resolved',
                'statusLabel' => 'Resolved',
                'statusClass' => 'resolved',
                'respondedBy' => 'Maria Santos',
            ],
            [
                'id' => 2,
                'type' => 'assistance',
                'icon' => 'ri-hand-heart-line',
                'iconBg' => '#fff3e0',
                'iconColor' => '#ef6c00',
                'title' => 'Assistance Needed',
                'description' => 'Juan requested help at location',
                'location' => 'Bacolod Public Plaza',
                'time' => '1 day ago',
                'status' => 'resolved',
                'statusLabel' => 'Resolved',
                'statusClass' => 'resolved',
                'respondedBy' => 'Jose Santos',
            ],
        ];

        // Quick stats
        $quickStats = [
            [
                'label' => 'PWD Members',
                'value' => '1',
                'icon' => 'ri-user-heart-line',
                'color' => '#1976d2',
            ],
            [
                'label' => 'Family Contacts',
                'value' => '3',
                'icon' => 'ri-team-line',
                'color' => '#43a047',
            ],
            [
                'label' => 'Emergency Alerts',
                'value' => '2',
                'icon' => 'ri-error-warning-line',
                'color' => '#ef6c00',
            ],
            [
                'label' => 'All Safe',
                'value' => '100%',
                'icon' => 'ri-shield-check-line',
                'color' => '#4caf50',
            ],
        ];

        require_once VIEW_PATH . 'family-dashboard.php';
    }
}