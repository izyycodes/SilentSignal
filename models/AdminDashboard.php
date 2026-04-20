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

    /**
     * Filtered Monthly Activity Chart — supports daily / weekly / monthly / yearly
     * @param string $period  'daily' | 'weekly' | 'monthly' | 'yearly'
     */
    public function getFilteredActivityChart(string $period = 'monthly') {
        switch ($period) {
            case 'daily':
                $interval  = '30 DAY';
                $fmt       = '%b %d';
                $groupBy   = "DATE(created_at)";
                break;
            case 'weekly':
                $interval  = '12 WEEK';
                $fmt       = 'Wk %v \'%y';
                $groupBy   = "YEARWEEK(created_at, 1)";
                break;
            case 'yearly':
                $interval  = '5 YEAR';
                $fmt       = '%Y';
                $groupBy   = "YEAR(created_at)";
                break;
            default: // monthly
                $interval  = '6 MONTH';
                $fmt       = '%b \'%y';
                $groupBy   = "DATE_FORMAT(created_at, '%Y-%m')";
                break;
        }

        $sql = "
            SELECT
                DATE_FORMAT(created_at, '{$fmt}') AS period_label,
                {$groupBy} AS period_sort,
                COUNT(*) AS count,
                'alerts' AS type
            FROM emergency_alerts
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$interval})
            GROUP BY {$groupBy}

            UNION ALL

            SELECT
                DATE_FORMAT(created_at, '{$fmt}') AS period_label,
                {$groupBy} AS period_sort,
                COUNT(*) AS count,
                'messages' AS type
            FROM contact_inquiries
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$interval})
            GROUP BY {$groupBy}

            ORDER BY period_sort
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Filtered Alert Status Chart — same period filter applied
     */
    public function getFilteredAlertStatus(string $period = 'monthly') {
        switch ($period) {
            case 'daily':   $interval = '30 DAY';  break;
            case 'weekly':  $interval = '12 WEEK'; break;
            case 'yearly':  $interval = '5 YEAR';  break;
            default:        $interval = '6 MONTH'; break;
        }
        $stmt = $this->db->prepare("
            SELECT status, COUNT(*) AS count
            FROM emergency_alerts
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$interval})
            GROUP BY status
            ORDER BY count DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Filtered Message Categories Chart
     */
    public function getFilteredMsgCategories(string $period = 'monthly') {
        switch ($period) {
            case 'daily':   $interval = '30 DAY';  break;
            case 'weekly':  $interval = '12 WEEK'; break;
            case 'yearly':  $interval = '5 YEAR';  break;
            default:        $interval = '6 MONTH'; break;
        }
        $stmt = $this->db->prepare("
            SELECT 
                COALESCE(NULLIF(TRIM(category), ''), 'general') AS category,
                COUNT(*) AS count
            FROM contact_inquiries
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$interval})
            GROUP BY category
            ORDER BY count DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}