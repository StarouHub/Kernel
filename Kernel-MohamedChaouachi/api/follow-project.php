<?php
/**
 * API pour gérer le suivi des projets
 * Permet aux utilisateurs de suivre/ne plus suivre des projets
 */

session_start();
require_once '../config.php';
require_once '../controller/userController.php';

header('Content-Type: application/json');

try {
    $controller = new UserController();
    
    // Vérifier si l'utilisateur est connecté
    if (!$controller->isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
        exit;
    }
    
    $currentUser = $controller->getCurrentUser();
    $userId = $currentUser->getId();
    
    // Vérifier la méthode HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
        exit;
    }
    
    // Récupérer les données JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['projet_id']) || !is_numeric($input['projet_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de projet manquant ou invalide']);
        exit;
    }
    
    $projetId = (int)$input['projet_id'];
    $action = $input['action'] ?? 'toggle'; // 'follow', 'unfollow', ou 'toggle'
    
    $pdo = config::getConnexion();
    
    // Vérifier que le projet existe
    $sql = "SELECT id, titre FROM projet WHERE id = :projet_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['projet_id' => $projetId]);
    $projet = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$projet) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Projet introuvable']);
        exit;
    }
    
    // Vérifier le statut de suivi actuel
    $sql = "SELECT id FROM project_followers WHERE user_id = :user_id AND projet_id = :projet_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId, 'projet_id' => $projetId]);
    $isFollowing = $stmt->fetch() !== false;
    
    $newStatus = false;
    
    if ($action === 'follow' || ($action === 'toggle' && !$isFollowing)) {
        // Commencer à suivre
        $sql = "INSERT IGNORE INTO project_followers (user_id, projet_id, follow_type, notification_frequency, date_followed, is_active) 
                VALUES (:user_id, :projet_id, 'interested', 'instant', NOW(), 1)
                ON DUPLICATE KEY UPDATE is_active = 1, date_followed = NOW()";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'projet_id' => $projetId]);
        
        $newStatus = true;
        $message = 'Vous suivez maintenant ce projet !';
        
    } elseif ($action === 'unfollow' || ($action === 'toggle' && $isFollowing)) {
        // Arrêter de suivre
        $sql = "UPDATE project_followers SET is_active = 0 WHERE user_id = :user_id AND projet_id = :projet_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'projet_id' => $projetId]);
        
        $newStatus = false;
        $message = 'Vous ne suivez plus ce projet';
    }
    
    // Compter le nombre total de followers
    $sql = "SELECT COUNT(*) as count FROM project_followers WHERE projet_id = :projet_id AND is_active = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['projet_id' => $projetId]);
    $followersCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'is_following' => $newStatus,
        'followers_count' => $followersCount,
        'project_title' => $projet['titre']
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>