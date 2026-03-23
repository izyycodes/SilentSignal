<?php
// views/mfa-verify.php
$pageStyles = [BASE_URL . 'assets/css/auth.css', BASE_URL . 'assets/css/mfa-verify.css'];
require_once VIEW_PATH . 'includes/home-header.php';
?>

<div class="main-container">
    <div class="back-to-home-wrapper">
        <a href="<?php echo BASE_URL; ?>index.php?action=auth" class="back-to-home">
            <i class="ri-arrow-left-line"></i> Back to Login
        </a>
    </div>

    <div class="mfa-container">
        <div class="mfa-card">
            <!-- Icon -->
            <div class="mfa-icon-wrap">
                <i class="ri-shield-keyhole-fill"></i>
            </div>

            <h2>Two-Factor Authentication</h2>
            <p class="mfa-subtitle">
                We sent a 6-digit code to<br>
                <strong><?php echo htmlspecialchars($maskedEmail); ?></strong>
            </p>

            <!-- Messages -->
            <?php if (isset($_SESSION['mfa_error'])): ?>
                <div class="mfa-alert mfa-alert-error">
                    <i class="ri-error-warning-line"></i>
                    <?php echo htmlspecialchars($_SESSION['mfa_error']); unset($_SESSION['mfa_error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['mfa_info'])): ?>
                <div class="mfa-alert mfa-alert-info">
                    <i class="ri-information-line"></i>
                    <?php echo htmlspecialchars($_SESSION['mfa_info']); unset($_SESSION['mfa_info']); ?>
                </div>
            <?php endif; ?>

            <!-- OTP Form -->
            <form action="<?php echo BASE_URL; ?>index.php?action=process_mfa_verify" method="POST" id="mfaForm">
                <div class="otp-inputs" id="otpInputs">
                    <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" autofocus>
                    <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
                </div>
                <!-- Hidden combined input -->
                <input type="hidden" name="otp_code" id="otpCode">

                <button type="submit" class="mfa-submit-btn" id="mfaSubmitBtn" disabled>
                    <i class="ri-shield-check-line"></i> Verify Code
                </button>
            </form>

            <!-- Expiry countdown -->
            <p class="mfa-expiry">Code expires in <span id="mfaTimer">10:00</span></p>

            <!-- Resend -->
            <div class="mfa-resend">
                Didn't receive it?
                <a href="<?php echo BASE_URL; ?>index.php?action=resend_mfa_code" id="resendLink" class="resend-link">
                    Resend Code
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . 'includes/home-footer.php'; ?>

<script>
(function () {
    const inputs = document.querySelectorAll('.otp-digit');
    const hiddenInput = document.getElementById('otpCode');
    const submitBtn = document.getElementById('mfaSubmitBtn');

    function updateHidden() {
        hiddenInput.value = [...inputs].map(i => i.value).join('');
        submitBtn.disabled = hiddenInput.value.length < 6;
    }

    inputs.forEach((inp, idx) => {
        inp.addEventListener('input', () => {
            inp.value = inp.value.replace(/\D/, '');
            if (inp.value && idx < inputs.length - 1) inputs[idx + 1].focus();
            updateHidden();
            if (hiddenInput.value.length === 6) submitBtn.click();
        });
        inp.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !inp.value && idx > 0) inputs[idx - 1].focus();
        });
        inp.addEventListener('paste', e => {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
            pasted.split('').forEach((ch, i) => { if (inputs[i]) inputs[i].value = ch; });
            updateHidden();
            const next = inputs[Math.min(pasted.length, inputs.length - 1)];
            if (next) next.focus();
            if (pasted.length === 6) submitBtn.click();
        });
    });

    // Countdown timer: 10 minutes
    let seconds = 600;
    const timerEl = document.getElementById('mfaTimer');
    const tick = setInterval(() => {
        seconds--;
        const m = String(Math.floor(seconds / 60)).padStart(2, '0');
        const s = String(seconds % 60).padStart(2, '0');
        timerEl.textContent = `${m}:${s}`;
        if (seconds <= 0) {
            clearInterval(tick);
            timerEl.textContent = 'Expired';
            timerEl.style.color = '#e53935';
            submitBtn.disabled = true;
            submitBtn.textContent = 'Code Expired';
        }
    }, 1000);
})();
</script>
</body>
</html>
