<?php
// controllers/AdminController.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once BASE_PATH . 'vendor/autoload.php';

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
            ['label' => 'Help Center', 'href' => 'help-center'],
            ['label' => 'Safety Guide', 'href' => 'safety-guide'],
            ['label' => 'FSL Resources', 'href' => 'fsl-resources'],
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

        require_once MODEL_PATH . 'User.php';
        require_once MODEL_PATH . 'EmergencyAlert.php';
        require_once MODEL_PATH . 'ContactInquiry.php';

        $userModel    = new User();
        $alertModel   = new EmergencyAlert();
        $inquiryModel = new ContactInquiry();

        $userStats    = $userModel->getStats();
        $alertStats   = $alertModel->getStats();
        $msgStats     = $inquiryModel->getStats();

        // Disaster alert count from DB
        require_once CONFIG_PATH . 'Database.php';
        $db = (new Database())->getConnection();
        $disasterStmt = $db->query("SELECT COUNT(*) FROM disaster_alerts");
        $totalDisasters = (int)$disasterStmt->fetchColumn();
        $activeDisasterStmt = $db->query("SELECT COUNT(*) FROM disaster_alerts WHERE status = 'active'");
        $activeDisasters = (int)$activeDisasterStmt->fetchColumn();

        // Calculate user growth (users created this month vs last month)
        $growthStmt = $db->query("
            SELECT
                SUM(CASE WHEN MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE()) THEN 1 ELSE 0 END) AS this_month,
                SUM(CASE WHEN MONTH(created_at)=MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at)=YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) THEN 1 ELSE 0 END) AS last_month
            FROM users
        ");
        $growth = $growthStmt->fetch(PDO::FETCH_ASSOC);
        $userGrowth = 0;
        if (!empty($growth['last_month']) && $growth['last_month'] > 0) {
            $userGrowth = round((($growth['this_month'] - $growth['last_month']) / $growth['last_month']) * 100);
        } elseif (!empty($growth['this_month'])) {
            $userGrowth = 100;
        }

        $stats = [
            'totalUsers'       => (int)$userStats['total'],
            'userGrowth'       => $userGrowth,
            'activeAlerts'     => $alertStats['active'],
            'resolvedToday'    => $alertStats['resolved_today'],
            'disasterAlerts'   => $totalDisasters,
            'activeDisasters'  => $activeDisasters,
            'messageInquiries' => (int)$msgStats['total'],
            'pendingMessages'  => (int)$msgStats['pending'],
        ];

        // Real recent activity from DB
        $recentActivity = [];

        // Latest emergency alert
        $latestAlertStmt = $db->query("
            SELECT ea.created_at, ea.alert_type, ea.location_address, CONCAT(u.fname,' ',u.lname) AS uname
            FROM emergency_alerts ea JOIN users u ON ea.user_id=u.id
            ORDER BY ea.created_at DESC LIMIT 1
        ");
        if ($row = $latestAlertStmt->fetch(PDO::FETCH_ASSOC)) {
            $loc = $row['location_address'] ?: 'unknown location';
            $recentActivity[] = [
                'type'        => 'alert',
                'icon'        => 'ri-alarm-warning-line',
                'title'       => 'Emergency Alert',
                'description' => $row['uname'] . ' triggered a ' . ucfirst(str_replace('_',' ',$row['alert_type'])) . ' in ' . $loc,
                'time'        => $this->timeAgo($row['created_at']),
            ];
        }

        // Latest user registration
        $latestUserStmt = $db->query("
            SELECT fname, lname, role, created_at FROM users ORDER BY created_at DESC LIMIT 1
        ");
        if ($row = $latestUserStmt->fetch(PDO::FETCH_ASSOC)) {
            $recentActivity[] = [
                'type'        => 'user',
                'icon'        => 'ri-user-add-line',
                'title'       => 'New User Registration',
                'description' => $row['fname'] . ' ' . $row['lname'] . ' registered as ' . ucfirst($row['role']) . ' user',
                'time'        => $this->timeAgo($row['created_at']),
            ];
        }

        // Latest disaster alert
        $latestDisasterStmt = $db->query("
            SELECT alert_type, location, created_at FROM disaster_alerts ORDER BY created_at DESC LIMIT 1
        ");
        if ($row = $latestDisasterStmt->fetch(PDO::FETCH_ASSOC)) {
            $recentActivity[] = [
                'type'        => 'disaster',
                'icon'        => 'ri-flood-line',
                'title'       => 'Disaster Alert',
                'description' => ucfirst($row['alert_type']) . ' alert issued for ' . $row['location'],
                'time'        => $this->timeAgo($row['created_at']),
            ];
        }

        // Latest message inquiry
        $latestMsgStmt = $db->query("
            SELECT name, subject, created_at FROM contact_inquiries ORDER BY created_at DESC LIMIT 1
        ");
        if ($row = $latestMsgStmt->fetch(PDO::FETCH_ASSOC)) {
            $recentActivity[] = [
                'type'        => 'message',
                'icon'        => 'ri-message-3-line',
                'title'       => 'New Message Inquiry',
                'description' => ($row['name'] ?: 'A user') . ' submitted: ' . $row['subject'],
                'time'        => $this->timeAgo($row['created_at']),
            ];
        }
        
        require_once VIEW_PATH . 'admin-dashboard.php';
    }

    /**
     * Helper: format timestamp as relative time
     */
    private function timeAgo($datetime) {
        if (!$datetime) return 'just now';
        $diff = time() - strtotime($datetime);
        if ($diff < 60)    return $diff . ' seconds ago';
        if ($diff < 3600)  return floor($diff / 60) . ' minutes ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        return floor($diff / 86400) . ' days ago';
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

        require_once MODEL_PATH . 'User.php';
        $userModel = new User();

        $perPage     = 5;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $offset      = ($currentPage - 1) * $perPage;

        $stats       = $userModel->getStats();
        $users       = $userModel->getAllPaginated($perPage, $offset);
        $totalPages  = (int)ceil($stats['total'] / $perPage);
        $rangeStart  = $offset + 1;
        $rangeEnd    = min($offset + $perPage, $stats['total']);

        // Variables used in view: $users, $stats, $totalPages,
        // $currentPage, $rangeStart, $rangeEnd, $perPage
        
        require_once VIEW_PATH . 'admin-users.php';
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

        require_once MODEL_PATH . 'EmergencyAlert.php';
        $alertModel = new EmergencyAlert();

        $perPage     = 5;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $offset      = ($currentPage - 1) * $perPage;

        $stats      = $alertModel->getStats();
        $alerts     = $alertModel->getAllPaginated($perPage, $offset);
        $totalCount = $alertModel->getTotalCount();
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        $rangeStart = $offset + 1;
        $rangeEnd   = min($offset + $perPage, $totalCount);
        
        require_once VIEW_PATH . 'admin-emergency-alerts.php';
    }

    /**
     * API: Get alerts as JSON (for AJAX refresh)
     */
    public function getAlertsAPI() {
        $this->requireAdmin();
        header('Content-Type: application/json');

        require_once MODEL_PATH . 'EmergencyAlert.php';
        $alertModel = new EmergencyAlert();

        $perPage = 5;
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $offset  = ($page - 1) * $perPage;

        echo json_encode([
            'success' => true,
            'alerts'  => $alertModel->getAllPaginated($perPage, $offset),
            'stats'   => $alertModel->getStats(),
            'total'   => $alertModel->getTotalCount(),
        ]);
        exit;
    }

    /**
     * API: Update alert status
     */
    public function updateAlertStatus() {
        $this->requireAdmin();
        header('Content-Type: application/json');

        $alertId   = (int)($_POST['alert_id'] ?? $_GET['alert_id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? $_GET['status'] ?? 'resolved');

        if ($alertId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid alert ID.']);
            exit;
        }

        require_once MODEL_PATH . 'EmergencyAlert.php';
        $alertModel = new EmergencyAlert();

        if ($alertModel->updateStatus($alertId, $newStatus)) {
            echo json_encode(['success' => true, 'message' => 'Alert status updated.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update alert.']);
        }
        exit;
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
        
        // Mock disaster alerts
        $alerts = [
            [
                'id' => 1,
                'alert_id' => '#DIS-2890',
                'disaster_type' => 'Typhoon Odette',
                'severity' => 'extreme',
                'location' => 'Visayas Region',
                'affected_users' => '2,847 users',
                'magnitude' => '185 km/h',
                'issued_time' => '30 min ago',
                'status' => 'active'
            ],
            [
                'id' => 2,
                'alert_id' => '#DIS-2889',
                'disaster_type' => 'Earthquake Detected',
                'severity' => 'severe',
                'location' => 'Mindanao, Bukidnon Region',
                'affected_users' => '1,523 users',
                'magnitude' => 'Magnitude 6.2',
                'issued_time' => '1 hour ago',
                'status' => 'monitoring'
            ],
            [
                'id' => 3,
                'alert_id' => '#DIS-2888',
                'disaster_type' => 'Flood Warning',
                'severity' => 'moderate',
                'location' => 'Metro Manila, Muntinlupa',
                'affected_users' => '892 users',
                'magnitude' => 'Water Level: 15m',
                'issued_time' => '2 hours ago',
                'status' => 'monitoring'
            ],
            [
                'id' => 4,
                'alert_id' => '#DIS-2887',
                'disaster_type' => 'Tropical Storm Paolo',
                'severity' => 'moderate',
                'location' => 'Luzon, Cagayan Valley',
                'affected_users' => '1,248 users',
                'magnitude' => '95 km/h',
                'issued_time' => '3 hours ago',
                'status' => 'active'
            ],
            [
                'id' => 5,
                'alert_id' => '#DIS-2886',
                'disaster_type' => 'Fire Alert',
                'severity' => 'severe',
                'location' => 'Quezon City, Commonwealth',
                'affected_users' => '416 users',
                'magnitude' => 'Level 3 Fire',
                'issued_time' => '5 hours ago',
                'status' => 'cleared'
            ],
            [
                'id' => 6,
                'alert_id' => '#DIS-2885',
                'disaster_type' => 'Earthquake Detected',
                'severity' => 'minor',
                'location' => 'Batangas, Taal Region',
                'affected_users' => '734 users',
                'magnitude' => 'Magnitude 4.2',
                'issued_time' => '8 hours ago',
                'status' => 'cleared'
            ]
        ];
        
        $stats = [
            'active_disasters' => 1267,
            'typhoons' => 67,
            'earthquakes' => 1200,
            'floods' => 67
        ];
        
        require_once VIEW_PATH . 'admin-disaster-alerts.php';
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
        $messages    = $contactInquiry->getAllPaginated($perPage, $offset);
        $totalPages  = (int)ceil($stats['total'] / $perPage);
        $rangeStart  = $offset + 1;
        $rangeEnd    = min($offset + $perPage, $stats['total']);

        require_once VIEW_PATH . 'admin-messages.php';
    }

    // ==================== USER MANAGEMENT ACTIONS ====================

    /**
     * Verify a user account
     */
    public function verifyUser() {
        $this->requireAdmin();

        $userId = (int)($_POST['user_id'] ?? $_GET['user_id'] ?? 0);
        $returnPage = (int)($_POST['page'] ?? $_GET['page'] ?? 1);

        if ($userId <= 0) {
            $_SESSION['error'] = 'Invalid user ID.';
            header('Location: ' . BASE_URL . 'index.php?action=admin-users&page=' . $returnPage);
            exit;
        }

        require_once MODEL_PATH . 'User.php';
        $userModel = new User();

        if ($userModel->verifyUser($userId)) {
            $_SESSION['success'] = 'User account verified successfully!';
        } else {
            $_SESSION['error'] = 'Failed to verify user account.';
        }

        header('Location: ' . BASE_URL . 'index.php?action=admin-users&page=' . $returnPage);
        exit;
    }

    /**
     * Toggle user active status (activate/deactivate)
     */
    public function toggleUserActive() {
        $this->requireAdmin();

        $userId = (int)($_POST['user_id'] ?? $_GET['user_id'] ?? 0);
        $returnPage = (int)($_POST['page'] ?? $_GET['page'] ?? 1);

        if ($userId <= 0) {
            $_SESSION['error'] = 'Invalid user ID.';
            header('Location: ' . BASE_URL . 'index.php?action=admin-users&page=' . $returnPage);
            exit;
        }

        require_once MODEL_PATH . 'User.php';
        $userModel = new User();

        if ($userModel->toggleActive($userId)) {
            $_SESSION['success'] = 'User status updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update user status.';
        }

        header('Location: ' . BASE_URL . 'index.php?action=admin-users&page=' . $returnPage);
        exit;
    }

    // ==================== MESSAGE REPLY & STATUS ====================

    /**
     * Send reply email to message sender
     */
    public function sendMessageReply() {
        $this->requireAdmin();

        $messageId  = (int)($_POST['message_id'] ?? 0);
        $replyText  = trim($_POST['reply_text'] ?? '');
        $returnPage = (int)($_POST['page'] ?? 1);

        if ($messageId <= 0 || empty($replyText)) {
            $_SESSION['error'] = 'Invalid message ID or empty reply.';
            header('Location: ' . BASE_URL . 'index.php?action=admin-messages&page=' . $returnPage);
            exit;
        }

        require_once MODEL_PATH . 'ContactInquiry.php';
        $inquiryModel = new ContactInquiry();

        $message = $inquiryModel->getById($messageId);

        if (!$message) {
            $_SESSION['error'] = 'Message not found.';
            header('Location: ' . BASE_URL . 'index.php?action=admin-messages&page=' . $returnPage);
            exit;
        }

        $to      = $message['email'];
        $subject = 'Re: ' . $message['subject'] . ' - Silent Signal Support';

        $emailBody  = "Hello " . ($message['name'] ?: 'there') . ",\n\n";
        $emailBody .= "Thank you for contacting Silent Signal. Here is our response to your inquiry:\n\n";
        $emailBody .= $replyText . "\n\n";
        $emailBody .= "---\n";
        $emailBody .= "Original Message:\n";
        $emailBody .= $message['message'] . "\n\n";
        $emailBody .= "Best regards,\n";
        $emailBody .= "Silent Signal Support Team\n";
        $emailBody .= CONTACT_EMAIL;

        $emailSent = false;

        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ssilentsignal@gmail.com'; 
            $mail->Password   = 'rnfa bxze eyix tmjw';      
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom(CONTACT_EMAIL, 'Silent Signal Support');
            $mail->addAddress($to, $message['name'] ?: '');

            $mail->Subject = $subject;
            $mail->Body    = $emailBody;

            $mail->send();
            $emailSent = true;

        } catch (Exception $e) {
            error_log("Mailer Error for message ID {$messageId}: " . $mail->ErrorInfo);
            $emailSent = false;
        }

        if ($emailSent) {
            $adminUserId = $_SESSION['user_id'];
            $inquiryModel->saveReply($messageId, $adminUserId, $replyText);
            $_SESSION['success'] = 'Reply sent successfully to ' . $to;
        } else {
            $_SESSION['error'] = 'Failed to send email. Please check your SMTP configuration.';
        }

        header('Location: ' . BASE_URL . 'index.php?action=admin-messages&page=' . $returnPage);
        exit;
    }

    /**
     * Mark message as resolved
     */
    public function resolveMessage() {
        $this->requireAdmin();

        $messageId  = (int)($_POST['message_id'] ?? $_GET['message_id'] ?? 0);
        $returnPage = (int)($_POST['page'] ?? $_GET['page'] ?? 1);

        if ($messageId <= 0) {
            $_SESSION['error'] = 'Invalid message ID.';
            header('Location: ' . BASE_URL . 'index.php?action=admin-messages&page=' . $returnPage);
            exit;
        }

        require_once MODEL_PATH . 'ContactInquiry.php';
        $inquiryModel = new ContactInquiry();

        if ($inquiryModel->updateStatus($messageId, 'resolved')) {
            $_SESSION['success'] = 'Message marked as resolved.';
        } else {
            $_SESSION['error'] = 'Failed to update message status.';
        }

        header('Location: ' . BASE_URL . 'index.php?action=admin-messages&page=' . $returnPage);
        exit;
    }

    /**
     * Update message status (in_review, replied, resolved)
     */
    public function updateMessageStatus() {
        $this->requireAdmin();

        $messageId  = (int)($_POST['message_id'] ?? 0);
        $newStatus  = trim($_POST['status'] ?? '');
        $returnPage = (int)($_POST['page'] ?? 1);

        $validStatuses = ['pending', 'in_review', 'replied', 'resolved'];

        if ($messageId <= 0 || !in_array($newStatus, $validStatuses)) {
            $_SESSION['error'] = 'Invalid message ID or status.';
            header('Location: ' . BASE_URL . 'index.php?action=admin-messages&page=' . $returnPage);
            exit;
        }

        require_once MODEL_PATH . 'ContactInquiry.php';
        $inquiryModel = new ContactInquiry();

        if ($inquiryModel->updateStatus($messageId, $newStatus)) {
            $_SESSION['success'] = 'Message status updated to ' . ucfirst(str_replace('_', ' ', $newStatus));
        } else {
            $_SESSION['error'] = 'Failed to update message status.';
        }

        header('Location: ' . BASE_URL . 'index.php?action=admin-messages&page=' . $returnPage);
        exit;
    }

    // ==================== EXPORT METHODS ====================

    /**
     * Helper: stream a CSV file download
     */
    private function exportCSV($filename, $headers, $rows) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, array_values($row));
        }
        fclose($output);
        exit;
    }

    /**
     * Export Users as CSV
     */
    public function exportUsers() {
        $this->requireAdmin();
        require_once CONFIG_PATH . 'Database.php';
        $db   = (new Database())->getConnection();
        $stmt = $db->query("
            SELECT u.id,
                CONCAT(u.fname,' ',u.lname) AS name,
                u.email, u.phone_number, u.role,
                CASE WHEN u.is_verified=1 THEN 'Verified' ELSE 'Pending' END AS verified_status,
                CASE WHEN u.is_active=1 THEN 'Active' ELSE 'Inactive' END AS active_status,
                DATE_FORMAT(u.created_at,'%Y-%m-%d %H:%i:%s') AS registered_at,
                COALESCE(mp.pwd_id,'') AS pwd_id,
                COALESCE(mp.disability_type,'') AS disability_type,
                CASE
                    WHEN (mp.city IS NULL OR mp.city='') AND (mp.province IS NULL OR mp.province='') THEN ''
                    WHEN (mp.city IS NULL OR mp.city='') THEN mp.province
                    WHEN (mp.province IS NULL OR mp.province='') THEN mp.city
                    ELSE CONCAT(mp.city,', ',mp.province)
                END AS location
            FROM users u
            LEFT JOIN medical_profiles mp ON u.id = mp.user_id
            ORDER BY u.created_at DESC
        ");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $headers = ['ID','Name','Email','Phone','Role','Verified','Status','Registered At','PWD ID','Disability Type','Location'];
        $rows = [];
        foreach ($users as $u) {
            $rows[] = [
                $u['id'], $u['name'], $u['email'], $u['phone_number'],
                ucfirst($u['role']), $u['verified_status'], $u['active_status'],
                $u['registered_at'], $u['pwd_id'], $u['disability_type'], $u['location'],
            ];
        }
        $this->exportCSV('silent_signal_users_' . date('Ymd_His') . '.csv', $headers, $rows);
    }

    /**
     * Export Emergency Alerts as CSV
     */
    public function exportEmergencyAlerts() {
        $this->requireAdmin();
        require_once MODEL_PATH . 'EmergencyAlert.php';
        $alertModel = new EmergencyAlert();
        $alerts     = $alertModel->getAllForExport();
        $prefixMap  = ['sos'=>'SOS','shake'=>'SHAKE','panic_click'=>'PANIC','medical'=>'MED','assistance'=>'ASST','fall_detection'=>'FALL'];
        $typeMap    = ['sos'=>'Emergency SOS','shake'=>'Shake Alert','panic_click'=>'Panic Button','medical'=>'Medi-Alert','assistance'=>'Assistance','fall_detection'=>'Fall Detection'];
        $headers    = ['ID','Alert ID','User','Email','Phone','Alert Type','Priority','Status','Location','Latitude','Longitude','Created At','Resolved At','Notes'];
        $rows = [];
        foreach ($alerts as $a) {
            $prefix  = $prefixMap[$a['alert_type']] ?? 'ALERT';
            $alertId = '#' . $prefix . '-' . str_pad($a['id'], 4, '0', STR_PAD_LEFT);
            $rows[]  = [
                $a['id'], $alertId, $a['user_name'], $a['user_email'], $a['user_phone'],
                $typeMap[$a['alert_type']] ?? ucfirst(str_replace('_',' ',$a['alert_type'])),
                ucfirst($a['priority']), ucfirst($a['status']),
                $a['location_address'] ?? '', $a['latitude'] ?? '', $a['longitude'] ?? '',
                $a['created_at'], $a['resolved_at'] ?? '', $a['notes'] ?? '',
            ];
        }
        $this->exportCSV('silent_signal_emergency_alerts_' . date('Ymd_His') . '.csv', $headers, $rows);
    }

    /**
     * Export Message Inquiries as CSV
     */
    public function exportMessages() {
        $this->requireAdmin();
        require_once CONFIG_PATH . 'Database.php';
        $db   = (new Database())->getConnection();
        $stmt = $db->query("
            SELECT id, COALESCE(name,'') AS name, email, subject, message, category, priority, status,
                   CASE WHEN is_read=1 THEN 'Yes' ELSE 'No' END AS is_read,
                   COALESCE(reply_message,'') AS reply_message,
                   COALESCE(DATE_FORMAT(replied_at,'%Y-%m-%d %H:%i:%s'),'') AS replied_at,
                   DATE_FORMAT(created_at,'%Y-%m-%d %H:%i:%s') AS created_at
            FROM contact_inquiries ORDER BY created_at DESC
        ");
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $headers  = ['ID','Name','Email','Subject','Message','Category','Priority','Status','Read','Reply','Replied At','Submitted At'];
        $rows = [];
        foreach ($messages as $m) {
            $rows[] = [
                $m['id'], $m['name'], $m['email'], $m['subject'], $m['message'],
                ucfirst($m['category']), ucfirst($m['priority']), ucfirst(str_replace('_',' ',$m['status'])),
                $m['is_read'], $m['reply_message'], $m['replied_at'], $m['created_at'],
            ];
        }
        $this->exportCSV('silent_signal_messages_' . date('Ymd_His') . '.csv', $headers, $rows);
    }

    /**
     * Export Disaster Alerts as CSV (hardcoded — matches the hardcoded view)
     */
    public function exportDisasterAlerts() {
        $this->requireAdmin();
        $headers = ['Alert ID','Disaster Type','Severity','Location','Affected Users','Magnitude/Intensity','Status'];
        $rows = [
            ['#DIS-2890','Typhoon Odette','Extreme','Visayas Region','2,847 users','185 km/h','Active'],
            ['#DIS-2889','Earthquake Detected','Severe','Mindanao, Bukidnon Region','1,523 users','Magnitude 6.2','Monitoring'],
            ['#DIS-2888','Flood Warning','Moderate','Metro Manila, Muntinlupa','892 users','Water Level: 15m','Monitoring'],
            ['#DIS-2887','Tropical Storm Paolo','Moderate','Luzon, Cagayan Valley','1,248 users','95 km/h','Active'],
            ['#DIS-2886','Fire Alert','Severe','Quezon City, Commonwealth','416 users','Level 3 Fire','Cleared'],
            ['#DIS-2885','Earthquake Detected','Minor','Batangas, Taal Region','734 users','Magnitude 4.2','Cleared'],
        ];
        $this->exportCSV('silent_signal_disaster_alerts_' . date('Ymd_His') . '.csv', $headers, $rows);
    }

    public function helpCenter()
    {
        $pageTitle = "Help Center - Silent Signal";
        require_once VIEW_PATH . 'help-center.php';
    }

    public function safetyGuide()
    {
        $pageTitle = "Safety Guide - Silent Signal";
        require_once VIEW_PATH . 'safety-guide.php';
    }

    public function fslResources()
    {
        $pageTitle = "FSL Resources - Silent Signal";
        require_once VIEW_PATH . 'fsl-resources.php';
    }
}
?>