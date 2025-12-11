<?php
/**
 * ➕ API - Create Task
 * Endpoint pour créer une nouvelle tâche
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
    // Log des données reçues pour debug
    error_log("Create task - POST data: " . print_r($_POST, true));
    
    // Validate required fields avec messages plus clairs
    $requiredFields = [
        'projet_id' => 'Projet',
        'titre' => 'Titre de la tâche',
        'created_by' => 'Utilisateur créateur'
    ];
    
    foreach ($requiredFields as $field => $label) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => "Le champ '$label' est requis",
                'field' => $field
            ]);
            exit();
        }
    }
    
    // Validation supplémentaire du titre
    $titre = trim($_POST['titre']);
    if (strlen($titre) < 3) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Le titre doit contenir au moins 3 caractères',
            'field' => 'titre'
        ]);
        exit();
    }
    
    if (strlen($titre) > 255) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Le titre ne peut pas dépasser 255 caractères',
            'field' => 'titre'
        ]);
        exit();
    }
    
    // Prepare data
    $data = [
        'projet_id' => (int)$_POST['projet_id'],
        'titre' => trim($_POST['titre']),
        'description' => trim($_POST['description'] ?? ''),
        'statut' => $_POST['statut'] ?? 'a_faire',
        'priorite' => $_POST['priorite'] ?? 'moyenne',
        'date_echeance' => !empty($_POST['date_echeance']) ? $_POST['date_echeance'] : null,
        'assignee_id' => !empty($_POST['assignee_id']) ? (int)$_POST['assignee_id'] : null,
        'couleur' => $_POST['couleur'] ?? '#3B82F6',
        'tags' => trim($_POST['tags'] ?? ''),
        'temps_estime' => !empty($_POST['temps_estime']) ? (int)$_POST['temps_estime'] : null,
        'created_by' => (int)$_POST['created_by']
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
    $result = $taskController->createTask($data);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Tâche créée avec succès',
            'task_id' => $result['task_id']
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? 'Erreur lors de la création'
        ]);
    }
    
} catch (Exception $e) {
    error_log("API create-task error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur'
    ]);
}
?>