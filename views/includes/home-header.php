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

    <!-- Theme (must load before other stylesheets to provide variables) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/theme.css">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/home.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/home-header.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/home-footer.css">

    <!-- Anti-flash: apply saved theme before first paint -->
    <script>
        (function(){
            var t = localStorage.getItem('ss-theme');
            if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <?php if (!empty($pageStyles)): foreach ((array)$pageStyles as $href): ?>
        <link rel="stylesheet" href="<?php echo $href; ?>">
    <?php endforeach; endif; ?>
</head>

<body>
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <!-- Logo -->
                <a href="<?php echo BASE_URL; ?>index.php?action=home" class="logo">
                    <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Silent Signal Logo" class="logo-icon">
                    <span class="logo-text">Silent Signal.</span>
                </a>

                <!-- Hamburger Menu Button -->
                <button class="hamburger" aria-label="Toggle menu">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>

                <!-- Navigation Links Container -->
                <div class="nav-container">
                    <ul class="nav-menu">
                        <li><a href="<?php echo isset($isHome) && $isHome ? '#home' : BASE_URL . 'index.php?action=home#home'; ?>" class="nav-link">Home</a></li>
                        <li><a href="<?php echo isset($isHome) && $isHome ? '#features' : BASE_URL . 'index.php?action=home#features'; ?>" class="nav-link">Features</a></li>
                        <li><a href="<?php echo isset($isHome) && $isHome ? '#how-it-works' : BASE_URL . 'index.php?action=home#how-it-works'; ?>" class="nav-link">How It Works</a></li>
                        <li><a href="<?php echo isset($isHome) && $isHome ? '#contact' : BASE_URL . 'index.php?action=home#contact'; ?>" class="nav-link">Contact</a></li>
                    </ul>

                    <div class="nav-buttons">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <!-- Logged in user -->
                            <?php
                            // Determine dashboard URL based on user role
                            $dashboardUrl = BASE_URL . 'index.php?action=';
                            if (isset($_SESSION['user_role'])) {
                                switch ($_SESSION['user_role']) {
                                    case 'admin':
                                        $dashboardUrl .= 'admin-dashboard';
                                        break;
                                    case 'family':
                                        $dashboardUrl .= 'family-dashboard';
                                        break;
                                    case 'pwd':
                                    default:
                                        $dashboardUrl .= 'dashboard';
                                        break;
                                }
                            } else {
                                $dashboardUrl .= 'dashboard'; // fallback
                            }
                            ?>
                            <a href="<?php echo $dashboardUrl; ?>" class="btn btn-secondary">Dashboard</a>
                            <a href="<?php echo BASE_URL; ?>index.php?action=logout" class="btn btn-primary">Logout</a>
                        <?php else: ?>
                            <!-- Guest user -->
                            <a href="<?php echo BASE_URL; ?>index.php?action=auth" class="btn btn-secondary">Login</a>
                            <a href="<?php echo BASE_URL; ?>index.php?action=auth&mode=signup" class="btn btn-primary">Sign Up</a>
                        <?php endif; ?>

                        <!-- Theme Toggle (desktop: icon button; mobile: shown as full button below) -->
                        <button class="theme-toggle-btn" aria-label="Toggle dark/light mode">
                            <i class="ri-sun-line icon-sun"></i>
                            <i class="ri-moon-line icon-moon"></i>
                        </button>
                    </div>

                    <!-- Mobile-only dark/light mode button (full width, inside drawer) -->
                    <button class="theme-toggle-mobile" aria-label="Toggle dark/light mode">
                        <span class="theme-toggle-mobile-light">
                            <i class="ri-moon-line"></i> Switch to Dark Mode
                        </span>
                        <span class="theme-toggle-mobile-dark">
                            <i class="ri-sun-line"></i> Switch to Light Mode
                        </span>
                    </button>
                </div>
            </nav>
        </div>
    </header>