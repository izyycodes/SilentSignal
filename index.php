<?php
// index.php - Main entry point

session_start();
require_once 'config/config.php';

$action = $_GET['action'] ?? 'home';
$isHome = ($action === 'home');

switch ($action) {	
    case 'home':
        require_once CONTROLLER_PATH . 'HomeController.php';
        $controller = new HomeController();
        $controller->index();
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

    default:
        require_once CONTROLLER_PATH . 'HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;

case 'family-dashboard':
require_once CONTROLLER_PATH . 'UserController.php';
$controller = new UserController();
$controller->familyDashboard();
break;
        
}
?>
