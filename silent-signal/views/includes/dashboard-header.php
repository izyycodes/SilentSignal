<?php
// views/includes/dashboard-header.php
// Shared header for all dashboard/logged-in pages

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "index.php?action=login");
    exit();
}

// Navigation items array
$navItems = [
    ['action' => 'dashboard', 'icon' => 'ri-home-line', 'label' => 'Home'],
    ['action' => 'emergency-alert', 'icon' => 'ri-alarm-warning-line', 'label' => 'Emergency Alert'],
    ['action' => 'disaster-monitor', 'icon' => 'ri-earth-line', 'label' => 'Disaster Monitor'],
    ['action' => 'family-checkin', 'icon' => 'ri-team-line', 'label' => 'Family Check-in'],
    ['action' => 'communication-hub', 'icon' => 'ri-message-2-line', 'label' => 'Communication Hub'],
];

// Get current action for active state
$currentAction = $_GET['action'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard - Silent Signal'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>assets/images/logo.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/c835d6c14b.js" crossorigin="anonymous"></script>
    
    <!-- Shared Dashboard Styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/dashboard-shared.css">
</head>
<body>
    
<!-- Top Navigation Bar -->
<header class="dashboard-header">
    <div class="header-container">
        <!-- Logo -->
        <a href="<?php echo BASE_URL; ?>index.php?action=dashboard" class="header-logo">
            <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Silent Signal Logo">
            <span>Silent Signal.</span>
        </a>
        
        <!-- Desktop Navigation -->
        <nav class="header-nav">
            <?php foreach ($navItems as $item): ?>
                <a href="<?php echo BASE_URL; ?>index.php?action=<?php echo $item['action']; ?>" 
                   class="nav-item <?php echo ($currentAction === $item['action']) ? 'active' : ''; ?>">
                    <?php echo $item['label']; ?>
                </a>
            <?php endforeach; ?>
        </nav>
        
        <!-- User Menu -->
        <div class="header-user">
            <div class="user-info">
                <span class="user-name"><?php echo $_SESSION['user_name'] ?? 'User'; ?></span>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                </div>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?action=logout" class="logout-btn" title="Logout">
                <i class="ri-logout-box-r-line"></i>
            </a>
        </div>
        
        <!-- Mobile Menu Button -->
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="ri-menu-line"></i>
        </button>
    </div>
</header>

<!-- Mobile Navigation Overlay -->
<div class="mobile-nav-overlay" id="mobileNavOverlay"></div>

<!-- Mobile Navigation Sidebar -->
<nav class="mobile-nav" id="mobileNav">
    <div class="mobile-nav-header">
        <div class="mobile-user-info">
            <div class="user-avatar large">
                <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
            </div>
            <div>
                <span class="user-name"><?php echo $_SESSION['user_name'] ?? 'User'; ?></span>
                <span class="user-role"><?php echo ucfirst($_SESSION['user_role'] ?? 'User'); ?></span>
            </div>
        </div>
        <button class="mobile-nav-close" id="mobileNavClose">
            <i class="ri-close-line"></i>
        </button>
    </div>
    
    <div class="mobile-nav-items">
        <?php foreach ($navItems as $item): ?>
            <a href="<?php echo BASE_URL; ?>index.php?action=<?php echo $item['action']; ?>" 
               class="mobile-nav-item <?php echo ($currentAction === $item['action']) ? 'active' : ''; ?>">
                <i class="<?php echo $item['icon']; ?>"></i>
                <span><?php echo $item['label']; ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    
    <div class="mobile-nav-footer">
        <a href="<?php echo BASE_URL; ?>index.php?action=logout" class="mobile-logout-btn">
            <i class="ri-logout-box-r-line"></i>
            <span>Logout</span>
        </a>
    </div>
</nav>

<!-- Main Content Wrapper -->
<main class="dashboard-main">