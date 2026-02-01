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
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=auth");
            exit();
        }
        require_once VIEW_PATH . 'dashboard.php';
        break;

    default:
        require_once CONTROLLER_PATH . 'HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
}
?>
