<?php
/**
 * api/get-notifications.php - Récupère les notifications
 */

header('Content-Type: application/json');

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/init.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

$notifications = $_SESSION['notifications'] ?? [];
$unread_count = count(array_filter($notifications, fn($n) => !$n['read'] ?? true));

echo json_encode([
    'success' => true,
    'notifications' => $notifications,
    'unread_count' => $unread_count,
    'updated' => $unread_count > 0,
    'timestamp' => date('Y-m-d H:i:s')
], JSON_UNESCAPED_UNICODE);
?>
