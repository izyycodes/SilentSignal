<?php
// controllers/AdminController.php

class AdminController
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
    private function getCurrentUser()
    {
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
    private function getCommonViewData()
    {
        return [
            'currentUser' => $this->getCurrentUser(),
            'currentAction' => $_GET['action'] ?? 'admin-dashboard'
        ];
    }

    /**
     * Check if user is admin
     */
    private function requireAdmin()
    {
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
    public function dashboard()
    {
        $this->requireAdmin();
        $pageTitle = "Admin Dashboard - Silent Signal";

        extract($this->getCommonViewData());
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
    public function users()
    {
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
    public function emergencyAlerts()
    {
        $this->requireAdmin();
        $pageTitle = "Emergency Alerts - Admin - Silent Signal";

        extract($this->getCommonViewData());
        extract($this->getSharedData());

        // Mock emergency alerts
        $alerts = [
            [
                'id' => 1,
                'alert_id' => '#SOS-2789',
                'user_name' => 'Maria Santos',
                'user_id' => '12645',
                'alert_type' => 'Emergency SOS',
                'priority' => 'critical',
                'location' => '123 Trinolan ng Magarilao Akda, Caloocan City',
                'time' => '2 min ago',
                'response_time' => '-',
                'status' => 'active'
            ],
            [
                'id' => 2,
                'alert_id' => '#SHAKE-2788',
                'user_name' => 'Jerome Buntaliada',
                'user_id' => '12446',
                'alert_type' => 'Shake Alert',
                'priority' => 'high',
                'location' => '436 Main St, Bacolod City',
                'time' => '5 min ago',
                'response_time' => '3 min',
                'status' => 'responded'
            ],
            [
                'id' => 3,
                'alert_id' => '#PANIC-2787',
                'user_name' => 'Paulo Santos',
                'user_id' => 'PWD-2024-15547',
                'alert_type' => 'Panic Button',
                'priority' => 'critical',
                'location' => '789 Juan St, Manila',
                'time' => '7 min ago',
                'response_time' => '2 min',
                'status' => 'resolved'
            ],
            [
                'id' => 4,
                'alert_id' => '#SOS-2786',
                'user_name' => 'Ana Santos',
                'user_id' => '12458',
                'alert_type' => 'Emergency SOS',
                'priority' => 'high',
                'location' => '221 Quezon Ave, Quezon City',
                'time' => '22 min ago',
                'response_time' => '5 min',
                'status' => 'resolved'
            ],
            [
                'id' => 5,
                'alert_id' => '#SHAKE-2785',
                'user_name' => 'Luis Cruz',
                'user_id' => 'PWD-2024-123459',
                'alert_type' => 'Shake Alert',
                'priority' => 'medium',
                'location' => '654 Osmena Blvd, Cebu City',
                'time' => '30 min ago',
                'response_time' => '8 min',
                'status' => 'resolved'
            ],
            [
                'id' => 6,
                'alert_id' => '#SOS-2784',
                'user_name' => 'Miguel Reyes',
                'user_id' => 'PWD-2024-15674',
                'alert_type' => 'Emergency SOS',
                'priority' => 'critical',
                'location' => '987 J. Laurel Ave, Lanao City',
                'time' => '28 min ago',
                'response_time' => '4 min',
                'status' => 'resolved'
            ]
        ];

        $stats = [
            'total_today' => 1267,
            'critical' => 67,
            'active' => 1200,
            'resolved_today' => 67
        ];

        require_once VIEW_PATH . 'admin-emergency-alerts.php';
    }

    // ==================== DISASTER ALERTS ====================

    /**
     * Disaster Alerts Page
     */
    public function disasterAlerts()
    {
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
    public function messages()
    {
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
    public function verifyUser()
    {
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
    public function toggleUserActive()
    {
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
    public function sendMessageReply()
    {
        $this->requireAdmin();

        require_once BASE_PATH . 'vendor/autoload.php';

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
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'aizhellegwynneth@gmail.com';
            $mail->Password   = 'gtub oycl mxfj nxat';
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom(CONTACT_EMAIL, 'Silent Signal Support');
            $mail->addAddress($to, $message['name'] ?: '');
            $mail->Subject = $subject;
            $mail->Body    = $emailBody;

            $mail->send();
            $emailSent = true;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
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
    public function resolveMessage()
    {
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
    public function updateMessageStatus()
    {
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
}
