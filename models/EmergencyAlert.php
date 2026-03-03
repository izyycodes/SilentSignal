<?php
// models/EmergencyAlert.php

require_once __DIR__ . '/../config/Database.php';

class EmergencyAlert {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Map DB alert_type to display label
     */
    private function formatAlertType($type) {
        $map = [
            'sos'            => 'Emergency SOS',
            'shake'          => 'Shake Alert',
            'panic_click'    => 'Panic Button',
            'medical'        => 'Medi-Alert',
            'assistance'     => 'Assistance',
            'fall_detection' => 'Fall Detection',
        ];
        return $map[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    /**
     * Map DB alert_type to alert ID prefix
     */
    private function formatAlertId($id, $type) {
        $prefixMap = [
            'sos'            => 'SOS',
            'shake'          => 'SHAKE',
            'panic_click'    => 'PANIC',
            'medical'        => 'MED',
            'assistance'     => 'ASST',
            'fall_detection' => 'FALL',
        ];
        $prefix = $prefixMap[$type] ?? 'ALERT';
        return '#' . $prefix . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Format timestamp as relative time ("X min ago")
     */
    private function timeAgo($datetime) {
        $now  = new DateTime();
        $past = new DateTime($datetime);
        $diff = $now->getTimestamp() - $past->getTimestamp();

        if ($diff < 60)        return $diff . ' sec ago';
        if ($diff < 3600)      return floor($diff / 60) . ' min ago';
        if ($diff < 86400)     return floor($diff / 3600) . ' hr ago';
        return floor($diff / 86400) . ' day ago';
    }

    /**
     * Calculate response time between created_at and resolved_at
     */
    private function calcResponseTime($createdAt, $resolvedAt) {
        if (!$resolvedAt) return '-';
        $start = new DateTime($createdAt);
        $end   = new DateTime($resolvedAt);
        $diff  = $end->getTimestamp() - $start->getTimestamp();
        if ($diff < 60)    return $diff . ' sec';
        if ($diff < 3600)  return floor($diff / 60) . ' min';
        return floor($diff / 3600) . ' hr';
    }

    /**
     * Get paginated emergency alerts with user info
     */
    public function getAllPaginated($limit = 5, $offset = 0) {
        $query = "
            SELECT
                ea.id,
                ea.alert_type,
                ea.latitude,
                ea.longitude,
                ea.location_address,
                ea.status,
                ea.priority,
                ea.created_at,
                ea.resolved_at,
                CONCAT(u.fname, ' ', u.lname) AS user_name,
                u.id AS user_db_id,
                COALESCE(mp.pwd_id, CONCAT('UID-', u.id)) AS user_id_display
            FROM emergency_alerts ea
            JOIN users u ON ea.user_id = u.id
            LEFT JOIN medical_profiles mp ON u.id = mp.user_id
            ORDER BY ea.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format rows for the view
        return array_map(function($row) {
            $location = $row['location_address'];
            if (!$location && $row['latitude'] && $row['longitude']) {
                $location = $row['latitude'] . ', ' . $row['longitude'];
            }
            if (!$location) $location = 'Location unavailable';

            // Map DB status to view status
            $statusMap = [
                'active'       => 'active',
                'acknowledged' => 'responded',
                'responded'    => 'responded',
                'resolved'     => 'resolved',
                'cancelled'    => 'resolved',
            ];
            $viewStatus = $statusMap[$row['status']] ?? $row['status'];

            return [
                'id'            => $row['id'],
                'alert_id'      => $this->formatAlertId($row['id'], $row['alert_type']),
                'user_name'     => $row['user_name'],
                'user_id'       => $row['user_id_display'],
                'alert_type'    => $this->formatAlertType($row['alert_type']),
                'alert_type_raw'=> $row['alert_type'],
                'priority'      => $row['priority'],
                'location'      => $location,
                'time'          => $this->timeAgo($row['created_at']),
                'response_time' => $this->calcResponseTime($row['created_at'], $row['resolved_at']),
                'status'        => $viewStatus,
            ];
        }, $rows);
    }

    /**
     * Get emergency alert statistics
     */
    public function getStats() {
        $query = "
            SELECT
                COUNT(*) AS total_today,
                SUM(CASE WHEN priority = 'critical' THEN 1 ELSE 0 END) AS critical,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active,
                SUM(CASE WHEN status IN ('resolved','cancelled') AND DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS resolved_today
            FROM emergency_alerts
            WHERE DATE(created_at) = CURDATE()
               OR status = 'active'
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ensure no nulls
        return [
            'total_today'   => (int)($row['total_today'] ?? 0),
            'critical'      => (int)($row['critical'] ?? 0),
            'active'        => (int)($row['active'] ?? 0),
            'resolved_today'=> (int)($row['resolved_today'] ?? 0),
        ];
    }

    /**
     * Get total count of all alerts
     */
    public function getTotalCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM emergency_alerts");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Update alert status
     */
    public function updateStatus($id, $status) {
        $validStatuses = ['active','acknowledged','responded','resolved','cancelled'];
        if (!in_array($status, $validStatuses)) return false;

        $resolvedAt = in_array($status, ['resolved','cancelled']) ? 'NOW()' : 'NULL';
        $resolvedBy = in_array($status, ['resolved','cancelled']) ? ($_SESSION['user_id'] ?? null) : null;

        $query = "UPDATE emergency_alerts 
                  SET status = :status,
                      resolved_at = {$resolvedAt},
                      resolved_by = :resolved_by
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':resolved_by', $resolvedBy, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Get a single alert by ID
     */
    public function getById($id) {
        $query = "
            SELECT ea.*, CONCAT(u.fname, ' ', u.lname) AS user_name, u.email AS user_email
            FROM emergency_alerts ea
            JOIN users u ON ea.user_id = u.id
            WHERE ea.id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all alerts for CSV export (no pagination)hi
     */
    public function getAllForExport() {
        $query = "
            SELECT
                ea.id,
                ea.alert_type,
                ea.location_address,
                ea.latitude,
                ea.longitude,
                ea.status,
                ea.priority,
                ea.created_at,
                ea.resolved_at,
                ea.notes,
                CONCAT(u.fname, ' ', u.lname) AS user_name,
                u.email AS user_email,
                u.phone_number AS user_phone,
                COALESCE(mp.pwd_id, CONCAT('UID-', u.id)) AS user_id_display
            FROM emergency_alerts ea
            JOIN users u ON ea.user_id = u.id
            LEFT JOIN medical_profiles mp ON u.id = mp.user_id
            ORDER BY ea.created_at DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
