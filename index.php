<?php
/**
 * Main Entry Point - MVC Router
 * Routes requests to appropriate controller actions
 */

session_start();

// Define base path
define('BASE_PATH', __DIR__);

// Get action from URL
$action = $_GET['action'] ?? 'index';

// Include controller
require_once __DIR__ . '/controllers/EvenementController.php';

// Create controller instance
$controller = new EvenementController();

// Check authentication (except for login, logout et inscription publique)
$publicActions = ['login', 'logout', 'inscription', 'inscription_save'];
if (!in_array($action, $publicActions) && !isset($_SESSION['user_role'])) {
    header('Location: index.php?action=login');
    exit;
}

// Route to appropriate action
switch ($action) {
    case 'login':
        $controller->login();
        break;
    
    case 'logout':
        $controller->logout();
        break;
    
    case 'index':
    case 'list':
        $controller->index();
        break;
    
    case 'create':
        $controller->create();
        break;
    
    case 'edit':
        $controller->edit();
        break;
    
    case 'details':
    case 'show':
        $controller->details();
        break;

    case 'inscription':
        $controller->inscriptionForm();
        break;

    case 'inscription_save':
        $controller->inscriptionSave();
        break;
    
    case 'save':
        $controller->save();
        break;
    
    case 'delete':
        $controller->delete();
        break;
    
    default:
        $controller->index();
        break;
}
?>

