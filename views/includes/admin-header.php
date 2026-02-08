<?php
// views/includes/admin-header.php

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: " . BASE_URL . "index.php?action=auth");
    exit();
}

// Fallback to session data
if (!isset($currentUser)) {
    $currentUser = [
        'id' => $_SESSION['user_id'] ?? 0,
        'name' => $_SESSION['user_name'] ?? 'Admin',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => 'admin',
        'initials' => $_SESSION['user_initials'] ?? 'A'
    ];
}

// if (!isset($adminNavItems)) {
//     $adminNavItems = [
//         ['action' => 'admin-dashboard', 'icon' => 'ri-dashboard-line', 'label' => 'Dashboard'],
//         ['action' => 'admin-users', 'icon' => 'ri-user-settings-line', 'label' => 'Users'],
//         ['action' => 'admin-emergency-alerts', 'icon' => 'ri-alarm-warning-line', 'label' => 'Emergency Alerts'],
//         ['action' => 'admin-disaster-alerts', 'icon' => 'ri-earth-line', 'label' => 'Disaster Alerts'],
//         ['action' => 'admin-messages', 'icon' => 'ri-message-3-line', 'label' => 'Messages'],
//     ];
// }

$currentAction = $_GET['action'] ?? 'admin-dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Dashboard - Silent Signal'; ?></title>
    
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>assets/images/logo.png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin-shared.css">
</head>
<body>

<!-- Admin Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Logo">
            <span>Silent Signal</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="ri-menu-fold-line"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <?php foreach ($adminNavItems as $item): ?>
            <a href="<?php echo BASE_URL; ?>index.php?action=<?php echo $item['action']; ?>" 
               class="nav-link <?php echo ($currentAction === $item['action']) ? 'active' : ''; ?>">
                <i class="<?php echo $item['icon']; ?>"></i>
                <span><?php echo $item['label']; ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?php echo $currentUser['initials']; ?></div>
            <div class="user-details">
                <div class="user-name"><?php echo $currentUser['name']; ?></div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
        <a href="<?php echo BASE_URL; ?>index.php?action=logout" class="logout-btn">
            <i class="ri-logout-box-r-line"></i>
        </a>
    </div>
</aside>

<!-- Admin Main Content -->
<main class="admin-main">
    <header class="admin-header">
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="ri-menu-line"></i>
        </button>
        <div class="header-title">
            <h1><?php echo isset($pageTitle) ? str_replace(' - Silent Signal', '', $pageTitle) : 'Dashboard'; ?></h1>
        </div>
        <div class="header-actions">
            <button class="header-btn" onclick="window.location.reload()">
                <i class="ri-refresh-line"></i>
            </button>
            <button class="header-btn">
                <i class="ri-notification-3-line"></i>
                <span class="badge">3</span>
            </button>
            <a href="<?php echo BASE_URL; ?>index.php?action=dashboard" class="header-btn" title="User View">
                <i class="ri-eye-line"></i>
            </a>
        </div>
    </header>
    
    <div class="admin-content">