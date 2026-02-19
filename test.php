<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "PHP Version: " . phpversion() . "<br>";

// Test 1: Does BASE_PATH resolve correctly?
define('BASE_PATH', __DIR__ . '/');
echo "BASE_PATH: " . BASE_PATH . "<br>";

// Test 2: Does vendor/autoload.php exist?
$autoload = BASE_PATH . 'vendor/autoload.php';
echo "Autoload exists: " . (file_exists($autoload) ? 'YES' : 'NO') . "<br>";

// Test 3: Can we load PHPMailer?
if (file_exists($autoload)) {
    require_once $autoload;
    echo "Autoload loaded: YES<br>";
    echo "PHPMailer exists: " . (class_exists('\PHPMailer\PHPMailer\PHPMailer') ? 'YES' : 'NO') . "<br>";
}

echo "All checks done.";
?>