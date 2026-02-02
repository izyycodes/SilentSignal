<?php
// controllers/UserController.php
// Handles all logged-in user pages

class UserController {
    
    /**
     * Check if user is logged in
     */
    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login to access this page.";
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
        require_once VIEW_PATH . 'dashboard.php';
    }
    
    /**
     * Emergency Alert System Page
     */
    public function emergencyAlert() {
        $this->requireLogin();
        // Page title is set inside the view file
        require_once VIEW_PATH . 'emergency-alert.php';
    }
    
    /**
     * Disaster Monitoring Page
     */
    public function disasterMonitor() {
        $this->requireLogin();
        // Page title is set inside the view file
        require_once VIEW_PATH . 'disaster-monitoring.php';
    }
    
    /**
     * Family Check-in Page (placeholder)
     */
    public function familyCheckin() {
        $this->requireLogin();
        $pageTitle = "Family Check-in - Silent Signal";
        require_once VIEW_PATH . 'family-checkin.php';
    }
    
    /**
     * Communication Hub Page (placeholder)
     */
    public function communicationHub() {
        $this->requireLogin();
        $pageTitle = "Communication Hub - Silent Signal";
        require_once VIEW_PATH . 'communication-hub.php';
    }
}