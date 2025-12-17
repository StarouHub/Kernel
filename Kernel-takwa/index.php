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
require_once __DIR__ . '/controller/EvenementController.php';

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
    
    case 'chatbot':
        require_once __DIR__ . '/services/ChatbotService.php';
        require_once __DIR__ . '/controllers/EvenementController.php';
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = $_POST['message'] ?? '';
            $eventId = $_POST['event_id'] ?? null;
            
            $chatbot = new ChatbotService();
            
            // Si un ID d'événement est fourni, on peut donner des réponses contextuelles
            if ($eventId) {
                $controller = new EvenementController();
                $evenement = $controller->getEvenementById((int)$eventId);
                if ($evenement) {
                    $result = $chatbot->processMessageWithContext($message, $evenement);
                } else {
                    $result = $chatbot->processMessage($message);
                }
            } else {
                $result = $chatbot->processMessage($message);
            }
            
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
        }
        exit;
        break;
    
    case 'payment_checkout':
        $controller->paymentCheckout();
        break;
    
    case 'process_payment':
        $controller->processPayment();
        break;
    
    case 'payment_success':
        $controller->paymentSuccess();
        break;
    
    default:
        $controller->index();
        break;
}
?>

