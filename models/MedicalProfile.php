<?php
// models/MedicalProfile.php

require_once __DIR__ . '/../config/Database.php';

class MedicalProfile {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Get medical profile by user ID
     */
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT mp.*, u.phone_number AS user_phone_number
            FROM medical_profiles mp
            JOIN users u ON u.id = mp.user_id
            WHERE mp.user_id = ? 
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($profile) {
            // If medical profile phone is empty, use the phone from users table
            if (empty($profile['phone']) && !empty($profile['user_phone_number'])) {
                $profile['phone'] = $profile['user_phone_number'];
            }
            unset($profile['user_phone_number']);
            
            // Decode JSON fields
            $profile['allergies'] = json_decode($profile['allergies'] ?? '[]', true);
            $profile['medications'] = json_decode($profile['medications'] ?? '[]', true);
            $profile['medical_conditions'] = json_decode($profile['medical_conditions'] ?? '[]', true);
            $profile['emergency_contacts'] = json_decode($profile['emergency_contacts'] ?? '[]', true);
            $profile['medication_reminders'] = json_decode($profile['medication_reminders'] ?? '[]', true);
        }
        
        return $profile;
    }
    
    /**
     * Create or update medical profile
     */
    public function saveProfile($userId, $data) {
        // Check if profile exists
        $existing = $this->getByUserId($userId);
        
        // Encode JSON fields
        $data['allergies'] = json_encode($data['allergies'] ?? []);
        $data['medications'] = json_encode($data['medications'] ?? []);
        $data['medical_conditions'] = json_encode($data['medical_conditions'] ?? []);
        $data['emergency_contacts'] = json_encode($data['emergency_contacts'] ?? []);
        $data['medication_reminders'] = json_encode($data['medication_reminders'] ?? []);
        
        if ($existing) {
            // Update existing profile
            $stmt = $this->db->prepare("
                UPDATE medical_profiles SET
                    first_name = ?,
                    last_name = ?,
                    date_of_birth = ?,
                    gender = ?,
                    pwd_id = ?,
                    phone = ?,
                    email = ?,
                    street_address = ?,
                    city = ?,
                    province = ?,
                    zip_code = ?,
                    disability_type = ?,
                    blood_type = ?,
                    allergies = ?,
                    medications = ?,
                    medical_conditions = ?,
                    emergency_contacts = ?,
                    sms_template = ?,
                    medication_reminders = ?,
                    updated_at = NOW()
                WHERE user_id = ?
            ");
            
            return $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['date_of_birth'],
                $data['gender'],
                $data['pwd_id'],
                $data['phone'],
                $data['email'],
                $data['street_address'],
                $data['city'],
                $data['province'],
                $data['zip_code'],
                $data['disability_type'],
                $data['blood_type'],
                $data['allergies'],
                $data['medications'],
                $data['medical_conditions'],
                $data['emergency_contacts'],
                $data['sms_template'],
                $data['medication_reminders'],
                $userId
            ]);
        } else {
            // Create new profile
            $stmt = $this->db->prepare("
                INSERT INTO medical_profiles (
                    user_id, first_name, last_name, date_of_birth, gender,
                    pwd_id, phone, email, street_address, city, province, zip_code,
                    disability_type, blood_type, allergies, medications, 
                    medical_conditions, emergency_contacts, sms_template, medication_reminders
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $userId,
                $data['first_name'],
                $data['last_name'],
                $data['date_of_birth'],
                $data['gender'],
                $data['pwd_id'],
                $data['phone'],
                $data['email'],
                $data['street_address'],
                $data['city'],
                $data['province'],
                $data['zip_code'],
                $data['disability_type'],
                $data['blood_type'],
                $data['allergies'],
                $data['medications'],
                $data['medical_conditions'],
                $data['emergency_contacts'],
                $data['sms_template'],
                $data['medication_reminders']
            ]);
        }
    }
    
    /**
     * Delete medical profile
     */
    public function deleteProfile($userId) {
        $stmt = $this->db->prepare("DELETE FROM medical_profiles WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}