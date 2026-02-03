<?php
// test-db-connection.php - Test database connection

require_once 'config/config.php';
require_once 'config/Database.php';

echo "<h2>Database Connection Test</h2>";

echo "<h3>Configuration:</h3>";
echo "Host: " . DB_HOST . "<br>";
echo "Database: " . DB_NAME . "<br>";
echo "Username: " . DB_USER . "<br>";
echo "Password: " . str_repeat('*', strlen(DB_PASS)) . "<br><br>";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "<p style='color: green; font-weight: bold;'>✓ Connection Successful!</p>";
        
        // Test query
        $query = "SELECT DATABASE() as db_name, VERSION() as mysql_version";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h3>Database Info:</h3>";
        echo "Database Name: " . $result['db_name'] . "<br>";
        echo "MySQL Version: " . $result['mysql_version'] . "<br><br>";
        
        // List tables
        $query = "SHOW TABLES";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h3>Tables in Database:</h3>";
        if (count($tables) > 0) {
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>" . $table . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No tables found. Please import your schema.sql file.</p>";
        }
        
    } else {
        echo "<p style='color: red; font-weight: bold;'>✗ Connection Failed!</p>";
    }
    
} catch(Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>⚠️ IMPORTANT:</strong> Delete this file after testing!</p>";
?>