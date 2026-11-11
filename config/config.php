<?php
// config/config.php - Environment-Based Configuration
// Works automatically on both localhost and HelioHost

// Detect environment based on server name (safe check)
$httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

$isLocal = (
    $httpHost === 'localhost' ||
    strpos($httpHost, '127.0.0.1') !== false ||
    strpos($httpHost, 'localhost:') !== false
);

// Site settings
define('SITE_NAME', 'Silent Signal');

if ($isLocal) {
    // ========================================
    // LOCAL DEVELOPMENT SETTINGS
    // ========================================
    define('BASE_URL', 'http://localhost/SilentSignal/');
    
    // Local Database
    define('DB_HOST', 'localhost'); 
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'silent_signal');
    
} else {
    // ========================================
    // HELIOHOST PRODUCTION SETTINGS
    // ========================================
    
    define('BASE_URL', 'https://silentsignal.helioho.st/'); 
    
    // HelioHost Database Settings
    // Format: servername.heliohost.org (tommy, johnny, morty, or ricky)
    define('DB_HOST', 'johnny.heliohost.org'); // Change to your server
    
    // IMPORTANT: Add your username prefix!
    define('DB_USER', 'rgdioma_admin'); 
    define('DB_PASS', 'adminlogin123'); 
    define('DB_NAME', 'rgdioma_silent_signal'); 
}

// Contact Information (same for both environments)
define('CONTACT_PHONE', '+639123456789');
define('CONTACT_EMAIL', 'ssilentsignal@gmail.com');
define('CONTACT_ADDRESS', 'Bacolod City, Philippines');
define('CONTACT_WEBSITE', 'silentsignal.helioho.st');

// API keys
define('GROQ_API_KEY', $_ENV['GROQ_API_KEY'] ?? '');
define('GROQ_MODEL',   $_ENV['GROQ_MODEL']   ?? 'openai/gpt-oss-120b');

define('PHILSMS_TOKEN', $_ENV['PHILSMS_TOKEN'] ?? '');

define('GEOAPIFY_API_KEY', $_ENV['GEOAPIFY_API_KEY'] ?? '');

// Paths (same for both environments)
define('BASE_PATH', __DIR__ . '/../'); 
define('VIEW_PATH', __DIR__ . '/../views/');
define('CONTROLLER_PATH', __DIR__ . '/../controllers/');
define('MODEL_PATH', __DIR__ . '/../models/');
define('CONFIG_PATH', __DIR__ . '/../config/');
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
    // Use BASE_PATH so logs folder is relative to the app root
    $logDir = BASE_PATH . 'logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    ini_set('error_log', $logDir . '/php_errors.log');
}

?>
