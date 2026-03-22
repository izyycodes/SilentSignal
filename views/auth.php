<?php
// views/auth.php - Combined Login and Signup with Sliding Animation
// Note: config is already loaded by index.php
// $pageTitle and $isHome are set by controller
require_once VIEW_PATH . 'includes/home-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/auth.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/forgot-password.css">

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
            <form action="<?php echo BASE_URL; ?>index.php?action=process_signup" method="POST" enctype="multipart/form-data">
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
                        <label for="fname">First Name <span class="pwd-field-required">*</span></label>
                        <input type="text" name="fname" required>
                    </div>
                    <div class="form-group">
                        <label for="lname">Last Name <span class="pwd-field-required">*</span></label>
                        <input type="text" name="lname" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="pwd-field-required">*</span></label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number <span class="pwd-field-required">*</span></label>
                    <input type="tel" name="phone" pattern="09\d{9}" required>
                </div>

                <div class="form-group">
                    <label for="role">Role <span class="pwd-field-required">*</span></label>
                    <select id="role" name="role" required onchange="togglePwdFields(this.value)">
                        <option value="" disabled selected>Select Role</option>
                        <option value="pwd">PWD User</option>
                        <option value="family">Family Member</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <!-- PWD-only fields -->
                <div id="pwdFields" style="display:none; width:100%;">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="pwd_id">PWD ID Number <span class="pwd-field-required">*</span></label>
                            <input type="text" name="pwd_id" id="pwd_id" placeholder="e.g. 06-10-01-001-0000123" maxlength="25" oninput="formatPwdId(this)">
                            <span class="field-hint"><i class="ri-information-line"></i> RR-PP-MM-BBB-NNNNNNN</span>
                        </div>
                        <div class="form-group">
                            <label for="pwd_id_photo">PWD ID Photo <span class="pwd-field-required">*</span></label>
                            <div class="pwd-upload-box" id="pwdUploadBox" onclick="document.getElementById('pwd_id_photo').click()">
                                <div class="pwd-upload-placeholder" id="pwdUploadPlaceholder">
                                    <i class="ri-id-card-line"></i>
                                    <div class="pwd-upload-text">
                                        <span>Click to upload</span>
                                        <small>JPG, PNG or PDF · Max 5MB</small>
                                    </div>
                                </div>
                                <div class="pwd-upload-preview" id="pwdUploadPreview" style="display:none;">
                                    <img id="pwdPreviewImg" src="" alt="PWD ID Preview">
                                    <div class="pwd-preview-overlay">
                                        <i class="ri-refresh-line"></i> Change
                                    </div>
                                </div>
                            </div>
                            <input type="file" name="pwd_id_photo" id="pwd_id_photo"
                                accept="image/jpeg,image/png,image/jpg,application/pdf"
                                style="display:none;" onchange="previewPwdId(this)">
                            <span class="pwd-file-name" id="pwdFileName"></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="signup-password">Password <span class="pwd-field-required">*</span></label>
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

                <!-- Remember Me + Forgot Password on same row -->
                <div class="remember-forgot-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember_me" id="rememberMe" value="1">
                        <span class="remember-custom-checkbox"></span>
                        <span class="remember-text">Remember me</span>
                    </label>
                    <a href="<?php echo BASE_URL; ?>index.php?action=forgot-password" class="forgot-link-inline">Forgot password?</a>
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

<script>
    // Pre-fill email and check Remember Me if cookie is set
    (function() {
        function getCookie(name) {
            const match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)'));
            return match ? decodeURIComponent(match[1]) : null;
        }
        const savedEmail = getCookie('ss_remember_email');
        if (savedEmail) {
            const emailInput = document.querySelector('input[name="email_phone"]');
            const checkbox = document.getElementById('rememberMe');
            if (emailInput) emailInput.value = savedEmail;
            if (checkbox) checkbox.checked = true;
        }
    })();

    function togglePwdFields(role) {
        const fields = document.getElementById('pwdFields');
        const pwdIdIn = document.getElementById('pwd_id');
        const pwdPhoto = document.getElementById('pwd_id_photo');
        if (role === 'pwd') {
            fields.style.display = 'block';
            pwdIdIn.required = true;
            pwdPhoto.required = true;
        } else {
            fields.style.display = 'none';
            pwdIdIn.required = false;
            pwdPhoto.required = false;
        }
    }

    function previewPwdId(input) {
        const file = input.files[0];
        const placeholder = document.getElementById('pwdUploadPlaceholder');
        const preview = document.getElementById('pwdUploadPreview');
        const img = document.getElementById('pwdPreviewImg');
        const fileName = document.getElementById('pwdFileName');

        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            alert('File is too large. Maximum size is 5MB.');
            input.value = '';
            return;
        }

        fileName.textContent = file.name;

        if (file.type === 'application/pdf') {
            placeholder.innerHTML = '<i class="ri-file-pdf-line" style="color:#ef4444;font-size:32px;"></i><span>' + file.name + '</span><small>PDF selected ✓</small>';
            preview.style.display = 'none';
            placeholder.style.display = 'flex';
        } else {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                placeholder.style.display = 'none';
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function formatPwdId(input) {
        // Strip everything except digits
        let raw = input.value.replace(/\D/g, '');

        let formatted = '';
        if (raw.length > 0) formatted = raw.substring(0, 2); // RR
        if (raw.length > 2) formatted += '-' + raw.substring(2, 4); // PP
        if (raw.length > 4) formatted += '-' + raw.substring(4, 6); // MM
        if (raw.length > 6) formatted += '-' + raw.substring(6, 9); // BBB
        if (raw.length > 9) formatted += '-' + raw.substring(9, 16); // NNNNNNN

        input.value = formatted;
    }
</script>

</body>

</html>