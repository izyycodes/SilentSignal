<?php
// controllers/AdminController.php

class AdminController {

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
            ['action' => 'admin-dashboard', 'icon' => 'ri-dashboard-line', 'label' => 'Home'],
            ['action' => 'admin-users', 'icon' => 'ri-user-settings-line', 'label' => 'Users'],
            ['action' => 'admin-emergency-alerts', 'icon' => 'ri-alarm-warning-line', 'label' => 'Emergency Alerts'],
            ['action' => 'admin-disaster-alerts', 'icon' => 'ri-earth-line', 'label' => 'Disaster Alerts'],
            ['action' => 'admin-messages', 'icon' => 'ri-message-3-line', 'label' => 'Messages'],
        ];

        // User dropdown menu items
        $this->userMenuItems = [
            ['action' => 'medical-profile', 'icon' => 'ri-heart-pulse-line', 'label' => 'Medical Profile'],
        ];

        // Footer quick links
        $this->footerLinks = [
            ['label' => 'Home', 'action' => 'admin-dashboard'],
            ['label' => 'Users', 'action' => 'admin-users'],
            ['label' => 'Emergency Alerts', 'action' => 'admin-emergency-alerts'],
            ['label' => 'Disaster Alerts', 'action' => 'admin-disaster-alerts'],
            ['label' => 'Messages', 'action' => 'admin-messages'],
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
     * Get current user data
     */
    private function getCurrentUser() {
        return [
            'id' => $_SESSION['user_id'] ?? 0,
            'name' => $_SESSION['user_name'] ?? 'Admin',
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'] ?? 'admin',
            'initials' => $_SESSION['user_initials'] ?? 'A'
        ];
    }

    /**
     * Get common admin view data
     */
    private function getCommonViewData() {
        return [
            'currentUser' => $this->getCurrentUser(),
            'currentAction' => $_GET['action'] ?? 'admin-dashboard'
        ];
    }

    /**
     * Check if user is admin
     */
    private function requireAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = "Access denied. Admin privileges required.";
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
    }
    
    // ==================== DASHBOARD ====================
    
    /**
     * Admin Dashboard (Overview)
     */
    public function dashboard() {
        $this->requireAdmin();
        $pageTitle = "Admin Dashboard - Silent Signal";
        
        extract($this->getCommonViewData());

        // Shared header/footer data
        extract($this->getSharedData());
        
        // Mock statistics
        $stats = [
            'totalUsers' => 1247,
            'userGrowth' => 12,
            'activeAlerts' => 8,
            'resolvedToday' => 15,
            'disasterAlerts' => 3,
            'activeDisasters' => 1,
            'messageInquiries' => 42,
            'pendingMessages' => 12
        ];
        
        // Mock recent activity
        $recentActivity = [
            [
                'type' => 'alert',
                'icon' => 'ri-alarm-warning-line',
                'title' => 'New Emergency Alert',
                'description' => 'Juan Dela Cruz triggered an emergency alert in Bacolod City',
                'time' => '5 minutes ago'
            ],
            [
                'type' => 'user',
                'icon' => 'ri-user-add-line',
                'title' => 'New User Registration',
                'description' => 'Maria Santos registered as PWD user',
                'time' => '15 minutes ago'
            ],
            [
                'type' => 'disaster',
                'icon' => 'ri-flood-line',
                'title' => 'Disaster Alert Updated',
                'description' => 'Flood warning updated for Negros Occidental',
                'time' => '1 hour ago'
            ],
            [
                'type' => 'message',
                'icon' => 'ri-message-3-line',
                'title' => 'New Message Inquiry',
                'description' => 'User reported issue with SMS notifications',
                'time' => '2 hours ago'
            ]
        ];
        
        require_once VIEW_PATH . 'admin-dashboard.php';
    }

    // ==================== USERS MANAGEMENT ====================
    
    /**
     * Users Management Page
     */
    public function users() {
        $this->requireAdmin();
        $pageTitle = "User Management - Admin - Silent Signal";
        
        extract($this->getCommonViewData());

        // Shared header/footer data
        extract($this->getSharedData());
        
        // Mock users data
        $users = [
            [
                'id' => 1,
                'name' => 'Juan Dela Cruz',
                'email' => 'juan@example.com',
                'phone' => '+63 912 345 6789',
                'role' => 'pwd',
                'status' => 'active',
                'disability' => 'Deaf/Mute',
                'created_at' => '2024-01-15',
                'last_login' => '2 hours ago'
            ],
            [
                'id' => 2,
                'name' => 'Maria Santos',
                'email' => 'maria@example.com',
                'phone' => '+63 923 456 7890',
                'role' => 'pwd',
                'status' => 'active',
                'disability' => 'Blind',
                'created_at' => '2024-01-20',
                'last_login' => '1 day ago'
            ],
            [
                'id' => 3,
                'name' => 'Pedro Garcia',
                'email' => 'pedro@example.com',
                'phone' => '+63 934 567 8901',
                'role' => 'family',
                'status' => 'active',
                'disability' => 'N/A',
                'created_at' => '2024-02-01',
                'last_login' => '3 hours ago'
            ],
            [
                'id' => 4,
                'name' => 'Ana Reyes',
                'email' => 'ana@example.com',
                'phone' => '+63 945 678 9012',
                'role' => 'responder',
                'status' => 'active',
                'disability' => 'N/A',
                'created_at' => '2024-02-05',
                'last_login' => 'Online'
            ],
            [
                'id' => 5,
                'name' => 'Carlos Lopez',
                'email' => 'carlos@example.com',
                'phone' => '+63 956 789 0123',
                'role' => 'pwd',
                'status' => 'inactive',
                'disability' => 'Mobility Impaired',
                'created_at' => '2023-12-10',
                'last_login' => '1 week ago'
            ]
        ];
        
        $stats = [
            'total' => 1247,
            'pwd' => 856,
            'family' => 298,
            'responders' => 93,
            'active' => 1189,
            'newThisMonth' => 47
        ];
        
        require_once VIEW_PATH . 'admin/users.php';
    }

    // ==================== EMERGENCY ALERTS ====================
    
    /**
     * Emergency Alerts Page
     */
    public function emergencyAlerts() {
        $this->requireAdmin();
        $pageTitle = "Emergency Alerts - Admin - Silent Signal";
        
        extract($this->getCommonViewData());

        // Shared header/footer data
        extract($this->getSharedData());
        
        // Mock emergency alerts
        $alerts = [
            [
                'id' => 1,
                'user_name' => 'Juan Dela Cruz',
                'user_phone' => '+63 912 345 6789',
                'alert_type' => 'Medical Emergency',
                'priority' => 'critical',
                'status' => 'active',
                'location' => 'Bacolod City Central',
                'latitude' => 10.6764,
                'longitude' => 122.9489,
                'description' => 'Severe chest pain, difficulty breathing',
                'created_at' => '2024-02-08 14:23:00',
                'time_ago' => '5 minutes ago'
            ],
            [
                'id' => 2,
                'user_name' => 'Maria Santos',
                'user_phone' => '+63 923 456 7890',
                'alert_type' => 'Lost/Disoriented',
                'priority' => 'high',
                'status' => 'responded',
                'location' => 'SM City Bacolod',
                'latitude' => 10.6658,
                'longitude' => 122.9503,
                'description' => 'Lost in shopping mall, unable to find way out',
                'responder_name' => 'Ana Reyes',
                'responded_at' => '2024-02-08 13:45:00',
                'created_at' => '2024-02-08 13:30:00',
                'time_ago' => '58 minutes ago'
            ],
            [
                'id' => 3,
                'user_name' => 'Carlos Lopez',
                'user_phone' => '+63 956 789 0123',
                'alert_type' => 'Fall/Injury',
                'priority' => 'medium',
                'status' => 'resolved',
                'location' => 'Lacson Street',
                'latitude' => 10.6738,
                'longitude' => 122.9523,
                'description' => 'Fell on stairs, minor injury',
                'responder_name' => 'Pedro Garcia',
                'responded_at' => '2024-02-08 12:10:00',
                'resolved_at' => '2024-02-08 12:45:00',
                'created_at' => '2024-02-08 12:00:00',
                'time_ago' => '2 hours ago'
            ],
            [
                'id' => 4,
                'user_name' => 'Lisa Fernandez',
                'user_phone' => '+63 967 890 1234',
                'alert_type' => 'Need Assistance',
                'priority' => 'low',
                'status' => 'active',
                'location' => 'Bacolod Public Plaza',
                'latitude' => 10.6735,
                'longitude' => 122.9545,
                'description' => 'Need help crossing the street',
                'created_at' => '2024-02-08 14:00:00',
                'time_ago' => '28 minutes ago'
            ]
        ];
        
        $stats = [
            'total' => 342,
            'active' => 8,
            'responded' => 15,
            'resolved' => 319,
            'critical' => 2,
            'avgResponseTime' => '8 min'
        ];
        
        require_once VIEW_PATH . 'admin/emergency-alerts.php';
    }

    // ==================== DISASTER ALERTS ====================
    
    /**
     * Disaster Alerts Page
     */
    public function disasterAlerts() {
        $this->requireAdmin();
        $pageTitle = "Disaster Alerts - Admin - Silent Signal";
        
        extract($this->getCommonViewData());

        // Shared header/footer data
        extract($this->getSharedData());
        
        // Mock disaster alerts
        $alerts = [
            [
                'id' => 1,
                'disaster_type' => 'Flood',
                'severity' => 'warning',
                'title' => 'Flood Warning - Negros Occidental',
                'description' => 'Heavy rainfall expected to cause flooding in low-lying areas. Water levels rising in major rivers.',
                'affected_areas' => 'Bacolod City, Talisay City, Silay City, Bago City',
                'instructions' => 'Stay alert. Prepare evacuation kits. Avoid flood-prone areas. Monitor local news.',
                'status' => 'active',
                'created_by' => 'Admin User',
                'created_at' => '2024-02-08 10:00:00',
                'expires_at' => '2024-02-09 18:00:00',
                'time_ago' => '4 hours ago'
            ],
            [
                'id' => 2,
                'disaster_type' => 'Typhoon',
                'severity' => 'watch',
                'title' => 'Typhoon Watch - Western Visayas',
                'description' => 'Tropical storm developing into typhoon, expected to affect Western Visayas region within 48 hours.',
                'affected_areas' => 'Entire Negros Occidental, Iloilo, Aklan, Antique',
                'instructions' => 'Secure loose objects. Stock emergency supplies. Charge devices. Stay tuned for updates.',
                'status' => 'active',
                'created_by' => 'Admin User',
                'created_at' => '2024-02-08 08:00:00',
                'expires_at' => '2024-02-10 20:00:00',
                'time_ago' => '6 hours ago'
            ],
            [
                'id' => 3,
                'disaster_type' => 'Earthquake',
                'severity' => 'advisory',
                'title' => 'Post-Earthquake Advisory',
                'description' => 'Magnitude 4.2 earthquake recorded. No damage reported. Aftershocks possible.',
                'affected_areas' => 'Bacolod City and nearby municipalities',
                'instructions' => 'Stay calm. Be prepared for possible aftershocks. Check for structural damage.',
                'status' => 'active',
                'created_by' => 'Admin User',
                'created_at' => '2024-02-07 22:30:00',
                'expires_at' => '2024-02-08 22:30:00',
                'time_ago' => '16 hours ago'
            ],
            [
                'id' => 4,
                'disaster_type' => 'Fire',
                'severity' => 'critical',
                'title' => 'Major Fire Incident - Downtown Bacolod',
                'description' => 'Large fire affecting commercial area. Multiple buildings involved. Firefighters on scene.',
                'affected_areas' => 'Downtown Bacolod - Lacson Street area',
                'instructions' => 'EVACUATE IMMEDIATELY if in affected area. Avoid the downtown area. Follow emergency personnel instructions.',
                'status' => 'expired',
                'created_by' => 'Emergency Admin',
                'created_at' => '2024-02-06 15:20:00',
                'expires_at' => '2024-02-06 20:00:00',
                'resolved_at' => '2024-02-06 19:30:00',
                'time_ago' => '2 days ago'
            ]
        ];
        
        $stats = [
            'total' => 156,
            'active' => 3,
            'expired' => 153,
            'critical' => 1,
            'warnings' => 1,
            'watches' => 1
        ];
        
        require_once VIEW_PATH . 'admin/disaster-alerts.php';
    }

    // ==================== MESSAGE INQUIRIES ====================
    
    /**
     * Message Inquiries Page
     */
    public function messages() {
        $this->requireAdmin();
        $pageTitle = "Message Inquiries - Admin - Silent Signal";
        
        extract($this->getCommonViewData());

        // Shared header/footer data
        extract($this->getSharedData());
        
        // Mock messages
        $messages = [
            [
                'id' => 1,
                'user_name' => 'Juan Dela Cruz',
                'user_email' => 'juan@example.com',
                'subject' => 'SMS Notifications Not Working',
                'message' => 'I am not receiving SMS alerts on my phone. Can you please check my account settings?',
                'category' => 'Technical Support',
                'priority' => 'high',
                'status' => 'pending',
                'created_at' => '2024-02-08 13:45:00',
                'time_ago' => '45 minutes ago'
            ],
            [
                'id' => 2,
                'user_name' => 'Maria Santos',
                'user_email' => 'maria@example.com',
                'subject' => 'How to Add Family Members?',
                'message' => 'I would like to add my daughter as an emergency contact. How do I do this through the app?',
                'category' => 'General Inquiry',
                'priority' => 'medium',
                'status' => 'in_progress',
                'admin_response' => 'Thank you for contacting us. You can add family members by going to Settings > Emergency Contacts...',
                'responded_by' => 'Admin User',
                'responded_at' => '2024-02-08 12:30:00',
                'created_at' => '2024-02-08 11:00:00',
                'time_ago' => '3 hours ago'
            ],
            [
                'id' => 3,
                'user_name' => 'Pedro Garcia',
                'user_email' => 'pedro@example.com',
                'subject' => 'Feature Request: Voice Commands',
                'message' => 'It would be great to have voice command support for people with limited mobility.',
                'category' => 'Feature Request',
                'priority' => 'low',
                'status' => 'resolved',
                'admin_response' => 'Thank you for your suggestion! We have added this to our feature roadmap for future updates.',
                'responded_by' => 'Admin User',
                'responded_at' => '2024-02-07 16:20:00',
                'created_at' => '2024-02-07 14:30:00',
                'time_ago' => '1 day ago'
            ],
            [
                'id' => 4,
                'user_name' => 'Ana Reyes',
                'user_email' => 'ana@example.com',
                'subject' => 'Cannot Update Medical Profile',
                'message' => 'Getting an error when trying to save my medical information. Please help!',
                'category' => 'Technical Support',
                'priority' => 'high',
                'status' => 'pending',
                'created_at' => '2024-02-08 14:10:00',
                'time_ago' => '18 minutes ago'
            ],
            [
                'id' => 5,
                'user_name' => 'Carlos Lopez',
                'user_email' => 'carlos@example.com',
                'subject' => 'Thank You!',
                'message' => 'I want to thank the team for this wonderful app. It has given me peace of mind.',
                'category' => 'Feedback',
                'priority' => 'low',
                'status' => 'resolved',
                'admin_response' => 'Thank you so much for your kind words! We are happy to help.',
                'responded_by' => 'Admin User',
                'responded_at' => '2024-02-08 10:00:00',
                'created_at' => '2024-02-08 09:30:00',
                'time_ago' => '5 hours ago'
            ]
        ];
        
        $stats = [
            'total' => 342,
            'pending' => 12,
            'in_progress' => 8,
            'resolved' => 322,
            'high_priority' => 5,
            'avgResponseTime' => '2.5 hours'
        ];
        
        require_once VIEW_PATH . 'admin/messages.php';
    }
}
?>