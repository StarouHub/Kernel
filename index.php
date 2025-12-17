<?php
/**
 * Main Entry Point - MVC Router
 * Routes requests to appropriate controller actions
 */

session_start();

// Define base path
define('BASE_PATH', __DIR__);

// Get action from URL (with default)
$action = $_GET['action'] ?? 'index';

// Include the main controller
require_once __DIR__ . '/controller/EvenementController.php';

// Create controller instance
$controller = new EvenementController();

// List of public actions that don't require authentication
$publicActions = ['login', 'logout', 'inscription', 'inscription_save'];

// Authentication check: redirect to login if not authenticated and action is not public
if (!in_array($action, $publicActions) && !isset($_SESSION['user_role'])) {
    header('Location: index.php?action=login');
    exit;
}

// Add the "Retour" button (visible on all pages)
echo '
<style>
    .btn-retour {
        position: fixed;
        top: 20px;
        left: 20px;
        background-color: #dc3545; /* Rouge Bootstrap danger */
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        font-size: 16px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        z-index: 9999;
        transition: background-color 0.3s;
    }
    .btn-retour:hover {
        background-color: #c82333;
    }
</style>
<a href="view/FrontOffice/index.php" class="btn-retour">← Retour</a>
';

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
        // Special case: API endpoint for chatbot
        require_once __DIR__ . '/servicest/ChatbotService.php'; // Note: corrigé "services" au lieu de "servicest"
        require_once __DIR__ . '/controller/EvenementController.php';

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = $_POST['message'] ?? '';
            $eventId = $_POST['event_id'] ?? null;

            $chatbot = new ChatbotService();

            if ($eventId) {
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