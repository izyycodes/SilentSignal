-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 23, 2026 at 10:03 PM
-- Server version: 11.8.5-MariaDB-log
-- PHP Version: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rgdioma_silent_signal`
--

-- --------------------------------------------------------

--
-- Table structure for table `checkin_media_logs`
--

CREATE TABLE `checkin_media_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `media_type` enum('photo','video') NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_inquiries`
--

CREATE TABLE `contact_inquiries` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_inquiries`
--

INSERT INTO `contact_inquiries` (`id`, `name`, `email`, `subject`, `message`, `category`, `priority`, `status`, `is_read`, `replied_by`, `reply_message`, `replied_at`, `created_at`, `updated_at`) VALUES
(1, 'Maria Santos', 'maria.santos@gmail.com', 'Unable to Send SOS Alert', 'I need to send an emergency alert but the app is not responding. Please help.', 'emergency', 'urgent', 'pending', 0, NULL, NULL, NULL, '2026-02-18 14:07:08', '2026-03-19 03:35:45'),
(2, 'Jerome Buntaliada', 'jerome.buntaliada@gmail.com', 'App Keeps Crashing on Shake', 'The app keeps crashing whenever I enable the shake detection feature.', 'technical', 'high', 'pending', 0, NULL, NULL, NULL, '2026-02-18 14:07:08', '2026-03-19 03:35:36'),
(3, 'Jose Santos', 'jose.santos@gmail.com', 'Amazing App, Saved My Life!', 'I want to thank you for creating this app. It has been a lifesaver for me.', 'feedback', 'normal', 'pending', 0, NULL, NULL, NULL, '2026-02-18 14:07:08', '2026-02-18 14:07:08'),
(4, 'Ana Santos', 'ana.santos@gmail.com', 'How to Add My Family Members?', 'I am trying to add my family members to the family dashboard but I cannot find the option.', 'support', 'normal', 'pending', 0, NULL, NULL, NULL, '2026-02-18 14:07:08', '2026-02-18 14:07:08'),
(5, NULL, 'luis.cruz@gmail.com', 'Request for FSL Video Tutorials', 'Could you add more Filipino Sign Language tutorials to the app?', 'general', 'normal', 'pending', 0, NULL, NULL, NULL, '2026-02-18 14:07:08', '2026-02-18 14:07:08'),
(8, 'Aizhelle', 'aizhellegwynneth@gmail.com', 'How to update medical profile', 'I need help updating my medical information in the app. I cannot find where to change my emergency contact details.', 'general', 'normal', 'replied', 0, 38, 'ok', '2026-03-04 03:38:00', '2026-02-19 12:23:30', '2026-03-04 03:38:00'),
(9, NULL, 's2100406@usls.edu.ph', 'Good App', 'this is a good app!', 'feedback', 'low', 'replied', 0, 38, 'appreciated!', '2026-03-02 02:59:22', '2026-03-02 02:58:46', '2026-03-02 02:59:22');

-- --------------------------------------------------------

--
-- Table structure for table `disaster_alerts`
--

CREATE TABLE `disaster_alerts` (
  `id` int(11) NOT NULL,
  `alert_type` enum('flood','earthquake','typhoon','fire','tsunami') NOT NULL,
  `severity` enum('low','moderate','high','critical') NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `affected_areas` text DEFAULT NULL,
  `status` enum('active','resolved') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `disaster_alerts`
--

INSERT INTO `disaster_alerts` (`id`, `alert_type`, `severity`, `location`, `description`, `affected_areas`, `status`, `created_at`, `updated_at`) VALUES
(1, 'typhoon', 'high', 'Iloilo City', 'Typhoon approaching the area. Strong winds and heavy rainfall expected.', 'Iloilo City, Guimaras, Capiz', 'active', '2026-02-18 14:07:08', '2026-02-18 14:07:08');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_alerts`
--

CREATE TABLE `emergency_alerts` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `emergency_alerts`
--

INSERT INTO `emergency_alerts` (`id`, `user_id`, `alert_type`, `latitude`, `longitude`, `location_address`, `message`, `status`, `priority`, `resolved_by`, `resolved_at`, `notes`, `created_at`) VALUES
(4, 24, 'sos', 10.67800000, 122.95060000, NULL, 'Emergency SOS activated', 'resolved', 'high', NULL, NULL, NULL, '2026-02-17 09:57:24'),
(5, 19, 'sos', 10.67823560, 122.96167840, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 2/19/26, 7:24 AM\n\n???? LOCATION:\n, , \nMap: https://maps.google.com/?q=10.6782356,122.9616784\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-02-18 23:24:32'),
(6, 9, 'sos', 10.67886883, 122.96197698, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 2/19/26, 9:25 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.678868830027755,122.9619769769516\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: seafood\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-02-19 01:25:30'),
(7, 9, 'sos', 10.67915671, 122.96207681, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 2/19/26, 9:25 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.679156713899195,122.96207681201882\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: seafood\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-02-19 01:25:40'),
(8, 9, 'sos', 10.67899468, 122.96217730, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 2/19/26, 9:26 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.678994679209186,122.96217729691884\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: seafood\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-02-19 01:26:29'),
(9, 19, 'sos', 10.67888300, 122.96233260, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 2/19/26, 9:32 AM\n\n???? LOCATION:\n, , \nMap: https://maps.google.com/?q=10.678883,122.9623326\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-02-19 01:32:39'),
(10, 19, 'sos', 10.67825940, 122.96239130, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 2/19/26, 9:33 AM\n\n???? LOCATION:\n, , \nMap: https://maps.google.com/?q=10.6782594,122.9623913\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-02-19 01:33:20'),
(11, 19, 'sos', 10.67895560, 122.96229720, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 2/19/26, 9:34 AM\n\n???? LOCATION:\n, , \nMap: https://maps.google.com/?q=10.6789556,122.9622972\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-02-19 01:34:25'),
(12, 19, 'sos', 10.67825500, 122.96244720, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 2/19/26, 9:35 AM\n\n???? LOCATION:\n, , \nMap: https://maps.google.com/?q=10.678255,122.9624472\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-02-19 01:35:27'),
(13, 19, 'sos', 10.67827100, 122.96245720, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 2/19/26, 9:48 AM\n\n???? LOCATION:\n, , \nMap: https://maps.google.com/?q=10.678271,122.9624572\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-02-19 01:48:13'),
(14, 9, 'sos', 10.67900683, 122.96226583, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 2/19/26, 10:07 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.679006833652254,122.96226583271546\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: seafood\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-02-19 02:08:12'),
(15, 9, 'sos', 10.67895471, 122.96225949, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 2/19/26, 10:10 AM\n\n???? LOCATION:\nMuseo de La Salle, La Salle Avenue, Villamonte, Bacolod-1, Bacolod, Negros Island Region, 6100, Philippines\nMap: https://maps.google.com/?q=10.678954714851889,122.9622594898854\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: seafood\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-02-19 02:10:08'),
(16, 9, 'sos', 10.67899795, 122.96223793, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 2/19/26, 10:10 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.678997949165526,122.96223793070973\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: seafood\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-02-19 02:11:08'),
(17, 19, 'sos', 10.67775150, 122.96206150, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 2/19/26, 11:18 AM\n\n???? LOCATION:\nUniversity of St. La Salle, Atis Street, La Salleville, Villamonte, Bacolod-1, Bacolod, Negros Island Region, 6100, Philippines\nMap: https://maps.google.com/?q=10.6777515,122.9620615\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\nConditions: anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-02-19 03:18:45'),
(18, 19, 'sos', 10.69000000, 122.57000000, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/3/26, 7:55 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.69,122.57\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\nConditions: anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-03 11:55:48'),
(19, 19, 'sos', 10.69000000, 122.57000000, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/3/26, 7:56 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.69,122.57\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\nConditions: anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-03 11:56:07'),
(20, 9, 'sos', 10.72608643, 122.98269281, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/3/26, 8:10 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.726086434748849,122.9826928119366\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: antibiotics\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-03 12:10:05'),
(21, 9, 'sos', NULL, NULL, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 3:43 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: Location unavailable\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: antibiotics\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'resolved', 'high', 38, '2026-03-03 21:46:52', NULL, '2026-03-03 19:43:42'),
(22, 9, 'sos', 10.67857348, 122.96230455, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 7:03 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.678573484601031,122.96230454539253\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: antibiotics\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-03 23:03:35'),
(23, 39, 'sos', 10.67847738, 122.96226672, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Felix Lee\nPWD ID: PWD-689-256\nPhone: 09856472161\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 7:17 AM\n\n???? LOCATION:\n, , \nMap: https://maps.google.com/?q=10.678477376055948,122.96226672100651\n\n???? MEDICAL INFO:\nBlood Type: A+\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-03 23:17:29'),
(24, 9, 'sos', 10.67862073, 122.96224413, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 8:12 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.678620733757542,122.96224413479051\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 00:13:14'),
(25, 9, 'sos', 10.67858087, 122.96228196, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 8:13 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6785808704151,122.96228196210438\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 00:13:22'),
(26, 19, 'sos', 10.67847820, 122.96223410, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 8:21 AM\n\n???? LOCATION:\nMuseo de La Salle, La Salle Avenue, Villamonte, Bacolod-1, Bacolod, Negros Island Region, 6100, Philippines\nMap: https://maps.google.com/?q=10.6784782,122.9622341\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\nConditions: anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 00:21:56'),
(27, 9, 'sos', 10.67843840, 122.96224860, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 9:46 AM\n\n???? LOCATION:\nMuseo de La Salle, La Salle Avenue, Villamonte, Bacolod-1, Bacolod, Negros Island Region, 6100, Philippines\nMap: https://maps.google.com/?q=10.6784384,122.9622486\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 01:46:41'),
(28, 9, 'sos', 10.67850120, 122.96224450, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 9:57 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6785012,122.9622445\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 01:57:54'),
(29, 9, 'sos', 10.67847210, 122.96226670, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 10:07 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784721,122.9622667\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 02:07:34'),
(30, 9, 'sos', 10.67847210, 122.96226670, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 10:07 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784721,122.9622667\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 02:07:39'),
(31, 9, 'sos', 10.67851720, 122.96225680, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 10:10 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6785172,122.9622568\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 02:10:47'),
(32, 9, 'sos', 10.67851270, 122.96226140, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 10:18 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6785127,122.9622614\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 02:18:31'),
(33, 9, 'sos', 10.67845120, 122.96226200, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName:  \nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 10:26 AM\n\n???? LOCATION:\nMuseo de La Salle, La Salle Avenue, Villamonte, Bacolod-1, Bacolod, Negros Island Region, 6100, Philippines\nMap: https://maps.google.com/?q=10.6784512,122.962262\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 02:26:46'),
(34, 9, 'sos', 10.67845510, 122.96224630, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName:  \nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 10:34 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784551,122.9622463\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 02:34:03'),
(35, 9, 'sos', 10.67872590, 122.96221270, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName:  \nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 10:38 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6787259,122.9622127\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 02:38:51'),
(36, 9, 'sos', 10.67845960, 122.96224910, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName:  \nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 10:56 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784596,122.9622491\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 02:56:34'),
(37, 9, 'sos', 10.67866620, 122.96224820, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 11:06 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6786662,122.9622482\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 03:06:29'),
(38, 9, 'sos', 10.67840860, 122.96228270, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 11:07 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784086,122.9622827\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 03:07:19'),
(39, 9, 'sos', 10.67846290, 122.96225420, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 11:29 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784629,122.9622542\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 03:30:03'),
(40, 9, 'sos', 10.67846290, 122.96225420, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 11:29 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784629,122.9622542\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 03:30:07'),
(41, 9, 'sos', 10.67846690, 122.96225920, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 11:30 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784669,122.9622592\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 03:30:17'),
(42, 9, 'sos', 10.67849020, 122.96222880, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 12:51 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784902,122.9622288\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 04:51:45'),
(43, 9, 'sos', 10.67841260, 122.96224580, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhellee de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 1:05 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784126,122.9622458\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 05:05:19'),
(44, 9, 'sos', 10.67841790, 122.96223210, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhellee de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 1:08 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784179,122.9622321\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 05:08:13'),
(45, 9, 'sos', 10.67847030, 122.96222120, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhellee de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 1:09 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784703,122.9622212\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 05:10:52'),
(46, 9, 'sos', 10.67845220, 122.96225700, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhellee de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 1:23 PM\n\n???? LOCATION:\nMuseo de La Salle, La Salle Avenue, Villamonte, Bacolod-1, Bacolod, Negros Island Region, 6100, Philippines\nMap: https://maps.google.com/?q=10.6784522,122.962257\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 05:23:37'),
(47, 9, 'sos', 10.67840070, 122.96223720, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhellee de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 1:24 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784007,122.9622372\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 05:24:18'),
(48, 9, 'sos', 10.67845820, 122.96226010, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhellee de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 1:50 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784582,122.9622601\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 05:50:14'),
(49, 9, 'sos', 10.67847150, 122.96226590, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhellee de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 1:50 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784715,122.9622659\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 05:50:53'),
(50, 9, 'sos', 10.67844230, 122.96221860, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhellee de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 1:51 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784423,122.9622186\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 05:51:06'),
(51, 9, 'sos', 10.67844260, 122.96229920, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhellee de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 1:52 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784426,122.9622992\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 05:52:04'),
(52, 9, 'sos', 10.67842480, 122.96231010, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhellee de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 1:52 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784248,122.9623101\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 05:52:20'),
(53, 9, 'sos', 10.67845860, 122.96223840, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhellee de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 2:22 PM\n\n???? LOCATION:\nMuseo de La Salle, La Salle Avenue, Villamonte, Bacolod-1, Bacolod, Negros Island Region, 6100, Philippines\nMap: https://maps.google.com/?q=10.6784586,122.9622384\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 06:22:24'),
(54, 9, 'sos', 10.67846080, 122.96223760, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhellee de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/4/26, 2:29 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.6784608,122.9622376\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-04 06:29:37'),
(55, 9, 'sos', 10.72606469, 122.98275610, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhellee de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/17/26, 8:51 PM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.726064686106875,122.98275609779382\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-17 12:51:48'),
(56, 19, 'sos', 10.73000000, 124.01000000, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 12:19 AM\n\n???? LOCATION:\nSan Jose Poblacion, Bawo, Cebu, Central Visayas, 6006, Philippines\nMap: https://maps.google.com/?q=10.73,124.01\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\nConditions: anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 16:19:33'),
(57, 9, 'sos', 10.77546400, 122.97273480, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 12:20 AM\n\n???? LOCATION:\nBuena Vista 3 Subdivision, Guinhalaran, Silay, Negros Occidental, Negros Island Region, 6116, Philippines\nMap: https://maps.google.com/?q=10.775464,122.9727348\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 16:20:31'),
(58, 19, 'sos', 10.73000000, 124.01000000, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 12:32 AM\n\n???? LOCATION:\nSan Jose Poblacion, Bawo, Cebu, Central Visayas, 6006, Philippines\nMap: https://maps.google.com/?q=10.73,124.01\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\nConditions: anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 16:32:18'),
(59, 9, 'sos', NULL, NULL, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 12:32 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: Location unavailable\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 16:32:39'),
(60, 9, 'sos', NULL, NULL, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 12:33 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: Location unavailable\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 16:33:20'),
(61, 19, 'sos', 10.73000000, 124.01000000, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 12:45 AM\n\n???? LOCATION:\nSan Jose Poblacion, Bawo, Cebu, Central Visayas, 6006, Philippines\nMap: https://maps.google.com/?q=10.73,124.01\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\nConditions: anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 16:45:34'),
(62, 9, 'sos', 10.77543690, 122.97272720, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 12:53 AM\n\n???? LOCATION:\nBuena Vista 3 Subdivision, Guinhalaran, Silay, Negros Occidental, Negros Island Region, 6116, Philippines\nMap: https://maps.google.com/?q=10.7754369,122.9727272\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 16:53:23'),
(63, 19, 'sos', 10.73000000, 124.01000000, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 12:54 AM\n\n???? LOCATION:\nSan Jose Poblacion, Bawo, Cebu, Central Visayas, 6006, Philippines\nMap: https://maps.google.com/?q=10.73,124.01\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\nConditions: anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 16:54:19'),
(64, 19, 'sos', 10.73000000, 124.01000000, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 1:00 AM\n\n???? LOCATION:\nSan Jose Poblacion, Bawo, Cebu, Central Visayas, 6006, Philippines\nMap: https://maps.google.com/?q=10.73,124.01\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\nConditions: anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 17:00:04'),
(65, 9, 'sos', 10.77544850, 122.97273300, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 1:01 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.7754485,122.972733\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 17:01:58'),
(66, 9, 'sos', 10.77497420, 122.97175870, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 1:03 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.7749742,122.9717587\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 17:03:06'),
(67, 19, 'sos', 10.73000000, 124.01000000, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 1:05 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.73,124.01\n\n???? MEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\nConditions: anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 17:05:10'),
(68, 9, 'sos', 10.77545510, 122.97272930, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 1:05 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.7754551,122.9727293\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 17:05:46'),
(69, 9, 'sos', 10.77544780, 122.97273120, NULL, '???? EMERGENCY ALERT ????\n⚠️ DEAF/MUTE - TEXT ONLY - NO CALLS ⚠️\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 1:12 AM\n\n???? LOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.7754478,122.9727312\n\n???? MEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\n⚠️ Please respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 17:12:45'),
(70, 9, 'sos', 10.77545210, 122.97273950, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 1:13 AM\n\nLOCATION:\nMap: https://maps.google.com/?q=10.7754521,122.9727395\n\nMEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 17:13:17'),
(71, 9, 'sos', 10.77536970, 122.97269500, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 1:13 AM\n\nLOCATION:\nMap: https://maps.google.com/?q=10.7753697,122.972695\n\nMEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 17:13:53'),
(72, 19, 'sos', 10.73000000, 124.01000000, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: User Test\nPWD ID: PWD-080-123\nPhone: 09875412658\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 1:13 AM\n\nLOCATION:\nMap: https://maps.google.com/?q=10.73,124.01\n\nMEDICAL INFO:\nBlood Type: A+\nAllergies: Seafood\nMedications: Antibiotics\nConditions: anxiety\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 17:13:53'),
(73, 9, 'sos', 10.77544560, 122.97273330, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 1:18 AM\n\nLOCATION:\nMap: https://maps.google.com/?q=10.7754456,122.9727333\n\nMEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 17:18:04'),
(74, 9, 'sos', 10.77545350, 122.97272980, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 1:23 AM\n\nLOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.7754535,122.9727298\n\nMEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 17:23:49'),
(75, 9, 'sos', 10.72607473, 122.98277768, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 6:41 AM\n\nLOCATION:\nZone 15, Talisay, Negros Occidental, Negros Island Region, 6115, Philippines\nMap: https://maps.google.com/?q=10.726074727653504,122.982777676922\n\nMEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 22:41:14'),
(76, 9, 'sos', 10.72607472, 122.98277748, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 6:42 AM\n\nLOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.72607472249672,122.98277748103088\n\nMEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-18 22:42:51'),
(77, 9, 'sos', 10.67896823, 122.96216510, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 9:46 AM\n\nLOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.678968232015631,122.96216510400558\n\nMEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-19 01:46:51'),
(78, 9, 'sos', 10.67896823, 122.96216510, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 9:47 AM\n\nLOCATION:\nMuseo de La Salle, La Salle Avenue, Villamonte, Bacolod-1, Bacolod, Negros Island Region, 6100, Philippines\nMap: https://maps.google.com/?q=10.678968232684264,122.96216510326823\n\nMEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-19 01:47:11'),
(79, 9, 'sos', 10.67896004, 122.96219167, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 9:48 AM\n\nLOCATION:\nMuseo de La Salle, La Salle Avenue, Villamonte, Bacolod-1, Bacolod, Negros Island Region, 6100, Philippines\nMap: https://maps.google.com/?q=10.6789600430714,122.96219167349977\n\nMEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-19 01:48:15'),
(80, 9, 'sos', 10.67896824, 122.96216510, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 9:51 AM\n\nLOCATION:\nMuseo de La Salle, La Salle Avenue, Villamonte, Bacolod-1, Bacolod, Negros Island Region, 6100, Philippines\nMap: https://maps.google.com/?q=10.678968235559894,122.96216510009758\n\nMEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-19 01:52:26'),
(81, 59, 'sos', NULL, NULL, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Dairin Janagap\nPhone: 09152411363\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 9:56 AM\n\nLOCATION:\n, , \nMap: Location unavailable\n\nMEDICAL INFO:\nBlood Type: A+\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-19 01:56:41'),
(82, 59, 'sos', 10.67900334, 122.96223009, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Dairin Janagap\nPhone: 09152411363\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 10:01 AM\n\nLOCATION:\n, , \nMap: https://maps.google.com/?q=10.67900334205256,122.9622300868485\n\nMEDICAL INFO:\nBlood Type: A+\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-19 02:01:50'),
(83, 59, 'sos', NULL, NULL, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Dairin Janagap\nPhone: 09152411363\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 11:39 AM\n\nLOCATION:\n, , \nMap: Location unavailable\n\nMEDICAL INFO:\nBlood Type: A+\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-19 03:31:49'),
(84, 61, 'sos', NULL, NULL, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: User Secret\nPhone: 09704943152\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/19/26, 11:52 AM\n\nLOCATION:\nsecret, secret, secret\nMap: Location unavailable\n\nMEDICAL INFO:\nBlood Type: A+\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-19 03:45:39'),
(85, 19, 'sos', 10.77542990, 122.97273080, NULL, 'EMERGENCY SOS\nDEAF/MUTE - TEXT ONLY\n\nFrom: User Test (PWD: PWD-080-123)\nLocation: maps.google.com/?q=10.7754,122.9727\nBlood: A+\nAllergies: Seafood\nMeds: Antibiotics\nReply via TEXT only.', 'active', 'high', NULL, NULL, NULL, '2026-03-22 09:11:11'),
(86, 9, 'sos', 10.72610620, 122.98273710, NULL, 'EMERGENCY SOS\nDEAF/MUTE - TEXT ONLY\n\nFrom: Aizhelle de la Cruz (PWD: PWD-123-456)\nLocation: maps.google.com/?q=10.7261,122.9827\nBlood: O+\nAllergies: peanuts\nMeds: biogesic\nReply via TEXT only.', 'active', 'high', NULL, NULL, NULL, '2026-03-22 11:15:20'),
(87, 9, 'sos', 10.72610540, 122.98273700, NULL, 'EMERGENCY SOS\nDEAF/MUTE - TEXT ONLY\n\nFrom: Aizhelle de la Cruz (PWD: PWD-123-456)\nLocation: maps.google.com/?q=10.7261,122.9827\nBlood: O+\nAllergies: peanuts\nMeds: biogesic\nReply via TEXT only.', 'active', 'high', NULL, NULL, NULL, '2026-03-22 11:15:51'),
(88, 63, 'sos', 10.72610780, 122.98273770, NULL, 'EMERGENCY SOS\nDEAF/MUTE - TEXT ONLY\n\nFrom: Aziealle Cruz (PWD: 00-11-22-334-4556677)\nLocation: maps.google.com/?q=10.7261,122.9827\nReply via TEXT only.', 'active', 'high', NULL, NULL, NULL, '2026-03-22 11:19:58'),
(89, 63, 'sos', 10.72610780, 122.98273770, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Aziealle Cruz\nPWD ID: 00-11-22-334-4556677\nPhone: 09099372368\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/22/26, 7:13 PM\n\nLOCATION:\nZone 15, Talisay, Negros Occidental, Negros Island Region, 6115, Philippines\nMap: https://maps.google.com/?q=10.7261078,122.9827377\n\n\nPlease respond via TEXT MESSAGE only.', 'active', 'high', NULL, NULL, NULL, '2026-03-22 11:21:10'),
(90, 9, 'sos', 10.72607285, 122.98278923, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nName: Aizhelle de la Cruz\nPWD ID: PWD-123-456\nPhone: 09949771317\nStatus: NEEDS IMMEDIATE HELP\nTime: 3/23/26, 6:14 PM\n\nLOCATION:\nMontinola Street, Victorias City, Negros Occidental\nMap: https://maps.google.com/?q=10.726072852934994,122.98278923404526\n\nMEDICAL INFO:\nBlood Type: O+\nAllergies: peanuts\nMedications: biogesic\nConditions: social anxiety\n\nThis person is DEAF/MUTE - Please respond via TEXT only.', 'active', 'high', NULL, NULL, NULL, '2026-03-23 10:14:44'),
(91, 63, 'sos', 10.72607291, 122.98278903, NULL, 'EMERGENCY ALERT\nDEAF/MUTE - TEXT ONLY - NO CALLS\n\nFrom: Aziealle Cruz (PWD: 00-11-22-334-4556677)\n\nLocation: maps.google.com/?q=10.7261,122.9828\n\nThis person is DEAF/MUTE - Please respond via TEXT only.', 'active', 'high', NULL, NULL, NULL, '2026-03-23 10:15:32');

-- --------------------------------------------------------

--
-- Table structure for table `family_broadcasts`
--

CREATE TABLE `family_broadcasts` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `pwd_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `family_broadcasts`
--

INSERT INTO `family_broadcasts` (`id`, `sender_id`, `pwd_id`, `message`, `created_at`) VALUES
(2, 33, 9, 'Emergency alert from a family member. Please check in immediately.', '2026-03-03 14:20:56');

-- --------------------------------------------------------

--
-- Table structure for table `family_emergency_responses`
--

CREATE TABLE `family_emergency_responses` (
  `id` int(11) NOT NULL,
  `alert_id` int(11) NOT NULL,
  `family_member_id` int(11) NOT NULL,
  `response_status` enum('notified','acknowledged','on_the_way','arrived','resolved') DEFAULT 'notified',
  `response_time` timestamp NULL DEFAULT NULL,
  `location_lat` decimal(10,8) DEFAULT NULL,
  `location_lng` decimal(11,8) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `family_emergency_responses`
--

INSERT INTO `family_emergency_responses` (`id`, `alert_id`, `family_member_id`, `response_status`, `response_time`, `location_lat`, `location_lng`, `notes`, `created_at`, `updated_at`) VALUES
(1, 4, 25, 'arrived', '2026-02-18 12:17:08', NULL, NULL, NULL, '2026-02-18 14:07:08', '2026-02-18 14:07:08'),
(2, 4, 26, 'acknowledged', '2026-02-18 12:12:08', NULL, NULL, NULL, '2026-02-18 14:07:08', '2026-02-18 14:07:08'),
(3, 8, 33, 'on_the_way', '2026-02-19 02:45:38', NULL, NULL, NULL, '2026-02-19 02:03:44', '2026-02-19 02:45:38'),
(4, 7, 33, 'on_the_way', '2026-02-19 02:03:49', 10.67902618, 122.96219483, NULL, '2026-02-19 02:03:49', '2026-02-19 02:03:49'),
(5, 22, 33, 'on_the_way', '2026-03-03 23:27:05', 10.67869064, 122.96222177, NULL, '2026-03-03 23:03:47', '2026-03-03 23:27:05');

-- --------------------------------------------------------

--
-- Table structure for table `family_pwd_relationships`
--

CREATE TABLE `family_pwd_relationships` (
  `id` int(11) NOT NULL,
  `family_member_id` int(11) NOT NULL,
  `pwd_user_id` int(11) NOT NULL,
  `relationship_type` varchar(50) NOT NULL,
  `is_primary_contact` tinyint(1) DEFAULT 0,
  `notification_enabled` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `family_pwd_relationships`
--

INSERT INTO `family_pwd_relationships` (`id`, `family_member_id`, `pwd_user_id`, `relationship_type`, `is_primary_contact`, `notification_enabled`, `created_at`) VALUES
(221, 56, 57, 'Sibling', 1, 1, '2026-03-04 03:23:47'),
(284, 59, 59, 'Uncle', 1, 1, '2026-03-19 03:31:38'),
(287, 61, 62, 'papa', 1, 1, '2026-03-19 04:50:32'),
(359, 9, 63, 'Friend', 1, 1, '2026-03-23 10:17:22'),
(366, 17, 19, 'Father', 1, 1, '2026-03-23 11:01:40');

-- --------------------------------------------------------

--
-- Table structure for table `hub_media_logs`
--

CREATE TABLE `hub_media_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `media_type` enum('photo','video') NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hub_media_logs`
--

INSERT INTO `hub_media_logs` (`id`, `user_id`, `media_type`, `file_size`, `latitude`, `longitude`, `created_at`) VALUES
(1, 9, 'video', NULL, 10.67842160, 122.96227580, '2026-03-04 02:28:51'),
(2, 9, 'photo', NULL, 10.67845660, 122.96226360, '2026-03-04 02:46:38');

-- --------------------------------------------------------

--
-- Table structure for table `medical_profiles`
--

CREATE TABLE `medical_profiles` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medical_profiles`
--

INSERT INTO `medical_profiles` (`id`, `user_id`, `first_name`, `last_name`, `date_of_birth`, `gender`, `pwd_id`, `phone`, `email`, `street_address`, `city`, `province`, `zip_code`, `disability_type`, `blood_type`, `allergies`, `medications`, `medical_conditions`, `emergency_contacts`, `sms_template`, `medication_reminders`, `created_at`, `updated_at`) VALUES
(2, 9, 'Aizhelle', 'de la Cruz', '2005-08-23', 'Female', 'PWD-123-456', '09949771317', 'aizhellegwynneth@gmail.com', 'Montinola Street', 'Victorias City', 'Negros Occidental', '6120', 'Deaf/Mute', 'O+', '[\"peanuts\"]', '[\"biogesic\"]', '[\"social anxiety\"]', '[{\"name\":\"Maria Fe Geronimo\",\"relation\":\"Guardian\",\"phone\":\"09071712614\",\"initials\":\"MF\",\"color\":\"rgb(229, 57, 53)\"}]', '???? EMERGENCY ALERT ????\n                            ⚠ USER IS DEAF/MUTE - TEXT ONLY - NO CALLS ⚠\n                            \n                            Name: Aizhelle de la Cruz\n                            PWD ID: PWD-123-456\n                            Phone: 09949771317\n                            Address: Montinola Street, Victorias City\n                            Status: Emergency SOS Activated\n                            Location: GPS coordinates will be included\n                            Time: [Timestamp]\n                            \n                            Medical Info:\n                            • Blood Type: O+\n                            • Allergies: peanuts\n                            • Medications: biogesic', '[{\"name\":\"Biogesic\",\"frequency\":\"Once daily\",\"time\":\"8:00 AM, 8:00 PM\",\"color\":\"rgb(76, 175, 80)\"}]', '2026-02-08 11:57:52', '2026-03-23 10:13:24'),
(8, 19, 'User', 'Test', '2005-08-01', 'Female', 'PWD-080-123', '09875412658', 'user@gmail.com', 'Montinola Street', 'Victorias City', 'Negros Occidental', '6119', 'Deaf/Mute', 'A+', '[\"Seafood\"]', '[\"Antibiotics\"]', '[\"anxiety\"]', '[{\"name\":\"Family Test\",\"relation\":\"Father\",\"phone\":\"09998546215\",\"initials\":\"FT\",\"color\":\"rgb(67, 160, 71)\"},{\"name\":\"Family Test 3\",\"relation\":\"Aunt\",\"phone\":\"09671789112\",\"initials\":\"FT\",\"color\":\"rgb(142, 36, 170)\"},{\"name\":\"Family Test 4\",\"relation\":\"Friend\",\"phone\":\"09312424151\",\"initials\":\"FT\",\"color\":\"rgb(255, 152, 0)\"},{\"name\":\"Family Test 5\",\"relation\":\"Friend\",\"phone\":\"09994105502\",\"initials\":\"FT\",\"color\":\"rgb(67, 160, 71)\"}]', '???? EMERGENCY ALERT ????\n                            ⚠ USER IS DEAF/MUTE - TEXT ONLY - NO CALLS ⚠\n                            \n                            Name: User Test\n                            PWD ID: PWD-080-123\n                            Phone: 09875412658\n                            Address: Montinola Street, Victorias City\n                            Status: Emergency SOS Activated\n                            Location: GPS coordinates will be included\n                            Time: [Timestamp]\n                            \n                            Medical Info:\n                            • Blood Type: A+\n                            • Allergies: Seafood\n                            • Medications: Antibiotics', '[{\"name\":\"Metformin 500mg\",\"frequency\":\"Twice daily\",\"time\":\"7:15 AM, 8:00 PM\",\"color\":\"rgb(156, 39, 176)\"}]', '2026-02-12 02:43:42', '2026-03-23 11:01:37'),
(10, 31, 'Marisol', 'Samillano', NULL, 'Male', 'PWD-987-654', '09558697412', 'marisolsamillano@gmail.com', '', 'Isabela', 'Negros Occidental', '6128', 'Deaf/Mute', 'AB+', '[\"peanuts\"]', '[]', '[\"social anxiety\"]', '[]', 'EMERGENCY ALERT - USER IS DEAF/MUTE - TEXT ONLY', '[]', '2026-02-18 14:10:50', '2026-02-18 14:12:07'),
(11, 24, 'Juan', 'Santos', NULL, 'Male', 'PWD-225-362', '+639111111111', 'juan@example.com', '', '', '', '', 'Deaf/Mute', 'A+', '[]', '[]', '[]', '[{\"name\":\"Family\",\"relation\":\"FAther\",\"phone\":\"09998546215\",\"initials\":\"F\",\"color\":\"rgb(255, 152, 0)\"},{\"name\":\"Maria\",\"relation\":\"Mother\",\"phone\":\"639123456789\",\"initials\":\"M\",\"color\":\"rgb(255, 152, 0)\"}]', '???? EMERGENCY ALERT ????\n                            ⚠ USER IS DEAF/MUTE - TEXT ONLY - NO CALLS ⚠\n                            \n                            Name: Juan Santos\n                            PWD ID: PWD-225-362\n                            Phone: +639111111111\n                            Address: , \n                            Status: Emergency SOS Activated\n                            Location: GPS coordinates will be included\n                            Time: [Timestamp]\n                            \n                            Medical Info:\n                            • Blood Type: A+\n                            • Allergies: \n                            • Medications:', '[]', '2026-02-18 14:18:50', '2026-02-19 02:54:06'),
(12, 42, 'Jerome', 'Buntalidad', NULL, '', '78-67-56-453-443', '09671789112', 'imspastic01@gmail.com', '', '', '', '', '', '', '[]', '[]', '[]', '[]', '', '[]', '2026-03-23 20:58:08', '2026-03-23 20:58:08'),
(17, 39, 'Felix', 'Lee', NULL, 'Male', 'PWD-689-256', '09856472161', 'leefelix@gmail.com', '', '', '', '', 'Deaf/Mute', 'A+', '[]', '[]', '[]', '[{\"name\":\"Lee Know\",\"relation\":\"Friend\",\"phone\":\"09701990175\",\"initials\":\"LK\",\"color\":\"rgb(67, 160, 71)\"}]', '???? EMERGENCY ALERT ????\n                            ⚠ USER IS DEAF/MUTE - TEXT ONLY - NO CALLS ⚠\n                            \n                            Name: Felix Lee\n                            PWD ID: PWD-689-256\n                            Phone: 09856472161\n                            Address: , \n                            Status: Emergency SOS Activated\n                            Location: GPS coordinates will be included\n                            Time: [Timestamp]\n                            \n                            Medical Info:\n                            • Blood Type: A+\n                            • Allergies: \n                            • Medications:', '[]', '2026-02-20 02:10:33', '2026-03-03 23:14:45'),
(22, 47, 'maria', 'mercedez', NULL, '', '', '09298242232', 'maria@gmail.com', '', '', '', '', '', '', '[]', '[]', '[]', '[]', '', '[]', '2026-02-25 09:16:46', '2026-02-25 09:16:46'),
(23, 50, 'Test', 'Relationship', NULL, 'Male', 'PWD-080-167', '09671189000', 'relationship@test.com', 'Paho', 'Bago', 'Taloc', '6101', 'Deaf/Mute', 'A+', '[]', '[]', '[]', '[{\"name\":\"Jerome Buntalidad\",\"relation\":\"Brother\",\"phone\":\"09162360648\",\"initials\":\"JB\",\"color\":\"rgb(229, 57, 53)\"}]', '???? EMERGENCY ALERT ????\n                            ⚠ USER IS DEAF/MUTE - TEXT ONLY - NO CALLS ⚠\n                            \n                            Name: Test Relationship\n                            PWD ID: \n                            Phone: 09671189000\n                            Address: , \n                            Status: Emergency SOS Activated\n                            Location: GPS coordinates will be included\n                            Time: [Timestamp]\n                            \n                            Medical Info:\n                            • Blood Type: \n                            • Allergies: \n                            • Medications:', '[]', '2026-03-02 20:57:48', '2026-03-02 20:59:31'),
(24, 53, 'Leo', 'Shura', NULL, '', '', '09675612342', 'leoshura@gmail.com', '', '', '', '', '', '', '[]', '[]', '[]', '[]', '', '[]', '2026-03-03 19:42:40', '2026-03-03 19:42:40'),
(25, 54, 'Baby', 'mo', '2005-12-14', 'Female', 'PWD-080-123', '09123456789', 'babymo@gmail.com', 'Purok Toterz', 'Bacolod City', 'Negros Occidental', '6100', 'Deaf/Mute', 'O+', '[]', '[]', '[]', '[]', '???? EMERGENCY ALERT ????\n                            ⚠ USER IS DEAF/MUTE - TEXT ONLY - NO CALLS ⚠\n                            \n                            Name: Baby mo\n                            PWD ID: \n                            Phone: 09123456789\n                            Address: , \n                            Status: Emergency SOS Activated\n                            Location: GPS coordinates will be included\n                            Time: [Timestamp]\n                            \n                            Medical Info:\n                            • Blood Type: \n                            • Allergies: \n                            • Medications:', '[]', '2026-03-04 02:25:03', '2026-03-04 02:26:13'),
(26, 55, 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'Duco', NULL, 'Male', '', '09707216118', 'jul@gmail.com', '', '', '', '', 'Deaf/Mute', 'A+', '[]', '[]', '[]', '[]', '???? EMERGENCY ALERT ????\n                            ⚠ USER IS DEAF/MUTE - TEXT ONLY - NO CALLS ⚠\n                            \n                            Name: AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA Duco\n                            PWD ID: \n                            Phone: 09707216118\n                            Address: , \n                            Status: Emergency SOS Activated\n                            Location: GPS coordinates will be included\n                            Time: [Timestamp]\n                            \n                            Medical Info:\n                            • Blood Type: A+\n                            • Allergies: \n                            • Medications:', '[]', '2026-03-04 03:13:55', '2026-03-04 03:15:50'),
(27, 57, 'Julianna', 'Anne', NULL, 'Male', '', '09767676767', 'che@gmail.com', '', '', '', '', 'Deaf/Mute', 'A+', '[]', '[]', '[]', '[{\"name\":\"Charmelle Duco\",\"relation\":\"Sibling\",\"phone\":\"09676767676\",\"initials\":\"CD\",\"color\":\"rgb(30, 136, 229)\"}]', '???? EMERGENCY ALERT ????\n                            ⚠ USER IS DEAF/MUTE - TEXT ONLY - NO CALLS ⚠\n                            \n                            Name: Che Anne\n                            PWD ID: \n                            Phone: 09767676767\n                            Address: , \n                            Status: Emergency SOS Activated\n                            Location: GPS coordinates will be included\n                            Time: [Timestamp]\n                            \n                            Medical Info:\n                            • Blood Type: \n                            • Allergies: \n                            • Medications:', '[]', '2026-03-04 03:22:00', '2026-03-04 03:23:47'),
(28, 58, 'Yeji', 'Hwang', NULL, '', '', '09665896542', 'hwangyeji@gmail.com', '', '', '', '', '', '', '[]', '[]', '[]', '[]', '', '[]', '2026-03-17 12:33:27', '2026-03-17 12:33:27'),
(29, 59, 'Dairin', 'Janagap', NULL, 'Male', '', '09152411363', 'd.janagap@usls.edu.ph', '', '', '', '', 'Deaf/Mute', 'A+', '[]', '[]', '[]', '[{\"name\":\"Ramon Magsaysay\",\"relation\":\"Uncle\",\"phone\":\"09152411363\",\"initials\":\"RM\",\"color\":\"rgb(30, 136, 229)\"}]', '???? EMERGENCY ALERT ????\n                            ⚠ Silent Signal Alert ⚠\n                            \n                            Name: Dairin Janagap\n                            PWD ID: \n                            Phone: 09152411363\n                            Address: Silay City\n                            Status: Emergency SOS Activated\n                            Location: GPS coordinates will be included\n                            Time: [Timestamp]\n                            \n                            Medical Info:\n                            • Blood Type: A+\n                            • Allergies: \n                            • Medications:', '[]', '2026-03-19 01:53:13', '2026-03-19 03:31:38'),
(30, 60, 'Ryujin', 'Shin', NULL, '', '', '09665477852', 'shinryujin@gmail.com', '', '', '', '', '', '', '[]', '[]', '[]', '[]', '', '[]', '2026-03-19 02:42:17', '2026-03-19 02:42:17'),
(31, 61, 'User', 'Secret', NULL, 'Male', '', '09704943152', 'user123@gmail.com', 'secret', 'secret', 'secret', '6100', 'Deaf/Mute', 'A+', '[]', '[]', '[]', '[{\"name\":\"rick roll\",\"relation\":\"Father\",\"phone\":\"09266896211\",\"initials\":\"RR\",\"color\":\"rgb(229, 57, 53)\"}]', '???? EMERGENCY ALERT ????\n                            ⚠ USER IS DEAF/MUTE - TEXT ONLY - NO CALLS ⚠\n                            \n                            Name: User Secret\n                            PWD ID: \n                            Phone: 09704943152\n                            Address: secret, secret\n                            Status: Emergency SOS Activated\n                            Location: GPS coordinates will be included\n                            Time: [Timestamp]\n                            \n                            Medical Info:\n                            • Blood Type: A+\n                            • Allergies: \n                            • Medications:', '[]', '2026-03-19 03:41:32', '2026-03-19 03:45:32'),
(32, 62, 'sample', 'user', NULL, 'Male', '', '09696969669', 'sampleuser@gmail.com', '', '', '', '', 'Deaf/Mute', 'O+', '[\"school\"]', '[\"addict\"]', '[\"deaf\"]', '[{\"name\":\"usersample\",\"relation\":\"papa\",\"phone\":\"09704943152\",\"initials\":\"U\",\"color\":\"rgb(255, 152, 0)\"}]', '???? EMERGENCY ALERT ????\n                            ⚠ USER IS DEAF/MUTE - TEXT ONLY - NO CALLS ⚠\n                            \n                            Name: sample user\n                            PWD ID: \n                            Phone: 09696969669\n                            Address: , \n                            Status: Emergency SOS Activated\n                            Location: GPS coordinates will be included\n                            Time: [Timestamp]\n                            \n                            Medical Info:\n                            • Blood Type: \n                            • Allergies: \n                            • Medications:', '[]', '2026-03-19 04:30:26', '2026-03-19 04:32:41'),
(33, 63, 'Aziealle', 'Cruz', NULL, 'Male', '00-11-22-334-4556677', '09099372368', 'aziealle@gmail.com', '', '', '', '', 'Deaf/Mute', '', '[]', '[]', '[]', '[{\"name\":\"Aizhelle de la Cruz\",\"relation\":\"Friend\",\"phone\":\"09949771317\",\"initials\":\"AD\",\"color\":\"rgb(142, 36, 170)\"},{\"name\":\"Maria Fe Geronimo\",\"relation\":\"Guardian\",\"phone\":\"09071712614\",\"initials\":\"MF\",\"color\":\"rgb(142, 36, 170)\"}]', '???? EMERGENCY ALERT ????\n                            ⚠ USER IS DEAF/MUTE - TEXT ONLY - NO CALLS ⚠\n                            \n                            Name: Aziealle Cruz\n                            PWD ID: 00-11-22-334-4556677\n                            Phone: 09099372368\n                            Address: , \n                            Status: Emergency SOS Activated\n                            Location: GPS coordinates will be included\n                            Time: [Timestamp]\n                            \n                            Medical Info:\n                            • Blood Type: \n                            • Allergies: \n                            • Medications:', '[]', '2026-03-20 03:56:39', '2026-03-23 10:16:33'),
(34, 64, 'Jerome', 'Buntalidad', NULL, '', '78-67-56-453-4', '09671789112', 'jerome@gmail.com', '', '', '', '', '', '', '[]', '[]', '[]', '[]', '', '[]', '2026-03-23 15:03:24', '2026-03-23 15:03:24');

-- --------------------------------------------------------

--
-- Table structure for table `mfa_codes`
--

CREATE TABLE `mfa_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mfa_codes`
--

INSERT INTO `mfa_codes` (`id`, `user_id`, `code`, `expires_at`, `used`, `created_at`) VALUES
(1, 42, '941954', '2026-03-24 05:22:03', 1, '2026-03-23 21:12:03'),
(2, 42, '959593', '2026-03-24 05:43:28', 1, '2026-03-23 21:33:28');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pwd_emergency_contacts`
--

CREATE TABLE `pwd_emergency_contacts` (
  `id` int(11) NOT NULL,
  `pwd_user_id` int(11) NOT NULL,
  `contact_user_id` int(11) DEFAULT NULL,
  `contact_name` varchar(150) NOT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pwd_emergency_contacts`
--

INSERT INTO `pwd_emergency_contacts` (`id`, `pwd_user_id`, `contact_user_id`, `contact_name`, `contact_phone`, `relationship`, `is_primary`, `created_at`, `updated_at`) VALUES
(40, 24, 17, 'Family', '09998546215', 'FAther', 1, '2026-02-19 02:54:06', '2026-02-19 02:54:06'),
(41, 24, NULL, 'Maria', '639123456789', 'Mother', 0, '2026-02-19 02:54:06', '2026-02-19 02:54:06'),
(99, 50, NULL, 'Jerome Buntalidad', '09162360648', 'Brother', 1, '2026-03-02 21:01:48', '2026-03-02 21:01:48'),
(172, 39, NULL, 'Lee Know', '09701990175', 'Friend', 1, '2026-03-03 23:14:45', '2026-03-03 23:14:45'),
(253, 57, 56, 'Charmelle Duco', '09676767676', 'Sibling', 1, '2026-03-04 03:23:47', '2026-03-04 03:23:47'),
(340, 59, 59, 'Ramon Magsaysay', '09152411363', 'Uncle', 1, '2026-03-19 03:31:38', '2026-03-19 03:31:38'),
(342, 61, NULL, 'rick roll', '09266896211', 'Father', 1, '2026-03-19 03:47:26', '2026-03-19 03:47:26'),
(345, 62, 61, 'usersample', '09704943152', 'papa', 1, '2026-03-19 04:50:32', '2026-03-19 04:50:32'),
(521, 63, 9, 'Aizhelle de la Cruz', '09949771317', 'Friend', 1, '2026-03-23 10:17:22', '2026-03-23 10:17:22'),
(522, 63, NULL, 'Maria Fe Geronimo', '09071712614', 'Guardian', 0, '2026-03-23 10:17:22', '2026-03-23 10:17:22'),
(547, 19, 17, 'Family Test', '09998546215', 'Father', 1, '2026-03-23 11:01:40', '2026-03-23 11:01:40'),
(548, 19, NULL, 'Family Test 3', '09671789112', 'Aunt', 0, '2026-03-23 11:01:40', '2026-03-23 11:01:40'),
(549, 19, NULL, 'Family Test 4', '09312424151', 'Friend', 0, '2026-03-23 11:01:40', '2026-03-23 11:01:40'),
(550, 19, NULL, 'Family Test 5', '09994105502', 'Friend', 0, '2026-03-23 11:01:40', '2026-03-23 11:01:40'),
(555, 9, NULL, 'Maria Fe Geronimo', '09071712614', 'Guardian', 1, '2026-03-23 11:19:31', '2026-03-23 11:19:31');

-- --------------------------------------------------------

--
-- Table structure for table `pwd_status_updates`
--

CREATE TABLE `pwd_status_updates` (
  `id` int(11) NOT NULL,
  `pwd_user_id` int(11) NOT NULL,
  `status` enum('safe','danger','unknown','needs_assistance') DEFAULT 'unknown',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `battery_level` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pwd_status_updates`
--

INSERT INTO `pwd_status_updates` (`id`, `pwd_user_id`, `status`, `latitude`, `longitude`, `battery_level`, `message`, `created_at`) VALUES
(1, 24, 'safe', 10.67800000, 122.95060000, 85, 'At home, all good', '2026-02-18 14:05:46'),
(2, 9, 'safe', 10.67903567, 122.96217831, NULL, 'I\'m safe.', '2026-02-19 01:26:56'),
(3, 9, 'needs_assistance', 10.67902260, 122.96218004, NULL, 'I need help!', '2026-02-19 01:27:01'),
(4, 9, 'safe', 10.67900746, 122.96221790, NULL, 'I\'m safe.', '2026-02-19 01:48:57'),
(5, 19, 'safe', NULL, NULL, NULL, 'I\'m safe.', '2026-03-03 11:00:51'),
(6, 19, 'needs_assistance', NULL, NULL, NULL, 'I need help!', '2026-03-03 11:00:53'),
(7, 19, 'safe', 10.77542950, 122.97273100, NULL, 'I\'m safe.', '2026-03-03 11:01:59'),
(8, 9, 'needs_assistance', 10.72608643, 122.98269281, NULL, 'I need help!', '2026-03-03 14:13:00'),
(9, 9, 'safe', 10.72608643, 122.98269281, NULL, 'I\'m safe.', '2026-03-03 14:13:12'),
(10, 9, 'needs_assistance', 10.67862259, 122.96224517, NULL, 'I need help!', '2026-03-04 00:17:22'),
(11, 9, 'safe', 10.67862259, 122.96224517, NULL, 'I\'m safe.', '2026-03-04 00:17:29'),
(12, 9, 'needs_assistance', 10.67868779, 122.96225184, NULL, 'I need help!', '2026-03-04 02:01:06'),
(13, 9, 'safe', 10.67868854, 122.96225202, NULL, 'I\'m safe.', '2026-03-04 02:13:26'),
(14, 9, 'needs_assistance', 10.67868776, 122.96225377, NULL, 'I need help!', '2026-03-04 02:13:38'),
(15, 9, 'safe', 10.67867529, 122.96224287, NULL, 'I\'m safe.', '2026-03-04 02:17:22'),
(16, 9, 'needs_assistance', 10.67845410, 122.96223730, NULL, 'I need help!', '2026-03-04 02:21:03'),
(17, 9, 'safe', 10.67845370, 122.96223730, NULL, 'I\'m safe.', '2026-03-04 02:21:34'),
(18, 54, 'needs_assistance', 10.67868268, 122.96225203, NULL, 'I need help!', '2026-03-04 02:28:00'),
(19, 54, 'needs_assistance', 10.67868854, 122.96225202, NULL, 'I need help!', '2026-03-04 02:30:00'),
(20, 54, 'safe', 10.67868854, 122.96225202, NULL, 'I\'m safe.', '2026-03-04 02:30:06'),
(21, 9, 'needs_assistance', 10.67869469, 122.96225807, NULL, 'I need help!', '2026-03-04 02:42:25'),
(22, 9, 'safe', 10.67869469, 122.96225807, NULL, 'I\'m safe.', '2026-03-04 02:42:46'),
(23, 9, 'needs_assistance', 10.67868854, 122.96225202, NULL, 'I need help!', '2026-03-04 02:57:51'),
(24, 9, 'safe', 10.67868854, 122.96225202, NULL, 'I\'m safe.', '2026-03-04 02:57:57'),
(25, 9, 'needs_assistance', 10.67868854, 122.96225202, NULL, 'I need help!', '2026-03-04 03:10:39'),
(26, 9, 'safe', 10.67846180, 122.96224330, NULL, 'I\'m safe.', '2026-03-04 03:10:52'),
(27, 9, 'safe', 10.67846650, 122.96224300, NULL, 'I\'m safe.', '2026-03-04 03:11:43'),
(28, 9, 'needs_assistance', 10.67846920, 122.96224240, NULL, 'I need help!', '2026-03-04 03:11:48'),
(29, 9, 'safe', 10.67846940, 122.96224220, NULL, 'I\'m safe.', '2026-03-04 03:11:52'),
(30, 9, 'needs_assistance', 10.67869065, 122.96225820, NULL, 'I need help!', '2026-03-04 03:34:35'),
(31, 9, 'safe', 10.67869065, 122.96225820, NULL, 'I\'m safe.', '2026-03-04 03:34:43'),
(32, 9, 'safe', 10.67869065, 122.96225820, NULL, 'I\'m safe.', '2026-03-04 03:34:43'),
(33, 9, 'needs_assistance', 10.67862588, 122.96223904, NULL, 'I need help!', '2026-03-04 04:55:57'),
(34, 9, 'needs_assistance', 10.67868790, 122.96225789, NULL, 'I need help!', '2026-03-04 04:56:00'),
(35, 9, 'safe', 10.67869360, 122.96225198, NULL, 'I\'m safe.', '2026-03-04 04:57:57'),
(36, 9, 'needs_assistance', 10.67867417, 122.96223974, NULL, 'I need help!', '2026-03-04 05:12:21'),
(37, 9, 'safe', 10.67868849, 122.96225004, NULL, 'I\'m safe.', '2026-03-04 05:12:49'),
(38, 9, 'needs_assistance', 10.67847000, 122.96223440, NULL, 'I need help!', '2026-03-04 05:26:07'),
(39, 9, 'safe', 10.67846910, 122.96223610, NULL, 'I\'m safe.', '2026-03-04 05:26:17'),
(40, 9, 'needs_assistance', 10.67868196, 122.96225482, NULL, 'I need help!', '2026-03-04 05:53:34'),
(41, 9, 'safe', 10.67868196, 122.96225482, NULL, 'I\'m safe.', '2026-03-04 05:53:43'),
(42, 9, 'safe', 10.67868196, 122.96225482, NULL, 'I\'m safe.', '2026-03-04 06:22:32'),
(43, 9, 'needs_assistance', 10.67848850, 122.96226090, NULL, 'I need help!', '2026-03-04 06:22:35'),
(44, 9, 'safe', 10.67868776, 122.96225377, NULL, 'I\'m safe.', '2026-03-04 06:37:05'),
(45, 61, 'needs_assistance', NULL, NULL, NULL, 'I need help!', '2026-03-19 03:47:31'),
(46, 61, 'needs_assistance', NULL, NULL, NULL, 'I need help!', '2026-03-19 03:47:53'),
(47, 62, 'needs_assistance', NULL, NULL, NULL, 'I need help!', '2026-03-19 04:33:48'),
(48, 62, 'safe', NULL, NULL, NULL, 'I\'m safe.', '2026-03-19 04:33:52'),
(49, 62, 'needs_assistance', NULL, NULL, NULL, 'I need help!', '2026-03-19 04:33:54');

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `user_id`, `token_hash`, `expires_at`, `created_at`) VALUES
(16, 63, '1c1e6cd8b1c2b5776147881289c20eba087234a8f82a90e06b173065e6e004f6', '2026-04-22 10:15:19', '2026-03-23 10:15:19');

-- --------------------------------------------------------

--
-- Table structure for table `sms_events`
--

CREATE TABLE `sms_events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `messages` text NOT NULL,
  `contacts` text NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sms_events`
--

INSERT INTO `sms_events` (`id`, `user_id`, `messages`, `contacts`, `latitude`, `longitude`, `created_at`) VALUES
(1, 9, '[{\"id\":\"medical_help\",\"title\":\"Medical Help\",\"desc\":\"I need medical assistance\"},{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"}]', '[{\"name\":\"Wynn de la Cruz\",\"phone\":\"09162360648\"}]', 10.58040675, 122.90691235, '2026-02-18 19:38:42'),
(2, 19, '[{\"id\":\"flood\",\"title\":\"Flood\",\"desc\":\"Flooding in area\"},{\"id\":\"fire\",\"title\":\"Fire\",\"desc\":\"There is a fire\"}]', '[{\"name\":\"Contact\",\"phone\":\"09994105502\"}]', 10.67821120, 122.96171060, '2026-02-18 23:25:45'),
(3, 9, '[{\"id\":\"medical_help\",\"title\":\"Medical Help\",\"desc\":\"I need medical assistance\"},{\"id\":\"first_aid\",\"title\":\"First Aid\",\"desc\":\"I need first aid\"}]', '[{\"name\":\"Wynn de la Cruz\",\"phone\":\"09162360648\"}]', 10.67902339, 122.96220897, '2026-02-19 01:27:32'),
(4, 19, '[{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"},{\"id\":\"sick\",\"title\":\"Sick\",\"desc\":\"I am feeling sick\"},{\"id\":\"food\",\"title\":\"Food\",\"desc\":\"I need food\"},{\"id\":\"drinks\",\"title\":\"Drinks\",\"desc\":\"I need something to drink\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"}]', NULL, NULL, '2026-03-03 11:01:03'),
(5, 19, '[{\"id\":\"shelter\",\"title\":\"Shelter\",\"desc\":\"I need shelter\"},{\"id\":\"rest_area\",\"title\":\"Rest Area\",\"desc\":\"Looking for rest area\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"}]', 10.69000000, 122.57000000, '2026-03-03 11:41:29'),
(6, 19, '[{\"id\":\"medical_help\",\"title\":\"Medical Help\",\"desc\":\"I need medical assistance\"},{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"}]', NULL, NULL, '2026-03-03 11:42:23'),
(7, 19, '[{\"id\":\"sick\",\"title\":\"Sick\",\"desc\":\"I am feeling sick\"},{\"id\":\"first_aid\",\"title\":\"First Aid\",\"desc\":\"I need first aid\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"}]', 10.77537320, 122.97270990, '2026-03-03 11:44:30'),
(8, 19, '[{\"id\":\"water\",\"title\":\"Water\",\"desc\":\"I need clean water\"},{\"id\":\"drinking_water\",\"title\":\"Drinking Water\",\"desc\":\"I need drinking water\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"}]', 10.69000000, 122.57000000, '2026-03-03 11:51:15'),
(9, 19, '[{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"},{\"id\":\"sick\",\"title\":\"Sick\",\"desc\":\"I am feeling sick\"},{\"id\":\"first_aid\",\"title\":\"First Aid\",\"desc\":\"I need first aid\"},{\"id\":\"food\",\"title\":\"Food\",\"desc\":\"I need food\"},{\"id\":\"drinks\",\"title\":\"Drinks\",\"desc\":\"I need something to drink\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"}]', 10.69000000, 122.57000000, '2026-03-03 11:58:19'),
(10, 19, '[{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"},{\"id\":\"sick\",\"title\":\"Sick\",\"desc\":\"I am feeling sick\"},{\"id\":\"first_aid\",\"title\":\"First Aid\",\"desc\":\"I need first aid\"},{\"id\":\"food\",\"title\":\"Food\",\"desc\":\"I need food\"},{\"id\":\"drinks\",\"title\":\"Drinks\",\"desc\":\"I need something to drink\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"}]', 10.69000000, 122.57000000, '2026-03-03 11:58:50'),
(11, 9, '[{\"id\":\"emergency\",\"title\":\"Emergency\",\"desc\":\"This is an emergency\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.72608643, 122.98269281, '2026-03-03 12:02:51'),
(12, 19, '[{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"},{\"id\":\"sick\",\"title\":\"Sick\",\"desc\":\"I am feeling sick\"},{\"id\":\"first_aid\",\"title\":\"First Aid\",\"desc\":\"I need first aid\"},{\"id\":\"food\",\"title\":\"Food\",\"desc\":\"I need food\"},{\"id\":\"drinks\",\"title\":\"Drinks\",\"desc\":\"I need something to drink\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"}]', 10.69000000, 122.57000000, '2026-03-03 12:10:43'),
(13, 9, '[{\"id\":\"food\",\"title\":\"Food\",\"desc\":\"I need food\"},{\"id\":\"drinks\",\"title\":\"Drinks\",\"desc\":\"I need something to drink\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67846239, 122.96232443, '2026-03-04 00:08:01'),
(14, 9, '[{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"},{\"id\":\"sick\",\"title\":\"Sick\",\"desc\":\"I am feeling sick\"},{\"id\":\"first_aid\",\"title\":\"First Aid\",\"desc\":\"I need first aid\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67835840, 122.96223660, '2026-03-04 01:58:57'),
(15, 9, '[{\"id\":\"shelter\",\"title\":\"Shelter\",\"desc\":\"I need shelter\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67846640, 122.96223460, '2026-03-04 02:14:22'),
(16, 9, '[{\"id\":\"sick\",\"title\":\"Sick\",\"desc\":\"I am feeling sick\"},{\"id\":\"first_aid\",\"title\":\"First Aid\",\"desc\":\"I need first aid\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67846310, 122.96225370, '2026-03-04 02:22:31'),
(17, 9, '[{\"id\":\"medical_help\",\"title\":\"Medical Help\",\"desc\":\"I need medical assistance\"},{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"},{\"id\":\"sick\",\"title\":\"Sick\",\"desc\":\"I am feeling sick\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67846320, 122.96224940, '2026-03-04 02:45:21'),
(18, 9, '[{\"id\":\"fire\",\"title\":\"Fire\",\"desc\":\"There is a fire\"},{\"id\":\"lost\",\"title\":\"Lost\",\"desc\":\"I am lost\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67844410, 122.96226530, '2026-03-04 02:46:38'),
(19, 9, '[{\"id\":\"food\",\"title\":\"Food\",\"desc\":\"I need food\"},{\"id\":\"drinks\",\"title\":\"Drinks\",\"desc\":\"I need something to drink\"},{\"id\":\"hungry\",\"title\":\"Hungry\",\"desc\":\"I am hungry\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67845960, 122.96223380, '2026-03-04 02:59:06'),
(20, 9, '[{\"id\":\"medical_help\",\"title\":\"Medical Help\",\"desc\":\"I need medical assistance\"},{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"},{\"id\":\"sick\",\"title\":\"Sick\",\"desc\":\"I am feeling sick\"},{\"id\":\"first_aid\",\"title\":\"First Aid\",\"desc\":\"I need first aid\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67843700, 122.96223500, '2026-03-04 03:09:18'),
(21, 9, '[{\"id\":\"food\",\"title\":\"Food\",\"desc\":\"I need food\"},{\"id\":\"drinks\",\"title\":\"Drinks\",\"desc\":\"I need something to drink\"},{\"id\":\"hungry\",\"title\":\"Hungry\",\"desc\":\"I am hungry\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67845580, 122.96223470, '2026-03-04 03:09:34'),
(22, 9, '[{\"id\":\"medical_help\",\"title\":\"Medical Help\",\"desc\":\"I need medical assistance\"},{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"},{\"id\":\"sick\",\"title\":\"Sick\",\"desc\":\"I am feeling sick\"},{\"id\":\"first_aid\",\"title\":\"First Aid\",\"desc\":\"I need first aid\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67848000, 122.96224000, '2026-03-04 03:32:30'),
(23, 9, '[{\"id\":\"food\",\"title\":\"Food\",\"desc\":\"I need food\"},{\"id\":\"drinks\",\"title\":\"Drinks\",\"desc\":\"I need something to drink\"},{\"id\":\"hungry\",\"title\":\"Hungry\",\"desc\":\"I am hungry\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67845350, 122.96219830, '2026-03-04 04:57:10'),
(24, 9, '[{\"id\":\"medical_help\",\"title\":\"Medical Help\",\"desc\":\"I need medical assistance\"},{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"},{\"id\":\"sick\",\"title\":\"Sick\",\"desc\":\"I am feeling sick\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67841660, 122.96226630, '2026-03-04 05:14:49'),
(25, 9, '[{\"id\":\"medical_help\",\"title\":\"Medical Help\",\"desc\":\"I need medical assistance\"},{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"},{\"id\":\"sick\",\"title\":\"Sick\",\"desc\":\"I am feeling sick\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67841660, 122.96226630, '2026-03-04 05:16:05'),
(26, 9, '[{\"id\":\"water\",\"title\":\"Water\",\"desc\":\"I need clean water\"},{\"id\":\"drinking_water\",\"title\":\"Drinking Water\",\"desc\":\"I need drinking water\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67845240, 122.96225900, '2026-03-04 05:27:24'),
(27, 9, '[{\"id\":\"water\",\"title\":\"Water\",\"desc\":\"I need clean water\"},{\"id\":\"drinking_water\",\"title\":\"Drinking Water\",\"desc\":\"I need drinking water\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67844640, 122.96226220, '2026-03-04 05:29:24'),
(28, 9, '[{\"id\":\"water\",\"title\":\"Water\",\"desc\":\"I need clean water\"},{\"id\":\"drinking_water\",\"title\":\"Drinking Water\",\"desc\":\"I need drinking water\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67845650, 122.96225570, '2026-03-04 05:29:46'),
(29, 9, '[{\"id\":\"water\",\"title\":\"Water\",\"desc\":\"I need clean water\"},{\"id\":\"drinking_water\",\"title\":\"Drinking Water\",\"desc\":\"I need drinking water\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67844020, 122.96226440, '2026-03-04 05:30:06'),
(30, 9, '[{\"id\":\"water\",\"title\":\"Water\",\"desc\":\"I need clean water\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67842950, 122.96223110, '2026-03-04 05:55:30'),
(31, 9, '[{\"id\":\"water\",\"title\":\"Water\",\"desc\":\"I need clean water\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67842950, 122.96223110, '2026-03-04 05:55:30'),
(32, 9, '[{\"id\":\"medical_help\",\"title\":\"Medical Help\",\"desc\":\"I need medical assistance\"},{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67850600, 122.96226140, '2026-03-04 06:23:01'),
(33, 9, '[{\"id\":\"medical_help\",\"title\":\"Medical Help\",\"desc\":\"I need medical assistance\"},{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.67843760, 122.96225600, '2026-03-04 06:41:12'),
(34, 9, '[{\"id\":\"sos\",\"title\":\"SOS EMERGENCY\",\"desc\":\"Immediate emergency assistance needed\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', 10.72606469, 122.98275610, '2026-03-17 12:51:10'),
(35, 9, '[{\"id\":\"sos\",\"title\":\"SOS EMERGENCY\",\"desc\":\"Immediate emergency assistance needed\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"},{\"name\":\"Wynn de la Cruz\",\"phone\":\"09099372364\"}]', NULL, NULL, '2026-03-18 16:45:46'),
(36, 9, '[{\"id\":\"sos\",\"title\":\"SOS EMERGENCY\",\"desc\":\"Immediate emergency assistance needed\"}]', '[{\"name\":\"Maebelle de la Cruz\",\"phone\":\"09159832461\"}]', 10.72607473, 122.98277757, '2026-03-18 22:40:53'),
(37, 19, '[{\"id\":\"sos\",\"title\":\"SOS EMERGENCY\",\"desc\":\"Immediate emergency assistance needed\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"},{\"name\":\"Family Test 2\",\"phone\":\"09994105502\"},{\"name\":\"Family Test 3\",\"phone\":\"09996587456\"}]', NULL, NULL, '2026-03-19 02:37:39'),
(38, 61, '[{\"id\":\"water\",\"title\":\"Water\",\"desc\":\"I need clean water\"}]', '[{\"name\":\"rick roll\",\"phone\":\"09266896211\"}]', NULL, NULL, '2026-03-19 03:49:07'),
(39, 62, '[{\"id\":\"sos\",\"title\":\"SOS EMERGENCY\",\"desc\":\"Immediate emergency assistance needed\"}]', '[{\"name\":\"usersample\",\"phone\":\"09704943152\"}]', NULL, NULL, '2026-03-19 04:32:55'),
(40, 62, '[{\"id\":\"sos\",\"title\":\"SOS EMERGENCY\",\"desc\":\"Immediate emergency assistance needed\"}]', '[{\"name\":\"usersample\",\"phone\":\"09704943152\"}]', NULL, NULL, '2026-03-19 04:33:08'),
(41, 62, '[{\"id\":\"emergency\",\"title\":\"Emergency\",\"desc\":\"This is an emergency\"}]', '[{\"name\":\"usersample\",\"phone\":\"09704943152\"}]', NULL, NULL, '2026-03-19 04:34:35'),
(42, 62, '[{\"id\":\"emergency\",\"title\":\"Emergency\",\"desc\":\"This is an emergency\"}]', '[{\"name\":\"usersample\",\"phone\":\"09704943152\"}]', NULL, NULL, '2026-03-19 04:34:44'),
(43, 9, '[{\"id\":\"sos\",\"title\":\"SOS EMERGENCY\",\"desc\":\"Immediate emergency assistance needed\"}]', '[{\"name\":\"Reghis Dioma\",\"phone\":\"09994105502\"},{\"name\":\"Jerome Buntalidad\",\"phone\":\"09671789112\"}]', 10.89935279, 123.07283616, '2026-03-19 10:34:05'),
(44, 19, '[{\"id\":\"lost\",\"title\":\"Lost\",\"desc\":\"I am lost\"},{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"},{\"id\":\"flood\",\"title\":\"Flood\",\"desc\":\"Flooding in area\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"},{\"name\":\"Family Test 2\",\"phone\":\"09994105502\"},{\"name\":\"Family Test 3\",\"phone\":\"09996587456\"}]', 10.77542950, 122.97271290, '2026-03-22 06:35:54'),
(45, 19, '[{\"id\":\"lost\",\"title\":\"Lost\",\"desc\":\"I am lost\"},{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"},{\"id\":\"flood\",\"title\":\"Flood\",\"desc\":\"Flooding in area\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"},{\"name\":\"Family Test 2\",\"phone\":\"09994105502\"},{\"name\":\"Family Test 3\",\"phone\":\"09996587456\"}]', 10.77543640, 122.97273350, '2026-03-22 06:37:03'),
(46, 19, '[{\"id\":\"lost\",\"title\":\"Lost\",\"desc\":\"I am lost\"},{\"id\":\"medication\",\"title\":\"Medication\",\"desc\":\"I need medication\"},{\"id\":\"flood\",\"title\":\"Flood\",\"desc\":\"Flooding in area\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"},{\"name\":\"Family Test 2\",\"phone\":\"09994105502\"},{\"name\":\"Family Test 3\",\"phone\":\"09996587456\"}]', 10.77545820, 122.97274020, '2026-03-22 06:41:28'),
(47, 19, '[{\"id\":\"emergency\",\"title\":\"Emergency\",\"desc\":\"This is an emergency\"},{\"id\":\"danger\",\"title\":\"Danger\",\"desc\":\"I am in danger\"},{\"id\":\"injured\",\"title\":\"Injured\",\"desc\":\"I am injured\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"},{\"name\":\"Family Test 2\",\"phone\":\"09994105502\"},{\"name\":\"Family Test 3\",\"phone\":\"09996587456\"}]', 10.77544560, 122.97273480, '2026-03-22 06:44:10'),
(48, 19, '[{\"id\":\"emergency\",\"title\":\"Emergency\",\"desc\":\"This is an emergency\"},{\"id\":\"danger\",\"title\":\"Danger\",\"desc\":\"I am in danger\"},{\"id\":\"injured\",\"title\":\"Injured\",\"desc\":\"I am injured\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"},{\"name\":\"Family Test 2\",\"phone\":\"09994105502\"},{\"name\":\"Family Test 3\",\"phone\":\"09996587456\"}]', 10.77545460, 122.97273330, '2026-03-22 07:01:37'),
(49, 19, '[{\"id\":\"emergency\",\"title\":\"Emergency\",\"desc\":\"This is an emergency\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"},{\"name\":\"Family Test 2\",\"phone\":\"09994105502\"},{\"name\":\"Family Test 3\",\"phone\":\"09996587456\"}]', 10.77542040, 122.97273320, '2026-03-22 07:07:00'),
(50, 19, '[{\"id\":\"emergency\",\"title\":\"Emergency\",\"desc\":\"This is an emergency\"},{\"id\":\"danger\",\"title\":\"Danger\",\"desc\":\"I am in danger\"},{\"id\":\"injured\",\"title\":\"Injured\",\"desc\":\"I am injured\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"},{\"name\":\"Family Test 2\",\"phone\":\"09994105502\"},{\"name\":\"Family Test 3\",\"phone\":\"09996587456\"}]', 10.77544210, 122.97273230, '2026-03-22 07:09:22'),
(51, 19, '[{\"id\":\"sos\",\"title\":\"SOS EMERGENCY\",\"desc\":\"Immediate emergency assistance needed\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"},{\"name\":\"Family Test 2\",\"phone\":\"09994105502\"},{\"name\":\"Family Test 3\",\"phone\":\"09996587456\"}]', NULL, NULL, '2026-03-22 07:44:10'),
(52, 19, '[{\"id\":\"sos\",\"title\":\"SOS EMERGENCY\",\"desc\":\"Immediate emergency assistance needed\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"},{\"name\":\"Family Test 2\",\"phone\":\"09994105502\"},{\"name\":\"Family Test 3\",\"phone\":\"09996587456\"}]', NULL, NULL, '2026-03-22 07:44:22'),
(53, 19, '[{\"id\":\"sos\",\"title\":\"SOS EMERGENCY\",\"desc\":\"Immediate emergency assistance needed\"}]', '[{\"name\":\"Family Test\",\"phone\":\"09998546215\"},{\"name\":\"Family Test 2\",\"phone\":\"09994105502\"},{\"name\":\"Family Test 3\",\"phone\":\"09996587456\"}]', NULL, NULL, '2026-03-22 07:53:15'),
(54, 9, '[{\"id\":\"food\",\"title\":\"Food\",\"desc\":\"I need food\"},{\"id\":\"hungry\",\"title\":\"Hungry\",\"desc\":\"I am hungry\"}]', '[{\"name\":\"Aziealle Cruz\",\"phone\":\"09099372368\"}]', 10.72610540, 122.98273690, '2026-03-22 11:16:30'),
(55, 63, '[{\"id\":\"danger\",\"title\":\"Danger\",\"desc\":\"I am in danger\"}]', '[{\"name\":\"Aizhelle de la Cruz\",\"phone\":\"09949771317\"}]', 10.72610780, 122.98273770, '2026-03-22 11:20:37'),
(56, 63, '[{\"id\":\"emergency\",\"title\":\"Emergency\",\"desc\":\"This is an emergency\"},{\"id\":\"food\",\"title\":\"Food\",\"desc\":\"I need food\"}]', '[{\"name\":\"Aizhelle de la Cruz\",\"phone\":\"09949771317\"}]', 10.72622332, 122.98276581, '2026-03-22 11:39:32'),
(57, 9, '[{\"id\":\"hungry\",\"title\":\"Hungry\",\"desc\":\"I am hungry\"}]', '[{\"name\":\"Maria Fe Geronimo\",\"phone\":\"09071712614\"}]', 10.72607291, 122.98278902, '2026-03-23 10:14:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `role` enum('pwd','family','admin') NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `pwd_id_photo` varchar(255) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `phone_number`, `role`, `password`, `is_verified`, `is_active`, `pwd_id_photo`, `verified_at`, `created_at`, `updated_at`) VALUES
(9, 'Aizhelle', 'de la Cruz', 'aizhellegwynneth@gmail.com', '09949771317', 'pwd', '$2y$12$jS9d1z8FrflZ1exnolYiHOGhslECaBdUBLG2o5OmwlYfMIOI.6fs6', 1, 1, NULL, '2026-02-18 13:46:43', '2026-02-08 11:57:52', '2026-03-23 11:22:40'),
(11, 'Reghis', 'Dioma', 'rgdioma@gmail.com', '09612045422', 'admin', '$2y$12$36OMt6ZQt/zB35naI3psuOp77TRGQjqLGGFeIc3efz.Qcn.DS4wCO', 1, 1, NULL, '2026-02-18 13:46:43', '2026-02-08 16:10:22', '2026-02-18 13:46:43'),
(12, 'Jerome', 'Buntalidad', 'jeromebuntalidad@gmail.com', '09162360648', 'family', '$2y$12$SmgOSZxg0O.TZCdjATtM7.svqgboifxXPZqF6IRUB5CwWTx5DngRa', 1, 1, NULL, '2026-02-18 13:46:43', '2026-02-08 16:12:27', '2026-02-18 13:46:43'),
(17, 'Family', 'Test', 'family@gmail.com', '09998546215', 'family', '$2y$12$B2k2g/nUR95mYvioTIf9guVZ0.984U9j4Asmw3msPbzA8Cg9vEI2e', 1, 1, NULL, '2026-02-18 13:46:43', '2026-02-12 02:40:52', '2026-02-18 13:46:43'),
(18, 'Admin', 'Test', 'admin2@gmail.com', '09563248615', 'admin', '$2y$12$4M8Luoritb12UcCKCUoTPuO6yXrrBl3c/vf6XAqRpIXM7RwTWpwSi', 1, 1, NULL, '2026-02-18 13:46:43', '2026-02-12 02:42:00', '2026-02-18 13:46:43'),
(19, 'User', 'Test', 'user@gmail.com', '09875412658', 'pwd', '$2y$12$N7fdmBsRpRSTSVzZDLKG3eCLoCMORnoVBfYXpAjLrhZq.4KQmwQiG', 1, 1, NULL, '2026-02-18 13:46:43', '2026-02-12 02:43:42', '2026-02-18 13:46:43'),
(24, 'Juan', 'Santos', 'juan@example.com', '+639111111111', 'pwd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, NULL, '2026-02-18 13:46:43', '2026-02-17 11:57:24', '2026-02-18 13:46:43'),
(25, 'Maria', 'Santos', 'maria@example.com', '+639123456789', 'family', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, NULL, '2026-02-18 13:46:43', '2026-02-17 11:57:24', '2026-02-18 13:46:43'),
(26, 'Jose', 'Santos', 'jose@example.com', '+639234567890', 'family', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, NULL, '2026-02-18 13:46:43', '2026-02-17 11:57:24', '2026-02-18 13:46:43'),
(27, 'Ana', 'Santos', 'ana@example.com', '+639345678901', 'family', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, NULL, '2026-02-18 13:46:43', '2026-02-17 11:57:24', '2026-02-18 13:46:43'),
(29, 'Admin', 'User', 'admin@silentsignal.com', '+639123456789', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, NULL, '2026-02-18 14:20:37', '2026-02-18 14:00:38', '2026-02-18 14:20:37'),
(31, 'Marisol', 'Samillano', 'marisolsamillano@gmail.com', '09558697412', 'pwd', '$2y$12$QIq2j8ltUn/GMnbmGfSyDuNyh2hBpncC2TGpPOV2tPgeOjIP.WvCK', 1, 1, NULL, '2026-02-18 14:15:02', '2026-02-18 14:10:50', '2026-02-18 14:15:02'),
(33, 'Maebelle', 'de la Cruz', 'delacruzmaebelle@gmail.com', '09159832461', 'family', '$2y$12$ogMTY5iuXL9Tgnu.tpzXGem2aqFitH9H6NH.FrIqlzXiFod7GYjTW', 0, 1, NULL, NULL, '2026-02-19 01:56:45', '2026-02-19 01:56:45'),
(38, 'Admin', 'Test', 'admin@gmail.com', '09887564123', 'admin', '$2y$12$lXY2u695.gJyG5Y3V.CRz.0F60pBfXJF6JHtMlFB8l0qqqtJvqb9W', 1, 1, NULL, '2026-02-20 01:57:52', '2026-02-20 01:57:35', '2026-02-20 01:57:52'),
(39, 'Felix', 'Lee', 'leefelix@gmail.com', '09856472161', 'pwd', '$2y$12$.YjM3pwEq3FqkkL1NZTOR.sXASnLssd5uzeSRohhprpmXpCwoOr.2', 1, 1, NULL, '2026-02-20 02:13:03', '2026-02-20 02:10:33', '2026-02-20 02:13:03'),
(40, 'Hyunjin', 'Hwang', 'hwanghyunjin@gmail.com', '09235649874', 'admin', '$2y$12$Upa7.JOUxeYWfJGvxZEzP.qDr13Ne9G3jJRY.KypPJ2wJ13Ia0.yq', 1, 1, NULL, '2026-02-20 02:12:56', '2026-02-20 02:11:43', '2026-02-20 02:12:56'),
(41, 'Jerome', 'Buntalidad', 'pwd@gmail.com', '09162360649', 'pwd', '$2y$12$SmgOSZxg0O.TZCdjATtM7.svqgboifxXPZqF6IRUB5CwWTx5DngRa', 1, 1, 'pwd_41_1773978117.jpg', '2026-02-18 13:46:43', '2026-02-08 16:12:27', '2026-02-18 13:46:43'),
(42, 'Jerome', 'Buntalidad', 'imspastic01@gmail.com', '09671789112', 'pwd', '$2y$10$goD0p4OJ4ZMZTkgGa07JBOQJE/h2YWhLFoJd.CcdJoMe3ZWSDyV.G', 0, 1, 'pwd_42_1774299487.png', NULL, '2026-03-23 20:58:07', '2026-03-23 20:58:08'),
(46, 'Junhui', 'Wen', 'junhui@gmail.com', '09556447896', 'admin', '$2y$12$shb0YI/UL208H61r/Cnta.8lmuNy1pQIfgum55HlHmZSiXxbRyv52', 0, 1, NULL, NULL, '2026-02-20 03:07:11', '2026-02-20 03:07:11'),
(47, 'maria', 'mercedez', 'maria@gmail.com', '09298242232', 'pwd', '$2y$12$BRo1vycXRGjHQnZDG5uua./Kgad.FoGGSvqAROp5E6WopQZHSQbxK', 0, 1, NULL, NULL, '2026-02-25 09:16:46', '2026-02-25 09:16:46'),
(48, 'test', 'testing', 'testing@gmail.com', '09956489358', 'family', '$2y$12$in.wuzE4fxlgsemPuinjnuYH1NBK2shgkRRfaPxNED.2Iw4iAPIcW', 0, 1, NULL, NULL, '2026-02-25 12:36:16', '2026-02-25 12:36:16'),
(49, 'waka', 'valeria', 'kurtval@gmail.com', '09293218231', 'family', '$2y$12$Tys5XAPwHVcEHkd1gbtfbOYinNjiGHWEwPZNaiwSg4GTr47lCLTfi', 1, 1, NULL, '2026-03-04 02:44:50', '2026-03-02 03:01:29', '2026-03-04 02:44:50'),
(50, 'Test', 'Relationship', 'relationship@test.com', '09671189000', 'pwd', '$2y$12$YZQqvIBR7PNlLDPa.qIEyeXSsSKu6Ik3MAjCSdRSvDVvJHSVyBYsK', 0, 1, NULL, NULL, '2026-03-02 20:57:48', '2026-03-02 20:57:48'),
(51, 'Den', 'Denden', 'den@email.com', '09477532075', 'family', '$2y$12$FB2tsEEQK0Hd/R6wPmDReOor7wHfaDhqPaqJX87Ny9/1T1n05B7oS', 1, 1, NULL, '2026-03-03 21:46:18', '2026-03-03 09:21:49', '2026-03-03 21:46:18'),
(52, 'Wynn', 'de la Cruz', 'wynndelacruz@gmail.com', '09099372364', 'family', '$2y$12$wLJy/d9uCvuJ0gFhrTVuK.7nGD2VYpru3mHnPQ5EOQoIjXEGUh.Wi', 1, 1, NULL, '2026-03-03 15:26:34', '2026-03-03 14:20:56', '2026-03-03 15:26:34'),
(53, 'Leo', 'Shura', 'leoshura@gmail.com', '09675612342', 'pwd', '$2y$12$dnlHwcd2tke38iGqDH7BkueGo3HoQ14KFhhexJ/Bbnp2cEM7ERuv2', 0, 1, NULL, NULL, '2026-03-03 19:42:40', '2026-03-03 19:42:40'),
(54, 'Baby', 'mo', 'babymo@gmail.com', '09123456789', 'pwd', '$2y$12$KSSrrM.Dk9WAsizmqBwA2OsojPiqQxGklJN79P6yfxIe7fnskKER.', 0, 1, NULL, NULL, '2026-03-04 02:25:03', '2026-03-04 02:25:03'),
(55, 'Julianna', 'Duco', 'jul@gmail.com', '09707216118', 'pwd', '$2y$12$Jiec0cEVy8vDanNFEHmg4u9h2WGWbmr4yXHCVxhaa7Uj/zpUXWqdC', 1, 1, NULL, '2026-03-04 03:19:39', '2026-03-04 03:13:55', '2026-03-04 03:19:39'),
(56, 'Charmelle', 'Duco', 'charm@gmail.com', '09676767676', 'family', '$2y$12$X5cKsJfxILhza0YJiQPlYu6otoOVCPT5hB1esTIgNuWQ/2qnAub52', 1, 1, NULL, '2026-03-04 03:20:31', '2026-03-04 03:20:18', '2026-03-04 03:20:31'),
(57, 'Che', 'Anne', 'che@gmail.com', '09767676767', 'pwd', '$2y$12$JscdBZgOwLiUA8F4EXBqoOkm1VkW.2pibtdvXd7ZLjDY0c1a0xeCm', 1, 1, NULL, '2026-03-04 03:22:08', '2026-03-04 03:22:00', '2026-03-04 03:22:08'),
(58, 'Yeji', 'Hwang', 'hwangyeji@gmail.com', '09665896542', 'pwd', '$2y$12$qTQQJ3KUKiECsd805DMC6eGqcU.rAlGoX1yGG1Q4q9E5pPGAJPA5u', 0, 1, NULL, NULL, '2026-03-17 12:33:27', '2026-03-17 12:33:27'),
(59, 'Dairin', 'Janagap', 'd.janagap@usls.edu.ph', '09152411363', 'pwd', '$2y$12$ZpsqB0chHvl8N6yRKT9duOLw29WQURRaI8NYE0OAbVUZvh7UQ8Gt.', 1, 1, NULL, '2026-03-19 01:53:34', '2026-03-19 01:53:13', '2026-03-19 01:53:34'),
(60, 'Ryujin', 'Shin', 'shinryujin@gmail.com', '09665477852', 'pwd', '$2y$12$H8RrUpdgtEhXRbMdTLKF2OFDVfU1tsnndFwiYB40oqyHCIYQ2mxdS', 0, 1, NULL, NULL, '2026-03-19 02:42:17', '2026-03-19 02:42:17'),
(61, 'User', 'Secret', 'user123@gmail.com', '09704943152', 'pwd', '$2y$12$ErCNhR11h9/g.9oR8nJyiez2R0WV3EVj5MNM.I5BgaT7dfB1g5CPi', 1, 1, NULL, '2026-03-19 03:50:19', '2026-03-19 03:41:32', '2026-03-19 03:55:14'),
(62, 'sample', 'user', 'sampleuser@gmail.com', '09696969669', 'pwd', '$2y$12$V3/7h/dPI3oJmOa06E1ASekpyMu27WIYGR14oMwPIh85qA9nvjsAe', 0, 1, NULL, NULL, '2026-03-19 04:30:26', '2026-03-19 04:30:26'),
(63, 'Aziealle', 'Cruz', 'aziealle@gmail.com', '09099372368', 'pwd', '$2y$12$vvghDNdEqL9YgBjtZZcbYOBKGlritOfRvHIkj/AdO4qu1NAQoyYky', 0, 1, 'pwd_63_1773978999.jpg', NULL, '2026-03-20 03:56:39', '2026-03-20 03:56:39'),
(64, 'Jerome', 'Buntalidad', 'jerome@gmail.com', '09671789112', 'pwd', '$2y$12$tLz3X1EabVVpFECyFMXcuexvtDBvwxKwu2DBgktB/TARdz9j3T9SO', 0, 1, 'pwd_64_1774278204.png', NULL, '2026-03-23 15:03:24', '2026-03-23 15:03:24');

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE `user_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mfa_enabled` tinyint(1) DEFAULT 0,
  `sos_countdown_seconds` int(3) DEFAULT 10,
  `auto_shake_enabled` tinyint(1) DEFAULT 0,
  `auto_invite_contacts` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_settings`
--

INSERT INTO `user_settings` (`id`, `user_id`, `mfa_enabled`, `sos_countdown_seconds`, `auto_shake_enabled`, `auto_invite_contacts`, `created_at`, `updated_at`) VALUES
(1, 42, 1, 13, 0, 0, '2026-03-23 20:58:11', '2026-03-23 21:33:23'),
(6, 19, 0, 10, 0, 0, '2026-03-23 21:59:11', '2026-03-23 21:59:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checkin_media_logs`
--
ALTER TABLE `checkin_media_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `contact_inquiries`
--
ALTER TABLE `contact_inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `replied_by` (`replied_by`);

--
-- Indexes for table `disaster_alerts`
--
ALTER TABLE `disaster_alerts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emergency_alerts`
--
ALTER TABLE `emergency_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `resolved_by` (`resolved_by`);

--
-- Indexes for table `family_broadcasts`
--
ALTER TABLE `family_broadcasts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_pwd` (`pwd_id`);

--
-- Indexes for table `family_emergency_responses`
--
ALTER TABLE `family_emergency_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_alert` (`alert_id`),
  ADD KEY `idx_family_member` (`family_member_id`);

--
-- Indexes for table `family_pwd_relationships`
--
ALTER TABLE `family_pwd_relationships`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_relationship` (`family_member_id`,`pwd_user_id`),
  ADD KEY `idx_family_member` (`family_member_id`),
  ADD KEY `idx_pwd_user` (`pwd_user_id`);

--
-- Indexes for table `hub_media_logs`
--
ALTER TABLE `hub_media_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `medical_profiles`
--
ALTER TABLE `medical_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_profile` (`user_id`);

--
-- Indexes for table `mfa_codes`
--
ALTER TABLE `mfa_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mfa_user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_pr_token` (`token_hash`);

--
-- Indexes for table `pwd_emergency_contacts`
--
ALTER TABLE `pwd_emergency_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pwd_user` (`pwd_user_id`),
  ADD KEY `idx_contact_user` (`contact_user_id`);

--
-- Indexes for table `pwd_status_updates`
--
ALTER TABLE `pwd_status_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pwd_user` (`pwd_user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_token_hash` (`token_hash`);

--
-- Indexes for table `sms_events`
--
ALTER TABLE `sms_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checkin_media_logs`
--
ALTER TABLE `checkin_media_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_inquiries`
--
ALTER TABLE `contact_inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `disaster_alerts`
--
ALTER TABLE `disaster_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `emergency_alerts`
--
ALTER TABLE `emergency_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `family_broadcasts`
--
ALTER TABLE `family_broadcasts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `family_emergency_responses`
--
ALTER TABLE `family_emergency_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `family_pwd_relationships`
--
ALTER TABLE `family_pwd_relationships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=367;

--
-- AUTO_INCREMENT for table `hub_media_logs`
--
ALTER TABLE `hub_media_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `medical_profiles`
--
ALTER TABLE `medical_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `mfa_codes`
--
ALTER TABLE `mfa_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pwd_emergency_contacts`
--
ALTER TABLE `pwd_emergency_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=556;

--
-- AUTO_INCREMENT for table `pwd_status_updates`
--
ALTER TABLE `pwd_status_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `sms_events`
--
ALTER TABLE `sms_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checkin_media_logs`
--
ALTER TABLE `checkin_media_logs`
  ADD CONSTRAINT `checkin_media_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contact_inquiries`
--
ALTER TABLE `contact_inquiries`
  ADD CONSTRAINT `contact_inquiries_ibfk_1` FOREIGN KEY (`replied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `emergency_alerts`
--
ALTER TABLE `emergency_alerts`
  ADD CONSTRAINT `emergency_alerts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `emergency_alerts_ibfk_2` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `family_broadcasts`
--
ALTER TABLE `family_broadcasts`
  ADD CONSTRAINT `family_broadcasts_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `family_broadcasts_ibfk_2` FOREIGN KEY (`pwd_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `family_emergency_responses`
--
ALTER TABLE `family_emergency_responses`
  ADD CONSTRAINT `family_emergency_responses_ibfk_1` FOREIGN KEY (`alert_id`) REFERENCES `emergency_alerts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `family_emergency_responses_ibfk_2` FOREIGN KEY (`family_member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `family_pwd_relationships`
--
ALTER TABLE `family_pwd_relationships`
  ADD CONSTRAINT `family_pwd_relationships_ibfk_1` FOREIGN KEY (`family_member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `family_pwd_relationships_ibfk_2` FOREIGN KEY (`pwd_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hub_media_logs`
--
ALTER TABLE `hub_media_logs`
  ADD CONSTRAINT `hub_media_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medical_profiles`
--
ALTER TABLE `medical_profiles`
  ADD CONSTRAINT `medical_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mfa_codes`
--
ALTER TABLE `mfa_codes`
  ADD CONSTRAINT `mfa_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `pr_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pwd_emergency_contacts`
--
ALTER TABLE `pwd_emergency_contacts`
  ADD CONSTRAINT `pwd_ec_ibfk_1` FOREIGN KEY (`pwd_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pwd_ec_ibfk_2` FOREIGN KEY (`contact_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pwd_status_updates`
--
ALTER TABLE `pwd_status_updates`
  ADD CONSTRAINT `pwd_status_updates_ibfk_1` FOREIGN KEY (`pwd_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `rt_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sms_events`
--
ALTER TABLE `sms_events`
  ADD CONSTRAINT `sms_events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
