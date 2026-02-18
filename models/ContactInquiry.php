<?php
// models/ContactInquiry.php

require_once __DIR__ . '/../config/Database.php';

class ContactInquiry {
    private $db;
    private $table_name = "contact_inquiries";

    // Valid categories matching the DB ENUM
    private $valid_categories = ['general', 'support', 'technical', 'feedback', 'emergency'];

    // Priority automatically assigned based on category
    private $category_priority_map = [
        'emergency' => 'urgent',
        'technical' => 'high',
        'support'   => 'normal',
        'general'   => 'normal',
        'feedback'  => 'low',
    ];

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Resolve priority from category — no manual input needed
     */
    private function resolvePriority($category) {
        return $this->category_priority_map[$category] ?? 'normal';
    }

    /**
     * Save a new inquiry from the contact form.
     * Priority is set automatically based on category.
     */
    public function create($name, $email, $subject, $message, $category) {
        $priority = $this->resolvePriority($category);

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table_name} (name, email, subject, message, category, priority)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$name, $email, $subject, $message, $category, $priority]);
    }

    /**
     * Validate that a submitted category matches the DB ENUM
     */
    public function isValidCategory($category) {
        return in_array($category, $this->valid_categories);
    }

    /**
     * Get paginated inquiries for the admin messages page
     */
    public function getAllPaginated($limit = 5, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT
                id,
                CONCAT('#MSG-', LPAD(id, 4, '0'))                       AS message_id,
                COALESCE(name, 'Anonymous')                              AS user_name,
                email                                                    AS user_email,
                CONCAT(UPPER(SUBSTRING(category, 1, 1)),
                       LOWER(SUBSTRING(category, 2)))                    AS category,
                subject,
                message,
                SUBSTRING(message, 1, 50)                                AS preview,
                priority,
                status,
                is_read,
                reply_message,
                DATE_FORMAT(created_at, '%b %d, %Y %h:%i %p')           AS date_received,
                DATE_FORMAT(replied_at, '%b %d, %Y %h:%i %p')           AS date_replied
            FROM {$this->table_name}
            ORDER BY
                FIELD(priority, 'urgent', 'high', 'normal', 'low'),
                created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get stats for the admin messages dashboard cards
     */
    public function getStats() {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN DATE(created_at) = CURDATE()
                         AND status = 'replied' THEN 1 ELSE 0 END) AS replied_today,
                SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END) AS urgent
            FROM {$this->table_name}
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single inquiry by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table_name} WHERE id = ? LIMIT 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mark an inquiry as read
     */
    public function markAsRead($id) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table_name}
            SET is_read = TRUE
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    /**
     * Update the status of an inquiry
     */
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table_name}
            SET status = ?
            WHERE id = ?
        ");
        return $stmt->execute([$status, $id]);
    }

    /**
     * Save admin reply to an inquiry
     */
    public function saveReply($id, $adminUserId, $replyMessage) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table_name}
            SET
                replied_by    = ?,
                reply_message = ?,
                replied_at    = NOW(),
                status        = 'replied'
            WHERE id = ?
        ");
        return $stmt->execute([$adminUserId, $replyMessage, $id]);
    }
}