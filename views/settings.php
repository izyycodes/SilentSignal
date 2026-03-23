<?php
// views/settings.php
$pageStyles = [BASE_URL . 'assets/css/settings.css'];
require_once VIEW_PATH . 'includes/dashboard-header.php';
?>

<div class="page-container">

    <!-- Page Header -->
    <div class="page-header settings-header">
        <div class="page-header-icon" style="background: linear-gradient(135deg, #1A4D7F 0%, #2361a0 100%);">
            <i class="ri-settings-3-fill"></i>
        </div>
        <div class="page-header-content">
            <h1>Settings</h1>
            <p>Manage your account preferences and security options</p>
        </div>
    </div>

    <div class="settings-layout">

        <!-- ───────────────────────────────────────────────
             SECTION 1 — SECURITY
        ─────────────────────────────────────────────── -->
        <section class="settings-section card">
            <div class="settings-section-header">
                <div class="card-icon purple"><i class="ri-shield-keyhole-line"></i></div>
                <div>
                    <h2>Security</h2>
                    <p class="settings-section-desc">Protect your account with additional verification</p>
                </div>
            </div>

            <div class="settings-rows">
                <!-- MFA Toggle -->
                <div class="settings-row" id="rowMfa">
                    <div class="settings-row-info">
                        <div class="settings-row-icon purple"><i class="ri-mail-lock-line"></i></div>
                        <div>
                            <h4>Two-Factor Authentication (MFA)</h4>
                            <p>Require an email verification code each time you log in</p>
                        </div>
                    </div>
                    <label class="toggle-switch" title="Toggle MFA">
                        <input type="checkbox" id="toggleMfa"
                               <?php echo $userSettings['mfa_enabled'] ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="settings-mfa-note <?php echo $userSettings['mfa_enabled'] ? 'visible' : ''; ?>" id="mfaNote">
                    <i class="ri-information-line"></i>
                    MFA is active. You will receive a 6-digit code at <strong><?php echo htmlspecialchars($_SESSION['user_email']); ?></strong> on every login.
                </div>
            </div>
        </section>

        <!-- ───────────────────────────────────────────────
             SECTION 2 — EMERGENCY ALERT
        ─────────────────────────────────────────────── -->
        <section class="settings-section card">
            <div class="settings-section-header">
                <div class="card-icon orange"><i class="ri-alarm-warning-line"></i></div>
                <div>
                    <h2>Emergency Alert</h2>
                    <p class="settings-section-desc">Configure how SOS alerts are triggered and sent</p>
                </div>
            </div>

            <div class="settings-rows">

                <!-- SOS Countdown -->
                <div class="settings-row">
                    <div class="settings-row-info">
                        <div class="settings-row-icon orange"><i class="ri-timer-line"></i></div>
                        <div>
                            <h4>Auto-Send Countdown</h4>
                            <p>Seconds before SOS alert is automatically sent after trigger</p>
                        </div>
                    </div>
                    <div class="settings-countdown-control">
                        <button class="countdown-btn minus" id="btnMinus" aria-label="Decrease">
                            <i class="ri-subtract-line"></i>
                        </button>
                        <div class="countdown-display">
                            <span id="countdownValue"><?php echo (int)$userSettings['sos_countdown_seconds']; ?></span>
                            <span class="countdown-unit">sec</span>
                        </div>
                        <button class="countdown-btn plus" id="btnPlus" aria-label="Increase">
                            <i class="ri-add-line"></i>
                        </button>
                    </div>
                </div>
                <div class="settings-row-hint">
                    <i class="ri-information-line"></i> Min 5 sec &nbsp;·&nbsp; Max 60 sec
                </div>

                <!-- Shake-to-Alert Toggle -->
                <div class="settings-row" id="rowShake">
                    <div class="settings-row-info">
                        <div class="settings-row-icon orange"><i class="ri-phone-line"></i></div>
                        <div>
                            <h4>Shake-to-Alert</h4>
                            <p>Automatically trigger SOS when your device is shaken vigorously</p>
                        </div>
                    </div>
                    <label class="toggle-switch" title="Toggle Shake-to-Alert">
                        <input type="checkbox" id="toggleShake"
                               <?php echo $userSettings['auto_shake_enabled'] ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="settings-shake-note <?php echo $userSettings['auto_shake_enabled'] ? 'visible' : ''; ?>" id="shakeNote">
                    <i class="ri-information-line"></i>
                    Shake detection is active. This requires the Emergency Alert page to be open in your browser.
                </div>

            </div>
        </section>

        <!-- ───────────────────────────────────────────────
             SECTION 3 — FAMILY & CONTACTS
        ─────────────────────────────────────────────── -->
        <section class="settings-section card">
            <div class="settings-section-header">
                <div class="card-icon green"><i class="ri-team-line"></i></div>
                <div>
                    <h2>Family & Contacts</h2>
                    <p class="settings-section-desc">Control how emergency contacts are managed</p>
                </div>
            </div>

            <div class="settings-rows">

                <!-- Auto-invite contacts -->
                <div class="settings-row" id="rowAutoInvite">
                    <div class="settings-row-info">
                        <div class="settings-row-icon green"><i class="ri-user-add-line"></i></div>
                        <div>
                            <h4>Auto-Invite Emergency Contacts</h4>
                            <p>Automatically send Family Check-in invitations to contacts saved in your Medical Profile</p>
                        </div>
                    </div>
                    <label class="toggle-switch" title="Toggle Auto-Invite">
                        <input type="checkbox" id="toggleAutoInvite"
                               <?php echo $userSettings['auto_invite_contacts'] ? 'checked' : ''; ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="settings-invite-note <?php echo $userSettings['auto_invite_contacts'] ? 'visible' : ''; ?>" id="inviteNote">
                    <i class="ri-information-line"></i>
                    When enabled, contacts added in your Medical Profile are automatically linked to your Family Check-in — no manual setup needed.
                </div>

            </div>
        </section>

        <!-- ───────────────────────────────────────────────
             SECTION 4 — ACCOUNT
        ─────────────────────────────────────────────── -->
        <section class="settings-section card">
            <div class="settings-section-header">
                <div class="card-icon blue"><i class="ri-user-settings-line"></i></div>
                <div>
                    <h2>Account</h2>
                    <p class="settings-section-desc">Your account details</p>
                </div>
            </div>
            <div class="settings-rows">
                <div class="settings-row settings-row-static">
                    <div class="settings-row-info">
                        <div class="settings-row-icon blue"><i class="ri-user-line"></i></div>
                        <div>
                            <h4>Name</h4>
                            <p><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                        </div>
                    </div>
                </div>
                <div class="settings-row settings-row-static">
                    <div class="settings-row-info">
                        <div class="settings-row-icon blue"><i class="ri-mail-line"></i></div>
                        <div>
                            <h4>Email</h4>
                            <p><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                        </div>
                    </div>
                </div>
                <div class="settings-row settings-row-static">
                    <div class="settings-row-info">
                        <div class="settings-row-icon blue"><i class="ri-smartphone-line"></i></div>
                        <div>
                            <h4>Phone</h4>
                            <p><?php echo htmlspecialchars($_SESSION['user_phone'] ?? 'Not set'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="settings-row settings-row-static">
                    <div class="settings-row-info">
                        <div class="settings-row-icon blue"><i class="ri-shield-user-line"></i></div>
                        <div>
                            <h4>Role</h4>
                            <p><?php echo ucfirst($_SESSION['user_role']); ?></p>
                        </div>
                    </div>
                </div>
                <div class="settings-actions">
                    <a href="<?php echo BASE_URL; ?>index.php?action=forgot-password" class="btn-settings-link">
                        <i class="ri-lock-password-line"></i> Change Password
                    </a>
                </div>
            </div>
        </section>

        <!-- Save Button -->
        <div class="settings-save-bar">
            <button class="btn-settings-save" id="btnSaveSettings">
                <i class="ri-save-line"></i> Save Settings
            </button>
            <span class="settings-save-status" id="saveStatus"></span>
        </div>

    </div><!-- /.settings-layout -->
</div><!-- /.page-container -->

<!-- Hidden values passed to JS -->
<script>
const BASE_URL              = <?php echo json_encode(BASE_URL); ?>;
const initialCountdown      = <?php echo (int)$userSettings['sos_countdown_seconds']; ?>;
</script>

<?php require_once VIEW_PATH . 'includes/dashboard-footer.php'; ?>
<script src="<?php echo BASE_URL; ?>assets/js/settings.js"></script>
</body>
</html>
