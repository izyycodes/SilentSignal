<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Silent Signal - Emergency Communication for PWD'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>assets/images/logo.png">
    <link rel="shortcut icon" type="image/png" href="<?php echo BASE_URL; ?>assets/images/logo.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/c835d6c14b.js" crossorigin="anonymous"></script>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/home.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/home-header.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/home-footer.css">
</head>
<body>
<header class="header">
    <div class="container">
            
            <input type="checkbox" id="sidebar-active">
            <label for="sidebar-active" class="open-sidebar-btn">
                <i class="ri-menu-line"></i>
            </label>
            
            <div class="links-container">
                <label for="sidebar-active" class="close-sidebar-btn">
                    <i class="ri-close-line"></i>
                </label>
                
                <div class="logo">
                    <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Silent Signal Logo" class="logo-icon">
                    <span class="logo-text">Silent Signal.</span>
                </div>

                <ul class="nav-menu">
                    <li><a href="<?php echo isset($isHome) && $isHome ? '#home' : BASE_URL . 'index.php?action=home#home'; ?>" class="nav-link">Home</a></li>
                    <li><a href="<?php echo isset($isHome) && $isHome ? '#features' : BASE_URL . 'index.php?action=home#features'; ?>" class="nav-link">Features</a></li>
                    <li><a href="<?php echo isset($isHome) && $isHome ? '#how-it-works' : BASE_URL . 'index.php?action=home#how-it-works'; ?>" class="nav-link">How It Works</a></li>
                    <li><a href="<?php echo isset($isHome) && $isHome ? '#contact' : BASE_URL . 'index.php?action=home#contact'; ?>" class="nav-link">Contact</a></li>
                </ul>
                
                <div class="nav-buttons">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <!-- Logged in user -->
                        <a href="<?php echo BASE_URL; ?>index.php?action=dashboard" class="btn btn-secondary">Dashboard</a>
                        <a href="<?php echo BASE_URL; ?>index.php?action=logout" class="btn btn-primary">Logout</a>
                    <?php else: ?>
                        <!-- Guest user -->
                        <a href="<?php echo BASE_URL; ?>index.php?action=login" class="btn btn-secondary">Login</a>
                        <a href="<?php echo BASE_URL; ?>index.php?action=signup" class="btn btn-primary">Sign Up</a>
                    <?php endif; ?>
                </div>

            </div>
    </div>
</header>
