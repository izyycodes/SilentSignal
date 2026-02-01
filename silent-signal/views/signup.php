<?php
// views/signup.php
// Note: config is already loaded by index.php
// $pageTitle and $isHome are set by controller
require_once VIEW_PATH . 'includes/home-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/login.css">

<!-- Main Container -->
<div class="main-container">
    <!-- Back to Home Link -->
    <div>
        <a href="<?php echo BASE_URL; ?>index.php?action=home" class="back-to-home">
            <i class="ri-arrow-left-line"></i> Back to Home
        </a>
    </div>

    <div class="form-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome Back!</h1>
            <p>To stay connected and access accessible emergency support, please log in using your details.</p>
            <a href="<?php echo BASE_URL; ?>index.php?action=login" class="welcome-btn">LOGIN</a>
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
    
            <div class="divider">or use your email for registration:</div>
    
            <!-- Display Messages -->
            <?php
            if(isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            ?>
    
            <!-- Signup Form -->
            <form action="<?php echo BASE_URL; ?>index.php?action=process_signup" method="POST">
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
                        <option value="" disabled selected>Select Role</option>
                        <option value="pwd">PWD User</option>
                        <option value="family">Family Member</option>
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
</div>

<?php require_once VIEW_PATH . 'includes/home-footer.php'; ?>

</body>
</html>
