<?php
/**
 * api/mark-notification-read.php - Marque une notification comme lue
 */

header('Content-Type: application/json');

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/init.php';
require_once ROOT_PATH . '/config/databaset.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non authentifié']);
    exit;
}

try {
    $database = Database::getInstance();
    $db = $database->getConnection();
    
    // Récupérer les données POST
    $notification_id = $_POST['notification_id'] ?? null;
    $mark_all = isset($_POST['mark_all']) && $_POST['mark_all'] == '1';
    
    if ($mark_all) {
        // Marquer toutes les notifications comme lues dans la BD
        $sql = "UPDATE notifications_history 
                SET is_read = 1, read_at = NOW() 
                WHERE user_id = ? AND is_read = 0";
        $stmt = $db->prepare($sql);
        $stmt->execute([$_SESSION['user_id']]);
        
        // Marquer toutes dans la session
        if (isset($_SESSION['notifications'])) {
            foreach ($_SESSION['notifications'] as &$notif) {
                $notif['read'] = true;
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues',
            'count' => $stmt->rowCount()
        ], JSON_UNESCAPED_UNICODE);
        
    } elseif ($notification_id) {
        // Extraire l'ID de la base de données si format "db_123" ou "notif_123"
        $db_id = null;
        if (strpos($notification_id, 'db_') === 0) {
            $db_id = (int)str_replace('db_', '', $notification_id);
        } elseif (strpos($notification_id, 'notif_') === 0) {
            $db_id = (int)str_replace('notif_', '', $notification_id);
        } else {
            $db_id = (int)$notification_id;
        }
        
        if ($db_id > 0) {
            // Marquer dans la base de données
            $sql = "UPDATE notifications_history 
                    SET is_read = 1, read_at = NOW() 
                    WHERE id = ? AND user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$db_id, $_SESSION['user_id']]);
        }
        
        // Marquer dans la session
        if (isset($_SESSION['notifications'])) {
            foreach ($_SESSION['notifications'] as &$notif) {
                if ($notif['id'] === $notification_id) {
                    $notif['read'] = true;
                    break;
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Notification marquée comme lue',
            'notification_id' => $notification_id
        ], JSON_UNESCAPED_UNICODE);
        
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'ID de notification manquant'
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
