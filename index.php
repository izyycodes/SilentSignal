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

    // User Modules    
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

    case 'save-medical-profile':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->saveMedicalProfile();
        break;


    // Family Module    
    case 'family-dashboard':
        require_once CONTROLLER_PATH . 'FamilyController.php';
        $controller = new FamilyController();
        $controller->familyDashboard();
        break;

    // Admin Module    
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

    case 'admin-verify-user':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->verifyUser();
        break;

    case 'admin-toggle-active':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->toggleUserActive();
        break;

    case 'admin-send-reply':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->sendMessageReply();
        break;

    case 'admin-resolve-message':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->resolveMessage();
        break;

    case 'admin-update-message-status':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->updateMessageStatus();
        break;

    default:
        require_once CONTROLLER_PATH . 'HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
}
?>