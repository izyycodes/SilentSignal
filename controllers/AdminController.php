<?php
// controllers/AdminController.php

require_once __DIR__ . '/../config/Database.php';

class AdminController {

    // Database connection
    private $db;

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
        $this->initDatabase();
    }

    /**
     * Initialize database connection
     */
    private function initDatabase()
    {
        try {
            $database = new Database();
            $this->db = $database->getConnection();
        } catch (Exception $e) {
            error_log("Database connection failed: " . $e->getMessage());
        }
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
        extract($this->getSharedData());
        
        // Get REAL statistics from database
        $stats = $this->getDashboardStats();
        
        // Get REAL recent activity
        $recentActivity = $this->getRecentActivity();
        
        require_once VIEW_PATH . 'admin-dashboard.php';
    }

    /**
     * Get dashboard statistics from database
     */
    private function getDashboardStats() {
        $stats = [
            'totalUsers' => 0,
            'userGrowth' => 0,
            'activeAlerts' => 0,
            'resolvedToday' => 0,
            'disasterAlerts' => 0,
            'activeDisasters' => 0,
            'messageInquiries' => 0,
            'pendingMessages' => 0
        ];
        
        try {
            // Total PWD users
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'pwd'");
            $stats['totalUsers'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Active emergency alerts (last 24 hours, not resolved)
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM emergency_alerts 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                AND (status IS NULL OR status != 'resolved')
            ");
            $stats['activeAlerts'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Resolved today
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM emergency_alerts 
                WHERE DATE(created_at) = CURDATE() AND status = 'resolved'
            ");
            $stats['resolvedToday'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Disaster responses today
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM disaster_alerts 
                WHERE DATE(created_at) = CURDATE()
            ");
            $stats['disasterAlerts'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Active disasters (users needing help)
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM disaster_alerts 
                WHERE status = 'sos' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ");
            $stats['activeDisasters'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Message inquiries (if table exists)
            try {
                $stmt = $this->db->query("SELECT COUNT(*) as count FROM contact_inquiries");
                $stats['messageInquiries'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                
                $stmt = $this->db->query("SELECT COUNT(*) as count FROM contact_inquiries WHERE status = 'pending'");
                $stats['pendingMessages'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            } catch (Exception $e) {
                // Table might not exist
            }
            
        } catch (Exception $e) {
            error_log("Dashboard stats error: " . $e->getMessage());
        }
        
        return $stats;
    }

    /**
     * Get recent activity from database
     */
    private function getRecentActivity() {
        $activity = [];
        
        try {
            // Get recent emergency alerts
            $stmt = $this->db->query("
                SELECT ea.*, u.fname, u.lname, mp.city
                FROM emergency_alerts ea
                LEFT JOIN users u ON ea.user_id = u.id
                LEFT JOIN medical_profiles mp ON ea.user_id = mp.user_id
                ORDER BY ea.created_at DESC
                LIMIT 5
            ");
            $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($alerts as $alert) {
                $activity[] = [
                    'type' => 'alert',
                    'icon' => 'ri-alarm-warning-line',
                    'title' => 'Emergency Alert - ' . strtoupper($alert['alert_type'] ?? 'SOS'),
                    'description' => ($alert['fname'] ?? 'User') . ' ' . ($alert['lname'] ?? '') . ' triggered an alert' . 
                                   ($alert['city'] ? ' in ' . $alert['city'] : ''),
                    'time' => $this->timeAgo($alert['created_at']),
                    'data' => $alert
                ];
            }
            
            // Get recent disaster responses
            $stmt = $this->db->query("
                SELECT da.*, u.fname, u.lname
                FROM disaster_alerts da
                LEFT JOIN users u ON da.user_id = u.id
                ORDER BY da.created_at DESC
                LIMIT 5
            ");
            $disasters = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($disasters as $disaster) {
                $icon = $disaster['status'] === 'safe' ? 'ri-shield-check-line' : 'ri-alarm-warning-line';
                $activity[] = [
                    'type' => 'disaster',
                    'icon' => $icon,
                    'title' => 'Disaster Response - ' . strtoupper($disaster['status'] ?? 'Unknown'),
                    'description' => ($disaster['fname'] ?? 'User') . ' ' . ($disaster['lname'] ?? '') . 
                                   ' responded to ' . ($disaster['alert_name'] ?? 'disaster alert'),
                    'time' => $this->timeAgo($disaster['created_at']),
                    'data' => $disaster
                ];
            }
            
            // Sort by time (most recent first)
            usort($activity, function($a, $b) {
                return strtotime($b['data']['created_at'] ?? 'now') - strtotime($a['data']['created_at'] ?? 'now');
            });
            
            // Return only top 10
            return array_slice($activity, 0, 10);
            
        } catch (Exception $e) {
            error_log("Recent activity error: " . $e->getMessage());
        }
        
        return $activity;
    }

    // ==================== USERS MANAGEMENT ====================
    
    /**
     * Users Management Page
     */
    public function users() {
        $this->requireAdmin();
        $pageTitle = "User Management - Admin - Silent Signal";
        
        extract($this->getCommonViewData());
        extract($this->getSharedData());
        
        // Get REAL users from database
        $users = $this->getAllUsers();
        $stats = $this->getUserStats();
        
        require_once VIEW_PATH . 'admin-users.php';
    }

    /**
     * Get all users from database
     */
    private function getAllUsers() {
        $users = [];
        
        try {
            $stmt = $this->db->query("
                SELECT u.*, mp.pwd_id, mp.disability_type, mp.city, mp.street_address
                FROM users u
                LEFT JOIN medical_profiles mp ON u.id = mp.user_id
                ORDER BY u.created_at DESC
            ");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $row) {
                $users[] = [
                    'id' => $row['id'],
                    'name' => $row['fname'] . ' ' . $row['lname'],
                    'email' => $row['email'],
                    'phone' => $row['phone_number'],
                    'role' => $row['role'],
                    'pwd_id' => $row['pwd_id'] ?? 'N/A',
                    'disability' => $row['disability_type'] ?? 'N/A',
                    'location' => ($row['city'] ?? '') . ', Philippines',
                    'registration_date' => date('M j, Y', strtotime($row['created_at'])),
                    'status' => $row['pwd_id'] ? 'verified' : 'pending'
                ];
            }
        } catch (Exception $e) {
            error_log("Get users error: " . $e->getMessage());
        }
        
        return $users;
    }

    /**
     * Get user statistics
     */
    private function getUserStats() {
        $stats = [
            'total' => 0,
            'verified' => 0,
            'pending' => 0,
            'inactive' => 0
        ];
        
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
            $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM users u
                INNER JOIN medical_profiles mp ON u.id = mp.user_id
                WHERE mp.pwd_id IS NOT NULL AND mp.pwd_id != ''
            ");
            $stats['verified'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            $stats['pending'] = $stats['total'] - $stats['verified'];
            
        } catch (Exception $e) {
            error_log("User stats error: " . $e->getMessage());
        }
        
        return $stats;
    }

    // ==================== EMERGENCY ALERTS ====================
    
    /**
     * Emergency Alerts Page
     */
    public function emergencyAlerts() {
        $this->requireAdmin();
        $pageTitle = "Emergency Alerts - Admin - Silent Signal";
        
        extract($this->getCommonViewData());
        extract($this->getSharedData());
        
        // Get REAL emergency alerts from database
        $alerts = $this->getAllEmergencyAlerts();
        $stats = $this->getEmergencyAlertStats();
        
        require_once VIEW_PATH . 'admin-emergency-alerts.php';
    }

    /**
     * Get all emergency alerts from database
     */
    private function getAllEmergencyAlerts() {
        $alerts = [];
        
        try {
            $stmt = $this->db->query("
                SELECT ea.*, u.fname, u.lname, u.phone_number, u.email,
                       mp.pwd_id, mp.blood_type, mp.disability_type, mp.city,
                       mp.allergies, mp.medications, mp.emergency_contacts
                FROM emergency_alerts ea
                LEFT JOIN users u ON ea.user_id = u.id
                LEFT JOIN medical_profiles mp ON ea.user_id = mp.user_id
                ORDER BY ea.created_at DESC
                LIMIT 100
            ");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $row) {
                $alertType = $row['alert_type'] ?? 'sos';
                $priority = 'high';
                if ($alertType === 'sos') $priority = 'critical';
                elseif ($alertType === 'shake') $priority = 'high';
                elseif ($alertType === 'panic') $priority = 'critical';
                
                $alerts[] = [
                    'id' => $row['id'],
                    'alert_id' => '#' . strtoupper(substr($alertType, 0, 3)) . '-' . $row['id'],
                    'user_name' => ($row['fname'] ?? 'Unknown') . ' ' . ($row['lname'] ?? ''),
                    'user_id' => $row['pwd_id'] ?? $row['user_id'],
                    'phone' => $row['phone_number'],
                    'email' => $row['email'],
                    'alert_type' => ucfirst($alertType) . ' Alert',
                    'priority' => $priority,
                    'location' => $row['city'] ?? 'Unknown Location',
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude'],
                    'time' => $this->timeAgo($row['created_at']),
                    'created_at' => $row['created_at'],
                    'response_time' => '-',
                    'status' => $row['status'] ?? 'active',
                    'message' => $row['message'],
                    'blood_type' => $row['blood_type'],
                    'disability_type' => $row['disability_type'],
                    'allergies' => $row['allergies'],
                    'medications' => $row['medications'],
                    'emergency_contacts' => $row['emergency_contacts']
                ];
            }
        } catch (Exception $e) {
            error_log("Get emergency alerts error: " . $e->getMessage());
        }
        
        return $alerts;
    }

    /**
     * Get emergency alert statistics
     */
    private function getEmergencyAlertStats() {
        $stats = [
            'total_today' => 0,
            'critical' => 0,
            'active' => 0,
            'resolved_today' => 0
        ];
        
        try {
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM emergency_alerts 
                WHERE DATE(created_at) = CURDATE()
            ");
            $stats['total_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM emergency_alerts 
                WHERE alert_type IN ('sos', 'panic') 
                AND (status IS NULL OR status = 'active')
            ");
            $stats['critical'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM emergency_alerts 
                WHERE status IS NULL OR status = 'active'
            ");
            $stats['active'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM emergency_alerts 
                WHERE status = 'resolved' AND DATE(created_at) = CURDATE()
            ");
            $stats['resolved_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
        } catch (Exception $e) {
            error_log("Emergency alert stats error: " . $e->getMessage());
        }
        
        return $stats;
    }

    // ==================== DISASTER ALERTS ====================
    
    /**
     * Disaster Alerts Page
     */
    public function disasterAlerts() {
        $this->requireAdmin();
        $pageTitle = "Disaster Alerts - Admin - Silent Signal";
        
        extract($this->getCommonViewData());
        extract($this->getSharedData());
        
        // Get REAL disaster responses from database
        $alerts = $this->getAllDisasterAlerts();
        $stats = $this->getDisasterAlertStats();
        
        require_once VIEW_PATH . 'admin-disaster-alerts.php';
    }

    /**
     * Get all disaster alerts/responses from database
     */
    private function getAllDisasterAlerts() {
        $alerts = [];
        
        try {
            $stmt = $this->db->query("
                SELECT da.*, u.fname, u.lname, u.phone_number,
                       mp.pwd_id, mp.city, mp.disability_type
                FROM disaster_alerts da
                LEFT JOIN users u ON da.user_id = u.id
                LEFT JOIN medical_profiles mp ON da.user_id = mp.user_id
                ORDER BY da.created_at DESC
                LIMIT 100
            ");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $row) {
                $alertName = $row['alert_name'] ?? 'Weather Alert';
                $severity = 'moderate';
                $disasterType = 'Weather Alert';
                
                // Determine disaster type and severity from alert name
                $alertLower = strtolower($alertName);
                if (strpos($alertLower, 'typhoon') !== false) {
                    $disasterType = 'Typhoon';
                    $severity = 'extreme';
                } elseif (strpos($alertLower, 'earthquake') !== false) {
                    $disasterType = 'Earthquake';
                    $severity = 'severe';
                } elseif (strpos($alertLower, 'flood') !== false) {
                    $disasterType = 'Flood';
                    $severity = 'moderate';
                } elseif (strpos($alertLower, 'storm') !== false) {
                    $disasterType = 'Storm';
                    $severity = 'severe';
                } elseif (strpos($alertLower, 'wind') !== false) {
                    $disasterType = 'Strong Wind';
                    $severity = 'moderate';
                }
                
                // Status mapping
                $status = $row['status'] ?? 'unknown';
                if ($status === 'sos') $status = 'active';
                elseif ($status === 'safe') $status = 'cleared';
                
                $alerts[] = [
                    'id' => $row['id'],
                    'alert_id' => '#DIS-' . $row['id'],
                    'disaster_type' => $disasterType,
                    'alert_name' => $alertName,
                    'severity' => $severity,
                    'location' => $row['city'] ?? 'Philippines',
                    'user_name' => ($row['fname'] ?? '') . ' ' . ($row['lname'] ?? ''),
                    'user_phone' => $row['phone_number'],
                    'affected_users' => '1 user',
                    'magnitude' => '-',
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude'],
                    'issued_time' => $this->timeAgo($row['created_at']),
                    'created_at' => $row['created_at'],
                    'status' => $status,
                    'user_status' => $row['status']
                ];
            }
        } catch (Exception $e) {
            error_log("Get disaster alerts error: " . $e->getMessage());
        }
        
        return $alerts;
    }

    /**
     * Get disaster alert statistics
     */
    private function getDisasterAlertStats() {
        $stats = [
            'active_disasters' => 0,
            'typhoons' => 0,
            'earthquakes' => 0,
            'floods' => 0
        ];
        
        try {
            // Active (SOS responses)
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM disaster_alerts 
                WHERE status = 'sos'
            ");
            $stats['active_disasters'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Count by type
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM disaster_alerts 
                WHERE LOWER(alert_name) LIKE '%typhoon%' OR LOWER(alert_name) LIKE '%storm%'
            ");
            $stats['typhoons'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM disaster_alerts 
                WHERE LOWER(alert_name) LIKE '%earthquake%'
            ");
            $stats['earthquakes'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM disaster_alerts 
                WHERE LOWER(alert_name) LIKE '%flood%'
            ");
            $stats['floods'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
        } catch (Exception $e) {
            error_log("Disaster alert stats error: " . $e->getMessage());
        }
        
        return $stats;
    }

    // ==================== MESSAGE INQUIRIES ====================
    
    /**
     * Message Inquiries Page
     */
    public function messages() {
        $this->requireAdmin();
        $pageTitle = "Message Inquiries - Admin - Silent Signal";

        extract($this->getCommonViewData());
        extract($this->getSharedData());

        require_once MODEL_PATH . 'ContactInquiry.php';
        $contactInquiry = new ContactInquiry();

        $perPage     = 5;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $offset      = ($currentPage - 1) * $perPage;

        $stats       = $contactInquiry->getStats();
        $messages    = $contactInquiry->getAll($perPage, $offset);
        $totalPages  = (int)ceil($stats['total'] / $perPage);
        $rangeStart  = $offset + 1;
        $rangeEnd    = min($offset + $perPage, $stats['total']);

        require_once VIEW_PATH . 'admin-messages.php';
    }

    // ==================== API ENDPOINTS ====================

    /**
     * API: Get alerts for real-time updates (AJAX)
     */
    public function getAlertsAPI() {
        $this->requireAdmin();
        
        header('Content-Type: application/json');
        
        $alerts = $this->getAllEmergencyAlerts();
        $stats = $this->getDashboardStats();
        
        echo json_encode([
            'success' => true,
            'alerts' => array_slice($alerts, 0, 20),
            'stats' => $stats,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit();
    }

    /**
     * API: Update alert status (AJAX)
     */
    public function updateAlertStatus() {
        $this->requireAdmin();
        
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['alert_id']) || !isset($input['status'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit();
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE emergency_alerts 
                SET status = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$input['status'], $input['alert_id']]);
            
            echo json_encode(['success' => true, 'message' => 'Status updated']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to update']);
        }
        exit();
    }

    // ==================== HELPER FUNCTIONS ====================

    /**
     * Convert timestamp to "time ago" format
     */
    private function timeAgo($timestamp) {
        if (!$timestamp) return 'Unknown';
        
        $time = strtotime($timestamp);
        $diff = time() - $time;
        
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . ' min ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        if ($diff < 604800) return floor($diff / 86400) . ' days ago';
        
        return date('M j, Y', $time);
    }
}
?>