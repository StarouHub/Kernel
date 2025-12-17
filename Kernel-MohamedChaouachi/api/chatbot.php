<?php
/**
 * API Chatbot - Endpoint pour les requêtes AJAX
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include_once(__DIR__ . '/../services/ChatbotService.php');

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

// Récupérer la question
$input = json_decode(file_get_contents('php://input'), true);
$question = $input['question'] ?? $_POST['question'] ?? '';

if (empty($question)) {
    echo json_encode([
        'success' => false,
        'message' => 'Question vide'
    ]);
    exit;
}

// Traiter la question
$chatbotService = new ChatbotService();
$response = $chatbotService->processQuestion($question);

echo json_encode($response);
?>
