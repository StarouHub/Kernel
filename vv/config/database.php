<?php
/**
 * Database Configuration
 * Configure your database connection settings here
 */

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'kernel');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_PORT', 3306);

// Get database connection (singleton pattern)
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log error for debugging
            error_log("Database connection error: " . $e->getMessage());
            
            // Show user-friendly error message
            if (defined('DEBUG') && DEBUG) {
                die("Database connection failed: " . htmlspecialchars($e->getMessage()));
            } else {
                die("Database connection failed. Please contact the administrator.");
            }
        }
    }
    
    return $pdo;
}

/**
 * Test database connection
 * @return bool
 */
function testDBConnection() {
    try {
        $db = getDB();
        return $db !== null;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get database connection info (for debugging)
 * @return array
 */
function getDBInfo() {
    return [
        'host' => DB_HOST,
        'database' => DB_NAME,
        'user' => DB_USER,
        'charset' => DB_CHARSET,
        'port' => DB_PORT,
    ];
}
?>

