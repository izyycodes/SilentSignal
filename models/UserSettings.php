<?php
// models/UserSettings.php
// Manages per-user preference settings (MFA, SOS countdown, shake toggle, auto-invite)

require_once __DIR__ . '/../config/Database.php';

class UserSettings {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Get settings for a user, creating defaults if none exist.
     */
    public function getSettings($userId) {
        $stmt = $this->db->prepare(
            "SELECT * FROM user_settings WHERE user_id = ? LIMIT 1"
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // Insert defaults and return them
            $this->db->prepare(
                "INSERT INTO user_settings (user_id, mfa_enabled, sos_countdown_seconds, auto_shake_enabled, auto_invite_contacts)
                 VALUES (?, 0, 10, 0, 0)"
            )->execute([$userId]);

            return [
                'user_id'              => $userId,
                'mfa_enabled'          => 0,
                'sos_countdown_seconds'=> 10,
                'auto_shake_enabled'   => 0,
                'auto_invite_contacts' => 0,
            ];
        }

        return $row;
    }

    /**
     * Save (upsert) settings for a user.
     */
    public function saveSettings($userId, $mfaEnabled, $sosCountdown, $autoShake, $autoInvite) {
        // Clamp countdown to 5–60 seconds
        $sosCountdown = max(5, min(60, (int)$sosCountdown));

        $stmt = $this->db->prepare(
            "INSERT INTO user_settings (user_id, mfa_enabled, sos_countdown_seconds, auto_shake_enabled, auto_invite_contacts)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
               mfa_enabled           = VALUES(mfa_enabled),
               sos_countdown_seconds = VALUES(sos_countdown_seconds),
               auto_shake_enabled    = VALUES(auto_shake_enabled),
               auto_invite_contacts  = VALUES(auto_invite_contacts),
               updated_at            = NOW()"
        );

        return $stmt->execute([$userId, (int)$mfaEnabled, $sosCountdown, (int)$autoShake, (int)$autoInvite]);
    }

    /**
     * Check if MFA is enabled for a user.
     */
    public function isMfaEnabled($userId) {
        $stmt = $this->db->prepare(
            "SELECT mfa_enabled FROM user_settings WHERE user_id = ? LIMIT 1"
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (bool)$row['mfa_enabled'] : false;
    }

    // ── MFA CODE MANAGEMENT ──────────────────────────────────────────────────

    /**
     * Generate and store a 6-digit OTP. Returns the code.
     */
    public function generateMfaCode($userId) {
        // Invalidate existing codes for this user
        $this->db->prepare(
            "UPDATE mfa_codes SET used = 1 WHERE user_id = ? AND used = 0"
        )->execute([$userId]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->db->prepare(
            "INSERT INTO mfa_codes (user_id, code, expires_at)
             VALUES (?, ?, NOW() + INTERVAL 10 MINUTE)"
        )->execute([$userId, $code]);

        return $code;
    }

    /**
     * Verify an OTP code. Returns true if valid (and marks it used).
     */
    public function verifyMfaCode($userId, $code) {
        $stmt = $this->db->prepare(
            "SELECT id FROM mfa_codes
             WHERE user_id = ? AND code = ? AND used = 0 AND expires_at > NOW()
             LIMIT 1"
        );
        $stmt->execute([$userId, $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->db->prepare(
                "UPDATE mfa_codes SET used = 1 WHERE id = ?"
            )->execute([$row['id']]);
            return true;
        }
        return false;
    }
}
