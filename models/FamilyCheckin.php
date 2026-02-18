<?php
// models/FamilyCheckin.php

require_once __DIR__ . '/../config/Database.php';

class FamilyCheckin {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->ensureTable();
    }

    /**
     * Create pwd_emergency_contacts if it doesn't exist (handles existing installs)
     */
    private function ensureTable() {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS `pwd_emergency_contacts` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `pwd_user_id` int(11) NOT NULL,
                `contact_user_id` int(11) DEFAULT NULL,
                `contact_name` varchar(150) NOT NULL,
                `contact_phone` varchar(30) DEFAULT NULL,
                `relationship` varchar(50) DEFAULT NULL,
                `is_primary` tinyint(1) DEFAULT 0,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_pwd_user` (`pwd_user_id`),
                KEY `idx_contact_user` (`contact_user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    // =========================================================================
    // STATUS UPDATES
    // =========================================================================

    /**
     * Save a status update for a PWD user
     */
    public function saveStatusUpdate($pwdUserId, $status, $latitude = null, $longitude = null, $message = null, $batteryLevel = null) {
        $stmt = $this->db->prepare("
            INSERT INTO pwd_status_updates (pwd_user_id, status, latitude, longitude, message, battery_level, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$pwdUserId, $status, $latitude, $longitude, $message, $batteryLevel]);
    }

    /**
     * Get the latest status for a PWD user
     */
    public function getLatestStatus($pwdUserId) {
        $stmt = $this->db->prepare("
            SELECT * FROM pwd_status_updates
            WHERE pwd_user_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$pwdUserId]);
        return $stmt->fetch();
    }

    /**
     * Get status history for breadcrumbs
     */
    public function getStatusHistory($pwdUserId, $limit = 20) {
        $limit = (int)$limit;
        $stmt  = $this->db->prepare("
            SELECT status, latitude, longitude, message, created_at
            FROM pwd_status_updates
            WHERE pwd_user_id = ?
            ORDER BY created_at DESC
            LIMIT {$limit}
        ");
        $stmt->execute([$pwdUserId]);
        return $stmt->fetchAll();
    }

    /**
     * Log a media capture
     */
    public function logMediaCapture($userId, $type, $latitude = null, $longitude = null) {
        $stmt = $this->db->prepare("
            INSERT INTO checkin_media_logs (user_id, media_type, latitude, longitude, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$userId, $type, $latitude, $longitude]);
    }

    // =========================================================================
    // EMERGENCY CONTACT SYNC
    // Keeps pwd_emergency_contacts in sync with medical_profiles.emergency_contacts
    // and also keeps family_pwd_relationships in sync for registered users.
    // =========================================================================

    /**
     * Sync emergency contacts from medical profile JSON into:
     *   1. pwd_emergency_contacts (all contacts, registered or not)
     *   2. family_pwd_relationships (only for contacts that are registered users)
     *
     * Called every time a PWD saves their medical profile.
     */
    public function syncEmergencyContacts($pwdUserId, array $contacts) {
        // ── Step 1: Delete old synced contacts for this PWD ───────────────────
        $this->db->prepare("DELETE FROM pwd_emergency_contacts WHERE pwd_user_id = ?")
                 ->execute([$pwdUserId]);

        if (empty($contacts)) {
            // Also remove all auto-synced family_pwd_relationships for this PWD
            // (keep manually-added ones if any; we flag auto-synced rows with a note —
            //  simplest approach: just replace all based on contacts list)
            $this->db->prepare("DELETE FROM family_pwd_relationships WHERE pwd_user_id = ?")
                     ->execute([$pwdUserId]);
            return;
        }

        // ── Step 2: For each contact, look up if they're a registered user ────
        $stmtLookup = $this->db->prepare("
            SELECT id FROM users WHERE phone_number = ? OR email = ? LIMIT 1
        ");

        $stmtInsertContact = $this->db->prepare("
            INSERT INTO pwd_emergency_contacts
                (pwd_user_id, contact_user_id, contact_name, contact_phone, relationship, is_primary)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        // ── Step 3: Rebuild family_pwd_relationships from scratch for this PWD
        $this->db->prepare("DELETE FROM family_pwd_relationships WHERE pwd_user_id = ?")
                 ->execute([$pwdUserId]);

        $stmtInsertRel = $this->db->prepare("
            INSERT IGNORE INTO family_pwd_relationships
                (family_member_id, pwd_user_id, relationship_type, is_primary_contact, notification_enabled)
            VALUES (?, ?, ?, ?, 1)
        ");

        foreach ($contacts as $i => $c) {
            $name     = trim($c['name']      ?? '');
            $phone    = trim($c['phone']     ?? '');
            $relation = trim($c['relation']  ?? ($c['relationship'] ?? 'Contact'));
            $isPrimary = ($i === 0) ? 1 : 0;

            if ($name === '') continue;

            // Look up registered user by phone or email
            $contactUserId = null;
            if ($phone !== '') {
                $email = trim($c['email'] ?? '');
                $stmtLookup->execute([$phone, $email]);
                $found = $stmtLookup->fetch();
                if ($found) {
                    $contactUserId = (int)$found['id'];
                }
            }

            // Insert into pwd_emergency_contacts
            $stmtInsertContact->execute([
                $pwdUserId,
                $contactUserId,
                $name,
                $phone,
                $relation,
                $isPrimary,
            ]);

            // If registered, also insert into family_pwd_relationships
            if ($contactUserId) {
                $stmtInsertRel->execute([
                    $contactUserId,
                    $pwdUserId,
                    $relation,
                    $isPrimary,
                ]);
            }
        }
    }

    // =========================================================================
    // FAMILY STATUS for Family Check-in page (PWD's view)
    // Returns ALL emergency contacts (registered + unregistered)
    // with status data from pwd_status_updates where available.
    // =========================================================================

    /**
     * Get family/emergency contacts for a PWD user's check-in page.
     * Pulls from pwd_emergency_contacts (synced from medical profile).
     * For registered contacts, fetches their latest status from pwd_status_updates.
     */
    public function getFamilyStatusesForPwd($pwdUserId) {
        $stmt = $this->db->prepare("
            SELECT
                pec.id,
                pec.contact_user_id,
                pec.contact_name  AS fname_full,
                pec.contact_phone AS phone_number,
                pec.relationship  AS relationship_type,
                pec.is_primary    AS is_primary_contact,
                psu.status        AS current_status,
                psu.latitude,
                psu.longitude,
                psu.message,
                psu.created_at    AS last_updated,
                u.fname,
                u.lname
            FROM pwd_emergency_contacts pec
            LEFT JOIN users u ON u.id = pec.contact_user_id
            LEFT JOIN (
                SELECT pwd_user_id, status, latitude, longitude, message, created_at
                FROM pwd_status_updates
                WHERE id IN (
                    SELECT MAX(id) FROM pwd_status_updates GROUP BY pwd_user_id
                )
            ) psu ON psu.pwd_user_id = pec.contact_user_id
            WHERE pec.pwd_user_id = ?
            ORDER BY pec.is_primary DESC, pec.id ASC
        ");
        $stmt->execute([$pwdUserId]);
        return $stmt->fetchAll();
    }

    /**
     * Get family group name based on PWD's last name
     */
    public function getFamilyGroupName($pwdUserId) {
        $stmt = $this->db->prepare("SELECT lname FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$pwdUserId]);
        $row = $stmt->fetch();
        return $row ? ($row['lname'] . ' Family') : 'My Family';
    }

    /**
     * Count emergency contacts for a PWD user
     */
    public function getFamilyMemberCount($pwdUserId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS cnt FROM pwd_emergency_contacts WHERE pwd_user_id = ?
        ");
        $stmt->execute([$pwdUserId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['cnt'] : 0;
    }

    // =========================================================================
    // For family-dashboard: PWDs linked to a family member (unchanged — uses family_pwd_relationships)
    // =========================================================================

    public function getPwdMembersForFamily($familyMemberId) {
        $stmt = $this->db->prepare("
            SELECT
                u.id,
                u.fname,
                u.lname,
                u.phone_number,
                fpr.relationship_type,
                psu.status        AS current_status,
                psu.created_at    AS last_updated,
                psu.latitude,
                psu.longitude,
                psu.message,
                mp.disability_type,
                mp.blood_type,
                mp.city,
                mp.street_address
            FROM family_pwd_relationships fpr
            JOIN users u ON u.id = fpr.pwd_user_id
            LEFT JOIN (
                SELECT pwd_user_id, status, created_at, latitude, longitude, message
                FROM pwd_status_updates
                WHERE id IN (
                    SELECT MAX(id) FROM pwd_status_updates GROUP BY pwd_user_id
                )
            ) psu ON psu.pwd_user_id = fpr.pwd_user_id
            LEFT JOIN medical_profiles mp ON mp.user_id = fpr.pwd_user_id
            WHERE fpr.family_member_id = ?
            ORDER BY u.fname ASC
        ");
        $stmt->execute([$familyMemberId]);
        return $stmt->fetchAll();
    }
}
?>
