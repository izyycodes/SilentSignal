<?php
// config/config.php - Configuration settings

// Site settings
define('SITE_NAME', 'Silent Signal');

// Updated BASE_URL for live server - IMPORTANT: use https and end with slash!
define('BASE_URL', 'https://rgdioma.helioho.st/');

// BASE_URL for localhost - uncomment if you want to open in live server
// define('BASE_URL', 'http://localhost/2026BSIT2DGroup5/silent-signal/');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'silent_signal');

// Contact Information
define('CONTACT_PHONE', '+639123456789');
define('CONTACT_EMAIL', 'silentsignal@gmail.com');
define('CONTACT_ADDRESS', 'Bacolod City, Philippines');
define('CONTACT_WEBSITE', 'www.silentsignal.com');

// Paths
define('VIEW_PATH', __DIR__ . '/../views/');
define('CONTROLLER_PATH', __DIR__ . '/../controllers/');
define('MODEL_PATH', __DIR__ . '/../models/');
define('ASSETS_PATH', BASE_URL . 'assets/');

?>