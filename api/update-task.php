<?php
/**
 * ✏️ API - Update Task
 * Endpoint pour mettre à jour une tâche existante
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
    // Validate required fields
    if (!isset($_POST['task_id']) || !is_numeric($_POST['task_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de tâche manquant ou invalide']);
        exit();
    }
    
    $taskId = (int)$_POST['task_id'];
    
    if (!isset($_POST['titre']) || empty(trim($_POST['titre']))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Le titre est requis']);
        exit();
    }
    
    // Prepare data
    $data = [
        'titre' => trim($_POST['titre']),
        'description' => trim($_POST['description'] ?? ''),
        'statut' => $_POST['statut'] ?? 'a_faire',
        'priorite' => $_POST['priorite'] ?? 'moyenne',
        'date_echeance' => !empty($_POST['date_echeance']) ? $_POST['date_echeance'] : null,
        'assignee_id' => !empty($_POST['assignee_id']) ? (int)$_POST['assignee_id'] : null,
        'couleur' => $_POST['couleur'] ?? '#3B82F6',
        'tags' => trim($_POST['tags'] ?? ''),
        'temps_estime' => !empty($_POST['temps_estime']) ? (int)$_POST['temps_estime'] : null,
        'temps_passe' => !empty($_POST['temps_passe']) ? (int)$_POST['temps_passe'] : 0
    ];
    
    // Validate data
    $validStatuses = ['a_faire', 'en_cours', 'termine'];
    if (!in_array($data['statut'], $validStatuses)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Statut invalide']);
        exit();
    }
    
    $validPriorities = ['basse', 'moyenne', 'haute'];
    if (!in_array($data['priorite'], $validPriorities)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Priorité invalide']);
        exit();
    }
    
    // Include controller
    include_once(__DIR__ . '/../controller/taskcontroller.php');
    
    $taskController = new TaskController();
    $result = $taskController->updateTask($taskId, $data);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Tâche mise à jour avec succès',
            'task_id' => $taskId
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? 'Erreur lors de la mise à jour'
        ]);
    }
    
} catch (Exception $e) {
    error_log("API update-task error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur'
    ]);
}
?>