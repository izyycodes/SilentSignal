-- =============================================================================
-- SILENT SIGNAL — COMPLETE DATABASE SCHEMA WITH DATA
-- Generated: 2026-02-19
-- Combines: rgdioma_silent_signal (production data) + full schema
-- Compatible with: MySQL 5.7+, MariaDB 10.3+, HelioHost, localhost
-- =============================================================================
-- Usage:
--   localhost:  CREATE DATABASE silent_signal; USE silent_signal;
--   HelioHost:  USE rgdioma_silent_signal; (database pre-created via cPanel)
-- =============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- =============================================================================
-- DROP EXISTING TABLES (safe re-import)
-- =============================================================================
DROP TABLE IF EXISTS `sms_events`;
DROP TABLE IF EXISTS `hub_media_logs`;
DROP TABLE IF EXISTS `family_broadcasts`;
DROP TABLE IF EXISTS `checkin_media_logs`;
DROP TABLE IF EXISTS `family_emergency_responses`;
DROP TABLE IF EXISTS `family_pwd_relationships`;
DROP TABLE IF EXISTS `pwd_emergency_contacts`;
DROP TABLE IF EXISTS `pwd_status_updates`;
DROP TABLE IF EXISTS `emergency_alerts`;
DROP TABLE IF EXISTS `disaster_alerts`;
DROP TABLE IF EXISTS `medical_profiles`;
DROP TABLE IF EXISTS `contact_inquiries`;
DROP TABLE IF EXISTS `users`;

-- =============================================================================
-- TABLE: users
-- =============================================================================
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `role` enum('pwd','family','admin') NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: contact_inquiries
-- =============================================================================
CREATE TABLE `contact_inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `category` enum('general','support','technical','feedback','emergency') DEFAULT 'general',
  `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
  `status` enum('pending','in_review','replied','resolved') DEFAULT 'pending',
  `is_read` tinyint(1) DEFAULT 0,
  `replied_by` int(11) DEFAULT NULL,
  `reply_message` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `replied_by` (`replied_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: disaster_alerts
-- =============================================================================
CREATE TABLE `disaster_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alert_type` enum('flood','earthquake','typhoon','fire','tsunami') NOT NULL,
  `severity` enum('low','moderate','high','critical') NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `affected_areas` text DEFAULT NULL,
  `status` enum('active','resolved') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: emergency_alerts (expanded ENUM for full compatibility)
-- =============================================================================
CREATE TABLE `emergency_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `alert_type` enum('sos','shake','panic_click','medical','assistance','fall_detection') NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `location_address` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('active','acknowledged','responded','resolved','cancelled') DEFAULT 'active',
  `priority` enum('low','medium','high','critical') DEFAULT 'high',
  `resolved_by` int(11) DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `resolved_by` (`resolved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: family_pwd_relationships
-- =============================================================================
CREATE TABLE `family_pwd_relationships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `family_member_id` int(11) NOT NULL,
  `pwd_user_id` int(11) NOT NULL,
  `relationship_type` varchar(50) NOT NULL,
  `is_primary_contact` tinyint(1) DEFAULT 0,
  `notification_enabled` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_relationship` (`family_member_id`,`pwd_user_id`),
  KEY `idx_family_member` (`family_member_id`),
  KEY `idx_pwd_user` (`pwd_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: family_emergency_responses
-- =============================================================================
CREATE TABLE `family_emergency_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alert_id` int(11) NOT NULL,
  `family_member_id` int(11) NOT NULL,
  `response_status` enum('notified','acknowledged','on_the_way','arrived','resolved') DEFAULT 'notified',
  `response_time` timestamp NULL DEFAULT NULL,
  `location_lat` decimal(10,8) DEFAULT NULL,
  `location_lng` decimal(11,8) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_alert` (`alert_id`),
  KEY `idx_family_member` (`family_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: medical_profiles
-- =============================================================================
CREATE TABLE `medical_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `pwd_id` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `disability_type` varchar(100) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `emergency_contacts` text DEFAULT NULL,
  `sms_template` text DEFAULT NULL,
  `medication_reminders` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_profile` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: pwd_emergency_contacts
-- Stores all emergency contacts for a PWD (registered + unregistered).
-- contact_user_id is set when the contact phone/email matches a registered user.
-- syncEmergencyContacts() in FamilyCheckin.php keeps this in sync with medical_profiles.
-- =============================================================================
CREATE TABLE `pwd_emergency_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pwd_user_id` int(11) NOT NULL,
  `contact_user_id` int(11) DEFAULT NULL,
  `contact_name` varchar(150) NOT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pwd_user` (`pwd_user_id`),
  KEY `idx_contact_user` (`contact_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: pwd_status_updates
-- =============================================================================
CREATE TABLE `pwd_status_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pwd_user_id` int(11) NOT NULL,
  `status` enum('safe','danger','unknown','needs_assistance') DEFAULT 'unknown',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `battery_level` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pwd_user` (`pwd_user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: sms_events
-- =============================================================================
CREATE TABLE `sms_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `messages` text NOT NULL,
  `contacts` text NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: hub_media_logs
-- =============================================================================
CREATE TABLE `hub_media_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `media_type` enum('photo','video') NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: checkin_media_logs
-- =============================================================================
CREATE TABLE `checkin_media_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `media_type` enum('photo','video') NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- TABLE: family_broadcasts
-- =============================================================================
CREATE TABLE `family_broadcasts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `pwd_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_sender` (`sender_id`),
  KEY `idx_pwd` (`pwd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- FOREIGN KEY CONSTRAINTS
-- =============================================================================
ALTER TABLE `contact_inquiries`
  ADD CONSTRAINT `contact_inquiries_ibfk_1` FOREIGN KEY (`replied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `emergency_alerts`
  ADD CONSTRAINT `emergency_alerts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `emergency_alerts_ibfk_2` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `family_emergency_responses`
  ADD CONSTRAINT `family_emergency_responses_ibfk_1` FOREIGN KEY (`alert_id`) REFERENCES `emergency_alerts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `family_emergency_responses_ibfk_2` FOREIGN KEY (`family_member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `family_pwd_relationships`
  ADD CONSTRAINT `family_pwd_relationships_ibfk_1` FOREIGN KEY (`family_member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `family_pwd_relationships_ibfk_2` FOREIGN KEY (`pwd_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `medical_profiles`
  ADD CONSTRAINT `medical_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `pwd_emergency_contacts`
  ADD CONSTRAINT `pwd_ec_ibfk_1` FOREIGN KEY (`pwd_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pwd_ec_ibfk_2` FOREIGN KEY (`contact_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `pwd_status_updates`
  ADD CONSTRAINT `pwd_status_updates_ibfk_1` FOREIGN KEY (`pwd_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `sms_events`
  ADD CONSTRAINT `sms_events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `hub_media_logs`
  ADD CONSTRAINT `hub_media_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `checkin_media_logs`
  ADD CONSTRAINT `checkin_media_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `family_broadcasts`
  ADD CONSTRAINT `family_broadcasts_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `family_broadcasts_ibfk_2` FOREIGN KEY (`pwd_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- =============================================================================
-- DATA: users
-- =============================================================================
INSERT INTO `users` VALUES
(9,'Aizhelle','de la Cruz','aizhellegwynneth@gmail.com','09949771317','pwd','$2y$12$6ax4EhNJhmIJJ/AaFclaX.P2F.JUTktx23685ipBFnJaBU14neX3m',1,1,'2026-02-18 13:46:43','2026-02-08 11:57:52','2026-02-18 13:46:43'),
(11,'Reghis','Dioma','rgdioma@gmail.com','09612045422','admin','$2y$12$36OMt6ZQt/zB35naI3psuOp77TRGQjqLGGFeIc3efz.Qcn.DS4wCO',1,1,'2026-02-18 13:46:43','2026-02-08 16:10:22','2026-02-18 13:46:43'),
(12,'Jerome','Buntalidad','jeromebuntalidad@gmail.com','09162360648','family','$2y$12$SmgOSZxg0O.TZCdjATtM7.svqgboifxXPZqF6IRUB5CwWTx5DngRa',1,1,'2026-02-18 13:46:43','2026-02-08 16:12:27','2026-02-18 13:46:43'),
(17,'Family','Test','family@gmail.com','09998546215','family','$2y$12$B2k2g/nUR95mYvioTIf9guVZ0.984U9j4Asmw3msPbzA8Cg9vEI2e',1,1,'2026-02-18 13:46:43','2026-02-12 02:40:52','2026-02-18 13:46:43'),
(18,'Admin','Test','admin@gmail.com','09563248615','admin','$2y$12$4M8Luoritb12UcCKCUoTPuO6yXrrBl3c/vf6XAqRpIXM7RwTWpwSi',1,1,'2026-02-18 13:46:43','2026-02-12 02:42:00','2026-02-18 13:46:43'),
(19,'User','Test','user@gmail.com','09875412658','pwd','$2y$12$N7fdmBsRpRSTSVzZDLKG3eCLoCMORnoVBfYXpAjLrhZq.4KQmwQiG',1,1,'2026-02-18 13:46:43','2026-02-12 02:43:42','2026-02-18 13:46:43'),
(24,'Juan','Santos','juan@example.com','+639111111111','pwd','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-02-18 13:46:43','2026-02-17 11:57:24','2026-02-18 13:46:43'),
(25,'Maria','Santos','maria@example.com','+639123456789','family','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-02-18 13:46:43','2026-02-17 11:57:24','2026-02-18 13:46:43'),
(26,'Jose','Santos','jose@example.com','+639234567890','family','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-02-18 13:46:43','2026-02-17 11:57:24','2026-02-18 13:46:43'),
(27,'Ana','Santos','ana@example.com','+639345678901','family','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-02-18 13:46:43','2026-02-17 11:57:24','2026-02-18 13:46:43'),
(29,'Admin','User','admin@silentsignal.com','+639123456789','admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-02-18 14:20:37','2026-02-18 14:00:38','2026-02-18 14:20:37'),
(31,'Marisol','Samillano','marisolsamillano@gmail.com','09558697412','pwd','$2y$12$QIq2j8ltUn/GMnbmGfSyDuNyh2hBpncC2TGpPOV2tPgeOjIP.WvCK',1,1,'2026-02-18 14:15:02','2026-02-18 14:10:50','2026-02-18 14:15:02');

-- =============================================================================
-- DATA: contact_inquiries
-- =============================================================================
INSERT INTO `contact_inquiries` (`id`,`name`,`email`,`subject`,`message`,`category`,`priority`,`status`,`is_read`,`replied_by`,`reply_message`,`replied_at`,`created_at`,`updated_at`) VALUES
(1,'Maria Santos','maria.santos@gmail.com','Unable to Send SOS Alert','I need to send an emergency alert but the app is not responding. Please help.','emergency','urgent','pending',0,NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08'),
(2,'Jerome Buntaliada','jerome.buntaliada@gmail.com','App Keeps Crashing on Shake','The app keeps crashing whenever I enable the shake detection feature.','technical','high','pending',0,NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08'),
(3,'Jose Santos','jose.santos@gmail.com','Amazing App, Saved My Life!','I want to thank you for creating this app. It has been a lifesaver for me.','feedback','normal','pending',0,NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08'),
(4,'Ana Santos','ana.santos@gmail.com','How to Add My Family Members?','I am trying to add my family members to the family dashboard but I cannot find the option.','support','normal','pending',0,NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08'),
(5,NULL,'luis.cruz@gmail.com','Request for FSL Video Tutorials','Could you add more Filipino Sign Language tutorials to the app?','general','normal','pending',0,NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08');

-- =============================================================================
-- DATA: disaster_alerts
-- =============================================================================
INSERT INTO `disaster_alerts` VALUES
(1,'typhoon','high','Iloilo City','Typhoon approaching the area. Strong winds and heavy rainfall expected.','Iloilo City, Guimaras, Capiz','active','2026-02-18 14:07:08','2026-02-18 14:07:08');

-- =============================================================================
-- DATA: emergency_alerts
-- =============================================================================
INSERT INTO `emergency_alerts` (`id`,`user_id`,`alert_type`,`latitude`,`longitude`,`location_address`,`message`,`status`,`priority`,`resolved_by`,`resolved_at`,`notes`,`created_at`) VALUES
(4,24,'sos',10.67800000,122.95060000,NULL,'Emergency SOS activated','resolved','high',NULL,NULL,NULL,'2026-02-17 09:57:24');

-- =============================================================================
-- DATA: family_pwd_relationships
-- =============================================================================
INSERT INTO `family_pwd_relationships` VALUES
(7,25,24,'Mother',1,1,'2026-02-18 14:05:11'),
(8,26,24,'Father',0,1,'2026-02-18 14:05:11'),
(9,27,24,'Sister',0,1,'2026-02-18 14:05:11');

-- =============================================================================
-- DATA: family_emergency_responses
-- =============================================================================
INSERT INTO `family_emergency_responses` (`id`,`alert_id`,`family_member_id`,`response_status`,`response_time`,`location_lat`,`location_lng`,`notes`,`created_at`,`updated_at`) VALUES
(1,4,25,'arrived','2026-02-18 12:17:08',NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08'),
(2,4,26,'acknowledged','2026-02-18 12:12:08',NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08');

-- =============================================================================
-- DATA: medical_profiles
-- =============================================================================
INSERT INTO `medical_profiles` (`id`,`user_id`,`first_name`,`last_name`,`date_of_birth`,`gender`,`pwd_id`,`phone`,`email`,`street_address`,`city`,`province`,`zip_code`,`disability_type`,`blood_type`,`allergies`,`medications`,`medical_conditions`,`emergency_contacts`,`sms_template`,`medication_reminders`,`created_at`,`updated_at`) VALUES
(2,9,'Aizhelle','de la Cruz',NULL,'Female','','09949771317','aizhellegwynneth@gmail.com','Montinola Street','Victorias City','Negros Occidental','6120','Deaf/Mute','O+','[\"seafood\"]','[\"biogesic\"]','[\"social anxiety\"]','[{\"name\":\"Wynn de la Cruz\",\"relation\":\"Father\",\"phone\":\"09162360648\",\"initials\":\"WD\",\"color\":\"rgb(229, 57, 53)\"}]','EMERGENCY ALERT - USER IS DEAF/MUTE - TEXT ONLY','[{\"name\":\"Biogesic\",\"frequency\":\"Once daily\",\"time\":\"8:00 AM, 8:00 PM\",\"color\":\"rgb(76, 175, 80)\"}]','2026-02-08 11:57:52','2026-02-18 10:00:36'),
(8,19,'User','Test',NULL,'Female','PWD-080-123','09875412658','user@gmail.com','','','','','Deaf/Mute','A+','[\"Seafood\"]','[\"Antibiotics\"]','[]','[{\"name\":\"Contact\",\"relation\":\"Father\",\"phone\":\"09994105502\",\"initials\":\"C\",\"color\":\"rgb(142, 36, 170)\"}]','EMERGENCY ALERT - USER IS DEAF/MUTE - TEXT ONLY','[]','2026-02-12 02:43:42','2026-02-18 14:21:05'),
(10,31,'Marisol','Samillano',NULL,'Male','PWD-987-654','09558697412','marisolsamillano@gmail.com','','Isabela','Negros Occidental','6128','Deaf/Mute','AB+','[\"peanuts\"]','[]','[\"social anxiety\"]','[]','EMERGENCY ALERT - USER IS DEAF/MUTE - TEXT ONLY','[]','2026-02-18 14:10:50','2026-02-18 14:12:07'),
(11,24,'Juan','Santos',NULL,'Male','PWD-225-362','+639111111111','juan@example.com','','','','','Deaf/Mute','A+','[]','[]','[]','[]','EMERGENCY ALERT - USER IS DEAF/MUTE - TEXT ONLY','[]','2026-02-18 14:18:50','2026-02-18 14:18:50');

-- =============================================================================
-- DATA: pwd_status_updates
-- =============================================================================
INSERT INTO `pwd_status_updates` VALUES
(1,24,'safe',10.67800000,122.95060000,85,'At home, all good','2026-02-18 14:05:46');

-- =============================================================================
-- AUTO_INCREMENT
-- =============================================================================
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
ALTER TABLE `contact_inquiries` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `disaster_alerts` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `emergency_alerts` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `family_emergency_responses` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `family_pwd_relationships` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
ALTER TABLE `medical_profiles` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
ALTER TABLE `pwd_emergency_contacts` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `pwd_status_updates` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- =============================================================================
-- TEST ACCOUNT REFERENCE (password = "password" for @example.com accounts)
-- admin@gmail.com           Admin Test (Admin)
-- rgdioma@gmail.com         Reghis Dioma (Admin)
-- admin@silentsignal.com    Admin User (Admin) - password: password
-- family@gmail.com          Family Test (Family)
-- maria@example.com         Maria Santos (Family) - password: password
-- jose@example.com          Jose Santos (Family)  - password: password
-- ana@example.com           Ana Santos (Family)   - password: password
-- user@gmail.com            User Test (PWD)
-- juan@example.com          Juan Santos (PWD)     - password: password
-- aizhellegwynneth@gmail.com Aizhelle de la Cruz (PWD)
-- marisolsamillano@gmail.com Marisol Samillano (PWD)
-- =============================================================================