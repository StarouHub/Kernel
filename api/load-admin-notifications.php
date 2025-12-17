<?php
/**
 * api/load-admin-notifications.php - Charge les notifications admin depuis la BD
 * À appeler lors de la connexion d'un admin pour remplir sa session
 */

header('Content-Type: application/json');

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/init.php';
require_once ROOT_PATH . '/config/databaset.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

try {
    $database = Database::getInstance();
    $db = $database->getConnection();
    
    // Charger les 20 dernières notifications NON lues
    $sql = "SELECT id, message, type, category, reclamation_id, is_read, created_at 
            FROM notifications_history 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 20";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mapper les notifications vers le format SESSION
    if (!isset($_SESSION['notifications'])) {
        $_SESSION['notifications'] = [];
    }
    
    foreach ($notifications as $notif) {
        $sessionNotif = [
            'id' => 'notif_' . $notif['id'],
            'message' => $notif['message'],
            'type' => $notif['type'],
            'category' => $notif['category'],
            'reclamation_id' => $notif['reclamation_id'],
            'date' => date('Y-m-d H:i:s', strtotime($notif['created_at'])),
            'read' => (bool)$notif['is_read'],
            'icon' => getNotificationIcon($notif['type']),
            'color' => getNotificationColor($notif['type'])
        ];
        
        // Ajouter à la session (si pas déjà présente)
        $found = false;
        foreach ($_SESSION['notifications'] as $existing) {
            if ($existing['id'] === $sessionNotif['id']) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $_SESSION['notifications'][] = $sessionNotif;
        }
    }
    
    $unread_count = count(array_filter($notifications, fn($n) => !$n['is_read']));
    
    echo json_encode([
        'success' => true,
        'notifications_loaded' => count($notifications),
        'unread_count' => $unread_count,
        'total_unread' => count(array_filter($_SESSION['notifications'] ?? [], fn($n) => !$n['read'] ?? true))
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

function getNotificationIcon($type) {
    $icons = [
        'info' => 'bi-info-circle',
        'success' => 'bi-check-circle',
        'warning' => 'bi-exclamation-triangle',
        'danger' => 'bi-x-circle',
        'system' => 'bi-gear',
        'user_reclamation' => 'bi-person-plus'
    ];
    return $icons[$type] ?? 'bi-bell';
}

function getNotificationColor($type) {
    $colors = [
        'info' => '#0284c7',
        'success' => '#16a34a',
        'warning' => '#ea580c',
        'danger' => '#dc2626',
        'system' => '#6366f1',
        'user_reclamation' => '#7c3aed'
    ];
    return $colors[$type] ?? '#475569';
}
?>
