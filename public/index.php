<?php
// Bootstrap file - Entry point for the application

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include authentication helper
require_once BASE_PATH . '/app/helpers/auth.php';

// Handle role switching
switchRole();

// Handle mode switching
handleModeSwitch();

// Include configuration
require_once BASE_PATH . '/config/database.php';

// Get controller and action from URL
$controller = $_GET['controller'] ?? 'sujet';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Sanitize controller and action
$controller = preg_replace('/[^a-z]/', '', strtolower($controller));
$action = preg_replace('/[^a-z]/', '', strtolower($action));

// Map controller name to class
$controllerClass = ucfirst($controller) . 'Controller';
$controllerFile = BASE_PATH . '/app/controller/' . $controllerClass . '.php';

// Check if controller file exists
if (!file_exists($controllerFile)) {
    die("Controller not found: " . htmlspecialchars($controller));
}

// Include controller
require_once $controllerFile;

// Check if controller class exists
if (!class_exists($controllerClass)) {
    die("Controller class not found: " . htmlspecialchars($controllerClass));
}

// Instantiate controller
$controllerInstance = new $controllerClass();

// Check if action method exists
if (!method_exists($controllerInstance, $action)) {
    die("Action not found: " . htmlspecialchars($action));
}

// Call action method
$controllerInstance->$action();
?>
