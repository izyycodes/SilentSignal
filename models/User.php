<?php
// models/User.php

require_once __DIR__ . '/../config/Database.php';

class User {
    private $db;
    private $table_name = "users";

    public $id;
    public $fname;
    public $lname;
    public $email;
    public $phone_number;
    public $role;
    public $password;
    public $loginError = null;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO {$this->table_name}
            (fname, lname, email, phone_number, role, password)
            VALUES (:fname, :lname, :email, :phone_number, :role, :password)";

        $stmt = $this->db->prepare($query);

        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":fname", $this->fname);
        $stmt->bindParam(":lname", $this->lname);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":password", $password_hash);

        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function emailExists() {
        $query = "SELECT * FROM {$this->table_name}
                  WHERE email = :email LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function phoneExists() {
        $query = "SELECT id FROM {$this->table_name}
                  WHERE phone_number = :phone_number LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function login($identifier, $password) {
        $query = "SELECT * FROM {$this->table_name}
                  WHERE email = :id OR phone_number = :id LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $identifier);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $row['password'])) {
                // Block deactivated accounts
                if (isset($row['is_active']) && !$row['is_active']) {
                    $this->loginError = 'deactivated';
                    return false;
                }
                $this->id           = $row['id'];
                $this->fname        = $row['fname'];
                $this->lname        = $row['lname'];
                $this->email        = $row['email'];
                $this->phone_number = $row['phone_number'];
                $this->role         = $row['role'];
                return true;
            }
        }
        return false;
    }

    /**
     * Get paginated users for admin panel with medical profile data
     */
    public function getAllPaginated($limit = 5, $offset = 0) {
        $query = "
            SELECT 
                u.id,
                CONCAT(u.fname, ' ', u.lname) AS name,
                u.email,
                u.phone_number AS phone,
                u.role,
                u.is_verified,
                u.is_active,
                DATE_FORMAT(u.created_at, '%b %d, %Y') AS registration_date,
                m.pwd_id,
                m.disability_type,
                CASE 
                    WHEN (m.city IS NULL OR m.city = '') AND (m.province IS NULL OR m.province = '') THEN NULL
                    WHEN (m.city IS NULL OR m.city = '') THEN m.province
                    WHEN (m.province IS NULL OR m.province = '') THEN m.city
                    ELSE CONCAT(m.city, ', ', m.province)
                END AS location
            FROM {$this->table_name} u
            LEFT JOIN medical_profiles m ON u.id = m.user_id
            ORDER BY u.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get user statistics for admin dashboard
     */
    public function getStats() {
        $query = "
            SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN is_verified = TRUE THEN 1 ELSE 0 END) AS verified,
                SUM(CASE WHEN is_verified = FALSE THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN is_active = FALSE THEN 1 ELSE 0 END) AS inactive
            FROM {$this->table_name}
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verify a user account
     */
    public function verifyUser($userId) {
        $query = "UPDATE {$this->table_name} 
                  SET is_verified = TRUE, verified_at = NOW() 
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId);

        return $stmt->execute();
    }

    /**
     * Toggle user active status
     */
    public function toggleActive($userId) {
        $query = "UPDATE {$this->table_name} 
                  SET is_active = NOT is_active 
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId);

        return $stmt->execute();
    }

    /**
     * Check if user account is active
     */
    public function isUserActive($userId) {
        $query = "SELECT is_active FROM {$this->table_name} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;
        return (bool)$row['is_active'];
    }

    /**
     * Update user phone number
     */
    public function updatePhone($userId, $phone) {
        $query = "UPDATE {$this->table_name} SET phone_number = :phone WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    /**
     * Get distinct disability types from medical profiles
     */
    public function getDistinctDisabilityTypes() {
        $query = "
            SELECT DISTINCT m.disability_type 
            FROM medical_profiles m
            WHERE m.disability_type IS NOT NULL AND m.disability_type != ''
            ORDER BY m.disability_type ASC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get user verification status
     */
    public function getUserVerifiedStatus($userId) {
        $query = "SELECT is_verified FROM {$this->table_name} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;
        return (bool)$row['is_verified'];
    }
}