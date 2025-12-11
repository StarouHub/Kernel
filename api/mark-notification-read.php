<?php
/**
 * api/mark-notification-read.php - Marque une notification comme lue
 */

header('Content-Type: application/json');

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/init.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$notification_id = $data['notification_id'] ?? null;

if (!$notification_id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
    exit;
}

if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = [];
}

foreach ($_SESSION['notifications'] as &$notif) {
    if ($notif['id'] === $notification_id) {
        $notif['read'] = true;
        break;
    }
}

echo json_encode([
    'success' => true,
    'message' => 'Notification marquée comme lue'
], JSON_UNESCAPED_UNICODE);
?>
