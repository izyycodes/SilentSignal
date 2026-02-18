-- =============================================================================
-- SILENT SIGNAL — FULL DATABASE SCHEMA + SHOWCASE DATA
-- Generated: 2026-02-19
-- Compatible: MySQL 5.7+, MariaDB 10.3+, HelioHost, localhost/XAMPP
-- =============================================================================
-- localhost:  CREATE DATABASE silent_signal; USE silent_signal;
-- HelioHost:  USE rgdioma_silent_signal;
-- =============================================================================
-- ALL ORIGINAL DATA IS PRESERVED EXACTLY AS-IS.
-- Demo accounts all use password:  password
--   (bcrypt: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)
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
-- TABLE: emergency_alerts
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
-- IDs used:
--   ORIGINAL:  9, 11, 12, 17, 18, 19, 24, 25, 26, 27, 29, 31
--   DEMO PWDs: 33 (Lorenzo), 34 (Rosa), 35 (Carlo)
--   DEMO FAM:  36 (Elena/Lorenzo-Mom), 37 (Ramon/Lorenzo-Dad), 38 (Sofia/Lorenzo-Sister)
--              39 (Benita/Rosa-Mom), 40 (Danilo/Rosa-Dad)
--              41 (Patricia/Carlo-Mom), 42 (Miguel/Carlo-Dad)
-- password hash for all @example.com & demo accounts = "password"
-- =============================================================================
INSERT INTO `users` (`id`,`fname`,`lname`,`email`,`phone_number`,`role`,`password`,`is_verified`,`is_active`,`verified_at`,`created_at`,`updated_at`) VALUES

-- ─── ORIGINAL ACCOUNTS (untouched) ──────────────────────────────────────────
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
(31,'Marisol','Samillano','marisolsamillano@gmail.com','09558697412','pwd','$2y$12$QIq2j8ltUn/GMnbmGfSyDuNyh2hBpncC2TGpPOV2tPgeOjIP.WvCK',1,1,'2026-02-18 14:15:02','2026-02-18 14:10:50','2026-02-18 14:15:02'),

-- ─── DEMO: PWD USERS ────────────────────────────────────────────────────────
-- Lorenzo Villanueva — Deaf/Mute, Bacolod City (password: password)
(33,'Lorenzo','Villanueva','lorenzo@example.com','09171234560','pwd','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-01-10 08:00:00','2026-01-10 08:00:00','2026-02-19 07:30:00'),
-- Rosa Magbanua — Hard of Hearing + Diabetes, Iloilo City (password: password)
(34,'Rosa','Magbanua','rosa@example.com','09182345670','pwd','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-01-12 09:00:00','2026-01-12 09:00:00','2026-02-19 07:10:00'),
-- Carlo Reyes — Deaf/Mute, Cebu City (password: password)
(35,'Carlo','Reyes','carlo@example.com','09193456780','pwd','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-01-15 10:00:00','2026-01-15 10:00:00','2026-02-19 08:10:00'),

-- ─── DEMO: FAMILY MEMBERS — VILLANUEVA (Lorenzo's family) ───────────────────
(36,'Elena','Villanueva','elena@example.com','09204567890','family','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-01-10 08:30:00','2026-01-10 08:30:00','2026-02-19 07:35:00'),
(37,'Ramon','Villanueva','ramon@example.com','09215678901','family','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-01-10 08:35:00','2026-01-10 08:35:00','2026-02-19 07:20:00'),
(38,'Sofia','Villanueva','sofia@example.com','09226789012','family','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-01-10 09:00:00','2026-01-10 09:00:00','2026-02-19 06:00:00'),

-- ─── DEMO: FAMILY MEMBERS — MAGBANUA (Rosa's family) ────────────────────────
(39,'Benita','Magbanua','benita@example.com','09237890123','family','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-01-12 09:30:00','2026-01-12 09:30:00','2026-02-19 07:15:00'),
(40,'Danilo','Magbanua','danilo@example.com','09248901234','family','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-01-12 09:45:00','2026-01-12 09:45:00','2026-02-19 06:30:00'),

-- ─── DEMO: FAMILY MEMBERS — REYES (Carlo's family) ──────────────────────────
(41,'Patricia','Reyes','patricia@example.com','09259012345','family','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-01-15 10:30:00','2026-01-15 10:30:00','2026-02-19 08:05:00'),
(42,'Miguel','Reyes','miguel@example.com','09260123456','family','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,1,'2026-01-15 10:45:00','2026-01-15 10:45:00','2026-02-19 07:55:00');

-- =============================================================================
-- DATA: medical_profiles
-- =============================================================================
INSERT INTO `medical_profiles` (`id`,`user_id`,`first_name`,`last_name`,`date_of_birth`,`gender`,`pwd_id`,`phone`,`email`,`street_address`,`city`,`province`,`zip_code`,`disability_type`,`blood_type`,`allergies`,`medications`,`medical_conditions`,`emergency_contacts`,`sms_template`,`medication_reminders`,`created_at`,`updated_at`) VALUES

-- ─── ORIGINAL PROFILES (untouched — exact bytes preserved) ──────────────────
(2,9,'Aizhelle','de la Cruz',NULL,'Female','','09949771317','aizhellegwynneth@gmail.com','Montinola Street','Victorias City','Negros Occidental','6120','Deaf/Mute','O+','[\"seafood\"]','[\"biogesic\"]','[\"social anxiety\"]','[{\"name\":\"Wynn de la Cruz\",\"relation\":\"Father\",\"phone\":\"09162360648\",\"initials\":\"WD\",\"color\":\"rgb(229, 57, 53)\"}]','EMERGENCY ALERT - USER IS DEAF/MUTE - TEXT ONLY','[{\"name\":\"Biogesic\",\"frequency\":\"Once daily\",\"time\":\"8:00 AM, 8:00 PM\",\"color\":\"rgb(76, 175, 80)\"}]','2026-02-08 11:57:52','2026-02-18 10:00:36'),
(8,19,'User','Test',NULL,'Female','PWD-080-123','09875412658','user@gmail.com','','','','','Deaf/Mute','A+','[\"Seafood\"]','[\"Antibiotics\"]','[]','[{\"name\":\"Contact\",\"relation\":\"Father\",\"phone\":\"09994105502\",\"initials\":\"C\",\"color\":\"rgb(142, 36, 170)\"}]','EMERGENCY ALERT - USER IS DEAF/MUTE - TEXT ONLY','[]','2026-02-12 02:43:42','2026-02-18 14:21:05'),
(10,31,'Marisol','Samillano',NULL,'Male','PWD-987-654','09558697412','marisolsamillano@gmail.com','','Isabela','Negros Occidental','6128','Deaf/Mute','AB+','[\"peanuts\"]','[]','[\"social anxiety\"]','[]','EMERGENCY ALERT - USER IS DEAF/MUTE - TEXT ONLY','[]','2026-02-18 14:10:50','2026-02-18 14:12:07'),
(11,24,'Juan','Santos',NULL,'Male','PWD-225-362','+639111111111','juan@example.com','','','','','Deaf/Mute','A+','[]','[]','[]','[]','EMERGENCY ALERT - USER IS DEAF/MUTE - TEXT ONLY','[]','2026-02-18 14:18:50','2026-02-18 14:18:50'),

-- ─── DEMO PROFILES ───────────────────────────────────────────────────────────

-- Lorenzo Villanueva (id 33) — Deaf/Mute, mild hypertension, Bacolod
(12,33,'Lorenzo','Villanueva','2001-03-14','Male','PWD-NOC-2026-001','09171234560','lorenzo@example.com','143 Lacson Street, Bacolod City Proper','Bacolod City','Negros Occidental','6100','Deaf/Mute','B+',
'[\"Penicillin\",\"Shellfish\"]',
'[\"Furosemide 40mg\",\"Vitamin B Complex\"]',
'[\"Mild hypertension\",\"Congenital bilateral hearing loss\"]',
'[{\"name\":\"Elena Villanueva\",\"relation\":\"Mother\",\"phone\":\"09204567890\",\"initials\":\"EV\",\"color\":\"rgb(25, 118, 210)\"},{\"name\":\"Ramon Villanueva\",\"relation\":\"Father\",\"phone\":\"09215678901\",\"initials\":\"RV\",\"color\":\"rgb(67, 160, 71)\"},{\"name\":\"Sofia Villanueva\",\"relation\":\"Sister\",\"phone\":\"09226789012\",\"initials\":\"SV\",\"color\":\"rgb(239, 108, 0)\"}]',
'EMERGENCY ALERT — USER IS DEAF/MUTE — RESPOND VIA TEXT ONLY\n\nName: Lorenzo Villanueva | PWD-NOC-2026-001\nPhone: 09171234560 | Blood Type: B+\nAddress: 143 Lacson Street, Bacolod City\nAllergies: Penicillin, Shellfish\nMedications: Furosemide 40mg, Vitamin B Complex',
'[{\"name\":\"Furosemide 40mg\",\"frequency\":\"Once daily\",\"time\":\"7:00 AM\",\"color\":\"rgb(239, 108, 0)\"},{\"name\":\"Vitamin B Complex\",\"frequency\":\"Once daily\",\"time\":\"8:00 AM\",\"color\":\"rgb(25, 118, 210)\"}]',
'2026-01-10 08:10:00','2026-02-19 07:00:00'),

-- Rosa Magbanua (id 34) — Hard of Hearing, Type 2 Diabetes + Hypertension, Iloilo City
(13,34,'Rosa','Magbanua','1998-07-22','Female','PWD-ILO-2026-002','09182345670','rosa@example.com','88 Iznart Street, Barangay San Jose','Iloilo City','Iloilo','5000','Hard of Hearing','O-',
'[\"Latex\",\"Aspirin\"]',
'[\"Metformin 500mg\",\"Losartan 50mg\",\"Glimepiride 2mg\"]',
'[\"Type 2 Diabetes Mellitus\",\"Hypertension\",\"Moderate sensorineural hearing loss\"]',
'[{\"name\":\"Benita Magbanua\",\"relation\":\"Mother\",\"phone\":\"09237890123\",\"initials\":\"BM\",\"color\":\"rgb(229, 57, 53)\"},{\"name\":\"Danilo Magbanua\",\"relation\":\"Father\",\"phone\":\"09248901234\",\"initials\":\"DM\",\"color\":\"rgb(156, 39, 176)\"}]',
'EMERGENCY ALERT — USER HAS HEARING DISABILITY — PREFER TEXT\n\nName: Rosa Magbanua | PWD-ILO-2026-002\nPhone: 09182345670 | Blood Type: O-\nAddress: 88 Iznart Street, Iloilo City\nDiabetic — may need glucose if unresponsive\nAllergies: Latex, Aspirin',
'[{\"name\":\"Metformin 500mg\",\"frequency\":\"Twice daily\",\"time\":\"7:00 AM, 7:00 PM\",\"color\":\"rgb(76, 175, 80)\"},{\"name\":\"Losartan 50mg\",\"frequency\":\"Once daily\",\"time\":\"7:00 AM\",\"color\":\"rgb(229, 57, 53)\"},{\"name\":\"Glimepiride 2mg\",\"frequency\":\"Once daily\",\"time\":\"Before breakfast\",\"color\":\"rgb(255, 193, 7)\"}]',
'2026-01-12 09:05:00','2026-02-19 06:30:00'),

-- Carlo Reyes (id 35) — Deaf/Mute, Hypothyroidism, Cebu City
(14,35,'Carlo','Reyes','2003-11-05','Male','PWD-CEB-2026-003','09193456780','carlo@example.com','22 V. Rama Avenue, Barangay Luz','Cebu City','Cebu','6000','Deaf/Mute','AB-',
'[\"Sulfa drugs\",\"Iodine-based contrast\"]',
'[\"Levothyroxine 50mcg\"]',
'[\"Congenital hypothyroidism\",\"Profound bilateral deafness since birth\"]',
'[{\"name\":\"Patricia Reyes\",\"relation\":\"Mother\",\"phone\":\"09259012345\",\"initials\":\"PR\",\"color\":\"rgb(25, 118, 210)\"},{\"name\":\"Miguel Reyes\",\"relation\":\"Father\",\"phone\":\"09260123456\",\"initials\":\"MR\",\"color\":\"rgb(67, 160, 71)\"}]',
'EMERGENCY ALERT — USER IS DEAF/MUTE — RESPOND VIA TEXT ONLY\n\nName: Carlo Reyes | PWD-CEB-2026-003\nPhone: 09193456780 | Blood Type: AB-\nAddress: 22 V. Rama Avenue, Cebu City\nAllergies: Sulfa drugs, Iodine-based contrast\nMedication: Levothyroxine 50mcg (daily, 6:30 AM)',
'[{\"name\":\"Levothyroxine 50mcg\",\"frequency\":\"Once daily\",\"time\":\"6:30 AM — take on empty stomach\",\"color\":\"rgb(156, 39, 176)\"}]',
'2026-01-15 10:10:00','2026-02-19 08:00:00');

-- =============================================================================
-- DATA: family_pwd_relationships
-- =============================================================================
INSERT INTO `family_pwd_relationships` (`id`,`family_member_id`,`pwd_user_id`,`relationship_type`,`is_primary_contact`,`notification_enabled`,`created_at`) VALUES

-- ─── ORIGINAL (untouched) ────────────────────────────────────────────────────
(7,25,24,'Mother',1,1,'2026-02-18 14:05:11'),
(8,26,24,'Father',0,1,'2026-02-18 14:05:11'),
(9,27,24,'Sister',0,1,'2026-02-18 14:05:11'),

-- ─── DEMO: Villanueva family → Lorenzo (33) ─────────────────────────────────
(10,36,33,'Mother',1,1,'2026-01-10 08:30:00'),
(11,37,33,'Father',0,1,'2026-01-10 08:35:00'),
(12,38,33,'Sister',0,1,'2026-01-10 09:00:00'),

-- ─── DEMO: Magbanua family → Rosa (34) ──────────────────────────────────────
(13,39,34,'Mother',1,1,'2026-01-12 09:30:00'),
(14,40,34,'Father',0,1,'2026-01-12 09:45:00'),

-- ─── DEMO: Reyes family → Carlo (35) ────────────────────────────────────────
(15,41,35,'Mother',1,1,'2026-01-15 10:30:00'),
(16,42,35,'Father',0,1,'2026-01-15 10:45:00');

-- =============================================================================
-- DATA: pwd_emergency_contacts
-- Mirrors medical_profiles emergency_contacts JSON for registered users
-- =============================================================================
INSERT INTO `pwd_emergency_contacts` (`id`,`pwd_user_id`,`contact_user_id`,`contact_name`,`contact_phone`,`relationship`,`is_primary`,`created_at`) VALUES

-- Juan Santos (24) — family already in family_pwd_relationships
(1,24,25,'Maria Santos','+639123456789','Mother',1,'2026-02-18 14:19:00'),
(2,24,26,'Jose Santos','+639234567890','Father',0,'2026-02-18 14:19:00'),
(3,24,27,'Ana Santos','+639345678901','Sister',0,'2026-02-18 14:19:00'),

-- Lorenzo Villanueva (33)
(4,33,36,'Elena Villanueva','09204567890','Mother',1,'2026-01-10 08:10:00'),
(5,33,37,'Ramon Villanueva','09215678901','Father',0,'2026-01-10 08:10:00'),
(6,33,38,'Sofia Villanueva','09226789012','Sister',0,'2026-01-10 08:10:00'),

-- Rosa Magbanua (34)
(7,34,39,'Benita Magbanua','09237890123','Mother',1,'2026-01-12 09:05:00'),
(8,34,40,'Danilo Magbanua','09248901234','Father',0,'2026-01-12 09:05:00'),

-- Carlo Reyes (35)
(9,35,41,'Patricia Reyes','09259012345','Mother',1,'2026-01-15 10:10:00'),
(10,35,42,'Miguel Reyes','09260123456','Father',0,'2026-01-15 10:10:00');

-- =============================================================================
-- DATA: pwd_status_updates
-- Latest row per PWD is what shows on the family dashboard.
-- Multiple rows per person build up a realistic history.
-- =============================================================================
INSERT INTO `pwd_status_updates` (`id`,`pwd_user_id`,`status`,`latitude`,`longitude`,`battery_level`,`message`,`created_at`) VALUES

-- ─── ORIGINAL (untouched) ────────────────────────────────────────────────────
(1,24,'safe',10.67800000,122.95060000,85,'At home, all good','2026-02-18 14:05:46'),

-- ─── Juan Santos — history leading to latest safe ───────────────────────────
(2,24,'needs_assistance',10.67750000,122.95020000,62,'Need help getting to evacuation center','2026-02-17 09:50:00'),
(3,24,'safe',10.67800000,122.95060000,78,'Back home safely, thank you','2026-02-17 11:30:00'),

-- ─── Lorenzo Villanueva — history, currently SAFE ───────────────────────────
(4,33,'safe',10.68100000,122.96200000,95,'At home before typhoon','2026-02-19 04:00:00'),
(5,33,'danger',10.68150000,122.96250000,72,'Strong winds, window broke, I am scared','2026-02-19 05:28:00'),
(6,33,'safe',10.68200000,122.96400000,70,'OK now, Elena is here with me','2026-02-19 05:50:00'),

-- ─── Rosa Magbanua — ACTIVE needs_assistance at public market ───────────────
(7,34,'safe',10.72100000,122.56300000,80,'Going to market with nanay','2026-02-19 05:30:00'),
(8,34,'needs_assistance',10.72350000,122.56520000,55,'Feeling dizzy and weak. Sugar might be low. I sat down near the entrance.','2026-02-19 06:41:00'),

-- ─── Carlo Reyes — SAFE, at school ──────────────────────────────────────────
(9,35,'safe',10.31600000,123.89100000,91,'At home before school','2026-02-19 05:45:00'),
(10,35,'danger',10.31580000,123.89050000,88,'Earthquake! Things are falling. I am hiding under my desk.','2026-02-19 06:15:00'),
(11,35,'safe',10.31600000,123.89100000,85,'Earthquake stopped. I am okay. House has minor cracks only.','2026-02-19 07:55:00'),

-- ─── Aizhelle de la Cruz — safe at home ─────────────────────────────────────
(12,9,'safe',10.90600000,123.02700000,79,'At home, monitoring typhoon updates','2026-02-19 06:00:00'),

-- ─── Marisol Samillano — unknown, low battery ───────────────────────────────
(13,31,'unknown',NULL,NULL,8,'Near Isabela market, going home soon','2026-02-18 18:30:00');

-- =============================================================================
-- DATA: disaster_alerts
-- =============================================================================
INSERT INTO `disaster_alerts` (`id`,`alert_type`,`severity`,`location`,`description`,`affected_areas`,`status`,`created_at`,`updated_at`) VALUES

-- ─── ORIGINAL (untouched) ────────────────────────────────────────────────────
(1,'typhoon','high','Iloilo City','Typhoon approaching the area. Strong winds and heavy rainfall expected.','Iloilo City, Guimaras, Capiz','active','2026-02-18 14:07:08','2026-02-18 14:07:08'),

-- ─── DEMO DISASTERS ──────────────────────────────────────────────────────────
-- Active typhoon over Negros Occidental (affects Lorenzo and Aizhelle)
(2,'typhoon','critical','Bacolod City','Super Typhoon Caloy making landfall over Negros Occidental. Signal No. 4 raised. Coastal barangays must evacuate immediately. Life-threatening storm surge expected along Guimaras Strait.','Bacolod City, Silay City, Talisay City, Victorias City, Negros Occidental coastline','active','2026-02-19 03:00:00','2026-02-19 05:00:00'),

-- Active earthquake in Cebu (affects Carlo)
(3,'earthquake','high','Cebu City','Magnitude 5.9 earthquake struck 12 km northeast of Cebu City at a depth of 10 km. Felt strongly in Metro Cebu. Aftershocks expected. PHIVOLCS monitoring. No tsunami warning issued.','Cebu City, Mandaue City, Lapu-Lapu City, Talisay, Carcar, Naga','active','2026-02-19 06:14:00','2026-02-19 06:14:00'),

-- Active flood in Iloilo (affects Rosa)
(4,'flood','moderate','Iloilo City','Continuous overnight rainfall caused flash flooding in low-lying barangays. Water levels 0.5–1.2 meters in affected areas. NDRRMC monitoring.','Barangay San Jose, Barangay Molo, Barangay Lapaz, Barangay Arevalo, Barangay Mansaya','active','2026-02-19 02:30:00','2026-02-19 06:00:00'),

-- Resolved fire in Cebu
(5,'fire','moderate','Cebu City','Fire broke out in a residential area near Carbon Public Market. Barangay Ermita most affected. BFP responded with 8 units. Fire under control after 2 hours.','Barangay Ermita, Barangay Parian, Barangay Duljo-Fatima','resolved','2026-02-18 18:45:00','2026-02-18 21:30:00'),

-- Resolved flood in Iloilo (earlier)
(6,'flood','low','Iloilo City','Minor flooding in barangays adjacent to Iloilo River after Typhoon Inday. Water levels now receding. Residents advised to stay vigilant.','Barangay San Isidro, Barangay Sto. Nino Norte, Barangay Balabago','resolved','2026-02-17 06:00:00','2026-02-17 15:30:00');

-- =============================================================================
-- DATA: emergency_alerts
-- =============================================================================
INSERT INTO `emergency_alerts` (`id`,`user_id`,`alert_type`,`latitude`,`longitude`,`location_address`,`message`,`status`,`priority`,`resolved_by`,`resolved_at`,`notes`,`created_at`) VALUES

-- ─── ORIGINAL (untouched) ────────────────────────────────────────────────────
(4,24,'sos',10.67800000,122.95060000,NULL,'Emergency SOS activated','resolved','high',NULL,NULL,NULL,'2026-02-17 09:57:24'),

-- ─── DEMO ALERTS ─────────────────────────────────────────────────────────────

-- Alert 5: Lorenzo — shake detected during typhoon (RESOLVED)
(5,33,'shake',10.68150000,122.96250000,'143 Lacson Street, Bacolod City','Strong shake detected. Lorenzo may have fallen during typhoon.','resolved','high',36,'2026-02-19 05:48:00','Lorenzo okay — phone fell off the table during violent winds. Elena confirmed he is safe inside.','2026-02-19 05:30:00'),

-- Alert 6: Lorenzo — panic click, earlier typhoon panic (RESOLVED)
(6,33,'panic_click',10.68100000,122.96200000,'143 Lacson Street, Bacolod City','Panic button pressed. Lorenzo is frightened by the typhoon.','resolved','critical',36,'2026-02-19 05:00:00','Lorenzo was scared by the storm. Elena called to reassure him. False alarm confirmed by family.','2026-02-19 04:45:00'),

-- Alert 7: Rosa — ACTIVE SOS at Iloilo public market (hypoglycemic episode)
(7,34,'sos',10.72350000,122.56520000,'Iloilo City Public Market, near Iznart entrance, Iloilo City','SOS triggered. Rosa is sitting near the market entrance feeling faint and dizzy. Diabetic episode suspected.','active','critical',NULL,NULL,NULL,'2026-02-19 06:42:00'),

-- Alert 8: Rosa — medical alert 45 minutes before the SOS (RESPONDED)
(8,34,'medical',10.72100000,122.56300000,'88 Iznart Street, Iloilo City','Medical alert sent. Rosa reports her glucose monitor reading is 61 mg/dL. Heading to market but feeling unsteady.','responded','high',NULL,NULL,NULL,'2026-02-19 05:58:00'),

-- Alert 9: Carlo — fall detection while commuting to school (RESOLVED)
(9,35,'fall_detection',10.31580000,123.89050000,'V. Rama Avenue corner Leon Kilat Street, Cebu City','Fall detected. Carlo tripped on an uneven pavement while walking to school.','resolved','high',41,'2026-02-19 07:20:00','Carlo had a minor fall. Small abrasion on left knee. Patricia arrived within 8 minutes. He continued to school after first aid.','2026-02-19 07:05:00'),

-- Alert 10: Carlo — SOS during the 5.9 earthquake (RESOLVED)
(10,35,'sos',10.31600000,123.89100000,'22 V. Rama Avenue, Barangay Luz, Cebu City','SOS during earthquake. Carlo is alone at home. Bookshelves fell. He is scared and asking for help.','resolved','critical',41,'2026-02-19 07:30:00','Carlo was frightened but physically uninjured. Patricia arrived within 15 minutes. House sustained minor cracks only. Family evacuated to an open area as precaution.','2026-02-19 06:17:00'),

-- Alert 11: Aizhelle — shake during evacuation (RESOLVED)
(11,9,'shake',10.90600000,123.02700000,'Montinola Street, Victorias City, Negros Occidental','Shake detected while Aizhelle was moving belongings during evacuation preparation.','resolved','high',NULL,NULL,'Aizhelle was safe — vibration from carrying heavy bags triggered the sensor. Confirmed by her father Wynn.','2026-02-18 14:28:00'),

-- Alert 12: Juan Santos — older SOS during last typhoon (RESOLVED)
(12,24,'sos',10.67750000,122.95010000,'Brgy. 1, near Rizal Street, Bacolod City area','SOS pressed. Juan is asking for help getting to the evacuation center. He cannot hear announcements.','resolved','critical',25,'2026-02-17 10:40:00','Maria arrived and accompanied Juan to the Bacolod City Sports Complex evacuation center. He is safe.','2026-02-17 09:55:00'),

-- Alert 13: Rosa — past assistance alert, 2 days ago (RESOLVED)
(13,34,'assistance',10.72050000,122.56200000,'88 Iznart Street, Iloilo City','Rosa needs help taking her medications. Glucose is normal but she needs assistance with her prescription refill.','resolved','medium',39,'2026-02-17 14:30:00','Benita went to the pharmacy and brought Rosa her Metformin refill. All good.','2026-02-17 13:45:00');

-- =============================================================================
-- DATA: family_emergency_responses
-- =============================================================================
INSERT INTO `family_emergency_responses` (`id`,`alert_id`,`family_member_id`,`response_status`,`response_time`,`location_lat`,`location_lng`,`notes`,`created_at`,`updated_at`) VALUES

-- ─── ORIGINAL (untouched) ────────────────────────────────────────────────────
(1,4,25,'arrived','2026-02-18 12:17:08',NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08'),
(2,4,26,'acknowledged','2026-02-18 12:12:08',NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08'),

-- ─── DEMO RESPONSES ──────────────────────────────────────────────────────────

-- Alert 5 (Lorenzo shake, typhoon): Elena resolved, Ramon acknowledged
(3,5,36,'resolved','2026-02-19 05:42:00',10.68180000,122.96290000,'I am with Lorenzo now. He is fine — phone fell during the wind gust. Staying with him until the typhoon passes.','2026-02-19 05:33:00','2026-02-19 05:48:00'),
(4,5,37,'acknowledged','2026-02-19 05:35:00',NULL,NULL,'Elena is handling it. I am monitoring at my own location. Will go over if needed.','2026-02-19 05:35:00','2026-02-19 05:35:00'),
(5,5,38,'acknowledged','2026-02-19 05:36:00',NULL,NULL,'Saw the alert. Mama is with Kuya. I am okay here.','2026-02-19 05:36:00','2026-02-19 05:36:00'),

-- Alert 6 (Lorenzo panic click): Elena resolved
(6,6,36,'resolved','2026-02-19 04:52:00',10.68130000,122.96210000,'Called Lorenzo — he was just very scared of the wind and thunder. Reassured him. No physical danger.','2026-02-19 04:48:00','2026-02-19 05:00:00'),

-- Alert 7 (Rosa ACTIVE SOS): Benita on_the_way, Danilo acknowledged
(7,7,39,'on_the_way','2026-02-19 06:47:00',10.71950000,122.56150000,'I am heading to the market right now. About 10 minutes away. Bringing juice and glucose tablets.','2026-02-19 06:44:00','2026-02-19 06:47:00'),
(8,7,40,'acknowledged','2026-02-19 06:45:00',NULL,NULL,'Benita is on the way. I am calling the barangay health center to put them on standby just in case.','2026-02-19 06:45:00','2026-02-19 06:45:00'),

-- Alert 8 (Rosa medical — responded): Benita acknowledged
(9,8,39,'acknowledged','2026-02-19 06:01:00',NULL,NULL,'Texting Rosa to sit down and not rush. I will check on her when she gets home.','2026-02-19 06:00:00','2026-02-19 06:01:00'),

-- Alert 9 (Carlo fall detection): Patricia arrived, Miguel on_the_way
(10,9,41,'arrived','2026-02-19 07:13:00',10.31570000,123.89040000,'I reached Carlo. He has a small scrape on his left knee. I cleaned it and put a bandage. He is fine and went to school.','2026-02-19 07:08:00','2026-02-19 07:20:00'),
(11,9,42,'on_the_way','2026-02-19 07:09:00',10.31200000,123.88900000,'Driving to Carlo now. Patricia got there first. I will turn back once she confirms he is okay.','2026-02-19 07:09:00','2026-02-19 07:15:00'),

-- Alert 10 (Carlo SOS earthquake): Patricia arrived, Miguel arrived
(12,10,41,'arrived','2026-02-19 06:32:00',10.31620000,123.89110000,'Arrived at the house. Carlo is scared but not injured. We are checking each room for damage. Minor cracks on walls only.','2026-02-19 06:22:00','2026-02-19 07:30:00'),
(13,10,42,'arrived','2026-02-19 06:38:00',10.31630000,123.89100000,'I am here with Patricia and Carlo. Moving outside to open area as precaution. Bringing his medication.','2026-02-19 06:38:00','2026-02-19 07:30:00'),

-- Alert 12 (Juan SOS typhoon): Maria arrived, Jose acknowledged, Ana on_the_way
(14,12,25,'arrived','2026-02-17 10:25:00',10.67780000,122.95020000,'Reached Juan. Taking him to the Sports Complex evacuation center now. He is calm and safe.','2026-02-17 10:05:00','2026-02-17 10:40:00'),
(15,12,26,'acknowledged','2026-02-17 10:07:00',NULL,NULL,'Maria is responding. I am staying home with the children. Will go if needed.','2026-02-17 10:07:00','2026-02-17 10:07:00'),
(16,12,27,'on_the_way','2026-02-17 10:10:00',NULL,NULL,'Heading to Juan. Maria may need help. Bringing extra water and food pack.','2026-02-17 10:10:00','2026-02-17 10:30:00'),

-- Alert 13 (Rosa assistance, 2 days ago): Benita resolved
(17,13,39,'resolved','2026-02-17 14:25:00',NULL,NULL,'Brought Rosa her Metformin and Glimepiride refill. She took her afternoon dose. All good now.','2026-02-17 13:50:00','2026-02-17 14:30:00');

-- =============================================================================
-- DATA: contact_inquiries
-- =============================================================================
INSERT INTO `contact_inquiries` (`id`,`name`,`email`,`subject`,`message`,`category`,`priority`,`status`,`is_read`,`replied_by`,`reply_message`,`replied_at`,`created_at`,`updated_at`) VALUES

-- ─── ORIGINAL (untouched) ────────────────────────────────────────────────────
(1,'Maria Santos','maria.santos@gmail.com','Unable to Send SOS Alert','I need to send an emergency alert but the app is not responding. Please help.','emergency','urgent','pending',0,NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08'),
(2,'Jerome Buntaliada','jerome.buntaliada@gmail.com','App Keeps Crashing on Shake','The app keeps crashing whenever I enable the shake detection feature.','technical','high','pending',0,NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08'),
(3,'Jose Santos','jose.santos@gmail.com','Amazing App, Saved My Life!','I want to thank you for creating this app. It has been a lifesaver for me.','feedback','normal','pending',0,NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08'),
(4,'Ana Santos','ana.santos@gmail.com','How to Add My Family Members?','I am trying to add my family members to the family dashboard but I cannot find the option.','support','normal','pending',0,NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08'),
(5,NULL,'luis.cruz@gmail.com','Request for FSL Video Tutorials','Could you add more Filipino Sign Language tutorials to the app?','general','normal','pending',0,NULL,NULL,NULL,'2026-02-18 14:07:08','2026-02-18 14:07:08'),

-- ─── DEMO INQUIRIES ──────────────────────────────────────────────────────────
(6,'Elena Villanueva','elena@example.com','GPS Location Not Updating','My son Lorenzo has been home for over an hour but his map pin is still showing the old location near the market. How do I refresh it?','technical','high','replied',1,11,'Hi Elena! GPS pins refresh every 60 seconds when the app is active and Lorenzo has mobile data. Please ask him to open the app and tap the Refresh button on his check-in page. If the issue persists, try restarting the app. Let us know!','2026-02-15 10:30:00','2026-02-14 09:15:00','2026-02-15 10:30:00'),
(7,'Benita Magbanua','benita@example.com','The App Saved Rosa During Her Diabetic Episode!','I just want to thank you from the bottom of my heart. Last week Rosa had a hypoglycemic episode at the market and the SOS alert reached me immediately. I got there in time with glucose tablets. Silent Signal saved her life. God bless your team!','feedback','normal','resolved',1,11,'Dear Benita, thank you so much for sharing this. Stories like yours are exactly why we built Silent Signal. We are overjoyed Rosa is safe. Please take care of each other — and do not hesitate to reach out anytime!','2026-02-16 14:00:00','2026-02-16 11:45:00','2026-02-16 14:00:00'),
(8,'Patricia Reyes','patricia@example.com','Fall Detection False Alarm When Carlo Dances','The fall detection triggered an alert while Carlo was just dancing in the living room. It worried us unnecessarily. Can sensitivity be reduced for active movements?','support','normal','in_review',1,NULL,NULL,NULL,'2026-02-17 16:20:00','2026-02-17 16:20:00'),
(9,'Ramon Villanueva','ramon@example.com','How to Add Lorenzo\'s Grandmother as a Watcher?','Our mother, Lorenzo\'s grandmother, wants to receive alerts for him too. She has a phone but is not tech-savvy. How do we set this up for her?','support','normal','replied',1,11,'Hello Ramon! Ask Lorenzo to go to his Medical Profile and add his grandmother as an emergency contact with her registered phone number. Once she signs up with that number, she will be automatically linked as a family watcher and appear on her family dashboard. We also have an FSL setup guide you can share with her. Let us know if you need help walking her through it!','2026-02-13 15:45:00','2026-02-13 13:00:00','2026-02-13 15:45:00'),
(10,'Carlo Reyes','carlo@example.com','FSL Evacuation PDF Was Very Helpful','I downloaded the FSL Evacuation Instructions PDF and it is really clear and easy to understand. My whole family printed it and put it on the refrigerator. Thank you for making this available!','feedback','low','resolved',1,11,'Thank you Carlo! We are so glad the FSL resources are helping. Stay safe and always keep that checklist handy. Feel free to suggest any additions!','2026-02-12 09:30:00','2026-02-12 08:00:00','2026-02-12 09:30:00'),
(11,'Rosa Magbanua','rosa@example.com','Battery Level Always Shows 100% Even When Low','My battery percentage has been stuck at 100% on the family dashboard all day. My real battery is at 43%. My family uses this to know if I need to charge — can this be fixed?','technical','high','pending',0,NULL,NULL,NULL,'2026-02-19 05:10:00','2026-02-19 05:10:00'),
(12,'Danilo Magbanua','danilo@example.com','Suggestion: Add Voice-to-Text in Messages','It would be very helpful if there was a voice-to-text feature for messages. Rosa can read but cannot hear, so if we could speak and have it typed, that would make communication so much easier during emergencies.','general','low','pending',0,NULL,NULL,NULL,'2026-02-18 20:00:00','2026-02-18 20:00:00'),
(13,'Sofia Villanueva','sofia@example.com','Alert Notification Sound Too Quiet','When Lorenzo triggers an SOS, the notification on my phone is almost silent. I nearly missed one last night during the storm. Is there a way to force maximum volume for emergency alerts?','support','high','pending',0,NULL,NULL,NULL,'2026-02-19 01:30:00','2026-02-19 01:30:00'),
(14,'Miguel Reyes','miguel@example.com','Aftershock — App Did Not Trigger Shake Alert','There was a strong aftershock this morning around 7 AM and Carlo''s phone did not send a shake alert even though things fell off shelves. Is there a minimum threshold? How sensitive is it?','technical','high','in_review',1,NULL,NULL,NULL,'2026-02-19 07:45:00','2026-02-19 07:45:00');

-- =============================================================================
-- DATA: sms_events (Communication Hub quick-message logs)
-- =============================================================================
INSERT INTO `sms_events` (`id`,`user_id`,`messages`,`contacts`,`latitude`,`longitude`,`created_at`) VALUES
(1,33,'[\"I am safe\",\"I am at home\",\"Do not worry\"]','[{\"name\":\"Elena Villanueva\",\"phone\":\"09204567890\"},{\"name\":\"Ramon Villanueva\",\"phone\":\"09215678901\"},{\"name\":\"Sofia Villanueva\",\"phone\":\"09226789012\"}]',10.68200000,122.96400000,'2026-02-19 05:52:00'),
(2,35,'[\"I am safe\",\"Earthquake stopped\",\"I am going outside\"]','[{\"name\":\"Patricia Reyes\",\"phone\":\"09259012345\"},{\"name\":\"Miguel Reyes\",\"phone\":\"09260123456\"}]',10.31600000,123.89100000,'2026-02-19 06:20:00'),
(3,24,'[\"I am safe\",\"I need assistance\",\"Please come to my location\"]','[{\"name\":\"Maria Santos\",\"phone\":\"+639123456789\"},{\"name\":\"Jose Santos\",\"phone\":\"+639234567890\"}]',10.67800000,122.95060000,'2026-02-17 09:53:00'),
(4,9,'[\"I am safe\",\"I am evacuating\",\"Will contact when I arrive\"]','[{\"name\":\"Wynn de la Cruz\",\"phone\":\"09162360648\"}]',10.90600000,123.02700000,'2026-02-18 14:20:00'),
(5,34,'[\"I am sitting down\",\"I feel dizzy\",\"Please come\"]','[{\"name\":\"Benita Magbanua\",\"phone\":\"09237890123\"}]',10.72350000,122.56520000,'2026-02-19 06:43:00');

-- =============================================================================
-- DATA: family_broadcasts
-- =============================================================================
INSERT INTO `family_broadcasts` (`id`,`sender_id`,`pwd_id`,`message`,`created_at`) VALUES
(1,36,33,'BROADCAST: Lorenzo triggered a shake alert during the typhoon. Elena is on the way. Ramon and Sofia please stand by and keep your phones on.','2026-02-19 05:31:00'),
(2,39,34,'BROADCAST: Rosa has triggered an SOS at the Iloilo public market. Benita is heading there. Danilo please contact the barangay health center as backup.','2026-02-19 06:44:00'),
(3,25,24,'BROADCAST: Juan triggered an SOS and needs help getting to the evacuation center. Maria is responding. Jose and Ana please stay available.','2026-02-17 10:00:00');

-- =============================================================================
-- DATA: checkin_media_logs
-- =============================================================================
INSERT INTO `checkin_media_logs` (`id`,`user_id`,`media_type`,`file_size`,`latitude`,`longitude`,`created_at`) VALUES
(1,33,'photo',231400,10.68200000,122.96400000,'2026-02-19 05:51:00'),
(2,35,'photo',198700,10.31600000,123.89100000,'2026-02-19 06:19:00'),
(3,24,'photo',312400,10.67800000,122.95060000,'2026-02-18 14:06:00'),
(4,9,'video',1048576,10.90600000,123.02700000,'2026-02-18 14:22:00'),
(5,34,'photo',176300,10.72350000,122.56520000,'2026-02-19 06:42:00');

-- =============================================================================
-- DATA: hub_media_logs
-- =============================================================================
INSERT INTO `hub_media_logs` (`id`,`user_id`,`media_type`,`file_size`,`latitude`,`longitude`,`created_at`) VALUES
(1,33,'photo',241800,10.68200000,122.96400000,'2026-02-19 05:53:00'),
(2,35,'video',2097152,10.31600000,123.89100000,'2026-02-19 06:21:00'),
(3,34,'photo',189200,10.72350000,122.56520000,'2026-02-19 06:44:00'),
(4,24,'photo',305100,10.67800000,122.95060000,'2026-02-17 09:54:00');

-- =============================================================================
-- AUTO_INCREMENT — set above highest used IDs
-- =============================================================================
ALTER TABLE `users`                      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
ALTER TABLE `contact_inquiries`          MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
ALTER TABLE `disaster_alerts`            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
ALTER TABLE `emergency_alerts`           MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
ALTER TABLE `family_emergency_responses` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
ALTER TABLE `family_pwd_relationships`   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
ALTER TABLE `medical_profiles`           MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
ALTER TABLE `pwd_emergency_contacts`     MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `pwd_status_updates`         MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
ALTER TABLE `sms_events`                 MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
ALTER TABLE `family_broadcasts`          MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
ALTER TABLE `checkin_media_logs`         MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
ALTER TABLE `hub_media_logs`             MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- =============================================================================
-- ACCOUNT QUICK REFERENCE
-- =============================================================================
-- ─── ORIGINAL ACCOUNTS ──────────────────────────────────────────────────────
-- rgdioma@gmail.com              Reghis Dioma         Admin    original password
-- admin@gmail.com                Admin Test           Admin    original password
-- admin@silentsignal.com         Admin User           Admin    password: password
-- family@gmail.com               Family Test          Family   original password
-- jeromebuntalidad@gmail.com     Jerome Buntalidad    Family   original password
-- aizhellegwynneth@gmail.com     Aizhelle de la Cruz  PWD      original password
-- user@gmail.com                 User Test            PWD      original password
-- marisolsamillano@gmail.com     Marisol Samillano    PWD      original password
-- juan@example.com               Juan Santos          PWD      password: password
-- maria@example.com              Maria Santos         Family   password: password
-- jose@example.com               Jose Santos          Family   password: password
-- ana@example.com                Ana Santos           Family   password: password
--
-- ─── DEMO SHOWCASE ACCOUNTS (all password: password) ────────────────────────
-- lorenzo@example.com    Lorenzo Villanueva  PWD    Bacolod City   Deaf/Mute + Hypertension
-- rosa@example.com       Rosa Magbanua       PWD    Iloilo City    Hard of Hearing + Diabetes  ← ACTIVE SOS
-- carlo@example.com      Carlo Reyes         PWD    Cebu City      Deaf/Mute + Hypothyroidism
-- elena@example.com      Elena Villanueva    Family Lorenzo's Mother (primary) — Resolved typhoon alert
-- ramon@example.com      Ramon Villanueva    Family Lorenzo's Father — Acknowledged
-- sofia@example.com      Sofia Villanueva    Family Lorenzo's Sister — Acknowledged
-- benita@example.com     Benita Magbanua     Family Rosa's Mother (primary) — ON THE WAY to Rosa
-- danilo@example.com     Danilo Magbanua     Family Rosa's Father — Acknowledged
-- patricia@example.com   Patricia Reyes      Family Carlo's Mother (primary) — Arrived, Carlo safe
-- miguel@example.com     Miguel Reyes        Family Carlo's Father — Arrived
--
-- ─── LIVE DEMO SCENARIOS ────────────────────────────────────────────────────
-- 🔴 ACTIVE: Rosa Magbanua (rosa@example.com) — SOS at Iloilo City market
--            Benita (benita@example.com) is ON THE WAY with glucose tablets
--            Login as benita@example.com to see: 1 active alert, Rosa needs_assistance
--
-- ✅ RESOLVED: Lorenzo Villanueva — shake alert during Typhoon Caloy, Elena arrived
--             Login as elena@example.com to see: typhoon disaster alert active, Lorenzo now safe
--
-- ✅ RESOLVED: Carlo Reyes — SOS during 5.9 earthquake, Patricia and Miguel arrived
--             Login as patricia@example.com to see: earthquake active, Carlo now safe
--
-- 🌪️ ACTIVE DISASTERS: Typhoon (Bacolod/Negros Occ), Earthquake (Cebu), Flood (Iloilo)
-- 📋 ADMIN PANEL: 14 contact inquiries across all categories (pending, in_review, replied, resolved)
-- =============================================================================
