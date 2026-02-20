<?php
// index.php - Main entry point

session_start();
require_once 'config/config.php';

$action = $_GET['action'] ?? 'home';
$isHome = ($action === 'home');

switch ($action) {	
    // Landing Page
    case 'home':
        require_once CONTROLLER_PATH . 'HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;

    case 'submit-contact':
        require_once CONTROLLER_PATH . 'HomeController.php';
        $controller = new HomeController();
        $controller->submitContact();
        break;

    // Combined Auth Page (Login & Signup)
    case 'auth':
        require_once CONTROLLER_PATH . 'AuthController.php';
        $controller = new AuthController();
        $controller->showAuth();
        break;
        
    case 'process_login':
        require_once CONTROLLER_PATH . 'AuthController.php';
        $controller = new AuthController();
        $controller->processLogin();
        break;
        
    case 'process_signup':
        require_once CONTROLLER_PATH . 'AuthController.php';
        $controller = new AuthController();
        $controller->processSignup();
        break;

    case 'logout':
        require_once CONTROLLER_PATH . 'AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    // ==========================================
    // USER MODULES
    // ==========================================
    
    case 'dashboard':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->dashboard();
        break;

    case 'emergency-alert':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->emergencyAlert();
        break;

    case 'disaster-monitor':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->disasterMonitor();
        break;

    case 'family-checkin':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->familyCheckin();
        break;

    case 'communication-hub':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->communicationHub();
        break;

    case 'medical-profile':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->medicalProfile();
        break;

    // ==========================================
    // USER AJAX ENDPOINTS
    // ==========================================

    case 'save-medical-profile':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->saveMedicalProfile();
        break;

    case 'log-emergency-alert':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->logEmergencyAlert();
        break;

    case 'log-disaster-response':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->logDisasterResponse();
        break;

    case 'update-safety-status':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->updateSafetyStatus();
        break;

    // ==========================================
    // FAMILY MODULE
    // ==========================================
    
    case 'family-dashboard':
        require_once CONTROLLER_PATH . 'FamilyController.php';
        $controller = new FamilyController();
        $controller->familyDashboard();
        break;

    // ==========================================
    // ADMIN MODULE
    // ==========================================
    
    case 'admin-dashboard':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->dashboard();
        break;

    case 'admin-users':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->users();
        break;

    case 'admin-emergency-alerts':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->emergencyAlerts();
        break;

    case 'admin-disaster-alerts':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->disasterAlerts();
        break;

    case 'admin-messages':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->messages();
        break;

    // ==========================================
    // ADMIN AJAX ENDPOINTS
    // ==========================================

    case 'admin-alerts-api':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->getAlertsAPI();
        break;

    case 'admin-update-alert':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->updateAlertStatus();
        break;

    // ==========================================
    // DEFAULT
    // ==========================================

    default:
        require_once CONTROLLER_PATH . 'HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
}
?>