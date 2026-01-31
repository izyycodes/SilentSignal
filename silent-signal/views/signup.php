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
    <div class="main-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome Back!</h1>
            <p>To stay connected and access accessible emergency support, please log in using your details.</p>
            <a href="index.php?action=login" class="welcome-btn">LOGIN</a>
        </div>

        <!-- Signup Form Section -->
        <div class="form-section">
            <h2>Create Account</h2>
            
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

            <div class="divider">or use your email for registration</div>

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

            <!-- Signup Form -->
            <form action="../controllers/AuthController.php?action=signup" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" required>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="user">User</option>
                        <option value="responder">Emergency Responder</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="submit-btn">SIGN UP</button>
            </form>
        </div>
    </div>

    <?php require_once VIEW_PATH . 'includes/home-footer.php'; ?>

</body>
</html>
