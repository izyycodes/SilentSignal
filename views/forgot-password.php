<?php
require_once VIEW_PATH . 'includes/home-header.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/auth.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/forgot-password.css">

<div class="main-container">
    <div class="back-to-home-wrapper">
        <a href="<?php echo BASE_URL; ?>index.php?action=auth" class="back-to-home">
            <i class="ri-arrow-left-line"></i> Back to Login
        </a>
    </div>

    <div class="fp-card">
        <!-- Icon header -->
        <div class="fp-icon-wrap">
            <div class="fp-icon">
                <i class="ri-lock-password-line"></i>
            </div>
        </div>

        <h2 class="fp-title">Forgot Your Password?</h2>
        <p class="fp-subtitle">
            Enter the email address linked to your account and we'll send you a secure reset link.
        </p>

        <!-- Success message -->
        <?php if (isset($_SESSION['fp_success'])): ?>
            <div class="fp-alert fp-alert-success">
                <i class="ri-checkbox-circle-line"></i>
                <?php echo htmlspecialchars($_SESSION['fp_success']); unset($_SESSION['fp_success']); ?>
            </div>
        <?php endif; ?>

        <!-- Error message -->
        <?php if (isset($_SESSION['fp_error'])): ?>
            <div class="fp-alert fp-alert-error">
                <i class="ri-error-warning-line"></i>
                <?php echo htmlspecialchars($_SESSION['fp_error']); unset($_SESSION['fp_error']); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>index.php?action=process_forgot_password" method="POST" id="fpForm">
            <div class="fp-form-group">
                <label for="fp-email">Email Address</label>
                <div class="fp-input-wrap">
                    <i class="ri-mail-line fp-input-icon"></i>
                    <input type="email" id="fp-email" name="email" class="fp-input"
                           placeholder="you@example.com" required autocomplete="email">
                </div>
            </div>

            <button type="submit" class="fp-btn" id="fpSubmitBtn">
                <span class="fp-btn-text"><i class="ri-send-plane-line"></i> Send Reset Link</span>
                <span class="fp-btn-loading" style="display:none;"><i class="ri-loader-4-line ri-spin"></i> Sending...</span>
            </button>
        </form>

        <div class="fp-footer">
            <span>Remember your password?</span>
            <a href="<?php echo BASE_URL; ?>index.php?action=auth">Log in here</a>
        </div>
    </div>
</div>

<script>
document.getElementById('fpForm').addEventListener('submit', function() {
    const btn = document.getElementById('fpSubmitBtn');
    btn.querySelector('.fp-btn-text').style.display = 'none';
    btn.querySelector('.fp-btn-loading').style.display = 'inline-flex';
    btn.disabled = true;
});
</script>

<?php require_once VIEW_PATH . 'includes/home-footer.php'; ?>