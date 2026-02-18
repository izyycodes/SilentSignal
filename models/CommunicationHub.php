<?php
// models/CommunicationHub.php

require_once __DIR__ . '/../config/Database.php';

class CommunicationHub {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Log an SMS send event
     */
    public function logSmsEvent($userId, $messages, $contacts, $latitude = null, $longitude = null, $locationLabel = null) {
        $stmt = $this->db->prepare("
            INSERT INTO sms_events (user_id, messages, contacts, latitude, longitude, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([
            $userId,
            json_encode($messages),
            json_encode($contacts),
            $latitude,
            $longitude
        ]);
    }

    /**
     * Log a media capture event (photo/video)
     */
    public function logMediaCapture($userId, $type, $latitude = null, $longitude = null) {
        $stmt = $this->db->prepare("
            INSERT INTO hub_media_logs (user_id, media_type, latitude, longitude, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$userId, $type, $latitude, $longitude]);
    }

    /**
     * Get recent SMS logs for a user
     */
    public function getRecentLogs($userId, $limit = 10) {
        $limit = (int)$limit;
        $stmt = $this->db->prepare("
            SELECT * FROM sms_events
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT {$limit}
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
?>
