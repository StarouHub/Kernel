<?php
/**
 * 🔍 API - Get Task
 * Endpoint pour récupérer les détails d'une tâche
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing or invalid task ID']);
        exit();
    }
    
    $taskId = (int)$_GET['id'];
    
    // Include controller
    include_once(__DIR__ . '/../controller/taskcontroller.php');
    
    $taskController = new TaskController();
    $task = $taskController->getTaskById($taskId);
    
    if ($task) {
        echo json_encode([
            'success' => true,
            'task' => $task,
            'message' => 'Tâche récupérée avec succès'
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Tâche non trouvée'
        ]);
    }
    
} catch (Exception $e) {
    error_log("API get-task error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur'
    ]);
}
?>