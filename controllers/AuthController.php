<?php
// controllers/AuthController.php
// Handles authentication: login, signup, logout

class AuthController {
    
    private $user;
    
    public function __construct() {
        // Load dependencies
        require_once __DIR__ . '/../models/User.php';
        
        $this->user = new User();
    }
    
    /**
     * Show auth page (login/signup)
     */
    public function showAuth() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirectToDashboard();
        }

        // ── Auto-login via Remember Me cookie ────────────────────────────
        if (isset($_COOKIE['ss_remember'])) {
            try {
                require_once __DIR__ . '/../config/Database.php';
                $db   = (new Database())->getConnection();
                $hash = hash('sha256', $_COOKIE['ss_remember']);
                $stmt = $db->prepare("
                    SELECT rt.user_id, u.fname, u.lname, u.email, u.phone_number, u.role
                    FROM remember_tokens rt
                    JOIN users u ON u.id = rt.user_id
                    WHERE rt.token_hash = ? AND rt.expires_at > NOW()
                    LIMIT 1
                ");
                $stmt->execute([$hash]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    require_once __DIR__ . '/../models/User.php';
                    $userModel = new User();
                    if ($userModel->isUserActive($user['user_id'])) {
                        $_SESSION['user_id']    = $user['user_id'];
                        $_SESSION['user_name']  = $user['fname'] . ' ' . $user['lname'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_role']  = $user['role'];
                        $_SESSION['user_phone'] = $user['phone_number'];
                        $this->redirectToDashboard();
                    }
                }
            } catch (Exception $e) {
                // Cookie invalid — clear it silently
                setcookie('ss_remember', '', time() - 3600, '/');
            }
        }
        // ─────────────────────────────────────────────────────────────────

        $pageTitle = "Login - Silent Signal";
        require_once VIEW_PATH . 'auth.php';
    }
    
    /**
     * Process login
     */
    public function processLogin() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
        
        $email_or_phone = trim($_POST['email_phone'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validate inputs
        if (empty($email_or_phone) || empty($password)) {
            $_SESSION['error'] = "All fields are required!";
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
        
        // Attempt login using User model
        if ($this->user->login($email_or_phone, $password)) {

            // ── MFA Check ─────────────────────────────────────────────────────────────
            require_once __DIR__ . '/../models/UserSettings.php';
            $settingsModel = new UserSettings();
            if ($settingsModel->isMfaEnabled($this->user->id)) {
                // Store pending login data in session (not full login yet)
                $_SESSION['mfa_pending_id']    = $this->user->id;
                $_SESSION['mfa_pending_name']  = $this->user->fname . ' ' . $this->user->lname;
                $_SESSION['mfa_pending_email'] = $this->user->email;
                $_SESSION['mfa_pending_role']  = $this->user->role;
                $_SESSION['mfa_pending_phone'] = $this->user->phone_number;
                $_SESSION['mfa_remember_me']   = !empty($_POST['remember_me']);

                // Generate and send OTP
                $code = $settingsModel->generateMfaCode($this->user->id);
                $this->sendMfaEmail($this->user->email, $this->user->fname, $code);

                header("Location: " . BASE_URL . "index.php?action=mfa-verify");
                exit();
            }
            // ──────────────────────────────────────────────────────────────────────

            // Set session variables
            $_SESSION['user_id']    = $this->user->id;
            $_SESSION['user_name']  = $this->user->fname . ' ' . $this->user->lname;
            $_SESSION['user_email'] = $this->user->email;
            $_SESSION['user_role']  = $this->user->role;
            $_SESSION['user_phone'] = $this->user->phone_number;

            // ── Remember Me ──────────────────────────────────────────────
            if (!empty($_POST['remember_me'])) {
                $token  = bin2hex(random_bytes(32));
                $expiry = time() + (30 * 24 * 60 * 60); // 30 days

                // Store hashed token in DB — use MySQL NOW() to avoid timezone mismatch
                require_once __DIR__ . '/../config/Database.php';
                $db   = (new Database())->getConnection();
                $hash = hash('sha256', $token);
                $db->prepare("
                    INSERT INTO remember_tokens (user_id, token_hash, expires_at)
                    VALUES (?, ?, NOW() + INTERVAL 30 DAY)
                    ON DUPLICATE KEY UPDATE token_hash = VALUES(token_hash), expires_at = NOW() + INTERVAL 30 DAY
                ")->execute([$this->user->id, $hash]);

                // Secure token cookie for auto-login
                setcookie('ss_remember', $token, [
                    'expires'  => $expiry,
                    'path'     => '/',
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);

                // Separate plain cookie to pre-fill email on login form (readable by JS)
                setcookie('ss_remember_email', $this->user->email, [
                    'expires'  => $expiry,
                    'path'     => '/',
                    'httponly' => false,
                    'samesite' => 'Lax',
                ]);
            } else {
                // If remember me not checked, only clear the auth token cookie
                setcookie('ss_remember', '', time() - 3600, '/');
            }
            // ─────────────────────────────────────────────────────────────

            $_SESSION['success'] = "Login successful! Welcome back, " . $this->user->fname . "!";
            $this->redirectToDashboard();

        } else {
            if ($this->user->loginError === 'deactivated') {
                $_SESSION['error'] = "Your account has been deactivated. Please contact the administrator.";
            } else {
                $_SESSION['error'] = "Invalid email/phone or password.";
            }
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
    }
    
    /**
     * Process signup
     */
    public function processSignup() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
        
        // Get form data
        $fname = trim($_POST['fname'] ?? '');
        $lname = trim($_POST['lname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'pwd';
        
        // Validate inputs
        $errors = [];
        
        if (empty($fname)) {
            $errors[] = "First name is required.";
        }
        
        if (empty($lname)) {
            $errors[] = "Last name is required.";
        }
        
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        } else {
            // Check if email exists
            $this->user->email = $email;
            if ($this->user->emailExists()) {
                $errors[] = "Email already registered.";
            }
        }
        
        if (!empty($phone)) {
            $this->user->phone_number = $phone;
            if ($this->user->phoneExists()) {
                $errors[] = "Phone number already registered.";
            }
        }
        
        if (empty($password)) {
            $errors[] = "Password is required.";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters.";
        }
        
        // Validate role
        $allowedRoles = ['pwd', 'family', 'admin'];
        if (!in_array($role, $allowedRoles)) {
            $role = 'pwd';
        }

        // PWD-specific validation
        $pwdId        = '';
        $pwdPhotoFile = '';
        if ($role === 'pwd') {
            $pwdId = trim($_POST['pwd_id'] ?? '');
            if (empty($pwdId)) {
                $errors[] = "PWD ID Number is required.";
            }
            if (empty($_FILES['pwd_id_photo']['name'])) {
                $errors[] = "PWD ID photo is required.";
            } else {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
                $fileType     = mime_content_type($_FILES['pwd_id_photo']['tmp_name']);
                $fileSize     = $_FILES['pwd_id_photo']['size'];
                $fileError    = $_FILES['pwd_id_photo']['error'];
                if ($fileError !== UPLOAD_ERR_OK) {
                    $errors[] = "Photo upload failed. Please try again.";
                } elseif (!in_array($fileType, $allowedTypes)) {
                    $errors[] = "Invalid file type. Only JPG, PNG, or PDF allowed.";
                } elseif ($fileSize > 5 * 1024 * 1024) {
                    $errors[] = "Photo file size must not exceed 5MB.";
                }
            }
        }
        
        // If errors, redirect back
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
        
        // Set user data
        $this->user->fname = $fname;
        $this->user->lname = $lname;
        $this->user->email = $email;
        $this->user->phone_number = $phone;
        $this->user->password = $password;
        $this->user->role = $role;
        
        // Create user
        if ($this->user->create()) {
            // Auto-login after signup
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['user_name'] = $fname . ' ' . $lname;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;
            $_SESSION['user_phone'] = $phone;
            
            // Create empty medical profile for PWD users
            if ($role === 'pwd') {

                // ▼ NEW: Move uploaded PWD ID photo
                $savedPhoto = '';
                if (!empty($_FILES['pwd_id_photo']['tmp_name'])) {
                    $ext       = strtolower(pathinfo($_FILES['pwd_id_photo']['name'], PATHINFO_EXTENSION));
                    $filename  = 'pwd_' . $this->user->id . '_' . time() . '.' . $ext;
                    $uploadDir = __DIR__ . '/../assets/uploads/pwd-ids/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    if (move_uploaded_file($_FILES['pwd_id_photo']['tmp_name'], $uploadDir . $filename)) {
                        $savedPhoto = $filename;
                        $this->user->savePwdIdPhoto($this->user->id, $savedPhoto);
                    }
                }
                // ▲ END NEW

                require_once __DIR__ . '/../models/MedicalProfile.php';
                $medicalProfile = new MedicalProfile();
                $profileData = [
                    'first_name' => $fname,
                    'last_name' => $lname,
                    'email' => $email,
                    'phone' => $phone,
                    'date_of_birth' => null,
                    'gender' => '',
                    'pwd_id' => $pwdId, // ← CHANGED: was empty string ''
                    'street_address' => '',
                    'city' => '',
                    'province' => '',
                    'zip_code' => '',
                    'disability_type' => '',
                    'blood_type' => '',
                    'allergies' => [],
                    'medications' => [],
                    'medical_conditions' => [],
                    'emergency_contacts' => [],
                    'sms_template' => '',
                    'medication_reminders' => []
                ];
                $medicalProfile->saveProfile($this->user->id, $profileData);
                
                $_SESSION['success'] = "Registration successful! Please complete your medical profile.";
                header("Location: " . BASE_URL . "index.php?action=medical-profile");
                exit();
            } else {
                $_SESSION['success'] = "Registration successful! Welcome to Silent Signal!";
                $this->redirectToDashboard();
            }
        } else {
            $_SESSION['error'] = "Registration failed. Please try again.";
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
    }
    
    /**
     * Process logout
     */
    public function logout() {
        // Clear Remember Me auth token & DB row — but keep email cookie for pre-fill
        if (isset($_COOKIE['ss_remember'])) {
            try {
                require_once __DIR__ . '/../config/Database.php';
                $db   = (new Database())->getConnection();
                $hash = hash('sha256', $_COOKIE['ss_remember']);
                $db->prepare("DELETE FROM remember_tokens WHERE token_hash = ?")->execute([$hash]);
            } catch (Exception $e) {}
            setcookie('ss_remember', '', time() - 3600, '/');
        }

        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
        header("Location: " . BASE_URL . "index.php?action=home");
        exit();
    }

    // =========================================================================
    // FORGOT PASSWORD — show form
    // =========================================================================
    public function showForgotPassword() {
        if (isset($_SESSION['user_id'])) { $this->redirectToDashboard(); }
        $pageTitle = "Forgot Password - Silent Signal";
        require_once VIEW_PATH . 'forgot-password.php';
    }

    // =========================================================================
    // FORGOT PASSWORD — process email submission
    // =========================================================================
    public function processForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "index.php?action=forgot-password");
            exit();
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['fp_error'] = "Please enter a valid email address.";
            header("Location: " . BASE_URL . "index.php?action=forgot-password");
            exit();
        }

        require_once __DIR__ . '/../config/Database.php';
        $db = (new Database())->getConnection();

        // Always show the same success message to prevent email enumeration
        $_SESSION['fp_success'] = "If an account with that email exists, a reset link has been sent. Please check your inbox (and spam folder).";

        // Look up user
        $stmt = $db->prepare("SELECT id, fname FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token  = bin2hex(random_bytes(32));

            // Save token — use MySQL NOW() to avoid PHP/MySQL timezone mismatch
            $db->prepare("
                INSERT INTO password_resets (user_id, token_hash, expires_at)
                VALUES (?, ?, NOW() + INTERVAL 1 HOUR)
                ON DUPLICATE KEY UPDATE token_hash = VALUES(token_hash), expires_at = NOW() + INTERVAL 1 HOUR
            ")->execute([$user['id'], hash('sha256', $token)]);

            // Use & in the actual URL (not &amp;) — &amp; is only for HTML attributes
            $resetLink = BASE_URL . "index.php?action=reset-password&token=" . rawurlencode($token);
            $this->sendResetEmail($email, $user['fname'], $resetLink);
        }

        header("Location: " . BASE_URL . "index.php?action=forgot-password");
        exit();
    }

    // =========================================================================
    // RESET PASSWORD — show form
    // =========================================================================
    public function showResetPassword() {
        if (isset($_SESSION['user_id'])) { $this->redirectToDashboard(); }

        $token = trim($_GET['token'] ?? '');
        if (empty($token)) {
            $_SESSION['error'] = "Invalid or missing reset token.";
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }

        // Validate token
        require_once __DIR__ . '/../config/Database.php';
        $db   = (new Database())->getConnection();
        $hash = hash('sha256', $token);
        $stmt = $db->prepare("SELECT user_id FROM password_resets WHERE token_hash = ? AND expires_at > NOW() LIMIT 1");
        $stmt->execute([$hash]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $_SESSION['error'] = "This reset link has expired or is invalid. Please request a new one.";
            header("Location: " . BASE_URL . "index.php?action=forgot-password");
            exit();
        }

        $pageTitle = "Reset Password - Silent Signal";
        require_once VIEW_PATH . 'reset-password.php';
    }

    // =========================================================================
    // RESET PASSWORD — process new password
    // =========================================================================
    public function processResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }

        $token    = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($password) || empty($confirm)) {
            $_SESSION['rp_error'] = "All fields are required.";
            header("Location: " . BASE_URL . "index.php?action=reset-password&token=" . urlencode($token));
            exit();
        }

        if (strlen($password) < 6) {
            $_SESSION['rp_error'] = "Password must be at least 6 characters.";
            header("Location: " . BASE_URL . "index.php?action=reset-password&token=" . urlencode($token));
            exit();
        }

        if (strlen($password) > 72) {
            $_SESSION['rp_error'] = "Password cannot exceed 72 characters.";
            header("Location: " . BASE_URL . "index.php?action=reset-password&token=" . urlencode($token));
            exit();
        }

        if ($password !== $confirm) {
            $_SESSION['rp_error'] = "Passwords do not match.";
            header("Location: " . BASE_URL . "index.php?action=reset-password&token=" . urlencode($token));
            exit();
        }

        require_once __DIR__ . '/../config/Database.php';
        $db   = (new Database())->getConnection();
        $hash = hash('sha256', $token);

        $stmt = $db->prepare("SELECT user_id FROM password_resets WHERE token_hash = ? AND expires_at > NOW() LIMIT 1");
        $stmt->execute([$hash]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $_SESSION['error'] = "This reset link has expired or is invalid. Please request a new one.";
            header("Location: " . BASE_URL . "index.php?action=forgot-password");
            exit();
        }

        // Update password
        $newHash = password_hash($password, PASSWORD_BCRYPT);
        $db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$newHash, $row['user_id']]);

        // Delete used token
        $db->prepare("DELETE FROM password_resets WHERE token_hash = ?")->execute([$hash]);

        $_SESSION['success'] = "Your password has been reset successfully. Please log in with your new password.";
        header("Location: " . BASE_URL . "index.php?action=auth");
        exit();
    }

    // =========================================================================
    // HELPER — send reset email via PHPMailer
    // =========================================================================
    private function sendResetEmail($toEmail, $toName, $resetLink) {
        require_once BASE_PATH . 'vendor/autoload.php';

        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ssilentsignal@gmail.com';
            $mail->Password   = 'rnfa bxze eyix tmjw';
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom(CONTACT_EMAIL, 'Silent Signal');
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Silent Signal Password';
            $mail->Body    = $this->buildResetEmailHtml($toName, $resetLink);
            $mail->AltBody = "Hi {$toName},\n\nYou requested a password reset for your Silent Signal account.\n\nReset your password here:\n{$resetLink}\n\nThis link expires in 1 hour. If you did not request this, ignore this email.\n\n— Silent Signal Team";

            $mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log("Password reset email failed for {$toEmail}: " . ($mail->ErrorInfo ?? $e->getMessage()));
        }
    }

    private function buildResetEmailHtml($name, $resetLink) {
        // In HTML attributes, & must be &amp; to be valid — email clients are strict about this
        $safeLink = str_replace('&', '&amp;', $resetLink);
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f5f7fa;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fa;padding:40px 20px;">
    <tr><td align="center">
      <table width="520" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#1A4D7F,#2d6a9f);padding:32px 40px;text-align:center;">
            <div style="font-size:28px;font-weight:700;color:#ffffff;letter-spacing:0.5px;">🔒 Silent Signal</div>
            <div style="font-size:14px;color:rgba(255,255,255,0.8);margin-top:6px;">Emergency Communication for PWD</div>
          </td>
        </tr>
        <!-- Body -->
        <tr>
          <td style="padding:36px 40px;">
            <h2 style="font-size:22px;color:#1a1a2e;margin:0 0 12px;">Reset Your Password</h2>
            <p style="font-size:15px;color:#555;line-height:1.7;margin:0 0 24px;">
              Hi <strong>{$name}</strong>,<br>
              We received a request to reset the password for your Silent Signal account. Click the button below to set a new password.
            </p>
            <div style="text-align:center;margin:28px 0;">
              <a href="{$safeLink}" style="display:inline-block;padding:14px 36px;background:linear-gradient(135deg,#1A4D7F,#2d6a9f);color:#ffffff;text-decoration:none;border-radius:10px;font-size:15px;font-weight:600;letter-spacing:0.3px;">Reset My Password</a>
            </div>
            <p style="font-size:13px;color:#888;line-height:1.6;margin:0 0 12px;">
              This link will expire in <strong>1 hour</strong>. If you didn't request a password reset, you can safely ignore this email — your password will remain unchanged.
            </p>
            <p style="font-size:12px;color:#aaa;word-break:break-all;">
              If the button doesn't work, copy and paste this link into your browser:<br>
              <a href="{$safeLink}" style="color:#1A4D7F;">{$resetLink}</a>
            </p>
          </td>
        </tr>
        <!-- Footer -->
        <tr>
          <td style="background:#f8f9fa;padding:20px 40px;text-align:center;border-top:1px solid #eee;">
            <p style="font-size:12px;color:#999;margin:0;">© 2026 Silent Signal · Bacolod City, Philippines</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }


    
    // =========================================================================
    // MFA VERIFICATION — Show page
    // =========================================================================
    public function showMfaVerify() {
        if (!isset($_SESSION['mfa_pending_id'])) {
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
        $pageTitle = "Two-Factor Authentication - Silent Signal";
        $maskedEmail = $this->maskEmail($_SESSION['mfa_pending_email']);
        require_once VIEW_PATH . 'mfa-verify.php';
    }

    // =========================================================================
    // MFA VERIFICATION — Process submitted code
    // =========================================================================
    public function processMfaVerify() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }

        if (!isset($_SESSION['mfa_pending_id'])) {
            $_SESSION['error'] = "Session expired. Please login again.";
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }

        $code   = trim($_POST['otp_code'] ?? '');
        $userId = $_SESSION['mfa_pending_id'];

        require_once __DIR__ . '/../models/UserSettings.php';
        $settingsModel = new UserSettings();

        if (!$settingsModel->verifyMfaCode($userId, $code)) {
            $_SESSION['mfa_error'] = "Invalid or expired code. Please try again.";
            header("Location: " . BASE_URL . "index.php?action=mfa-verify");
            exit();
        }

        // Code correct — complete the login
        $_SESSION['user_id']    = $_SESSION['mfa_pending_id'];
        $_SESSION['user_name']  = $_SESSION['mfa_pending_name'];
        $_SESSION['user_email'] = $_SESSION['mfa_pending_email'];
        $_SESSION['user_role']  = $_SESSION['mfa_pending_role'];
        $_SESSION['user_phone'] = $_SESSION['mfa_pending_phone'];
        $rememberMe             = $_SESSION['mfa_remember_me'] ?? false;

        // Clean up MFA pending keys
        unset($_SESSION['mfa_pending_id'], $_SESSION['mfa_pending_name'],
              $_SESSION['mfa_pending_email'], $_SESSION['mfa_pending_role'],
              $_SESSION['mfa_pending_phone'], $_SESSION['mfa_remember_me'],
              $_SESSION['mfa_error']);

        // Handle Remember Me if it was requested
        if ($rememberMe) {
            $token  = bin2hex(random_bytes(32));
            $expiry = time() + (30 * 24 * 60 * 60);
            require_once __DIR__ . '/../config/Database.php';
            $db   = (new Database())->getConnection();
            $hash = hash('sha256', $token);
            $db->prepare("
                INSERT INTO remember_tokens (user_id, token_hash, expires_at)
                VALUES (?, ?, NOW() + INTERVAL 30 DAY)
                ON DUPLICATE KEY UPDATE token_hash = VALUES(token_hash), expires_at = NOW() + INTERVAL 30 DAY
            ")->execute([$_SESSION['user_id'], $hash]);
            setcookie('ss_remember', $token, ['expires' => $expiry, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
            setcookie('ss_remember_email', $_SESSION['user_email'], ['expires' => $expiry, 'path' => '/', 'httponly' => false, 'samesite' => 'Lax']);
        }

        $_SESSION['success'] = "Login successful! Welcome back, " . explode(' ', $_SESSION['user_name'])[0] . "!";
        $this->redirectToDashboard();
    }

    // =========================================================================
    // MFA — Resend code
    // =========================================================================
    public function resendMfaCode() {
        if (!isset($_SESSION['mfa_pending_id'])) {
            header("Location: " . BASE_URL . "index.php?action=auth");
            exit();
        }
        require_once __DIR__ . '/../models/UserSettings.php';
        $settingsModel = new UserSettings();
        $code = $settingsModel->generateMfaCode($_SESSION['mfa_pending_id']);
        $this->sendMfaEmail($_SESSION['mfa_pending_email'], explode(' ', $_SESSION['mfa_pending_name'])[0], $code);
        $_SESSION['mfa_info'] = "A new verification code has been sent to your email.";
        header("Location: " . BASE_URL . "index.php?action=mfa-verify");
        exit();
    }

    // =========================================================================
    // HELPER — send MFA OTP email
    // =========================================================================
    private function sendMfaEmail($toEmail, $toName, $code) {
        require_once BASE_PATH . 'vendor/autoload.php';
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ssilentsignal@gmail.com';
            $mail->Password   = 'rnfa bxze eyix tmjw';
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->setFrom(CONTACT_EMAIL, 'Silent Signal');
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = 'Your Silent Signal Login Code';
            $mail->Body    = $this->buildMfaEmailHtml($toName, $code);
            $mail->AltBody = "Hi {$toName},\n\nYour Silent Signal verification code is: {$code}\n\nThis code expires in 10 minutes.\n\nIf you did not attempt to login, please change your password immediately.\n\n— Silent Signal Team";
            $mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log("MFA email failed for {$toEmail}: " . ($mail->ErrorInfo ?? $e->getMessage()));
        }
    }

    private function buildMfaEmailHtml($name, $code) {
        $digits = str_split($code);
        $digitHtml = '';
        foreach ($digits as $d) {
            $digitHtml .= "<span style=\"display:inline-block;width:44px;height:54px;line-height:54px;text-align:center;font-size:28px;font-weight:700;color:#1A4D7F;background:#f0f6ff;border:2px solid #c7daf5;border-radius:8px;margin:0 4px;\">{$d}</span>";
        }
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f5f7fa;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fa;padding:40px 20px;">
    <tr><td align="center">
      <table width="520" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
        <tr>
          <td style="background:linear-gradient(135deg,#1A4D7F,#2d6a9f);padding:32px 40px;text-align:center;">
            <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;letter-spacing:-0.3px;">Silent Signal</h1>
            <p style="margin:6px 0 0;color:rgba(255,255,255,0.8);font-size:13px;">Two-Factor Authentication</p>
          </td>
        </tr>
        <tr>
          <td style="padding:36px 40px;">
            <h2 style="margin:0 0 8px;color:#1a1a2e;font-size:20px;">Hi {$name},</h2>
            <p style="color:#555;font-size:14px;line-height:1.6;margin:0 0 28px;">
              Your one-time login verification code is below. It expires in <strong>10 minutes</strong>.
            </p>
            <div style="text-align:center;margin:0 0 28px;">{$digitHtml}</div>
            <p style="color:#888;font-size:13px;line-height:1.6;margin:0 0 8px;">
              If you didn't try to log in, someone may have your password — please change it immediately.
            </p>
            <p style="color:#bbb;font-size:12px;margin:0;">Do not share this code with anyone.</p>
          </td>
        </tr>
        <tr>
          <td style="background:#f8f9fa;padding:20px 40px;text-align:center;border-top:1px solid #eee;">
            <p style="font-size:12px;color:#999;margin:0;">© 2026 Silent Signal · Bacolod City, Philippines</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }

    // =========================================================================
    // HELPER — mask email for display (e.g. j***@gmail.com)
    // =========================================================================
    private function maskEmail($email) {
        [$local, $domain] = explode('@', $email, 2);
        $masked = substr($local, 0, 1) . str_repeat('*', max(1, strlen($local) - 1));
        return $masked . '@' . $domain;
    }

        /**
     * Redirect to appropriate dashboard based on role
     */
    private function redirectToDashboard() {
        $role = $_SESSION['user_role'] ?? 'pwd';
        
        switch ($role) {
            case 'admin':
                header("Location: " . BASE_URL . "index.php?action=admin-dashboard");
                break;
            case 'family':
                header("Location: " . BASE_URL . "index.php?action=family-dashboard");
                break;
            default:
                header("Location: " . BASE_URL . "index.php?action=dashboard");
        }
        exit();
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get current user ID
     */
    public static function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
}