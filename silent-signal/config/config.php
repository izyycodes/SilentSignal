<?php
// config/config.php - Environment-Based Configuration
// Works automatically on both localhost and HelioHost

// Detect environment based on server name
$isLocal = (
    $_SERVER['HTTP_HOST'] === 'localhost' || 
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'localhost:') !== false
);

// Site settings
define('SITE_NAME', 'Silent Signal');

if ($isLocal) {
    // ========================================
    // LOCAL DEVELOPMENT SETTINGS
    // ========================================
    define('BASE_URL', 'http://localhost/2026BSIT2DGroup5/silent-signal/');
    
    // Local Database
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'silent_signal');
    
} else {
    // ========================================
    // HELIOHOST PRODUCTION SETTINGS
    // ========================================
    
    define('BASE_URL', 'https://rgdioma.helioho.st/'); 
    
    // HelioHost Database Settings
    // Format: servername.heliohost.org (tommy, johnny, morty, or ricky)
    define('DB_HOST', 'morty.heliohost.org'); // Change to your server
    
    // IMPORTANT: Add your username prefix!
    define('DB_USER', 'rgdioma_admin'); 
    define('DB_PASS', 'adminlogin123'); 
    define('DB_NAME', 'rgdioma_silent_signal'); 
}

// Contact Information (same for both environments)
define('CONTACT_PHONE', '+639123456789');
define('CONTACT_EMAIL', 'contact@silentsignal.com');
define('CONTACT_ADDRESS', 'Bacolod City, Philippines');
define('CONTACT_WEBSITE', 'www.silentsignal.com');

// Paths (same for both environments)
define('VIEW_PATH', __DIR__ . '/../views/');
define('CONTROLLER_PATH', __DIR__ . '/../controllers/');
define('MODEL_PATH', __DIR__ . '/../models/');
define('ASSETS_PATH', BASE_URL . 'assets/');

// Error Reporting
if ($isLocal) {
    // Show errors in development
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    // Hide errors in production, log them instead
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
    ini_set('log_errors', 1);
    // HelioHost error log location
    ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/../logs/php_errors.log');
}

?>