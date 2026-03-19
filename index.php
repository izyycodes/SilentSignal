<?php
// index.php - Main entry pointwdwad

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

    // Support Pages — route to the correct controller based on role
    case 'help-center':
    case 'safety-guide':
    case 'fsl-resources':
        $role = $_SESSION['user_role'] ?? 'guest';

        if ($role === 'admin') {
            require_once CONTROLLER_PATH . 'AdminController.php';
            $controller = new AdminController();
        } elseif ($role === 'family') {
            require_once CONTROLLER_PATH . 'FamilyController.php';
            $controller = new FamilyController();
        } elseif ($role === 'pwd') {
            require_once CONTROLLER_PATH . 'UserController.php';
            $controller = new UserController();
        } else {
            require_once CONTROLLER_PATH . 'HomeController.php';
            $controller = new HomeController();
        }

        if ($action === 'help-center')    $controller->helpCenter();
        elseif ($action === 'safety-guide')  $controller->safetyGuide();
        elseif ($action === 'fsl-resources') $controller->fslResources();
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

    // ── USER AJAX ENDPOINTS ──

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

    // ── FAMILY AJAX ENDPOINTS ──

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

    case 'get-pwd-live-status':
        require_once CONTROLLER_PATH . 'FamilyController.php';
        $controller = new FamilyController();
        $controller->getPwdLiveStatus();
        break;

    case 'admin-export-users':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->exportUsers();
        break;

    case 'admin-export-emergency-alerts':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->exportEmergencyAlerts();
        break;

    case 'admin-export-messages':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->exportMessages();
        break;

    case 'admin-export-disaster-alerts':
        require_once CONTROLLER_PATH . 'AdminController.php';
        $controller = new AdminController();
        $controller->exportDisasterAlerts();
        break;

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
    
    case 'send-philsms':
        require_once CONTROLLER_PATH . 'UserController.php';
        $controller = new UserController();
        $controller->sendPhilSms();
        break;

    default:
        require_once CONTROLLER_PATH . 'HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
}
