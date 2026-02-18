<?php
// models/FamilyDashboard.php
// Handles all DB queries for the Family Member Dashboard

require_once __DIR__ . '/../config/Database.php';

class FamilyDashboard {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Get all PWD members linked to a family member, with latest status & medical info
     */
    public function getPwdMembers($familyMemberId) {
        $stmt = $this->db->prepare("
            SELECT
                u.id,
                u.fname,
                u.lname,
                u.phone_number,
                fpr.relationship_type,
                fpr.is_primary_contact,
                psu.status        AS current_status,
                psu.latitude,
                psu.longitude,
                psu.battery_level,
                psu.message       AS status_message,
                psu.created_at    AS last_updated,
                mp.disability_type,
                mp.blood_type,
                mp.city,
                mp.street_address,
                mp.date_of_birth
            FROM family_pwd_relationships fpr
            JOIN users u ON u.id = fpr.pwd_user_id
            LEFT JOIN (
                SELECT pwd_user_id, status, latitude, longitude, battery_level, message, created_at
                FROM pwd_status_updates
                WHERE id IN (
                    SELECT MAX(id) FROM pwd_status_updates GROUP BY pwd_user_id
                )
            ) psu ON psu.pwd_user_id = fpr.pwd_user_id
            LEFT JOIN medical_profiles mp ON mp.user_id = fpr.pwd_user_id
            WHERE fpr.family_member_id = ?
            ORDER BY fpr.is_primary_contact DESC, u.fname ASC
        ");
        $stmt->execute([$familyMemberId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all other family members responsible for the same PWD(s) this family member monitors.
     * Shows their response status on the most recent active/acknowledged alert for any shared PWD.
     */
    public function getCoFamilyMembers($familyMemberId) {
        $stmt = $this->db->prepare("
            SELECT
                u.id,
                u.fname,
                u.lname,
                u.phone_number,
                fpr2.relationship_type,
                fer.response_status,
                fer.response_time,
                u.updated_at AS last_seen,
                pwd.fname    AS pwd_fname,
                pwd.lname    AS pwd_lname
            FROM family_pwd_relationships fpr1
            JOIN family_pwd_relationships fpr2
                ON  fpr2.pwd_user_id     = fpr1.pwd_user_id
                AND fpr2.family_member_id != fpr1.family_member_id
            JOIN users u   ON u.id   = fpr2.family_member_id
            JOIN users pwd ON pwd.id = fpr2.pwd_user_id
            LEFT JOIN (
                SELECT ea.user_id, MAX(ea.id) AS latest_id
                FROM emergency_alerts ea
                WHERE ea.status IN ('active','acknowledged','responded')
                GROUP BY ea.user_id
            ) latest_ea ON latest_ea.user_id = fpr1.pwd_user_id
            LEFT JOIN family_emergency_responses fer
                ON  fer.alert_id        = latest_ea.latest_id
                AND fer.family_member_id = fpr2.family_member_id
            WHERE fpr1.family_member_id = ?
            GROUP BY u.id, fpr2.relationship_type, fpr2.pwd_user_id
            ORDER BY fpr2.is_primary_contact DESC, u.fname ASC
        ");
        $stmt->execute([$familyMemberId]);
        return $stmt->fetchAll();
    }

    /**
     * Get recent emergency alerts for PWDs this family member monitors.
     * Shows the most recent response from any family member on each alert.
     */
    public function getRecentAlerts($familyMemberId, $limit = 10) {
        $limit = (int)$limit;
        $stmt = $this->db->prepare("
            SELECT
                ea.id,
                ea.alert_type,
                ea.message,
                ea.latitude,
                ea.longitude,
                ea.status,
                ea.created_at,
                u.fname,
                u.lname,
                my_fer.response_status,
                my_fer.response_time,
                responder.fname AS responder_fname,
                responder.lname AS responder_lname
            FROM emergency_alerts ea
            JOIN family_pwd_relationships fpr ON fpr.pwd_user_id = ea.user_id
                AND fpr.family_member_id = ?
            JOIN users u ON u.id = ea.user_id
            LEFT JOIN family_emergency_responses my_fer
                ON  my_fer.alert_id        = ea.id
                AND my_fer.family_member_id = ?
            LEFT JOIN family_emergency_responses any_fer
                ON  any_fer.alert_id = ea.id
                AND any_fer.id = (
                    SELECT MAX(fer2.id)
                    FROM family_emergency_responses fer2
                    WHERE fer2.alert_id = ea.id
                      AND fer2.response_status IN ('arrived','resolved','on_the_way','acknowledged')
                )
            LEFT JOIN users responder ON responder.id = any_fer.family_member_id
            ORDER BY ea.created_at DESC
            LIMIT {$limit}
        ");
        $stmt->execute([$familyMemberId, $familyMemberId]);
        return $stmt->fetchAll();
    }

    /**
     * Count summary stats for quick stats panel
     */
    public function getQuickStats($familyMemberId) {
        // PWD count
        $stmtPwd = $this->db->prepare("
            SELECT COUNT(*) AS cnt FROM family_pwd_relationships WHERE family_member_id = ?
        ");
        $stmtPwd->execute([$familyMemberId]);
        $pwdCount = (int)($stmtPwd->fetch()['cnt'] ?? 0);

        // Co-family members count (unique family members linked to same PWDs)
        $stmtFam = $this->db->prepare("
            SELECT COUNT(DISTINCT fpr2.family_member_id) AS cnt
            FROM family_pwd_relationships fpr1
            JOIN family_pwd_relationships fpr2
                ON fpr2.pwd_user_id = fpr1.pwd_user_id
                AND fpr2.family_member_id != fpr1.family_member_id
            WHERE fpr1.family_member_id = ?
        ");
        $stmtFam->execute([$familyMemberId]);
        $familyCount = (int)($stmtFam->fetch()['cnt'] ?? 0);

        // Active/recent alerts
        $stmtAlerts = $this->db->prepare("
            SELECT COUNT(*) AS cnt
            FROM emergency_alerts ea
            JOIN family_pwd_relationships fpr ON fpr.pwd_user_id = ea.user_id
            WHERE fpr.family_member_id = ?
        ");
        $stmtAlerts->execute([$familyMemberId]);
        $alertCount = (int)($stmtAlerts->fetch()['cnt'] ?? 0);

        // Safe count vs total
        $stmtSafe = $this->db->prepare("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN psu.status = 'safe' THEN 1 ELSE 0 END) AS safe_count
            FROM family_pwd_relationships fpr
            LEFT JOIN (
                SELECT pwd_user_id, status
                FROM pwd_status_updates
                WHERE id IN (
                    SELECT MAX(id) FROM pwd_status_updates GROUP BY pwd_user_id
                )
            ) psu ON psu.pwd_user_id = fpr.pwd_user_id
            WHERE fpr.family_member_id = ?
        ");
        $stmtSafe->execute([$familyMemberId]);
        $safeRow = $stmtSafe->fetch();
        $total = (int)($safeRow['total'] ?? 0);
        $safeCount = (int)($safeRow['safe_count'] ?? 0);
        $safePercent = $total > 0 ? round(($safeCount / $total) * 100) . '%' : 'N/A';

        return [
            'pwdCount'     => $pwdCount,
            'familyCount'  => $familyCount,
            'alertCount'   => $alertCount,
            'safePercent'  => $safePercent,
        ];
    }

    /**
     * Get emergency contacts for a PWD (for family dashboard PWD card display)
     * Returns both registered and unregistered contacts
     */
    public function getPwdEmergencyContacts($pwdUserId) {
        $stmt = $this->db->prepare("
            SELECT
                pec.id,
                pec.contact_user_id,
                pec.contact_name,
                pec.contact_phone,
                pec.relationship,
                pec.is_primary,
                psu.status     AS current_status,
                psu.latitude,
                psu.longitude,
                psu.created_at AS last_updated
            FROM pwd_emergency_contacts pec
            LEFT JOIN (
                SELECT pwd_user_id, status, latitude, longitude, created_at
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
     * Get a single PWD's full profile (for view-pwd-profile)
     */
    public function getPwdProfile($pwdUserId, $familyMemberId) {
        // Verify access
        $stmtCheck = $this->db->prepare("
            SELECT id FROM family_pwd_relationships
            WHERE family_member_id = ? AND pwd_user_id = ?
            LIMIT 1
        ");
        $stmtCheck->execute([$familyMemberId, $pwdUserId]);
        if (!$stmtCheck->fetch()) return null;

        $stmt = $this->db->prepare("
            SELECT
                u.id, u.fname, u.lname, u.email, u.phone_number,
                mp.*,
                psu.status AS current_status, psu.latitude, psu.longitude,
                psu.battery_level, psu.created_at AS last_updated
            FROM users u
            LEFT JOIN medical_profiles mp ON mp.user_id = u.id
            LEFT JOIN (
                SELECT pwd_user_id, status, latitude, longitude, battery_level, created_at
                FROM pwd_status_updates
                WHERE id IN (
                    SELECT MAX(id) FROM pwd_status_updates GROUP BY pwd_user_id
                )
            ) psu ON psu.pwd_user_id = u.id
            WHERE u.id = ?
        ");
        $stmt->execute([$pwdUserId]);
        $row = $stmt->fetch();
        if ($row) {
            $row['allergies']          = json_decode($row['allergies'] ?? '[]', true);
            $row['medications']        = json_decode($row['medications'] ?? '[]', true);
            $row['medical_conditions'] = json_decode($row['medical_conditions'] ?? '[]', true);
            // Prefer synced contacts from pwd_emergency_contacts (richer data)
            $syncedContacts = $this->getPwdEmergencyContacts($pwdUserId);
            if (!empty($syncedContacts)) {
                $row['emergency_contacts'] = array_map(function($c) {
                    return [
                        'name'         => $c['contact_name'],
                        'phone'        => $c['contact_phone'],
                        'relationship' => $c['relationship'],
                        'is_registered'=> !empty($c['contact_user_id']),
                        'status'       => $c['current_status'] ?? null,
                    ];
                }, $syncedContacts);
            } else {
                $row['emergency_contacts'] = json_decode($row['emergency_contacts'] ?? '[]', true);
            }
        }
        return $row;
    }

    /**
     * Log a family member's emergency response
     */
    public function respondToAlert($alertId, $familyMemberId, $responseStatus, $lat = null, $lng = null, $notes = null) {
        // Check if a response already exists
        $stmtCheck = $this->db->prepare("
            SELECT id FROM family_emergency_responses
            WHERE alert_id = ? AND family_member_id = ?
            LIMIT 1
        ");
        $stmtCheck->execute([$alertId, $familyMemberId]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE family_emergency_responses
                SET response_status = ?, response_time = NOW(), location_lat = ?, location_lng = ?, notes = ?
                WHERE alert_id = ? AND family_member_id = ?
            ");
            return $stmt->execute([$responseStatus, $lat, $lng, $notes, $alertId, $familyMemberId]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO family_emergency_responses
                    (alert_id, family_member_id, response_status, response_time, location_lat, location_lng, notes)
                VALUES (?, ?, ?, NOW(), ?, ?, ?)
            ");
            return $stmt->execute([$alertId, $familyMemberId, $responseStatus, $lat, $lng, $notes]);
        }
    }

    /**
     * Log an "alert all family" broadcast event
     */
    public function logBroadcast($familyMemberId, $pwdUserId, $message) {
        $stmt = $this->db->prepare("
            INSERT INTO family_broadcasts (sender_id, pwd_id, message, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute([$familyMemberId, $pwdUserId, $message]);
    }

    /**
     * Get live status of a specific PWD (for polling)
     */
    public function getPwdLiveStatus($pwdUserId, $familyMemberId) {
        // Verify access
        $stmtCheck = $this->db->prepare("
            SELECT id FROM family_pwd_relationships
            WHERE family_member_id = ? AND pwd_user_id = ?
            LIMIT 1
        ");
        $stmtCheck->execute([$familyMemberId, $pwdUserId]);
        if (!$stmtCheck->fetch()) return null;

        $stmt = $this->db->prepare("
            SELECT status, latitude, longitude, battery_level, message, created_at
            FROM pwd_status_updates
            WHERE pwd_user_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$pwdUserId]);
        return $stmt->fetch();
    }
}
?>
