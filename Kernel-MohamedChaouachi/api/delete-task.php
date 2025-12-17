<?php
/**
 * 🗑️ API - Delete Task
 * Endpoint pour supprimer une tâche
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['task_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing task ID']);
        exit();
    }
    
    $taskId = (int)$input['task_id'];
    
    if ($taskId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid task ID']);
        exit();
    }
    
    // Include controller
    include_once(__DIR__ . '/../controller/taskcontroller.php');
    
    $taskController = new TaskController();
    $result = $taskController->deleteTask($taskId);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Tâche supprimée avec succès',
            'task_id' => $taskId
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? 'Erreur lors de la suppression'
        ]);
    }
    
} catch (Exception $e) {
    error_log("API delete-task error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur'
    ]);
}
?>