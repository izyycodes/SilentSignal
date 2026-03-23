<?php
$pageStyles = [BASE_URL . 'assets/css/auth.css', BASE_URL . 'assets/css/forgot-password.css'];
require_once VIEW_PATH . 'includes/home-header.php';
?>


<div class="main-container">
    <div class="back-to-home-wrapper">
        <a href="<?php echo BASE_URL; ?>index.php?action=forgot-password" class="back-to-home">
            <i class="ri-arrow-left-line"></i> Request a new link
        </a>
    </div>

    <div class="fp-card">
        <div class="fp-icon-wrap">
            <div class="fp-icon fp-icon-green">
                <i class="ri-shield-keyhole-line"></i>
            </div>
        </div>

        <h2 class="fp-title">Set New Password</h2>
        <p class="fp-subtitle">Choose a strong password — at least 6 characters long.</p>

        <?php if (isset($_SESSION['rp_error'])): ?>
            <div class="fp-alert fp-alert-error">
                <i class="ri-error-warning-line"></i>
                <?php echo htmlspecialchars($_SESSION['rp_error']); unset($_SESSION['rp_error']); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>index.php?action=process_reset_password" method="POST" id="rpForm">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">

            <div class="fp-form-group">
                <label for="rp-password">New Password</label>
                <div class="fp-input-wrap">
                    <i class="ri-lock-line fp-input-icon"></i>
                    <input type="password" id="rp-password" name="password" class="fp-input"
                           placeholder="Min. 6 characters" required minlength="6" maxlength="72">
                    <i class="fas fa-eye fp-pw-toggle" id="rp-toggle1" onclick="toggleFpPw('rp-password', 'rp-toggle1')"></i>
                </div>
                <div class="fp-strength-bar" id="strengthBar">
                    <div class="fp-strength-fill" id="strengthFill"></div>
                </div>
                <div class="fp-strength-label" id="strengthLabel"></div>
            </div>

            <div class="fp-form-group">
                <label for="rp-confirm">Confirm Password</label>
                <div class="fp-input-wrap">
                    <i class="ri-lock-line fp-input-icon"></i>
                    <input type="password" id="rp-confirm" name="confirm_password" class="fp-input"
                           placeholder="Repeat your new password" required maxlength="72">
                    <i class="fas fa-eye fp-pw-toggle" id="rp-toggle2" onclick="toggleFpPw('rp-confirm', 'rp-toggle2')"></i>
                </div>
                <div class="fp-match-msg" id="matchMsg"></div>
            </div>

            <button type="submit" class="fp-btn" id="rpSubmitBtn">
                <span class="fp-btn-text"><i class="ri-save-line"></i> Save New Password</span>
                <span class="fp-btn-loading" style="display:none;"><i class="ri-loader-4-line ri-spin"></i> Saving...</span>
            </button>
        </form>
    </div>
</div>

<script>
// Toggle password visibility
function toggleFpPw(inputId, iconId) {
    const inp  = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        inp.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password strength meter
const pwInput  = document.getElementById('rp-password');
const fill     = document.getElementById('strengthFill');
const label    = document.getElementById('strengthLabel');

pwInput.addEventListener('input', function () {
    const val = this.value;
    let score = 0;
    if (val.length >= 6)  score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { pct: '20%',  color: '#ef4444', text: 'Very weak' },
        { pct: '40%',  color: '#f97316', text: 'Weak' },
        { pct: '60%',  color: '#eab308', text: 'Fair' },
        { pct: '80%',  color: '#22c55e', text: 'Strong' },
        { pct: '100%', color: '#16a34a', text: 'Very strong' },
    ];
    const lvl = levels[Math.max(0, score - 1)] || levels[0];

    fill.style.width      = val.length ? lvl.pct : '0%';
    fill.style.background = lvl.color;
    label.textContent     = val.length ? lvl.text : '';
    label.style.color     = lvl.color;
});

// Match check
const cfmInput = document.getElementById('rp-confirm');
const matchMsg = document.getElementById('matchMsg');

function checkMatch() {
    if (!cfmInput.value) { matchMsg.textContent = ''; return; }
    if (pwInput.value === cfmInput.value) {
        matchMsg.textContent = '✓ Passwords match';
        matchMsg.className   = 'fp-match-msg fp-match-ok';
    } else {
        matchMsg.textContent = '✗ Passwords do not match';
        matchMsg.className   = 'fp-match-msg fp-match-err';
    }
}
pwInput.addEventListener('input',  checkMatch);
cfmInput.addEventListener('input', checkMatch);

// Loading state on submit
document.getElementById('rpForm').addEventListener('submit', function (e) {
    if (pwInput.value !== cfmInput.value) {
        e.preventDefault();
        matchMsg.textContent = '✗ Passwords do not match';
        matchMsg.className   = 'fp-match-msg fp-match-err';
        return;
    }
    const btn = document.getElementById('rpSubmitBtn');
    btn.querySelector('.fp-btn-text').style.display    = 'none';
    btn.querySelector('.fp-btn-loading').style.display = 'inline-flex';
    btn.disabled = true;
});
</script>

<?php require_once VIEW_PATH . 'includes/home-footer.php'; ?>