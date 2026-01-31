<?php
require_once __DIR__ . '/../config/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Silent Signal'; ?></title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/c835d6c14b.js" crossorigin="anonymous"></script>
   
    <link rel="stylesheet" href="assets/css/home-header.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/home-footer.css">
</head>
<body>
    <?php require_once VIEW_PATH . 'includes/home-header.php'; ?>

    <!-- Main Container -->
    <div class="main-container login-container">
        <!-- Login Form Section -->
        <div class="form-section">
            <h2>Login</h2>
            
            <!-- Social Login -->
            <div class="social-login">
                <button class="social-btn" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </button>
                <button class="social-btn" title="Google">
                    <i class="fab fa-google"></i>
                </button>
                <button class="social-btn" title="GitHub">
                    <i class="fab fa-github"></i>
                </button>
            </div>

            <div class="divider">or use your email account</div>

            <!-- Display Messages -->
            <?php
            if(isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            ?>

            <!-- Login Form -->
            <form action="../controllers/AuthController.php?action=login" method="POST">
                <div class="form-group">
                    <label for="email_phone">Email/Phone Number</label>
                    <input type="text" id="email_phone" name="email_phone" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="submit-btn">LOGIN</button>
            </form>
        </div>

        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Hello, Friend!</h1>
            <p>Enter your details and start your journey with accessible and reliable emergency support.</p>
            <a href="index.php?action=signup" class="welcome-btn">SIGN UP</a>
        </div>
    </div>

	 <?php require_once VIEW_PATH . '/includes/home-footer.php'; ?>

</body>
</html>

