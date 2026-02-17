-- =============================================================================
-- SILENT SIGNAL - COMPLETE DATABASE SCHEMA
-- =============================================================================
-- This schema includes all tables for the Silent Signal system including
-- users, emergency alerts, disaster alerts, medical profiles, family
-- relationships, contact inquiries, and sample test data.
-- =============================================================================

-- -----------------------------------------------------------------------------
-- STEP 1: CREATE BASE TABLES
-- -----------------------------------------------------------------------------

-- Users table (base table for all user types)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fname VARCHAR(100) NOT NULL,
    lname VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone_number VARCHAR(20),
    role ENUM('pwd', 'family', 'admin') NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Emergency alerts table
CREATE TABLE IF NOT EXISTS emergency_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    alert_type ENUM('sos', 'shake', 'panic_click') NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    message TEXT,
    status ENUM('active', 'responded', 'resolved', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Disaster alerts table
CREATE TABLE IF NOT EXISTS disaster_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alert_type ENUM('flood', 'earthquake', 'typhoon', 'fire', 'tsunami') NOT NULL,
    severity ENUM('low', 'moderate', 'high', 'critical') NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT,
    affected_areas TEXT,
    status ENUM('active', 'resolved') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_location (location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Medical profiles table
CREATE TABLE IF NOT EXISTS medical_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,

    -- Personal Information
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    date_of_birth DATE,
    gender VARCHAR(20),
    pwd_id VARCHAR(50),
    phone VARCHAR(20),
    email VARCHAR(100),
    street_address VARCHAR(255),
    city VARCHAR(100),
    province VARCHAR(100),
    zip_code VARCHAR(20),

    -- Medical Information
    disability_type VARCHAR(100),
    blood_type VARCHAR(5),
    allergies TEXT,           -- JSON array
    medications TEXT,         -- JSON array
    medical_conditions TEXT,  -- JSON array

    -- Emergency Contacts
    emergency_contacts TEXT,  -- JSON array

    -- SMS Configuration
    sms_template TEXT,

    -- Medication Reminders
    medication_reminders TEXT, -- JSON array

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_profile (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- STEP 2: CREATE FAMILY MEMBER DASHBOARD TABLES
-- -----------------------------------------------------------------------------

-- Family member relationships (who is responsible for which PWD)
CREATE TABLE IF NOT EXISTS family_pwd_relationships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    family_member_id INT NOT NULL,
    pwd_user_id INT NOT NULL,
    relationship_type VARCHAR(50) NOT NULL, -- Mother, Father, Sibling, Guardian, etc.
    is_primary_contact BOOLEAN DEFAULT FALSE,
    notification_enabled BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (family_member_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pwd_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_family_member (family_member_id),
    INDEX idx_pwd_user (pwd_user_id),
    UNIQUE KEY unique_relationship (family_member_id, pwd_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Family member responses to emergencies
CREATE TABLE IF NOT EXISTS family_emergency_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alert_id INT NOT NULL,
    family_member_id INT NOT NULL,
    response_status ENUM('notified', 'acknowledged', 'on_the_way', 'arrived', 'resolved') DEFAULT 'notified',
    response_time TIMESTAMP NULL,
    location_lat DECIMAL(10, 8),
    location_lng DECIMAL(11, 8),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (alert_id) REFERENCES emergency_alerts(id) ON DELETE CASCADE,
    FOREIGN KEY (family_member_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_alert (alert_id),
    INDEX idx_family_member (family_member_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- PWD Status tracking (for safety check-ins)
CREATE TABLE IF NOT EXISTS pwd_status_updates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pwd_user_id INT NOT NULL,
    status ENUM('safe', 'danger', 'unknown', 'needs_assistance') DEFAULT 'unknown',
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    battery_level INT,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pwd_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_pwd_user (pwd_user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- STEP 3: CREATE CONTACT INQUIRIES TABLE (NEW)
-- -----------------------------------------------------------------------------

-- Stores messages submitted via the landing page contact form
CREATE TABLE IF NOT EXISTS contact_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) DEFAULT NULL,               -- Optional (matches your form)
    email VARCHAR(255) NOT NULL,                  -- Required (matches your form)
    subject VARCHAR(255) NOT NULL,                -- Required (matches your form)
    message TEXT NOT NULL,                        -- Required (matches your form)
    category ENUM('general', 'support', 'technical', 'feedback', 'emergency') DEFAULT 'general',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    status ENUM('pending', 'in_review', 'replied', 'resolved') DEFAULT 'pending',
    is_read BOOLEAN DEFAULT FALSE,
    replied_by INT NULL,                          -- Admin who replied
    reply_message TEXT,                           -- Admin's reply
    replied_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (replied_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- STEP 4: INSERT DEFAULT ADMIN USER
-- -----------------------------------------------------------------------------
-- Password: admin123

INSERT INTO users (fname, lname, email, phone_number, role, password)
VALUES ('Admin', 'User', 'admin@silentsignal.com', '+639123456789', 'admin',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- -----------------------------------------------------------------------------
-- STEP 5: INSERT SAMPLE TEST USERS
-- -----------------------------------------------------------------------------
-- All passwords: admin123

-- PWD User - Juan Santos
INSERT INTO users (fname, lname, email, phone_number, role, password)
VALUES ('Juan', 'Santos', 'juan@example.com', '+639111111111', 'pwd',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

SET @pwd_id = LAST_INSERT_ID();

-- Family Member 1 - Maria Santos (Mother)
INSERT INTO users (fname, lname, email, phone_number, role, password)
VALUES ('Maria', 'Santos', 'maria@example.com', '+639123456789', 'family',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

SET @mother_id = LAST_INSERT_ID();

-- Family Member 2 - Jose Santos (Father)
INSERT INTO users (fname, lname, email, phone_number, role, password)
VALUES ('Jose', 'Santos', 'jose@example.com', '+639234567890', 'family',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

SET @father_id = LAST_INSERT_ID();

-- Family Member 3 - Ana Santos (Sister)
INSERT INTO users (fname, lname, email, phone_number, role, password)
VALUES ('Ana', 'Santos', 'ana@example.com', '+639345678901', 'family',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

SET @sister_id = LAST_INSERT_ID();

-- -----------------------------------------------------------------------------
-- STEP 6: CREATE FAMILY RELATIONSHIPS
-- -----------------------------------------------------------------------------

INSERT INTO family_pwd_relationships (family_member_id, pwd_user_id, relationship_type, is_primary_contact)
VALUES
    (@mother_id, @pwd_id, 'Mother', TRUE),
    (@father_id, @pwd_id, 'Father', FALSE),
    (@sister_id, @pwd_id, 'Sister', FALSE);

-- -----------------------------------------------------------------------------
-- STEP 7: INSERT SAMPLE PWD STATUS UPDATES
-- -----------------------------------------------------------------------------

INSERT INTO pwd_status_updates (pwd_user_id, status, latitude, longitude, battery_level, message)
VALUES
    (@pwd_id, 'safe', 10.6780, 122.9506, 85, 'At home, all good');

-- -----------------------------------------------------------------------------
-- STEP 8: INSERT SAMPLE EMERGENCY ALERT
-- -----------------------------------------------------------------------------

INSERT INTO emergency_alerts (user_id, alert_type, latitude, longitude, message, status, created_at)
VALUES
    (@pwd_id, 'sos', 10.6780, 122.9506, 'Emergency SOS activated', 'resolved', DATE_SUB(NOW(), INTERVAL 2 HOUR));

SET @alert_id = LAST_INSERT_ID();

-- -----------------------------------------------------------------------------
-- STEP 9: INSERT FAMILY RESPONSES TO THE ALERT
-- -----------------------------------------------------------------------------

INSERT INTO family_emergency_responses (alert_id, family_member_id, response_status, response_time)
VALUES
    (@alert_id, @mother_id, 'arrived', DATE_SUB(NOW(), INTERVAL 110 MINUTE)),
    (@alert_id, @father_id, 'acknowledged', DATE_SUB(NOW(), INTERVAL 115 MINUTE));

-- -----------------------------------------------------------------------------
-- STEP 10: INSERT SAMPLE DISASTER ALERT
-- -----------------------------------------------------------------------------

INSERT INTO disaster_alerts (alert_type, severity, location, description, affected_areas, status)
VALUES
    ('typhoon', 'high', 'Iloilo City', 'Typhoon approaching the area. Strong winds and heavy rainfall expected.',
    'Iloilo City, Guimaras, Capiz', 'active');

-- -----------------------------------------------------------------------------
-- STEP 11: INSERT SAMPLE CONTACT INQUIRIES (for testing)
-- -----------------------------------------------------------------------------

INSERT INTO contact_inquiries (name, email, subject, message, category, priority, status)
VALUES
    ('Maria Santos', 'maria.santos@gmail.com', 'Unable to Send SOS Alert',
    'I need to send an emergency alert but the app is not responding. Please help.',
    'emergency', 'urgent', 'pending'),

    ('Jerome Buntaliada', 'jerome.buntaliada@gmail.com', 'App Keeps Crashing on Shake',
    'The app keeps crashing whenever I enable the shake detection feature.',
    'technical', 'high', 'pending'),

    ('Jose Santos', 'jose.santos@gmail.com', 'Amazing App, Saved My Life!',
    'I want to thank you for creating this app. It has been a lifesaver for me.',
    'feedback', 'normal', 'pending'),

    ('Ana Santos', 'ana.santos@gmail.com', 'How to Add My Family Members?',
    'I am trying to add my family members to the family dashboard but I cannot find the option.',
    'support', 'normal', 'pending'),

    (NULL, 'luis.cruz@gmail.com', 'Request for FSL Video Tutorials',
    'Could you add more Filipino Sign Language tutorials to the app?',
    'general', 'normal', 'pending');

-- =============================================================================
-- END OF SCHEMA
-- =============================================================================

-- To verify the setup, you can run these queries:
-- SELECT * FROM users;
-- SELECT * FROM family_pwd_relationships;
-- SELECT * FROM pwd_status_updates;
-- SELECT * FROM emergency_alerts;
-- SELECT * FROM family_emergency_responses;
-- SELECT * FROM contact_inquiries;