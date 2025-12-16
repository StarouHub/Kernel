<?php
/**
 * Core Bootstrap File
 * Loads core classes and sets up the application foundation
 */

// Load database configuration first
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}

// Load database configuration
if (file_exists(BASE_PATH . '/config/database.php')) {
    require_once BASE_PATH . '/config/database.php';
}

// Load core classes
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Model.php';

// Auto-load models (optional - for convenience)
spl_autoload_register(function ($class) {
    $modelPath = BASE_PATH . '/app/models/' . $class . '.php';
    if (file_exists($modelPath)) {
        require_once $modelPath;
    }
});

?>

