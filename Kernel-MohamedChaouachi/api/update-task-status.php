<?php
/**
 * 🔄 API - Update Task Status
 * Endpoint pour mettre à jour le statut d'une tâche (drag & drop)
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
    
    if (!$input || !isset($input['task_id']) || !isset($input['status'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit();
    }
    
    $taskId = (int)$input['task_id'];
    $newStatus = $input['status'];
    
    // Validate status
    $validStatuses = ['a_faire', 'en_cours', 'termine'];
    if (!in_array($newStatus, $validStatuses)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit();
    }
    
    // Include controller
    include_once(__DIR__ . '/../controller/taskcontroller.php');
    
    $taskController = new TaskController();
    $result = $taskController->changeTaskStatus($taskId, $newStatus);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Statut mis à jour avec succès',
            'task_id' => $taskId,
            'new_status' => $newStatus
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? 'Erreur lors de la mise à jour'
        ]);
    }
    
} catch (Exception $e) {
    error_log("API update-task-status error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur'
    ]);
}
?>