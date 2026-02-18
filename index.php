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

    case 'update-checkin-status':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->updateCheckinStatus();
        break;

    case 'get-family-status':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->getFamilyStatus();
        break;

    case 'get-location-history':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->getLocationHistory();
        break;

    case 'log-checkin-media':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->logCheckinMedia();
        break;

    case 'communication-hub':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->communicationHub();
        break;

    case 'send-hub-sms':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->sendHubSms();
        break;

    case 'log-hub-media':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->logHubMedia();
        break;

    case 'get-emergency-contacts':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->getEmergencyContacts();
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

    case 'get-pwd-live-status':
        require_once CONTROLLER_PATH . 'FamilyController.php';
        $controller = new FamilyController();
        $controller->getPwdLiveStatus();
        break;

    case 'get-pwd-profile':
        require_once CONTROLLER_PATH . 'FamilyController.php';
        $controller = new FamilyController();
        $controller->getPwdProfile();
        break;

    case 'respond-to-alert':
        require_once CONTROLLER_PATH . 'FamilyController.php';
        $controller = new FamilyController();
        $controller->respondToAlert();
        break;

    case 'alert-all-family':
        require_once CONTROLLER_PATH . 'FamilyController.php';
        $controller = new FamilyController();
        $controller->alertAllFamily();
        break;

    case 'refresh-family-dashboard':
        require_once CONTROLLER_PATH . 'FamilyController.php';
        $controller = new FamilyController();
        $controller->refreshDashboard();
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

    case 'log-emergency-alert':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->logEmergencyAlert();
        break;

    default:
        require_once CONTROLLER_PATH . 'HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
}
?>
