<?php
// views/auth.php - Combined Login and Signup with Sliding Animation
// Note: config is already loaded by index.php
// $pageTitle and $isHome are set by controller
require_once VIEW_PATH . 'includes/home-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/auth.css">

<!-- Main Container -->
<div class="main-container">
    <!-- Back to Home Link -->
    <div class="back-to-home-wrapper">
        <a href="<?php echo BASE_URL; ?>index.php?action=home" class="back-to-home">
            <i class="ri-arrow-left-line"></i> Back to Home
        </a>
    </div>

    <!-- Auth Container -->
    <div class="auth-container" id="authContainer">

        <!-- Sign Up Form -->
        <div class="form-container sign-up">
            <form action="<?php echo BASE_URL; ?>index.php?action=process_signup" method="POST">
                <h2>Create Account</h2>

                <!-- Social Login -->
                <div class="social-login">
                    <button type="button" class="social-btn" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </button>
                    <button type="button" class="social-btn" title="Google">
                        <i class="fab fa-google"></i>
                    </button>
                    <button type="button" class="social-btn" title="GitHub">
                        <i class="fab fa-github"></i>
                    </button>
                </div>

                <div class="divider">or use your email for registration:</div>

                <!-- Display Messages for Signup -->
                <?php if (isset($_SESSION['signup_error'])): ?>
                    <div class="alert alert-error">
                        <?php
                        echo htmlspecialchars($_SESSION['signup_error']);
                        unset($_SESSION['signup_error']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fname">First Name</label>
                        <input type="text" name="fname" required>
                    </div>
                    <div class="form-group">
                        <label for="lname">Last Name</label>
                        <input type="text" name="lname" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" pattern="09\d{9}" required>
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
                    <label for="signup-password">Password</label>
                    <div class="password-group">
                        <input type="password" name="password" id="signup-password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('signup-password')"></i>

                    </div>
                </div>

                <button type="submit" class="submit-btn">SIGN UP</button>
            </form>
        </div>

        <!-- Login Form -->
        <div class="form-container login">
            <form action="<?php echo BASE_URL; ?>index.php?action=process_login" method="POST">
                <h2>Login</h2>

                <!-- Social Login -->
                <div class="social-login">
                    <button type="button" class="social-btn" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </button>
                    <button type="button" class="social-btn" title="Google">
                        <i class="fab fa-google"></i>
                    </button>
                    <button type="button" class="social-btn" title="GitHub">
                        <i class="fab fa-github"></i>
                    </button>
                </div>

                <div class="divider">or use your email account:</div>

                <!-- Display Messages for Login -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php
                        echo htmlspecialchars($_SESSION['error']);
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php
                        echo htmlspecialchars($_SESSION['success']);
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="email_phone">Email / Phone Number</label>
                    <input type="text" name="email_phone" required>
                </div>

                <div class="form-group">
                    <label for="login-password">Password</label>
                    <div class="password-group">
                        <input type="password" name="password" id="login-password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('login-password')"></i>
                    </div>
                </div>

                <div class="forgot-link">
                    <a href="#">Forgot password?</a>
                </div>

                <button type="submit" class="submit-btn">LOGIN</button>
            </form>
        </div>

        <!-- Toggle Panels -->
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>To stay connected and access accessible emergency support, please log in using your details.</p>
                    <button type="button" class="toggle-btn" id="login">Login</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your details and start your journey with accessible and reliable emergency support.</p>
                    <button type="button" class="toggle-btn" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . 'includes/home-footer.php'; ?>

<script src="<?php echo BASE_URL; ?>assets/js/auth.js"></script>

</body>

</html>