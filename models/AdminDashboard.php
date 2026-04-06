<?php
// models/AdminDashboard.php

require_once CONFIG_PATH . 'Database.php';

class AdminDashboard {

    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

   /**
     * User role breakdown (PWD, Family, Admin)
     */
    public function getUserRoleBreakdown() {
        $stmt = $this->db->query("
            SELECT role, COUNT(*) AS count
            FROM users
            GROUP BY role
            ORDER BY count DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * SOS Alert status breakdown (active, resolved, false alarm)
     */
    public function getAlertStatusChart() {
        $stmt = $this->db->query("
            SELECT status, COUNT(*) AS count
            FROM emergency_alerts
            GROUP BY status
            ORDER BY count DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Monthly alerts vs messages (last 6 months)
     */
    public function getMonthlyActivityChart() {
        $stmt = $this->db->query("
            SELECT DATE_FORMAT(created_at, '%b') AS month,
                   YEAR(created_at) AS year,
                   MONTH(created_at) AS month_num,
                   COUNT(*) AS count,
                   'alerts' AS type
            FROM emergency_alerts
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY YEAR(created_at), MONTH(created_at)

            UNION ALL

            SELECT DATE_FORMAT(created_at, '%b') AS month,
                   YEAR(created_at) AS year,
                   MONTH(created_at) AS month_num,
                   COUNT(*) AS count,
                   'messages' AS type
            FROM contact_inquiries
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY YEAR(created_at), MONTH(created_at)

            ORDER BY year, month_num
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Message inquiry categories
     */
    public function getMessageCategoriesChart() {
        $stmt = $this->db->query("
            SELECT 
                COALESCE(NULLIF(TRIM(category), ''), 'general') AS category,
                COUNT(*) AS count
            FROM contact_inquiries
            GROUP BY category
            ORDER BY count DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}